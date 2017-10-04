<?php

namespace App;

class PipelineRecruiting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccountPipelineRecruiting';

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
        'contractIn',
        'contractOut',
        'firstShift',
        'declined',
        'lastUpdated'
    ];

    /**
     * Accessor for instance attribute.
     *
     * @return string
     */
    public function getInstanceAttribute()
    {
        return 'recruiting';
    }
}
