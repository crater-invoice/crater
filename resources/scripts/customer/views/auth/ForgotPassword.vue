<template>
  <form id="loginForm" @submit.prevent="validateBeforeSubmit">
    <BaseInputGroup
      :error="v$.email.$error && v$.email.$errors[0].$message"
      :label="$t('login.enter_email')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="formData.email"
        type="email"
        name="email"
        :invalid="v$.email.$error"
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
        to="login"
        class="text-sm text-primary-400 hover:text-gray-700"
      >
        {{ $t('general.back_to_login') }}
      </router-link>
    </div>
  </form>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { required, email, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/scripts/customer/stores/auth'
import { useRouter, useRoute } from 'vue-router'

// // store
const authStore = useAuthStore()
const { t } = useI18n()
const route = useRoute()

// local state

const formData = reactive({
  email: '',
  company: '',
})
const isSent = ref(false)
const isLoading = ref(false)

// validation

const rules = computed(() => {
  return {
    email: {
      required: helpers.withMessage(t('validation.required'), required),
      email: helpers.withMessage(t('validation.email_incorrect'), email),
    },
  }
})

const v$ = useVuelidate(rules, formData)

// methods

function validateBeforeSubmit(e) {
  v$.value.$touch()
  if (v$.value.$invalid) {
    return true
  }
  isLoading.value = true
  let data = {
    ...formData,
    company: route.params.company,
  }

  authStore
    .forgotPassword(data)
    .then((res) => {
      isLoading.value = false
    })
    .catch((err) => {
      isLoading.value = false
    })
  isSent.value = true
}
</script>
