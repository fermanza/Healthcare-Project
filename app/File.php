<?php

namespace App;

class File extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tFilelog';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'fileLogId';

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'downloadDate',
        'processedDate',
        'modifiedDate',
    ];

    /**
     * Get the FileType for the File.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(FileType::class, 'fileTypeId');
    }
}
