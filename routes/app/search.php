<?php

use App\Http\Controllers\LanguageController;

// Users Pages
Route::middleware('auth')->group(function () {

    Route::get('/client/{searchString}', 'SearchController@client')->name('search.client');
    Route::post('/client/maf', 'SearchController@maf')->name('search.client.maf');
    Route::post('/client/fclp', 'SearchController@fclp')->name('search.client.fclp');



  // Client Route Ends //

});
