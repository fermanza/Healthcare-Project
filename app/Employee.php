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
     * Get the Recruiting Accounts for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recruitingAccounts()
    {
        return $this->hasMany(Account::class, 'recruiter_id');
    }

    /**
     * Get the Managing Accounts for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managingAccounts()
    {
        return $this->hasMany(Account::class, 'manager_id');
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
}
