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
    $router->get('sidebar-collapse', 'SidebarController@collapse');
    $router->get('sidebar-expand', 'SidebarController@expand');

    $router->get('settings/credentials', 'SettingsController@credentials')->name('settings.credentials.edit');
    $router->patch('settings/credentials', 'SettingsController@updateCredentials')->name('settings.credentials.update');
});

$router->group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'acl']], function ($router) {

    $router->post('accounts/image', 'AccountsController@image')->name('accounts.image');
    $router->patch('accounts/merge', 'AccountsController@merge')->name('accounts.merge');
    $router->patch('accounts/parent', 'AccountsController@parent')->name('accounts.parent');
    $router->patch('accounts/{account}/remove-parent', 'AccountsController@removeParent')->name('accounts.removeParent');
    $router->get('accounts/{account}/internal-plan', 'AccountsController@internalPlan')->name('accounts.internalPlan');
    $router->resource('accounts', 'AccountsController');

    $router->resource('users', 'UsersController');
    $router->resource('roles', 'RolesController');
    $router->resource('permissions', 'PermissionsController');
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
