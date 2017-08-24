<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContractLogScope implements Scope
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
            
        if (! $user || session('ignore-contract-log-role-scope')) {
            return;
        }

        if ($user->hasRoleId(config('instances.roles.manager'))) {
            $builder->whereHas('accounts.manager', function ($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                    ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder->whereHas('accounts.recruiter', function ($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                    ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder->whereHas('accounts.coordinator', function ($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                    ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder->whereHas('accounts.rsc', function ($query) use ($user) {
                $query->where('directorId', $user->employeeId)
                    ->whereNotNull('directorId');
            });
        }  else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder->whereHas('accounts.dca', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.svp'))) {
            $builder->whereHas('accounts.svp', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.rmd'))) {
            $builder->whereHas('accounts.rmd', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        } else if ($user->hasRoleId(config('instances.roles.other'))) {
            $builder->whereHas('accounts.other', function($query) use ($user) {
                $query->where('employeeId', $user->employeeId)
                ->whereNotNull('employeeId');
            });
        }

        // if ($user->hasRoleId(config('instances.roles.director'))) {
        //     $builder->whereHas('accounts.manager.employee', function ($query) use ($user) {
        //         $query->where('managerId', $user->employeeId)
        //             ->whereNotNull('managerId');
        //     });
        // }
    }
}