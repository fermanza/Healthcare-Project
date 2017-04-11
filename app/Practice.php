<?php

namespace App;

class Practice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tPractice';

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
