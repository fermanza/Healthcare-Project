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
        $commonRules = [
            'employeeType' => 'required',
            'isFullTime' => 'boolean',
        ];

        if ($this->isCreate()) {
            $methodRules = [
                'personId' => 'required|exists:tPerson,id',
            ];
        } else {
            $methodRules = [];
        }

        return array_merge($commonRules, $methodRules);
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $employee
     * @return null
     */
    public function save(Model $employee)
    {
        if ($this->isCreate()) {
            $employee->personId = $this->personId;
        }
        
        $employee->employeeType = $this->employeeType;
        $employee->isFullTime = $this->isFullTime ?: false;
        $employee->save();
    }
}
