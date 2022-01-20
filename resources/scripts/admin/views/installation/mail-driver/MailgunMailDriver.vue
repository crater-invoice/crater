<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:mb-6 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_driver.$error &&
          v$.mailgunConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="mailgunConfig.mail_driver"
          :options="mailDriverStore.mail_drivers"
          :can-deselect="false"
          :invalid="v$.mailgunConfig.mail_driver.$error"
          :content-loading="isFetchingInitialData"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.mailgun_domain')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_mailgun_domain.$error &&
          v$.mailgunConfig.mail_mailgun_domain.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailgunConfig.mail_mailgun_domain"
          :invalid="v$.mailgunConfig.mail_mailgun_domain.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mailgun_domain"
          @input="v$.mailgunConfig.mail_mailgun_domain.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:mb-6 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.mailgun_secret')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_mailgun_secret.$error &&
          v$.mailgunConfig.mail_mailgun_secret.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailgunConfig.mail_mailgun_secret"
          :invalid="v$.mailgunConfig.mail_mailgun_secret.$error"
          :type="getInputType"
          :content-loading="isFetchingInitialData"
          name="mailgun_secret"
          autocomplete="off"
          data-lpignore="true"
          @input="v$.mailgunConfig.mail_mailgun_secret.$touch()"
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

      <BaseInputGroup
        :label="$t('wizard.mail.mailgun_endpoint')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.mail_mailgun_endpoint.$error &&
          v$.mailgunConfig.mail_mailgun_endpoint.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailgunConfig.mail_mailgun_endpoint"
          :invalid="v$.mailgunConfig.mail_mailgun_endpoint.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="mailgun_endpoint"
          @input="v$.mailgunConfig.mail_mailgun_endpoint.$touch()"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
      <BaseInputGroup
        :label="$t('wizard.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.from_mail.$error &&
          v$.mailgunConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailgunConfig.from_mail"
          name="from_mail"
          type="text"
          :invalid="v$.mailgunConfig.from_mail.$error"
          :content-loading="isFetchingInitialData"
          @input="v$.mailgunConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.mailgunConfig.from_name.$error &&
          v$.mailgunConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailgunConfig.from_name"
          :invalid="v$.mailgunConfig.from_name.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_name"
          @input="v$.mailgunConfig.from_name.$touch()"
        />
      </BaseInputGroup>
    </div>

    <BaseButton
      :loading="loading"
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
import { required, email, helpers } from '@vuelidate/validators'
import { useI18n } from 'vue-i18n'
import useVuelidate from '@vuelidate/core'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'
import { computed, onMounted, ref } from 'vue'

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

const mailDriverStore = useMailDriverStore()
const { t } = useI18n()

const mailgunConfig = computed(() => {
  return mailDriverStore.mailgunConfig
})

const getInputType = computed(() => {
  if (isShowPassword.value) {
    return 'text'
  }
  return 'password'
})

mailgunConfig.value.mail_driver = 'mailgun'

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

function saveEmailConfig() {
  v$.value.$touch()
  if (!v$.value.$invalid) {
    emit('submit-data', mailDriverStore.mailgunConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.mailgunConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.mailgunConfig.mail_driver)
}
</script>
