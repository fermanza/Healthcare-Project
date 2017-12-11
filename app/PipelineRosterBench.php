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
        'provisionalPrivilegeStart'
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

    /**
     * Get the provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'providerId');
    }

    /**
     * Get the rosterbench's pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'pipelineId');
    }

    /**
     * Get the credentialingNotes.
     *
     * @param  string  $value
     * @return string
     */
    public function getCredentialingNotesAttribute($value)
    {
        return utf8_decode($value);
    }
}
