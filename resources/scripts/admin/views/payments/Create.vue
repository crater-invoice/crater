<template>
  <PaymentModeModal />

  <BasePage class="relative payment-create">
    <form action="" @submit.prevent="submitPaymentData">
      <BasePageHeader :title="pageTitle" class="mb-5">
        <BaseBreadcrumb>
          <BaseBreadcrumbItem
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <BaseBreadcrumbItem
            :title="$tc('payments.payment', 2)"
            to="/admin/payments"
          />
          <BaseBreadcrumbItem :title="pageTitle" to="#" active />
        </BaseBreadcrumb>

        <template #actions>
          <BaseButton
            :loading="isSaving"
            :disabled="isSaving"
            variant="primary"
            type="submit"
            class="hidden sm:flex"
          >
            <template #left="slotProps">
              <BaseIcon
                v-if="!isSaving"
                name="SaveIcon"
                :class="slotProps.class"
              />
            </template>
            {{
              isEdit
                ? $t('payments.update_payment')
                : $t('payments.save_payment')
            }}
          </BaseButton>
        </template>
      </BasePageHeader>

      <BaseCard>
        <BaseInputGrid>
          <BaseInputGroup
            :label="$t('payments.date')"
            :content-loading="isLoadingContent"
            required
            :error="
              v$.currentPayment.payment_date.$error &&
              v$.currentPayment.payment_date.$errors[0].$message
            "
          >
            <BaseDatePicker
              v-model="paymentStore.currentPayment.payment_date"
              :content-loading="isLoadingContent"
              :calendar-button="true"
              calendar-button-icon="calendar"
              :invalid="v$.currentPayment.payment_date.$error"
              @update:modelValue="v$.currentPayment.payment_date.$touch()"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('payments.payment_number')"
            :content-loading="isLoadingContent"
            required
          >
            <BaseInput
              v-model="paymentStore.currentPayment.payment_number"
              :content-loading="isLoadingContent"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('payments.customer')"
            :error="
              v$.currentPayment.customer_id.$error &&
              v$.currentPayment.customer_id.$errors[0].$message
            "
            :content-loading="isLoadingContent"
            required
          >
            <BaseCustomerSelectInput
              v-model="paymentStore.currentPayment.customer_id"
              :content-loading="isLoadingContent"
              v-if="!isLoadingContent"
              :invalid="v$.currentPayment.customer_id.$error"
              :placeholder="$t('customers.select_a_customer')"
              show-action
              @update:modelValue="
                selectNewCustomer(paymentStore.currentPayment.customer_id)
              "
            />
          </BaseInputGroup>

          <BaseInputGroup
            :content-loading="isLoadingContent"
            :label="$t('payments.invoice')"
            :help-text="
              selectedInvoice
                ? `Due Amount: ${
                    paymentStore.currentPayment.maxPayableAmount / 100
                  }`
                : ''
            "
          >
            <BaseMultiselect
              v-model="paymentStore.currentPayment.invoice_id"
              :content-loading="isLoadingContent"
              value-prop="id"
              track-by="invoice_number"
              label="invoice_number"
              :options="invoiceList"
              :loading="isLoadingInvoices"
              :placeholder="$t('invoices.select_invoice')"
              @select="onSelectInvoice"
            >
              <template #singlelabel="{ value }">
                <div class="absolute left-3.5">
                  {{ value.invoice_number }} ({{
                    utils.formatMoney(value.total, value.customer.currency)
                  }})
                </div>
              </template>

              <template #option="{ option }">
                {{ option.invoice_number }} ({{
                  utils.formatMoney(option.total, option.customer.currency)
                }})
              </template>
            </BaseMultiselect>
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('payments.amount')"
            :content-loading="isLoadingContent"
            :error="
              v$.currentPayment.amount.$error &&
              v$.currentPayment.amount.$errors[0].$message
            "
            required
          >
            <div class="relative w-full">
              <BaseMoney
                :key="paymentStore.currentPayment.currency"
                v-model="amount"
                :currency="paymentStore.currentPayment.currency"
                :content-loading="isLoadingContent"
                :invalid="v$.currentPayment.amount.$error"
                @update:modelValue="v$.currentPayment.amount.$touch()"
              />
            </div>
          </BaseInputGroup>

          <BaseInputGroup
            :content-loading="isLoadingContent"
            :label="$t('payments.payment_mode')"
          >
            <BaseMultiselect
              v-model="paymentStore.currentPayment.payment_method_id"
              :content-loading="isLoadingContent"
              label="name"
              value-prop="id"
              track-by="name"
              :options="paymentStore.paymentModes"
              :placeholder="$t('payments.select_payment_mode')"
              searchable
            >
              <template #action>
                <BaseSelectAction @click="addPaymentMode">
                  <BaseIcon
                    name="PlusIcon"
                    class="h-4 mr-2 -ml-2 text-center text-primary-400"
                  />
                  {{ $t('settings.payment_modes.add_payment_mode') }}
                </BaseSelectAction>
              </template>
            </BaseMultiselect>
          </BaseInputGroup>

          <ExchangeRateConverter
            :store="paymentStore"
            store-prop="currentPayment"
            :v="v$.currentPayment"
            :is-loading="isLoadingContent"
            :is-edit="isEdit"
            :customer-currency="paymentStore.currentPayment.currency_id"
          />
        </BaseInputGrid>

        <!-- Payment Custom Fields -->
        <PaymentCustomFields
          type="Payment"
          :is-edit="isEdit"
          :is-loading="isLoadingContent"
          :store="paymentStore"
          store-prop="currentPayment"
          :custom-field-scope="paymentValidationScope"
          class="mt-6"
        />

        <!-- Payment Note field -->
        <div class="relative mt-6">
          <div
            class="
              z-20
              float-right
              text-sm
              font-semibold
              leading-5
              text-primary-400
            "
          >
            <SelectNotePopup type="Payment" @select="onSelectNote" />
          </div>

          <label class="mb-4 text-sm font-medium text-gray-800">
            {{ $t('estimates.notes') }}
          </label>

          <BaseCustomInput
            v-model="paymentStore.currentPayment.notes"
            :content-loading="isLoadingContent"
            :fields="PaymentFields"
            class="mt-1"
          />
        </div>

        <BaseButton
          :loading="isSaving"
          :content-loading="isLoadingContent"
          variant="primary"
          type="submit"
          class="flex justify-center w-full mt-4 sm:hidden md:hidden"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{
            isEdit ? $t('payments.update_payment') : $t('payments.save_payment')
          }}
        </BaseButton>
      </BaseCard>
    </form>
  </BasePage>
</template>

<script setup>
import ExchangeRateConverter from '@/scripts/admin/components/estimate-invoice-common/ExchangeRateConverter.vue'

import {
  ref,
  reactive,
  computed,
  inject,
  watchEffect,
  onBeforeUnmount,
} from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import {
  required,
  numeric,
  helpers,
  between,
  requiredIf,
  decimal,
} from '@vuelidate/validators'

import useVuelidate from '@vuelidate/core'
import { useCustomerStore } from '@/scripts/admin/stores/customer'
import { usePaymentStore } from '@/scripts/admin/stores/payment'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useCustomFieldStore } from '@/scripts/admin/stores/custom-field'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useModalStore } from '@/scripts/stores/modal'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import { useGlobalStore } from '@/scripts/admin/stores/global'

import SelectNotePopup from '@/scripts/admin/components/SelectNotePopup.vue'
import PaymentCustomFields from '@/scripts/admin/components/custom-fields/CreateCustomFields.vue'
import PaymentModeModal from '@/scripts/admin/components/modal-components/PaymentModeModal.vue'

const route = useRoute()
const router = useRouter()

const paymentStore = usePaymentStore()
const notificationStore = useNotificationStore()
const customerStore = useCustomerStore()
const customFieldStore = useCustomFieldStore()
const companyStore = useCompanyStore()
const modalStore = useModalStore()
const invoiceStore = useInvoiceStore()
const globalStore = useGlobalStore()

const utils = inject('utils')
const { t } = useI18n()

let isSaving = ref(false)
let isLoadingInvoices = ref(false)
let invoiceList = ref([])
const selectedInvoice = ref(null)

const paymentValidationScope = 'newEstimate'

const PaymentFields = reactive([
  'customer',
  'company',
  'customerCustom',
  'payment',
  'paymentCustom',
])

const amount = computed({
  get: () => paymentStore.currentPayment.amount / 100,
  set: (value) => {
    paymentStore.currentPayment.amount = Math.round(value * 100)
  },
})

const isLoadingContent = computed(() => paymentStore.isFetchingInitialData)

const isEdit = computed(() => route.name === 'payments.edit')

const pageTitle = computed(() => {
  if (isEdit.value) {
    return t('payments.edit_payment')
  }
  return t('payments.new_payment')
})

const rules = computed(() => {
  return {
    currentPayment: {
      customer_id: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      payment_date: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      amount: {
        required: helpers.withMessage(t('validation.required'), required),
        between: helpers.withMessage(
          t('validation.payment_greater_than_due_amount'),
          between(0, paymentStore.currentPayment.maxPayableAmount)
        ),
      },
      exchange_rate: {
        required: requiredIf(function () {
          helpers.withMessage(t('validation.required'), required)
          return paymentStore.showExchangeRate
        }),
        decimal: helpers.withMessage(
          t('validation.valid_exchange_rate'),
          decimal
        ),
      },
    },
  }
})

const v$ = useVuelidate(rules, paymentStore, {
  $scope: paymentValidationScope,
})

watchEffect(() => {
  // fetch customer and its invoices
  paymentStore.currentPayment.customer_id
    ? onCustomerChange(paymentStore.currentPayment.customer_id)
    : ''
  if (route.query.customer) {
    paymentStore.currentPayment.customer_id = route.query.customer
  }
})

// Reset State on Create
paymentStore.resetCurrentPayment()

if (route.query.customer) {
  paymentStore.currentPayment.customer_id = route.query.customer
}

paymentStore.fetchPaymentInitialData(isEdit.value)

if (route.params.id && !isEdit.value) {
  setInvoiceFromUrl()
}

async function addPaymentMode() {
  modalStore.openModal({
    title: t('settings.payment_modes.add_payment_mode'),
    componentName: 'PaymentModeModal',
  })
}

function onSelectNote(data) {
  paymentStore.currentPayment.notes = '' + data.notes
}

async function setInvoiceFromUrl() {
  let res = await invoiceStore.fetchInvoice(route?.params?.id)

  paymentStore.currentPayment.customer_id = res.data.data.customer.id
  paymentStore.currentPayment.invoice_id = res.data.data.id
}

async function onSelectInvoice(id) {
  if (id) {
    selectedInvoice.value = invoiceList.value.find((inv) => inv.id === id)

    amount.value = selectedInvoice.value.due_amount / 100
    paymentStore.currentPayment.maxPayableAmount =
      selectedInvoice.value.due_amount
  }
}

function onCustomerChange(customer_id) {
  if (customer_id) {
    let data = {
      customer_id: customer_id,
      status: 'DUE',
      limit: 'all',
    }

    if (isEdit.value) {
      data.status = ''
    }

    isLoadingInvoices.value = true

    Promise.all([
      invoiceStore.fetchInvoices(data),
      customerStore.fetchCustomer(customer_id),
    ])
      .then(async ([res1, res2]) => {
        if (res1) {
          invoiceList.value = [...res1.data.data]
        }

        if (res2 && res2.data) {
          paymentStore.currentPayment.selectedCustomer = res2.data.data
          paymentStore.currentPayment.customer = res2.data.data
          paymentStore.currentPayment.currency = res2.data.data.currency
          if(isEdit.value && !customerStore.editCustomer && paymentStore.currentPayment.customer_id) {
            customerStore.editCustomer = res2.data.data
          }
        }

        if (paymentStore.currentPayment.invoice_id) {
          selectedInvoice.value = invoiceList.value.find(
            (inv) => inv.id === paymentStore.currentPayment.invoice_id
          )

          paymentStore.currentPayment.maxPayableAmount =
            selectedInvoice.value.due_amount +
            paymentStore.currentPayment.amount

          if (amount.value === 0) {
            amount.value = selectedInvoice.value.due_amount / 100
          }
        }

        if (isEdit.value) {
          // remove all invoices that are paid except currently selected invoice
          invoiceList.value = invoiceList.value.filter((v) => {
            return (
              v.due_amount > 0 || v.id == paymentStore.currentPayment.invoice_id
            )
          })
        }

        isLoadingInvoices.value = false
      })
      .catch((error) => {
        isLoadingInvoices.value = false
        console.error(error, 'error')
      })
  }
}
onBeforeUnmount(() => {
  paymentStore.resetCurrentPayment()
  invoiceList.value = []
  customerStore.editCustomer = null
})

async function submitPaymentData() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return false
  }

  isSaving.value = true

  let data = {
    ...paymentStore.currentPayment,
  }

  let response = null

  try {
    const action = isEdit.value
      ? paymentStore.updatePayment
      : paymentStore.addPayment

    response = await action(data)

    router.push(`/admin/payments/${response.data.data.id}/view`)
  } catch (err) {
    isSaving.value = false
  }
}

function selectNewCustomer(id) {
  let params = {
    userId: id,
  }

  if (route.params.id) params.model_id = route.params.id

  paymentStore.currentPayment.invoice_id = selectedInvoice.value = null
  paymentStore.currentPayment.amount = 0
  invoiceList.value = []
  paymentStore.getNextNumber(params, true)
}
</script>
