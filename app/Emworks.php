<?php

namespace App;

class Emworks extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tProviderAccountEmworks';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'interview',
        'contractIn',
        'contractOut',
        'firstShift',
        'resigned',
        'lastShift',
        'fileToCredentialing',
        'privilegeGoal',
        'appToHospital',
        'ProvisionalPrivilegeStart'
    ];
}
