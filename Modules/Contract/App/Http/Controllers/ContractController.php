<?php

namespace Modules\Contract\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libs\Helper\UrlHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Contract\App\Http\Requests\ContractCreateRequest;
use Modules\Contract\App\Resources\ContractItemResource;
use Modules\Contract\App\Resources\ContractResource;
use Modules\Contract\App\Resources\ContractTempResource;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\ContractCategoryModel;
use Modules\Contract\Models\ContractCatItemModel;
use Modules\Contract\Models\ContractItemModel;
use Modules\Contract\Models\ContractModel;
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
//

//        return view('pdf.contract', ['contract'=>$contract]);

        $pdf = PDF::loadView('pdf.contract', ['contract' => $contract], [], [
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
            'watermark' => 'پیمانگاه',

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

        $category = ContractCategoryModel::query()->where("alias", $request->post("category"))->first();
        if (!$category) {
            return response()->json(['status' => false, 'message' => 'دسته بندی پیدا نشد.'], 404);
        }
        $contract = new ContractModel();
        $contract->user_id = auth()->id();
        $contract->title = $request->post('title');
        $contract->category_id = $category->id;
        $contract->save();
        return response()->json(['status' => true, 'contract_id' => $contract->code]);
    }

    /**
     * Display a listing of the resource.
     */
    public function details(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        $tempData = ContractCatItemTempModel::query()->where("id", $contract->category_item_id)->first();


        return response()->json(['contract' => new ContractResource($contract), 'template' => new ContractTempResource($tempData), 'items' => ContractItemResource::collection($contract->items), 'users' => ContractUserResource::collection($contract->users), 'status' => true]);

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

}
