<?php

use App\Http\Controllers\LanguageController;

// Users Pages
Route::middleware('auth')->group(function () {

  Route::prefix('create')->middleware(['role_or_permission:super-admin|create-transaction'])->group(function () {
    Route::post('/card/{clientId}', 'Client\TransactionController@createCard')->name('create.transaction.card');
    Route::post('/cash/{clientId}', 'Client\TransactionController@createCash')->name('create.transaction.cash');
    Route::post('/cheque/{clientId}', 'Client\TransactionController@createCheque')->name('create.transaction.cheque');
    Route::post('/other/{clientId}', 'Client\TransactionController@createOther')->name('create.transaction.other');
    Route::post('/asf/{clientId}', 'Client\TransactionController@createAsf')->name('create.transaction.asf');
  });


  Route::prefix('edit')->middleware(['role_or_permission:super-admin|edit-transaction'])->group(function () {
    Route::post('/card/{transactionId}', 'Client\TransactionController@editCard')->name('edit.transaction.card');
    Route::post('/cheque/{transactionId}', 'Client\TransactionController@editCheque')->name('edit.transaction.cheque');
    Route::post('/cash/{transactionId}', 'Client\TransactionController@editCash')->name('edit.transaction.cash');
    Route::post('/others/{transactionId}', 'Client\TransactionController@editOthers')->name('edit.transaction.others');
    Route::post('/asf/{clientId}', 'Client\TransactionController@editAsf')->name('edit.transaction.asf');

//    Route::post('/cash/{clientId}', 'Client\TransactionController@createCash')->name('create.transaction.cash');
//    Route::post('/cheque/{clientId}', 'Client\TransactionController@createCheque')->name('create.transaction.cheque');
  });

    Route::get('/chart/profile/{clientId}', 'Client\TransactionController@displayTransactionPieChart')->name('display.transaction.pie.chart');


    Route::get('/import-history', 'Client\TransactionController@importHistory')->name('display.transaction.nach.import.history');
    Route::get('/import-history/{importId}/{bank}', 'Client\TransactionController@importHistoryDetails')->name('display.transaction.nach.import.history.details');


    Route::post('/import-delete/{importId}/{bank}', 'Client\TransactionController@importHistoryDelete');


    Route::get('/downlaod/axis-mis', 'Client\TransactionController@downloadAxisMis')->name('download.axis.mis');
    Route::post('/downlaod/axis-mis-file', 'Client\TransactionController@downloadAxisMisFile')->name('download.axis.mis.file');


    Route::get('/download/cheques', 'Client\TransactionController@downloadChequesView')->name('download.cheques.view');
    Route::post('/download/cheques', 'Client\TransactionController@downloadCheques')->name('download.cheques');

    Route::get('/upload/axis-mis', 'Client\TransactionController@uploadAxisMis')->name('upload.axis.mis');
    Route::post('/upload/axis-mis-file', 'Client\TransactionController@uploadAxisMisFile')->name('upload.axis.mis.file');

    Route::get('/upload', 'Client\TransactionController@uploadTransaction')->name('upload.transaction');
    Route::post('/upload/file', 'Client\TransactionController@uploadTransactionFile')->name('upload.transaction.file');


    Route::post('/disable/nach', 'Client\TransactionController@disableNach')->name('disable.nach');



    Route::post('/add/refund/request/{slug}', 'Client\Transaction\RefundController@refund')->name('add.refund.request');
  Route::get('/client/refund-requests', 'Client\Transaction\RefundController@refundRequests')->name('refund.requests');
  Route::post('/client/refund-request-accept', 'Client\Transaction\RefundController@acceptRefundRequest')->name('accept.refund.request');
  Route::post('/client/refund-request-deny', 'Client\Transaction\RefundController@denyRefundRequest')->name('deny.refund.request');
  Route::post('/client/refund-request-approve', 'Client\Transaction\RefundController@approveRefundRequest')->name('approve.refund.request');
  Route::post('/client/refund-request-reject', 'Client\Transaction\RefundController@rejectRefundRequest')->name('reject.refund.request');
  Route::post('/client/refund-request-approve-accounts', 'Client\Transaction\RefundController@approveAccountsRefundRequest')->name('approve.accounts.refund.request');



    Route::post('/add/pdc', 'Client\TransactionController@addPdc')->name('add.pdc');
    Route::post('/update/pdc', 'Client\TransactionController@updatePdc')->name('update.pdc');
    Route::post('/update/pdc/status/{id}', 'Client\TransactionController@updatePdcStatus')->name('update.pdc.status');

    Route::get('/reimbursement', 'Client\TransactionController@reimbursementIndex')->name('reimbursement.index');
    Route::get('/reimbursement-summary', 'Client\TransactionController@reimbursementSummary')->name('reimbursement.summary');
    Route::post('/reimbursement/add-new', 'Client\TransactionController@reimbursementAdd')->name('reimbursement.add');
    Route::post('/reimbursement/update', 'Client\TransactionController@reimbursementUpdate')->name('reimbursement.update');
    Route::post('/reimburse', 'Client\TransactionController@reimburse')->name('reimbursement.pay');
    Route::post('/reject', 'Client\TransactionController@reject')->name('reimbursement.reject');



    Route::get('/venue-expense', 'Client\TransactionController@venueExpense')->name('venue.expense');
    Route::get('/venue-expense-summary', 'Client\TransactionController@venueExpenseSummary')->name('venue.expense.summary');
    Route::post('/venue/add', 'Client\TransactionController@venueAdd')->name('venue.add');
    Route::post('/venue/edit', 'Client\TransactionController@venueEdit')->name('venue.edit');
    Route::post('/venue/cancel', 'Client\TransactionController@venueCancel')->name('venue.cancel');
    Route::post('/venue/expense/add', 'Client\TransactionController@venueExpenseAdd')->name('venue.add.expense');
    Route::post('/venue/expense/edit', 'Client\TransactionController@venueExpenseEdit')->name('venue.edit.expense');


  // Client Route Ends //

});
