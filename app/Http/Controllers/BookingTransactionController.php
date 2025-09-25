<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Services\BookingTransactionService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingTransactionController extends Controller
{
    //
    private $bookingTransactionService;

    public function __construct(BookingTransactionService $bookingTransactionService)
    {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    public function index(array $fields)
    {
        $transactions = $this->bookingTransactionService->getAll($fields);
        return response()->json(TransactionResource::collection($transactions), 200);
    }

    public function show(int $id, array $fields)
    {
        try {
            $transaction = $this->bookingTransactionService->getIdForManager($id, $fields);
            return response()->json(new TransactionResource($transaction), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }

    public function updateStatus(Request $request, int $transactionId)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:Approved,Rejected'],
        ]);
        
        try {
            $transaction = $this->bookingTransactionService->updateStatus($transactionId, $request->only('status'));
            return response()->json(
                [
                    'message' => 'Transaction status updated successfully',
                    'data' => new TransactionResource($transaction)
                ], 200
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
