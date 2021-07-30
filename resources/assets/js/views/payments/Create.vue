<template>
  <base-page class="relative payment-create">
    <form action="" @submit.prevent="submitPaymentData">
      <sw-page-header :title="pageTitle" class="mb-5">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('payments.payment', 2)"
            to="/admin/payments"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'payments.edit'"
            :title="$t('payments.edit_payment')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('payments.new_payment')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden sm:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('payments.update_payment')
                : $t('payments.save_payment')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

      <sw-card v-else>
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <sw-input-group
            :label="$t('payments.date')"
            :error="DateError"
            required
          >
            <base-date-picker
              v-model="formData.payment_date"
              :invalid="$v.formData.payment_date.$error"
              :calendar-button="true"
              class="mt-1"
              calendar-button-icon="calendar"
              @input="$v.formData.payment_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payments.payment_number')"
            :error="paymentNumError"
            required
          >
            <sw-input
              :prefix="`${paymentPrefix} - `"
              :invalid="$v.paymentNumAttribute.$error"
              v-model.trim="paymentNumAttribute"
              class="mt-1"
              @input="$v.paymentNumAttribute.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payments.customer')"
            :error="customerError"
            required
          >
            <sw-select
              v-model="customer"
              :options="customers"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :disabled="isEdit"
              :placeholder="$t('customers.select_a_customer')"
              label="name"
              class="mt-1"
              track-by="id"
            />
          </sw-input-group>

          <sw-input-group :label="$t('payments.invoice')">
            <sw-select
              v-model="invoice"
              :options="invoiceList"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :disabled="isEdit"
              :placeholder="$t('invoices.select_invoice')"
              :custom-label="invoiceWithAmount"
              class="mt-1"
              track-by="invoice_number"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('payments.amount')"
            :error="amountError"
            required
          >
            <div class="relative w-full mt-1">
              <sw-money
                v-model="amount"
                :currency="customerCurrency"
                :invalid="$v.formData.amount.$error"
                class="
                  relative
                  w-full
                  focus:border focus:border-solid focus:border-primary-500
                "
                @input="$v.formData.amount.$touch()"
              />
            </div>
          </sw-input-group>

          <sw-input-group :label="$t('payments.payment_mode')">
            <sw-select
              v-model="formData.payment_method"
              :options="paymentModes"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('payments.select_payment_mode')"
              :max-height="150"
              label="name"
              class="mt-1"
            >
              <div slot="afterList">
                <button
                  type="button"
                  class="
                    flex
                    items-center
                    justify-center
                    w-full
                    px-2
                    py-2
                    bg-gray-200
                    border-none
                    outline-none
                    text-primary-400
                  "
                  @click="addPaymentMode"
                >
                  <shopping-cart-icon class="h-5 mr-3 text-primary-400" />
                  <label>{{
                    $t('settings.customization.payments.add_payment_mode')
                  }}</label>
                </button>
              </div>
            </sw-select>
          </sw-input-group>
        </div>

        <div v-if="customFields.length > 0">
          <div class="grid gap-6 mt-6 grid-col-1 md:grid-cols-2">
            <sw-input-group
              v-for="(field, index) in customFields"
              :label="field.label"
              :required="field.is_required ? true : false"
              :key="index"
            >
              <component
                :type="field.type.label"
                :field="field"
                :is-edit="isEdit"
                :is="field.type + 'Field'"
                :invalid-fields="invalidFields"
                @update="setCustomFieldValue"
              />
            </sw-input-group>
          </div>
        </div>

        <sw-popup
          ref="notePopup"
          class="z-10 my-6 text-sm font-semibold leading-5 text-primary-400"
        >
          <div slot="activator" class="float-right mt-1">
            + {{ $t('general.insert_note') }}
          </div>
          <note-select-popup type="Payment" @select="onSelectNote" />
        </sw-popup>

        <sw-input-group :label="$t('payments.note')" class="mt-6 mb-4">
          <base-custom-input
            v-model="formData.notes"
            :fields="PaymentFields"
            class="mb-4"
          />
        </sw-input-group>

        <sw-button
          :disabled="isLoading"
          :loading="isLoading"
          variant="primary"
          type="submit"
          class="flex w-full mt-4 sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEdit ? $t('payments.update_payment') : $t('payments.save_payment')
          }}
        </sw-button>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { ShoppingCartIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'

const { required, between, numeric } = require('vuelidate/lib/validators')

export default {
  components: { ShoppingCartIcon },
  mixins: [CustomFieldsMixin],

  data() {
    return {
      formData: {
        user_id: null,
        payment_number: null,
        payment_date: new Date(),
        amount: 100,
        payment_method: null,
        invoice_id: null,
        notes: null,
        payment_method_id: null,
      },
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      customer: null,
      invoice: null,
      invoiceList: [],
      isLoading: false,
      isRequestOnGoing: false,
      maxPayableAmount: Number.MAX_SAFE_INTEGER,
      isSettingInitialData: true,
      paymentNumAttribute: null,
      paymentPrefix: '',
      PaymentFields: [
        'customer',
        'company',
        'customerCustom',
        'payment',
        'paymentCustom',
      ],
    }
  },
  validations() {
    return {
      customer: {
        required,
      },
      formData: {
        payment_date: {
          required,
        },
        amount: {
          required,
          between: between(1, this.maxPayableAmount + 1),
        },
      },
      paymentNumAttribute: {
        required,
        numeric,
      },
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    ...mapGetters('payment', ['paymentModes', 'selectedNote']),
    ...mapGetters('customer', ['customers']),
    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },
    pageTitle() {
      if (this.$route.name === 'payments.edit') {
        return this.$t('payments.edit_payment')
      }
      return this.$t('payments.new_payment')
    },
    isEdit() {
      if (this.$route.name === 'payments.edit') {
        return true
      }
      return false
    },
    customerCurrency() {
      if (this.customer && this.customer.currency) {
        return {
          decimal: this.customer.currency.decimal_separator,
          thousands: this.customer.currency.thousand_separator,
          prefix: this.customer.currency.symbol + ' ',
          precision: this.customer.currency.precision,
          masked: false,
        }
      } else {
        return this.defaultCurrencyForInput
      }
    },
    customerError() {
      if (!this.$v.customer.$error) {
        return ''
      }

      if (!this.$v.customer.required) {
        return this.$tc('validation.required')
      }
    },
    DateError() {
      if (!this.$v.formData.payment_date.$error) {
        return ''
      }
      if (!this.$v.formData.payment_date.required) {
        return this.$t('validation.required')
      }
    },
    amountError() {
      if (!this.$v.formData.amount.$error) {
        return ''
      }

      if (!this.$v.formData.amount.required) {
        return this.$t('validation.required')
      }

      if (
        !this.$v.formData.amount.between &&
        this.$v.formData.amount.numeric &&
        this.amount <= 0
      ) {
        return this.$t('validation.payment_greater_than_zero')
      }

      if (!this.$v.formData.amount.between && this.amount > 0) {
        return this.$t('validation.payment_greater_than_due_amount')
      }
    },
    paymentNumError() {
      if (!this.$v.paymentNumAttribute.$error) {
        return ''
      }

      if (!this.$v.paymentNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.paymentNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
  },
  watch: {
    customer(newValue) {
      this.formData.user_id = newValue.id
      if (!this.isEdit) {
        if (this.isSettingInitialData) {
          this.isSettingInitialData = false
        } else {
          this.invoice = null
          this.formData.invoice_id = null
        }
        this.formData.amount = 0
        this.invoiceList = []
        this.fetchCustomerInvoices(newValue.id)
      }
    },
    selectedNote() {
      if (this.selectedNote) {
        this.formData.notes = this.selectedNote
      }
    },
    invoice(newValue) {
      if (newValue) {
        this.formData.invoice_id = newValue.id
        if (!this.isEdit) {
          this.setPaymentAmountByInvoiceData(newValue.id)
        }
      }
    },
  },
  async mounted() {
    this.$v.formData.$reset()
    this.resetSelectedNote()
    this.$nextTick(() => {
      this.loadData()
      if (this.$route.params.id && !this.isEdit) {
        this.setInvoicePaymentData()
      }
    })
  },
  methods: {
    ...mapActions('invoice', ['fetchInvoice', 'fetchInvoices']),

    ...mapActions('payment', [
      'addPayment',
      'updatePayment',
      'fetchPayment',
      'fetchPaymentModes',
      'resetSelectedNote',
    ]),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('customer', ['fetchCustomers']),

    ...mapActions('notification', ['showNotification']),

    invoiceWithAmount({ invoice_number, due_amount }) {
      return `${invoice_number} (${this.$utils.formatGraphMoney(
        due_amount,
        this.customer.currency
      )})`
    },

    async addPaymentMode() {
      this.openModal({
        title: this.$t('settings.customization.payments.add_payment_mode'),
        componentName: 'PaymentMode',
      })
    },

    async checkAutoGenerate() {
      let response = await this.fetchCompanySettings(['payment_auto_generate'])

      let response1 = await axios.get('/api/v1/next-number?key=payment')

      if (response.data && response.data.payment_auto_generate === 'YES') {
        if (response1.data) {
          this.paymentNumAttribute = response1.data.nextNumber
          this.paymentPrefix = response1.data.prefix
          return true
        }
      } else {
        this.paymentPrefix = response1.data.prefix
      }
    },

    async loadData() {
      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchPayment(this.$route.params.id)
        this.formData = { ...this.formData, ...response.data.payment }
        this.customer = response.data.payment.user
        this.formData.payment_date = moment(
          response.data.payment.payment_date,
          'YYYY-MM-DD'
        ).toString()
        this.formData.amount = parseFloat(response.data.payment.amount)
        this.paymentPrefix = response.data.payment_prefix
        this.paymentNumAttribute = response.data.nextPaymentNumber
        this.formData.payment_method = response.data.payment.payment_method
        if (response.data.payment.invoice !== null) {
          this.maxPayableAmount =
            parseInt(response.data.payment.amount) +
            parseInt(response.data.payment.invoice.due_amount)
          this.invoice = response.data.payment.invoice
        }
        let res = await this.fetchCustomFields({
          type: 'Payment',
          limit: 'all',
        })
        this.setEditCustomFields(
          response.data.payment.fields,
          res.data.customFields.data
        )

        if (this.formData.payment_method_id) {
          await this.fetchPaymentModes({ limit: 'all' })
        }

        if (this.formData.user_id) {
          await this.fetchCustomers({ limit: 'all' })
        }
        this.isRequestOnGoing = false
      } else {
        this.isRequestOnGoing = true
        this.checkAutoGenerate()
        this.setInitialCustomFields('Payment')
        this.formData.payment_date = moment().toString()
        this.fetchPaymentModes({ limit: 'all' })
        await this.fetchCustomers({ limit: 'all' })
        if (this.$route.query.customer) {
          this.setPaymentCustomer(parseInt(this.$route.query.customer))
        }
        this.isRequestOnGoing = false
      }
      return true
    },
    setPaymentCustomer(id) {
      this.customer = this.customers.find((c) => {
        return c.id === id
      })
    },
    async setInvoicePaymentData() {
      let data = await this.fetchInvoice(this.$route.params.id)
      this.customer = data.data.invoice.user
      this.invoice = data.data.invoice
    },
    async setPaymentAmountByInvoiceData(id) {
      let data = await this.fetchInvoice(id)
      this.formData.amount = data.data.invoice.due_amount
      this.maxPayableAmount = data.data.invoice.due_amount
    },
    async fetchCustomerInvoices(userId) {
      let data = {
        customer_id: userId,
        status: 'DUE',
      }
      let response = await this.fetchInvoices(data)
      this.invoiceList = response.data.invoices.data
    },
    async submitPaymentData() {
      let validate = await this.touchCustomField()
      this.$v.customer.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid || validate.error) {
        return true
      }

      this.formData.payment_number =
        this.paymentPrefix + '-' + this.paymentNumAttribute

      if (this.isEdit) {
        let data = {
          editData: {
            ...this.formData,
            payment_method_id: this.formData.payment_method
              ? this.formData.payment_method.id
              : null,
            payment_date: moment(this.formData.payment_date).format(
              'YYYY-MM-DD'
            ),
          },
          id: this.$route.params.id,
        }

        try {
          this.isLoading = true
          let response = await this.updatePayment(data)

          if (response.data.success) {
            this.isLoading = false
            this.$router.push(
              `/admin/payments/${response.data.payment.id}/view`
            )
            this.showNotification({
              type: 'success',
              message: this.$t('payments.updated_message'),
            })
            return true
          }

          if (response.data.error === 'invalid_amount') {
            this.showNotification({
              type: 'error',
              message: this.$t('invalid_amount_message'),
            })
            return false
          }
          this.showNotification({
            type: 'error',
            message: response.data.error,
          })
        } catch (err) {
          this.isLoading = false

          if (err.response.data.errors.payment_number) {
            this.showNotification({
              type: 'error',
              message: err.response.data.errors.payment_number,
            })
            return true
          }
          this.showNotification({
            type: 'error',
            message: err.response.data.message,
          })
        }
      } else {
        let data = {
          ...this.formData,
          payment_method_id: this.formData.payment_method
            ? this.formData.payment_method.id
            : null,
          payment_date: moment(this.formData.payment_date).format('YYYY-MM-DD'),
        }

        this.isLoading = true

        try {
          let response = await this.addPayment(data)

          if (response.data.success) {
            this.$router.push(
              `/admin/payments/${response.data.payment.id}/view`
            )
            this.showNotification({
              type: 'success',
              message: this.$t('payments.created_message'),
            })
            this.isLoading = true
            return true
          }

          if (response.data.error === 'invalid_amount') {
            this.showNotification({
              type: 'error',
              message: this.$t('invalid_amount_message'),
            })
            return false
          }
          this.showNotification({
            type: 'error',
            message: response.data.error,
          })
        } catch (err) {
          this.isLoading = false

          if (err.response.data.errors.payment_number) {
            this.showNotification({
              type: 'error',
              message: err.response.data.errors.payment_number,
            })
            return true
          }
          this.showNotification({
            type: 'error',
            message: err.response.data.message,
          })
        }
      }
    },
    onSelectNote(data) {
      this.formData.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>
