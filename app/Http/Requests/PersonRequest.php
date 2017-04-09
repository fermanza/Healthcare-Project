<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class PersonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $person
     * @return null
     */
    public function save(Model $person)
    {
        $person->first_name = $this->first_name;
        $person->last_name = $this->last_name;
        $person->save();
    }
}
