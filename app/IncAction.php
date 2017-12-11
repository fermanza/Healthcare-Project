<?php

namespace App;

class IncAction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vIncAction';

    public function account()
    {
        return $this->belongsTo(Account::class, 'siteCode', 'siteCode');
    }
}
