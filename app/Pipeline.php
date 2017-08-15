<?php

namespace App;

class Pipeline extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccountPipeline';

    protected $casts = [
        'staffPhysicianHaves' => 'float',
        'staffAppsHaves' => 'float',
        'staffPhysicianNeeds' => 'float',
        'staffAppsNeeds' => 'float'
    ];

    /**
     * Get the Roster Physicians for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rostersBenchs()
    {
        return $this->hasMany(PipelineRosterBench::class, 'pipelineId');
    }

    /**
     * Get the Recruitings for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recruitings()
    {
        return $this->hasMany(PipelineRecruiting::class, 'pipelineId');
    }

    /**
     * Get the Locums for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locums()
    {
        return $this->hasMany(PipelineLocum::class, 'pipelineId');
    }
}
