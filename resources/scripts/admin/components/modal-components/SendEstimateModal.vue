<template>
  <BaseModal
    :show="modalActive"
    @close="closeSendEstimateModal"
    @open="setInitialData"
  >
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="h-6 w-6 text-gray-500 cursor-pointer"
          @click="closeSendEstimateModal"
        />
      </div>
    </template>

    <form v-if="!isPreview" action="">
      <div class="px-8 py-8 sm:p-6">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$t('general.from')"
            required
            :error="v$.from.$error && v$.from.$errors[0].$message"
          >
            <BaseInput
              v-model="estimateMailForm.from"
              type="text"
              :invalid="v$.from.$error"
              @input="v$.from.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('general.to')"
            required
            :error="v$.to.$error && v$.to.$errors[0].$message"
          >
            <BaseInput
              v-model="estimateMailForm.to"
              type="text"
              :invalid="v$.to.$error"
              @input="v$.to.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('general.subject')"
            required
            :error="v$.subject.$error && v$.subject.$errors[0].$message"
          >
            <BaseInput
              v-model="estimateMailForm.subject"
              type="text"
              :invalid="v$.subject.$error"
              @input="v$.subject.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup :label="$t('general.body')" required>
            <BaseCustomInput
              v-model="estimateMailForm.body"
              :fields="estimateMailFields"
            />
          </BaseInputGroup>
        </BaseInputGrid>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeSendEstimateModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="button"
          class="mr-3"
          @click="submitForm"
        >
          <BaseIcon v-if="!isLoading" name="PhotographIcon" class="h-5 mr-2" />
          {{ $t('general.preview') }}
        </BaseButton>
      </div>
    </form>
    <div v-else>
      <div class="my-6 mx-4 border border-gray-200 relative">
        <BaseButton
          class="absolute top-4 right-4"
          :disabled="isLoading"
          variant="primary-outline"
          @click="cancelPreview"
        >
          <BaseIcon name="PencilIcon" class="h-5 mr-2" />
          Edit
        </BaseButton>
        <iframe
          :src="templateUrl"
          frameborder="0"
          class="w-full"
          style="min-height: 500px"
        ></iframe>
      </div>

      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeSendEstimateModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>
        <BaseButton
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="button"
          @click="submitForm"
        >
          <BaseIcon v-if="!isLoading" name="PaperAirplaneIcon" class="mr-2" />
          {{ $t('general.send') }}
        </BaseButton>
      </div>
    </div>
  </BaseModal>
</template>

<script setup>
import { computed, onMounted, ref, watchEffect, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, email, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useModalStore } from '@/scripts/stores/modal'
import { useEstimateStore } from '@/scripts/admin/stores/estimate'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'

const modalStore = useModalStore()
const estimateStore = useEstimateStore()
const notificationStore = useNotificationStore()
const companyStore = useCompanyStore()
const mailDriverStore = useMailDriverStore()

const { t } = useI18n()
const isLoading = ref(false)
const templateUrl = ref('')
const isPreview = ref(false)

const estimateMailFields = ref([
  'customer',
  'customerCustom',
  'estimate',
  'estimateCustom',
  'company',
])

let estimateMailForm = reactive({
  id: null,
  from: null,
  to: null,
  subject: 'New Estimate',
  body: null,
})

const emit = defineEmits(['update'])

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'SendEstimateModal'
})

const modalData = computed(() => {
  return modalStore.data
})

const rules = {
  from: {
    required: helpers.withMessage(t('validation.required'), required),
    email: helpers.withMessage(t('validation.email_incorrect'), email),
  },
  to: {
    required: helpers.withMessage(t('validation.required'), required),
    email: helpers.withMessage(t('validation.email_incorrect'), email),
  },
  subject: {
    required: helpers.withMessage(t('validation.required'), required),
  },
  body: {
    required: helpers.withMessage(t('validation.required'), required),
  },
}

const v$ = useVuelidate(
  rules,
  computed(() => estimateMailForm)
)

function cancelPreview() {
  isPreview.value = false
}

async function setInitialData() {
  let admin = await companyStore.fetchBasicMailConfig()

  estimateMailForm.id = modalStore.id

  if (admin.data) {
    estimateMailForm.from = admin.data.from_mail
  }

  if (modalData.value) {
    estimateMailForm.to = modalData.value.customer.email
  }

  estimateMailForm.body =
    companyStore.selectedCompanySettings.estimate_mail_body
}

async function submitForm() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  try {
    isLoading.value = true

    if (!isPreview.value) {
      const previewResponse = await estimateStore.previewEstimate(
        estimateMailForm
      )
      isLoading.value = false

      isPreview.value = true
      var blob = new Blob([previewResponse.data], { type: 'text/html' })
      templateUrl.value = URL.createObjectURL(blob)

      return
    }

    const response = await estimateStore.sendEstimate(estimateMailForm)

    isLoading.value = false

    if (response.data.success) {
      emit('update')
      closeSendEstimateModal()
      return true
    }
  } catch (error) {
    console.error(error)
    isLoading.value = false
    notificationStore.showNotification({
      type: 'error',
      message: t('estimates.something_went_wrong'),
    })
  }
}

function closeSendEstimateModal() {
  modalStore.closeModal()

  setTimeout(() => {
    v$.value.$reset()
    isPreview.value = false
    templateUrl.value = null
  }, 300)
}
</script>
