<template>
  <form
    id="loginForm"
    class="space-y-6"
    action="#"
    method="POST"
    @submit.prevent="validateBeforeSubmit"
  >
    <BaseInputGroup
      :error="
        v$.loginData.email.$error && v$.loginData.email.$errors[0].$message
      "
      :label="$t('login.email')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="authStore.loginData.email"
        type="email"
        :invalid="v$.loginData.email.$error"
        @input="v$.loginData.email.$touch()"
      />
    </BaseInputGroup>
    <BaseInputGroup
      :error="
        v$.loginData.password.$error &&
        v$.loginData.password.$errors[0].$message
      "
      :label="$t('login.password')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="authStore.loginData.password"
        :invalid="v$.loginData.password.$error"
        type="password"
        @input="v$.loginData.password.$touch()"
      />
    </BaseInputGroup>
    <div class="flex items-center justify-between">
      <router-link
        :to="{ name: 'customer.forgot-password' }"
        class="text-sm text-primary-400 hover:text-gray-500 dark:hover:text-primary-500"
      >
        {{ $t('login.forgot_password') }}
      </router-link>
    </div>

    <div>
      <BaseButton
        :loading="isLoading"
        :disabled="isLoading"
        type="submit"
        class="w-full justify-center"
      >
        <template #left="slotProps">
          <BaseIcon name="LockClosedIcon" :class="slotProps.class" />
        </template>
        {{ $t('login.login') }}
      </BaseButton>
    </div>
  </form>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useVuelidate } from '@vuelidate/core'
import { required, email, helpers } from '@vuelidate/validators'
import { useAuthStore } from '@/scripts/customer/stores/auth'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const { t } = useI18n()

let isLoading = ref(false)

const rules = computed(() => {
  return {
    loginData: {
      email: {
        required: helpers.withMessage(t('validation.required'), required),
        email: helpers.withMessage(t('validation.email_incorrect'), email),
      },
      password: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
  }
})

const v$ = useVuelidate(rules, authStore)

async function validateBeforeSubmit() {
  v$.value.loginData.$touch()
  if (v$.value.loginData.$invalid) {
    return true
  }
  isLoading.value = true
  let data = {
    ...authStore.loginData,
    company: route.params.company,
  }

  try {
    await authStore.login(data)
    isLoading.value = false
    return router.push({ name: 'customer.dashboard' })
    authStore.$reset()
  } catch (error) {
    isLoading.value = false
  }
}
</script>
