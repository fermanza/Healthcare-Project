<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteCode extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tSiteCodeHistory';

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
