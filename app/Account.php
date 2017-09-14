<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\AccountScope;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tAccount';

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'startDate',
        'endDate',
        'pressReleaseDate',
    ];

    /**
     * Get the Recruiter (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recruiter()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.recruiter'))
            ->where('isPrimary', true);
    }

    /**
     * Get the Secondary Recruiters (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recruiters()
    {
        return $this->hasMany(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.recruiter'))
            ->where('isPrimary', false);
    }

    /**
     * Get the Manager (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function manager()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.manager'));
    }

    /**
     * Get the Scheduler (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function scheduler()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.scheduler'));
    }

    /**
     * Get the Enrollment (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function enrollment()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.enrollment'));
    }

    /**
     * Get the Payroll (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payroll()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.payroll'));
    }

    /**
     * Get the Credentialer (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function credentialer()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.credentialer'));
    }

    /**
     * Get the DCA role ((AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dca()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.dca'));
    }

    /**
     * Get the DCS role ((AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dcs()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.dcs'));
    }

    /**
     * Get the SVP role (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function svp()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.svp'));
    }

    /**
     * Get the RMD role ((AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rmd()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.rmd'));
    }

    /**
     * Get the VP role ((AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vp()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.vp_of_operations'));
    }

    /**
     * Get the Other role (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function other()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.other'));
    }

    /**
     * Get the Contract Coordinator (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coordinator()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.contract_coordinator'));
    }

    /**
     * Get the Practices for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function practices()
    {
        return $this->belongsToMany(Practice::class, 'tAccountToPractice', 'accountId', 'practiceId');
    }

    /**
     * Get the Division for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'divisionId');
    }

    /**
     * Get the Region for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'operatingUnitId');
    }

    /**
     * Get the RSC for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rsc()
    {
        return $this->belongsTo(RSC::class, 'RSCId');
    }

    /**
     * Get the SiteCodes for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteCodes()
    {
        return $this->hasMany(SiteCode::class, 'accountId')->latest();
    }

    /**
     * Get the PhysiciansApps for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function physiciansApps()
    {
        return $this->hasMany(PhysiciansApps::class, 'accountId')->latest();
    }

    /**
     * Get the Pipeline for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pipeline()
    {
        return $this->hasOne(Pipeline::class, 'accountId');
    }

    /**
     * Get the Summary for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function summary()
    {
        return $this->hasOne(AccountSummary::class, 'accountId');
    }

    /**
     * Determines if start date is less than 7 months ago.
     *
     * @return boolean
     */
    public function isRecentlyCreated()
    {
        $monthsToGetOld = 7;
        $monthsSinceCreated = $this->getMonthsSinceCreated();

        return $monthsSinceCreated < $monthsToGetOld;
    }

    /**
     * Returns the number of months since created.
     *
     * @return float
     */
    public function getMonthsSinceCreated()
    {
        if (! $this->startDate) {
            return INF;
        }
        
        $monthDays = 30;
        $days = Carbon::now()->diffInDays($this->startDate);
        $months = $days / $monthDays;

        return number_format($months, 1);
    }

    /**
     * Determines if end date has been met.
     *
     * @return boolean
     */
    public function hasEnded()
    {
        if (! $this->endDate) {
            return false;
        }

        return Carbon::now()->gte($this->endDate);
    }

    /**
     * Scope a query to check if account has ended or not.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTermed(Builder $query, $termed)
    {
        return $termed == true 
            ? $query->whereNotNull('endDate')->whereDate('endDate', '<=', Carbon::today()->format('Y-m-d')) 
            : $query->where(function($query){ 
                $query->whereNull('endDate')->orWhere('endDate', '>', Carbon::today()->format('Y-m-d')); 
            });
    }
}
