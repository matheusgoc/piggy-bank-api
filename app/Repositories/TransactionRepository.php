<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\TransactionUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Category Repository
 * @package App\Repositories
 */
class TransactionRepository
{
    private const UPLOAD_RECEIPT_PATH = 'receipts';

    public function list($year, $month, $limit = null) {

        $date = (new \DateTime())->setDate($year, $month, 1);
        $start = $date->format('Y-m-d');
        $end = $date->format('Y-m-t');

        Log::info('TransactionRepository::list', [$date, $start, $end]);

        $builder = TransactionUser::where('user_id', '=', Auth::id())
            ->join('transactions', 'transaction_id', '=', 'id')
            ->whereBetween('ordered_at', [$start, $end])
            ->orderBy('ordered_at');

        if ($limit) {
            $builder->limit($limit);
        }

        DB::enableQueryLog();
        $result = $builder->get();
        Log::info('SQL', DB::getQueryLog());

        return $result;
    }

    public function listSlice($date, $direction, $limit) {

        // DB::enableQueryLog();
        // Log::info('SQL', DB::getQueryLog());

        $operator = ($direction === 'after')? '>' : '<';
        return TransactionUser::where('user_id', '=', Auth::id())
            ->join('transactions', 'transaction_id', '=', 'id')
            ->where('ordered_at', $operator, $date)
            ->orderBy('ordered_at')
            ->limit($limit)
            ->get();
    }

    public function get(Transaction $transaction) {

        return $transaction->currentUserTransaction;
    }

    public function create($data, UploadedFile $file = null) {

        $receipt = null;
        DB::beginTransaction();
        try {

            Log::info('file', [$file]);

            // upload receipt
            $receipt = $this->uploadReceipt($file);

            Log::info('data', [$data]);

            // create Transaction
            $transaction = new Transaction();
            $transaction->fill($data);
            $transaction->key = $data['key'];
            $transaction->receipt = $receipt;
            $transaction->save();

            // save Category
            $category = $this->saveCategory($data['category']);

            // create TransactionUser
            $transactionUser = new TransactionUser();
            $transactionUser->fill($data);
            $transactionUser->amount = $this->changeAmountSignalByType($data['amount'], $data['type']);
            $transactionUser->user_id = Auth::id();
            $transactionUser->transaction_id = $transaction->id;
            $transactionUser->category_id = $category->id;
            $transactionUser->is_owner = true;
            $transactionUser->save();

            DB::commit();

            return $transactionUser;

        } catch (\Exception $ex) {

            // delete receipt and rollback transaction
            if ($receipt) {
                $this->deleteReceipt($receipt);
            }
            DB::rollBack();
            throw $ex;
        }
    }

    public function update(Transaction $transaction, $data, UploadedFile $file = null) {

        $receipt = null;
        $receiptToRemove = null;
        DB::beginTransaction();
        try {

            // save Category
            $category = $this->saveCategory($data['category']);

            // retrieve CategoryUser and set its attributes
            $transactionUser = $transaction->currentUserTransaction;
            $transactionUser->fill($data);
            $transactionUser->category_id = $category->id;

            // keep transaction amount
            $amount = $transactionUser->amount;

            // case the current user is the owner of this transaction
            if ($transactionUser->is_owner) {

                // set transaction's amount
                $amount = $data['amount'];

                // remove receipt if asked for
                if (!empty($data['is_receipt_removed'])) {
                    $receiptToRemove = $transaction->receipt;
                    $transaction->receipt = null;
                }

                // upload receipt case there is a new one
                if ($file) {

                    $receipt = $this->uploadReceipt($file);
                    $receiptToRemove = $transaction->receipt;
                    $transaction->receipt = $receipt;
                }

                // set and save transaction
                $transaction->fill($data);
                $transaction->save();
            }

            // fix the amount signal according to the transaction's type
            $transactionUser->amount = $this->changeAmountSignalByType($amount, $data['type']);
            $transactionUser->save();

            // case the receipt was removed
            if ($receiptToRemove) {

                // remove previous receipt
                $this->deleteReceipt($receiptToRemove);
            }

            DB::commit();

            return $transactionUser;

        } catch (\Exception $ex) {

            // delete receipt and rollback transaction
            if ($receipt) {
                $this->deleteReceipt($receipt);
            }
            DB::rollBack();
            throw $ex;
        }
    }

    private function changeAmountSignalByType($amount, $type) {

        if (($type === 'E' && $amount > 0) ||
            ($type === 'I' && $amount < 0)) {
            $amount = -$amount;
        }

        return $amount;
    }

    private function saveCategory($categoryName) {

        $categoryRepo = new CategoryRepository();
        return $categoryRepo->create($categoryName);
    }

    private function uploadReceipt($file) {

        $receipt = null;
        if ($file) {
            $receipt = $file->storePublicly('public/'.self::UPLOAD_RECEIPT_PATH);
            $receipt = str_replace('public/', '', $receipt);
        }

        return $receipt;
    }

    private function deleteReceipt($receipt) {

        Storage::delete('public/'.$receipt);
    }

    public function delete(Transaction $transaction) {

        DB::beginTransaction();
        try {

            $transactionUser = $transaction->currentUserTransaction;
            $transactionUser->delete();

            $doesntHasOtherUser = $transaction->users()
                ->where('user_id', '<>', Auth::id())
                ->doesntExist();

            if ($doesntHasOtherUser) {

                $this->deleteTransactionCascade($transaction);
            }

            DB::commit();

        } catch (\Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }

    private function deleteTransactionCascade(Transaction $transaction) {

        function getTransactionsToDelete(Transaction $transaction) {

            $ids = [];
            $receipts = [];
            foreach($transaction->subTransactions as $subTransaction) {
                $subIdsAndReports = getTransactionsToDelete($subTransaction);
                array_push($ids, $subIdsAndReports['ids']);
                array_push($reports, $subIdsAndReports['reports']);
            }
            $ids[] = $transaction->id;
            if ($transaction->receipt) {
                $receipts[] = $transaction->receipt;
            }

            return [
                'ids' => $ids,
                'receipts' => $receipts
            ];
        }

        $transactionsToDelete = getTransactionsToDelete($transaction);
        Transaction::destroy($transactionsToDelete['ids']);
        foreach ($transactionsToDelete['receipts'] as $receipt) {
            Storage::move('public/'.$receipt, 'deleted/'.$receipt);
        }
    }
}
