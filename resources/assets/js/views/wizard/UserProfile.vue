<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.account_info') }}</p>
      <p class="form-desc">{{ $t('wizard.account_info_desc') }}</p>
      <div class="row mb-4">
        <div class="col-md-6">
          <label class="form-label">{{ $tc('settings.account_settings.profile_picture') }}</label>
          <div id="pick-avatar" class="image-upload-box avatar-upload">
            <div class="overlay">
              <font-awesome-icon class="white-icon" icon="camera"/>
            </div>
            <img v-if="previewAvatar" :src="previewAvatar" class="preview-logo">
            <div v-else class="upload-content">
              <font-awesome-icon class="upload-icon" icon="cloud-upload-alt"/>
              <p class="upload-text"> {{ $tc('general.choose_file') }} </p>
            </div>
          </div>
        </div>
        <avatar-cropper
          :labels="{ submit: 'submit', cancel: 'Cancel'}"
          :cropper-options="cropperOptions"
          :output-options="cropperOutputOptions"
          :output-quality="0.8"
          :upload-handler="cropperHandler"
          trigger="#pick-avatar"
          @changed="setFileObject"
          @error="handleUploadError"
        />
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.name') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.name.$error"
            v-model.trim="profileData.name"
            type="text"
            name="name"
            @input="$v.profileData.name.$touch()"
          />
          <div v-if="$v.profileData.name.$error">
            <span v-if="!$v.profileData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
            <span v-if="!$v.profileData.name.minLength" class="text-danger"> {{ $tc('validation.name_min_length', $v.profileData.name.$params.minLength.min, { count: $v.profileData.name.$params.minLength.min }) }} </span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.email') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.email.$error"
            v-model.trim="profileData.email"
            type="text"
            name="email"
            @input="$v.profileData.email.$touch()"
          />
          <div v-if="$v.profileData.email.$error">
            <span v-if="!$v.profileData.email.required" class="text-danger">{{ $tc('validation.required') }}</span>
            <span v-if="!$v.profileData.email.email" class="text-danger">{{ $tc('validation.email_incorrect') }}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.password') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.password.$error"
            v-model.trim="profileData.password"
            type="password"
            name="password"
            @input="$v.profileData.password.$touch()"
          />
          <div v-if="$v.profileData.password.$error">
            <span v-if="!$v.profileData.password.required" class="text-danger">{{ $tc('validation.required') }}</span>
            <span v-if="!$v.profileData.password.minLength" class="text-danger"> {{ $tc('validation.password_min_length', $v.profileData.password.$params.minLength.min, {count: $v.profileData.password.$params.minLength.min}) }} </span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.confirm_password') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.confirm_password.$error"
            v-model.trim="profileData.confirm_password"
            type="password"
            name="confirm_password"
            @input="$v.profileData.confirm_password.$touch()"
          />
          <div v-if="$v.profileData.confirm_password.$error">
            <span v-if="!$v.profileData.confirm_password.sameAsPassword" class="text-danger">{{ $tc('validation.password_incorrect') }}</span>
          </div>
        </div>
      </div>
      <base-button
        :loading="loading"
        class="pull-right mt-4"
        icon="save"
        color="theme"
        type="submit"
      >
        {{ $t('wizard.save_cont') }}
      </base-button>
    </form>
  </div>
</template>
<script>
import AvatarCropper from 'vue-avatar-cropper'
import { validationMixin } from 'vuelidate'
import { mapActions } from 'vuex'
const { required, requiredIf, sameAs, minLength, email } = require('vuelidate/lib/validators')

export default {
  components: {
    AvatarCropper
  },
  mixins: [validationMixin],
  data () {
    return {
      cropperOutputOptions: {
        width: 150,
        height: 150
      },
      cropperOptions: {
        autoCropArea: 1,
        viewMode: 0,
        movable: true,
        zoomable: true
      },
      profileData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null
      },
      loading: false,
      previewAvatar: '/images/default-avatar.jpg',
      fileObject: null
    }
  },
  validations: {
    profileData: {
      name: {
        required,
        minLength: minLength(3)
      },
      email: {
        email,
        required
      },
      password: {
        required,
        minLength: minLength(8)
      },
      confirm_password: {
        required: requiredIf('isRequired'),
        sameAsPassword: sameAs('password')
      }
    }
  },
  computed: {
    isRequired () {
      if (this.profileData.password === null || this.profileData.password === undefined || this.profileData.password === '') {
        return false
      }
      return true
    }
  },
  methods: {
    ...mapActions('userProfile', [
      'uploadOnboardAvatar'
    ]),
    cropperHandler (cropper) {
      this.previewAvatar = cropper.getCroppedCanvas().toDataURL(this.cropperOutputMime)
    },
    setFileObject (file) {
      this.fileObject = file
    },
    handleUploadError (message, type, xhr) {
      window.toastr['error']('Oops! Something went wrong...')
    },
    async next () {
      this.$v.profileData.$touch()
      if (this.$v.profileData.$invalid) {
        return true
      }
      this.loading = true
      let response = await window.axios.post('/api/admin/onboarding/profile', this.profileData)
      console.log('user_id', response.data.user.id)

      if (response.data) {
        if (this.fileObject && this.previewAvatar) {
          let avatarData = new FormData()
          avatarData.append('admin_avatar', JSON.stringify({
            name: this.fileObject.name,
            data: this.previewAvatar,
            id: response.data.user.id
          }))

          this.uploadOnboardAvatar(avatarData)
        }
        this.$emit('next')
        this.loading = false
      }
      return true
    }
  }
}
</script>
