<?php

namespace App;

use Carbon\Carbon;

class PipelineRosterBench extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccountPipelineRosterBench';

    protected $dates = [
        'firstShift',
    ];

    public function getFirstShiftAttribute($value){
    	if($value) {
    		$formattedDate = Carbon::parse($value)->format('Y-m-d');

    		return $formattedDate;
    	}

    	return $value;
    }
}
