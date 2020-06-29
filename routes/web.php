<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
}, ['as' => 'home']);

Route::match(['get', 'post'], '/upload', ['as' => 'upload', 'uses' => 'FileController@upload']);

Auth::routes(['register' => false]);
Route::resource('competitions', 'CompetitionController');
Route::match(['get', 'put'], 'competitions/parse/{competition}', ['as' => 'competitions.parse', 'uses' => 'CompetitionController@parse']);
Route::get('competitions/parse/{competition}/dry-run', ['as' => 'competitions.dry_run', 'uses' => 'CompetitionController@dryRun']);
Route::get('/save-to-database/{competition}', ['uses' => 'CompetitionController@saveToDatabase', 'as' => 'save_database']);
