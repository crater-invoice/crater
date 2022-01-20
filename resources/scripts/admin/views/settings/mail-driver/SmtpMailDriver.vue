<template>
  <form @submit.prevent="saveEmailConfig">
    <BaseInputGrid>
      <BaseInputGroup
        :label="$t('settings.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.mail_driver.$error &&
          v$.smtpConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="mailDriverStore.smtpConfig.mail_driver"
          :content-loading="isFetchingInitialData"
          :options="mailDrivers"
          :can-deselect="false"
          :invalid="v$.smtpConfig.mail_driver.$error"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.host')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.mail_host.$error &&
          v$.smtpConfig.mail_host.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.smtpConfig.mail_host"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_host"
          :invalid="v$.smtpConfig.mail_host.$error"
          @input="v$.smtpConfig.mail_host.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :content-loading="isFetchingInitialData"
        :label="$t('settings.mail.username')"
      >
        <BaseInput
          v-model.trim="mailDriverStore.smtpConfig.mail_username"
          :content-loading="isFetchingInitialData"
          type="text"
          name="db_name"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :content-loading="isFetchingInitialData"
        :label="$t('settings.mail.password')"
      >
        <BaseInput
          v-model.trim="mailDriverStore.smtpConfig.mail_password"
          :content-loading="isFetchingInitialData"
          :type="getInputType"
          name="password"
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

      <BaseInputGroup
        :label="$t('settings.mail.port')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.mail_port.$error &&
          v$.smtpConfig.mail_port.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.smtpConfig.mail_port"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mail_port"
          :invalid="v$.smtpConfig.mail_port.$error"
          @input="v$.smtpConfig.mail_port.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.encryption')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.mail_encryption.$error &&
          v$.smtpConfig.mail_encryption.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model.trim="mailDriverStore.smtpConfig.mail_encryption"
          :content-loading="isFetchingInitialData"
          :options="encryptions"
          :searchable="true"
          :show-labels="false"
          placeholder="Select option"
          :invalid="v$.smtpConfig.mail_encryption.$error"
          @input="v$.smtpConfig.mail_encryption.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.from_mail.$error &&
          v$.smtpConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.smtpConfig.from_mail"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_mail"
          :invalid="v$.smtpConfig.from_mail.$error"
          @input="v$.smtpConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.smtpConfig.from_name.$error &&
          v$.smtpConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.smtpConfig.from_name"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_name"
          :invalid="v$.smtpConfig.from_name.$error"
          @input="v$.smtpConfig.from_name.$touch()"
        />
      </BaseInputGroup>
    </BaseInputGrid>

    <div class="flex my-10">
      <BaseButton
        :disabled="isSaving"
        :content-loading="isFetchingInitialData"
        :loading="isSaving"
        type="submit"
        variant="primary"
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
import { reactive, onMounted, ref, computed } from 'vue'
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

const getInputType = computed(() => {
  if (isShowPassword.value) {
    return 'text'
  }
  return 'password'
})

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

onMounted(() => {
  for (const key in mailDriverStore.smtpConfig) {
    if (props.configData.hasOwnProperty(key)) {
      mailDriverStore.smtpConfig[key] = props.configData[key]
    }
  }
})

async function saveEmailConfig() {
  v$.value.smtpConfig.$touch()
  if (!v$.value.smtpConfig.$invalid) {
    emit('submit-data', mailDriverStore.smtpConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.smtpConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.smtpConfig.mail_driver)
}
</script>
