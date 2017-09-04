<?php

namespace App;

use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Carbon\Carbon;

class Model extends BaseModel
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = false;

    /**
     * Scope a query to the given Filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, Filter $filter)
    {
        return $filter->apply($query);
    }

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @return string
     */
    public function fromDateTime($value)
    {
        if(strlen($value) <= 10) {
            return Carbon::createFromFormat(
                'm/d/Y', $value
            );
        } else {
            return Carbon::createFromFormat(
                'm/d/Y H:m:s', $value
            );
        }
    }
}
