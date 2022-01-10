<template>
  <BaseModal :show="modalActive" @close="closeTestModal">
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
            :label="$t('general.to')"
            :error="v$.formData.to.$error && v$.formData.to.$errors[0].$message"
            variant="horizontal"
            required
          >
            <BaseInput
              ref="to"
              v-model="formData.to"
              type="text"
              :invalid="v$.formData.to.$error"
              @input="v$.formData.to.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('general.subject')"
            :error="
              v$.formData.subject.$error &&
              v$.formData.subject.$errors[0].$message
            "
            variant="horizontal"
            required
          >
            <BaseInput
              v-model="formData.subject"
              type="text"
              :invalid="v$.formData.subject.$error"
              @input="v$.formData.subject.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('general.message')"
            :error="
              v$.formData.message.$error &&
              v$.formData.message.$errors[0].$message
            "
            variant="horizontal"
            required
          >
            <BaseTextarea
              v-model="formData.message"
              rows="4"
              cols="50"
              :invalid="v$.formData.message.$error"
              @input="v$.formData.message.$touch()"
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
          @click="closeTestModal()"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton :loading="isSaving" variant="primary" type="submit">
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
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'

let isSaving = ref(false)
let formData = reactive({
  to: '',
  subject: '',
  message: '',
})

const modalStore = useModalStore()
const mailDriverStore = useMailDriverStore()
const { t } = useI18n()

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'MailTestModal'
})

const rules = {
  formData: {
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
  },
}

const v$ = useVuelidate(rules, { formData })

function resetFormData() {
  formData.id = ''
  formData.to = ''
  formData.subject = ''
  formData.message = ''

  v$.value.$reset()
}

async function onTestMailSend() {
  v$.value.formData.$touch()
  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true
  let response = await mailDriverStore.sendTestMail(formData)
  if (response.data) {
    closeTestModal()
    isSaving.value = false
  }
}
function closeTestModal() {
  modalStore.closeModal()
  setTimeout(() => {
    modalStore.resetModalData()
    resetFormData()
  }, 300)
}
</script>
