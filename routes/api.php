<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Authentication & Password Reset
//----------------------------------

Route::group(['prefix' => 'auth'], function () {

    Route::post('login', 'Auth\AccessTokensController@store');
    Route::get('logout', 'Auth\AccessTokensController@destroy');
    Route::post('refresh_token', 'Auth\AccessTokensController@update');

    // Send reset password mail
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

    // handle reset password form process
    Route::post('reset/password', 'Auth\ResetPasswordController@reset');

});

Route::post('is-registered',[
    'as' => 'is-registered','uses' => 'Auth\AccessTokensController@isRegistered'
]);

Route::get('/ping', [
    'as' => 'ping',
    'uses' => 'UsersController@ping'
]);

// Country, State & City
//----------------------------------

Route::get('/countries', [
    'as' => 'countries',
    'uses' => 'LocationController@getCountries'
]);


// Onboarding
//----------------------------------
Route::group(['middleware' => 'redirect-if-installed'], function () {

    Route::get('/admin/onboarding', [
        'as' => 'admin.onboarding',
        'uses' => 'OnboardingController@getOnboardingData'
    ]);

    Route::get('/admin/onboarding/requirements', [
        'as' => 'admin.onboarding.requirements',
        'uses' => 'RequirementsController@requirements'
    ]);

    Route::get('/admin/onboarding/permissions', [
        'as' => 'admin.onboarding.permissions',
        'uses' => 'PermissionsController@permissions'
    ]);

    Route::post('/admin/onboarding/environment/database', [
        'as' => 'admin.onboarding.database',
        'uses' => 'EnvironmentController@saveDatabaseEnvironment'
    ]);

    Route::get('/admin/onboarding/environment/mail', [
        'as' => 'admin.onboarding.mail',
        'uses' => 'EnvironmentController@getMailDrivers'
    ]);

    Route::post('/admin/onboarding/environment/mail', [
        'as' => 'admin.onboarding.mail',
        'uses' => 'EnvironmentController@saveMailEnvironment'
    ]);

    Route::post('/admin/onboarding/profile', [
        'as' => 'admin.profile',
        'uses' => 'OnboardingController@adminProfile'
    ]);

    Route::post('/admin/profile/upload-avatar', [
        'as' => 'admin.on_boarding.avatar',
        'uses' => 'OnboardingController@uploadAdminAvatar'
    ]);

    Route::post('/admin/onboarding/company', [
        'as' => 'admin.company',
        'uses' => 'OnboardingController@adminCompany'
    ]);

    Route::post('/admin/onboarding/company/upload-logo', [
        'as' => 'upload.admin.company.logo',
        'uses' => 'CompanyController@uploadCompanyLogo'
    ]);

    Route::post('/admin/onboarding/settings', [
        'as' => 'admin.settings',
        'uses' => 'OnboardingController@companySettings'
    ]);
});


// App version
// ----------------------------------

Route::get('/settings/app/version', [
    'as' => 'settings.app.version',
    'uses' => 'SettingsController@getAppVersion'
]);

Route::group(['middleware' => 'api'], function () {

    Route::group([
      'middleware' => 'admin'
    ], function () {


        // Auto update routes
        //----------------------------------
        Route::post('/update', [
            'as' => 'auto.update',
            'uses' => 'UpdateController@update'
        ]);

        Route::post('/update/finish', [
            'as' => 'auto.update.finish',
            'uses' => 'UpdateController@finishUpdate'
        ]);

        Route::get('/check/update', [
            'as' => 'check.update',
            'uses' => 'UpdateController@checkLatestVersion'
        ]);

        Route::get('/bootstrap', [
            'as' => 'bootstrap',
            'uses' => 'UsersController@getBootstrap'
        ]);

        Route::resource('payment-methods', 'PaymentMethodController');

        Route::resource('units', 'UnitController');


        // Dashboard
        //----------------------------------

        Route::get('/dashboard', [
            'as' => 'dashboard',
            'uses' => 'DashboardController@index'
        ]);


        // Customers
        //----------------------------------

        Route::post('/customers/delete', [
            'as' => 'customers.delete',
            'uses' => 'CustomersController@delete'
        ]);

        Route::resource('customers', 'CustomersController');


        // Items
        //----------------------------------

        Route::post('/items/delete', [
            'as' => 'items.delete',
            'uses' => 'ItemsController@delete'
        ]);

        Route::resource('items', 'ItemsController');


        // Invoices
        //-------------------------------------------------

        Route::post('/invoices/delete', [
            'as' => 'invoices.delete',
            'uses' => 'InvoicesController@delete'
        ]);

        Route::post('/invoices/send', [
            'as' => 'invoices.send',
            'uses' => 'InvoicesController@sendInvoice'
        ]);

        Route::post('/invoices/clone', [
            'as' => 'invoices.send',
            'uses' => 'InvoicesController@cloneInvoice'
        ]);

        Route::post('/invoices/mark-as-paid', [
            'as' => 'invoices.paid',
            'uses' => 'InvoicesController@markAsPaid'
        ]);

        Route::post('/invoices/mark-as-sent', [
            'as' => 'invoices.sent',
            'uses' => 'InvoicesController@markAsSent'
        ]);

        Route::get('/invoices/unpaid/{id}', [
            'as' => 'bootstrap',
            'uses' => 'InvoicesController@getCustomersUnpaidInvoices'
        ]);

        Route::resource('invoices', 'InvoicesController');


        // Tax Types
        //----------------------------------

        Route::resource('tax-types', 'TaxTypeController');


        // Estimates
        //-------------------------------------------------

        Route::post('/estimates/delete', [
            'as' => 'estimates.delete',
            'uses' => 'EstimatesController@delete'
        ]);

        Route::post('/estimates/send', [
            'as' => 'estimates.send',
            'uses' => 'EstimatesController@sendEstimate'
        ]);

        Route::post('/estimates/mark-as-sent', [
            'as' => 'estimates.send',
            'uses' => 'EstimatesController@markEstimateSent'
        ]);

        Route::post('/estimates/accept', [
            'as' => 'estimates.mark.accepted',
            'uses' => 'EstimatesController@markEstimateAccepted'
        ]);

        Route::post('/estimates/reject', [
            'as' => 'estimates.mark.rejected',
            'uses' => 'EstimatesController@markEstimateRejected'
        ]);

        Route::post('/estimates/{id}/convert-to-invoice', [
            'as' => 'estimate.to.invoice',
            'uses' => 'EstimatesController@estimateToInvoice'
        ]);

        Route::resource('estimates', 'EstimatesController');


        // Expenses
        //----------------------------------

        Route::post('/expenses/delete', [
            'as' => 'expenses.delete',
            'uses' => 'ExpensesController@delete'
        ]);

        Route::get('/expenses/{id}/show/receipt', [
            'as' => 'expenses.show',
            'uses' => 'ExpensesController@showReceipt',
        ]);

        Route::post('/expenses/{id}/upload/receipts', [
            'as' => 'estimate.to.invoice',
            'uses' => 'ExpensesController@uploadReceipts'
        ]);

        Route::resource('expenses', 'ExpensesController');


        // Expenses Categories
        //----------------------------------

        Route::resource('categories', 'ExpenseCategoryController');


        // Payments
        //----------------------------------

        Route::post('/payments/delete', [
            'as' => 'payments.delete',
            'uses' => 'PaymentController@delete'
        ]);

        Route::post('/payments/send', [
            'as' => 'payments.send',
            'uses' => 'PaymentController@sendPayment'
        ]);

        Route::resource('payments', 'PaymentController');


        // Settings
        //----------------------------------

        Route::group(['prefix' => 'settings'], function () {

            Route::get('/profile', [
                'as' => 'get.admin.profile',
                'uses' => 'CompanyController@getAdmin'
            ]);

            Route::put('/profile', [
                'as' => 'admin.profile',
                'uses' => 'CompanyController@updateAdminProfile'
            ]);

            Route::post('/profile/upload-avatar', [
                'as' => 'admin.profile.avatar',
                'uses' => 'CompanyController@uploadAdminAvatar'
            ]);

            Route::post('/company/upload-logo', [
                'as' => 'upload.admin.company.logo',
                'uses' => 'CompanyController@uploadCompanyLogo'
            ]);

            Route::get('/company', [
                'as' => 'get.admin.company',
                'uses' => 'CompanyController@getAdminCompany'
            ]);

            Route::post('/company', [
                'as' => 'admin.company',
                'uses' => 'CompanyController@updateAdminCompany'
            ]);

            Route::get('/general', [
                'as' => 'get.admin.company.setting',
                'uses' => 'CompanyController@getGeneralSettings'
            ]);

            Route::put('/general', [
                'as' => 'admin.company.setting',
                'uses' => 'CompanyController@updateGeneralSettings'
            ]);

            Route::get('/colors', [
                'as' => 'admin.colors.setting',
                'uses' => 'CompanyController@getColors'
            ]);

            Route::get('/get-setting', [
                'as' => 'get.admin.setting',
                'uses' => 'CompanyController@getSetting'
            ]);

            Route::put('/update-setting', [
                'as' => 'admin.update.setting',
                'uses' => 'CompanyController@updateSetting'
            ]);

            Route::get('/get-customize-setting', [
                'as' => 'admin.get.customize.setting',
                'uses' => 'CompanyController@getCustomizeSetting'
            ]);

            Route::put('/update-customize-setting', [
                'as' => 'admin.update.customize.setting',
                'uses' => 'CompanyController@updateCustomizeSetting'
            ]);

            Route::get('/environment/mail', [
                'as' => 'admin.environment.mail',
                'uses' => 'EnvironmentController@getMailDrivers'
            ]);

            Route::get('/environment/mail-env', [
                'as' => 'admin.mail.env',
                'uses' => 'EnvironmentController@getMailEnvironment'
            ]);

            Route::post('/environment/mail', [
                'as' => 'admin.environment.mail.save',
                'uses' => 'EnvironmentController@saveMailEnvironment'
            ]);

            Route::post('/test/mail', [
                'as' => 'admin.test.mail.config',
                'uses' => 'SettingsController@testEmailConfig'
            ]);

        });

    });

});
