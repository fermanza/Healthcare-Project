<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractNote extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tContractNote';


    /**
     * Get formatted contract note.
     *
     * @param  string  $value
     * @return string
     */
    public function getContractNoteAttribute($value)
    {
        $endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');

        return str_replace($endash, '-', $value);
    }
}
