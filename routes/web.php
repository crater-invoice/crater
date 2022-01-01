<?php

use Crater\Http\Controllers\V1\Admin\Auth\LoginController;
use Crater\Http\Controllers\V1\Admin\Expense\ShowReceiptController;
use Crater\Http\Controllers\V1\Admin\Report\CustomerSalesReportController;
use Crater\Http\Controllers\V1\Admin\Report\ExpensesReportController;
use Crater\Http\Controllers\V1\Admin\Report\ItemSalesReportController;
use Crater\Http\Controllers\V1\Admin\Report\ProfitLossReportController;
use Crater\Http\Controllers\V1\Admin\Report\TaxSummaryReportController;
use Crater\Http\Controllers\V1\Customer\EstimatePdfController as CustomerEstimatePdfController;
use Crater\Http\Controllers\V1\Customer\InvoicePdfController as CustomerInvoicePdfController;
use Crater\Http\Controllers\V1\PDF\DownloadInvoicePdfController;
use Crater\Http\Controllers\V1\PDF\DownloadPaymentPdfController;
use Crater\Http\Controllers\V1\PDF\DownloadReceiptController;
use Crater\Http\Controllers\V1\PDF\EstimatePdfController;
use Crater\Http\Controllers\V1\PDF\InvoicePdfController;
use Crater\Http\Controllers\V1\PDF\PaymentPdfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
*/

Route::post('login', [LoginController::class, 'login']);


Route::middleware('auth:sanctum')->prefix('reports')->group(function () {

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


// view invoice pdf
// -------------------------------------------------

Route::get('/invoices/pdf/{invoice:unique_hash}', InvoicePdfController::class);


// download invoice pdf
// -------------------------------------------------

Route::get('/invoices/pdf/download/{invoice:unique_hash}', DownloadInvoicePdfController::class);



// view estimate pdf
// -------------------------------------------------

Route::get('/estimates/pdf/{estimate:unique_hash}', EstimatePdfController::class);


// view payment pdf
// -------------------------------------------------

Route::get('/payments/pdf/{payment:unique_hash}', PaymentPdfController::class);


// download payment pdf
// -------------------------------------------------

Route::get('/payments/pdf/download/{payment:unique_hash}', DownloadPaymentPdfController::class);


// download expense receipt
// -------------------------------------------------

Route::get('/expenses/{expense}/download-receipt', DownloadReceiptController::class);
Route::get('/expenses/{expense}/receipt', ShowReceiptController::class);


// customer pdf endpoints for invoice and estimate
// -------------------------------------------------

Route::get('/customer/invoices/pdf/{invoice:unique_hash}', CustomerInvoicePdfController::class);

Route::get('/customer/estimates/pdf/{estimate:unique_hash}', CustomerEstimatePdfController::class);


Route::get('auth/logout', function () {
    Auth::guard('web')->logout();
});


// Setup for installation of app
// ----------------------------------------------

Route::get('/installation', function () {
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
})->where('vue', '[\/\w\.-]*')->name('login')->middleware(['install']);
