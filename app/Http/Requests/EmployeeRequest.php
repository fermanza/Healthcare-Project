<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'person_id' => 'required|exists:people,id',
            'type' => 'required',
            'is_full_time' => 'boolean',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $employee
     * @return null
     */
    public function save(Model $employee)
    {
        $employee->person_id = $this->person_id;
        $employee->type = $this->type;
        $employee->is_full_time = $this->is_full_time ?: false;
        $employee->save();
    }
}
