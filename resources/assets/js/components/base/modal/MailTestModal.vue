<template>
  <div class="mail-test-modal">
    <form action="" @submit.prevent="onTestMailSend">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('general.to') }} <span class="required"> *</span></label>
          <div class="col-sm-7">
            <base-input
              ref="to"
              :invalid="$v.formData.to.$error"
              v-model="formData.to"
              type="text"
              @input="$v.formData.to.$touch()"
            />
            <div v-if="$v.formData.to.$error">
              <span v-if="!$v.formData.to.required" class="form-group__message text-danger">{{ $tc('validation.required') }}</span>
              <span v-if="!$v.formData.to.email" class="form-group__message text-danger"> {{ $t('validation.email_incorrect') }} </span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('general.subject') }} <span class="required"> *</span></label>
          <div class="col-sm-7">
            <div class="base-input">
              <base-input
                :invalid="$v.formData.subject.$error"
                v-model="formData.subject"
                type="text"
                @input="$v.formData.subject.$touch()"
              />
            </div>
            <div v-if="$v.formData.subject.$error">
              <span v-if="!$v.formData.subject.required" class="text-danger">{{ $t('validation.required') }}</span>
              <span v-if="!$v.formData.subject.maxLength" class="text-danger">{{ $t('validation.subject_maxlength') }}</span>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label input-label">{{ $t('general.message') }}<span class="required"> *</span></label>
          <div class="col-sm-7">
            <base-text-area
              v-model="formData.message"
              :invalid="$v.formData.message.$error"
              rows="4"
              cols="50"
              @input="$v.formData.message.$touch()"
            />
            <div v-if="$v.formData.message.$error">
              <span v-if="!$v.formData.message.required" class="text-danger">{{ $t('validation.required') }}</span>
              <span v-if="!$v.formData.message.maxLength" class="text-danger">{{ $t('validation.message_maxlength') }}</span>
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
const { required, minLength, email, maxLength } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        to: null,
        subject: null,
        message: null
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
      to: {
        required,
        email
      },
      subject: {
        required,
        maxLength: maxLength(100)
      },
      message: {
        required,
        maxLength: maxLength(255)
      }
    }
  },
  async mounted () {
    this.$refs.to.focus = true
  },
  methods: {
    ...mapActions('modal', [
      'closeModal',
      'resetModalData'
    ]),
    resetFormData () {
      this.formData = {
        to: null,
        subject: null,
        message: null
      }
      this.$v.formData.$reset()
    },
    async onTestMailSend () {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response = await axios.post('/api/settings/test/mail', this.formData)
      if (response.data) {

        if (response.data.success) {
          window.toastr['success'](this.$tc('general.send_mail_successfully'))
          this.closeTaxModal()
          this.isLoading = false
          return true
        }

        window.toastr['error'](this.$tc('validation.something_went_wrong'))
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    closeTaxModal () {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    }
  }
}
</script>
