<?php

namespace App;

use Carbon\Carbon;

class AccountSummary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vAccountSummary';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'Start Date',
        'MonthEndDate',
    ];

    protected $dateFormat = 'Y-m-d H:i:s+';

    /**
     * Determines if start date is less than 7 months ago.
     *
     * @return boolean
     */
    public function isRecentlyCreated()
    {
        $monthsToGetOld = 7;
        $monthsSinceCreated = $this->getMonthsSinceCreated();

        return $monthsSinceCreated < $monthsToGetOld;
    }

    /**
     * Returns the number of months since created.
     *
     * @return float
     */
    public function getMonthsSinceCreated()
    {
        if (! $this->{'Start Date'}) {
            return INF;
        }
        
        $monthDays = 30;
        $days = Carbon::now()->diffInDays($this->{'Start Date'});
        $months = $days / $monthDays;

        return number_format($months, 1);
    }
}
