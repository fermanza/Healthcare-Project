<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * The Request where the filters come in.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The Builder to apply the filters.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Filter constructor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filter to given Builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function apply(Builder $query)
    {
        $this->query = $query;

        foreach ($this->input() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }

        return $this->query;
    }

    /**
     * Get the underlying request input to filter.
     *
     * @return array
     */
    public function input()
    {
        return array_filter($this->request->all());
    }
}
