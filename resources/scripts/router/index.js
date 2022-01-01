import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/scripts/stores/user'
import { useGlobalStore } from '@/scripts/stores/global'
import LayoutBasic from '@/scripts/layouts/LayoutBasic.vue'
import LayoutLogin from '@/scripts/layouts/LayoutLogin.vue'
import abilities from '@/scripts/stub/abilities'

const LayoutInstallation = () =>
  import('@/scripts/layouts/LayoutInstallation.vue')

const Login = () => import('@/scripts/views/auth/Login.vue')
const ResetPassword = () => import('@/scripts/views/auth/ResetPassword.vue')
const ForgotPassword = () => import('@/scripts/views/auth/ForgotPassword.vue')

// Dashboard
const Dashboard = () => import('@/scripts/views/dashboard/Dashboard.vue')

// Customers
const CustomerIndex = () => import('@/scripts/views/customers/Index.vue')
const CustomerCreate = () => import('@/scripts/views/customers/Create.vue')
const CustomerView = () => import('@/scripts/views/customers/View.vue')

//Settings
const SettingsIndex = () => import('@/scripts/views/settings/SettingsIndex.vue')
const AccountSetting = () =>
  import('@/scripts/views/settings/AccountSetting.vue')
const CompanyInfo = () =>
  import('@/scripts/views/settings/CompanyInfoSettings.vue')
const Preferences = () =>
  import('@/scripts/views/settings/PreferencesSetting.vue')
const Customization = () =>
  import('@/scripts/views/settings/customization/CustomizationSetting.vue')
const Notifications = () =>
  import('@/scripts/views/settings/NotificationsSetting.vue')
const TaxTypes = () => import('@/scripts/views/settings/TaxTypesSetting.vue')
const PaymentMode = () =>
  import('@/scripts/views/settings/PaymentsModeSetting.vue')
const CustomFieldsIndex = () =>
  import('@/scripts/views/settings/CustomFieldsSetting.vue')
const NotesSetting = () => import('@/scripts/views/settings/NotesSetting.vue')
const ExpenseCategory = () =>
  import('@/scripts/views/settings/ExpenseCategorySetting.vue')
const ExchangeRateSetting = () =>
  import('@/scripts/views/settings/ExchangeRateProviderSetting.vue')
const MailConfig = () =>
  import('@/scripts/views/settings/MailConfigSetting.vue')
const FileDisk = () => import('@/scripts/views/settings/FileDiskSetting.vue')
const Backup = () => import('@/scripts/views/settings/BackupSetting.vue')
const UpdateApp = () => import('@/scripts/views/settings/UpdateAppSetting.vue')
const RolesSettings = () => import('@/scripts/views/settings/RolesSettings.vue')

// Items
const ItemsIndex = () => import('@/scripts/views/items/Index.vue')
const ItemCreate = () => import('@/scripts/views/items/Create.vue')

// Expenses
const ExpensesIndex = () => import('@/scripts/views/expenses/Index.vue')
const ExpenseCreate = () => import('@/scripts/views/expenses/Create.vue')

// Users
const UserIndex = () => import('@/scripts/views/users/Index.vue')
const UserCreate = () => import('@/scripts/views/users/Create.vue')

// Estimates
const EstimateIndex = () => import('@/scripts/views/estimates/Index.vue')
const EstimateCreate = () =>
  import('@/scripts/views/estimates/create/EstimateCreate.vue')
const EstimateView = () => import('@/scripts/views/estimates/View.vue')

// Payments
const PaymentsIndex = () => import('@/scripts/views/payments/Index.vue')
const PaymentCreate = () => import('@/scripts/views/payments/Create.vue')
const PaymentView = () => import('@/scripts/views/payments/View.vue')

const NotFoundPage = () => import('@/scripts/views/errors/404.vue')

// Invoice
const InvoiceIndex = () => import('@/scripts/views/invoices/Index.vue')
const InvoiceCreate = () =>
  import('@/scripts/views/invoices/create/InvoiceCreate.vue')
const InvoiceView = () => import('@/scripts/views/invoices/View.vue')

// Recurring Invoice
const RecurringInvoiceIndex = () =>
  import('@/scripts/views/recurring-invoices/Index.vue')
const RecurringInvoiceCreate = () =>
  import('@/scripts/views/recurring-invoices/create/RecurringInvoiceCreate.vue')
const RecurringInvoiceView = () =>
  import('@/scripts/views/recurring-invoices/View.vue')

// Reports
const ReportsIndex = () => import('@/scripts/views/reports/layout/Index.vue')

// Installation
const Installation = () =>
  import('@/scripts/views/installation/Installation.vue')

let routes = [
  {
    path: '/installation',
    component: LayoutInstallation,
    meta: { requiresAuth: false },
    children: [
      {
        path: '/installation',
        component: Installation,
        name: 'installation',
      },
    ],
  },
  {
    path: '/',
    component: LayoutLogin,
    meta: { requiresAuth: false, redirectIfAuthenticated: true },
    children: [
      {
        path: '/',
        component: Login,
      },
      {
        path: 'login',
        name: 'login',
        component: Login,
      },
      {
        path: 'forgot-password',
        component: ForgotPassword,
        name: 'forgot-password',
      },
      {
        path: '/reset-password/:token',
        component: ResetPassword,
        name: 'reset-password',
      },
    ],
  },
  {
    path: '/admin',
    component: LayoutBasic,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        meta: { ability: abilities.DASHBOARD },
        component: Dashboard,
      },

      // Customers
      {
        path: 'customers',
        meta: { ability: abilities.VIEW_CUSTOMER },
        component: CustomerIndex,
      },
      {
        path: 'customers/create',
        name: 'customers.create',
        meta: { ability: abilities.CREATE_CUSTOMER },
        component: CustomerCreate,
      },
      {
        path: 'customers/:id/edit',
        name: 'customers.edit',
        meta: { ability: abilities.EDIT_CUSTOMER },
        component: CustomerCreate,
      },
      {
        path: 'customers/:id/view',
        name: 'customers.view',
        meta: { ability: abilities.VIEW_CUSTOMER },
        component: CustomerView,
      },
      // Payments
      {
        path: 'payments',
        meta: { ability: abilities.VIEW_PAYMENT },
        component: PaymentsIndex,
      },
      {
        path: 'payments/create',
        name: 'payments.create',
        meta: { ability: abilities.CREATE_PAYMENT },
        component: PaymentCreate,
      },
      {
        path: 'payments/:id/create',
        name: 'invoice.payments.create',
        meta: { ability: abilities.CREATE_PAYMENT },
        component: PaymentCreate,
      },
      {
        path: 'payments/:id/edit',
        name: 'payments.edit',
        meta: { ability: abilities.EDIT_PAYMENT },
        component: PaymentCreate,
      },
      {
        path: 'payments/:id/view',
        name: 'payments.view',
        meta: { ability: abilities.VIEW_PAYMENT },
        component: PaymentView,
      },

      //settings
      {
        path: 'settings',
        name: 'settings',
        component: SettingsIndex,
        children: [
          {
            path: 'account-settings',
            name: 'account.settings',
            component: AccountSetting,
          },
          {
            path: 'company-info',
            name: 'company.info',
            meta: { isOwner: true },
            component: CompanyInfo,
          },
          {
            path: 'preferences',
            name: 'preferences',
            meta: { isOwner: true },
            component: Preferences,
          },
          {
            path: 'customization',
            name: 'customization',
            meta: { isOwner: true },
            component: Customization,
          },
          {
            path: 'notifications',
            name: 'notifications',
            meta: { isOwner: true },
            component: Notifications,
          },
          {
            path: 'roles-settings',
            name: 'roles.settings',
            meta: { isOwner: true },
            component: RolesSettings,
          },
          {
            path: 'exchange-rate-provider',
            name: 'exchange.rate.provider',
            meta: { ability: abilities.VIEW_EXCHANGE_RATE },
            component: ExchangeRateSetting,
          },
          {
            path: 'tax-types',
            name: 'tax.types',
            meta: { ability: abilities.VIEW_TAX_TYPE },
            component: TaxTypes,
          },
          {
            path: 'notes',
            name: 'notes',
            meta: { ability: abilities.VIEW_ALL_NOTES },
            component: NotesSetting,
          },
          {
            path: 'payment-mode',
            name: 'payment.mode',
            component: PaymentMode,
          },
          {
            path: 'custom-fields',
            name: 'custom.fields',
            meta: { ability: abilities.VIEW_CUSTOM_FIELDS },
            component: CustomFieldsIndex,
          },
          {
            path: 'expense-category',
            name: 'expense.category',
            meta: { ability: abilities.VIEW_EXPENSE },
            component: ExpenseCategory,
          },

          {
            path: 'mail-configuration',
            name: 'mailconfig',
            meta: { isOwner: true },
            component: MailConfig,
          },
          {
            path: 'file-disk',
            name: 'file-disk',
            meta: { isOwner: true },
            component: FileDisk,
          },
          {
            path: 'backup',
            name: 'backup',
            meta: { isOwner: true },
            component: Backup,
          },
          {
            path: 'update-app',
            name: 'updateapp',
            meta: { isOwner: true },
            component: UpdateApp,
          },
        ],
      },

      // Items
      {
        path: 'items',
        meta: { ability: abilities.VIEW_ITEM },
        component: ItemsIndex,
      },
      {
        path: 'items/create',
        name: 'items.create',
        meta: { ability: abilities.CREATE_ITEM },
        component: ItemCreate,
      },
      {
        path: 'items/:id/edit',
        name: 'items.edit',
        meta: { ability: abilities.EDIT_ITEM },
        component: ItemCreate,
      },

      // Expenses
      {
        path: 'expenses',
        meta: { ability: abilities.VIEW_EXPENSE },
        component: ExpensesIndex,
      },
      {
        path: 'expenses/create',
        name: 'expenses.create',
        meta: { ability: abilities.CREATE_EXPENSE },
        component: ExpenseCreate,
      },
      {
        path: 'expenses/:id/edit',
        name: 'expenses.edit',
        meta: { ability: abilities.EDIT_EXPENSE },
        component: ExpenseCreate,
      },

      // Users
      {
        path: 'users',
        name: 'users.index',
        meta: { isOwner: true },
        component: UserIndex,
      },
      {
        path: 'users/create',
        meta: { isOwner: true },
        name: 'users.create',
        component: UserCreate,
      },
      {
        path: 'users/:id/edit',
        name: 'users.edit',
        meta: { isOwner: true },
        component: UserCreate,
      },

      // Estimates
      {
        path: 'estimates',
        name: 'estimates.index',
        meta: { ability: abilities.VIEW_ESTIMATE },
        component: EstimateIndex,
      },
      {
        path: 'estimates/create',
        name: 'estimates.create',
        meta: { ability: abilities.CREATE_ESTIMATE },
        component: EstimateCreate,
      },
      {
        path: 'estimates/:id/view',
        name: 'estimates.view',
        meta: { ability: abilities.VIEW_ESTIMATE },
        component: EstimateView,
      },
      {
        path: 'estimates/:id/edit',
        name: 'estimates.edit',
        meta: { ability: abilities.EDIT_ESTIMATE },
        component: EstimateCreate,
      },

      // Invoices
      {
        path: 'invoices',
        name: 'invoices.index',
        meta: { ability: abilities.VIEW_INVOICE },
        component: InvoiceIndex,
      },
      {
        path: 'invoices/create',
        name: 'invoices.create',
        meta: { ability: abilities.CREATE_INVOICE },
        component: InvoiceCreate,
      },
      {
        path: 'invoices/:id/view',
        name: 'invoices.view',
        meta: { ability: abilities.VIEW_INVOICE },
        component: InvoiceView,
      },
      {
        path: 'invoices/:id/edit',
        name: 'invoices.edit',
        meta: { ability: abilities.EDIT_INVOICE },
        component: InvoiceCreate,
      },

      // Recurring Invoices
      {
        path: 'recurring-invoices',
        name: 'recurring-invoices.index',
        meta: { ability: abilities.VIEW_RECURRING_INVOICE },
        component: RecurringInvoiceIndex,
      },
      {
        path: 'recurring-invoices/create',
        name: 'recurring-invoices.create',
        meta: { ability: abilities.CREATE_RECURRING_INVOICE },
        component: RecurringInvoiceCreate,
      },
      {
        path: 'recurring-invoices/:id/view',
        name: 'recurring-invoices.view',
        meta: { ability: abilities.VIEW_RECURRING_INVOICE },
        component: RecurringInvoiceView,
      },
      {
        path: 'recurring-invoices/:id/edit',
        name: 'recurring-invoices.edit',
        meta: { ability: abilities.EDIT_RECURRING_INVOICE },
        component: RecurringInvoiceCreate,
      },

      // Reports
      {
        path: 'reports',
        meta: { ability: abilities.VIEW_FINANCIAL_REPORT },
        component: ReportsIndex,
      },
    ],
  },
  { path: '/:catchAll(.*)', component: NotFoundPage },
]

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: 'active',
  routes,
})

router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  const globalStore = useGlobalStore()
  let ability = to.meta.ability
  const { isAppLoaded } = globalStore

  if (ability && isAppLoaded && to.meta.requiresAuth) {
    if (userStore.hasAbilities(ability)) {
      next()
    } else next({ name: 'account.settings' })
  } else if (to.meta.isOwner && isAppLoaded) {
    if (userStore.currentUser.is_owner) {
      next()
    } else next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
