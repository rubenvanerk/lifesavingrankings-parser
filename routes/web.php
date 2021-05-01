<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('competitions');
});

Auth::routes(['register' => false]);
Route::resource('competitions', 'CompetitionConfigController')->middleware('auth');
Route::match(['get', 'put'], 'competitions/parse/{competition}', ['as' => 'competitions.parse', 'uses' => 'CompetitionConfigController@parse'])->middleware('auth');
Route::get('competitions/parse/{competition}/dry-run', ['as' => 'competitions.dry_run', 'uses' => 'CompetitionConfigController@dryRun'])->middleware('auth');
Route::get('/save-to-database/{competition}', ['uses' => 'CompetitionConfigController@saveToDatabase', 'as' => 'save_database'])->middleware('auth');
