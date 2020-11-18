<?php

  Route::get('/','Client\EmailController@emails')->name('emails');
  Route::get('/mrd','Client\EmailController@emailsMrd')->name('emails.mrd');
  Route::get('/accounts','Client\EmailController@emailsAccounts')->name('emails.accounts');
  Route::get('/noreply','Client\EmailController@emailsNoreply')->name('emails.noreply');
  Route::get('/bookings','Client\EmailController@emailsBookings')->name('emails.bookings');
  Route::get('/{id}','Client\EmailController@emailsContent')->name('email.content');


?>
