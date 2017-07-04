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
    $router->get('dashboard', 'AdminController@index')->name('dashboard');

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
    $router->resource('accounts', 'AccountsController', ['except' => 'show']);

    $router->resource('users', 'UsersController', ['except' => 'show']);
    $router->resource('roles', 'RolesController', ['except' => 'show']);
    $router->resource('permissions', 'PermissionsController', ['except' => ['create', 'store', 'show', 'destroy']]);
    $router->resource('files', 'FilesController');
    $router->resource('regions', 'RegionsController', ['except' => 'show']);
    $router->resource('groups', 'GroupsController', ['except' => 'show']);
    $router->resource('practices', 'PracticesController', ['except' => 'show']);
    $router->resource('divisions', 'DivisionsController', ['except' => 'show']);
    $router->resource('people', 'PeopleController', ['except' => 'show']);
    $router->resource('positionTypes', 'PositionTypesController', ['except' => 'show']);
    $router->resource('employees', 'EmployeesController', ['except' => 'show']);
    $router->resource('contractLogs', 'ContractLogsController', ['except' => 'show']);

});
