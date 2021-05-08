<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('competitions');
});

Auth::routes(['register' => false]);
Route::resource('competitions', 'CompetitionController')->middleware('auth');
Route::match(['get', 'put'], 'competitions/parse/{competition}', ['as' => 'competitions.parse', 'uses' => 'ParsingController@parse'])->middleware('auth');
Route::get('competitions/parse/{competition}/dry-run', ['as' => 'competitions.dry_run', 'uses' => 'ParsingController@dryRun'])->middleware('auth');
Route::get('/save-to-database/{competition}', ['uses' => 'ParsingController@saveToDatabase', 'as' => 'save_database'])->middleware('auth');
