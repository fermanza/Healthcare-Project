<?php

namespace App;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tGroup';

    /**
     * Get the Divisions for the Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function divisions()
    {
        return $this->hasMany(Division::class, 'groupId');
    }

    /**
     * Get the Region for the Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }
}
