<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionUserResource;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private $repo;

    public function __construct()
    {
        $this->repo = new TransactionRepository();
    }

    public function list($year, $month, $limit = null)
    {
        $transactionList = $this->repo->list($year, $month, $limit);

        return TransactionUserResource::collection($transactionList);
    }

    public function listSlice($date = null, $direction = 'after', $limit = 30)
    {
        if (!$date) {
            $date = date('Y-m-d', strtotime('last day of last month'));
        }

        $transactionList = $this->repo->listSlice($date, $direction, $limit);

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
