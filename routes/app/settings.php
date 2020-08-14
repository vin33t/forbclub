<?php

use App\Http\Controllers\LanguageController;

// Users Pages
Route::middleware('auth')->group(function () {

  // Role Management Routes Start //
  Route::prefix('role-management')->group(function () {

    Route::get('/', 'SettingsController@roleManagementIndex')->name('role-management')->middleware(['role_or_permission:super-admin|view-role']);


    Route::prefix('create')->middleware(['role_or_permission:super-admin|create-role'])->group(function () {
      Route::get('/role', 'SettingsController@createRoleForm')->name('create.role');
      Route::post('/role', 'SettingsController@createRole')->name('create.role');
    });
  });
  // Role Management Route Ends //


});
