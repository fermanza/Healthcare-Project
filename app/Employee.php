<?php

namespace App;

class Employee extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tEmployee';

    /**
     * Get the Person for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'personId');
    }

    /**
     * Get the EmploymentStatus for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(EmployementStatus::class, 'employementStatusId');
    }

    /**
     * Get the AccountEmployees for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountEmployees()
    {
        return $this->hasMany(AccountEmployee::class, 'employeeId');
    }

    /**
     * Get the PositionType for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function positionType()
    {
        return $this->belongsTo(PositionType::class, 'positionTypeId');
    }

    /**
     * Get the Manager (Employee) for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'managerId');
    }

    /**
     * Get Employee's full name.
     *
     * @return string
     */
    public function fullName()
    {
        return $this->person->fullName();
    }

    /**
     * Determine if the Employee belongs to an Account with the given position.
     *
     * @param  int  $positionId
     * @return boolean
     */
    public function hasPosition($positionId)
    {
        return $this->positionTypeId == $positionId;
    }
}
