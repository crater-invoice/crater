<template>
  <base-page class="relative">
    <form action="" @submit.prevent="sendData">
      <!-- Page Header -->
      <sw-page-header :title="pageTitle" class="mb-5">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />

          <sw-breadcrumb-item
            :title="$tc('expenses.expense', 2)"
            to="/admin/expenses"
          />

          <sw-breadcrumb-item
            v-if="$route.name === 'expenses.edit'"
            :title="$t('expenses.edit_expense')"
            to="#"
            active
          />

          <sw-breadcrumb-item
            v-else
            :title="$t('expenses.new_expense')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            v-if="isReceiptAvailable"
            :href="getReceiptUrl"
            tag-name="a"
            variant="primary"
            outline
            size="lg"
            class="mr-2"
          >
            <download-icon class="h-5 mr-2 -ml-1" />
            {{ $t('expenses.download_receipt') }}
          </sw-button>

          <div class="hidden md:block">
            <sw-button
              :loading="isLoading"
              :disabled="isLoading"
              variant="primary"
              type="submit"
              size="lg"
            >
              <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
              {{
                isEdit
                  ? $t('expenses.update_expense')
                  : $t('expenses.save_expense')
              }}
            </sw-button>
          </div>
        </template>
      </sw-page-header>

      <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />

      <sw-card v-else>
        <div class="grid gap-6 grid-col-1 md:grid-cols-2">
          <sw-input-group
            :label="$t('expenses.category')"
            :error="categoryError"
            required
          >
            <sw-select
              ref="baseSelect"
              v-model="category"
              :options="categories"
              :invalid="$v.category.$error"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.categories.select_a_category')"
              class="mt-2"
              label="name"
              track-by="id"
              @input="$v.category.$touch()"
            >
              <sw-button
                slot="afterList"
                type="button"
                variant="gray-light"
                class="flex items-center justify-center w-full px-4 py-3 bg-gray-200 border-none outline-none"
                @click="openCategoryModal"
              >
                <shopping-cart-icon class="h-5 text-center text-primary-400" />
                <label class="ml-2 text-xs leading-none text-primary-400">{{
                  $t('settings.expense_category.add_new_category')
                }}</label>
              </sw-button>
            </sw-select>
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.expense_date')"
            :error="dateError"
            required
          >
            <base-date-picker
              v-model="formData.expense_date"
              :invalid="$v.formData.expense_date.$error"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
              @input="$v.formData.expense_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.amount')"
            :error="amountError"
            required
          >
            <sw-money
              v-model="amount"
              :currency="defaultCurrencyForInput"
              :invalid="$v.formData.amount.$error"
              class="focus:border focus:border-solid focus:border-primary-500"
              @input="$v.formData.amount.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('expenses.customer')">
            <sw-select
              ref="baseSelect"
              v-model="customer"
              :options="customers"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('customers.select_a_customer')"
              class="mt-1"
              label="name"
              track-by="id"
            />
          </sw-input-group>

          <sw-input-group :label="$t('expenses.note')" :error="notesError">
            <sw-textarea
              v-model="formData.notes"
              rows="4"
              @input="$v.formData.notes.$touch()"
            />
          </sw-input-group>

          <sw-input-group :label="$t('expenses.receipt')">
            <div
              id="receipt-box"
              class="relative flex items-center justify-center h-24 p-6 bg-transparent border-2 border-gray-200 border-dashed rounded-md image-upload-box"
            >
              <img
                v-if="previewReceipt"
                :src="previewReceipt"
                class="absolute opacity-100 preview-logo"
                style="max-height: 80%; animation: fadeIn 2s ease"
              />
              <div v-else class="flex flex-col items-center">
                <cloud-upload-icon
                  class="h-5 mb-2 text-xl leading-6 text-gray-400"
                />
                <p class="text-xs leading-4 text-center text-gray-400">
                  Drag a file here or
                  <span id="pick-avatar" class="cursor-pointer text-primary-500"
                    >browse</span
                  >
                  to choose a file
                </p>
              </div>
            </div>

            <sw-avatar
              :preview-avatar="previewReceipt"
              :enable-cropper="false"
              trigger="#receipt-box"
              @changed="onChange"
            >
              <template v-slot:icon>
                <cloud-upload-icon
                  class="h-5 mb-2 text-xl leading-6 text-gray-400"
                />
              </template>
            </sw-avatar>
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

        <div class="block mt-2 md:hidden">
          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            :tabindex="6"
            variant="primary"
            type="submit"
            size="lg"
            class="flex w-full"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{
              isEdit
                ? $t('expenses.update_expense')
                : $t('expenses.save_expense')
            }}
          </sw-button>
        </div>
      </sw-card>
    </form>
  </base-page>
</template>

<script>
import moment from 'moment'
import { mapActions, mapGetters } from 'vuex'
const { required, minValue, maxLength } = require('vuelidate/lib/validators')
import { DownloadIcon } from '@vue-hero-icons/outline'
import { CloudUploadIcon, ShoppingCartIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'

export default {
  components: {
    CloudUploadIcon,
    ShoppingCartIcon,
    DownloadIcon,
  },
  mixins: [CustomFieldsMixin],

  props: {
    addname: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      formData: {
        expense_category_id: null,
        expense_date: new Date(),
        amount: 100,
        notes: '',
        user_id: null,
      },

      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
      isRequestOnGoing: false,
      isReceiptAvailable: false,
      isLoading: false,
      category: null,
      previewReceipt: null,
      fileSendUrl: '/api/v1/expenses',
      customer: null,
      fileObject: null,
    }
  },

  validations: {
    category: {
      required,
    },

    formData: {
      expense_date: {
        required,
      },

      amount: {
        required,
        minValue: minValue(0.1),
        maxLength: maxLength(20),
      },

      notes: {
        maxLength: maxLength(65000),
      },
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),

    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = Math.round(newValue * 100)
      },
    },

    pageTitle() {
      if (this.$route.name === 'expenses.edit') {
        return this.$t('expenses.edit_expense')
      }
      return this.$t('expenses.new_expense')
    },

    isEdit() {
      if (this.$route.name === 'expenses.edit') {
        return true
      }
      return false
    },

    ...mapGetters('category', ['categories']),

    ...mapGetters('customer', ['customers']),

    ...mapGetters('company', ['getSelectedCompany']),

    getReceiptUrl() {
      if (this.isEdit) {
        return `/expenses/${this.$route.params.id}/receipt`
      }
    },

    categoryError() {
      if (!this.$v.category.$error) {
        return ''
      }
      if (!this.$v.category.required) {
        return this.$t('validation.required')
      }
    },

    dateError() {
      if (!this.$v.formData.expense_date.$error) {
        return ''
      }
      if (!this.$v.formData.expense_date.required) {
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
      if (!this.$v.formData.amount.maxLength) {
        return this.$t('validation.price_maxlength')
      }
      if (!this.$v.formData.amount.minValue) {
        return this.$t('validation.price_minvalue')
      }
    },

    notesError() {
      if (!this.$v.formData.notes.$error) {
        return ''
      }
      if (!this.$v.formData.notes.maxLength) {
        return this.$t('validation.notes_maxlength')
      }
    },
  },

  watch: {
    category(newValue) {
      this.formData.expense_category_id = newValue.id
    },
  },

  mounted() {
    this.$v.formData.$reset()

    this.loadData()

    window.hub.$on('newCategory', (val) => {
      this.category = val
    })
  },

  methods: {
    ...mapActions('expense', [
      'getExpenseReceipt',
      'addExpense',
      'updateExpense',
      'fetchExpense',
    ]),

    ...mapActions('modal', ['openModal']),

    ...mapActions('category', ['fetchCategories']),

    ...mapActions('customer', ['fetchCustomers']),

    ...mapActions('notification', ['showNotification']),

    openCategoryModal() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModal',
      })
    },

    onChange(data) {
      this.previewReceipt = data.image
      this.fileObject = data.file
    },

    async getReceipt() {
      let res = await this.getExpenseReceipt(this.$route.params.id)

      if (res.data.error) {
        this.isReceiptAvailable = false
        return true
      }

      this.isReceiptAvailable = true
      this.previewReceipt = res.data.image
    },

    setExpenseCustomer(id) {
      this.customer = this.customers.find((c) => {
        return c.id == id
      })
    },

    async loadData() {
      this.isRequestOnGoing = true
      await this.fetchCategories({ limit: 'all' })
      await this.fetchCustomers({ limit: 'all' })
      if (this.isEdit) {
        this.isRequestOnGoing = true
        let response = await this.fetchExpense(this.$route.params.id)

        this.formData = { ...this.formData, ...response.data.expense }

        this.formData.expense_date = moment(
          this.formData.expense_date
        ).toString()

        this.formData.amount = response.data.expense.amount

        this.fileSendUrl = `/api/v1/expenses/${this.$route.params.id}`

        if (response.data.expense.expense_category_id) {
          this.category = this.categories.find(
            (category) =>
              category.id === response.data.expense.expense_category_id
          )
        }

        if (response.data.expense.user_id) {
          this.customer = this.customers.find(
            (customer) => customer.id === response.data.expense.user_id
          )
        }

        let res = await this.fetchCustomFields({
          type: 'Expense',
          limit: 'all',
        })

        this.setEditCustomFields(
          response.data.expense.fields,
          res.data.customFields.data
        )

        this.getReceipt()
        this.isRequestOnGoing = false
        return true
      }
      await this.setInitialCustomFields('Expense')
      if (this.$route.query.customer) {
        this.setExpenseCustomer(parseInt(this.$route.query.customer))
      }
      this.isRequestOnGoing = false
    },

    async sendData() {
      let validate = await this.touchCustomField()
      this.$v.category.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid || validate.error) {
        return true
      }

      let data = new FormData()

      if (this.fileObject) {
        data.append('attachment_receipt', this.fileObject)
      }
      data.append('expense_category_id', this.formData.expense_category_id)
      data.append(
        'expense_date',
        moment(this.formData.expense_date).format('YYYY-MM-DD')
      )
      data.append('amount', this.formData.amount)
      data.append('notes', this.formData.notes ? this.formData.notes : '')
      data.append('user_id', this.customer ? this.customer.id : '')
      data.append('customFields', JSON.stringify(this.formData.customFields))

      if (this.isEdit) {
        this.isLoading = true
        data.append('_method', 'PUT')
        let response = await this.updateExpense({
          id: this.$route.params.id,
          editData: data,
        })

        if (response.data.success) {
          this.isLoading = false
          this.showNotification({
            type: 'success',
            message: this.$t('expenses.updated_message'),
          })
          this.$router.push('/admin/expenses')
          return true
        }
        this.showNotification({
          type: 'error',
          message: response.data.error,
        })
      } else {
        this.isLoading = true
        let response = await this.addExpense(data)
        this.isLoading = false

        if (response.data.success) {
          this.showNotification({
            type: 'success',
            message: this.$t('expenses.created_message'),
          })
          this.$router.push('/admin/expenses')
          return true
        }
        this.showNotification({
          type: 'success',
          message: response.data.success,
        })
      }
    },
  },
}
</script>
