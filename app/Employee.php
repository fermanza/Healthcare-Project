<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * Get the Person for the Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
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
