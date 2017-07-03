<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class RoleRequest extends FormRequest
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
            'display_name' => 'required',
            'description' => '',
            // 'permissions' => 'required|array|exists:tPermission,id',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $employee
     * @return null
     */
    public function save(Model $role)
    {
        $role->name = $this->name;
        $role->display_name = $this->display_name;
        $role->description = $this->description;
        $role->save();

        // $role->permissions()->sync($this->permissions);
    }
}
