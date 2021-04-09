<template>
  <form class="relative h-full" @submit.prevent="updateCompanyData">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.company_info.company_info') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.company_info.section_description') }}
        </p>
      </template>

      <div class="grid mb-6 md:grid-cols-2">
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
                <span id="pick-avatar" class="cursor-pointer text-primary-500">
                  browse
                </span>
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

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.company_info.company_name')"
          :error="nameError"
          required
        >
          <sw-input
            v-model="formData.name"
            :invalid="$v.formData.name.$error"
            :placeholder="$t('settings.company_info.company_name')"
            class="mt-2"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.phone')">
          <sw-input
            v-model="formData.phone"
            :placeholder="$t('settings.company_info.phone')"
            class="mt-2"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.company_info.country')"
          :error="countryError"
          required
        >
          <sw-select
            v-model="country"
            :options="countries"
            :class="{ error: $v.formData.country_id.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            class="mt-2"
            label="name"
            track-by="id"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.state')">
          <sw-input
            v-model="formData.state"
            :placeholder="$tc('settings.company_info.state')"
            name="state"
            class="mt-2"
            type="text"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.city')">
          <sw-input
            v-model="formData.city"
            :placeholder="$tc('settings.company_info.city')"
            name="city"
            class="mt-2"
            type="text"
          />
        </sw-input-group>

        <sw-input-group :label="$tc('settings.company_info.zip')">
          <sw-input
            v-model="formData.zip"
            :placeholder="$tc('settings.company_info.zip')"
            class="mt-2"
          />
        </sw-input-group>

        <div>
          <sw-input-group
            :label="$tc('settings.company_info.address')"
            :error="address1Error"
          >
            <sw-textarea
              v-model="formData.address_street_1"
              :placeholder="$tc('general.street_1')"
              :class="{ invalid: $v.formData.address_street_1.$error }"
              rows="2"
              @input="$v.formData.address_street_1.$touch()"
            />
          </sw-input-group>

          <sw-input-group :error="address2Error" class="my-2">
            <sw-textarea
              v-model="formData.address_street_2"
              :placeholder="$tc('general.street_2')"
              :class="{ invalid: $v.formData.address_street_2.$error }"
              rows="2"
              @input="$v.formData.address_street_2.$touch()"
            />
          </sw-input-group>
        </div>
      </div>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        class="mt-4"
        variant="primary"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $tc('settings.company_info.save') }}
      </sw-button>
    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CloudUploadIcon } from '@vue-hero-icons/solid'
const { required, email, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
  },
  data() {
    return {
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
        city: '',
      },
      isLoading: false,
      country: null,
      passData: [],
      fileSendUrl: '/api/v1/settings/company',
      previewLogo: null,
      fileObject: null,
      cropperOutputMime: '',
      isRequestOnGoing: false,
    }
  },
  watch: {
    country(newCountry) {
      this.formData.country_id = newCountry.id
      if (this.isFetchingData) {
        return true
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
      },
      country_id: {
        required,
      },
      email: {
        email,
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
    ...mapGetters(['countries']),
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    countryError() {
      if (!this.$v.formData.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    address1Error() {
      if (!this.$v.formData.address_street_1.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_1.maxLength) {
        return this.$tc('validation.address_maxlength')
      }
    },
    address2Error() {
      if (!this.$v.formData.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_2.maxLength) {
        return this.$tc('validation.address_maxlength')
      }
    },
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('company', ['updateCompany', 'updateCompanyLogo']),
    ...mapActions('user', ['fetchCurrentUser']),
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
    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()
      this.isFetchingData = true
      if (response.data.user) {
        this.formData.name = response.data.user.company.name
        this.formData.address_street_1 =
          response.data.user.company.address.address_street_1
        this.formData.address_street_2 =
          response.data.user.company.address.address_street_2
        this.formData.zip = response.data.user.company.address.zip
        this.formData.phone = response.data.user.company.address.phone
        this.formData.state = response.data.user.company.address.state
        this.formData.city = response.data.user.company.address.city
        this.country = response.data.user.company.address.country
        this.previewLogo = response.data.user.company.logo
      }
      this.isRequestOnGoing = false
    },
    async updateCompanyData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true

      let response = await this.updateCompany(this.formData)
      if (response.data.success) {
        this.isLoading = false
        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append(
            'company_logo',
            JSON.stringify({
              name: this.fileObject.name,
              data: this.previewLogo,
            })
          )
          await this.updateCompanyLogo(logoData)
        }
        this.isLoading = false
        this.showNotification({
          type: 'success',
          message: this.$t('settings.company_info.updated_message'),
        })
        return true
      }
      this.isLoading = false
      this.showNotification({
        type: 'error',
        message: response.data.error,
      })
      return true
    },
  },
}
</script>
