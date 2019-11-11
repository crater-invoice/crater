<template>
  <div class="main-content currencycreate">
    <div class="page-header">
      <h3 class="page-title">{{ $t('settings.currencies.add_currency') }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/settings/currencies">{{ $tc('settings.currencies.currency',2) }}</router-link></li>
        <li class="breadcrumb-item"><a href="#">{{ $t('navigation.add') }}</a></li>
      </ol>
      <div class="page-actions">
        <router-link slot="item-title" to="/admin/settings/currencies">
          <base-button icon="backward" color="theme">
            {{ $t('navigation.go_back') }}
          </base-button>
        </router-link>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <div class="card">
          <form action="" @submit.prevent="submiteCurrency">
            <div class="card-body">
              <div class="form-group">
                <label class="control-label">{{ $t('settings.currencies.name') }}:</label><span class="required text-danger"> *</span>
                <input
                  :class="{ error: $v.formData.name.$error }"
                  v-model.trim="formData.name"
                  type="text"
                  name="name"
                  class="form-control"
                  @input="$v.formData.name.$touch()"
                >
                <div v-if="$v.formData.name.$error">
                  <span v-if="!$v.formData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">{{ $t('settings.currencies.code') }}:</label><span class="required"> *</span>
                <input
                  :class="{ error: $v.formData.code.$error }"
                  v-model="formData.code"
                  type="text"
                  name="code"
                  class="form-control"
                  @input="$v.formData.code.$touch()"
                >
                <div v-if="$v.formData.code.$error">
                  <span v-if="!$v.formData.code.required" class="text-danger">{{ $tc('validation.required') }}</span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">{{ $t('settings.currencies.symbol') }}:</label>
                <input
                  v-model="formData.symbol"
                  type="text"
                  name="symbol"
                  class="form-control"
                >
              </div>
              <div class="form-group">
                <label class="control-label">{{ $t('settings.currencies.precision') }}:</label>
                <input
                  v-model="formData.precision"
                  type="text"
                  name="precision"
                  class="form-control"
                >
              </div>
              <div class="form-group">
                <label class="control-label">{{ $t('settings.currencies.thousand_separator') }}:</label><span class="required"> *</span>
                <input
                  :class="{ error: $v.formData.thousand_separator.$error }"
                  v-model="formData.thousand_separator"
                  type="text"
                  name="thousand_separator"
                  class="form-control"
                  @input="$v.formData.thousand_separator.$touch()"
                >
                <div v-if="$v.formData.thousand_separator.$error">
                  <span v-if="!$v.formData.thousand_separator.required" class="text-danger">{{ $tc('validation.required') }}</span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">{{ $t('settings.currencies.decimal_separator') }}:</label><span class="required"> *</span>
                <input
                  :class="{ error: $v.formData.decimal_separator.$error }"
                  v-model="formData.decimal_separator"
                  type="text"
                  name="decimal_separator"
                  class="form-control"
                  @input="$v.formData.decimal_separator.$touch()"
                >
                <div v-if="$v.formData.decimal_separator.$error">
                  <span v-if="!$v.formData.decimal_separator.required" class="text-danger">{{ $tc('validation.required') }}</span>
                </div>
              </div>
              <div class="form-group">
                <label>{{ $t('settings.currencies.position_of_symbol') }}:</label><span class="required"> *</span>
                <select
                  v-model="formData.swap_currency_symbol"
                  :class="{ error: $v.formData.swap_currency_symbol.$error }"
                  class="form-control ls-select2"
                  name="swap_currency_symbol"
                  @select="$v.formData.swap_currency_symbol.$touch()"
                >
                  <option value="0">{{ $t('settings.currencies.right') }}</option>
                  <option value="1">{{ $t('settings.currencies.left') }}</option>
                </select>
                <div v-if="$v.formData.swap_currency_symbol.$error">
                  <span v-if="!$v.formData.swap_currency_symbol.required" class="text-danger">{{ $tc('validation.required') }}</span>
                </div>
              </div>
              <base-button color="theme" type="submit">
                {{ $t('navigation.add') }}
              </base-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions } from 'vuex'
import { validationMixin } from 'vuelidate'
const { required } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return this.$store.state.currency
  },
  computed: {
    isEdit () {
      if (this.$route.name === 'currencyedit') {
        return true
      }
      return false
    }
  },
  validations: {
    formData: {
      name: {
        required
      },
      code: {
        required
      },
      thousand_separator: {
        required
      },
      decimal_separator: {
        required
      },
      swap_currency_symbol: {
        required
      }
    }
  },
  mounted () {
    if (!this.isEdit) {
      return true
    }
    this.loadData(this.$route.params.id)
  },
  methods: {
    ...mapActions('currency', [
      'loadData',
      'addCurrency',
      'editCurrency'
    ]),
    async submiteCurrency () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return false
      }
      if (this.isEdit) {
        this.editCurrency(this.$route.params.id)
        return true
      }
      this.addCurrency()
      return true
    }
  }
}
</script>
