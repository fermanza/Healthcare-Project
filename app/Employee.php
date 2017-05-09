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
     * @param  string  $position
     * @return boolean
     */
    public function hasPosition($position)
    {
        return $this->accountEmployees->contains->hasPosition($position);
    }
}
