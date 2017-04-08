<?php

namespace App\Http\Requests;

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
            'name' => 'required',
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

            $file->filename = $filename;
            $file->path = $path;
        }
            
        $file->name = $this->name;
        $file->save();
    }
}
