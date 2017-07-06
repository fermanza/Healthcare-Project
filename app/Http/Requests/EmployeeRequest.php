<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
            'employementStatusId' => 'required|exists:tEmployementStatus,id',
            'positionTypeId' => 'nullable|exists:tPositionType,id',
            'managerId' => [
                'nullable',
                Rule::exists('tEmployee', 'id')->where(function ($query) {
                    $query->whereIn('positionTypeId', [
                        config('instances.position_types.manager'),
                        config('instances.position_types.director'),
                    ]);
                }),
            ],
            'employeeType' => 'required',
            'EDPercent' => 'required|in:0,0.5,1',
            'IPSPercent' => 'required|in:0,0.5,1',
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

        $employee->employementStatusId = $this->employementStatusId;
        $employee->positionTypeId = $this->positionTypeId;
        $employee->managerId = $this->managerId;
        $employee->employeeType = $this->employeeType;
        $employee->EDPercent = $this->EDPercent;
        $employee->IPSPercent = $this->IPSPercent;
        $employee->save();
    }
}
