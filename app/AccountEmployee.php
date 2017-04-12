<?php

namespace App;

class AccountEmployee extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccountToEmployee';

    /**
     * Get the Account for the AccountEmployee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'accountId');
    }

    /**
     * Get the Employee for the AccountEmployee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    /**
     * Get the PositionType for the AccountEmployee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function positionType()
    {
        return $this->belongsTo(PositionType::class, 'positionTypeId');
    }
}