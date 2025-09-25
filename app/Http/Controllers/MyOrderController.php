<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyOrderRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use App\Services\BookingTransactionService;

class MyOrderController extends Controller
{
    private $bookingTransactionService;

    public function __construct(BookingTransactionService $bookingTransactionService)
    {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    public function index()
    {
        $userId = auth()->id();
        $orders = $this->bookingTransactionService->getAllForUser($userId);
        return response()->json(TransactionResource::collection($orders));
    }

    public function show(int $id)
    {
        $userId = auth()->id();
        try {
            $order = $this->bookingTransactionService->getById($id, $userId);
            return response()->json(new TransactionResource($order));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
    
    public function store(MyOrderRequest $request)
    {
        $data = $request->validated();

        try {
            $order = $this->bookingTransactionService->create($data);
            return response()->json(new TransactionResource($order), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }




}
