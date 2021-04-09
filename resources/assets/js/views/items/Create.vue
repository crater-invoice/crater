<template>
  <base-page>
    <!-- Page Header -->
    <sw-page-header :title="pageTitle" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard" />
        <sw-breadcrumb-item :title="$tc('items.item', 2)" to="/admin/items" />
        <sw-breadcrumb-item
          v-if="$route.name === 'items.edit'"
          :title="$t('items.edit_item')"
          to="#"
          active
        />
        <sw-breadcrumb-item
          v-else
          :title="$t('items.new_item')"
          to="#"
          active
        />
      </sw-breadcrumb>
    </sw-page-header>

    <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-6">
        <form action="" @submit.prevent="submitItem">
          <sw-card>
            <sw-input-group
              :label="$t('items.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('items.price')"
              :error="priceError"
              class="mb-4"
              required
            >
              <sw-money
                v-model.trim="price"
                :invalid="$v.formData.price.$error"
                :currency="defaultCurrencyForInput"
                class="relative w-full focus:border focus:border-solid focus:border-primary-500"
                @input="$v.formData.price.$touch()"
              />
            </sw-input-group>

            <sw-input-group :label="$t('items.unit')" class="mb-4">
              <sw-select
                v-model="formData.unit"
                :options="itemUnits"
                :searchable="true"
                :show-labels="false"
                :placeholder="$t('items.select_a_unit')"
                class="mt-2"
                label="name"
              >
                <div
                  slot="afterList"
                  class="flex items-center justify-center w-full px-6 py-3 text-base bg-gray-200 cursor-pointer text-primary-400"
                  @click="addItemUnit"
                >
                  <shopping-cart-icon
                    class="h-5 mr-2 -ml-2 text-center text-primary-400"
                  />

                  <label class="ml-2 text-sm leading-none text-primary-400">{{
                    $t('settings.customization.items.add_item_unit')
                  }}</label>
                </div>
              </sw-select>
            </sw-input-group>

            <sw-input-group
              v-if="isTaxPerItem"
              :label="$t('items.taxes')"
              class="mb-4"
            >
              <sw-select
                v-model="formData.taxes"
                :options="getTaxTypes"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :multiple="true"
                class="mt-2"
                track-by="tax_type_id"
                label="tax_name"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('items.description')"
              :error="descriptionError"
              class="mb-4"
            >
              <sw-textarea
                v-model="formData.description"
                rows="2"
                name="description"
                @input="$v.formData.description.$touch()"
              />
            </sw-input-group>

            <div class="mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                size="lg"
                class="flex justify-center w-full md:w-auto"
              >
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ isEdit ? $t('items.update_item') : $t('items.save_item') }}
              </sw-button>
            </div>
          </sw-card>
        </form>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { ShoppingCartIcon } from '@vue-hero-icons/solid'
import TheSiteHeaderVue from '../layouts/partials/TheSiteHeader.vue'
const {
  required,
  minLength,
  numeric,
  minValue,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ShoppingCartIcon,
  },

  data() {
    return {
      isLoading: false,
      title: 'Add Item',
      units: [],
      taxes: [],
      taxPerItem: '',

      formData: {
        name: '',
        description: '',
        price: '',
        unit_id: null,
        unit: null,
        taxes: [],
      },

      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),

    ...mapGetters('item', ['itemUnits']),

    ...mapGetters('taxType', ['taxTypes']),

    price: {
      get: function () {
        return this.formData.price / 100
      },
      set: function (newValue) {
        this.formData.price = Math.round(newValue * 100)
      },
    },

    pageTitle() {
      if (this.$route.name === 'items.edit') {
        return this.$t('items.edit_item')
      }
      return this.$t('items.new_item')
    },

    ...mapGetters('taxType', ['taxTypes']),

    isEdit() {
      if (this.$route.name === 'items.edit') {
        return true
      }
      return false
    },

    isTaxPerItem() {
      return this.taxPerItem === 'YES' ? 1 : 0
    },

    getTaxTypes() {
      return this.taxTypes.map((tax) => {
        return {
          ...tax,
          tax_type_id: tax.id,
          tax_name: tax.name + ' (' + tax.percent + '%)',
        }
      })
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },

    priceError() {
      if (!this.$v.formData.price.$error) {
        return ''
      }

      if (!this.$v.formData.price.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.formData.price.maxLength) {
        return this.$t('validation.price_maxlength')
      }

      if (!this.$v.formData.price.minValue) {
        return this.$t('validation.price_minvalue')
      }
    },

    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }

      if (!this.$v.formData.description.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },

  created() {
    this.loadData()
  },

  mounted() {
    this.setTaxPerItem()

    this.$v.formData.$reset()
  },

  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },

      price: {
        required,
        numeric,
        maxLength: maxLength(20),
        minValue: minValue(0.1),
      },

      description: {
        maxLength: maxLength(65000),
      },
    },
  },

  methods: {
    ...mapActions('item', [
      'addItem',
      'fetchItem',
      'updateItem',
      'fetchItemUnits',
    ]),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('notification', ['showNotification']),

    async setTaxPerItem() {
      let response = await this.fetchCompanySettings(['tax_per_item'])

      if (response.data) {
        response.data.tax_per_item === 'YES'
          ? (this.taxPerItem = 'YES')
          : (this.taxPerItem = 'NO')
      }
    },

    async loadData() {
      if (this.isEdit) {
        let response = await this.fetchItem(this.$route.params.id)

        this.formData = { ...response.data.item, unit: null }

        this.fractional_price = response.data.item.price

        if (this.formData.unit_id) {
          await this.fetchItemUnits({ limit: 'all' })
          this.formData.unit = this.itemUnits.find(
            (_unit) => response.data.item.unit_id === _unit.id
          )
        }

        if (this.formData.taxes) {
          await this.fetchTaxTypes({ limit: 'all' })
          this.formData.taxes = response.data.item.taxes.map((tax) => {
            return { ...tax, tax_name: tax.name + '(' + tax.percent + '%)' }
          })
        }
      } else {
        this.fetchItemUnits({ limit: 'all' })
        this.fetchTaxTypes({ limit: 'all' })
      }
    },

    async submitItem() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      if (this.formData.unit) {
        this.formData.unit_id = this.formData.unit.id
      }

      let response
      this.isLoading = true

      if (this.isEdit) {
        response = await this.updateItem(this.formData)
      } else {
        let data = {
          ...this.formData,
          taxes: this.formData.taxes.map((tax) => {
            return {
              tax_type_id: tax.id,
              amount: (this.formData.price * tax.percent) / 100,
              percent: tax.percent,
              name: tax.name,
              collective_tax: 0,
            }
          }),
        }
        response = await this.addItem(data)
      }

      if (response.data) {
        this.isLoading = false

        if (!this.isEdit) {
          this.showNotification({
            type: 'success',
            message: this.$tc('items.created_message'),
          })
          this.$router.push('/admin/items')
          return true
        } else {
          this.showNotification({
            type: 'success',
            message: this.$tc('items.updated_message'),
          })
          this.$router.push('/admin/items')
          return true
        }
        this.showNotification({
          type: 'error',
          message: response.data.error,
        })
      }
    },

    async addItemUnit() {
      this.openModal({
        title: this.$t('settings.customization.items.add_item_unit'),
        componentName: 'ItemUnit',
      })
    },
  },
}
</script>
