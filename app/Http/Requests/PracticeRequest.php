<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class PracticeRequest extends FormRequest
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
            'code' => '',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $practice
     * @return null
     */
    public function save(Model $practice)
    {
        $practice->name = $this->name;
        $practice->code = $this->code;
        $practice->save();
    }
}
