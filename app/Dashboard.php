<?php

namespace App;

class Dashboard extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tDashboard';

    /**
     * Get the Users for the Dashboard.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'tUserToDashboard', 'id', 'userId');
    }
}
