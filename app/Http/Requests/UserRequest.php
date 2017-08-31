<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $commonRules = [
            'name' => 'required',
            'email' => 'required|email',
            'roles' => 'required|array|exists:tRole,id',
            'employeeId' => 'nullable|exists:tEmployee,id',
            'RSCId' => 'nullable|exists:tRSC,id',
            'operatingUnitId' => 'nullable|exists:tOperatingUnit,id',
        ];

        if ($this->isCreate()) {
            $methodRules = [
                'password' => 'required|min:6',
            ];
        } else {
            $methodRules = [
                'password' => 'nullable|min:6',
            ];
        }

        return array_merge($commonRules, $methodRules);
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $employee
     * @return null
     */
    public function save(Model $user)
    {
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = $this->password ? bcrypt($this->password) : $user->password;
        $user->employeeId = $this->employeeId;
        $user->RSCId = $this->RSCId;
        $user->operatingUnitId = $this->operatingUnitId;
        $user->save();

        $user->roles()->sync($this->roles);
    }
}
