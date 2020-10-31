<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;

class ReportController extends Controller
{
    /**
     * @var ReportRepository
     */
    private $repo;

    public function __construct(ReportRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Retrieve subtotals grouped by type and category
     *
     * @param $year
     * @param $month
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($year, $month, $tz)
    {
        // DB::enableQueryLog();
        list($start, $end) = $this->getMonthPeriod($year, $month, $tz);
        $generalSubTotals = $this->repo->getSubtotals(null, $end);
        $monthSubTotals = $this->repo->getSubtotals($start, $end);
        // Log::debug('SQL', DB::getQueryLog());

        return response()->json([
            'general' => $generalSubTotals,
            'monthly' => $monthSubTotals,
        ]);
    }
}
