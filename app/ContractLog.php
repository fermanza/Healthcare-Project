<?php

namespace App;

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
        return $this->belongsTo(Account::class, 'accountId');
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
}
