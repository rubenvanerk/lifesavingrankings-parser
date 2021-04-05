<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('competitions');
});

Auth::routes(['register' => false]);
Route::resource('competitions', 'CompetitionController')->middleware('auth');
Route::match(['get', 'put'], 'competitions/parse/{competition}', ['as' => 'competitions.parse', 'uses' => 'CompetitionController@parse'])->middleware('auth');
Route::get('competitions/parse/{competition}/dry-run', ['as' => 'competitions.dry_run', 'uses' => 'CompetitionController@dryRun'])->middleware('auth');
Route::get('/save-to-database/{competition}', ['uses' => 'CompetitionController@saveToDatabase', 'as' => 'save_database'])->middleware('auth');
