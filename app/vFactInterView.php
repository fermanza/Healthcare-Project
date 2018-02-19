<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Builder;

class vFactInterview extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vFactInterviewsAndApps';

    /**
     * Scope a query to check if account is new or same store.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNew(Builder $query, $new = null)
    {
        $date = date('Y-m-d');

    	if ($new) {
	        return $new == 1 
	            ? $query->whereRaw('(select case 
                        when round(datediff('.$date.', StartDate) / 30, 1) > 0 then round(datediff('.$date.', StartDate) / 30, 1)
                        else 0
                    end as months) <= 7')
	            : $query->whereRaw('(select case 
                        when round(datediff('.$date.', StartDate) / 30, 1) > 0 then round(datediff('.$date.', StartDate) / 30, 1)
                        else 0
                    end as months) > 7');
	    }
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'accountId');
    }
}
