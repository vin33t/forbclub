<?php

Route::middleware('auth')->group(function () {

  Route::get('/', 'ReportsController@index')->name('reports');

});
?>
