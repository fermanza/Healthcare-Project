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
            'group_id' => 'required|exists:groups,id',
            'name' => 'required',
            'code' => '',
            'is_jv' => 'boolean',
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
        $division->group_id = $this->group_id;
        $division->name = $this->name;
        $division->code = $this->code;
        $division->is_jv = $this->is_jv ?: false;
        $division->save();
    }
}
