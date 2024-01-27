<?php

namespace InvoiceShelf\Providers;

use InvoiceShelf\Policies\CompanyPolicy;
use InvoiceShelf\Policies\CustomerPolicy;
use InvoiceShelf\Policies\DashboardPolicy;
use InvoiceShelf\Policies\EstimatePolicy;
use InvoiceShelf\Policies\ExpensePolicy;
use InvoiceShelf\Policies\InvoicePolicy;
use InvoiceShelf\Policies\ItemPolicy;
use InvoiceShelf\Policies\ModulesPolicy;
use InvoiceShelf\Policies\NotePolicy;
use InvoiceShelf\Policies\OwnerPolicy;
use InvoiceShelf\Policies\PaymentPolicy;
use InvoiceShelf\Policies\RecurringInvoicePolicy;
use InvoiceShelf\Policies\ReportPolicy;
use InvoiceShelf\Policies\SettingsPolicy;
use InvoiceShelf\Policies\UserPolicy;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \InvoiceShelf\Models\Customer::class => \InvoiceShelf\Policies\CustomerPolicy::class,
        \InvoiceShelf\Models\Invoice::class => \InvoiceShelf\Policies\InvoicePolicy::class,
        \InvoiceShelf\Models\Estimate::class => \InvoiceShelf\Policies\EstimatePolicy::class,
        \InvoiceShelf\Models\Payment::class => \InvoiceShelf\Policies\PaymentPolicy::class,
        \InvoiceShelf\Models\Expense::class => \InvoiceShelf\Policies\ExpensePolicy::class,
        \InvoiceShelf\Models\ExpenseCategory::class => \InvoiceShelf\Policies\ExpenseCategoryPolicy::class,
        \InvoiceShelf\Models\PaymentMethod::class => \InvoiceShelf\Policies\PaymentMethodPolicy::class,
        \InvoiceShelf\Models\TaxType::class => \InvoiceShelf\Policies\TaxTypePolicy::class,
        \InvoiceShelf\Models\CustomField::class => \InvoiceShelf\Policies\CustomFieldPolicy::class,
        \InvoiceShelf\Models\User::class => \InvoiceShelf\Policies\UserPolicy::class,
        \InvoiceShelf\Models\Item::class => \InvoiceShelf\Policies\ItemPolicy::class,
        \Silber\Bouncer\Database\Role::class => \InvoiceShelf\Policies\RolePolicy::class,
        \InvoiceShelf\Models\Unit::class => \InvoiceShelf\Policies\UnitPolicy::class,
        \InvoiceShelf\Models\RecurringInvoice::class => \InvoiceShelf\Policies\RecurringInvoicePolicy::class,
        \InvoiceShelf\Models\ExchangeRateProvider::class => \InvoiceShelf\Policies\ExchangeRateProviderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create company', [CompanyPolicy::class, 'create']);
        Gate::define('transfer company ownership', [CompanyPolicy::class, 'transferOwnership']);
        Gate::define('delete company', [CompanyPolicy::class, 'delete']);

        Gate::define('manage modules', [ModulesPolicy::class, 'manageModules']);

        Gate::define('manage settings', [SettingsPolicy::class, 'manageSettings']);
        Gate::define('manage company', [SettingsPolicy::class, 'manageCompany']);
        Gate::define('manage backups', [SettingsPolicy::class, 'manageBackups']);
        Gate::define('manage file disk', [SettingsPolicy::class, 'manageFileDisk']);
        Gate::define('manage email config', [SettingsPolicy::class, 'manageEmailConfig']);
        Gate::define('manage notes', [NotePolicy::class, 'manageNotes']);
        Gate::define('view notes', [NotePolicy::class, 'viewNotes']);

        Gate::define('send invoice', [InvoicePolicy::class, 'send']);
        Gate::define('send estimate', [EstimatePolicy::class, 'send']);
        Gate::define('send payment', [PaymentPolicy::class, 'send']);

        Gate::define('delete multiple items', [ItemPolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple customers', [CustomerPolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple users', [UserPolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple invoices', [InvoicePolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple estimates', [EstimatePolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple expenses', [ExpensePolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple payments', [PaymentPolicy::class, 'deleteMultiple']);
        Gate::define('delete multiple recurring invoices', [RecurringInvoicePolicy::class, 'deleteMultiple']);

        Gate::define('view dashboard', [DashboardPolicy::class, 'view']);

        Gate::define('view report', [ReportPolicy::class, 'viewReport']);

        Gate::define('owner only', [OwnerPolicy::class, 'managedByOwner']);
    }
}
