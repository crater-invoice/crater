<template>
  <!-- Host -->
  <BaseInputGroup
    :label="$t(`${pre_t}.host`)"
    :error="v$.host.$error && v$.host.$errors[0].$message"
    required
  >
    <BaseInput
      v-model.trim="mailSenderStore.sesConfig.host"
      :invalid="v$.host.$error"
      type="text"
      @input="v$.host.$touch()"
    />
  </BaseInputGroup>

  <!-- Port -->
  <BaseInputGroup
    :label="$t(`${pre_t}.port`)"
    :error="v$.port.$error && v$.port.$errors[0].$message"
    required
  >
    <BaseInput
      v-model.trim="mailSenderStore.sesConfig.port"
      type="text"
      :invalid="v$.port.$error"
      @input="v$.port.$touch()"
    />
  </BaseInputGroup>

  <!-- Encryption -->
  <BaseInputGroup
    :label="$t(`${pre_t}.encryption`)"
    :error="v$.encryption.$error && v$.encryption.$errors[0].$message"
    required
  >
    <BaseMultiselect
      v-model.trim="mailSenderStore.sesConfig.encryption"
      :options="encryptions"
      :searchable="true"
      :show-labels="false"
      :placeholder="$t('general.select_option')"
      :invalid="v$.encryption.$error"
      @input="v$.encryption.$touch()"
    />
  </BaseInputGroup>

  <!-- SES Key -->
  <BaseInputGroup
    :label="$t(`${pre_t}.ses_key`)"
    :error="v$.ses_key.$error && v$.ses_key.$errors[0].$message"
    required
  >
    <BaseInput
      v-model.trim="mailSenderStore.sesConfig.ses_key"
      type="text"
      :invalid="v$.ses_key.$error"
      @input="v$.ses_key.$touch()"
    />
  </BaseInputGroup>

  <!-- SES Secret -->
  <BaseInputGroup
    :label="$t(`${pre_t}.ses_secret`)"
    :error="v$.ses_secret.$error && v$.ses_secret.$errors[0].$message"
    required
  >
    <BaseInput
      v-model="mailSenderStore.sesConfig.ses_secret"
      :type="getInputType"
      autocomplete="off"
      :invalid="v$.ses_secret.$error"
      @input="v$.ses_secret.$touch()"
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
</template>

<script setup>
import { computed, ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, email, numeric, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'

const pre_t = 'settings.mail_sender.ses_config'
const { t } = useI18n()

const props = defineProps({
  mailSenderStore: {
    type: Object,
    require: true,
    default: Object,
  },
})

let isShowPassword = ref(false)
const getInputType = computed(() => {
  if (isShowPassword.value) {
    return 'text'
  }
  return 'password'
})
const encryptions = props.mailSenderStore.mail_encryptions

const rules = computed(() => {
  return {
    host: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    port: {
      required: helpers.withMessage(t('validation.required'), required),
      numeric,
    },
    encryption: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    ses_key: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    ses_secret: {
      required: helpers.withMessage(t('validation.required'), required),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => props.mailSenderStore.sesConfig)
)
</script>
