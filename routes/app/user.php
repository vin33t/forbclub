<?php
use App\Http\Controllers\LanguageController;

// Users Pages
Route::middleware('auth')->group(function () {

  Route::get('/app-user-list', 'UserPagesController@user_list');
  Route::get('/app-user-view', 'UserPagesController@user_view');
  Route::get('/app-user-edit', 'UserPagesController@user_edit');


// Users Data

  Route::get('/user-list', 'UserPagesController@get_users');


});
