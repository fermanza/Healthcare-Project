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
            'firstName' => 'required',
            'lastName' => 'required',
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
        $person->firstName = $this->firstName;
        $person->lastName = $this->lastName;
        $person->save();
    }
}
