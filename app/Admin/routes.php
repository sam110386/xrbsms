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
    $router->get('/clients/autocomplete', 'ClientController@autocomplete')->name('Clients.autocomplete');


    // SMS Routes
    $router->get('/sms', 'SmsController@index')->name('Sms.index');
    $router->get('/sms/new', 'SmsController@new')->name('Sms.new');
    $router->post('/sms/send', 'SmsController@send')->name('Sms.send');
    $router->get('/sms/bulk', 'SmsController@bulk')->name('Sms.bulk');
    $router->post('/sms/sendbulk', 'SmsController@sendbulk')->name('Sms.sendbulk');
    $router->match(['get','put', 'patch','post'],'/sms/loadclients', 'SmsController@loadclients')->name('Sms.loadclients');
    //Sms Logs
    $router->get('/smslogs', 'SmslogsController@index')->name('Smslogs.index');
    $router->get('/smslogs/index', 'SmslogsController@index')->name('Smslogs.index1');
    $router->get('/smslogs/{id}', 'SmslogsController@show')->name('Smslogs.show')->where('id', '[0-9]+');
    $router->delete('/smslogs/{id}', 'SmslogsController@destroy')->where('id', '[0-9 ,]+');

    //setting
    $router->get('/setting', 'SettingController@index')->name('Setting.index');
    $router->get('/setting/smspisetting', 'SettingController@smspisetting')->name('Setting.smspisetting');
    $router->match(['put', 'patch','post'],'/setting/smsapiformsave', 'SettingController@smsapiformsave')->name('Setting.smsapiformsave');

    // SMS Scheddule Routes
    $router->get('/smsschedule', 'SmsscheduleController@index')->name('Smsschedule.index');
    $router->get('/smsschedule/{id}', 'SmsscheduleController@show')->name('Smsschedule.show')->where('id', '[0-9]+');
    $router->get('/smsschedule/{id}/edit/', 'SmsscheduleController@edit')->name('Smsschedule.edit')->where('id', '[0-9]+');

    $router->get('/smsschedule/create', 'SmsscheduleController@create')->name('Smsschedule.create');

    $router->post('/smsschedule/store', 'SmsscheduleController@store')->name('Smsschedule.store');
    $router->match(['put', 'patch'], '/smsschedule/{id}','SmsscheduleController@update');
    $router->delete('/smsschedule/{id}', 'SmsscheduleController@destroy')->where('id', '[0-9]+');

    $router->get('/settings', 'GeneralSettingsController@edit')->name('GeneralSettings.view');
    $router->match(['put', 'patch','post'],'/settings', 'GeneralSettingsController@update')->name('GeneralSettings.update');


    // TESTING SMS RESPONSE
    $router->get('/test/sms', 'ExampleController@testSms');
    $router->get('/test/sms/status', 'ExampleController@testSmsStatus');

});
