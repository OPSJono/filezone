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

use App\Models\User;
use Laravel\Lumen\Routing\Router;

use Illuminate\Http\Request;
use Dusterio\LumenPassport\LumenPassport;

// App root.
$router->get('/', function (Request $request) use ($router) {
    return response()->json([
        'version' => $router->app->version(),
    ]);
});

// Oauth/Passport Register, Login, Logout routes.
$router->group(['prefix' => 'v1/oauth'], function () use ($router) {
    LumenPassport::routes($router, ['prefix' => '']);
    $router->post('register', ['uses' => 'AuthController@register']);
    $router->post('logout', ['middleware' => 'auth', 'uses' => 'AuthController@logout']);
});

// Main application routes.
// User authentication required.
$router->group(['middleware' => 'auth', 'prefix' => 'v1'], function () use ($router) {
    // Lumen version, current logged in user info.
    $router->get('/', function (Request $request) use ($router) {
        return response()->json([
            'version' => $router->app->version(),
            'user' => User::currentUser(),
        ]);
    });

    // Endpoints for Folders.
    $router->group(['prefix' => 'folders'], function () use ($router) {
        $router->get('/', ['uses' => 'FolderController@index']);
        $router->post('create', ['uses' => 'FolderController@create']);
        $router->post('{id}/update', ['uses' => 'FolderController@update']);
        $router->post('{id}/delete', ['uses' => 'FolderController@delete']);
    });

    // Endpoints for Files in a Folder.
    $router->group(['prefix' => 'files'], function () use ($router) {
        $router->get('/', ['uses' => 'FileController@index']);
        $router->post('create', ['uses' => 'FileController@create']);
        $router->post('{id}/update', ['uses' => 'FileController@update']);
        $router->post('{id}/delete', ['uses' => 'FileController@delete']);
    });
});
