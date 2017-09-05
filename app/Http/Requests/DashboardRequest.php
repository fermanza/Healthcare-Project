<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use App\DashboardUser;

class DashboardRequest extends FormRequest
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
            'url' => 'required',
            'users' => 'nullable|array|exists:tUser,id',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $dashboard
     * @return null
     */
    public function save(Model $dashboard)
    {
        $dashboard->name = $this->name;
        $dashboard->description = $this->description;
        $dashboard->url = $this->url;

        $dashboard->save();

        $dashboard->users()->sync($this->users ?: []);
    }
}
