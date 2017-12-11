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
        'contractOut',
        'startDate',
        'declined'
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
     * Get the locum'spipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'pipelineId');
    }
}
