<template>
  <div class="main-content expenses">
    <form action="" @submit.prevent="sendData">
      <div class="page-header">
        <h3 class="page-title">{{ isEdit ? $t('expenses.edit_expense') : $t('expenses.new_expense') }}</h3>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/expenses">{{ $tc('expenses.expense', 2) }}</router-link></li>
          <li class="breadcrumb-item"><a href="#">{{ isEdit ? $t('expenses.edit_expense') : $t('expenses.new_expense') }}</a></li>
        </ol>
        <div class="page-actions row header-button-container">
          <div v-if="isReceiptAvailable" class="col-xs-2 mr-4">
            <a :href="getReceiptUrl">
              <base-button
                :loading="isLoading"
                icon="download"
                color="theme"
                outline
              >
                {{ $t('expenses.download_receipt') }}
              </base-button>
            </a>
          </div>
          <div class="col-xs-2">
            <base-button
              :loading="isLoading"
              icon="save"
              color="theme"
              type="submit"
            >
              {{ isEdit ? $t('expenses.update_expense') : $t('expenses.save_expense') }}
            </base-button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <!-- <div class="form-group col-sm-6">
                  <label class="control-label">{{ $t('expenses.expense_title') }}</label>
                  <input v-model="formData.title" type="text" name="name" class="form-control">
                </div> -->
                <div class="form-group col-sm-6">
                  <label class="control-label">{{ $t('expenses.category') }}</label><span class="text-danger"> * </span>
                  <base-select
                    ref="baseSelect"
                    v-model="category"
                    :options="categories"
                    :invalid="$v.category.$error"
                    :searchable="true"
                    :show-labels="false"
                    :placeholder="$t('expenses.categories.select_a_category')"
                    label="name"
                    track-by="id"
                    @input="$v.category.$touch()"
                  >
                    <div slot="afterList">
                      <button type="button" class="list-add-button" @click="openCategoryModal">
                        <font-awesome-icon class="icon" icon="cart-plus" />
                        <label>{{ $t('settings.expense_category.add_new_category') }}</label>
                      </button>
                    </div>
                  </base-select>
                  <div v-if="$v.category.$error">
                    <span v-if="!$v.category.required" class="text-danger">{{ $t('validation.required') }}</span>
                  </div>
                </div>
                <!-- <div class="form-group col-sm-6">
                  <label>{{ $t('expenses.contact') }}</label>
                  <select v-model="formData.contact" name="contact" class="form-control ls-select2">
                    <option v-for="(contact, index) in contacts" :key="index" :value="contact.id"> {{ contact.name }}</option>
                  </select>
                </div> -->
                <div class="form-group col-sm-6">
                  <label>{{ $t('expenses.expense_date') }}</label><span class="text-danger"> * </span>
                  <base-date-picker
                    v-model="formData.expense_date"
                    :invalid="$v.formData.expense_date.$error"
                    :calendar-button="true"
                    calendar-button-icon="calendar"
                    @change="$v.formData.expense_date.$touch()"
                  />
                  <div v-if="$v.formData.expense_date.$error">
                    <span v-if="!$v.formData.expense_date.required" class="text-danger">{{ $t('validation.required') }}</span>
                  </div>
                </div>
                <div class="form-group col-sm-6">
                  <label>{{ $t('expenses.amount') }}</label> <span class="text-danger"> * </span>
                  <div class="base-input">
                    <money
                      :class="{'invalid' : $v.formData.amount.$error}"
                      v-model="amount"
                      v-bind="defaultCurrencyForInput"
                      class="input-field"
                    />
                  </div>
                  <div v-if="$v.formData.amount.$error">
                    <span v-if="!$v.formData.amount.required" class="text-danger">{{ $t('validation.required') }} </span>
                    <span v-if="!$v.formData.amount.maxLength" class="text-danger">{{ $t('validation.price_maxlength') }}</span>
                    <span v-if="!$v.formData.amount.minValue" class="text-danger">{{ $t('validation.price_minvalue') }}</span>
                  </div>
                </div>
                <div class="form-group col-sm-6">
                  <label for="description">{{ $t('expenses.note') }}</label>
                  <base-text-area
                    v-model="formData.notes"
                    @input="$v.formData.notes.$touch()"
                  />
                  <div v-if="$v.formData.notes.$error">
                    <span v-if="!$v.formData.notes.maxLength" class="text-danger">{{ $t('validation.notes_maxlength') }}</span>
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label for="description">{{ $t('expenses.receipt') }} : </label>
                  <div class="image-upload-box" @click="$refs.file.click()">
                    <input ref="file" class="d-none" type="file" @change="onFileChange">
                    <img v-if="previewReceipt" :src="previewReceipt" class="preview-logo">
                    <div v-else class="upload-content">
                      <font-awesome-icon class="upload-icon" icon="cloud-upload-alt"/>
                      <p class="upload-text"> {{ $t('general.choose_file') }} </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group collapse-button-container">
                    <base-button
                      :loading="isLoading"
                      icon="save"
                      color="theme"
                      type="submit"
                      class="collapse-button"
                    >
                      {{ isEdit ? $t('expenses.update_expense') : $t('expenses.save_expense') }}
                    </base-button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import MultiSelect from 'vue-multiselect'
import moment from 'moment'
import { mapActions, mapGetters } from 'vuex'
import { validationMixin } from 'vuelidate'
const { required, minValue, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  props: {
    addname: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      formData: {
        expense_category_id: null,
        expense_date: new Date(),
        amount: null,
        notes: ''
      },
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false
      },
      isReceiptAvailable: false,
      isLoading: false,
      file: null,
      category: null,
      passData: [],
      contacts: [],
      previewReceipt: null,
      fileSendUrl: '/api/expenses'
    }
  },
  validations: {
    category: {
      required
    },
    formData: {
      expense_date: {
        required
      },
      amount: {
        required,
        minValue: minValue(0.1),
        maxLength: maxLength(20)
      },
      notes: {
        maxLength: maxLength(255)
      }
    }
  },
  computed: {
    ...mapGetters('currency', [
      'defaultCurrencyForInput'
    ]),
    amount: {
      get: function () {
        return this.formData.amount / 100
      },
      set: function (newValue) {
        this.formData.amount = newValue * 100
      }
    },
    isEdit () {
      if (this.$route.name === 'expenses.edit') {
        return true
      }
      return false
    },
    ...mapGetters('category', [
      'categories'
    ]),
    ...mapGetters('company', [
      'getSelectedCompany'
    ]),
    getReceiptUrl () {
      if (this.isEdit) {
        return `/expenses/${this.$route.params.id}/receipt/${this.getSelectedCompany.unique_hash}`
      }
    }
  },
  watch: {
    category (newValue) {
      this.formData.expense_category_id = newValue.id
    }
  },
  mounted () {
    // this.$refs.baseSelect.$refs.search.focus()
    this.fetchInitialData()
    if (this.isEdit) {
      this.getReceipt()
    }
    window.hub.$on('newCategory', (val) => {
      this.category = val
    })
  },
  methods: {
    ...mapActions('expense', [
      'fetchCreateExpense',
      'getFile',
      'sendFileWithData',
      'addExpense',
      'updateExpense',
      'fetchExpense'
    ]),
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('category', [
      'fetchCategories'
    ]),
    openCategoryModal () {
      this.openModal({
        'title': 'Add Category',
        'componentName': 'CategoryModal'
      })
      // this.$refs.table.refresh()
    },
    onFileChange (e) {
      var input = event.target
      this.file = input.files[0]
      if (input.files && input.files[0]) {
        var reader = new FileReader()
        reader.onload = (e) => {
          this.previewReceipt = e.target.result
        }
        reader.readAsDataURL(input.files[0])
      }
    },
    async getReceipt () {
      let res = await axios.get(`/api/expenses/${this.$route.params.id}/show/receipt`)

      if (res.data.error) {
        this.isReceiptAvailable = false
        return true
      }

      this.isReceiptAvailable = true
      this.previewReceipt = res.data.image
    },
    async fetchInitialData () {
      this.fetchCategories()
      if (this.isEdit) {
        let response = await this.fetchExpense(this.$route.params.id)
        this.category = response.data.expense.category
        this.formData = { ...response.data.expense }
        this.formData.expense_date = moment(this.formData.expense_date).toString()
        this.formData.amount = (response.data.expense.amount)
        this.fileSendUrl = `/api/expenses/${this.$route.params.id}`
      }
    },
    async sendData () {
      this.$v.category.$touch()
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      let data = new FormData()

      if (this.file) {
        data.append('attachment_receipt', this.file)
      }
      data.append('expense_category_id', this.formData.expense_category_id)
      data.append('expense_date',  moment(this.formData.expense_date).format('DD/MM/YYYY'))
      data.append('amount', (this.formData.amount))
      data.append('notes', this.formData.notes)

      if (this.isEdit) {
        this.isLoading = true
        data.append('_method', 'PUT')
        let response = await this.updateExpense({id: this.$route.params.id, editData: data})
        if (response.data.success) {
          window.toastr['success'](this.$t('expenses.updated_message'))
          this.isLoading = false
          this.$router.push('/admin/expenses')
          return true
        }
        window.toastr['error'](response.data.error)
      } else {
        this.isLoading = true
        let response = await this.addExpense(data)
        if (response.data.success) {
          window.toastr['success'](this.$t('expenses.created_message'))
          this.isLoading = false
          this.$router.push('/admin/expenses')
          this.isLoading = false
          return true
        }
        window.toastr['success'](response.data.success)
      }
    }
  }
}
</script>
