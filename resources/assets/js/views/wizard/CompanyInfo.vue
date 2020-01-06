<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.company_info') }}</p>
      <p class="form-desc">{{ $t('wizard.company_info_desc') }}</p>
      <div class="row mb-4">
        <div class="col-md-6">
          <label class="input-label">{{ $tc('settings.company_info.company_logo') }}</label>
          <div id="pick-avatar" class="image-upload-box">
            <div class="overlay">
              <font-awesome-icon class="white-icon" icon="camera"/>
            </div>
            <img v-if="previewLogo" :src="previewLogo" class="preview-logo">
            <div v-else class="upload-content">
              <font-awesome-icon class="upload-icon" icon="cloud-upload-alt"/>
              <p class="upload-text"> {{ $t('general.choose_file') }} </p>
            </div>
          </div>
        </div>
        <avatar-cropper
          :labels="{ submit: 'Submit', cancel: 'Cancel'}"
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
          <label class="form-label">{{ $t('wizard.company_name') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.companyData.name.$error"
            v-model.trim="companyData.name"
            type="text"
            name="name"
            @input="$v.companyData.name.$touch()"
          />
          <div v-if="$v.companyData.name.$error">
            <span v-if="!$v.companyData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.country') }}</label><span class="text-danger"> *</span>
          <base-select
            v-model="country"
            :class="{'error': $v.companyData.country_id.$error }"
            :options="countries"
            :searchable="true"
            :allow-empty="false"
            :show-labels="false"
            :placeholder="$t('general.select_country')"
            track-by="id"
            label="name"
          />
          <div v-if="$v.companyData.country_id.$error">
            <span v-if="!$v.companyData.country_id.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.state') }}</label>
          <base-input
            v-model="companyData.state"
            name="state"
            type="text"
          />
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.city') }}</label>
          <base-input
            v-model="companyData.city"
            name="city"
            type="text"
          />
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.address') }}</label>
          <base-text-area
            :invalid="$v.companyData.address_street_1.$error"
            v-model.trim="companyData.address_street_1"
            :placeholder="$t('general.street_1')"
            name="billing_street1"
            rows="2"
            @input="$v.companyData.address_street_1.$touch()"
          />
          <div v-if="$v.companyData.address_street_1.$error">
            <span v-if="!$v.companyData.address_street_1.maxLength" class="text-danger">{{ $t('validation.description_maxlength') }}</span>
          </div>
          <base-text-area
            :invalid="$v.companyData.address_street_2.$error"
            v-model="companyData.address_street_2"
            :placeholder="$t('general.street_2')"
            name="billing_street2"
            rows="2"
            @input="$v.companyData.address_street_2.$touch()"
          />
          <div v-if="$v.companyData.address_street_2.$error">
            <span v-if="!$v.companyData.address_street_2.maxLength" class="text-danger">{{ $t('validation.description_maxlength') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <label class="form-label">{{ $t('wizard.zip_code') }}</label>
              <base-input
                v-model.trim="companyData.zip"
                type="text"
                name="zip"
              />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <label class="form-label">{{ $t('wizard.phone') }}</label>
              <base-input
                v-model.trim="companyData.phone"
                type="text"
                name="phone"
              />
            </div>
          </div>
        </div>
      </div>
      <base-button
        :loading="loading"
        class="pull-right"
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
import MultiSelect from 'vue-multiselect'
import AvatarCropper from 'vue-avatar-cropper'
import { validationMixin } from 'vuelidate'
const { required, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect,
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
      companyData: {
        logo: '',
        name: null,
        address_street_1: '',
        address_street_2: '',
        city: '',
        state: '',
        country_id: '',
        zip: '',
        phone: ''
      },
      loading: false,
      step: 1,
      countries: [],
      country: null,
      previewLogo: null
    }
  },
  validations: {
    companyData: {
      name: {
        required
      },
      country_id: {
        required
      },
      address_street_1: {
        maxLength: maxLength(255)
      },
      address_street_2: {
        maxLength: maxLength(255)
      }
    }
  },
  watch: {
    country ({ id }) {
      this.companyData.country_id = id
      return true
    }
  },
  mounted () {
    this.fetchCountry()
  },
  methods: {
    cropperHandler (cropper) {
      this.previewLogo = cropper.getCroppedCanvas().toDataURL(this.cropperOutputMime)
    },
    setFileObject (file) {
      this.fileObject = file
    },
    handleUploadError (message, type, xhr) {
      window.toastr['error']('Oops! Something went wrong...')
    },
    async next () {
      this.$v.companyData.$touch()
      if (this.$v.companyData.$invalid) {
        return true
      }
      this.loading = true
      let response = await window.axios.post('/api/admin/onboarding/company', this.companyData)

      if (response.data) {
        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append('company_logo', JSON.stringify({
            name: this.fileObject.name,
            data: this.previewLogo
          }))

          await axios.post('/api/admin/onboarding/company/upload-logo', logoData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'company': response.data.user.company.id
            }
          })
        }

        this.$emit('next')
        this.loading = false
      }
    },
    onFileChange (e) {
      var input = event.target
      this.companyData.logo = input.files[0]
      if (input.files && input.files[0]) {
        var reader = new FileReader()
        reader.onload = (e) => {
          this.previewLogo = e.target.result
        }
        reader.readAsDataURL(input.files[0])
      }
    },
    async fetchCountry () {
      let res = await window.axios.get('/api/countries')
      if (res) {
        this.countries = res.data.countries
      }
    }
  }
}
</script>
