<template>
  <sw-wizard-step
    :title="$t('wizard.account_info')"
    :description="$t('wizard.account_info_desc')"
  >
    <form action="" @submit.prevent="next()">
      <div class="grid grid-cols-1 mb-4 md:grid-cols-2 md:mb-6">
        <sw-input-group
          :label="$tc('settings.account_settings.profile_picture')"
        >
          <sw-avatar
            :preview-avatar="previewAvatar"
            :label="$tc('general.choose_file')"
            @changed="onChange"
            @uploadHandler="onUploadHandler"
            @handleUploadError="onHandleUploadError"
          >
            <template v-slot:icon>
              <cloud-upload-icon
                class="h-5 mb-2 text-xl leading-6 text-gray-400"
              />
            </template>
          </sw-avatar>
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
        <sw-input-group :label="$t('wizard.name')" :error="nameError" required>
          <sw-input
            :invalid="$v.profileData.name.$error"
            v-model.trim="profileData.name"
            type="text"
            name="name"
            @input="$v.profileData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.email')"
          :error="emailError"
          required
        >
          <sw-input
            :invalid="$v.profileData.email.$error"
            v-model.trim="profileData.email"
            type="text"
            name="email"
            @input="$v.profileData.email.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
        <sw-input-group
          :label="$t('wizard.password')"
          :error="passwordError"
          required
        >
          <sw-input
            :invalid="$v.profileData.password.$error"
            v-model.trim="profileData.password"
            type="password"
            name="password"
            @input="$v.profileData.password.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.confirm_password')"
          :error="confirmPasswordError"
          required
        >
          <sw-input
            :invalid="$v.profileData.confirm_password.$error"
            v-model.trim="profileData.confirm_password"
            type="password"
            name="confirm_password"
            @input="$v.profileData.confirm_password.$touch()"
          />
        </sw-input-group>
      </div>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('wizard.save_cont') }}
      </sw-button>
    </form>
  </sw-wizard-step>
</template>

<script>
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import { mapActions } from 'vuex'
const {
  required,
  requiredIf,
  sameAs,
  minLength,
  email,
} = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
  },
  data() {
    return {
      profileData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null,
      },
      isLoading: false,
      previewAvatar: '/images/default-avatar.jpg',
      fileObject: null,
      cropperOutputMime: '',
    }
  },
  validations: {
    profileData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        email,
        required,
      },
      password: {
        required,
        minLength: minLength(8),
      },
      confirm_password: {
        required: requiredIf('isRequired'),
        sameAsPassword: sameAs('password'),
      },
    },
  },
  computed: {
    emailError() {
      if (!this.$v.profileData.email.$error) {
        return ''
      }
      if (!this.$v.profileData.email.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.profileData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    nameError() {
      if (!this.$v.profileData.name.$error) {
        return ''
      }
      if (!this.$v.profileData.name.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.profileData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.profileData.name.$params.minLength.min,
          { count: this.$v.profileData.name.$params.minLength.min }
        )
      }
    },
    passwordError() {
      if (!this.$v.profileData.password.$error) {
        return ''
      }
      if (!this.$v.profileData.password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.profileData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.profileData.password.$params.minLength.min,
          { count: this.$v.profileData.password.$params.minLength.min }
        )
      }
    },
    confirmPasswordError() {
      if (!this.$v.profileData.confirm_password.$error) {
        return ''
      }
      if (!this.$v.profileData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },
    isRequired() {
      if (
        this.profileData.password === null ||
        this.profileData.password === undefined ||
        this.profileData.password === ''
      ) {
        return false
      }
      return true
    },
  },
  methods: {
    ...mapActions('user', ['uploadAvatar']),
    ...mapActions('notification', ['showNotification']),
    onUploadHandler(cropper) {
      this.previewAvatar = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    onHandleUploadError() {
      this.showNotification({
        type: 'error',
        message: 'Oops! Something went wrong...',
      })
    },
    onChange(file) {
      this.cropperOutputMime = file.type
      this.fileObject = file
    },
    async next() {
      this.$v.profileData.$touch()
      if (this.$v.profileData.$invalid) {
        return true
      }
      this.isLoading = true
      let response = await window.axios.put('/api/v1/me', this.profileData)

      if (response.data) {
        if (this.fileObject && this.previewAvatar) {
          let avatarData = new FormData()
          avatarData.append(
            'admin_avatar',
            JSON.stringify({
              name: this.fileObject.name,
              data: this.previewAvatar,
              id: response.data.user.id,
            })
          )

          this.uploadAvatar(avatarData)
        }
        this.$emit('next', 6)
        this.isLoading = false
      }
      return true
    },
  },
}
</script>

<style lang="scss">
.avatar-upload {
  @keyframes fadeIn {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
}
</style>
