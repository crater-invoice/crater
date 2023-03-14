<template>
  <BaseWizardStep
    :title="$t('wizard.mail.mail_config')"
    :description="$t('wizard.mail.mail_config_desc')"
  >
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

        <!-- Is Default? -->
        <BaseSwitchSection
          v-model="mailSenderStore.currentMailSender.is_default"
          :title="$t(`${pre_t}.is_default`)"
          :description="$t(`${pre_t}.is_default_description`)"
        />
        <BaseDivider class="my-4" />

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
          {{ $t('wizard.save_cont') }}
        </BaseButton>
      </div>
    </form>
  </BaseWizardStep>
</template>

<script setup>
import { computed, ref, inject, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useMailSenderStore } from '@/scripts/admin/stores/mail-sender'
import { useVuelidate } from '@vuelidate/core'
import { required, email, minLength, helpers } from '@vuelidate/validators'
import MailSenderDropdown from '@/scripts/admin/components/dropdowns/MailSenderIndexDropdown.vue'
import MailSenderTestModal from '@/scripts/admin/components/modal-components/MailSenderTestModal.vue'
import SmtpDriver from '@/scripts/admin/views/settings/mail-sender/SmtpDriver.vue'
import MailgunDriver from '@/scripts/admin/views/settings/mail-sender/MailgunDriver.vue'
import SesDriver from '@/scripts/admin/views/settings/mail-sender/SesDriver.vue'

const pre_t = 'settings.mail_sender'
const mailSenderStore = useMailSenderStore()
const { t } = useI18n()
const table = ref(null)
const utils = inject('utils')
let isSaving = ref(false)
const emit = defineEmits(['next'])

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
    emit('next')
    isSaving.value = false
  } catch (err) {
    isSaving.value = false
    return true
  }
}

onMounted(async () => {
  await mailSenderStore.fetchMailDrivers()
})
</script>
