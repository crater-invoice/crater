<template>
  <form id="loginForm" @submit.prevent="onSubmit">
    <BaseInputGroup
      :error="v$.email.$error && v$.email.$errors[0].$message"
      :label="$t('login.enter_email')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="formData.email"
        :invalid="v$.email.$error"
        focus
        type="email"
        name="email"
        @input="v$.email.$touch()"
      />
    </BaseInputGroup>
    <BaseButton
      :loading="isLoading"
      :disabled="isLoading"
      type="submit"
      variant="primary"
    >
      <div v-if="!isSent">
        {{ $t('validation.send_reset_link') }}
      </div>
      <div v-else>
        {{ $t('validation.not_yet') }}
      </div>
    </BaseButton>

    <div class="mt-4 mb-4 text-sm">
      <router-link
        to="/login"
        class="text-sm text-primary-400 hover:text-gray-700"
      >
        {{ $t('general.back_to_login') }}
      </router-link>
    </div>
  </form>
</template>

<script type="text/babel" setup>
import axios from 'axios'
import { reactive, ref, computed } from 'vue'
import { required, email, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useI18n } from 'vue-i18n'
import { handleError } from '@/scripts/helpers/error-handling'

const notificationStore = useNotificationStore()
const { t } = useI18n()

const formData = reactive({
  email: '',
})

const isSent = ref(false)
const isLoading = ref(false)

const rules = {
  email: {
    required: helpers.withMessage(t('validation.required'), required),
    email: helpers.withMessage(t('validation.email_incorrect'), email),
  },
}

const v$ = useVuelidate(rules, formData)

async function onSubmit(e) {
  v$.value.$touch()

  if (!v$.value.$invalid) {
    try {
      isLoading.value = true
      let res = await axios.post('/api/v1/auth/password/email', formData)
      if (res.data) {
        notificationStore.showNotification({
          type: 'success',
          message: 'Mail sent successfully',
        })
      }
      isSent.value = true
      isLoading.value = false
    } catch (err) {
      handleError(err)
      isLoading.value = false
    }
  }
}
</script>
