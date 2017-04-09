<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class PositionTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $positionType
     * @return null
     */
    public function save(Model $positionType)
    {
        $positionType->name = $this->name;
        $positionType->save();
    }
}
