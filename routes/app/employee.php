<?php

use App\Http\Controllers\LanguageController;

// Users Pages
Route::middleware('auth')->group(function () {
  Route::get('/logs','EmployeeController@userLogs')->name('employee.logs');

  // EmployeeRoutes Start //
//  Route::prefix('employee')->group(function () {

  Route::get('/', 'EmployeeController@employeeIndex')->name('employee')->middleware(['role_or_permission:super-admin|view-employee']);

  Route::get('/edit/{id}', 'EmployeeController@editEmployee')->name('edit.employee');
  Route::post('/edit/{id}', 'EmployeeController@updateEmployee')->name('edit.employee');

  Route::prefix('create')->middleware(['role_or_permission:super-admin|create-employee'])->group(function () {
    Route::post('/new/employee', 'SettingsController@createEmployee')->name('add.new.employee');
    Route::get('/employee', 'SettingsController@createEmployee')->name('create.employee');
    Route::post('/role', 'SettingsController@createEmployee')->name('create.employee');
  });

  Route::prefix('login')->middleware(['role_or_permission:super-admin|edit-employee'])->group(function () {
    Route::post('/suspend', 'EmployeeController@suspendLogin')->name('suspend.employee.login');
    Route::post('/activate', 'EmployeeController@activateLogin')->name('activate.employee.login');
    Route::post('/create', 'EmployeeController@createLogin')->name('create.employee.login');
  });


//  });
  // EmployeeRoute Ends //

});
