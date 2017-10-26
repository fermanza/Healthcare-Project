<?php

namespace App\Http\Requests;

use App\Account;
use App\Pipeline;
use App\SiteCode;
use App\PositionType;
use App\PhysiciansApps;
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
        $commonRules = [
            'name' => 'required',
            'photoPath' => '',
            'recruiterId' => 'exists:tEmployee,id',
            'credentialerId' => 'exists:tEmployee,id',
            'RSCId' => 'exists:tEmployee,id',
            'schedulerId' => 'exists:tEmployee,id',
            'enrollmentId' => 'exists:tEmployee,id',
            'payrollId' => 'exists:tEmployee,id',
            'recruiters' => 'nullable|array|exists:tEmployee,id',
            'managerId' => 'exists:tEmployee,id',
            'practiceId' => 'exists:tPractice,id',
            'divisionId' => 'exists:tDivision,id',
            'RSCId' => 'exists:tRSC,id',
            'systemAffiliationId' => 'exists:tSystemAffiliation,id',
            'operatingUnitId' => 'exists:tOperatingUnit,id',
            'googleAddress' => '',
            'street' => '',
            'number' => '',
            'city' => '',
            'state' => '',
            'zipCode' => '',
            'country' => '',
            'latitude' => 'between:-90,90',
            'longitude' => 'between:-180,180',
            'startDate' => 'nullable|date_format:"m/d/Y"',
            'endDate' => 'nullable|date_format:"m/d/Y"',
            'accountDescription' => '',
            'pressRelease' => 'boolean',
            'pressReleaseDate' => 'nullable|date_format:"m/d/Y"',
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
            'isIC' => 'boolean',
            'hasSMD' => 'boolean',
            'hasAMD' => 'boolean',
        ];
        
        if ($this->isCreate()) {
            $methodRules = [
                'siteCode' => 'required|unique:tAccount,siteCode|numeric',
            ];
        } else {
            $methodRules = [
                'siteCode' => 'required|unique:tAccount,siteCode,'.$this->account->id.'|numeric',
            ];
        }

        return array_merge($commonRules, $methodRules);
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

        if ($this->isEdit() && $this->physiciansOrAppsChanged()) {
            $this->createPhysiciansAppsHistory();
        }

        $account->name = $this->name;
        $account->siteCode = $this->siteCode;
        $account->photoPath = $this->photoPath;
        $account->divisionId = $this->divisionId;
        $account->RSCId = $this->RSCId;
        $account->operatingUnitId = $this->operatingUnitId;
        $account->systemAffiliationId = $this->systemAffiliationId;
        $account->isIC = $this->isIC ?: false;
        $account->hasSMD = $this->hasSMD ?: false;
        $account->hasAMD = $this->hasAMD ?: false;
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
        $account->endDate = $this->endDate ? $this->endDate: null;
        $account->accountDescription = $this->accountDescription;
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
        $account->requirements = $this->requirements;
        $account->fees = $this->fees;
        $account->applications = $this->applications;
        $account->meetings = $this->meetings;
        $account->other = $this->other;
        $account->save();

        if ($this->isCreate()) {
            $this->createPipeline($account);
        }

        $this->associateRecruiter($account);

        if ($this->credentialerId) {
            $this->associateCredentialer($account);
        }

        if ($this->DCSId) {
            $this->associateDCS($account);
        }

        if ($this->schedulerId) {
            $this->associateScheduler($account);
        }

        if ($this->enrollmentId) {
            $this->associateEnrollment($account);
        }

        if ($this->payrollId) {
            $this->associatePayroll($account);
        }

        $this->associateRecruiters($account);
       
        $this->associateManager($account);

        $account->practices()->sync($this->practiceId ? [$this->practiceId] : []);
        
        if ($pastSiteCode != $this->siteCode) {
            $this->createSiteCodeHistory($account);
        }
        
        if ($this->isEdit() && $pastSiteCode != $this->siteCode) {
            $this->updateRelatedSiteCodes($pastSiteCode);
        }
    }

    /**
     * Creates a Pipeline for the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function createPipeline($account)
    {
        $pipeline = new Pipeline;
        $pipeline->accountId = $account->id;
        $pipeline->save();
    }

    /**
     * Associates a Recruiter to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateRecruiter($account)
    {
        $employee = AccountEmployee::where('accountId', $account->id)->where('positionTypeId', config('instances.position_types.recruiter'))->where('isPrimary', true);
        $employee->delete();
        
        if ($this->recruiterId) {
            AccountEmployee::unguard();
            AccountEmployee::updateOrCreate([
                'accountId' => $account->id,
                'positionTypeId' => config('instances.position_types.recruiter'),
                'isPrimary' => true,
            ], [
                'employeeId' => $this->recruiterId,
            ]);
            AccountEmployee::reguard();
        }
    }

    /**
     * Associates a Credentialer to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateCredentialer($account)
    {
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => config('instances.position_types.credentialer'),
            'isPrimary' => true,
        ], [
            'employeeId' => $this->credentialerId,
        ]);
        AccountEmployee::reguard();
    }

    /**
     * Associates a DCS to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateDCS($account)
    {
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => config('instances.position_types.dcs'),
            'isPrimary' => true,
        ], [
            'employeeId' => $this->DCSId,
        ]);
        AccountEmployee::reguard();
    }

    /**
     * Associates a Scheduler to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateScheduler($account)
    {
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => config('instances.position_types.scheduler'),
            'isPrimary' => true,
        ], [
            'employeeId' => $this->schedulerId,
        ]);
        AccountEmployee::reguard();
    }

    /**
     * Associates an Enrollment to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateEnrollment($account)
    {
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => config('instances.position_types.enrollment'),
            'isPrimary' => true,
        ], [
            'employeeId' => $this->enrollmentId,
        ]);
        AccountEmployee::reguard();
    }

    /**
     * Associates a Payroll to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associatePayroll($account)
    {
        AccountEmployee::unguard();
        AccountEmployee::updateOrCreate([
            'accountId' => $account->id,
            'positionTypeId' => config('instances.position_types.payroll'),
            'isPrimary' => true,
        ], [
            'employeeId' => $this->payrollId,
        ]);
        AccountEmployee::reguard();
    }

    /**
     * Associates secondary Recruiters to the Account.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $account
     * @return null
     */
    protected function associateRecruiters($account)
    {
        AccountEmployee::unguard();
        AccountEmployee::where([
            'accountId' => $account->id,
            'positionTypeId' => config('instances.position_types.recruiter'),
            'isPrimary' => false,
        ])->delete();

        foreach ($this->input('recruiters', []) as $recruiter) {
            AccountEmployee::create([
                'employeeId' => $recruiter,
                'accountId' => $account->id,
                'positionTypeId' => config('instances.position_types.recruiter'),
                'isPrimary' => false,
            ]);
        }
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
        $employee = AccountEmployee::where('accountId', $account->id)->where('positionTypeId', config('instances.position_types.manager'));
        $employee->delete();

        if ($this->managerId) {
            AccountEmployee::unguard();
            AccountEmployee::updateOrCreate([
                'accountId' => $account->id,
                'positionTypeId' => config('instances.position_types.manager'),
            ], [
                'employeeId' => $this->managerId,
            ]);
            AccountEmployee::reguard();
        }
    }

    /**
     * Determine if Physicians or Apps inputs Changed.
     *
     * @return null
     */
    protected function physiciansOrAppsChanged()
    {
        return $this->account->physiciansNeeded != $this->physiciansNeeded ||
                $this->account->appsNeeded != $this->appsNeeded ||
                $this->account->physicianHoursPerMonth != $this->physicianHoursPerMonth ||
                $this->account->appHoursPerMonth != $this->appHoursPerMonth;
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
     * Create a PhysiciansApps record.
     *
     * @return null
     */
    protected function createPhysiciansAppsHistory()
    {
        $physiciansApps = new PhysiciansApps;
        $physiciansApps->accountId = $this->account->id;
        $physiciansApps->physiciansNeeded = $this->account->physiciansNeeded != $this->physiciansNeeded ? $this->physiciansNeeded : null;
        $physiciansApps->appsNeeded = $this->account->appsNeeded != $this->appsNeeded ? $this->appsNeeded : null;
        $physiciansApps->physicianHoursPerMonth = $this->account->physicianHoursPerMonth != $this->physicianHoursPerMonth ? $this->physicianHoursPerMonth : null;
        $physiciansApps->appHoursPerMonth = $this->account->appHoursPerMonth != $this->appHoursPerMonth ? $this->appHoursPerMonth : null;
        $physiciansApps->save();
    }

    /**
     * Updates the related Merged SiteCodes with the new one.
     *
     * @param  string  $pastSiteCode
     * @return null
     */
    public function updateRelatedSiteCodes($pastSiteCode)
    {
        Account::where('mergedSiteCode', $pastSiteCode)->update([
            'mergedSiteCode' => $this->siteCode,
        ]);

        Account::where('parentSiteCode', $pastSiteCode)->update([
            'parentSiteCode' => $this->siteCode,
        ]);
    }
}

