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

    $router->post('accounts/image', 'AccountsController@image');
    $router->resource('accounts', 'AccountsController');

});

