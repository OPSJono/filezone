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
 * @var $router \Laravel\Lumen\Routing\Router;
 */

// Uses Auth Middleware
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return response()->json([
            'version' => $router->app->version()
        ]);
    });
});
