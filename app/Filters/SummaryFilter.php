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
        $this->query->whereIn('Operating Unit', $names);
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
     * Apply states filter.
     *
     * @param  array  $states
     * @return void
     */
    public function states($states)
    {
        $this->query->whereHas('account', function($query) use ($states) {
            $query->whereIn('state', $states);
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

    /**
     * Apply groups filter.
     *
     * @param  array  $groups
     * @return void
     */
    public function groups($groups)
    {
        $this->query->whereHas('account.division.group', function($query) use ($groups) {
            $query->whereIn('groupId', $groups);
        });
    }

    /**
     * Apply sites filter.
     *
     * @param  array  $sites
     * @return void
     */
    public function sites($sites)
    {
        $this->query->whereHas('account', function($query) use ($sites) {
            $query->whereIn('id', $sites);
        });
    }

    /**
     * Apply cities filter.
     *
     * @param  array  $cities
     * @return void
     */
    public function cities($cities)
    {
        $this->query->whereIn('city', $cities);
    }

    /**
     * Apply termed filter.
     *
     * @param  int $val
     * @return void
     */
    public function termed($val)
    {
        $this->query->whereHas('account', function($query) use ($val) {
            if($val == 1) {
                $query->whereNull('endDate'); 
            } elseif ($val == 2) {
                $query->whereNotNull('endDate');
            }
        });
    }

    /**
     * Apply SVP filter.
     *
     * @param  array  $names
     * @return void
     */
    public function SVP($names)
    {
        $this->query->whereHas('account.pipeline', function($query) use ($names) {
            $query->whereIn('SVP', $names);
        });
    }

    /**
     * Apply RMD filter.
     *
     * @param  array  $names
     * @return void
     */
    public function RMD($names)
    {
        $this->query->whereHas('account.pipeline', function($query) use ($names) {
            $query->whereIn('RMD', $names);
        });
    }
}
