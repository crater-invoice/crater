<template>
  <sw-wizard-step
    :title="$t('wizard.company_info')"
    :description="$t('wizard.company_info_desc')"
  >
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <form action="" @submit.prevent="next()">
      <div>
        <div class="grid grid-cols-1 mb-4 md:grid-cols-2 md:mb-6">
          <sw-input-group :label="$tc('settings.company_info.company_logo')">
            <div
              id="logo-box"
              class="relative flex items-center justify-center h-24 p-5 mt-2 bg-transparent border-2 border-gray-200 border-dashed rounded-md image-upload-box"
            >
              <img
                v-if="previewLogo"
                :src="previewLogo"
                class="absolute opacity-100 preview-logo"
                style="max-height: 80%; animation: fadeIn 2s ease"
              />
              <div v-else class="flex flex-col items-center">
                <cloud-upload-icon
                  class="h-5 mb-2 text-xl leading-6 text-gray-400"
                />
                <p class="text-xs leading-4 text-center text-gray-400">
                  Drag a file here or
                  <span id="pick-avatar" class="cursor-pointer text-primary-500"
                    >browse</span
                  >
                  to choose a file
                </p>
              </div>
            </div>

            <sw-avatar
              :preview-avatar="previewLogo"
              trigger="#logo-box"
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
          <sw-input-group
            :label="$t('wizard.company_name')"
            :error="companyNameError"
            required
          >
            <sw-input
              :invalid="$v.companyData.name.$error"
              v-model.trim="companyData.name"
              type="text"
              name="name"
              @input="$v.companyData.name.$touch()"
            />
          </sw-input-group>
          <sw-input-group
            :label="$t('wizard.country')"
            :error="countryError"
            required
          >
            <sw-select
              v-model="country"
              :class="{ error: $v.companyData.country_id.$error }"
              :options="countries"
              :searchable="true"
              :allow-empty="false"
              :show-labels="false"
              :placeholder="$t('general.select_country')"
              track-by="id"
              label="name"
            />
          </sw-input-group>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
          <sw-input-group :label="$t('wizard.state')">
            <sw-input v-model="companyData.state" name="state" type="text" />
          </sw-input-group>

          <sw-input-group :label="$t('wizard.city')">
            <sw-input v-model="companyData.city" name="city" type="text" />
          </sw-input-group>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
          <div>
            <sw-input-group
              :label="$t('wizard.address')"
              :error="address1Error"
            >
              <sw-textarea
                :invalid="$v.companyData.address_street_1.$error"
                v-model.trim="companyData.address_street_1"
                :placeholder="$t('general.street_1')"
                name="billing_street1"
                rows="2"
                @input="$v.companyData.address_street_1.$touch()"
              />
            </sw-input-group>

            <sw-input-group :error="address2Error" class="mt-1 lg:mt-2 md:mt-2">
              <sw-textarea
                :invalid="$v.companyData.address_street_2.$error"
                v-model="companyData.address_street_2"
                :placeholder="$t('general.street_2')"
                name="billing_street2"
                rows="2"
                @input="$v.companyData.address_street_2.$touch()"
              />
            </sw-input-group>
          </div>

          <div>
            <sw-input-group :label="$t('wizard.zip_code')">
              <sw-input v-model.trim="companyData.zip" type="text" name="zip" />
            </sw-input-group>

            <sw-input-group :label="$t('wizard.phone')" class="mt-4">
              <sw-input
                v-model.trim="companyData.phone"
                type="text"
                name="phone"
              />
            </sw-input-group>
          </div>
        </div>

        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          class="mt-4"
          variant="primary"
          type="submit"
        >
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ $t('wizard.save_cont') }}
        </sw-button>
      </div>
    </form>
  </sw-wizard-step>
</template>

<script>
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import { mapActions } from 'vuex'
const { required, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
  },
  data() {
    return {
      companyData: {
        logo: '',
        name: null,
        address_street_1: '',
        address_street_2: '',
        city: '',
        state: '',
        country_id: '',
        zip: '',
        phone: '',
      },
      isLoading: false,
      isFetching: false,
      step: 1,
      countries: [],
      country: null,
      previewLogo: null,
      fileObject: null,
      cropperOutputMime: '',
    }
  },
  validations: {
    companyData: {
      name: {
        required,
      },
      country_id: {
        required,
      },
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
  },
  computed: {
    companyNameError() {
      if (!this.$v.companyData.name.$error) {
        return ''
      }

      if (!this.$v.companyData.name.required) {
        return this.$tc('validation.required')
      }
    },
    countryError() {
      if (!this.$v.companyData.country_id.$error) {
        return ''
      }

      if (!this.$v.companyData.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    address1Error() {
      if (!this.$v.companyData.address_street_1.$error) {
        return ''
      }

      if (!this.$v.companyData.address_street_1.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    address2Error() {
      if (!this.$v.companyData.address_street_2.$error) {
        return ''
      }

      if (!this.$v.companyData.address_street_2.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },
  watch: {
    country({ id }) {
      this.companyData.country_id = id
      return true
    },
  },
  mounted() {
    this.fetchCountries()
  },
  methods: {
    ...mapActions('company', ['setSelectedCompany']),
    ...mapActions('notification', ['showNotification']),
    onUploadHandler(cropper) {
      this.previewLogo = cropper
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
      this.$v.companyData.$touch()

      if (this.$v.companyData.$invalid) {
        return true
      }

      this.isLoading = true

      let response = await window.axios.put('/api/v1/company', this.companyData)

      if (response.data) {
        this.setSelectedCompany(response.data.company)

        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append(
            'company_logo',
            JSON.stringify({
              name: this.fileObject.name,
              data: this.previewLogo,
            })
          )

          await axios.post('/api/v1/company/upload-logo', logoData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              company: response.data.company.id,
            },
          })
        }

        this.$emit('next', 7)
        this.isLoading = false
      }
    },

    onFileChange(e) {
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

    async fetchCountries() {
      this.isFetching = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.isFetching = false
    },
  },
}
</script>
