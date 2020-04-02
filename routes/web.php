<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/**
 * @var $router Router;
 */
use Laravel\Lumen\Routing\Router;

use Illuminate\Http\Request;
use Dusterio\LumenPassport\LumenPassport;

$router->group(['prefix' => 'v1/oauth'], function () use ($router) {
    LumenPassport::routes($router, ['prefix' => '']);
    $router->post('register', ['uses' => 'AuthController@register']);
    $router->post('logout', ['middleware' => 'auth', 'uses' => 'AuthController@logout']);
});


// Uses Auth Middleware
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/', function (Request $request) use ($router) {
        return response()->json([
            'version' => $router->app->version(),
            'user' => $request->user()
        ]);
    });
});
