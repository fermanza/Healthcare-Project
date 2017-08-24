<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;

class ContractLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tContractLogs';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'contractOutDate',
        'contractInDate',
        'sentToQADate',
        'counterSigDate',
        'sentToPayrollDate',
        'projectedStartDate',
        'actualStartDate',
    ];

    /**
     * Get the Account for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'accountId')
            ->withoutGlobalScope('role');
    }

    /**
     * Get the Accounts for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'tContractLogToAccounts', 'contractLogId', 'accountId')
            ->withoutGlobalScope('role');
    }

    /**
     * Get the ContractStatus for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(ContractStatus::class, 'statusId');
    }

    /**
     * Get the Position for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'positionId');
    }

    /**
     * Get the Practice for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function practice()
    {
        return $this->belongsTo(Practice::class, 'practiceId');
    }

    /**
     * Get the Division for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'divisionId');
    }

    /**
     * Get the ContractNote for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function note()
    {
        return $this->belongsTo(ContractNote::class, 'contractNoteId');
    }

    /**
     * Get the ProviderDesignation for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designation()
    {
        return $this->belongsTo(ProviderDesignation::class, 'providerDesignationId');
    }

    /**
     * Get the Recruiter for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recruiter()
    {
        return $this->belongsTo(Employee::class, 'recruiterId');
    }

    /**
     * Get the Recruiters for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recruiters()
    {
        return $this->belongsToMany(Employee::class, 'tContractLogToEmployee', 'contractLogId', 'employeeId');
    }

    /**
     * Get the Manager for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'managerId');
    }

    /**
     * Get the Contract Coordinator for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coordinator()
    {
        return $this->belongsTo(Employee::class, 'contractCoordinatorId');
    }

    /**
     * Get the Speciality for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialtyId');
    }

    /**
     * Get the Contract Type for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ContractType::class, 'contractTypeId');
    }

    /**
     * Get the Owner for the ContractLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Employee::class, 'logOwnerId');
    }
}
