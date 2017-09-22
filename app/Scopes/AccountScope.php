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
            $builder = $this->validate($builder, $user, 'manager', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.recruiter'))) {
            $builder = $this->validate($builder, $user, 'recruiter', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.contract_coordinator'))) {
            $builder = $this->validate($builder, $user, 'coordinator', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.director'))) {
            $builder = $this->validate($builder, $user, 'rsc', 'directorId');
        } else if ($user->hasRoleId(config('instances.roles.dca'))) {
            $builder = $this->validate($builder, $user, 'dca', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.other_view'))) {
            $builder = $this->validate($builder, $user, 'other', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.other_edit'))) {
            $builder = $this->validate($builder, $user, 'other', 'employeeId');
        }  else if ($user->hasRoleId(config('instances.roles.credentialer'))) {
            $builder = $this->validate($builder, $user, 'credentialer', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.vp_of_operations'))) {
            $builder = $this->validate($builder, $user, 'vp', 'employeeId');
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
            if($role == 'recruiter') {
                $builder->where(function ($query) use ($role, $user, $employeeType) {
                    $query->whereHas($role, function ($query) use ($user, $employeeType) {
                        $query->where($employeeType, $user->employeeId)
                            ->whereNotNull($employeeType);
                    })->orWhereHas('recruiters', function($query) use ($user, $employeeType) {
                        $query->where($employeeType, $user->employeeId)
                            ->whereNotNull($employeeType);
                    });
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