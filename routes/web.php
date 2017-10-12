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

    $router->get('accounts/toggleScope', 'AccountsController@toggleScope')->name('accounts.toggleScope');
    $router->get('contractLogs/toggleScope', 'ContractLogsController@toggleScope')->name('contractLogs.toggleScope');
    $router->get('reports/summary/toggleScope', 'ReportsController@toggleScopeSummary')->name('summaryReport.toggleScope');

    $router->get('sidebar-collapse', 'SidebarController@collapse');
    $router->get('sidebar-expand', 'SidebarController@expand');

    $router->get('settings/credentials', 'SettingsController@credentials')->name('settings.credentials.edit');
    $router->patch('settings/credentials', 'SettingsController@updateCredentials')->name('settings.credentials.update');
});

$router->group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'acl']], function ($router) {

    $router->get('reports/summary', 'ReportsController@summary')->name('reports.summary.index');
    $router->get('reports/usage', 'ReportsController@usage')->name('reports.usage.index');
    $router->get('reports/summary/excel', 'ReportsController@exportToExcel')->name('reports.summary.excel');
    $router->get('reports/summary/excel/detail', 'ReportsController@exportToExcelDetailed')->name('reports.summary.excel.details');

    $router->resource('dashboards', 'DashboardsController');

    $router->resource('affiliations', 'AffiliationsController');

    $router->post('accounts/image', 'AccountsController@image')->name('accounts.image');
    $router->patch('accounts/merge', 'AccountsController@merge')->name('accounts.merge');
    $router->patch('accounts/parent', 'AccountsController@parent')->name('accounts.parent');
    $router->patch('accounts/{account}/remove-parent', 'AccountsController@removeParent')->name('accounts.removeParent');
    $router->get('accounts/{account}/internal-plan', 'AccountsController@internalPlan')->name('accounts.internalPlan');
    $router->get('accounts/termed', 'AccountsController@termed')->name('termedSites.index');
    $router->get('accounts/{account}/manager', 'AccountsController@findManager')->name('accounts.find');
    
    $router->get('accounts/export', 'AccountsPipelineController@exportIndex')->name('accounts.export');
    $router->post('accounts/export/pdf', 'AccountsPipelineController@bulkExport')->name('accounts.export.pdf');
    
    $router->resource('accounts', 'AccountsController');

    $router->get('accounts/{account}/pipeline', 'AccountsPipelineController@index')->name('accounts.pipeline.index');
    $router->patch('accounts/{account}/pipeline', 'AccountsPipelineController@update')->name('accounts.pipeline.update');
    $router->get('accounts/{account}/pipeline/export/word', 'AccountsPipelineController@exportWord')->name('accounts.pipeline.export.word');
    $router->get('accounts/{account}/pipeline/export/excel', 'AccountsPipelineController@exportExcel')->name('accounts.pipeline.export.excel');

    $router->post('accounts/{account}/pipeline/rosterBench', 'PipelineRosterBenchController@store')->name('accounts.pipeline.rosterBench.store');
    $router->patch('accounts/{account}/pipeline/rosterBench/{rosterBench}', 'PipelineRosterBenchController@update')->name('accounts.pipeline.rosterBench.update');
    $router->delete('accounts/{account}/pipeline/rosterBench/{rosterBench}', 'PipelineRosterBenchController@destroy')->name('accounts.pipeline.rosterBench.destroy');
    $router->patch('accounts/{account}/pipeline/rosterBench/{rosterBench}/resign', 'PipelineRosterBenchController@resign')->name('accounts.pipeline.rosterBench.resign');
    $router->post('accounts/{account}/pipeline/rosterBench/{rosterBench}/switch', 'PipelineRosterBenchController@switch')->name('accounts.pipeline.rosterBench.switch');

    $router->post('accounts/{account}/pipeline/recruiting', 'PipelineRecruitingController@store')->name('accounts.pipeline.recruiting.store');
    $router->patch('accounts/{account}/pipeline/recruiting/{recruiting}/decline', 'PipelineRecruitingController@decline')->name('accounts.pipeline.recruiting.decline');
     $router->patch('accounts/{account}/pipeline/recruiting/{recruiting}', 'PipelineRecruitingController@update')->name('accounts.pipeline.recruiting.update');
    $router->delete('accounts/{account}/pipeline/recruiting/{recruiting}', 'PipelineRecruitingController@destroy')->name('accounts.pipeline.recruiting.destroy');
    $router->post('accounts/{account}/pipeline/recruiting/{recruiting}/switch', 'PipelineRecruitingController@switch')->name('accounts.pipeline.recruiting.switch');

    $router->post('accounts/{account}/pipeline/locum', 'PipelineLocumController@store')->name('accounts.pipeline.locum.store');
    $router->patch('accounts/{account}/pipeline/locum/{locum}/decline', 'PipelineLocumController@decline')->name('accounts.pipeline.locum.decline');
    $router->patch('accounts/{account}/pipeline/locum/{locum}', 'PipelineLocumController@update')->name('accounts.pipeline.locum.update');
    $router->delete('accounts/{account}/pipeline/locum/{locum}', 'PipelineLocumController@destroy')->name('accounts.pipeline.locum.destroy');
    $router->post('accounts/{account}/pipeline/locum/{locum}/switch', 'PipelineLocumController@switch')->name('accounts.pipeline.locum.switch');

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
    
    $router->get('contractLogs/exportAll', 'ContractLogsController@exportAll')->name('contractLogs.exportAll');
    $router->get('contractLogs/download', 'ContractLogsController@downloadZip')->name('contractLogs.downloadZip');
    $router->get('contractLogs/excel', 'ContractLogsController@exportToExcel')->name('contractLogs.excel');
    $router->resource('contractLogs', 'ContractLogsController', ['except' => 'show']);

    $router->get('users/csv', 'UsersController@importCsv')->name('users.bulkCreate');
});
