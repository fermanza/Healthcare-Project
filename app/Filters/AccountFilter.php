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
}
