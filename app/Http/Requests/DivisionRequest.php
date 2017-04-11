<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class DivisionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'groupId' => 'required|exists:tGroup,id',
            'name' => 'required',
            'code' => '',
            'isJV' => 'boolean',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $division
     * @return null
     */
    public function save(Model $division)
    {
        $division->groupId = $this->groupId;
        $division->name = $this->name;
        $division->code = $this->code;
        $division->isJV = $this->isJV ?: false;
        $division->save();
    }
}
