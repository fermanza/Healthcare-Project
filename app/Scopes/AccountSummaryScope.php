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
            $builder = $this->validate($builder, $user, 'account.manager', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder = $this->validate($builder, $user, 'account.recruiter', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder = $this->validate($builder, $user, 'account.coordinator', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder = $this->validate($builder, $user, 'account.rsc', 'directorId');
        } else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder = $this->validate($builder, $user, 'account.dca', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.svp'))) {
            $builder = $this->validate($builder, $user, 'account.svp', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.rmd'))) {
            $builder = $this->validate($builder, $user, 'account.rmd', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.other'))) {
            $builder = $this->validate($builder, $user, 'account.other', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.credentialer'))) {
            $builder = $this->validate($builder, $user, 'account.credentialer', 'employeeId');
        }
    }

    private function validate($builder, $user, $role, $employeeType) {
        if ($user->RSCId && $user->operatingUnitId) {
            $builder->where('RSCId', $user->RSCId)
                ->where('operatingUnitId', $user->operatingUnitId)
                ->whereNotNull('RSCId')
                ->whereNotNull('operatingUnitId');
        } else if ($user->RSCId && !$user->operatingUnitId) {
            $builder->where('RSCId', $user->RSCId)
                ->whereNotNull('RSCId');
        } else if (!$user->RSCId && $user->operatingUnitId) {
            $builder->where('operatingUnitId', $user->operatingUnitId)
                ->whereNotNull('operatingUnitId');
        } else {
            if($role == 'account.recruiter') {
                $builder->whereHas($role, function ($query) use ($user, $employeeType) {
                    $query->where($employeeType, $user->employeeId)
                        ->whereNotNull($employeeType);
                })->orWhereHas('account.recruiters', function($query) use ($user, $employeeType) {
                    $query->where($employeeType, $user->employeeId)
                        ->whereNotNull($employeeType);
                });
            } else {
                $builder->whereHas($role, function ($query) use ($user, $employeeType) {
                    $query->where($employeeType, $user->employeeId)
                        ->whereNotNull($employeeType);
                });
            }
        }

        return $builder;
    }
}