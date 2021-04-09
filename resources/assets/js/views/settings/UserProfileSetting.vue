<template>
  <form class="relative h-full" @submit.prevent="updateUserData">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.account_settings.account_settings') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.account_settings.section_description') }}
        </p>
      </template>

      <div class="grid mb-4 md:grid-cols-6">
        <div>
          <label
            class="text-sm not-italic font-medium leading-4 text-black whitespace-nowrap"
          >
            {{ $tc('settings.account_settings.profile_picture') }}
          </label>
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
        </div>
      </div>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.account_settings.name')"
          :error="nameError"
        >
          <sw-input
            v-model="formData.name"
            :invalid="$v.formData.name.$error"
            :placeholder="$t('settings.user_profile.name')"
            class="mt-2"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.account_settings.email')"
          :error="emailError"
        >
          <sw-input
            v-model="formData.email"
            :invalid="$v.formData.email.$error"
            :placeholder="$t('settings.user_profile.email')"
            class="mt-2"
            @input="$v.formData.email.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.account_settings.password')"
          :error="passwordError"
        >
          <sw-input
            v-model="formData.password"
            :invalid="$v.formData.password.$error"
            :placeholder="$t('settings.user_profile.password')"
            type="password"
            class="mt-2"
            @input="$v.formData.password.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.account_settings.confirm_password')"
          :error="confirmPasswordError"
          class="mt-1 mb-2"
        >
          <sw-input
            v-model="formData.confirm_password"
            :invalid="$v.formData.confirm_password.$error"
            :placeholder="$t('settings.user_profile.confirm_password')"
            type="password"
            @input="$v.formData.confirm_password.$touch()"
          />
        </sw-input-group>
      </div>

      <div class="grid gap-6 mt-4 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.language')"
          :error="languageError"
        >
          <sw-select
            v-model="language"
            :options="languages"
            :class="{ error: $v.language.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$tc('settings.preferences.select_language')"
            class="mt-2"
            label="name"
            track-by="code"
          />
        </sw-input-group>
      </div>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        class="mt-6"
        variant="primary"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $tc('settings.account_settings.save') }}
      </sw-button>
    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import BaseLoader from '../../components/base/BaseLoader.vue'

const {
  required,
  requiredIf,
  sameAs,
  email,
  minLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
    BaseLoader,
  },

  data() {
    return {
      formData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null,
      },
      isLoading: false,
      previewAvatar: null,
      cropperOutputMime: '',
      fileObject: null,
      language: null,
      isRequestOnGoing: false,
    }
  },

  validations: {
    formData: {
      name: {
        required,
      },
      email: {
        required,
        email,
      },
      password: {
        minLength: minLength(8),
      },
      confirm_password: {
        sameAsPassword: sameAs('password'),
      },
    },
    language: {
      required,
    },
  },

  computed: {
    ...mapGetters(['languages']),

    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    confirmPasswordError() {
      if (!this.$v.formData.confirm_password.$error) {
        return ''
      }

      if (!this.$v.formData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },

    languageError() {
      if (!this.$v.language.$error) {
        return ''
      }
      if (!this.$v.language.required) {
        return this.$tc('validation.required')
      }
    },
  },

  watch: {
    'formData.password'(val) {
      if (!val) {
        this.formData.confirm_password = ''
      }
    },
  },

  mounted() {
    this.setInitialData()
    this.fetchLanguages()
  },

  methods: {
    ...mapActions('user', [
      'fetchCurrentUser',
      'updateCurrentUser',
      'fetchUserSettings',
      'updateUserSettings',
      'uploadAvatar',
    ]),

    ...mapActions(['fetchLanguages']),

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

    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()

      this.formData.name = response.data.user.name
      this.formData.email = response.data.user.email

      if (response.data.user.avatar) {
        this.previewAvatar = response.data.user.avatar
      } else {
        this.previewAvatar = '/images/default-avatar.jpg'
      }

      let res = await this.fetchUserSettings(['language'])

      this.language = this.languages.find(
        (language) => language.code == res.data.language
      )
      this.isRequestOnGoing = false
    },

    async updateUserData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        name: this.formData.name,
        email: this.formData.email,
      }
      try {
        if (
          this.formData.password != null &&
          this.formData.password !== undefined &&
          this.formData.password !== ''
        ) {
          data = { ...data, password: this.formData.password }
        }

        let response = await this.updateCurrentUser(data)

        let languageData = {
          settings: {
            language: this.language.code,
          },
        }

        let languageRes = await this.updateUserSettings(languageData)

        if (response.data.success) {
          this.isLoading = false

          if (this.fileObject && this.previewAvatar) {
            let avatarData = new FormData()

            avatarData.append(
              'admin_avatar',
              JSON.stringify({
                name: this.fileObject.name,
                data: this.previewAvatar,
              })
            )

            this.uploadAvatar(avatarData)
          }
          this.showNotification({
            type: 'success',
            message: this.$t('settings.account_settings.updated_message'),
          })

          this.formData.password = ''
          this.formData.confirm_password = ''
        }
      } catch (error) {
        this.isLoading = false
        return true
      }
    },
  },
}
</script>
