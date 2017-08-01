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
     * Accessor for instance attribute.
     *
     * @return string
     */
    public function getInstanceAttribute()
    {
        return 'recruiting';
    }
}
