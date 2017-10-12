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
        'lastShift',
        'fileToCredentialing',
        'privilegeGoal',
        'appToHospital',
        'lastUpdated'
    ];

    /**
     * Get user who updated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'lastUpdatedBy');
    }
}
