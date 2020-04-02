<template>
  <div class="category-modal">
    <form action="" @submit.prevent="submitCategoryData">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('expenses.category') }}<span class="required text-danger">*</span></label>
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
          <label class="col-sm-4 col-form-label input-label">{{ $t('expenses.description') }}</label>
          <div class="col-sm-7">
            <base-text-area
              v-model="formData.description"
              rows="4"
              cols="50"
              @input="$v.formData.description.$touch()"
            />
            <div v-if="$v.formData.description.$error">
              <span v-if="!$v.formData.name.maxLength" class="text-danger"> {{ $tc('validation.description_maxlength') }} </span>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <base-button
          :outline="true"
          class="mr-3"
          color="theme"
          @click="closeCategoryModal"
        >
          {{ $t('general.cancel') }}
        </base-button>
        <base-button
          :loading="isLoading"
          icon="save"
          color="theme"
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
const { required, minLength, maxLength } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
        description: null
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
      description: {
        maxLength: maxLength(255)
      }
    }
  },
  watch: {
    'modalDataID' (val) {
      if (val) {
        this.isEdit = true
        this.setData()
      } else {
        this.isEdit = false
      }
    },
    'modalActive' (val) {
      if (!this.modalActive) {
        this.resetFormData()
      }
    }
  },
  mounted () {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  destroyed () {

  },
  methods: {
    ...mapActions('modal', [
      'closeModal',
      'resetModalData'
    ]),
    ...mapActions('category', [
      'addCategory',
      'updateCategory'
    ]),
    resetFormData () {
      this.formData = {
        id: null,
        name: null,
        description: null
      }
      this.$v.formData.$reset()
    },
    async submitCategoryData () {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response
      if (!this.isEdit) {
        response = await this.addCategory(this.formData)
      } else {
        response = await this.updateCategory(this.formData)
      }

      if (response.data) {
        if (!this.isEdit) {
          window.toastr['success'](this.$t('settings.expense_category.created_message'))
        } else {
          window.toastr['success'](this.$t('settings.expense_category.updated_message'))
        }
        window.hub.$emit('newCategory', response.data.category)
        this.closeCategoryModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    async setData () {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
        description: this.modalData.description
      }
    },
    closeCategoryModal () {
      this.resetFormData()
      this.closeModal()
    }
  }
}
</script>
