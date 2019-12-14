<template>
  <div class="tax-type-modal">
    <form action="" @submit.prevent="submitTaxTypeData">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('tax_types.name') }} <span class="required"> *</span></label>
          <div class="col-sm-7">
            <base-input
              ref="name"
              :invalid="$v.formData.name.$error"
              v-model="formData.name"
              type="text"
              @input="$v.formData.name.$touch()"
            />
            <div v-if="$v.formData.name.$error">
              <span v-if="!$v.formData.name.required" class="form-group__message text-danger">{{ $tc('validation.required') }}</span>
              <span v-if="!$v.formData.name.minLength" class="form-group__message text-danger"> {{ $tc('validation.name_min_length', $v.formData.name.$params.minLength.min, { count: $v.formData.name.$params.minLength.min }) }} </span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('tax_types.percent') }} <span class="required"> *</span></label>
          <div class="col-sm-7">
            <div class="base-input">
              <money
                :class="{'invalid' : $v.formData.percent.$error}"
                v-model="formData.percent"
                v-bind="defaultInput"
                class="input-field"
              />
            </div>
            <div v-if="$v.formData.percent.$error">
              <span v-if="!$v.formData.percent.required" class="text-danger">{{ $t('validation.required') }}</span>
              <span v-if="!$v.formData.percent.between" class="form-group__message text-danger">{{ $t('validation.enter_valid_tax_rate') }}</span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('tax_types.description') }}</label>
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
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('tax_types.compound_tax') }}</label>
          <div class="col-sm-7 mr-4">
            <base-switch
              v-model="formData.compound_tax"
              class="btn-switch compound-tax-toggle"
            />
          </div>
        </div>
      </div>
      <div class="card-footer">
        <base-button
          :outline="true"
          class="mr-3"
          color="theme"
          type="button"
          @click="closeTaxModal"
        >
          {{ $t('general.cancel') }}
        </base-button>
        <base-button
          :loading="isLoading"
          color="theme"
          icon="save"
          type="submit"
        >
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </base-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { validationMixin } from 'vuelidate'
const { required, minLength, between, maxLength } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
        percent: '',
        description: null,
        compound_tax: false,
        collective_tax: 0
      },
      defaultInput: {
        decimal: '.',
        thousands: ',',
        prefix: '% ',
        precision: 2,
        masked: false
      }
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive'
    ])
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3)
      },
      percent: {
        required,
        between: between(0, 100)
      },
      description: {
        maxLength: maxLength(255)
      }
    }
  },
  // watch: {
  //   'modalDataID' (val) {
  //     if (val) {
  //       this.isEdit = true
  //       this.setData()
  //     } else {
  //       this.isEdit = false
  //     }
  //   },
  //   'modalActive' (val) {
  //     if (!this.modalActive) {
  //       this.resetFormData()
  //     }
  //   }
  // },
  async mounted () {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
      // this.resetFormData()
    }
  },
  methods: {
    ...mapActions('modal', [
      'closeModal',
      'resetModalData'
    ]),
    ...mapActions('taxType', [
      'addTaxType',
      'updateTaxType',
      'fetchTaxType'
    ]),
    resetFormData () {
      this.formData = {
        id: null,
        name: null,
        percent: null,
        description: null,
        collective_tax: 0
      }
      this.$v.formData.$reset()
    },
    async submitTaxTypeData () {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response
      if (!this.isEdit) {
        response = await this.addTaxType(this.formData)
      } else {
        response = await this.updateTaxType(this.formData)
      }
      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](this.$t('settings.tax_types.created_message'))
        } else {
          window.toastr['success'](this.$t('settings.tax_types.updated_message'))
        }
        window.hub.$emit('newTax', response.data.taxType)
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData () {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
        percent: this.modalData.percent,
        description: this.modalData.description,
        compound_tax: this.modalData.compound_tax ? true : false
      }
    },
    closeTaxModal () {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    }
  }
}
</script>
