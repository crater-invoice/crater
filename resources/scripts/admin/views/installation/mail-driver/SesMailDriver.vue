<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_driver.$error &&
          v$.sesConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="sesConfig.mail_driver"
          :options="mailDriverStore.mail_drivers"
          :can-deselect="false"
          :content-loading="isFetchingInitialData"
          :invalid="v$.sesConfig.mail_driver.$error"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.host')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_host.$error &&
          v$.sesConfig.mail_host.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="sesConfig.mail_host"
          :invalid="v$.sesConfig.mail_host.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_host"
          @input="v$.sesConfig.mail_host.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.port')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_port.$error &&
          v$.sesConfig.mail_port.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="sesConfig.mail_port"
          :invalid="v$.sesConfig.mail_port.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_port"
          @input="v$.sesConfig.mail_port.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.encryption')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_encryption.$error &&
          v$.sesConfig.mail_encryption.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model.trim="sesConfig.mail_encryption"
          :invalid="v$.sesConfig.mail_encryption.$error"
          :options="encryptions"
          :content-loading="isFetchingInitialData"
          @input="v$.sesConfig.mail_encryption.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.from_mail.$error &&
          v$.sesConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="sesConfig.from_mail"
          :invalid="v$.sesConfig.from_mail.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_mail"
          @input="v$.sesConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.from_name.$error &&
          v$.sesConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="sesConfig.from_name"
          :invalid="v$.sesConfig.from_name.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="name"
          @input="v$.sesConfig.from_name.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
      <BaseInputGroup
        :label="$t('wizard.mail.ses_key')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_ses_key.$error &&
          v$.sesConfig.mail_ses_key.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="sesConfig.mail_ses_key"
          :invalid="v$.sesConfig.mail_ses_key.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_ses_key"
          @input="v$.sesConfig.mail_ses_key.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.ses_secret')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_ses_secret.$error &&
          v$.sesConfig.mail_ses_secret.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="sesConfig.mail_ses_secret"
          :invalid="v$.sesConfig.mail_ses_secret.$error"
          :type="getInputType"
          :content-loading="isFetchingInitialData"
          name="mail_ses_secret"
          autocomplete="off"
          data-lpignore="true"
          @input="v$.sesConfig.mail_ses_secret.$touch()"
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
import { computed, reactive, ref } from 'vue'
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

const { t } = useI18n()
const encryptions = reactive(['tls', 'ssl', 'starttls'])
let isShowPassword = ref(false)

const mailDriverStore = useMailDriverStore()

const sesConfig = computed(() => {
  return mailDriverStore.sesConfig
})

sesConfig.value.mail_driver = 'ses'

const rules = computed(() => {
  return {
    sesConfig: {
      mail_driver: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_host: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_port: {
        required: helpers.withMessage(t('validation.required'), required),
        numeric,
      },
      mail_ses_key: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_ses_secret: {
        required: helpers.withMessage(t('validation.required'), required),
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
    emit('submit-data', mailDriverStore.sesConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.sesConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.sesConfig.mail_driver)
}
</script>
