<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhysiciansApps extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tPhysicianAppHistory';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Get the Account for the SiteCode.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'accountId');
    }
}
