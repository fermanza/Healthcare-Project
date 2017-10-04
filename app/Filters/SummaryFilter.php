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
        $this->query->whereIn('Practice', $names);
    }

    /**
     * Apply affiliation filter.
     *
     * @param  array  $names
     * @return void
     */
    public function affiliations($names)
    {
        $this->query->whereIn('System Affiliation', $names);
    }

    /**
     * Apply recruiters filter.
     *
     * @param  array  $names
     * @return void
     */
    public function recruiters($names)
    {
        $this->query->whereIn('RSC Recruiter', $names);
    }

    /**
     * Apply managers filter.
     *
     * @param  array  $names
     * @return void
     */
    public function managers($names)
    {
        $this->query->whereIn('Managers', $names);
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
        $this->query->whereHas('account', function($query) use ($ids) {
            $query->whereIn('RSCId', $ids);
        });
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

        $this->query->whereYear('MonthEndDate', $year)
            ->whereMonth('MonthEndDate', $month);
    }

    /**
     * Apply DOO filter.
     *
     * @param  array  $names
     * @return void
     */
    public function DOO($names)
    {
        $this->query->whereIn('DOO', $names);
    }
}
