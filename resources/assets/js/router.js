import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store/index.js'

/*
 |--------------------------------------------------------------------------
 | Admin Views
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

/*
 |--------------------------------------------------------------------------
 | Admin Views
 |--------------------------------------------------------------------------|
 */

// Dashbord
import Dashboard from './views/dashboard/Dashboard.vue'

// Customers
import CustomerIndex from './views/customers/Index.vue'
import CustomerCreate from './views/customers/Create.vue'

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

// Report
import SalesReports from './views/reports/SalesReports'
import ExpensesReport from './views/reports/ExpensesReport'
import ProfitLossReport from './views/reports/ProfitLossReport'
import TaxReport from './views/reports/TaxReport.vue'
import ReportLayout from './views/reports/layout/Index.vue'

// Settings
import SettingsLayout from './views/settings/layout/Index.vue'
import CompanyInfo from './views/settings/CompanyInfo.vue'
import Customization from './views/settings/Customization.vue'
import Notifications from './views/settings/Notifications.vue'
import Preferences from './views/settings/Preferences.vue'
import UserProfile from './views/settings/UserProfile.vue'
import TaxTypes from './views/settings/TaxTypes.vue'
import ExpenseCategory from './views/settings/ExpenseCategory.vue'
import MailConfig from './views/settings/MailConfig.vue'
import UpdateApp from './views/settings/UpdateApp.vue'

import Wizard from './views/wizard/Index.vue'

Vue.use(VueRouter)

const routes = [
  /*
   |--------------------------------------------------------------------------
   | Frontend Routes
   |--------------------------------------------------------------------------|
   */

  /*
   |--------------------------------------------------------------------------
   | Auth & Registration Routes
   |--------------------------------------------------------------------------|
   */

  {
    path: '/',
    component: LayoutLogin,
    meta: { redirectIfAuthenticated: true },
    children: [
      {
        path: '/',
        component: Login
      },
      {
        path: 'login',
        component: Login,
        name: 'login'
      },
      {
        path: '/forgot-password',
        component: ForgotPassword,
        name: 'forgot-password'
      },
      {
        path: '/reset-password/:token',
        component: ResetPassword,
        name: 'reset-password'
      },
      {
        path: 'register',
        component: Register,
        name: 'register'
      }
    ]
  },

  /*
   |--------------------------------------------------------------------------
   | Onboarding Routes
   |--------------------------------------------------------------------------|
   */
  {
    path: '/on-boarding',
    component: LayoutWizard,
    children: [
      {
        path: '/',
        component: Wizard,
        name: 'wizard'
      }
    ]
  },
  /*
   |--------------------------------------------------------------------------
   | Admin Backend Routes
   |--------------------------------------------------------------------------|
   */
  {
    path: '/admin',
    component: LayoutBasic, // Change the desired Layout here
    meta: { requiresAuth: true },
    children: [
      // Dashbord
      {
        path: '/',
        component: Dashboard,
        name: 'dashboard'
      },
      {
        path: 'dashboard',
        component: Dashboard
      },

      // Customer
      {
        path: 'customers',
        component: CustomerIndex
      },
      {
        path: 'customers/create',
        name: 'customers.create',
        component: CustomerCreate
      },
      {
        path: 'customers/:id/edit',
        name: 'customers.edit',
        component: CustomerCreate
      },

      // Items
      {
        path: 'items',
        component: ItemsIndex
      },
      {
        path: 'items/create',
        name: 'items.create',
        component: ItemCreate
      },
      {
        path: 'items/:id/edit',
        name: 'items.edit',
        component: ItemCreate
      },

      // Estimate
      {
        path: 'estimates',
        name: 'estimates.index',
        component: EstimateIndex
      },
      {
        path: 'estimates/create',
        name: 'estimates.create',
        component: EstimateCreate
      },
      {
        path: 'estimates/:id/view',
        name: 'estimates.view',
        component: EstimateView
      },
      {
        path: 'estimates/:id/edit',
        name: 'estimates.edit',
        component: EstimateCreate
      },

      // Invoice
      {
        path: 'invoices',
        name: 'invoices.index',
        component: InvoiceIndex
      },
      {
        path: 'invoices/create',
        name: 'invoices.create',
        component: InvoiceCreate
      },
      {
        path: 'invoices/:id/view',
        name: 'invoices.view',
        component: InvoiceView
      },
      {
        path: 'invoices/:id/edit',
        name: 'invoices.edit',
        component: InvoiceCreate
      },

      // Payments
      {
        path: 'payments',
        name: 'payments.index',
        component: PaymentsIndex
      },
      {
        path: 'payments/create',
        name: 'payments.create',
        component: PaymentCreate
      },
      {
        path: 'payments/:id/create',
        name: 'invoice.payments.create',
        component: PaymentCreate
      },

      {
        path: 'payments/:id/edit',
        name: 'payments.edit',
        component: PaymentCreate
      },
      {
        path: 'payments/:id/view',
        name: 'payments.view',
        component: PaymentView
      },

      // Expenses
      {
        path: 'expenses',
        component: ExpensesIndex
      },
      {
        path: 'expenses/create',
        name: 'expenses.create',
        component: ExpenseCreate
      },
      {
        path: 'expenses/:id/edit',
        name: 'expenses.edit',
        component: ExpenseCreate
      },

      // Reports
      {
        path: 'reports',
        component: ReportLayout,
        children: [
          {
            path: 'sales',
            component: SalesReports
          },
          {
            path: 'expenses',
            component: ExpensesReport
          },
          {
            path: 'profit-loss',
            component: ProfitLossReport
          },
          {
            path: 'taxes',
            component: TaxReport
          }
        ]
      },

      // Settings
      {
        path: 'settings',
        component: SettingsLayout,
        children: [
          {
            path: 'company-info',
            name: 'company.info',
            component: CompanyInfo
          },
          {
            path: 'customization',
            name: 'customization',
            component: Customization
          },
          {
            path: 'user-profile',
            name: 'user.profile',
            component: UserProfile
          },
          {
            path: 'preferences',
            name: 'preferences',
            component: Preferences
          },
          {
            path: 'tax-types',
            name: 'tax.types',
            component: TaxTypes
          },
          {
            path: 'expense-category',
            name: 'expense.category',
            component: ExpenseCategory
          },
          {
            path: 'mail-configuration',
            name: 'mailconfig',
            component: MailConfig
          },
          {
            path: 'notifications',
            name: 'notifications',
            component: Notifications
          },
          {
            path: 'update-app',
            name: 'updateapp',
            component: UpdateApp
          }
        ]
      }
    ]
  },

  //  DEFAULT ROUTE
  { path: '*', component: NotFoundPage }
]

const router = new VueRouter({
  routes,
  mode: 'history',
  linkActiveClass: 'active'
})

router.beforeEach((to, from, next) => {
  //  Redirect if not authenticated on secured routes
  if (to.matched.some(m => m.meta.requiresAuth)) {
    if (!store.getters['auth/isAuthenticated']) {
      return next('/login')
    }
  }

  if (to.matched.some(m => m.meta.redirectIfAuthenticated) && store.getters['auth/isAuthenticated']) {
    return next('/admin/dashboard')
  }

  return next()
})

export default router
