<?php
// use Illuminate\Http\Request;
use Illuminate\Routing\Router;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=> 'v1'], function (Router $router) {
    $router->post('/sms/send', 'ApiController@sendSms');
    $router->post('/sms/status', 'ApiController@getSmsStatus');
    $router->post('/user/add', 'ApiController@addClient');
});