<?php

Artisan::command('inspire', function () {
    $this->comment(Illuminate\Foundation\Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('set-new-routes', function () {
    $aclNames = collect(config('acl'))->collapse();
    App\Permission::unguard();

    collect(Route::getRoutes())
        ->filter(function ($route) {
            return in_array('acl', $route->middleware());
        })->each(function ($route) use ($aclNames) {
            App\Permission::firstOrCreate([
                'name' => $route->getName(),
            ], [
                'display_name' => array_get($aclNames, $route->getName()),
            ]);
        });

    App\Permission::reguard();
})->describe('Register any new route to the Permissions table');

Artisan::command('sync-current-contractlogs-to-accounts', function () {
    App\ContractLog::all()->each(function ($contractLog) {
        $contractLog->accounts()->syncWithoutDetaching([$contractLog->account->id]);
    });
})->describe('Sync the existing Contract Logs with their initial Account to pivot');
