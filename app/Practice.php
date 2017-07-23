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

    /**
     * Check if Practice is IPS
     *
     * @return bool
     */
    public function isIPS()
    {
        return starts_with($this->name, 'IPS');
    }

    /**
     * Check if Practice is ED
     *
     * @return bool
     */
    public function isED()
    {
        return starts_with($this->name, 'ED');
    }
}
