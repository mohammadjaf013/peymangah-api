<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Contract\Models\PaymentLogModel;
use Modules\User\App\Resources\UserTransactionsResource;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {


        $transactions = PaymentLogModel::query()
            ->latest()
            ->where("user_id", auth()->id())->paginate(15);


        return response()->json(['transactions' => UserTransactionsResource::collection($transactions),
            'page'=>$transactions->currentPage(),'total'=>$transactions->total(),'perPage'=>$transactions->perPage(), 'status' => true
        ]);

    }
}
