<?php

namespace App\Filters;

class ProvidersFilter extends Filter
{
    /**
     * Apply sites filter.
     *
     * @param  array  $sites
     * @return void
     */
    public function sites($sites)
    {
        $this->query->whereHas('pipeline.account', function($query) use ($sites) {
            $query->whereIn('id', $sites);
        });
    }
}
