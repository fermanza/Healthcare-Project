<?php

namespace App;

class Provider extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tProvider';

    /**
     * Get the fullName.
     *
     * @param  string  $value
     * @return string
     */
    public function getFullNameAttribute($value)
    {
        return utf8_decode($value);
    }

    /**
     * Get the firstName.
     *
     * @param  string  $value
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return utf8_decode($value);
    }

    /**
     * Get the lastName.
     *
     * @param  string  $value
     * @return string
     */
    public function getLastNameAttribute($value)
    {
        return utf8_decode($value);
    }
}
