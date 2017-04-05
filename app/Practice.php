<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    /**
     * Get the Accounts for the Practice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
