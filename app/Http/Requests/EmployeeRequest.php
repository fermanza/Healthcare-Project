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
            'personId' => 'required|exists:tPerson,id',
            'employeeType' => 'required',
            'isFullTime' => 'boolean',
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
        $employee->personId = $this->personId;
        $employee->employeeType = $this->employeeType;
        $employee->isFullTime = $this->isFullTime ?: false;
        $employee->save();
    }
}
