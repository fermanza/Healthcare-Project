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

    public function lastUpdate() {
        return $this->rostersBenchs->concat($this->recruitings)->concat($this->locums)
        ->sortByDesc(function ($relationship) {
            return $relationship->lastUpdated;
        })->first();
    }

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
     * Get the pipeline's account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'accountId');
    }


}
