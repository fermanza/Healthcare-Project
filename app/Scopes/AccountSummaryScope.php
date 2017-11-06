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
        } else if ($user->hasRoleId(config('instances.roles.other_view'))) {
            $builder = $this->validate($builder, $user, 'account.other', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.other_edit'))) {
            $builder = $this->validate($builder, $user, 'account.other', 'employeeId');
        }  else if ($user->hasRoleId(config('instances.roles.credentialer'))) {
            $builder = $this->validate($builder, $user, 'account.credentialer', 'employeeId');
        } else if ($user->hasRoleId(config('instances.roles.vp_of_operations'))) {
            $builder = $this->validate($builder, $user, 'account.vp', 'employeeId');
        }
    }

    private function validate($builder, $user, $role, $employeeType) {
        // if (!$user->RSCs->isEmpty() && !$user->operatingUnits->isEmpty()) {
        //     $RSCs = $user->RSCs->map(function($RSC) {
        //         return $RSC->id;
        //     });

        //     $operatingUnits = $user->operatingUnits->map(function($operatingUnit) {
        //         return $operatingUnit->id;
        //     });

        //     $builder->whereHas('account', function($query) use ($RSCs, $operatingUnits) {
        //         $query->whereIn('RSCId', $RSCs)
        //             ->whereIn('operatingUnitId', $operatingUnits)
        //             ->whereNotNull('RSCId')
        //             ->whereNotNull('operatingUnitId');
        //     });
        // } else if (!$user->RSCs->isEmpty() && $user->operatingUnits->isEmpty()) {
        //     $RSCs = $user->RSCs->map(function($RSC) {
        //         return $RSC->id;
        //     });

        //     $builder->whereHas('account', function($query) use ($RSCs) {
        //         $query->whereIn('RSCId', $RSCs)
        //             ->whereNotNull('RSCId');
        //     });
        // } else if ($user->RSCs->isEmpty() && !$user->operatingUnits->isEmpty()) {
        //     $operatingUnits = $user->operatingUnits->map(function($operatingUnit) {
        //         return $operatingUnit->id;
        //     });

        //     $builder->whereHas('account', function($query) use ($operatingUnits) {
        //         $query->whereIn('operatingUnitId', $operatingUnits)
        //             ->whereNotNull('operatingUnitId');
        //     });
        // } 

        if ($user->RSCId && $user->operatingUnitId) {
            $builder->whereHas('account', function($query) use ($user) {
                $query->where('account.RSCId', $user->RSCId)
                ->where('account.operatingUnitId', $user->operatingUnitId)
                ->whereNotNull('RSCId')
                ->whereNotNull('operatingUnitId');
            });
        } else if ($user->RSCId && !$user->operatingUnitId) {
            $builder->whereHas('account', function($query) use ($user) {
                $query->where('RSCId', $user->RSCId)
                ->whereNotNull('RSCId');
            });
        } else if (!$user->RSCId && $user->operatingUnitId) {
            $builder->whereHas('account', function($query) use ($user) {
                $query->where('operatingUnitId', $user->operatingUnitId)
                ->whereNotNull('operatingUnitId');
            });
        } else {
            if($role == 'account.recruiter') {
                $builder->where(function ($query) use ($role, $user, $employeeType) {
                    $query->whereHas($role, function ($query) use ($user, $employeeType) {
                        $query->where($employeeType, $user->employeeId)
                            ->whereNotNull($employeeType);
                    })->orWhereHas('account.recruiters', function($query) use ($user, $employeeType) {
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