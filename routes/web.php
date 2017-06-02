<?php

// Splash Page
$router->get('/', 'HomeController@index');

// Authentication Routes...
$router->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$router->post('login', 'Auth\LoginController@login');
$router->get('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
// $router->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// $router->post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
$router->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$router->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$router->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$router->post('password/reset', 'Auth\ResetPasswordController@reset');

$router->group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function ($router) {

    $router->get('settings/credentials', 'SettingsController@credentials')->name('settings.credentials');
    $router->patch('settings/credentials', 'SettingsController@updateCredentials');

    $router->get('sidebar-collapse', 'SidebarController@collapse');
    $router->get('sidebar-expand', 'SidebarController@expand');

    $router->post('accounts/image', 'AccountsController@image');
    $router->patch('accounts/merge', 'AccountsController@merge')->name('accounts.merge');
    $router->patch('accounts/parent', 'AccountsController@parent')->name('accounts.parent');
    $router->get('accounts/{account}/internal-plan', 'AccountsController@internalPlan')->name('accounts.internalPlan');
    $router->patch('accounts/{account}/remove-parent', 'AccountsController@removeParent')->name('accounts.removeParent');
    $router->resource('accounts', 'AccountsController');

    $router->resource('files', 'FilesController');
    $router->resource('regions', 'RegionsController');
    $router->resource('groups', 'GroupsController');
    $router->resource('practices', 'PracticesController');
    $router->resource('divisions', 'DivisionsController');
    $router->resource('people', 'PeopleController');
    $router->resource('positionTypes', 'PositionTypesController');
    $router->resource('employees', 'EmployeesController');
    $router->resource('contractLogs', 'ContractLogsController');

});

