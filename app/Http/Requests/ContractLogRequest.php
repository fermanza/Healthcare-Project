<?php

namespace App\Http\Requests;

use App\Account;
use App\ContractStatus;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContractLogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $commonRules = [
            'accountId' => 'required|exists:tAccount,id',
            'recruiterId' => 'required|exists:tEmployee,id',
            'managerId' => 'required|exists:tEmployee,id',
            'statusId' => 'required|exists:tContractStatus,id',
            'accounts' => 'nullable|array|exists:tAccount,id',
            'recruiters' => 'nullable|array|exists:tEmployee,id',
            'value' => 'required|in:0,0.5,1,1.5',
            'providerFirstName' => 'required',
            'providerMiddleInitial' => 'nullable',
            'providerLastName' => 'required',
            'specialtyId' => 'required|exists:tSpecialty,id',
            'contractOutDate' => 'date_format:"m/d/Y"',
            'contractInDate' => 'nullable|date_format:"m/d/Y"',
            'sentToQADate' => 'nullable|date_format:"m/d/Y"',
            'counterSigDate' => 'nullable|date_format:"m/d/Y"',
            'sentToPayrollDate' => 'nullable|date_format:"m/d/Y"',
            'projectedStartDate' => 'date_format:"m/d/Y"',
            'actualStartDate' => 'nullable|date_format:"m/d/Y"',
            'numOfHours' => 'required_without:numOfShifts|nullable|numeric|min:0',
            'numOfShifts' => 'required_without:numOfHours|nullable|numeric|min:0',
            'contractTypeId' => 'required|exists:tContractType,id',
            'contractNoteId' => 'nullable|exists:tContractNote,id',
            'comments' => '',
            'contractCoordinatorId' => 'required|exists:tEmployee,id',
            'logOwnerId' => 'required|exists:tEmployee,id',
            'positionId' => 'required|exists:tPosition,id',
        ];

        if ($this->isCreate()) {
            $methodRules = [
                // 'personId' => 'required|exists:tPerson,id',
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
    public function save(Model $contractLog)
    {
        $account = Account::withoutGlobalScope('role')->with('practices')->find($this->accountId);

        $contractLog->accountId = $this->accountId;
        $contractLog->recruiterId = $this->recruiterId;
        $contractLog->managerId = $this->managerId;
        $contractLog->statusId = $this->statusId;
        $contractLog->providerDesignationId = $this->providerDesignationId;
        $contractLog->practiceId = $account->practices->count() ? $account->practices->first()->id : null;
        $contractLog->providerFirstName = $this->providerFirstName;
        $contractLog->providerMiddleInitial = $this->providerMiddleInitial;
        $contractLog->providerLastName = $this->providerLastName;
        $contractLog->specialtyId = $this->specialtyId;
        $contractLog->divisionId = $account->divisionId;
        $contractLog->contractOutDate = $this->contractOutDate ? $this->contractOutDate : null;;
        $contractLog->contractInDate = $this->contractInDate ? $this->contractInDate : null;;
        $contractLog->sentToQADate = $this->sentToQADate ? $this->sentToQADate : null;;
        $contractLog->counterSigDate = $this->counterSigDate ? $this->counterSigDate : null;;
        $contractLog->sentToPayrollDate = $this->sentToPayrollDate ? $this->sentToPayrollDate : null;;
        $contractLog->projectedStartDate = $this->projectedStartDate ? $this->projectedStartDate : null;;
        $contractLog->actualStartDate = $this->actualStartDate ? $this->actualStartDate : null;;
        $contractLog->numOfHours = $this->numOfHours;
        $contractLog->numOfShifts = $this->numOfShifts;
        $contractLog->contractTypeId = $this->contractTypeId;
        $contractLog->contractNoteId = $this->contractNoteId;
        $contractLog->comments = $this->comments;
        $contractLog->contractCoordinatorId = $this->contractCoordinatorId;
        $contractLog->logOwnerId = $this->logOwnerId;
        $contractLog->positionId = $this->positionId;
        $contractLog->value = $this->value;
        $contractLog->inactive = $this->inactive ? $this->inactive : 0;
        $contractLog->declined = $this->declined ? $this->declined : 0;
        $contractLog->neverReturned = $this->neverReturned ? $this->neverReturned : 0;
        $contractLog->lastUpdated = Carbon::now();
        $contractLog->lastUpdatedBy = \Auth::id();
        $contractLog->save();

        $contractLog->accounts()->sync(array_merge(
            [$this->accountId],
            $this->accounts ?: []
        ));

        $contractLog->recruiters()->sync(array_merge(
            [$this->recruiterId],
            $this->recruiters ?: []
        ));
    }
}
