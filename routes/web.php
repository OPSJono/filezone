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

use App\Http\Middleware\SuperUserMiddleware;
use App\Models\User;
use Laravel\Lumen\Routing\Router;

use Illuminate\Http\Request;
use Dusterio\LumenPassport\LumenPassport;

$router->options(
    '/{any:.*}',
    [
        function (){
            return response(['success' => true])
                ->header('Access-Control-Allow-Origin', '*', true)
            ;
        }
    ]
);

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
    $router->addRoute(['GET', 'POST'], 'validate-url-signature', ['as' => 'validate-url-signature', 'uses' => 'AuthController@validateUrlSignature']);
    $router->post('password/request', ['as' => 'password.request', 'uses' => 'AuthController@requestPasswordReset']);

    $router->group(['middleware' => 'signed'], function () use ($router) {
        $router->post('password/reset', ['as' => 'password.reset', 'uses' => 'AuthController@resetPassword']);
        $router->get('verification/verify', ['as' => 'verification.verify', 'uses' => 'AuthController@verifyEmail']);
    });

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('logout', ['uses' => 'AuthController@logout']);
    });
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

    // Endpoints for User Profile.
    $router->group(['prefix' => 'profile'], function () use ($router) {
        $router->get('/', ['uses' => 'ProfileController@index']);
        $router->post('update', ['uses' => 'ProfileController@update']);
    });

    // Endpoints for Users.
    $router->group(['prefix' => 'users', 'middleware' => SuperUserMiddleware::class], function () use ($router) {
        $router->get('/', ['uses' => 'UserController@index']);
        $router->post('create', ['uses' => 'UserController@create']);
        $router->get('{id}/view', ['uses' => 'UserController@view']);
        $router->post('{id}/update', ['uses' => 'UserController@update']);
        $router->delete('{id}/delete', ['uses' => 'UserController@delete']);
    });

    // Endpoints for Folders.
    $router->group(['prefix' => 'folders'], function () use ($router) {
        $router->get('/', ['uses' => 'FolderController@index']);
        $router->post('create', ['uses' => 'FolderController@create']);
        $router->get('{id}/view', ['uses' => 'FolderController@view']);
        $router->post('{id}/update', ['uses' => 'FolderController@update']);
        $router->delete('{id}/delete', ['uses' => 'FolderController@delete']);
    });

    // Endpoints for Files in a Folder.
    $router->group(['prefix' => 'files'], function () use ($router) {
        $router->get('/', ['uses' => 'FileController@index']);
        $router->post('create', ['uses' => 'FileController@create']);
        $router->get('{id}/view', ['uses' => 'FileController@view']);
        $router->post('{id}/update', ['uses' => 'FileController@update']);
        $router->delete('{id}/delete', ['uses' => 'FileController@delete']);

        $router->get('{id}/download', ['uses' => 'FileController@download']);
    });
});
