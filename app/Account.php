<?php

namespace App;

use Carbon\Carbon;

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
                    ->whereHas('positionType', function ($query) {
                        $query->where('name', 'Recruiter');
                    });
    }

    /**
     * Get the Manager (AccountEmployee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->hasOne(AccountEmployee::class, 'accountId')
                    ->whereHas('positionType', function ($query) {
                        $query->where('name', 'Manager');
                    });
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
     * Get the SiteCodes for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteCodes()
    {
        return $this->hasMany(SiteCode::class, 'accountId')->latest();
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
}
