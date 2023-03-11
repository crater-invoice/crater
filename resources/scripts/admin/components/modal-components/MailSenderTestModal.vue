<template>
  <BaseModal :show="modalActive" @open="setInitialData">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeTestModal"
        />
      </div>
    </template>
    <form action="" @submit.prevent="onTestMailSend">
      <div class="p-4 md:p-8">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$t(`${pre_t}.title`)"
            variant="horizontal"
            :content-loading="isFetchingInitialData"
            :error="
              v$.mail_sender_id.$error && v$.mail_sender_id.$errors[0].$message
            "
          >
            <BaseMultiselect
              v-model="formData.mail_sender_id"
              :invalid="v$.mail_sender_id.$error"
              label="name"
              :options="mailSenderStore.mailSenders"
              value-prop="id"
              :can-deselect="false"
              :can-clear="false"
              :placeholder="$t(`${pre_t}.select_mail_sender`)"
              searchable
              track-by="name"
              :content-loading="isFetchingInitialData"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('general.to')"
            :error="v$.to.$error && v$.to.$errors[0].$message"
            variant="horizontal"
            required
            :content-loading="isFetchingInitialData"
          >
            <BaseInput
              v-model="formData.to"
              type="text"
              :invalid="v$.to.$error"
              :content-loading="isFetchingInitialData"
              @input="v$.to.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('general.subject')"
            :error="v$.subject.$error && v$.subject.$errors[0].$message"
            variant="horizontal"
            required
            :content-loading="isFetchingInitialData"
          >
            <BaseInput
              v-model="formData.subject"
              type="text"
              :invalid="v$.subject.$error"
              :content-loading="isFetchingInitialData"
              @input="v$.subject.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('general.message')"
            :error="v$.message.$error && v$.message.$errors[0].$message"
            variant="horizontal"
            required
            :content-loading="isFetchingInitialData"
          >
            <BaseTextarea
              v-model="formData.message"
              rows="4"
              cols="50"
              :invalid="v$.message.$error"
              :content-loading="isFetchingInitialData"
              @input="v$.message.$touch()"
            />
          </BaseInputGroup>
        </BaseInputGrid>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          variant="primary-outline"
          type="button"
          class="mr-3"
          :content-loading="isFetchingInitialData"
          @click="closeTestModal()"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton
          :loading="isSaving"
          variant="primary"
          type="submit"
          :content-loading="isFetchingInitialData"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              name="PaperAirplaneIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.send') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, email, maxLength, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useModalStore } from '@/scripts/stores/modal'
import { useMailSenderStore } from '@/scripts/admin/stores/mail-sender'

const pre_t = 'settings.mail_sender'
const modalStore = useModalStore()
const mailSenderStore = useMailSenderStore()
const { t } = useI18n()
let isSaving = ref(false)
let formData = reactive({
  mail_sender_id: '',
  to: '',
  subject: '',
  message: '',
})
const isFetchingInitialData = ref(false)

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'MailSenderTestModal'
})

function setInitialData() {
  isFetchingInitialData.value = true
  formData.mail_sender_id = modalStore.id
  setTimeout(() => {
    isFetchingInitialData.value = false
  }, 100)
}

const rules = {
  mail_sender_id: {
    required: helpers.withMessage(t('validation.required'), required),
  },
  to: {
    required: helpers.withMessage(t('validation.required'), required),
    email: helpers.withMessage(t('validation.email_incorrect'), email),
  },
  subject: {
    required: helpers.withMessage(t('validation.required'), required),
    maxLength: helpers.withMessage(
      t('validation.subject_maxlength'),
      maxLength(100)
    ),
  },
  message: {
    required: helpers.withMessage(t('validation.required'), required),
    maxLength: helpers.withMessage(
      t('validation.message_maxlength'),
      maxLength(255)
    ),
  },
}

const v$ = useVuelidate(rules, formData)

function resetFormData() {
  formData.mail_sender_id = ''
  formData.to = ''
  formData.subject = ''
  formData.message = ''

  v$.value.$reset()
}

async function onTestMailSend() {
  v$.value.$touch()
  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true
  let response = await mailSenderStore.sendTestMail(formData)
  if (response.data) {
    closeTestModal()
  }
}
function closeTestModal() {
  modalStore.closeModal()
  setTimeout(() => {
    isSaving.value = false
    modalStore.resetModalData()
    resetFormData()
  }, 300)
}
</script>
