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
        'declined'
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
     * Get the recruiting's pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'pipelineId');
    }
}
