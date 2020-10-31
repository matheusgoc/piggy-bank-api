<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionUserResource;
use App\Models\Transaction;
use App\Repositories\ReportRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private $repo;

    public function __construct()
    {
        $this->repo = new TransactionRepository();
    }

    public function list($year, $month, $tz, $limit = null)
    {
        //DB::enableQueryLog();
        $reportRepo = new ReportRepository();
        list($start, $end) = $this->getMonthPeriod($year, $month, $tz);
        $transactionList = $this->repo->list($start, $end, $limit);
        $generalSubTotals = $reportRepo->getSubtotals(null, $end);
        $monthSubTotals = $reportRepo->getSubtotals($start, $end);
        //Log::debug('SQL', DB::getQueryLog());

        return response()->json([
            'transactions' => TransactionUserResource::collection($transactionList),
            'reports' => [
                'general' => $generalSubTotals,
                'monthly' => $monthSubTotals,
            ]
        ]);
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
