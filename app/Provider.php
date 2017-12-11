<?php

namespace App;

class Provider extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tProvider';

     /**
     * Get the provider's accounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'tProviderToAccount', 'providerId', 'accountId');
    }
}
