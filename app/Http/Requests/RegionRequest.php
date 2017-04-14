<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class RegionRequest extends FormRequest
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
     * @param  \Illuminate\Database\Eloquent\Model  $region
     * @return null
     */
    public function save(Model $region)
    {
        $region->name = $this->name;
        $region->code = $this->code;
        $region->save();
    }
}
