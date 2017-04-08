<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteCode extends Model
{
    /**
     * Get the Account for the SiteCode.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
