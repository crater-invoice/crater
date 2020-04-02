<template>
  <div class="item-modal">
    <form action="" @submit.prevent="submitItemData">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">
            {{ $t('items.name') }}<span class="required">*</span>
          </label>
          <div class="col-sm-7">
            <base-input
              ref="name"
              :invalid="$v.formData.name.$error"
              v-model="formData.name"
              type="text"
              @input="$v.formData.name.$touch()"
            />

            <div v-if="$v.formData.name.$error">
              <span v-if="!$v.formData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
              <span v-if="!$v.formData.name.minLength" class="text-danger"> {{ $tc('validation.name_min_length', $v.formData.name.$params.minLength.min, { count: $v.formData.name.$params.minLength.min }) }} </span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('items.price') }}<span class="required">*</span></label>
          <div class="col-sm-7">
            <div class="base-input">
              <money
                :class="{'invalid' : $v.formData.price.$error}"
                v-model="price"
                v-bind="defaultCurrencyForInput"
                class="input-field"
              />
            </div>
            <div v-if="$v.formData.price.$error">
              <span v-if="!$v.formData.price.required" class="text-danger">{{ $tc('validation.required') }}</span>
              <span v-if="!$v.formData.price.numeric" class="text-danger">{{ $tc('validation.numbers_only') }}</span>
              <span v-if="!$v.formData.price.maxLength" class="text-danger">{{ $t('validation.price_maxlength') }}</span>
              <span v-if="!$v.formData.price.minValue" class="text-danger">{{ $t('validation.price_minvalue') }}</span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('items.unit') }}</label>
          <div class="col-sm-7">
            <base-select
              v-model="formData.unit"
              :options="itemUnits"
              :searchable="true"
              :show-labels="false"
              label="name"
            >
              <div slot="afterList">
                <button type="button" class="list-add-button" @click="addItemUnit">
                  <font-awesome-icon class="icon" icon="cart-plus" />
                  <label>{{ $t('settings.customization.items.add_item_unit') }}</label>
                </button>
              </div>
            </base-select>
          </div>
        </div>
        <div v-if="isTexPerItem" class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('items.taxes') }}</label>
          <div class="col-sm-7">
            <base-select
              v-model="formData.taxes"
              :options="getTaxTypes"
              :searchable="true"
              :show-labels="false"
              :allow-empty="true"
              :multiple="true"
              label="tax_name"
            />
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('items.description') }}</label>
          <div class="col-sm-7">
            <base-text-area
              v-model="formData.description"
              rows="4"
              cols="50"
              @input="$v.formData.description.$touch()"
            />
            <div v-if="$v.formData.description.$error">
              <span v-if="!$v.formData.description.maxLength" class="text-danger">{{ $t('validation.description_maxlength') }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <base-button
          :outline="true"
          class="mr-3"
          color="theme"
          type="button"
          @click="closeItemModal"
        >
          {{ $t('general.cancel') }}
        </base-button>
        <base-button
          v-if="isEdit"
          :loading="isLoading"
          color="theme"
          @click="submitItemData"
        >
          {{ $t('general.update') }}
        </base-button>
        <base-button
          v-else
          :loading="isLoading"
          icon="save"
          color="theme"
          type="submit"
        >
          {{ $t('general.save') }}
        </base-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { validationMixin } from 'vuelidate'
const { required, minLength, numeric, maxLength, minValue } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return {
      isEdit: false,
      isLoading: false,
      tempData: null,
      units: [
        { name: 'box', value: 'box' },
        { name: 'cm', value: 'cm' },
        { name: 'dz', value: 'dz' },
        { name: 'ft', value: 'ft' },
        { name: 'g', value: 'g' },
        { name: 'in', value: 'in' },
        { name: 'kg', value: 'kg' },
        { name: 'km', value: 'km' },
        { name: 'lb', value: 'lb' },
        { name: 'mg', value: 'mg' },
        { name: 'pc', value: 'pc' }
      ],
      taxes: [],
      formData: {
        name: null,
        price: null,
        description: null,
        unit: null,
        taxes: []
      }
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3)
      },
      price: {
        required,
        numeric,
        minValue: minValue(0.1),
        maxLength: maxLength(20)
      },
      description: {
        maxLength: maxLength(255)
      }
    }
  },
  computed: {
    ...mapGetters('currency', [
      'defaultCurrencyForInput'
    ]),
    price: {
      get: function () {
        return this.formData.price / 100
      },
      set: function (newValue) {
        this.formData.price = newValue * 100
      }
    },
    // itemUnits () {
    //   return this.units
    // },
    ...mapGetters('modal', [
      'modalDataID',
      'modalData'
    ]),
    ...mapGetters('item', [
      'getItemById',
      'itemUnits'
    ]),
    ...mapGetters('taxType', [
      'taxTypes'
    ]),
    isTexPerItem () {
      return this.modalData.taxPerItem === 'YES'
    },
    getTaxTypes () {
      return this.taxTypes.map(tax => {
        return {...tax, tax_name: tax.name + ' (' + tax.percent + '%)'}
      })
    }
  },
  watch: {
    modalDataID () {
      this.isEdit = true
      this.fetchEditData()
    }
  },
  created () {
    if (this.modalDataID) {
      this.isEdit = true
      this.fetchEditData()
    }

    if (this.isEdit) {
      this.loadEditData()
    }
  },
  mounted () {
    this.$refs.name.focus = true
  },
  methods: {
    ...mapActions('modal', [
      'openModal',
      'closeModal',
      'resetModalData'
    ]),
    ...mapActions('item', [
      'addItem',
      'updateItem'
    ]),
    ...mapActions('invoice', [
      'setItem'
    ]),
    resetFormData () {
      this.formData = {
        name: null,
        price: null,
        description: null,
        unit: null,
        id: null
      }
      this.$v.$reset()
    },
    fetchEditData () {
      this.tempData = this.getItemById(this.modalDataID)
      if (this.tempData) {
        this.formData.name = this.tempData.name
        this.formData.price = this.tempData.price
        this.formData.description = this.tempData.description
        this.formData.unit = this.tempData.unit
        this.formData.id = this.tempData.id
      }
    },
    async submitItemData () {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      if (this.formData.unit) {
        this.formData.unit = this.formData.unit.name
      }
      this.isLoading = true
      let response
      if (this.isEdit) {
        response = await this.updateItem(this.formData)
      } else {
        let data = {
          ...this.formData,
          taxes: this.formData.taxes.map(tax => {
            return {
              tax_type_id: tax.id,
              amount: ((this.formData.price * tax.percent) / 100),
              percent: tax.percent,
              name: tax.name,
              collective_tax: 0
            }
          })
        }
        response = await this.addItem(data)
      }
      if (response.data) {
        window.toastr['success'](this.$tc('items.created_message'))
        this.setItem(response.data.item)
        window.hub.$emit('newItem', response.data.item)
        this.isLoading = false
        this.resetModalData()
        this.resetFormData()
        this.closeModal()
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async addItemUnit () {
      this.openModal({
        'title': 'Add Item Unit',
        'componentName': 'ItemUnit'
      })
    },
    closeItemModal () {
      this.resetFormData()
      this.closeModal()
      this.resetModalData()
    }
  }
}
</script>
