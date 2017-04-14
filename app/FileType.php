<?php

namespace App;

class FileType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tFilelogFileType';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'fileTypeId';

    /**
     * Get the FileFeed for the FileType.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feed()
    {
        return $this->belongsTo(FileFeed::class, 'feedId');
    }
}
