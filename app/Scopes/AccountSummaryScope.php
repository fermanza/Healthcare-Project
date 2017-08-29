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
                $query->where('employeeId', $user->employeeId)
                    ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder->whereHas('account.recruiter', function ($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                    ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder->whereHas('account.coordinator', function ($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                    ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder->whereHas('account.rsc', function ($query) use ($user) {
                $query->where('directorId', $user->employeeId)
                    ->whereNotNull('directorId');
            });
        }  else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder->whereHas('account.dca', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.svp'))) {
            $builder->whereHas('account.svp', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.rmd'))) {
            $builder->whereHas('account.rmd', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.other'))) {
            $builder->whereHas('account.other', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        }
    }
}