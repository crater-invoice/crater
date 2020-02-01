<template>
  <div class="setting-main-container">
    <form action="" @submit.prevent="updateCompany">
      <div class="card setting-card">
        <div class="page-header">
          <h3 class="page-title">{{ $t('settings.company_info.company_info') }}</h3>
          <p class="page-sub-title">
            {{ $t('settings.company_info.section_description') }}
          </p>
        </div>
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
                <p class="upload-text"> {{ $tc('general.choose_file') }} </p>
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
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.company_name') }}</label> <span class="text-danger"> * </span>
            <base-input
              v-model="formData.name"
              :invalid="$v.formData.name.$error"
              :placeholder="$t('settings.company_info.company_name')"
              @input="$v.formData.name.$touch()"
            />
            <div v-if="$v.formData.name.$error">
              <span v-if="!$v.formData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.phone') }}</label>
            <base-input
              v-model="formData.phone"
              :placeholder="$t('settings.company_info.phone')"
            />
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.country') }}</label><span class="text-danger"> * </span>
            <base-select
              v-model="country"
              :options="countries"
              :class="{'error': $v.formData.country_id.$error }"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$t('general.select_country')"
              label="name"
              track-by="id"
            />
            <div v-if="$v.formData.country_id.$error">
              <span v-if="!$v.formData.country_id.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.state') }}</label>
            <base-input
              v-model="formData.state"
              :placeholder="$tc('settings.company_info.state')"
              name="state"
              type="text"
            />
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.city') }}</label>
            <base-input
              v-model="formData.city"
              :placeholder="$tc('settings.company_info.city')"
              name="city"
              type="text"
            />
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.zip') }}</label>
            <base-input
              v-model="formData.zip"
              :placeholder="$tc('settings.company_info.zip')"
            />
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.address') }}</label>
            <base-text-area
              v-model="formData.address_street_1"
              :placeholder="$tc('general.street_1')"
              :class="{'invalid': $v.formData.address_street_1.$error }"
              rows="2"
              @input="$v.formData.address_street_1.$touch()"
            />
            <div v-if="$v.formData.address_street_1.$error">
              <span v-if="!$v.formData.address_street_1.maxLength" class="text-danger">{{ $tc('validation.address_maxlength') }}</span>
            </div>
            <base-text-area
              v-model="formData.address_street_2"
              :placeholder="$tc('general.street_2')"
              :class="{'invalid': $v.formData.address_street_2.$error }"
              rows="2"
              @input="$v.formData.address_street_2.$touch()"
            />
            <div v-if="$v.formData.address_street_2.$error">
              <span v-if="!$v.formData.address_street_2.maxLength" class="text-danger">{{ $tc('validation.address_maxlength') }}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <base-button
              :loading="isLoading"
              :disabled="isLoading"
              icon="save"
              color="theme"
              type="submit"
            >
              {{ $tc('settings.company_info.save') }}
            </base-button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
import IconUpload from '../../components/icon/upload'
import ImageBox from '../components/ImageBox.vue'
import AvatarCropper from 'vue-avatar-cropper'
import { validationMixin } from 'vuelidate'
import { mapActions } from 'vuex'
const { required, email, maxLength } = require('vuelidate/lib/validators')

export default {
  components: { AvatarCropper, IconUpload, ImageBox },
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
      isFetchingData: false,
      formData: {
        name: null,
        email: '',
        phone: '',
        zip: '',
        address_street_1: '',
        address_street_2: '',
        website: '',
        country_id: null,
        state: '',
        city: ''
      },
      isLoading: false,
      isHidden: false,
      country: null,
      previewLogo: null,
      countries: [],
      passData: [],
      fileSendUrl: '/api/settings/company',
      fileObject: null
    }
  },
  watch: {
    country (newCountry) {
      this.formData.country_id = newCountry.id
      if (this.isFetchingData) {
        return true
      }
    }
  },
  validations: {
    formData: {
      name: {
        required
      },
      country_id: {
        required
      },
      email: {
        email
      },
      address_street_1: {
        maxLength: maxLength(255)
      },
      address_street_2: {
        maxLength: maxLength(255)
      }
    }
  },
  mounted () {
    this.fetchCountry()
    this.setInitialData()
  },
  methods: {
    ...mapActions('companyInfo', [
      'loadData',
      'editCompany',
      'getFile'
    ]),
    cropperHandler (cropper) {
      this.previewLogo = cropper.getCroppedCanvas().toDataURL(this.cropperOutputMime)
    },
    setFileObject (file) {
      this.fileObject = file
    },
    handleUploadError (message, type, xhr) {
      window.toastr['error']('Oops! Something went wrong...')
    },
    async setInitialData () {
      let response = await this.loadData()
      this.isFetchingData = true
      this.formData.name = response.data.user.company.name
      this.formData.address_street_1 = response.data.user.addresses[0].address_street_1
      this.formData.address_street_2 = response.data.user.addresses[0].address_street_2
      this.formData.zip = response.data.user.addresses[0].zip
      this.formData.phone = response.data.user.addresses[0].phone
      this.formData.state = response.data.user.addresses[0].state
      this.formData.city = response.data.user.addresses[0].city
      this.country = response.data.user.addresses[0].country
      this.previewLogo = response.data.user.company.logo
    },
    async updateCompany () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true

      let response = await this.editCompany(this.formData)
      if (response.data.success) {
        this.isLoading = false
        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append('company_logo', JSON.stringify({
            name: this.fileObject.name,
            data: this.previewLogo
          }))
          await axios.post('/api/settings/company/upload-logo', logoData)
        }
        this.isLoading = false
        window.toastr['success'](this.$t('settings.company_info.updated_message'))
        return true
      }
      this.isLoading = false
      window.toastr['error'](response.data.error)
      return true
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
