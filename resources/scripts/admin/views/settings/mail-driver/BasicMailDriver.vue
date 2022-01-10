<template>
  <form @submit.prevent="saveEmailConfig">
    <BaseInputGrid>
      <BaseInputGroup
        :label="$t('settings.mail.driver')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.basicMailConfig.mail_driver.$error &&
          v$.basicMailConfig.mail_driver.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="mailDriverStore.basicMailConfig.mail_driver"
          :content-loading="isFetchingInitialData"
          :options="mailDrivers"
          :can-deselect="false"
          :invalid="v$.basicMailConfig.mail_driver.$error"
          @update:modelValue="onChangeDriver"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_mail')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.basicMailConfig.from_mail.$error &&
          v$.basicMailConfig.from_mail.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.basicMailConfig.from_mail"
          :content-loading="isFetchingInitialData"
          type="text"
          name="from_mail"
          :invalid="v$.basicMailConfig.from_mail.$error"
          @input="v$.basicMailConfig.from_mail.$touch()"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('settings.mail.from_name')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.basicMailConfig.from_name.$error &&
          v$.basicMailConfig.from_name.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model.trim="mailDriverStore.basicMailConfig.from_name"
          :content-loading="isFetchingInitialData"
          type="text"
          name="name"
          :invalid="v$.basicMailConfig.from_name.$error"
          @input="v$.basicMailConfig.from_name.$touch()"
        />
      </BaseInputGroup>
    </BaseInputGrid>
    <div class="flex mt-8">
      <BaseButton
        :content-loading="isFetchingInitialData"
        :disabled="isSaving"
        :loading="isSaving"
        variant="primary"
        type="submit"
      >
        <template #left="slotProps">
          <BaseIcon v-if="!isSaving" :class="slotProps.class" name="SaveIcon" />
        </template>
        {{ $t('general.save') }}
      </BaseButton>
      <slot />
    </div>
  </form>
</template>

<script setup>
import { onMounted, computed } from 'vue'
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

onMounted(() => {
  for (const key in mailDriverStore.basicMailConfig) {
    if (props.configData.hasOwnProperty(key)) {
      mailDriverStore.$patch((state) => {
        state.basicMailConfig[key] = props.configData[key]
      })
    }
  }
})

async function saveEmailConfig() {
  v$.value.basicMailConfig.$touch()
  if (!v$.value.basicMailConfig.$invalid) {
    emit('submit-data', mailDriverStore.basicMailConfig)
  }
  return false
}

function onChangeDriver() {
  v$.value.basicMailConfig.mail_driver.$touch()
  emit('on-change-driver', mailDriverStore.basicMailConfig.mail_driver)
}
</script>
