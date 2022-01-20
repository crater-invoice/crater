<template>
  <BaseModal :show="modalActive" @close="closeCompanyModal" @open="getInitials">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}

        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeCompanyModal"
        />
      </div>
    </template>
    <form action="" @submit.prevent="submitCompanyData">
      <div class="p-4 mb-16 sm:p-6 space-y-4">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :content-loading="isFetchingInitialData"
            :label="$tc('settings.company_info.company_logo')"
          >
            <BaseContentPlaceholders v-if="isFetchingInitialData">
              <BaseContentPlaceholdersBox :rounded="true" class="w-full h-24" />
            </BaseContentPlaceholders>
            <div v-else class="flex flex-col items-center">
              <BaseFileUploader
                :preview-image="previewLogo"
                base64
                @remove="onFileInputRemove"
                @change="onFileInputChange"
              />
            </div>
          </BaseInputGroup>

          <BaseInputGroup
            :label="$tc('settings.company_info.company_name')"
            :error="
              v$.newCompanyForm.name.$error &&
              v$.newCompanyForm.name.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            required
          >
            <BaseInput
              v-model="newCompanyForm.name"
              :invalid="v$.newCompanyForm.name.$error"
              :content-loading="isFetchingInitialData"
              @input="v$.newCompanyForm.name.$touch()"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :content-loading="isFetchingInitialData"
            :label="$tc('settings.company_info.country')"
            :error="
              v$.newCompanyForm.address.country_id.$error &&
              v$.newCompanyForm.address.country_id.$errors[0].$message
            "
            required
          >
            <BaseMultiselect
              v-model="newCompanyForm.address.country_id"
              :content-loading="isFetchingInitialData"
              label="name"
              :invalid="v$.newCompanyForm.address.country_id.$error"
              :options="globalStore.countries"
              value-prop="id"
              :can-deselect="true"
              :can-clear="false"
              searchable
              track-by="name"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('wizard.currency')"
            :error="
              v$.newCompanyForm.currency.$error &&
              v$.newCompanyForm.currency.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            :help-text="$t('wizard.currency_set_alert')"
            required
          >
            <BaseMultiselect
              v-model="newCompanyForm.currency"
              :content-loading="isFetchingInitialData"
              :options="globalStore.currencies"
              label="name"
              value-prop="id"
              :searchable="true"
              track-by="name"
              :placeholder="$tc('settings.currencies.select_currency')"
              :invalid="v$.newCompanyForm.currency.$error"
              class="w-full"
            >
            </BaseMultiselect>
          </BaseInputGroup>
        </BaseInputGrid>
      </div>

      <div class="z-0 flex justify-end p-4 bg-gray-50 border-modal-bg">
        <BaseButton
          class="mr-3 text-sm"
          variant="primary-outline"
          outline
          type="button"
          @click="closeCompanyModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>
        <BaseButton
          :loading="isSaving"
          :disabled="isSaving"
          variant="primary"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.save') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { useModalStore } from '@/scripts/stores/modal'
import { computed, onMounted, ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, minLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useRouter } from 'vue-router'

const router = useRouter()
const companyStore = useCompanyStore()
const modalStore = useModalStore()
const globalStore = useGlobalStore()

const { t } = useI18n()
let isSaving = ref(false)
let previewLogo = ref(null)
let isFetchingInitialData = ref(false)
let companyLogoFileBlob = ref(null)
let companyLogoName = ref(null)

const newCompanyForm = reactive({
  name: null,
  currency: '',
  address: {
    country_id: null,
  },
})

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'CompanyModal'
})

const rules = {
  newCompanyForm: {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
    address: {
      country_id: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
    currency: {
      required: helpers.withMessage(t('validation.required'), required),
    },
  },
}

const v$ = useVuelidate(rules, { newCompanyForm })

async function getInitials() {
  isFetchingInitialData.value = true
  await globalStore.fetchCurrencies()
  await globalStore.fetchCountries()

  newCompanyForm.currency = companyStore.selectedCompanyCurrency.id
  newCompanyForm.address.country_id =
    companyStore.selectedCompany.address.country_id

  isFetchingInitialData.value = false
}

function onFileInputChange(fileName, file) {
  companyLogoName.value = fileName
  companyLogoFileBlob.value = file
}

function onFileInputRemove() {
  companyLogoName.value = null
  companyLogoFileBlob.value = null
}

async function submitCompanyData() {
  v$.value.newCompanyForm.$touch()
  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true
  try {
    const res = await companyStore.addNewCompany(newCompanyForm)
    if (res.data.data) {
      await companyStore.setSelectedCompany(res.data.data)
      if (companyLogoFileBlob && companyLogoFileBlob.value) {
        let logoData = new FormData()

        logoData.append(
          'company_logo',
          JSON.stringify({
            name: companyLogoName.value,
            data: companyLogoFileBlob.value,
          })
        )

        await companyStore.updateCompanyLogo(logoData)
        router.push('/admin/dashboard')
      }
      await globalStore.setIsAppLoaded(false)
      await globalStore.bootstrap()
      closeCompanyModal()
    }
    isSaving.value = false
  } catch {
    isSaving.value = false
  }
}

function resetNewCompanyForm() {
  newCompanyForm.name = ''
  newCompanyForm.currency = ''
  newCompanyForm.address.country_id = ''

  v$.value.$reset()
}

function closeCompanyModal() {
  modalStore.closeModal()

  setTimeout(() => {
    resetNewCompanyForm()
    v$.value.$reset()
  }, 300)
}
</script>
