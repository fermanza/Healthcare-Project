<?php

namespace App\Http\Requests;

use App\SiteCode;
use App\PositionType;
use App\AccountEmployee;
use Illuminate\Database\Eloquent\Model;

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
            'name' => 'required',
            'siteCode' => 'required|numeric',
            'photoPath' => '',
            'recruiterId' => 'exists:tEmployee,id',
            'managerId' => 'exists:tEmployee,id',
            'practiceId' => 'exists:tPractice,id',
            'divisionId' => 'exists:tDivision,id',
            'googleAddress' => '',
            'street' => '',
            'number' => '',
            'city' => '',
            'state' => '',
            'zipCode' => '',
            'country' => '',
            'latitude' => 'between:-90,90',
            'longitude' => 'between:-180,180',
            'startDate' => 'nullable|date_format:"Y-m-d"',
            'physiciansNeeded' => 'integer|min:0',
            'appsNeeded' => 'integer|min:0',
            'physicianHoursPerMonth' => 'integer|min:0',
            'appHoursPerMonth' => 'integer|min:0',
            'pressRelease' => 'boolean',
            'pressReleaseDate' => 'nullable|date_format:"Y-m-d"',
            'managementChangeMailers' => 'boolean',
            'recruitingMailers' => 'boolean',
            'emailBlast' => 'boolean',
            'purlCampaign' => 'boolean',
            'marketingSlick' => 'boolean',
            'collaborationRecruitingTeam' => 'boolean',
            'collaborationRecruitingTeamNames' => '',
            'compensationGrid' => 'boolean',
            'compensationGridBonuses' => '',
            'recruitingIncentives' => 'boolean',
            'recruitingIncentivesDescription' => '',
            'locumCompaniesNotified' => 'boolean',
            'searchFirmsNotified' => 'boolean',
            'departmentsCoordinated' => 'boolean',
        ];
    }

    /**
     * Save the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    public function save(Model $account)
    {
        $pastSiteCode = $account->siteCode;

        $account->name = $this->name;
        $account->siteCode = $this->siteCode;
        $account->photoPath = $this->photoPath;
        $account->divisionId = $this->divisionId;
        $account->googleAddress = $this->googleAddress;
        $account->street = $this->street;
        $account->number = $this->number;
        $account->city = $this->city;
        $account->state = $this->state;
        $account->zipCode = $this->zipCode;
        $account->country = $this->country;
        $account->latitude = $this->latitude;
        $account->longitude = $this->longitude;
        $account->startDate = $this->startDate ? $this->startDate: null;
        $account->physiciansNeeded = $this->physiciansNeeded;
        $account->appsNeeded = $this->appsNeeded;
        $account->physicianHoursPerMonth = $this->physicianHoursPerMonth;
        $account->appHoursPerMonth = $this->appHoursPerMonth;
        $account->pressRelease = $this->pressRelease ?: false;
        $account->pressReleaseDate = $this->pressReleaseDate;
        $account->managementChangeMailers = $this->managementChangeMailers ?: false;
        $account->recruitingMailers = $this->recruitingMailers ?: false;
        $account->emailBlast = $this->emailBlast ?: false;
        $account->purlCampaign = $this->purlCampaign ?: false;
        $account->marketingSlick = $this->marketingSlick ?: false;
        $account->collaborationRecruitingTeam = $this->collaborationRecruitingTeam ?: false;
        $account->collaborationRecruitingTeamNames = $this->collaborationRecruitingTeamNames;
        $account->compensationGrid = $this->compensationGrid ?: false;
        $account->compensationGridBonuses = $this->compensationGridBonuses;
        $account->recruitingIncentives = $this->recruitingIncentives ?: false;
        $account->recruitingIncentivesDescription = $this->recruitingIncentivesDescription;
        $account->locumCompaniesNotified = $this->locumCompaniesNotified ?: false;
        $account->searchFirmsNotified = $this->searchFirmsNotified ?: false;
        $account->departmentsCoordinated = $this->departmentsCoordinated ?: false;
        $account->save();

        if ($this->recruiterId) {
            $this->associateRecruiter($account);
        }

        if ($this->managerId) {
            $this->associateManager($account);
        }

        $account->practices()->sync($this->practiceId ? [$this->practiceId] : []);
        
        if ($pastSiteCode != $this->siteCode) {
            $this->createSiteCodeHistory($account);
        }
    }

    /**
     * Create a SiteCode record.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function createSiteCodeHistory($account)
    {
        $siteCode = new SiteCode;
        $siteCode->accountId = $account->id;
        $siteCode->siteCode = $this->siteCode;
        $siteCode->save();
    }

    /**
     * Associates a Recruiter to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateRecruiter($account)
    {
        $recruiterPosition = PositionType::where('name', 'Recruiter')->first();
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => $recruiterPosition->id,
        ], [
            'employeeId' => $this->recruiterId,
        ]);
        AccountEmployee::reguard();
    }

    /**
     * Associates a Manager to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateManager($account)
    {
        $managerPosition = PositionType::where('name', 'Manager')->first();
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => $managerPosition->id,
        ], [
            'employeeId' => $this->managerId,
        ]);
        AccountEmployee::reguard();
    }
}
