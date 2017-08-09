<?php

namespace App;

class RSC extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tRSC';

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * Get the Director (Employee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function director()
    {
        return $this->hasOne(Employee::class, 'directorId');
    }
}
