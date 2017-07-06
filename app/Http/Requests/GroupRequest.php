<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class GroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'regionId' => 'required|exists:tOperatingUnit,id',
            'name' => 'required',
            'code' => '',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $group
     * @return null
     */
    public function save(Model $group)
    {
        $group->regionId = $this->regionId;
        $group->name = $this->name;
        $group->code = $this->code;
        $group->save();
    }
}
