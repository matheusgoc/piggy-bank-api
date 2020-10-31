<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Retrieve the first and last day of a given month in a year
     *
     * @param $year
     * @param $month
     * @return array
     */
    protected function getMonthPeriod($year, $month, $tz = 'UTC')
    {
        $date = (new \DateTime())->setDate($year, $month, 1);
        $start = Carbon::parse($date->format('Y-m-d').' 00:00:00', $tz)->setTimezone('UTC');
        $end = Carbon::parse($date->format('Y-m-t').' 23:59:59', $tz)->setTimezone('UTC');

        return [$start, $end];
    }
}
