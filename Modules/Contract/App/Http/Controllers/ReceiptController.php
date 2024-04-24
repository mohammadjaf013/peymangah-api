<?php

namespace Modules\Contract\App\Http\Controllers;

use App\Events\SendSmsActiveEvent;
use App\Events\SendSmsActiveReceiptEvent;
use App\Events\SendSmsPreviewEvent;
use App\Events\SendSmsPreviewReceiptEvent;
use App\Http\Controllers\Controller;
use App\Libs\Helper\UrlHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use Modules\Contract\App\Http\Requests\ReceiptCreateRequest;
use Modules\Contract\App\Resources\ContractItemResource;
use Modules\Contract\App\Resources\ContractResource;
use Modules\Contract\App\Resources\ContractTempResource;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\App\Resources\Receipt\ReceiptDetailsResource;
use Modules\Contract\App\Resources\Receipt\ReceiptUserResource;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\ContractCatItemModel;
use Modules\Contract\Models\ContractItemModel;
use Modules\Contract\Models\ContractLogModel;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\PaymentLogModel;
use Modules\Contract\Models\Receipt\ReceiptLogModel;
use Modules\Contract\Models\Receipt\ReceiptModel;
use PDF;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

//use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public array $data = [];


    public function list(Request $request): JsonResponse
    {

        $query = ReceiptModel::query()
            ->where("user_id", auth()->id())
            ->latest();

        if($request->has("status") && $request->get("status") != "all"){
            $query->where("status",$request->get("status"));
        }
        if($request->has("title") &&  !empty($request->get("title"))){
            $query->where("title","like", "%".$request->get("title")."%");
        }
        $contracts =$query->paginate(15);


        return response()->json(['receipts' => ReceiptDetailsResource::collection($contracts),'page'=>$contracts->currentPage(),'total'=>$contracts->total(),'perPage'=>$contracts->perPage(), 'status' => true]);

    }
    /**
     * Display a listing of the resource.
     */
    public function pdf(Request $request, $id)
    {

        $contract = ReceiptModel::query()
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }
//

//        return view('pdf.contract', ['contract'=>$contract]);

        $pdf = PDF::loadView('pdf.receipt', ['receipt' => $contract], [], [
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
    public function create(ReceiptCreateRequest $request): JsonResponse
    {


        $contract = new ReceiptModel();
        $contract->user_id = auth()->id();
        $contract->title = $request->post('title');
        $contract->body = $request->post('body');
        $contract->save();


        $log = new ReceiptLogModel();
        $log->receipt_id = $contract->id;
        $log->user_id = auth()->id();
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "CREATE";
        $log->message = "ایجاد رسید";
        $log->save();

        return response()->json(['status' => true, 'receipt_id' => $contract->code]);
    }


    /**
     * Display a listing of the resource.
     */
    public function active(Request $request, $id): JsonResponse
    {

        $contract = ReceiptModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }
        if ($contract->is_paid == 0) {
            return response()->json(['message' => 'مبلغ رسید را پرداخت کنید سپس درخواست فعال سازی را ارسال نمایید.'], 404);
        }

        $contract->is_locked = 1;
        $contract->status = "signing";
        $contract->save();

        $log = new ReceiptLogModel();
        $log->receipt_id = $contract->id;
        $log->user_id = auth()->id();
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "ACTIVE";
        $log->message = "فعل سازی  رسید";
        $log->save();
        event(new SendSmsActiveReceiptEvent($contract));

        return response()->json(['status' => true]);

    }

    public function sendsms(Request $request, $id): JsonResponse
    {

        $contract = ReceiptModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }
        if ($contract->is_locked) {
            return response()->json(['message' => 'رسید فعال سازی شده قابل ارسال پیشنمایش نیست.'], 404);
        }


        event(new SendSmsPreviewReceiptEvent($contract));


        $log = new ReceiptLogModel();
        $log->receipt_id = $contract->id;
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

        $contract = ReceiptModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }
        return response()->json(['receipt' => new ReceiptDetailsResource($contract), 'users' => ReceiptUserResource::collection($contract->users), 'status' => true]);

    }


    /**
     * Display a listing of the resource.
     */
    public function payment(Request $request, $id): JsonResponse
    {

        $contract = ReceiptModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }

        if ($contract->is_paid) {
            return response()->json(['type' => "return", 'link' => UrlHelper::url("/panel/receipt/" . $contract->code . "/details")]);

        }
        $contract->price = (is_null($contract->price)) ? 25000 : $contract->price;

        $log = new PaymentLogModel();
        $log->user_id = auth()->id();
        $log->contract_id = $contract->id;
        $log->price = $contract->price;
        $log->is_paid = 0;
        $log->save();

        $callback = url("/api/receipt/banback?id=" . $log->code);

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
        $contract = ReceiptModel::query()->where("id", $log->contract_id)->first();

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


            $log = new ReceiptLogModel();
            $log->receipt_id = $contract->id;
            $log->user_id = auth()->id();
            $log->ip = $request->getClientIp();
            $log->user_agent = $request->userAgent();
            $log->event = "PAY";
            $log->message = "پرداخت";
            $log->save();


            DB::commit();
            return redirect(UrlHelper::url("/panel/receipt/" . $contract->code . "/details"));

        } catch (InvalidPaymentException $exception) {
            report($exception);
            DB::rollBack();
            $log->error_data = serialize($exception);
            $log->error_msg = $exception->getMessage();
            $log->save();
            DB::commit();
            return redirect(UrlHelper::url("/panel/receipt/" . $contract->code . "/payment"));


        }
    }


    public function update(Request $request, $id): JsonResponse
    {


        $contract = ReceiptModel::query()
            ->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }
        if ($contract->is_locked == 1) {
            return response()->json(['message' => 'امکان بروزرسانی وجود ندارد.'], 404);
        }

        DB::beginTransaction();

        try {
            $contract->title = $request->post("title");
            $contract->body = $request->post("body");
            $contract->save();
            DB::commit();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => 'خطا داده های ارسالی معتبر نمیباشد..'], 404);

        }
    }
}
