<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class AffiliationRequest extends FormRequest
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
            'displayName' => 'required',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $affiliation
     * @return null
     */
    public function save(Model $affiliation)
    {
        $affiliation->name = $this->name;
        $affiliation->displayName = $this->displayName;

        $affiliation->save();
    }
}
