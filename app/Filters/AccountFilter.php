<?php

namespace App\Filters;

class AccountFilter extends Filter
{
    /**
     * Apply RSCs filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function RSCs($ids)
    {
        $this->query->whereHas('rsc', function ($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }

    /**
     * Apply practices filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function practices($ids)
    {
        $this->query->whereHas('practices', function($query) use ($ids) {
            $query->whereIn('practiceId', $ids);
        });
    }

    /**
     * Apply affiliation filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function affiliations($ids)
    {
        $this->query->whereHas('systemAffiliation', function($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }

    /**
     * Apply recruiters filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function recruiters($ids)
    {
        $this->query->whereHas('recruiter', function($query) use ($ids) {
            $query->whereIn('employeeId', $ids);
        });
    }

    /**
     * Apply managers filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function managers($ids)
    {
        $this->query->whereHas('manager', function($query) use ($ids) {
            $query->whereIn('employeeId', $ids);
        });
    }

    /**
     * Apply regions filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function regions($ids)
    {
        $this->query->whereHas('region', function($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }

    /**
     * Apply DOO filter.
     *
     * @param  array  $ids
     * @return void
     */
    public function DOO($ids)
    {
        $this->query->whereHas('dca', function($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }
}
