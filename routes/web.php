<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/test', function () use ($router) {
    return 'ok';
});

// Routes without /api prefix (for public access like teacher showed)
$router->get('/users', 'UserController@index');
$router->post('/users', 'UserController@add');
$router->get('/users/{id}', 'UserController@show');
$router->put('/users/{id}', 'UserController@update');
$router->patch('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@delete');

// Routes with /api prefix (original group)
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/users', 'UserController@index');
    $router->post('/users', 'UserController@add');
    $router->get('/users/{id}', 'UserController@show');
    $router->put('/users/{id}', 'UserController@update');
    $router->patch('/users/{id}', 'UserController@update');
    $router->delete('/users/{id}', 'UserController@delete');
    $router->get('/users-raw', function () {    
        return \App\Models\User::all();
    });
    $router->get('/debug', function () {
        return [
            'db' => env('DB_DATABASE'),
            'cwd' => getcwd(),
        ];
    });
});