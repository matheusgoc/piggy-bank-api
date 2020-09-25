<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionUserResource;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private $repo;

    public function __construct()
    {
        $this->repo = new TransactionRepository();
    }

    public function list($date, $direction)
    {
        $transactionList = $this->repo->list($date, $direction);

        foreach ($transactionList as $transaction) {
            Log::info($transaction->id);
        }

        return TransactionUserResource::collection($transactionList);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $file = $request->file('receipt');
        $transactionUser = $this->repo->create($data, $file);

        return new TransactionUserResource($transactionUser);
    }

    public function show(Transaction $transaction)
    {
        $transactionUser = $this->repo->get($transaction);

        return new TransactionUserResource($transactionUser);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->all();
        $receipt = $request->file('receipt');
        $transactionUser = $this->repo->update($transaction, $data, $receipt);

        return new TransactionUserResource($transactionUser);
    }

    public function destroy(Transaction $transaction)
    {
        $this->repo->delete($transaction);

        return response()->noContent();
    }
}
