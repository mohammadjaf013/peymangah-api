<?php

namespace Modules\Contract\App\Http\Controllers;

use App\Events\SendSmsActiveEvent;
use App\Events\SendSmsPreviewEvent;
use App\Http\Controllers\Controller;
use App\Libs\Helper\UrlHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Contract\App\Http\Requests\ContractCreateRequest;
use Modules\Contract\App\Resources\ContractAttacheResource;
use Modules\Contract\App\Resources\ContractItemResource;
use Modules\Contract\App\Resources\ContractResource;
use Modules\Contract\App\Resources\ContractTempResource;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\ContractAttacheModel;
use Modules\Contract\Models\ContractCatItemModel;
use Modules\Contract\Models\ContractItemModel;
use Modules\Contract\Models\ContractLogModel;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\Contract\Models\PaymentLogModel;
use PDF;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

//use Barryvdh\DomPDF\Facade\Pdf;

class ContractController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function pdf(Request $request, $id)
    {

        $contract = ContractModel::query()
//            ->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        $fd= null;
        $date = ContractUserModel::query()->where("contract_id",$contract->id)->orderBy("sign_at","desc")->first();
        if($date){

            $fd =$date->sign_at;
        }

        $isSignd = $contract->users->count() == $contract->users->where("is_signed",1)->count();

        $pdf = PDF::loadView('pdf.contract', ['contract' => $contract ,'date'=>$fd, 'isSigned'=>$isSignd], [], [
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => '10',
            'default_font' => 'vazir',
            'display_mode' => 'fullpage',
            'directionality' => 'rtl',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 35,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10,
            'orientation' => 'P',
            'watermark' => $isSignd ? 'پیمانگاه' : '',

            'show_watermark' => true,
            'custom_font_dir' => base_path('resources/fonts/'),
            'custom_font_data' => [
                'vazir' => [
                    'R' => 'Vazirmatn-FD-Medium.ttf',
                    'B' => 'Vazirmatn-FD-Bold.ttf',
                    'I' => 'Vazir-Italic.ttf',
                    'BI' => 'Vazir-BoldItalic.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'auto_language_detection' => false,
        ]);

        return $pdf->stream('contract.pdf');

    }

    /**
     * Display a listing of the resource.
     */
    public function create(ContractCreateRequest $request): JsonResponse
    {

        $category = ContractCatItemTempModel::query()->where("id", $request->post("category"))->first();


        if (!$category) {
            return response()->json(['status' => false, 'message' => 'دسته بندی پیدا نشد.'], 404);
        }

        $contract = new ContractModel();
        $contract->user_id = auth()->id();
        $contract->title = $request->post('title');
        $contract->category_id = $category->mainCat->id;
        $contract->category_item_id = $category->id;
        $contract->save();


        $log = new ContractLogModel();
        $log->contract_id = $contract->id;
        $log->user_id = auth()->id();
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "CREATE";
        $log->message = "ایجاد قرارداد";
        $log->save();

        return response()->json(['status' => true, 'contract_id' => $contract->code]);
    }


    /**
     * Display a listing of the resource.
     */
    public function active(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }
        if ($contract->is_paid == 0) {
            return response()->json(['message' => 'مبلغ قرارداد را پرداخت کنید سپس درخواست فعال سازی را ارسال نمایید.'], 404);
        }

        $contract->is_locked = 1;
        $contract->status = "signing";
        $contract->save();

        $log = new ContractLogModel();
        $log->contract_id = $contract->id;
        $log->user_id = auth()->id();
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "ACTIVE";
        $log->message = "فعل سازی قرارداد";
        $log->save();
        event(new SendSmsActiveEvent($contract));

        return response()->json(['status' => true]);

    }

    public function sendsms(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }
        if ($contract->is_locked) {
            return response()->json(['message' => 'قرارداد فعال سازی شده قابل ارسال پیشنمایش نیست.'], 404);
        }


        event(new SendSmsPreviewEvent($contract));


        $log = new ContractLogModel();
        $log->contract_id = $contract->id;
        $log->user_id = auth()->id();
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "PREVIEW_SMS";
        $log->message = "ارسال پیامک پیشنمایش";
        $log->save();

        return response()->json(['status' => true]);

    }

    public function details(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }
        $tempData = ContractCatItemTempModel::query()->where("id", $contract->category_item_id)->first();
        return response()->json(['contract' => new ContractResource($contract), 'template' => (!$tempData) ? null : new ContractTempResource($tempData), 'items' => ContractItemResource::collection($contract->items), 'users' => ContractUserResource::collection($contract->users), 'status' => true]);

    }


    /**
     * Display a listing of the resource.
     */
    public function cr(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        DB::beginTransaction();

        try {
            ContractCatItemModel::query()->where("contract_id", $contract->id)->delete();
            ContractItemModel::query()->where("contract_id", $contract->id)->delete();

            if (!$request->has("cats")) {
                return response()->json(['message' => 'خطا داده های ارسالی معتبر نمیباشد..'], 404);

            }

            $cats = $request->post("cats", []);
            foreach ($cats as $cat) {
                $newCat = ContractCatItemModel::create([
                    'title' => $cat['title'],
                    'contract_id' => $contract->id
                ]);
                foreach ($cat['contents'] as $content) {
                    ContractItemModel::create([
                        'content' => $content['content'],
                        'contract_catitem_id' => $newCat->id,
                        'contract_id' => $contract->id,

                    ]);
                }
                DB::commit();
            }
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا داده های ارسالی معتبر نمیباشد..'], 404);

        }
    }


    public function payment(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        if ($contract->is_paid) {


            return response()->json(['type' => "return", 'link' => UrlHelper::url("/panel/contract/" . $contract->code . "/details")]);

        }
        $contract->price = (is_null($contract->price)) ? 25000 : $contract->price;

        $log = new PaymentLogModel();
        $log->user_id = auth()->id();
        $log->contract_id = $contract->id;
        $log->price = $contract->price;
        $log->is_paid = 0;
        $log->save();

        $callback = url("/api/contract/banback?id=" . $log->code);

        $invoice = (new Invoice)->amount($contract->price);
        $pay = Payment::callbackUrl($callback)->purchase(
            $invoice,
            function ($driver, $transactionId) use ($log, $invoice) {
                $log->result = serialize($invoice);
                $log->reference = $transactionId;
                $log->save();
            }
        )->pay();


        return response()->json(['type' => "bank", 'link' => $pay->getAction()]);

    }

    public function banback(Request $request)
    {
        $log = PaymentLogModel::query()->where("code", $request->get("id"))->first();
        if (!$log) {
            return redirect(UrlHelper::url("/panel/contract"));
        }
        DB::beginTransaction();
        $contract = ContractModel::query()->where("id", $log->contract_id)->first();

        try {
            $receipt = Payment::amount($log->price)->transactionId($log->reference)->verify();


            $log->receipt_id = $receipt->getReferenceId();
            $log->back_result = serialize($receipt);
            $log->is_paid = 1;
            $log->paid_at = Carbon::now();
            $log->save();
            if ($contract) {
                $contract->is_paid = 1;
                $contract->update();
            }


            $log = new ContractLogModel();
            $log->contract_id = $contract->id;
            $log->user_id = auth()->id();
            $log->ip = $request->getClientIp();
            $log->user_agent = $request->userAgent();
            $log->event = "PAY";
            $log->message = "پرداخت";
            $log->save();


            DB::commit();
            return redirect(UrlHelper::url("/panel/contract/" . $contract->code . "/details"));

        } catch (InvalidPaymentException $exception) {
            report($exception);
            DB::rollBack();
            $log->error_data = serialize($exception);
            $log->error_msg = $exception->getMessage();
            $log->save();
            DB::commit();
            return redirect(UrlHelper::url("/panel/contract/" . $contract->code . "/payment"));


        }
    }


    public function update(Request $request, $id): JsonResponse
    {


        $contract = ContractModel::query()
            ->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }
        if ($contract->is_locked == 1) {
            return response()->json(['message' => 'امکان بروزرسانی وجود ندارد.'], 404);
        }
        if (!$request->has("cats")) {
            return response()->json(['message' => 'خطا داده های ارسالی معتبر نمیباشد..'], 404);
        }

        DB::beginTransaction();

        try {

            $cats = $request->post("cats", []);

            foreach ($cats as $cat) {

                if (isset($cat['id'])) {

                    $catData = ContractCatItemModel::query()
                        ->where("code", $cat['id'])
                        ->where("contract_id", $contract->id)->first();


                    if ($catData) {

                        $catData->title = $cat['title'];
                        $catData->save();
                    }
                    if (isset($cat['contents']) && is_array($cat['contents'])) {
                        foreach ($cat['contents'] as $content) {
                            if (isset($content['id'])) {

                                $contentItem = ContractItemModel::query()
                                    ->where("contract_id", $contract->id)
                                    ->where("code", $content['id'])
                                    ->where("contract_catitem_id", $catData->id)->first();
                                if ($contentItem) {
                                    $contentItem->content = $content['content'];
                                    $contentItem->update();
                                }
                            } else {
                                ContractItemModel::create([
                                    'content' => $content['content'],
                                    'contract_catitem_id' => $catData->id,
                                    'contract_id' => $contract->id,

                                ]);
                            }

                        }
                    }
                } else {

                    $newCat = ContractCatItemModel::create([
                        'title' => $cat['title'],
                        'contract_id' => $contract->id
                    ]);
                    foreach ($cat['contents'] as $content) {
                        ContractItemModel::create([
                            'content' => $content['content'],
                            'contract_catitem_id' => $newCat->id,
                            'contract_id' => $contract->id,

                        ]);
                    }
                }

                $log = new ContractLogModel();
                $log->contract_id = $contract->id;
                $log->user_id = auth()->id();
                $log->ip = $request->getClientIp();
                $log->user_agent = $request->userAgent();
                $log->event = "UPDATE";
                $log->message = "بروزرسانی قرارداد";
                $log->save();

                DB::commit();
            }
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا داده های ارسالی معتبر نمیباشد..'], 404);

        }
    }

    public function attache(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }


        $log = new ContractAttacheModel();
        $log->contract_id = $contract->id;
        $log->user_id = auth()->id();
        $log->save();

        $log->refresh();
        return response()->json(['status' => true,"file"=>ContractAttacheResource::make($log)]);

    }
    public function attacheRemove(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }


        $log =  ContractAttacheModel::query()->where("contract_id",$contract->id)
            ->where("user_id",auth()->id())
            ->where("code",$request->post("id"))->first();
        if (!$log) {
            return response()->json(['message' => 'پیوستی یافت نشد.'], 404);
        }
        $log->delete();
        return response()->json(['status' => true]);

    }

}
