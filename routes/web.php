<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', ['as' => 'upload', function () use ($router) {
    return view('upload');
}]);
$router->post('/', ['uses' => 'FileController@upload']);

$router->get('/config/{file?}', ['uses' => 'FileController@config', 'as' => 'config'])->where(['file' => '.*']);
$router->post('/config/{file?}', ['uses' => 'FileController@saveConfig', 'as' => 'save_config'])->where(['file' => '.*']);
$router->get('/dry-run/{file?}', ['uses' => 'FileController@dryRun', 'as' => 'dry_run'])->where(['file' => '.*']);
$router->get('/save-to-database/{connection}/{file:.*}', ['uses' => 'FileController@saveToDatabase', 'as' => 'save_database']);

$router->get('/browse/{path?}', ['uses' => 'FileController@browse', 'as' => 'browse'])->where(['path' => '.*']);


