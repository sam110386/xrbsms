<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    // Clients Routes
    $router->get('/clients', 'ClientController@index')->name('Clients.index');
    $router->get('/clients/{id}', 'ClientController@show')->name('Client.show')->where('id', '[0-9]+');
    $router->get('/clients/{id}/edit/', 'ClientController@edit')->name('Client.edit')->where('id', '[0-9]+');

    $router->get('/clients/create', 'ClientController@create')->name('Client.create');

    $router->post('/clients', 'ClientController@store')->name('Client.store');
    $router->match(['put', 'patch'], '/clients/{id}','ClientController@update');
    $router->delete('/clients/{id}', 'ClientController@destroy')->where('id', '[0-9]+');



    // SMS Routes
    $router->get('/sms', 'SmsController@index')->name('Sms.index');
    $router->get('/sms/new', 'SmsController@new')->name('Sms.new');
    $router->post('/sms/send', 'SmsController@send')->name('Sms.send');
    //Sms Logs
    $router->get('/smslogs', 'SmslogsController@index')->name('Smslogs.index');
    $router->get('/smslogs/index', 'SmslogsController@index')->name('Smslogs.index1');
    $router->get('/smslogs/{id}', 'SmslogsController@show')->name('Smslogs.show')->where('id', '[0-9]+');
    $router->delete('/smslogs/{id}', 'SmslogsController@destroy')->where('id', '[0-9]+');
});
