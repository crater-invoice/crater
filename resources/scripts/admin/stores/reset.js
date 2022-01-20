import axios from 'axios'
import { defineStore } from 'pinia'
import { useBackupStore } from './backup'
import { useCategoryStore } from './category'
import { useCompanyStore } from './company'
import { useCustomFieldStore } from './custom-field'
import { useCustomerStore } from './customer'
import { useDashboardStore } from './dashboard'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useDiskStore } from './disk'
import { useEstimateStore } from './estimate'
import { useExchangeRateStore } from './exchange-rate'
import { useExpenseStore } from './expense'
import { useGlobalStore } from './global'
import { useInstallationStore } from './installation'
import { useInvoiceStore } from './invoice'
import { useItemStore } from './item'
import { useMailDriverStore } from './mail-driver'
import { useModalStore } from '@/scripts/stores/modal'
import { useNotesStore } from './note'
import { useNotificationStore } from '@/scripts/stores/notification'
import { usePaymentStore } from './payment'
import { useRecurringInvoiceStore } from './recurring-invoice'
import { useRoleStore } from './role'
import { useTaxTypeStore } from './tax-type'
import { useUserStore } from './user'
import { useUsersStore } from './users'

export const useResetStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'reset',
    actions: {
      clearPinia() {
        const backupStore = useBackupStore()
        const categoryStore = useCategoryStore()
        const companyStore = useCompanyStore()
        const customFieldStore = useCustomFieldStore()
        const customerStore = useCustomerStore()
        const dashboardStore = useDashboardStore()
        const dialogStore = useDialogStore()
        const diskStore = useDiskStore()
        const estimateStore = useEstimateStore()
        const exchangeRateStore = useExchangeRateStore()
        const expenseStore = useExpenseStore()
        const globalStore = useGlobalStore()
        const installationStore = useInstallationStore()
        const invoiceStore = useInvoiceStore()
        const itemStore = useItemStore()
        const mailDriverStore = useMailDriverStore()
        const modalStore = useModalStore()
        const noteStore = useNotesStore()
        const notificationStore = useNotificationStore()
        const paymentStore = usePaymentStore()
        const recurringInvoiceStore = useRecurringInvoiceStore()
        const roleStore = useRoleStore()
        const taxTypeStore = useTaxTypeStore()
        const userStore = useUserStore()
        const usersStore = useUsersStore()

        backupStore.$reset()
        categoryStore.$reset()
        companyStore.$reset()
        customFieldStore.$reset()
        customerStore.$reset()
        dashboardStore.$reset()
        dialogStore.$reset()
        diskStore.$reset()
        estimateStore.$reset()
        exchangeRateStore.$reset()
        expenseStore.$reset()
        globalStore.$reset()
        installationStore.$reset()
        invoiceStore.$reset()
        itemStore.$reset()
        mailDriverStore.$reset()
        modalStore.$reset()
        noteStore.$reset()
        notificationStore.$reset()
        paymentStore.$reset()
        recurringInvoiceStore.$reset()
        roleStore.$reset()
        taxTypeStore.$reset()
        userStore.$reset()
        usersStore.$reset()
      },
    },
  })()
}
