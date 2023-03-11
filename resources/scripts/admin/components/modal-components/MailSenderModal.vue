<template>
  <BaseModal
    :show="modalStore.active && modalStore.componentName === 'MailSenderModal'"
  >
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="h-6 w-6 text-gray-500 cursor-pointer"
          @click="closeMailSenderModal"
        />
      </div>
    </template>
    <form action="" @submit.prevent="submitMailSenderData">
      <div class="p-4 sm:p-6 my-2">
        <!-- Name -->
        <BaseInputGrid>
          <BaseInputGroup
            :label="$t(`${pre_t}.name`)"
            :error="v$.name.$error && v$.name.$errors[0].$message"
            :help-text="$t(`${pre_t}.name_help`)"
            required
          >
            <BaseInput
              v-model.trim="mailSenderStore.currentMailSender.name"
              :invalid="v$.name.$error"
              type="text"
              @input="v$.name.$touch()"
            />
          </BaseInputGroup>

          <!-- From Name -->
          <BaseInputGroup
            :label="$t(`${pre_t}.from_name`)"
            :error="v$.from_name.$error && v$.from_name.$errors[0].$message"
            required
          >
            <BaseInput
              v-model="mailSenderStore.currentMailSender.from_name"
              :invalid="v$.from_name.$error"
              type="text"
              @input="v$.from_name.$touch()"
            />
          </BaseInputGroup>

          <!-- From Address -->
          <BaseInputGroup
            :label="$t(`${pre_t}.from_address`)"
            :error="
              v$.from_address.$error && v$.from_address.$errors[0].$message
            "
            required
          >
            <BaseInput
              v-model="mailSenderStore.currentMailSender.from_address"
              :invalid="v$.from_address.$error"
              type="text"
              @input="v$.from_address.$touch()"
            />
          </BaseInputGroup>

          <!-- CC -->
          <BaseInputGroup
            :label="$t(`${pre_t}.cc`)"
            :error="v$.cc.$error && v$.cc.$errors[0].$message"
            :help-text="$t(`${pre_t}.email_list`)"
          >
            <BaseInput
              v-model="mailSenderStore.currentMailSender.cc"
              :invalid="v$.cc.$error"
              type="text"
              @input="v$.cc.$touch()"
            />
          </BaseInputGroup>

          <!-- BCC -->
          <BaseInputGroup
            :label="$t(`${pre_t}.bcc`)"
            :error="v$.bcc.$error && v$.bcc.$errors[0].$message"
            :help-text="$t(`${pre_t}.email_list`)"
          >
            <BaseInput
              v-model="mailSenderStore.currentMailSender.bcc"
              :invalid="v$.bcc.$error"
              type="text"
              @input="v$.bcc.$touch()"
            />
          </BaseInputGroup>

          <!-- Mail Driver -->
          <BaseInputGroup
            :label="$t(`${pre_t}.driver`)"
            :error="v$.driver.$error && v$.driver.$errors[0].$message"
            required
          >
            <BaseMultiselect
              v-model="mailSenderStore.currentMailSender.driver"
              :options="mailSenderStore.mail_drivers"
              :can-deselect="false"
              :invalid="v$.driver.$error"
            />
          </BaseInputGroup>

          <component
            :is="loadMailDriver"
            :mail-sender-store="mailSenderStore"
          />
        </BaseInputGrid>

        <BaseDivider class="mt-4 mb-0" />

        <!-- Is Default? -->
        <BaseSwitchSection
          v-model="mailSenderStore.currentMailSender.is_default"
          :title="$t(`${pre_t}.is_default`)"
          :description="$t(`${pre_t}.is_default_description`)"
        />
      </div>

      <div
        class="z-0 flex justify-end p-4 border-t border-solid border--200 border-modal-bg"
      >
        <BaseButton
          class="mr-3 text-sm"
          variant="primary-outline"
          type="button"
          @click="closeMailSenderModal"
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
          {{
            mailSenderStore.isEdit ? $t('general.update') : $t('general.save')
          }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, email, minLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useModalStore } from '@/scripts/stores/modal'
import { useMailSenderStore } from '@/scripts/admin/stores/mail-sender'
import SmtpDriver from '@/scripts/admin/views/settings/mail-sender/SmtpDriver.vue'
import MailgunDriver from '@/scripts/admin/views/settings/mail-sender/MailgunDriver.vue'
import SesDriver from '@/scripts/admin/views/settings/mail-sender/SesDriver.vue'

const pre_t = 'settings.mail_sender'
const modalStore = useModalStore()
const mailSenderStore = useMailSenderStore()
const { t } = useI18n()
let isSaving = ref(false)
const loadMailDriver = computed(() => {
  switch (mailSenderStore.currentMailSender.driver) {
    case 'smtp':
      return SmtpDriver
      break
    case 'mail':
      return false
      break
    case 'sendmail':
      return false
      break
    case 'mailgun':
      return MailgunDriver
      break
    case 'ses':
      return SesDriver
      break
    default:
      return false
  }
})

// This is multiple email custom validation
const multiEmail = (value) => {
  if (value == '' || value === null) return true
  const emailRegex =
    /^(?:[A-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[A-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9]{2,}(?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/i

  const emailArr = value.split(',')
  let isValid = emailArr.every((v) => {
    return emailRegex.test(v)
  })
  return isValid
}

const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
    from_name: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    from_address: {
      required: helpers.withMessage(t('validation.required'), required),
      email: helpers.withMessage(t('validation.email_incorrect'), email),
    },
    cc: {
      multiEmail: helpers.withMessage(
        t('validation.email_incorrect'),
        multiEmail
      ),
    },
    bcc: {
      multiEmail: helpers.withMessage(
        t('validation.email_incorrect'),
        multiEmail
      ),
    },
    driver: {
      required: helpers.withMessage(t('validation.required'), required),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => mailSenderStore.currentMailSender)
)

async function submitMailSenderData() {
  v$.value.$touch()
  if (v$.value.$invalid) {
    return true
  }
  try {
    const action = mailSenderStore.isEdit
      ? mailSenderStore.updateMailSender
      : mailSenderStore.addMailSender
    isSaving.value = true

    var mailDriverConfig = null
    switch (mailSenderStore.currentMailSender.driver) {
      case 'smtp':
        mailDriverConfig = mailSenderStore.smtpConfig
        break
      case 'mailgun':
        mailDriverConfig = mailSenderStore.mailgunConfig
        break
      case 'ses':
        mailDriverConfig = mailSenderStore.sesConfig
        break
    }
    mailSenderStore.currentMailSender.settings = mailDriverConfig

    let res = await action(mailSenderStore.currentMailSender)
    isSaving.value = false
    modalStore.refreshData ? modalStore.refreshData(res.data.data) : ''
    closeMailSenderModal()
  } catch (err) {
    isSaving.value = false
    return true
  }
}

function closeMailSenderModal() {
  modalStore.closeModal()
  setTimeout(() => {
    mailSenderStore.resetCurrentMailSender()
    v$.value.$reset()
  }, 300)
}

onMounted(async () => {
  await mailSenderStore.fetchMailDrivers()
})
</script>
