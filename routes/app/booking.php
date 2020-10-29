<?php


Route::middleware('auth')->group(function () {

  // Booking Routes Start //
//  Route::prefix('employee')->group(function () {

//  Route::get('/', 'EmployeeController@employeeIndex')->name('employee')->middleware(['role_or_permission:super-admin|view-employee']);
//
//  Route::get('/edit/{id}', 'EmployeeController@editEmployee')->name('edit.employee');
//  Route::post('/edit/{id}', 'EmployeeController@updateEmployee')->name('edit.employee');

  Route::prefix('create')->middleware(['role_or_permission:super-admin|create-client'])->group(function () {
    Route::get('/{slug}', 'Client\BookingController@createBooking')->name('create.client.booking');
    Route::post('/{slug}', 'Client\BookingController@storeBooking')->name('store.client.booking');
    Route::get('/offer/{bookingId}', 'Client\BookingController@bookingOffer')->name('booking.offer');
    Route::post('/offer/{bookingId}/store', 'Client\BookingController@storeBookingOffer')->name('store.booking.offer');
  });
    Route::post('/offer/{bookingId}/update', 'Client\BookingController@updateBookingOffer')->name('update.booking.offer');

    Route::get('/', 'Client\BookingController@index')->name('booking');
    Route::get('/all', 'Client\BookingController@show')->name('booking.all');
    Route::get('/in-processing-by-mrd', 'Client\BookingController@inProcessingByMrd')->name('booking.processing.mrd');
    Route::get('/approved-by-manager', 'Client\BookingController@approvedByManager')->name('booking.manager.approved');
    Route::get('/holiday-in-progress', 'Client\BookingController@holidayInProgress')->name('booking.holiday.progress');
    Route::get('/convert-booking/{bookingId}', 'Client\BookingController@ConvertBooking')->name('booking.convert');
    Route::post('/add-transaction', 'Client\BookingController@addTransaction')->name('booking.add.transaction');
    Route::post('/convert-booking', 'Client\BookingController@convert')->name('booking.convert');

    Route::post('/status/update/{bookingId}', 'Client\BookingController@updateStatus')->name('update.booking.status');


    Route::post('/approve/offer/{bookingId}', 'Client\BookingController@approveOffer')->name('approve.booking.offer');
  // Booking Route Ends //

});
