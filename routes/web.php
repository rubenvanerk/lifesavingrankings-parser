<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
}, ['as' => 'home']);

Route::match(['get', 'post'], '/upload', ['as' => 'upload', 'uses' => 'FileController@upload']);
Route::get('/config/{file?}', ['uses' => 'FileController@config', 'as' => 'config'])->where(['file' => '.*']);
Route::post('/config/{file?}', ['uses' => 'FileController@saveConfig', 'as' => 'save_config'])->where(['file' => '.*']);
Route::get('/dry-run/{file?}', ['uses' => 'FileController@dryRun', 'as' => 'dry_run'])->where(['file' => '.*']);
Route::get('/save-to-database/{connection}/{file:.*}', ['uses' => 'FileController@saveToDatabase', 'as' => 'save_database']);
Route::get('/browse/{path?}', ['uses' => 'FileController@browse', 'as' => 'browse'])->where(['path' => '.*']);

Auth::routes(['register' => false]);
Route::resource('competitions', 'CompetitionController');
Route::match(['get', 'put'], 'competitions/parse/{competition}', ['as' => 'competitions.parse', 'uses' => 'CompetitionController@parse']);
Route::get('competitions/parse/{competition}/dry-run', ['as' => 'competitions.dry_run', 'uses' => 'CompetitionController@dryRun']);
