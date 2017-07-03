<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class PermissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required',
            'display_name' => 'required',
            // 'description' => '',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $employee
     * @return null
     */
    public function save(Model $permission)
    {
        // $permission->name = $this->name;
        $permission->display_name = $this->display_name;
        // $permission->description = $this->description;
        $permission->save();
    }
}
