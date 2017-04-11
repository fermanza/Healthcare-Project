<?php

namespace App\Http\Requests;

use App\FileFeed;
use Carbon\Carbon;
use App\FileStatus;
use Illuminate\Database\Eloquent\Model;

class FileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $commonRules = [
            'fileTypeId' => 'required|exists:tFilelogFileType,fileTypeId',
        ];

        if ($this->isCreate()) {
            $methodRules = [
                'file' => 'required|file',
            ];
        } else {
            $methodRules = [];
        }

        return array_merge($commonRules, $methodRules);
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $file
     * @return null
     */
    public function save(Model $file)
    {
        if ($this->isCreate()) {
            $uploadedFile = $this->file('file');
            $filename = $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->store('files', 's3');

            $file->statusTypeId = FileStatus::where('statusName', 'To Process')->value('statusTypeId');
            $file->feedId = FileFeed::where('feedName', 'Admin Uploads')->value('feedId');
            $file->filename = $filename;
            $file->path = $path;
            $file->downloadDate = Carbon::now();
            $file->modifiedDate = Carbon::now();
        }
            
        $file->fileTypeId = $this->fileTypeId;
        $file->save();
    }
}
