<?php

  Route::get('/','Client\EmailController@emails')->name('emails');
  Route::get('/sent','Client\EmailController@emailsSent')->name('emails.sent');
  Route::get('/mrd','Client\EmailController@emailsMrd')->name('emails.mrd');
  Route::get('/accounts','Client\EmailController@emailsAccounts')->name('emails.accounts');
  Route::get('/noreply','Client\EmailController@emailsNoreply')->name('emails.noreply');
  Route::get('/bookings','Client\EmailController@emailsBookings')->name('emails.bookings');
  Route::get('/{id}','Client\EmailController@emailsContent')->name('email.content');
  Route::get('/sent/{id}','Client\EmailController@emailsSentContent')->name('email.sent.content');



  Route::post('/search','Client\EmailController@searchMail')->name('email.search');


?>
