<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /**
     * Get the Employees for the Person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get Person's full name.
     *
     * @return string
     */
    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
