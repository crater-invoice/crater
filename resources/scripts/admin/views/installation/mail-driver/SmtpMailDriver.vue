<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.mail_driver.$error &&
          v$.smtpConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="smtpConfig.mail_driver"
          :options="mailDriverStore.mail_drivers"
          :can-deselect="false"
          :content-loading="isFetchingInitialData"
          :invalid="v$.smtpConfig.mail_driver.$error"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.host')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.mail_host.$error &&
          v$.smtpConfig.mail_host.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="smtpConfig.mail_host"
          :invalid="v$.smtpConfig.mail_host.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_host"
          @input="v$.smtpConfig.mail_host.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.username')"
        :content-loading="isFetchingInitialData"
      >
        <BaseInput
          v-model.trim="smtpConfig.mail_username"
          :content-loading="isFetchingInitialData"
          type="text"
          name="db_name"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.password')"
        :content-loading="isFetchingInitialData"
      >
        <BaseInput
          v-model.trim="smtpConfig.mail_password"
          :type="getInputType"
          :content-loading="isFetchingInitialData"
          autocomplete="off"
          data-lpignore="true"
          name="password"
        >
          <template #right>
            <BaseIcon
              v-if="isShowPassword"
              name="EyeOffIcon"
              class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
              @click="isShowPassword = !isShowPassword"
            />
            <BaseIcon
              v-else
              name="EyeIcon"
              class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
              @click="isShowPassword = !isShowPassword"
            />
          </template>
        </BaseInput>
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.port')"
        :error="
          v$.smtpConfig.mail_port.$error &&
          v$.smtpConfig.mail_port.$errors[0].$message
        "
        :content-loading="isFetchingInitialData"
        required
      >
        <BaseInput
          v-model.trim="smtpConfig.mail_port"
          :invalid="v$.smtpConfig.mail_port.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_port"
          @input="v$.smtpConfig.mail_port.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.encryption')"
        :error="
          v$.smtpConfig.mail_encryption.$error &&
          v$.smtpConfig.mail_encryption.$errors[0].$message
        "
        :content-loading="isFetchingInitialData"
        required
      >
        <BaseMultiselect
          v-model.trim="smtpConfig.mail_encryption"
          :options="encryptions"
          :can-deselect="false"
          :invalid="v$.smtpConfig.mail_encryption.$error"
          :content-loading="isFetchingInitialData"
          @input="v$.smtpConfig.mail_encryption.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
      <BaseInputGroup
        :label="$t('wizard.mail.from_mail')"
        :error="
          v$.smtpConfig.from_mail.$error &&
          v$.smtpConfig.from_mail.$errors[0].$message
        "
        :content-loading="isFetchingInitialData"
        required
      >
        <BaseInput
          v-model.trim="smtpConfig.from_mail"
          :invalid="v$.smtpConfig.from_mail.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_mail"
          @input="v$.smtpConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.from_name')"
        :error="
          v$.smtpConfig.from_name.$error &&
          v$.smtpConfig.from_name.$errors[0].$message
        "
        :content-loading="isFetchingInitialData"
        required
      >
        <BaseInput
          v-model.trim="smtpConfig.from_name"
          :invalid="v$.smtpConfig.from_name.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_name"
          @input="v$.smtpConfig.from_name.$touch()"
        />
      </BaseInputGroup>
    </div>

    <BaseButton
      :loading="isSaving"
      :disabled="isSaving"
      :content-loading="isFetchingInitialData"
      class="mt-4"
    >
      <template #left="slotProps">
        <BaseIcon v-if="!isSaving" name="SaveIcon" :class="slotProps.class" />
      </template>
      {{ $t('general.save') }}
    </BaseButton>
  </form>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { required, email, numeric, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useI18n } from 'vue-i18n'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'

const props = defineProps({
  isSaving: {
    type: Boolean,
    require: true,
    default: false,
  },
  isFetchingInitialData: {
    type: Boolean,
    require: true,
    default: false,
  },
})

const emit = defineEmits(['submit-data', 'on-change-driver'])

let isShowPassword = ref(false)
const encryptions = reactive(['tls', 'ssl', 'starttls'])
const { t } = useI18n()

const mailDriverStore = useMailDriverStore()

const smtpConfig = computed(() => {
  return mailDriverStore.smtpConfig
})

const getInputType = computed(() => {
  if (isShowPassword.value) {
    return 'text'
  }
  return 'password'
})

smtpConfig.value.mail_driver = 'smtp'

const rules = computed(() => {
  return {
    smtpConfig: {
      mail_driver: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_host: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_port: {
        required: helpers.withMessage(t('validation.required'), required),
        numeric: helpers.withMessage(t('validation.numbers_only'), numeric),
      },
      mail_encryption: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      from_mail: {
        required: helpers.withMessage(t('validation.required'), required),
        email: helpers.withMessage(t('validation.email_incorrect'), email),
      },
      from_name: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => mailDriverStore)
)

async function saveEmailConfig() {
  v$.value.$touch()
  if (!v$.value.$invalid) {
    emit('submit-data', mailDriverStore.smtpConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.smtpConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.smtpConfig.mail_driver)
}
</script>
