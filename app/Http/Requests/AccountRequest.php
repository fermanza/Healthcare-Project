<?php

namespace App\Http\Requests;

class AccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'site_code' => 'required|numeric',
            'photo_path' => '',
            'recruiter_id' => 'exists:employees,id',
            'manager_id' => 'exists:employees,id',
            'practice_id' => 'exists:practices,id',
            'division_id' => 'exists:divisions,id',
            'google_address' => '',
            'street' => '',
            'number' => '',
            'city' => '',
            'state' => '',
            'zip_code' => '',
            'country' => '',
            'latitude' => 'between:-90,90',
            'longitude' => 'between:-180,180',
            'start_date' => 'nullable|date_format:"Y-m-d H:i"',
            'physicians_needed' => 'integer|min:0',
            'apps_needed' => 'integer|min:0',
            'physician_hours_per_month' => 'integer|min:0',
            'app_hours_per_month' => 'integer|min:0',
            'press_release' => 'boolean',
            'press_release_date' => 'nullable|date_format:"Y-m-d"',
            'management_change_mailers' => 'boolean',
            'recruiting_mailers' => 'boolean',
            'email_blast' => 'boolean',
            'purl_campaign' => 'boolean',
            'marketing_slick' => 'boolean',
            'collaboration_recruiting_team' => 'boolean',
            'collaboration_recruiting_team_names' => '',
            'compensation_grid' => 'boolean',
            'compensation_grid_bonuses' => '',
            'recruiting_incentives' => 'boolean',
            'recruiting_incentives_description' => '',
            'locum_companies_notified' => 'boolean',
            'search_firms_notified' => 'boolean',
            'departments_coordinated' => 'boolean',
        ];
    }

    public function save($account)
    {
        $account->name = $this->name;
        $account->site_code = $this->site_code;
        $account->photo_path = $this->photo_path;
        $account->recruiter_id = $this->recruiter_id;
        $account->manager_id = $this->manager_id;
        $account->practice_id = $this->practice_id;
        $account->division_id = $this->division_id;
        $account->google_address = $this->google_address;
        $account->street = $this->street;
        $account->number = $this->number;
        $account->city = $this->city;
        $account->state = $this->state;
        $account->zip_code = $this->zip_code;
        $account->country = $this->country;
        $account->latitude = $this->latitude;
        $account->longitude = $this->longitude;
        $account->start_date = $this->start_date ? $this->start_date.':00': null;
        $account->physicians_needed = $this->physicians_needed;
        $account->apps_needed = $this->apps_needed;
        $account->physician_hours_per_month = $this->physician_hours_per_month;
        $account->app_hours_per_month = $this->app_hours_per_month;
        $account->press_release = $this->press_release ?: false;
        $account->press_release_date = $this->press_release_date;
        $account->management_change_mailers = $this->management_change_mailers ?: false;
        $account->recruiting_mailers = $this->recruiting_mailers ?: false;
        $account->email_blast = $this->email_blast ?: false;
        $account->purl_campaign = $this->purl_campaign ?: false;
        $account->marketing_slick = $this->marketing_slick ?: false;
        $account->collaboration_recruiting_team = $this->collaboration_recruiting_team ?: false;
        $account->collaboration_recruiting_team_names = $this->collaboration_recruiting_team_names;
        $account->compensation_grid = $this->compensation_grid ?: false;
        $account->compensation_grid_bonuses = $this->compensation_grid_bonuses;
        $account->recruiting_incentives = $this->recruiting_incentives ?: false;
        $account->recruiting_incentives_description = $this->recruiting_incentives_description;
        $account->locum_companies_notified = $this->locum_companies_notified ?: false;
        $account->search_firms_notified = $this->search_firms_notified ?: false;
        $account->departments_coordinated = $this->departments_coordinated ?: false;
        $account->save();
    }
}
