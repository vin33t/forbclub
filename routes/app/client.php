<?php

use App\Http\Controllers\LanguageController;

Route::middleware('auth')->group(function () {

  // Client Routes Start //
//  Route::prefix('employee')->group(function () {

//  Route::get('/', 'EmployeeController@employeeIndex')->name('employee')->middleware(['role_or_permission:super-admin|view-employee']);
//
//  Route::get('/edit/{id}', 'EmployeeController@editEmployee')->name('edit.employee');
//  Route::post('/edit/{id}', 'EmployeeController@updateEmployee')->name('edit.employee');

  Route::prefix('create')->middleware(['role_or_permission:super-admin|create-client'])->group(function () {
    Route::get('/', 'Client\ClientController@createClient')->name('create.client');
    Route::post('/', 'Client\ClientController@storeClient')->name('create.client');
  });


  Route::get('/profile/{slug}', 'Client\ClientController@viewClient')->name('view.client')->middleware(['role_or_permission:super-admin|view-client']);

  Route::prefix('comment')->middleware(['role_or_permission:super-admin|view-client'])->group(function () {
    Route::post('/{activityId}', 'Client\ClientController@storeTimelineComment')->name('create.client.timelineComment');
    Route::post('/reply', 'Client\ClientController@storeClient')->name('create.client');

  });

  Route::prefix('package')->middleware(['role_or_permission:super-admin'])->group(function () {
      Route::post('/add/benefit/{currentPackageId}/{clientId}', 'Client\ClientController@addPackageBenefit')->name('add.client.package.benefit');
//      Route::post('/edit/benefit/{packageId}', 'Client\ClientController@storeTimelineComment')->name('edit.client.package.benefit');
  });


  Route::post('update/mode-of-payment','Client\ClientController@updateModeOfPayment')->name('update.modeOfPayment');

  Route::get('migrate','Client\ClientController@migrate');
  Route::get('mTxn',function(){
//    foreach (\App\Client\Transaction\CardPayment::all() as $card){
//      if($card->maf != 0){
//        $client = \App\Client\Package\SoldPackages::where('fclpId',$card->maf)->first();
//        $card->client_id = $client->id;
//        $card->save();
//      }
//    }
//
//    foreach (\App\Client\Transaction\CashPayment::all() as $cash){
//      if($cash->ftk != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId',$cash->maf)->first();
//        $cash->client_id = $client->id;
//        $cash->save();
//      }
//    }
//
//    foreach (\App\Client\Transaction\ChequePayment::all() as $cheque){
//      if($cheque->maf != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId', $cheque->maf)->first();
//        if($client){
//        $cheque->client_id = $client->id;
//        $cheque->save();
//        }
//      }
//    }
//
//    foreach (\App\Client\Transaction\OtherPayment::all() as $other){
//      if($other->maf != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId', $other->maf)->first();
//        $other->client_id = $client->id;
//        $other->save();
//      }
//    }
//
//    foreach (\App\Client\Transaction\AxisNachPayment::all() as $axis){
//      if($axis->maf != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId', $axis->maf)->first();
//        $axis->client_id = $client->id;
//        $axis->save();
//      }
//    }
//
//    foreach (\App\Client\Transaction\YesNachPayment::all() as $yes){
//      if($yes->maf != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId', $yes->maf)->first();
//        $yes->client_id = $client->id;
//        $yes->save();
//      }
//    }
//
//    foreach (\App\Client\Mis\AxisMis::all() as $mis){
//      if($mis->maf != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId', $mis->maf)->first();
//        $mis->client_id = $client->id;
//        $mis->save();
//      }
//    }
//
//    foreach (\App\DisableNach::all() as $nach){
//      if($nach->maf != 0) {
//        $client = \App\Client\Package\SoldPackages::where('fclpId', $nach->maf)->first();
//        $nach->client_id = $client->id;
//        $nach->save();
//      }
//    }
  });

  // Client Route Ends //

});
