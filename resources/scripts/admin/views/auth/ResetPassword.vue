<template>
  <form id="loginForm" @submit.prevent="onSubmit">
    <BaseInputGroup
      :error="errorEmail"
      :label="$t('login.email')"
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

    <BaseInputGroup
      :error="errorPassword"
      :label="$t('login.password')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="formData.password"
        :invalid="v$.password.$error"
        type="password"
        name="password"
        @input="v$.password.$touch()"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :error="errorConfirmPassword"
      :label="$t('login.retype_password')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="formData.password_confirmation"
        :invalid="v$.password_confirmation.$error"
        type="password"
        name="password"
        @input="v$.password_confirmation.$touch()"
      />
    </BaseInputGroup>

    <BaseButton :loading="isLoading" type="submit" variant="primary">
      {{ $t('login.reset_password') }}
    </BaseButton>
  </form>
</template>

<script type="text/babel" setup>
import { ref, computed, reactive } from 'vue'
import useVuelidate from '@vuelidate/core'
import { required, email, minLength, sameAs } from '@vuelidate/validators'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import { handleError } from '@/scripts/helpers/error-handling'

const notificationStore = useNotificationStore()
const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const formData = reactive({
  email: '',
  password: '',
  password_confirmation: '',
})

const isLoading = ref(false)

const rules = computed(() => {
  return {
    email: { required, email },
    password: {
      required,
      minLength: minLength(8),
    },
    password_confirmation: {
      sameAsPassword: sameAs(formData.password),
    },
  }
})

const v$ = useVuelidate(rules, formData)

const errorEmail = computed(() => {
  if (!v$.value.email.$error) {
    return ''
  }
  if (v$.value.email.required.$invalid) {
    return t('validation.required')
  }
  if (v$.value.email.email) {
    return t('validation.email_incorrect')
  }
  return false
})

const errorPassword = computed(() => {
  if (!v$.value.password.$error) {
    return ''
  }

  if (v$.value.password.required.$invalid) {
    return t('validation.required')
  }
  if (v$.value.password.minLength) {
    return t('validation.password_min_length', {
      count: v$.value.password.minLength.$params.min,
    })
  }
  return false
})

const errorConfirmPassword = computed(() => {
  if (!v$.value.password_confirmation.$error) {
    return ''
  }
  if (v$.value.password_confirmation.sameAsPassword.$invalid) {
    return t('validation.password_incorrect')
  }
  return false
})

async function onSubmit(e) {
  v$.value.$touch()

  if (!v$.value.$invalid) {
    try {
      let data = {
        email: formData.email,
        password: formData.password,
        password_confirmation: formData.password_confirmation,
        token: route.params.token,
      }
      isLoading.value = true
      let res = await axios.post('/api/v1/auth/reset/password', data)
      isLoading.value = false
      if (res.data) {
        notificationStore.showNotification({
          type: 'success',
          message: t('login.password_reset_successfully'),
        })
        router.push('/login')
      }
    } catch (err) {
      handleError(err)
        isLoading.value = false
      if (err.response && err.response.status === 403) {
        // notificationStore.showNotification({
        //   type: 'error',
        //   message: t('validation.email_incorrect'),
        // })
      }
    }
  }
}
</script>
