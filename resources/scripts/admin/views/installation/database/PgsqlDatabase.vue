<template>
  <form action="" @submit.prevent="next">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:mb-6 md:mb-6">
      <BaseInputGroup
        :label="$t('wizard.database.app_url')"
        :content-loading="isFetchingInitialData"
        :error="v$.app_url.$error && v$.app_url.$errors[0].$message"
        required
      >
        <BaseInput
          v-model="databaseData.app_url"
          :content-loading="isFetchingInitialData"
          :invalid="v$.app_url.$error"
          type="text"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.database.connection')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.database_connection.$error &&
          v$.database_connection.$errors[0].$message
        "
        required
      >
        <BaseMultiselect
          v-model="databaseData.database_connection"
          :content-loading="isFetchingInitialData"
          :invalid="v$.database_connection.$error"
          :options="connections"
          :can-deselect="false"
          :can-clear="false"
          @update:modelValue="onChangeConnection"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.database.port')"
        :content-loading="isFetchingInitialData"
        :error="v$.database_port.$error && v$.database_port.$errors[0].$message"
        required
      >
        <BaseInput
          v-model="databaseData.database_port"
          :content-loading="isFetchingInitialData"
          :invalid="v$.database_port.$error"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.database.db_name')"
        :content-loading="isFetchingInitialData"
        :error="v$.database_name.$error && v$.database_name.$errors[0].$message"
        required
      >
        <BaseInput
          v-model="databaseData.database_name"
          :content-loading="isFetchingInitialData"
          :invalid="v$.database_name.$error"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.database.username')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.database_username.$error &&
          v$.database_username.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model="databaseData.database_username"
          :content-loading="isFetchingInitialData"
          :invalid="v$.database_username.$error"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :content-loading="isFetchingInitialData"
        :label="$t('wizard.database.password')"
      >
        <BaseInput
          v-model="databaseData.database_password"
          :content-loading="isFetchingInitialData"
          type="password"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('wizard.database.host')"
        :content-loading="isFetchingInitialData"
        :error="
          v$.database_hostname.$error &&
          v$.database_hostname.$errors[0].$message
        "
        required
      >
        <BaseInput
          v-model="databaseData.database_hostname"
          :content-loading="isFetchingInitialData"
          :invalid="v$.database_hostname.$error"
        />
      </BaseInputGroup>
    </div>

    <BaseButton
      v-show="!isFetchingInitialData"
      :content-loading="isFetchingInitialData"
      type="submit"
      class="mt-4"
      :loading="isSaving"
      :disabled="isSaving"
    >
      <template #left="slotProps">
        <BaseIcon v-if="!isSaving" name="SaveIcon" :class="slotProps.class" />
      </template>
      {{ $t('wizard.save_cont') }}
    </BaseButton>
  </form>
</template>

<script setup>
import { computed, onMounted, reactive, inject } from 'vue'
import { useInstallationStore } from '@/scripts/admin/stores/installation'
import { helpers, required, numeric } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useI18n } from 'vue-i18n'

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
})

const emit = defineEmits(['submit-data', 'on-change-driver'])

const connections = reactive(['sqlite', 'mysql', 'pgsql'])
const { t } = useI18n()
const utils = inject('utils')

const installationStore = useInstallationStore()

const databaseData = computed(() => {
  return installationStore.currentDataBaseData
})

onMounted(() => {
  for (const key in databaseData.value) {
    if (props.configData.hasOwnProperty(key)) {
      databaseData.value[key] = props.configData[key]
    }
  }
})

const isUrl = (value) => utils.checkValidUrl(value)

const rules = {
  database_connection: {
    required: helpers.withMessage(t('validation.required'), required),
  },
  database_hostname: {
    required: helpers.withMessage(t('validation.required'), required),
  },
  database_port: {
    required: helpers.withMessage(t('validation.required'), required),
    numeric: numeric,
  },
  database_name: {
    required: helpers.withMessage(t('validation.required'), required),
  },
  database_username: {
    required: helpers.withMessage(t('validation.required'), required),
  },
  app_url: {
    required: helpers.withMessage(t('validation.required'), required),
    isUrl: helpers.withMessage(t('validation.invalid_url'), isUrl),
  },
}

const v$ = useVuelidate(rules, databaseData.value)

function next() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  emit('submit-data', databaseData.value)
}

function onChangeConnection() {
  v$.value.database_connection.$touch()
  emit('on-change-driver', databaseData.value.database_connection)
}
</script>
