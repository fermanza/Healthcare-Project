<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * Get the Divisions for the Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function divisions()
    {
        return $this->hasMany(Division::class);
    }
}
