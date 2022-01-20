<template>
  <form @submit.prevent="saveEmailConfig">
    <BaseInputGrid>
      <BaseInputGroup
        :label="$t('settings.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_driver.$error &&
          v$.sesConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="mailDriverStore.sesConfig.mail_driver"
          :content-loading="isFetchingInitialData"
          :options="mailDrivers"
          :can-deselect="false"
          :invalid="v$.sesConfig.mail_driver.$error"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.host')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_host.$error &&
          v$.sesConfig.mail_host.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.sesConfig.mail_host"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_host"
          :invalid="v$.sesConfig.mail_host.$error"
          @input="v$.sesConfig.mail_host.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.port')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_port.$error &&
          v$.sesConfig.mail_port.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.sesConfig.mail_port"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_port"
          :invalid="v$.sesConfig.mail_port.$error"
          @input="v$.sesConfig.mail_port.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.encryption')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_encryption.$error &&
          v$.sesConfig.mail_encryption.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model.trim="mailDriverStore.sesConfig.mail_encryption"
          :content-loading="isFetchingInitialData"
          :options="encryptions"
          :invalid="v$.sesConfig.mail_encryption.$error"
          placeholder="Select option"
          @input="v$.sesConfig.mail_encryption.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.from_mail.$error &&
          v$.sesConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.sesConfig.from_mail"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_mail"
          :invalid="v$.sesConfig.from_mail.$error"
          @input="v$.sesConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.from_name.$error &&
          v$.sesConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.sesConfig.from_name"
          :content-loading="isFetchingInitialData"
          type="text"
          name="name"
          :invalid="v$.sesConfig.from_name.$error"
          @input="v$.sesConfig.from_name.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.ses_key')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_ses_key.$error &&
          v$.sesConfig.mail_ses_key.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.sesConfig.mail_ses_key"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_ses_key"
          :invalid="v$.sesConfig.mail_ses_key.$error"
          @input="v$.sesConfig.mail_ses_key.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.ses_secret')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.sesConfig.mail_ses_secret.$error &&
          v$.mail_ses_secret.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.sesConfig.mail_ses_secret"
          :content-loading="isFetchingInitialData"
          :type="getInputType"
          name="mail_ses_secret"
          autocomplete="off"
          :invalid="v$.sesConfig.mail_ses_secret.$error"
          @input="v$.sesConfig.mail_ses_secret.$touch()"
        >
          <template #right>
            <BaseIcon
              v-if="isShowPassword"
              class="mr-1 text-gray-500 cursor-pointer"
              name="EyeOffIcon"
              @click="isShowPassword = !isShowPassword"
            />
            <BaseIcon
              v-else
              class="mr-1 text-gray-500 cursor-pointer"
              name="EyeIcon"
              @click="isShowPassword = !isShowPassword"
            />
          </template>
        </BaseInput>
      </BaseInputGroup>
    </BaseInputGrid>

    <div class="flex my-10">
      <BaseButton
        :disabled="isSaving"
        :content-loading="isFetchingInitialData"
        :loading="isSaving"
        variant="primary"
        type="submit"
      >
        <template #left="slotProps">
          <BaseIcon v-if="!isSaving" name="SaveIcon" :class="slotProps.class" />
        </template>
        {{ $t('general.save') }}
      </BaseButton>
      <slot />
    </div>
  </form>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { required, email, numeric, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useI18n } from 'vue-i18n'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'

const props = defineProps({
  configData: {
    type: Object,
    require: true,
    default: Object,
  },
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
  mailDrivers: {
    type: Array,
    require: true,
    default: Array,
  },
})

const emit = defineEmits(['submit-data', 'on-change-driver'])

const mailDriverStore = useMailDriverStore()
const { t } = useI18n()

let isShowPassword = ref(false)
const encryptions = reactive(['tls', 'ssl', 'starttls'])

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

const getInputType = computed(() => {
  if (isShowPassword.value) {
    return 'text'
  }
  return 'password'
})

onMounted(() => {
  for (const key in mailDriverStore.sesConfig) {
    if (props.configData.hasOwnProperty(key)) {
      mailDriverStore.sesConfig[key] = props.configData[key]
    }
  }
})

async function saveEmailConfig() {
  v$.value.sesConfig.$touch()
  if (!v$.value.sesConfig.$invalid) {
    emit('submit-data', mailDriverStore.sesConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.sesConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.sesConfig.mail_driver)
}
</script>
