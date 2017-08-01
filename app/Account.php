<?php

namespace App;

use Carbon\Carbon;
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
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('role', function (Builder $builder) {
            $user = auth()->user();
            
            if (! $user || session('ignore-account-role-scope')) {
                return;
            }

            if ($user->hasRoleId(config('instances.roles.manager'))) {
                $builder->whereHas('manager', function ($query) use ($user) {
                    $query->where('employeeId', $user->employeeId)
                        ->whereNotNull('employeeId');
                });
            } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
                $builder->whereHas('recruiter', function ($query) use ($user) {
                    $query->where('employeeId', $user->employeeId)
                        ->whereNotNull('employeeId');
                });
            } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
                $builder->whereHas('coordinator', function ($query) use ($user) {
                    $query->where('employeeId', $user->employeeId)
                        ->whereNotNull('employeeId');
                });
            }

            // if ($user->hasRoleId(config('instances.roles.director'))) {
            //     $builder->whereHas('manager.employee', function ($query) use ($user) {
            //         $query->where('managerId', $user->employeeId)
            //             ->whereNotNull('managerId');
            //     });
            // }
        });
    }

    /**
     * Get the Recruiter (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recruiter()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
            ->where('positionTypeId', config('instances.position_types.recruiter'));
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
     * Determines if start date is less than 6 months ago.
     *
     * @return boolean
     */
    public function isRecentlyCreated()
    {
        if (! $this->startDate) {
            return false;
        }
        // Six months ago
        $pastDate = Carbon::now()->subMonths(6);

        return $this->startDate->gte($pastDate);
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
}
