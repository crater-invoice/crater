<?php

use Crater\Http\Controllers\V1\Auth\LoginController;
use Crater\Http\Controllers\V1\Estimate\EstimatePdfController;
use Crater\Http\Controllers\V1\Expense\DownloadReceiptController;
use Crater\Http\Controllers\V1\Invoice\InvoicePdfController;
use Crater\Http\Controllers\V1\Mobile\Customer\EstimatePdfController as CustomerEstimatePdfController;
use Crater\Http\Controllers\V1\Mobile\Customer\InvoicePdfController as CustomerInvoicePdfController;
use Crater\Http\Controllers\V1\Payment\PaymentPdfController;
use Crater\Http\Controllers\V1\Report\CustomerSalesReportController;
use Crater\Http\Controllers\V1\Report\ExpensesReportController;
use Crater\Http\Controllers\V1\Report\ItemSalesReportController;
use Crater\Http\Controllers\V1\Report\ProfitLossReportController;
use Crater\Http\Controllers\V1\Report\TaxSummaryReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
*/

Route::post('login', [LoginController::class, 'login']);


Route::prefix('reports')->group(function () {

    // sales report by customer
    //----------------------------------
    Route::get('/sales/customers/{hash}', CustomerSalesReportController::class);

    // sales report by items
    //----------------------------------
    Route::get('/sales/items/{hash}', ItemSalesReportController::class);

    // report for expenses
    //----------------------------------
    Route::get('/expenses/{hash}', ExpensesReportController::class);

    // report for tax summary
    //----------------------------------
    Route::get('/tax-summary/{hash}', TaxSummaryReportController::class);

    // report for profit and loss
    //----------------------------------
    Route::get('/profit-loss/{hash}', ProfitLossReportController::class);
});


// download invoice pdf
// -------------------------------------------------

Route::get('/invoices/pdf/{invoice:unique_hash}', InvoicePdfController::class);


// download estimate pdf
// -------------------------------------------------

Route::get('/estimates/pdf/{estimate:unique_hash}', EstimatePdfController::class);


// download payment pdf
// -------------------------------------------------

Route::get('/payments/pdf/{payment:unique_hash}', PaymentPdfController::class);


// download expense receipt
// -------------------------------------------------

Route::get('/expenses/{expense}/receipt', DownloadReceiptController::class);


// customer pdf endpoints for invoice and estimate
// -------------------------------------------------

Route::get('/customer/invoices/pdf/{invoice:unique_hash}', CustomerInvoicePdfController::class);

Route::get('/customer/estimates/pdf/{estimate:unique_hash}', CustomerEstimatePdfController::class);


Route::get('auth/logout', function () {
    Auth::guard('web')->logout();
});


// Setup for installation of app
// ----------------------------------------------

Route::get('/on-boarding', function () {
    return view('app');
})->name('install')->middleware('redirect-if-installed');


// Move other http requests to the Vue App
// -------------------------------------------------

Route::get('/admin/{vue?}', function () {
    return view('app');
})->where('vue', '[\/\w\.-]*')->name('admin')->middleware(['install', 'redirect-if-unauthenticated']);


// Move other http requests to the Vue App
// -------------------------------------------------

Route::get('/{vue?}', function () {
    return view('app');
})->where('vue', '[\/\w\.-]*')->name('login')->middleware(['install', 'guest']);
