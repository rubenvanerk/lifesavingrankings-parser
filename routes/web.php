<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', ['as' => 'upload', 'uses' => 'FileController@upload']);

$router->get('/config/{file?}', ['uses' => 'FileController@config', 'as' => 'config'])->where(['file' => '.*']);
$router->post('/config/{file?}', ['uses' => 'FileController@saveConfig', 'as' => 'save_config'])->where(['file' => '.*']);
$router->get('/dry-run/{file?}', ['uses' => 'FileController@dryRun', 'as' => 'dry_run'])->where(['file' => '.*']);
$router->get('/save-to-database/{connection}/{file:.*}', ['uses' => 'FileController@saveToDatabase', 'as' => 'save_database']);

$router->get('/browse/{path?}', ['uses' => 'FileController@browse', 'as' => 'browse'])->where(['path' => '.*']);

Auth::routes(['register' => false]);
