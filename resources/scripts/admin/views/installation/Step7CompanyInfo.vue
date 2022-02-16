<template>
  <BaseWizardStep
    :title="$t('wizard.company_info')"
    :description="$t('wizard.company_info_desc')"
    step-container="bg-white border border-gray-200 border-solid mb-8 md:w-full p-8 rounded w-full"
  >
    <form action="" @submit.prevent="next">
      <div class="grid grid-cols-1 mb-4 md:grid-cols-2 md:mb-6">
        <BaseInputGroup :label="$tc('settings.company_info.company_logo')">
          <BaseFileUploader
            base64
            :preview-image="previewLogo"
            @change="onFileInputChange"
            @remove="onFileInputRemove"
          />
        </BaseInputGroup>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
        <BaseInputGroup
          :label="$t('wizard.company_name')"
          :error="
            v$.companyForm.name.$error &&
            v$.companyForm.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="companyForm.name"
            :invalid="v$.companyForm.name.$error"
            type="text"
            name="name"
            @input="v$.companyForm.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('wizard.country')"
          :error="
            v$.companyForm.address.country_id.$error &&
            v$.companyForm.address.country_id.$errors[0].$message
          "
          :content-loading="isFetchingInitialData"
          required
        >
          <BaseMultiselect
            v-model="companyForm.address.country_id"
            label="name"
            :invalid="v$.companyForm.address.country_id.$error"
            :options="globalStore.countries"
            value-prop="id"
            :can-deselect="false"
            :can-clear="false"
            :content-loading="isFetchingInitialData"
            :placeholder="$t('general.select_country')"
            searchable
            track-by="name"
          />
        </BaseInputGroup>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
        <BaseInputGroup :label="$t('wizard.state')">
          <BaseInput
            v-model="companyForm.address.state"
            name="state"
            type="text"
          />
        </BaseInputGroup>

        <BaseInputGroup :label="$t('wizard.city')">
          <BaseInput
            v-model="companyForm.address.city"
            name="city"
            type="text"
          />
        </BaseInputGroup>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
        <div>
          <BaseInputGroup
            :label="$t('wizard.address')"
            :error="
              v$.companyForm.address.address_street_1.$error &&
              v$.companyForm.address.address_street_1.$errors[0].$message
            "
          >
            <BaseTextarea
              v-model.trim="companyForm.address.address_street_1"
              :invalid="v$.companyForm.address.address_street_1.$error"
              :placeholder="$t('general.street_1')"
              name="billing_street1"
              rows="2"
              @input="v$.companyForm.address.address_street_1.$touch()"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :error="
              v$.companyForm.address.address_street_2.$error &&
              v$.companyForm.address.address_street_2.$errors[0].$message
            "
            class="mt-1 lg:mt-2 md:mt-2"
          >
            <BaseTextarea
              v-model="companyForm.address.address_street_2"
              :invalid="v$.companyForm.address.address_street_2.$error"
              :placeholder="$t('general.street_2')"
              name="billing_street2"
              rows="2"
              @input="v$.companyForm.address.address_street_2.$touch()"
            />
          </BaseInputGroup>
        </div>

        <div>
          <BaseInputGroup :label="$t('wizard.zip_code')">
            <BaseInput
              v-model.trim="companyForm.address.zip"
              type="text"
              name="zip"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('wizard.phone')" class="mt-4">
            <BaseInput
              v-model.trim="companyForm.address.phone"
              type="text"
              name="phone"
            />
          </BaseInputGroup>
        </div>
      </div>

      <BaseButton :loading="isSaving" :disabled="isSaving" class="mt-4">
        <template #left="slotProps">
          <BaseIcon v-if="!isSaving" name="SaveIcon" :class="slotProps.class" />
        </template>
        {{ $t('wizard.save_cont') }}
      </BaseButton>
    </form>
  </BaseWizardStep>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, maxLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const emit = defineEmits(['next'])

let isFetchingInitialData = ref(false)
let isSaving = ref(false)
const { t } = useI18n()
let previewLogo = ref(null)
let logoFileBlob = ref(null)
let logoFileName = ref(null)

const companyForm = reactive({
  name: null,
  address: {
    address_street_1: '',
    address_street_2: '',
    website: '',
    country_id: null,
    state: '',
    city: '',
    phone: '',
    zip: '',
  },
})

const companyStore = useCompanyStore()
const globalStore = useGlobalStore()

onMounted(async () => {
  isFetchingInitialData.value = true
  await globalStore.fetchCountries()
  isFetchingInitialData.value = false

  // set default country
  companyForm.address.country_id = globalStore.countries.find((country) => {
    return country.code == 'US'
  })?.id
})

const rules = {
  companyForm: {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    address: {
      country_id: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      address_street_1: {
        maxLength: helpers.withMessage(
          t('validation.address_maxlength', { count: 255 }),
          maxLength(255)
        ),
      },
      address_street_2: {
        maxLength: helpers.withMessage(
          t('validation.address_maxlength', { count: 255 }),
          maxLength(255)
        ),
      },
    },
  },
}

const v$ = useVuelidate(rules, { companyForm })

function onFileInputChange(fileName, file, fileCount, fileList) {
  logoFileName.value = fileList.name
  logoFileBlob.value = file
}

function onFileInputRemove() {
  logoFileBlob.value = null
}

async function next() {
  v$.value.companyForm.$touch()
  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true
  let res = companyStore.updateCompany(companyForm)
  if (res) {
    if (logoFileBlob.value) {
      let logoData = new FormData()

      logoData.append(
        'company_logo',
        JSON.stringify({
          name: logoFileName.value,
          data: logoFileBlob.value,
        })
      )
      await companyStore.updateCompanyLogo(logoData)
    }
    isSaving.value = false
    emit('next', 7)
  }
}
</script>
