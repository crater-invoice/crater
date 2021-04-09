<template>
  <div class="item-modal">
    <form action="" @submit.prevent="submitItemData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('items.name')"
          :error="nameError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-input
            ref="name"
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('items.price')"
          :error="priceError"
          class="mb-4"
          variant="horizontal"
          required
        >
          <sw-money
            v-model="price"
            :currency="defaultCurrencyForInput"
            :invalid="$v.formData.price.$error"
            class="relative w-full focus:border focus:border-solid focus:border-primary"
            @input="$v.formData.price.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('items.unit')"
          class="mb-4"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.unit"
            :options="itemUnits"
            :searchable="true"
            :show-labels="false"
            :max-height="200"
            label="name"
          >
          </sw-select>
        </sw-input-group>

        <sw-input-group
          v-if="isTexPerItem"
          :label="$t('items.taxes')"
          class="mb-4"
          variant="horizontal"
        >
          <sw-select
            v-model="formData.taxes"
            :options="getTaxTypes"
            :searchable="true"
            :show-labels="false"
            :allow-empty="true"
            :multiple="true"
            label="tax_name"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('items.description')"
          :error="descriptionError"
          variant="horizontal"
        >
          <sw-textarea
            v-model="formData.description"
            rows="4"
            cols="50"
            @input="$v.formData.description.$touch()"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeItemModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
        >
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ isEdit ? $t('general.update') : $t('general.save') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { ShoppingCartIcon } from '@vue-hero-icons/solid'

const {
  required,
  minLength,
  numeric,
  maxLength,
  minValue,
} = require('vuelidate/lib/validators')

export default {
  components: {
    ShoppingCartIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      tempData: null,
      taxes: [],
      formData: {
        name: null,
        price: null,
        description: null,
        unit: null,
        taxes: [],
      },
    }
  },

  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      price: {
        required,
        minValue: minValue(0.1),
        maxLength: maxLength(20),
      },
      description: {
        maxLength: maxLength(255),
      },
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrencyForInput']),
    price: {
      get: function () {
        return this.formData.price / 100
      },
      set: function (newValue) {
        this.formData.price = Math.round(newValue * 100)
      },
    },

    ...mapGetters('modal', ['modalDataID', 'modalData']),
    ...mapGetters('item', ['getItemById', 'itemUnits']),
    ...mapGetters('taxType', ['taxTypes']),
    isTexPerItem() {
      return this.modalData.taxPerItem === 'YES'
    },

    getTaxTypes() {
      return this.taxTypes.map((tax) => {
        return { ...tax, tax_name: tax.name + ' (' + tax.percent + '%)' }
      })
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
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
        return this.$tc('validation.required')
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

  watch: {
    modalDataID() {
      this.isEdit = true
      this.fetchEditData()
    },
  },

  created() {
    if (this.modalDataID) {
      this.isEdit = true
      this.fetchEditData()
    }

    if (this.isEdit) {
      this.loadEditData()
    }
  },

  mounted() {
    this.$v.formData.$reset()
    this.$refs.name.focus = true
    this.fetchItemUnits({ limit: 'all' })
  },

  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('item', ['addItem', 'updateItem', 'fetchItemUnits']),
    ...mapActions('invoice', ['setItem']),
    ...mapActions('notification', ['showNotification']),

    resetFormData() {
      this.formData = {
        name: null,
        price: null,
        description: null,
        unit: null,
        id: null,
      }
      this.$v.$reset()
    },

    fetchEditData() {
      this.tempData = this.getItemById(this.modalDataID)
      if (this.tempData) {
        this.formData.name = this.tempData.name
        this.formData.price = this.tempData.price
        this.formData.description = this.tempData.description
        this.formData.unit = this.tempData.unit
        this.formData.id = this.tempData.id
      }
    },

    async submitItemData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      if (this.formData.unit) {
        this.formData.unit_id = this.formData.unit.id
      }

      this.isLoading = true
      let response
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
        this.showNotification({
          type: 'success',
          message: this.$tc('items.created_message'),
        })
        this.setItem(response.data.item)

        window.hub.$emit('newItem', response.data.item)
        this.isLoading = false
        this.resetModalData()
        this.resetFormData()
        this.closeModal()
        return true
      }
      this.showNotification({
        type: 'error',
        message: response.data.error,
      })
    },

    closeItemModal() {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    },
  },
}
</script>
