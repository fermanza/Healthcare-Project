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
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder->whereHas('rsc', function($query) use ($user) {
                $query->where('directorId', $user->employeeId)
                ->whereNotNull('directorId');
            });
        }

        // if ($user->hasRoleId(config('instances.roles.director'))) {
        //     $builder->whereHas('manager.employee', function ($query) use ($user) {
        //         $query->where('managerId', $user->employeeId)
        //             ->whereNotNull('managerId');
        //     });
        // }
    }
}