<?php

  Route::get('/','Client\EmailController@emails')->name('emails');
  Route::get('/client/{slug}','Client\EmailController@emailsSearchClient')->name('emails.search.client');
  Route::get('/compose','Client\EmailController@compose')->name('email.compose');
  Route::get('/sent','Client\EmailController@emailsSent')->name('emails.sent');
  Route::get('/mrd','Client\EmailController@emailsMrd')->name('emails.mrd');
  Route::get('/accounts','Client\EmailController@emailsAccounts')->name('emails.accounts');
  Route::get('/noreply','Client\EmailController@emailsNoreply')->name('emails.noreply');
  Route::get('/bookings','Client\EmailController@emailsBookings')->name('emails.bookings');
  Route::get('/{id}','Client\EmailController@emailsContent')->name('email.content');
  Route::get('/sent/{id}','Client\EmailController@emailsSentContent')->name('email.sent.content');

  Route::get('/templates/view','Client\EmailController@templates')->name('email.templates');
  Route::post('/templates/create','Client\EmailController@storeTemplate')->name('email.templates.create');
  Route::get('/templates/view/{id}','Client\EmailController@viewTemplate')->name('email.templates.view');

  Route::post('/search','Client\EmailController@searchMail')->name('email.search');


?>
