<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AccountSummaryScope implements Scope
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
            
        if (! $user || session('ignore-summary-role-scope')) {
            return;
        }

        if ($user->hasRoleId(config('instances.roles.manager'))) {
            $builder->whereHas('account.manager', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder->whereHas('account.recruiter', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder->whereHas('account.coordinator', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder->whereHas('account.rsc', function ($query) use ($user) {
                $query = $this->validate($query, $user, 'directorId');
            });
        }  else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder->whereHas('account.dca', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.svp'))) {
            $builder->whereHas('account.svp', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.rmd'))) {
            $builder->whereHas('account.rmd', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.other'))) {
            $builder->whereHas('account.other', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.credentialer'))) {
            $builder->whereHas('account.credentialer', function($query) use ($user) {
                $query = $this->validate($query, $user, 'employeeId');
            });
        }
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