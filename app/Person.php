<?php

namespace App;

class Person extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tPerson';

    /**
     * Get the Employees for the Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'employeeId');
    }

    /**
     * Get Person's full name.
     *
     * @return string
     */
    public function fullName()
    {
        return $this->firstName.' '.$this->lastName;
    }
}
