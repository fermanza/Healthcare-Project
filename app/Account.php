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
        'start_date',
        'press_release_date',
    ];

    /**
     * Get the Recruiter (Employee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recruiter()
    {
        return $this->belongsTo(Employee::class, 'recruiter_id');
    }

    /**
     * Get the Manager (Employee) for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the Practice for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    /**
     * Get the Division for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the SiteCodes for the Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteCodes()
    {
        return $this->hasMany(SiteCode::class)->latest();
    }

    /**
     * Determines if start date is less than 6 months ago.
     *
     * @return boolean
     */
    public function isRecentlyCreated()
    {
        if (! $this->start_date) {
            return false;
        }
        // Six months ago
        $pastDate = Carbon::now()->subMonths(6);

        return $this->start_date->gte($pastDate);
    }
}
