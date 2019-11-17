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
              <img v-if="previewLogo" :src="previewLogo" class="preview-logo">
              <div v-else class="upload-content">
                <font-awesome-icon class="upload-icon" icon="cloud-upload-alt"/>
                <p class="upload-text"> {{ $tc('general.choose_file') }} </p>
              </div>
            </div>
          </div>
          <avatar-cropper
            :labels="{ submit: 'submit', cancel: 'Cancle'}"
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
            <base-select
              v-model="state"
              :options="states"
              :searchable="true"
              :disabled="isDisabledState"
              :show-labels="false"
              :placeholder="$t('general.select_state')"
              label="name"
              track-by="id"
            />
          </div>
          <div class="col-md-6 mb-4">
            <label class="input-label">{{ $tc('settings.company_info.city') }}</label>
            <base-select
              v-model="city"
              :options="cities"
              :searchable="true"
              :show-labels="false"
              :disabled="isDisabledCity"
              :placeholder="$t('general.select_city')"
              label="name"
              track-by="id"
            />
          </div>
          <!-- <div class="col-md-6 mb-3">
            <label class="input-label">Website</label>
            <base-input
              v-model="formData.website"
              placeholder="Website"
            />
          </div> -->
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
const { required, email, numeric, maxLength } = require('vuelidate/lib/validators')

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
        logo: '',
        email: '',
        phone: '',
        zip: '',
        address_street_1: '',
        address_street_2: '',
        website: '',
        country_id: null,
        state_id: '',
        city_id: ''
      },
      isLoading: false,
      isHidden: false,
      country: null,
      previewLogo: null,
      city: null,
      state: null,
      countries: [],
      isDisabledState: true,
      isDisabledCity: true,
      states: [],
      cities: [],
      passData: [],
      fileSendUrl: '/api/settings/company',
      fileObject: null
    }
  },
  watch: {
    country (newCountry) {
      this.formData.country_id = newCountry.id
      if (this.formData.country_id) {
        this.isDisabledState = false
      }
      this.fetchState()
      if (this.isFetchingData) {
        return true
      }

      this.state = null
      this.city = null
    },
    state (newState) {
      if (newState !== null && newState !== undefined) {
        this.formData.state_id = newState.id
        this.fetchCities()
        this.isDisabledCity = false

        if (this.isFetchingData) {
          this.isFetchingData = false
          return true
        }
        this.city = null
        return true
      }
      // this.formData.state_id = null
      this.cities = []
      this.city = null
      // this.formData.city_id = null
      this.isDisabledCity = true
      return true
    },
    city (newCity) {
      if (newCity !== null && newCity !== undefined) {
        this.formData.city_id = newCity.id
        return true
      }
      // this.formData.city_id = null
      // return true
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
      this.country = response.data.user.addresses[0].country
      this.state = response.data.user.addresses[0].state
      this.city = response.data.user.addresses[0].city
      this.previewLogo = response.data.user.company.logo
    },
    async updateCompany () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let data = new FormData()
      data.append('name', this.formData.name)
      data.append('address_street_1', this.formData.address_street_1)
      data.append('address_street_2', this.formData.address_street_2)
      data.append('city_id', this.formData.city_id)
      data.append('state_id', this.formData.state_id)
      data.append('country_id', this.formData.country_id)
      data.append('zip', this.formData.zip)
      data.append('phone', this.formData.phone)

      let response = await this.editCompany(data)
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
    },
    async fetchState () {
      this.$v.formData.country_id.$touch()
      let res = await window.axios.get(`/api/states/${this.country.id}`)
      if (res) {
        this.states = res.data.states
      }
    },
    async fetchCities () {
      let res = await window.axios.get(`/api/cities/${this.state.id}`)
      if (res) {
        this.cities = res.data.cities
      }
    }
  }
}
</script>
