<?php

namespace App;

use Carbon\Carbon;
use Laracasts\Presenter\PresentableTrait;
use App\Presenters\SummaryPresenter;

class AccountSummary extends Model
{

    use PresentableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vAccountSummary';
    protected $presenter = SummaryPresenter::class;

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
    public function getMonthsSinceCreated($date = null)
    {
        if (! $this->{'Start Date'}) {
            return INF;
        }

        $targetDate = Carbon::now();

        if ($date) {
            $monthYear = explode('-', $date);
            $month = $monthYear[0];
            $year = $monthYear[1];

            $targetDate = Carbon::parse(date($year.'-'.$month.'-'.'01'));
        }

        $monthDays = 30;

        if($this->{'Start Date'}->gte(Carbon::now())) {
            return 0;
        } else {
            $days = $targetDate->diffInDays($this->{'Start Date'});
        }
        
        $months = $days / $monthDays;

        return number_format($months, 1);
    }
    
    public function account()
    {
        return $this->belongsTo(Account::class, 'accountId');
    }

    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }
}
