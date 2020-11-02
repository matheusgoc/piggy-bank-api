<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ReportRepository
 * @package App\Repositories
 */
class ReportRepository
{
    /**
     * Retrieve transactions' subtotals grouped by type and category
     *
     * @param $start
     * @param $end
     * @return array
     */
    public function getSubtotals($start = null, $end = null)
    {
        /*
           select
                tu.type,
                c.name,
                SUM(tu.amount) as subtotal
            from
                transactions t
            join transaction_user tu on
                tu.transaction_id = t.id
            join categories c on
                tu.category_id = c.id
            where
                t.ordered_at between ('2020-01-01 00:00:00' and '2020-12-30 23:59:59') and
                t.deleted_at is null and
                tu.deleted_at is null
            group by
                tu.type,
                c.name
            order by
                tu.type,
                SUM(ABS(tu.amount)) desc
        */

        $qb = DB::table('transactions', 't')
            ->join('transaction_user as tu', 'tu.transaction_id', '=', 't.id')
            ->join('categories as c', 'tu.category_id', '=', 'c.id')
            ->select('tu.type', 'c.name', DB::raw('sum(tu.amount) as subtotal'))
            ->where('user_id', Auth::id())
            ->whereNull('t.deleted_at')
            ->whereNull('tu.deleted_at')
            ->groupBy('tu.type', 'c.name')
            ->orderByRaw(DB::raw('SUM(ABS(tu.amount)) desc'));

        if ($start) $qb->where('t.ordered_at', '>=', $start);
        if ($end) $qb->where('t.ordered_at', '<=', $end);

        $subtotals = $qb->get();

        // handle totals
        $expenses = 0;
        $incomes = 0;
        $categories = [
            'incomes' => [],
            'expenses' => []
        ];
        foreach($subtotals as $row) {
            switch($row->type) {
                case 'E': $expenses += $row->subtotal; break;
                case 'I': $incomes += $row->subtotal; break;
            }
            $type = ($row->type == 'I')? 'incomes' : 'expenses';
            if (!isset($categories[$type][$row->name])) {
                $categories[$type][$row->name] = 0;
            }
            $categories[$type][$row->name] += round(abs($row->subtotal), 2);
        }

        // retrieve totals
        return [
            'expenses' => round(abs($expenses), 2),
            'incomes' => round($incomes, 2),
            'categories' => $categories,
        ];
    }
}
