<?php

namespace App\Filters;

class ContractLogsFilter extends Filter
{
    /**
     * Apply divisions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function divisions($ids)
    {
        $this->query->whereIn('divisionId', $ids);
    }

    /**
     * Apply practices filter.
     *
     * @param  array  $names
     * @return void
     */
    public function practices($names)
    {
        $this->query->whereHas('practice', function ($query) use ($names) {
            $query->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name', 'like', "{$name}%");
                }
            });
        });
    }

    /**
     * Apply statuses filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function statuses($ids)
    {
        $this->query->whereHas('status', function ($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }

    /**
     * Apply positions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function positions($ids)
    {
        $this->query->whereIn('positionId', $ids);
    }

    /**
     * Apply contractOutDate filter.
     *
     * @param  string  $date
     * @return void
     */
    public function contractOutDate($date)
    {
        $this->query->where('contractOutDate', $date);
    }

    /**
     * Apply contractInDate filter.
     *
     * @param  string  $date
     * @return void
     */
    public function contractInDate($date)
    {
        $this->query->where('contractInDate', $date);
    }

    /**
     * Apply hospitalName filter.
     *
     * @param  string  $name
     * @return void
     */
    public function hospitalName($name)
    {
        $this->query->whereHas('account', function ($query) use ($name) {
            $query->where('name', $name);
        });
    }
}
