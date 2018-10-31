<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('/example', 'ExampleController@index')->name('Example.index');

    // Clients Routes
    $router->get('/clients', 'ClientController@index')->name('Clients.index');
    $router->get('/clients/show', 'ClientController@show')->name('Client.show');
    $router->get('/clients/edit/{id}', 'ClientController@edit')->name('Client.edit');
    $router->get('/clients/create', 'ClientController@create')->name('Client.create');
    //$router->post('/clients/store', 'ClientController@store')->name('Client.store');
    $router->post('/clients', 'ClientController@store')->name('Client.store');



    // SMS Routes
    $router->get('/sms', 'SmsController@index')->name('Sms.index');
    $router->get('/sms/new', 'SmsController@new')->name('Sms.new');
    $router->post('/sms/send', 'SmsController@send')->name('Sms.send');
});
