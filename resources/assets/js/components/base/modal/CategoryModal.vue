<template>
  <form action="" @submit.prevent="submitCategoryData">
    <div class="p-8 sm:p-6">
      <sw-input-group
        :label="$t('expenses.category')"
        :error="nameError"
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
        :label="$t('expenses.description')"
        :error="descriptionError"
        class="mt-5"
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
      class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid border-modal-bg"
    >
      <sw-button
        type="button"
        variant="primary-outline"
        class="mr-3 text-sm"
        @click="closeCategoryModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button :loading="isLoading" variant="primary" type="submit">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ !isEdit ? $t('general.save') : $t('general.update') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength, maxLength } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
        description: null,
      },
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
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
    descriptionError() {
      if (!this.$v.formData.description.$error) {
        return ''
      }
      if (!this.$v.formData.name.maxLength) {
        return this.$tc('validation.description_maxlength')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      description: {
        maxLength: maxLength(255),
      },
    },
  },
  watch: {
    modalDataID(val) {
      if (val) {
        this.isEdit = true
        this.setData()
      } else {
        this.isEdit = false
      }
    },
    modalActive(val) {
      if (!this.modalActive) {
        this.resetFormData()
      }
    },
  },

  mounted() {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },

  methods: {
    ...mapActions('modal', ['closeModal']),
    ...mapActions('category', ['addCategory', 'updateCategory']),
    ...mapActions('notification', ['showNotification']),

    resetFormData() {
      this.formData = {
        id: null,
        name: null,
        description: null,
      }
      this.$v.formData.$reset()
    },
    async submitCategoryData() {
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
          this.showNotification({
            type: 'success',
            message: this.$t('settings.expense_category.created_message'),
          })
        } else {
          this.showNotification({
            type: 'success',
            message: this.$t('settings.expense_category.updated_message'),
          })
        }
        window.hub.$emit('newCategory', response.data.category)
        this.refreshData ? this.refreshData() : ''
        this.closeCategoryModal()
        this.isLoading = false
        return true
      }
      this.showNotification({
        type: 'error',
        message: response.data.error,
      })
    },
    async setData() {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
        description: this.modalData.description,
      }
    },
    closeCategoryModal() {
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
