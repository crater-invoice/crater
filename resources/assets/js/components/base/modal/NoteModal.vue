<template>
  <div class="note-modal">
    <form action="" @submit.prevent="submitNote">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('settings.customization.notes.name')"
          :error="nameError"
          class="mb-4"
          variant="vertical"
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
          :label="$t('settings.customization.notes.type')"
          :error="typeError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-select
            v-model="noteType"
            :options="types"
            :allow-empty="false"
            :show-labels="false"
            class="mt-2"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.customization.notes.notes')"
          :error="noteError"
          variant="vertical"
          required
        >
          <base-custom-input
            v-model="formData.notes"
            :fields="fields"
            class="mt-2"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end px-4 py-4 border-t border-solid border-gray-light"
      >
        <sw-button
          class="mr-2"
          variant="primary-outline"
          type="button"
          @click="closeNoteModal"
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
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')
export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      types: ['Invoice', 'Estimate', 'Payment'],
      selectType: null,
      formData: {
        type: '',
        name: '',
        notes: '',
      },
      noteType: null,
      fields: [],
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
    noteError() {
      if (!this.$v.formData.notes.$error) {
        return ''
      }

      if (!this.$v.formData.notes.required) {
        return this.$tc('validation.required')
      }
    },
    typeError() {
      if (!this.$v.noteType.$error) {
        return ''
      }

      if (!this.$v.noteType.required) {
        return this.$tc('validation.required')
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
      notes: {
        required,
      },
    },
    noteType: {
      required,
    },
  },
  watch: {
    noteType() {
      this.setFields()
    },
  },
  async mounted() {
    this.setFields()
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    } else {
      this.modalData
        ? (this.noteType = this.modalData)
        : (this.noteType = 'Invoice')
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('notes', ['addNote', 'updateNote']),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('invoice', {
      setInvoiceNote: 'selectNote',
    }),
    ...mapActions('estimate', {
      setEstimateNote: 'selectNote',
    }),
    ...mapActions('payment', {
      setPaymentNote: 'selectNote',
    }),

    setFields() {
      this.fields = ['customer', 'customerCustom']

      if (this.noteType === 'Invoice') {
        this.fields.push('invoice', 'invoiceCustom')
      }

      if (this.noteType === 'Estimate') {
        this.fields.push('estimate', 'estimateCustom')
      }

      if (this.noteType === 'Payment') {
        this.fields.push('payment', 'paymentCustom')
      }

      return true
    },
    resetFormData() {
      this.formData = {
        name: null,
        notes: null,
      }

      this.notetype = null
      this.$v.formData.$reset()
    },
    async submitNote() {
      this.$v.formData.$touch()
      this.$v.noteType.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      if (this.isEdit) {
        let data = {
          id: this.modalDataID,
          type: this.noteType,
          name: this.formData.name,
          notes: this.formData.notes,
        }

        let res = await this.updateNote(data)
        if (res.data) {
          this.showNotification({
            type: 'success',
            message: this.$t('settings.customization.notes.note_updated'),
          })

          this.refreshData ? this.refreshData() : ''
          this.closeNoteModal()
          return true
        }
        this.showNotification({
          type: 'error',
          message: res.data.error,
        })
      } else {
        try {
          let data = {
            type: this.noteType,
            name: this.formData.name,
            notes: this.formData.notes,
          }

          let response = await this.addNote(data)

          if (response.data && response.data.note) {
            this.isLoading = false
            this.showNotification({
              type: 'success',
              message: this.$t('settings.customization.notes.note_added'),
            })
            if (
              (this.$route.name === 'invoices.create' &&
                response.data.note.type === 'Invoice') ||
              (this.$route.name === 'invoices.edit' &&
                response.data.note.type === 'Invoice')
            ) {
              this.setInvoiceNote(response.data.note)
            }

            if (
              (this.$route.name === 'estimates.create' &&
                response.data.note.type === 'Estimate') ||
              (this.$route.name === 'estimates.edit' &&
                response.data.note.type === 'Estimate')
            ) {
              this.setEstimateNote(response.data.note)
            }

            if (
              (this.$route.name === 'payments.create' &&
                response.data.note.type === 'Payment') ||
              (this.$route.name === 'payments.edit' &&
                response.data.note.type === 'Payment')
            ) {
              this.setPaymentNote(response.data.note)
            }

            this.refreshData ? this.refreshData() : ''
            this.closeNoteModal()
            return true
          }
          this.showNotification({
            type: 'error',
            message: response.data.error,
          })
        } catch (err) {
          if (err.response.data.errors.name) {
            this.isLoading = true
          }
        }
      }
    },
    async setData() {
      this.noteType = this.modalData.type
      this.formData.name = this.modalData.name
      this.formData.notes = this.modalData.notes
    },
    closeNoteModal() {
      this.closeModal()
      this.resetFormData()
    },
  },
}
</script>
<style lang="scss">
.note-modal {
  .header-editior .editor-menu-bar {
    margin-left: 0.5px;
    margin-right: 0px;
  }
}
</style>
