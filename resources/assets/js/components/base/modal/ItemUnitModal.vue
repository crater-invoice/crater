<template>
  <form action="" @submit.prevent="submitItemUnit">
    <div class="p-8 sm:p-6">
      <sw-input-group
        :label="$t('settings.customization.items.unit_name')"
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
    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="closeItemUnitModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button
        :loading="isLoading"
        variant="primary"
        icon="save"
        type="submit"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ !isEdit ? $t('general.save') : $t('general.update') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
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
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
    },
  },

  async mounted() {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('item', ['addItemUnit', 'updateItemUnit', 'fatchItemUnit']),
    ...mapActions('notification', ['showNotification']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },
    async submitItemUnit() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response

      try {
        if (!this.isEdit) {
          response = await this.addItemUnit(this.formData)
        } else {
          response = await this.updateItemUnit(this.formData)
        }

        if (response.data) {
          this.isLoading = false
          if (!this.isEdit) {
            this.showNotification({
              type: 'success',
              message: this.$t('settings.customization.items.item_unit_added'),
            })
          } else {
            this.showNotification({
              type: 'success',
              message: this.$t(
                'settings.customization.items.item_unit_updated'
              ),
            })
          }
          this.refreshData ? this.refreshData() : ''
          this.closeItemUnitModal()
          return true
        }
      } catch (error) {
        this.isLoading = false
        this.showNotification({
          type: 'error',
          message: response.data.error,
        })
      }
    },
    async setData() {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
      }
    },
    closeItemUnitModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
