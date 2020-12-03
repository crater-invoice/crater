import Vue from 'vue'
import VueRouter from 'vue-router'

/*
 |--------------------------------------------------------------------------
 | Views
 |--------------------------------------------------------------------------|
 */

// Layouts
import LayoutBasic from './views/layouts/LayoutBasic.vue'
import LayoutLogin from './views/layouts/LayoutLogin.vue'
import LayoutWizard from './views/layouts/LayoutWizard.vue'

// Auth
import Login from './views/auth/Login.vue'
import ForgotPassword from './views/auth/ForgotPassword.vue'
import ResetPassword from './views/auth/ResetPassword.vue'
import Register from './views/auth/Register.vue'

import NotFoundPage from './views/errors/404.vue'

// Dashbord
import Dashboard from './views/dashboard/Dashboard.vue'

// Customers
import CustomerIndex from './views/customers/Index.vue'
import CustomerCreate from './views/customers/Create.vue'
import CustomerView from './views/customers/View.vue'

// Items
import ItemsIndex from './views/items/Index.vue'
import ItemCreate from './views/items/Create.vue'

// Invoices
import InvoiceIndex from './views/invoices/Index.vue'
import InvoiceCreate from './views/invoices/Create.vue'
import InvoiceView from './views/invoices/View.vue'

// Payments
import PaymentsIndex from './views/payments/Index.vue'
import PaymentCreate from './views/payments/Create.vue'
import PaymentView from './views/payments/View.vue'

// Estimates
import EstimateIndex from './views/estimates/Index.vue'
import EstimateCreate from './views/estimates/Create.vue'
import EstimateView from './views/estimates/View.vue'

// Expenses
import ExpensesIndex from './views/expenses/Index'
import ExpenseCreate from './views/expenses/Create.vue'

//User
import UserIndex from './views/users/Index.vue'
import UserCreate from './views/users/Create.vue'

// Report
import SalesReports from './views/reports/SalesReports'
import ExpensesReport from './views/reports/ExpensesReport'
import ProfitLossReport from './views/reports/ProfitLossReport'
import TaxReport from './views/reports/TaxReport.vue'
import ReportLayout from './views/reports/layout/Index.vue'

// Settings
import SettingsLayout from './views/settings/SettingsIndex.vue'
import CompanyInfo from './views/settings/CompanyInfoSetting.vue'
import Customization from './views/settings/CustomizationSetting.vue'
import Notifications from './views/settings/NotificationsSetting.vue'
import Preferences from './views/settings/PreferencesSetting.vue'
import UserProfile from './views/settings/UserProfileSetting.vue'
import TaxTypes from './views/settings/TaxTypesSetting.vue'
import NotesSetting from './views/settings/NotesSetting.vue'
import ExpenseCategory from './views/settings/ExpenseCategorySetting.vue'
import MailConfig from './views/settings/MailConfigSetting.vue'
import UpdateApp from './views/settings/UpdateAppSetting.vue'
import Backup from './views/settings/BackupSetting.vue'
import FileDisk from './views/settings/FileDiskSetting.vue'
import CustomFieldsIndex from './views/settings/CustomFieldsSetting.vue'
import PaymentMode from './views/settings/PaymentsModeSetting.vue'
import Wizard from './views/wizard/Wizard.vue'

Vue.use(VueRouter)

const routes = [
  /*
   |--------------------------------------------------------------------------
   | Auth & Registration
   |--------------------------------------------------------------------------|
   */

  {
    path: '/',
    component: LayoutLogin,
    meta: { redirectIfAuthenticated: true },
    children: [
      {
        path: '/',
        component: Login,
      },
      {
        path: 'login',
        component: Login,
        name: 'login',
      },
      {
        path: '/forgot-password',
        component: ForgotPassword,
        name: 'forgot-password',
      },
      {
        path: '/reset-password/:token',
        component: ResetPassword,
        name: 'reset-password',
      },
      {
        path: 'register',
        component: Register,
        name: 'register',
      },
    ],
  },

  /*
   |--------------------------------------------------------------------------
   | Onboarding
   |--------------------------------------------------------------------------|
   */
  {
    path: '/on-boarding',
    component: LayoutWizard,
    children: [
      {
        path: '/',
        component: Wizard,
        name: 'wizard',
      },
    ],
  },

  /*
   |--------------------------------------------------------------------------
   | Admin
   |--------------------------------------------------------------------------|
   */
  {
    path: '/admin',
    component: LayoutBasic,
    meta: { requiresAuth: true },
    children: [
      // Dashboard
      {
        path: '/',
        component: Dashboard,
        name: 'dashboard',
      },
      {
        path: 'dashboard',
        component: Dashboard,
      },

      // Customers
      {
        path: 'customers',
        component: CustomerIndex,
      },
      {
        path: 'customers/create',
        name: 'customers.create',
        component: CustomerCreate,
      },
      {
        path: 'customers/:id/edit',
        name: 'customers.edit',
        component: CustomerCreate,
      },
      {
        path: 'customers/:id/view',
        name: 'customers.view',
        component: CustomerView,
      },

      // Items
      {
        path: 'items',
        component: ItemsIndex,
      },
      {
        path: 'items/create',
        name: 'items.create',
        component: ItemCreate,
      },
      {
        path: 'items/:id/edit',
        name: 'items.edit',
        component: ItemCreate,
      },

      // Estimates
      {
        path: 'estimates',
        name: 'estimates.index',
        component: EstimateIndex,
      },
      {
        path: 'estimates/create',
        name: 'estimates.create',
        component: EstimateCreate,
      },
      {
        path: 'estimates/:id/view',
        name: 'estimates.view',
        component: EstimateView,
      },
      {
        path: 'estimates/:id/edit',
        name: 'estimates.edit',
        component: EstimateCreate,
      },

      // Invoices
      {
        path: 'invoices',
        name: 'invoices.index',
        component: InvoiceIndex,
      },
      {
        path: 'invoices/create',
        name: 'invoices.create',
        component: InvoiceCreate,
      },
      {
        path: 'invoices/:id/view',
        name: 'invoices.view',
        component: InvoiceView,
      },
      {
        path: 'invoices/:id/edit',
        name: 'invoices.edit',
        component: InvoiceCreate,
      },

      // Payments
      {
        path: 'payments',
        name: 'payments.index',
        component: PaymentsIndex,
      },
      {
        path: 'payments/create',
        name: 'payments.create',
        component: PaymentCreate,
      },
      {
        path: 'payments/:id/create',
        name: 'invoice.payments.create',
        component: PaymentCreate,
      },
      {
        path: 'payments/:id/edit',
        name: 'payments.edit',
        component: PaymentCreate,
      },
      {
        path: 'payments/:id/view',
        name: 'payments.view',
        component: PaymentView,
      },

      // Expenses
      {
        path: 'expenses',
        component: ExpensesIndex,
      },
      {
        path: 'expenses/create',
        name: 'expenses.create',
        component: ExpenseCreate,
      },
      {
        path: 'expenses/:id/edit',
        name: 'expenses.edit',
        component: ExpenseCreate,
      },

      // User
      {
        path: 'users',
        component: UserIndex,
      },
      {
        path: 'users/create',
        name: 'users.create',
        component: UserCreate,
      },
      {
        path: 'users/:id/edit',
        name: 'users.edit',
        component: UserCreate,
      },

      // Reports
      {
        path: 'reports',
        component: ReportLayout,
        children: [
          {
            path: 'sales',
            component: SalesReports,
          },
          {
            path: 'expenses',
            component: ExpensesReport,
          },
          {
            path: 'profit-loss',
            component: ProfitLossReport,
          },
          {
            path: 'taxes',
            component: TaxReport,
          },
        ],
      },

      // Settings
      {
        path: 'settings',
        component: SettingsLayout,
        children: [
          {
            path: 'company-info',
            name: 'company.info',
            component: CompanyInfo,
          },
          {
            path: 'customization',
            name: 'customization',
            component: Customization,
          },
          {
            path: 'payment-mode',
            name: 'payment.mode',
            component: PaymentMode,
          },

          {
            path: 'custom-fields',
            name: 'custom.fields',
            component: CustomFieldsIndex,
          },
          {
            path: 'user-profile',
            name: 'user.profile',
            component: UserProfile,
          },
          {
            path: 'preferences',
            name: 'preferences',
            component: Preferences,
          },
          {
            path: 'tax-types',
            name: 'tax.types',
            component: TaxTypes,
          },
          {
            path: 'notes',
            name: 'notes',
            component: NotesSetting,
          },
          {
            path: 'expense-category',
            name: 'expense.category',
            component: ExpenseCategory,
          },
          {
            path: 'mail-configuration',
            name: 'mailconfig',
            component: MailConfig,
          },
          {
            path: 'notifications',
            name: 'notifications',
            component: Notifications,
          },
          {
            path: 'update-app',
            name: 'updateapp',
            component: UpdateApp,
          },
          {
            path: 'backup',
            name: 'backup',
            component: Backup,
          },
          {
            path: 'file-disk',
            name: 'file-disk',
            component: FileDisk,
          },
        ],
      },
    ],
  },

  //  DEFAULT ROUTE
  { path: '*', component: NotFoundPage },
]

const router = new VueRouter({
  routes,
  mode: 'history',
  linkActiveClass: 'active',
})

export default router
