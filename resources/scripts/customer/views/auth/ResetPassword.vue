<template>
  <form id="loginForm" @submit.prevent="onSubmit">
    <BaseInputGroup
      :error="v$.email.$error && v$.email.$errors[0].$message"
      :label="$t('login.email')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="loginData.email"
        type="email"
        name="email"
        :invalid="v$.email.$error"
        @input="v$.email.$touch()"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :error="v$.password.$error && v$.password.$errors[0].$message"
      :label="$t('login.password')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="loginData.password"
        :type="isShowPassword ? 'text' : 'password'"
        name="password"
        :invalid="v$.password.$error"
        @input="v$.password.$touch()"
      >
        <template #right>
          <EyeOffIcon
            v-if="isShowPassword"
            class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
            @click="isShowPassword = !isShowPassword"
          />
          <EyeIcon
            v-else
            class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
            @click="isShowPassword = !isShowPassword"
          /> </template
      ></BaseInput>
    </BaseInputGroup>

    <BaseInputGroup
      :error="
        v$.password_confirmation.$error &&
        v$.password_confirmation.$errors[0].$message
      "
      :label="$t('login.retype_password')"
      class="mb-4"
      required
    >
      <BaseInput
        v-model="loginData.password_confirmation"
        type="password"
        name="password"
        :invalid="v$.password_confirmation.$error"
        @input="v$.password_confirmation.$touch()"
      />
    </BaseInputGroup>

    <BaseButton type="submit" variant="primary">
      {{ $t('login.reset_password') }}
    </BaseButton>
  </form>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import useVuelidate from '@vuelidate/core'
import { useGlobalStore } from '@/scripts/customer/stores/global'
import {
  required,
  helpers,
  minLength,
  sameAs,
  email,
} from '@vuelidate/validators'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/scripts/customer/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()
const loginData = reactive({
  email: '',
  password: '',
  password_confirmation: '',
})

const globalStore = useGlobalStore()

let isShowPassword = ref(false)
let isLoading = ref(false)

const rules = computed(() => {
  return {
    email: {
      required: helpers.withMessage(t('validation.required'), required),
      email: helpers.withMessage(t('validation.email_incorrect'), email),
    },
    password: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.password_min_length', { count: 8 }),
        minLength(8)
      ),
    },
    password_confirmation: {
      sameAsPassword: helpers.withMessage(
        t('validation.password_incorrect'),
        sameAs(loginData.password)
      ),
    },
  }
})

const v$ = useVuelidate(rules, loginData)

async function onSubmit(e) {
  v$.value.$touch()

  if (!v$.value.$invalid) {
    let data = {
      email: loginData.email,
      password: loginData.password,
      password_confirmation: loginData.password_confirmation,
      token: route.params.token,
    }
    isLoading.value = true
    let res = authStore.resetPassword(data, route.params.company)
    isLoading.value = false
    if (res.data) {
      router.push({ name: 'customer.login' })
    }
  }
}
</script>
