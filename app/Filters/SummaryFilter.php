<?php

namespace App\Filters;

use Carbon\Carbon;

class SummaryFilter extends Filter
{
    /**
     * Apply practices filter.
     *
     * @param  array  $names
     * @return void
     */
    public function practices($names)
    {
        $this->query->whereIn('vAccountSummary.practice', $names);
    }

    /**
     * Apply divisions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function divisions($ids)
    {
        $this->query->whereIn('tAccount.divisionId', $ids);
    }

    /**
     * Apply recruiters filter.
     *
     * @param  array  $names
     * @return void
     */
    public function recruiters($names)
    {
        $this->query->whereIn('vAccountSummary.RSC Recruiter', $names);
    }

    /**
     * Apply managers filter.
     *
     * @param  array  $names
     * @return void
     */
    public function managers($names)
    {
        $this->query->whereIn('vAccountSummary.Managers', $names);
    }

    /**
     * Apply regions filter.
     *
     * @param  array  $names
     * @return void
     */
    public function regions($names)
    {
        $this->query->whereIn('vAccountSummary.Operating Unit', $names);
    }
    
    /**
     * Apply RSCs filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function RSCs($ids)
    {
        $this->query->whereIn('tAccount.RSCId', $ids);
    }

    /**
     * Apply contractOutDate filter.
     *
     * @param  string  $date
     * @return void
     */
    public function monthEndDate($date)
    {
        $monthYear = explode('-', $date);
        $month = $monthYear[0];
        $year = $monthYear[1];

        $this->query->whereYear('vAccountSummary.MonthEndDate', $year)
            ->whereMonth('vAccountSummary.MonthEndDate', $month);
    }
}
