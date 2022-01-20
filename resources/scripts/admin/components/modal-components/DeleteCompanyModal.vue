<template>
  <BaseModal :show="modalActive" @close="closeCompanyModal">
    <div class="flex justify-between w-full">
      <div class="px-6 pt-6">
        <h6 class="font-medium text-lg text-left">
          {{ modalStore.title }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{
            $t('settings.company_info.delete_company_modal_desc', {
              company: companyStore.selectedCompany.name,
            })
          }}
        </p>
      </div>
    </div>
    <form action="" @submit.prevent="submitCompanyData">
      <div class="p-4 sm:p-6 space-y-4">
        <BaseInputGroup
          :label="
            $t('settings.company_info.delete_company_modal_label', {
              company: companyStore.selectedCompany.name,
            })
          "
          :error="
            v$.formData.name.$error && v$.formData.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="formData.name"
            :invalid="v$.formData.name.$error"
            @input="v$.formData.name.$touch()"
          />
        </BaseInputGroup>
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
          :loading="isDeleting"
          :disabled="isDeleting"
          variant="danger"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isDeleting"
              name="TrashIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.delete') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useModalStore } from '@/scripts/stores/modal'
import { computed, onMounted, ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, minLength, helpers, sameAs } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useGlobalStore } from '@/scripts/admin/stores/global'

const companyStore = useCompanyStore()
const modalStore = useModalStore()
const globalStore = useGlobalStore()
const router = useRouter()
const { t } = useI18n()
let isDeleting = ref(false)

const formData = reactive({
  id: companyStore.selectedCompany.id,
  name: null,
})

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'DeleteCompanyModal'
})

const rules = {
  formData: {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      sameAsName: helpers.withMessage(
        t('validation.company_name_not_same'),
        sameAs(companyStore.selectedCompany.name)
      ),
    },
  },
}

const v$ = useVuelidate(
  rules,
  { formData },
  {
    $scope: false,
  }
)

async function submitCompanyData() {
  v$.value.$touch()
  if (v$.value.$invalid) {
    return true
  }

  const company = companyStore.companies[0]

  isDeleting.value = true
  try {
    const res = await companyStore.deleteCompany(formData)
    console.log(res.data.success)
    if (res.data.success) {
      closeCompanyModal()
      await companyStore.setSelectedCompany(company)
      router.push('/admin/dashboard')
      await globalStore.setIsAppLoaded(false)
      await globalStore.bootstrap()
    }
    isDeleting.value = false
  } catch {
    isDeleting.value = false
  }
}

function resetNewCompanyForm() {
  formData.id = null
  formData.name = ''

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
