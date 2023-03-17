<template>
  <!-- Domain -->
  <BaseInputGroup
    :label="$t(`${pre_t}.domain`)"
    :error="v$.domain.$error && v$.domain.$errors[0].$message"
    required
  >
    <BaseInput
      v-model.trim="mailSenderStore.mailgunConfig.domain"
      :invalid="v$.domain.$error"
      type="text"
      @input="v$.domain.$touch()"
    />
  </BaseInputGroup>

  <!-- Mailgun Secret -->
  <BaseInputGroup
    :label="$t(`${pre_t}.secret`)"
    :error="v$.secret.$error && v$.secret.$errors[0].$message"
    required
  >
    <BaseInput
      v-model="mailSenderStore.mailgunConfig.secret"
      :type="getInputType"
      autocomplete="off"
      :invalid="v$.secret.$error"
      @input="v$.secret.$touch()"
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

  <!-- Mailgun Endpoint -->
  <BaseInputGroup
    :label="$t(`${pre_t}.endpoint`)"
    :error="v$.endpoint.$error && v$.endpoint.$errors[0].$message"
    required
  >
    <BaseInput
      v-model.trim="mailSenderStore.mailgunConfig.endpoint"
      type="text"
      :invalid="v$.endpoint.$error"
      @input="v$.endpoint.$touch()"
    />
  </BaseInputGroup>
</template>

<script setup>
import { computed, ref } from "vue"
import { useI18n } from "vue-i18n"
import { required, email, numeric, helpers } from "@vuelidate/validators"
import { useVuelidate } from "@vuelidate/core"

const pre_t = "settings.mail_sender.mailgun_config"
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
    return "text"
  }
  return "password"
})

const rules = computed(() => {
  return {
    domain: {
      required: helpers.withMessage(t("validation.required"), required),
    },
    endpoint: {
      required: helpers.withMessage(t("validation.required"), required),
    },
    secret: {
      required: helpers.withMessage(t("validation.required"), required),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => props.mailSenderStore.mailgunConfig)
)
</script>
