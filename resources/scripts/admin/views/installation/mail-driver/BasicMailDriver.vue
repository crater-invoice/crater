<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.basicMailConfig.mail_driver.$error &&
          v$.basicMailConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="basicMailConfig.mail_driver"
          :invalid="v$.basicMailConfig.mail_driver.$error"
          :options="mailDriverStore.mail_drivers"
          :can-deselect="false"
          :content-loading="isFetchingInitialData"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
      <BaseInputGroup
        :label="$t('wizard.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.basicMailConfig.from_name.$error &&
          v$.basicMailConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="basicMailConfig.from_name"
          :invalid="v$.basicMailConfig.from_name.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          name="name"
          @input="v$.basicMailConfig.from_name.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.basicMailConfig.from_mail.$error &&
          v$.basicMailConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="basicMailConfig.from_mail"
          :invalid="v$.basicMailConfig.from_mail.$error"
          :content-loading="isFetchingInitialData"
          type="text"
          @input="v$.basicMailConfig.from_mail.$touch()"
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
import { required, email, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useI18n } from 'vue-i18n'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'
import { computed } from 'vue'

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

const mailDriverStore = useMailDriverStore()

const basicMailConfig = computed(() => {
  return mailDriverStore.basicMailConfig
})

const mailDrivers = computed(() => {
  return mailDriverStore.mail_drivers
})

basicMailConfig.value.mail_driver = 'mail'

const rules = computed(() => {
  return {
    basicMailConfig: {
      mail_driver: {
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

function saveEmailConfig() {
  v$.value.$touch()
  if (!v$.value.$invalid) {
    emit('submit-data', mailDriverStore.basicMailConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.basicMailConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore?.basicMailConfig?.mail_driver)
}
</script>
