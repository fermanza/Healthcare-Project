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

    /**
     * Get the Roster Physicians for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rosterPhysicians()
    {
        return $this->hasMany(PipelineRosterBench::class, 'pipelineId')
            ->where('place', 'roster')->where('activity', 'physician');
    }

    /**
     * Get the Roster APPs for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rosterApps()
    {
        return $this->hasMany(PipelineRosterBench::class, 'pipelineId')
            ->where('place', 'roster')->where('activity', 'app');
    }

    /**
     * Get the Bench Physicians for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function benchPhysicians()
    {
        return $this->hasMany(PipelineRosterBench::class, 'pipelineId')
            ->where('place', 'bench')->where('activity', 'physician');
    }

    /**
     * Get the Bench APPs for the Pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function benchApps()
    {
        return $this->hasMany(PipelineRosterBench::class, 'pipelineId')
            ->where('place', 'bench')->where('activity', 'app');
    }
}
