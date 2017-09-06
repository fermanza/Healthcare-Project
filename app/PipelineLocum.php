<?php

namespace App;

class PipelineLocum extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccountPipelineLocum';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'instance',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'application',
        'interview',
        'potentialStart',
        'startDate',
        'declined',
    ];

    /**
     * Accessor for instance attribute.
     *
     * @return string
     */
    public function getInstanceAttribute()
    {
        return 'locum';
    }
}
