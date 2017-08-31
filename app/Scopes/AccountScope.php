<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AccountScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model) {
        $user = auth()->user();
        
        if (! $user || session('ignore-account-role-scope')) {
            return;
        }

        if ($user->hasRoleId(config('instances.roles.manager'))) {
            $builder->whereHas('manager', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder->whereHas('recruiter', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder->whereHas('coordinator', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder->whereHas('rsc', function($query) use ($user) {
                $query = $this->validate($query, $user, 'directorId');
            });
        } else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder->whereHas('dca', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.svp'))) {
            $builder->whereHas('svp', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.rmd'))) {
            $builder->whereHas('rmd', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.other'))) {
            $builder->whereHas('other', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.credentialer'))) {
            $builder->whereHas('credentialer', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        }

        // if ($user->hasRoleId(config('instances.roles.director'))) {
        //     $builder->whereHas('manager.employee', function ($query) use ($user) {
        //         $query->where('managerId', $user->employeeId)
        //             ->whereNotNull('managerId');
        //     });
        // }
    }

    private function validate($query, $user, $role) {
        if ($user->RSCId && $user->operatingUnitId) {
            $query->where($role, $user->employeeId)
                ->where('RSCId', $user->RSCId)
                ->where('operatingUnitId', $user->operatingUnitId)
                ->whereNotNull($role)
                ->whereNotNull('RSCId')
                ->whereNotNull('operatingUnitId');
        } else if ($user->RSCId && !$user->operatingUnitId) {
            $query->where($role, $user->employeeId)
                ->where('RSCId', $user->RSCId)
                ->whereNotNull($role)
                ->whereNotNull('RSCId');
        } else if (!$user->RSCId && $user->operatingUnitId) {
            $query->where($role, $user->employeeId)
                ->where('operatingUnitId', $user->operatingUnitId)
                ->whereNotNull($role)
                ->whereNotNull('operatingUnitId');
        } else {
            $query->where($role, $user->employeeId)
                ->whereNotNull($role);
        }

        return $query;
    }
}