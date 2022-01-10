<template>
  <form @submit.prevent="saveEmailConfig">
    <BaseInputGrid>
      <BaseInputGroup
        :label="$t('settings.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_driver.$error &&
          v$.mailgunConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="mailDriverStore.mailgunConfig.mail_driver"
          :content-loading="isFetchingInitialData"
          :options="mailDrivers"
          :can-deselect="false"
          :invalid="v$.mailgunConfig.mail_driver.$error"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.mailgun_domain')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_mailgun_domain.$error &&
          v$.mailgunConfig.mail_mailgun_domain.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.mailgunConfig.mail_mailgun_domain"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mailgun_domain"
          :invalid="v$.mailgunConfig.mail_mailgun_domain.$error"
          @input="v$.mailgunConfig.mail_mailgun_domain.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.mailgun_secret')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_mailgun_secret.$error &&
          v$.mailgunConfig.mail_mailgun_secret.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.mailgunConfig.mail_mailgun_secret"
          :content-loading="isFetchingInitialData"
          :type="getInputType"
          name="mailgun_secret"
          autocomplete="off"
          :invalid="v$.mailgunConfig.mail_mailgun_secret.$error"
          @input="v$.mailgunConfig.mail_mailgun_secret.$touch()"
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
        :label="$t('settings.mail.mailgun_endpoint')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_mailgun_endpoint.$error &&
          v$.mailgunConfig.mail_mailgun_endpoint.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.mailgunConfig.mail_mailgun_endpoint"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mailgun_endpoint"
          :invalid="v$.mailgunConfig.mail_mailgun_endpoint.$error"
          @input="v$.mailgunConfig.mail_mailgun_endpoint.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.from_mail.$error &&
          v$.mailgunConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.mailgunConfig.from_mail"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_mail"
          :invalid="v$.mailgunConfig.from_mail.$error"
          @input="v$.mailgunConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.from_name.$error &&
          v$.mailgunConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.mailgunConfig.from_name"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_name"
          :invalid="v$.mailgunConfig.from_name.$error"
          @input="v$.mailgunConfig.from_name.$touch()"
        />
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
import { onMounted, ref, computed } from 'vue'
import { required, email, helpers } from '@vuelidate/validators'
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

const getInputType = computed(() => {
  if (isShowPassword.value) {
    return 'text'
  }
  return 'password'
})

const rules = computed(() => {
  return {
    mailgunConfig: {
      mail_driver: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_mailgun_domain: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_mailgun_endpoint: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      mail_mailgun_secret: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      from_mail: {
        required: helpers.withMessage(t('validation.required'), required),
        email,
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
  for (const key in mailDriverStore.mailgunConfig) {
    if (props.configData.hasOwnProperty(key)) {
      mailDriverStore.mailgunConfig[key] = props.configData[key]
    }
  }
})

async function saveEmailConfig() {
  v$.value.mailgunConfig.$touch()
  if (!v$.value.mailgunConfig.$invalid) {
    emit('submit-data', mailDriverStore.mailgunConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.mailgunConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.mailgunConfig.mail_driver)
}
</script>
