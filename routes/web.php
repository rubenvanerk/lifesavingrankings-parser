<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', ['as' => 'upload', function () use ($router) {
    return view('upload');
}]);
$router->post('/', ['uses' => 'FileController@upload']);

$router->get('/config/{file:.*}', ['uses' => 'FileController@config', 'as' => 'config']);
$router->post('/config/{file:.*}', ['uses' => 'FileController@saveConfig', 'as' => 'save_config']);
$router->get('/dry-run/{file:.*}', ['uses' => 'FileController@dryRun', 'as' => 'dry_run']);
$router->get('/save-to-database/{file:.*}', ['uses' => 'FileController@saveToDatabase', 'as' => 'save_database']);

$router->get('/browse[/{path:.*}]', ['uses' => 'FileController@browse', 'as' => 'browse']);


