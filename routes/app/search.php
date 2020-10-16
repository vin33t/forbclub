<?php

use App\Http\Controllers\LanguageController;

// Users Pages
Route::middleware('auth')->group(function () {

    Route::get('/client/{searchString}', 'SearchController@client')->name('search.client');



  // Client Route Ends //

});
