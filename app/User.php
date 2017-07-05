<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use LaratrustUserTrait { hasPermission as laratrustHasPermission; }
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tUser';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the Employee for the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  string|bool  $team      Team name or requiredAll roles.
     * @param  bool  $requireAll All roles in the array are required.
     * @return bool
     */
    public function hasPermission($permission, $team = null, $requireAll = false)
    {
        return $this->isSuperUser() 
            ? true
            : $this->laratrustHasPermission($permission, $team, $requireAll);
    }

    /**
     * Checks if the user has any of the roles by their id.
     *
     * @param  int|array  $roleIds
     * @return bool
     */
    public function hasRoleId($roleIds)
    {
        $roleIds = is_array($roleIds) ? $roleIds : func_get_args();

        return $this->cachedRoles()->contains(function ($role) use ($roleIds) {
            return in_array($role->id, $roleIds);
        });
    }

    /**
     * Check if user is Super User.
     *
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->hasRoleId(config('instances.roles.super_admin'));
    }
}
