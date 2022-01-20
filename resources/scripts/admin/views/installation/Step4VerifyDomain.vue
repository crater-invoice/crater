<template>
  <BaseWizardStep
    :title="$t('wizard.verify_domain.title')"
    :description="$t('wizard.verify_domain.desc')"
  >
    <div class="w-full md:w-2/3">
      <BaseInputGroup
        :label="$t('wizard.verify_domain.app_domain')"
        :error="v$.app_domain.$error && v$.app_domain.$errors[0].$message"
        required
      >
        <BaseInput
          v-model="formData.app_domain"
          :invalid="v$.app_domain.$error"
          type="text"
          @input="v$.app_domain.$touch()"
        />
      </BaseInputGroup>
    </div>

    <p class="mt-4 mb-0 text-sm text-gray-600">Notes:</p>
    <ul class="w-full text-gray-600 list-disc list-inside">
      <li class="text-sm leading-8">
        App domain should not contain
        <b class="inline-block px-1 bg-gray-100 rounded-sm">https://</b> or
        <b class="inline-block px-1 bg-gray-100 rounded-sm">http</b> in front of
        the domain.
      </li>
      <li class="text-sm leading-8">
        If you're accessing the website on a different port, please mention the
        port. For example:
        <b class="inline-block px-1 bg-gray-100">localhost:8080</b>
      </li>
    </ul>

    <BaseButton
      :loading="isSaving"
      :disabled="isSaving"
      class="mt-8"
      @click="verifyDomain"
    >
      {{ $t('wizard.verify_domain.verify_now') }}
    </BaseButton>
  </BaseWizardStep>
</template>

<script setup>
import { required, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { ref, inject, computed, reactive } from 'vue'
import { useInstallationStore } from '@/scripts/admin/stores/installation'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useI18n } from 'vue-i18n'

const emit = defineEmits(['next'])

const formData = reactive({
  app_domain: window.location.origin.replace(/(^\w+:|^)\/\//, ''),
})
const isSaving = ref(false)
const { t } = useI18n()
const utils = inject('utils')
const isUrl = (value) => utils.checkValidDomainUrl(value)

const installationStore = useInstallationStore()
const notificationStore = useNotificationStore()

const rules = {
  app_domain: {
    required: helpers.withMessage(t('validation.required'), required),
    isUrl: helpers.withMessage(t('validation.invalid_domain_url'), isUrl),
  },
}

const v$ = useVuelidate(
  rules,
  computed(() => formData)
)

async function verifyDomain() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true

  try {
    await installationStore.setInstallationDomain(formData)
    await installationStore.installationLogin()
    let driverRes = await installationStore.checkAutheticated()

    if (driverRes.data) {
      emit('next', 4)
    }

    isSaving.value = false
  } catch (e) {
    notificationStore.showNotification({
      type: 'error',
      message: t('wizard.verify_domain.failed'),
    })

    isSaving.value = false
  }
}
</script>
