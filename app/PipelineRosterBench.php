<?php

namespace App;

use Carbon\Carbon;

class PipelineRosterBench extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccountPipelineRosterBench';

    protected $casts = [
        'hours' => 'double',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'interview',
        'contractIn',
        'contractOut',
        'firstShift',
        'resigned',
        'lastShift'
    ];
}
