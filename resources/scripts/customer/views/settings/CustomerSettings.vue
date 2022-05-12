<template>
  <form class="relative h-full mt-4" @submit.prevent="updateCustomerData">
    <BaseCard>
      <div>
        <h6 class="font-bold text-left">
          {{ $t('settings.account_settings.account_settings') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-left text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.account_settings.section_description') }}
        </p>
      </div>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2 mt-6">
        <BaseInputGroup
          :label="$tc('settings.account_settings.profile_picture')"
        >
          <BaseFileUploader
            v-model="imgFiles"
            :avatar="true"
            accept="image/*"
            @change="onFileInputChange"
            @remove="onFileInputRemove"
          />
        </BaseInputGroup>

        <!-- Empty Column -->
        <span></span>

        <BaseInputGroup
          :label="$tc('settings.account_settings.name')"
          :error="
            v$.userForm.name.$error && v$.userForm.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="userStore.userForm.name"
            :invalid="v$.userForm.name.$error"
            @input="v$.userForm.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.account_settings.email')"
          :error="
            v$.userForm.email.$error && v$.userForm.email.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="userStore.userForm.email"
            :invalid="v$.userForm.email.$error"
            @input="v$.userForm.email.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :error="
            v$.userForm.password.$error &&
            v$.userForm.password.$errors[0].$message
          "
          :label="$tc('settings.account_settings.password')"
        >
          <BaseInput
            v-model="userStore.userForm.password"
            :type="isShowPassword ? 'text' : 'password'"
            :invalid="v$.userForm.password.$error"
            @input="v$.userForm.password.$touch()"
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
              /> </template
          ></BaseInput>
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.account_settings.confirm_password')"
          :error="
            v$.userForm.confirm_password.$error &&
            v$.userForm.confirm_password.$errors[0].$message
          "
        >
          <BaseInput
            v-model="userStore.userForm.confirm_password"
            :type="isShowConfirmPassword ? 'text' : 'password'"
            :invalid="v$.userForm.confirm_password.$error"
            @input="v$.userForm.confirm_password.$touch()"
          >
            <template #right>
              <BaseIcon
                v-if="isShowConfirmPassword"
                name="EyeOffIcon"
                class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                @click="isShowConfirmPassword = !isShowConfirmPassword"
              />
              <BaseIcon
                v-else
                name="EyeIcon"
                class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                @click="isShowConfirmPassword = !isShowConfirmPassword"
              /> </template
          ></BaseInput>
        </BaseInputGroup>
      </div>

      <BaseButton :loading="isSaving" :disabled="isSaving" class="mt-6">
        <template #left="slotProps">
          <BaseIcon v-if="!isSaving" name="SaveIcon" :class="slotProps.class" />
        </template>
        {{ $t('general.save') }}
      </BaseButton>
    </BaseCard>
  </form>
</template>

<script setup>
import { SaveIcon } from '@heroicons/vue/solid'
import { ref, computed } from 'vue'
import { useGlobalStore } from '@/scripts/customer/stores/global'
import { useUserStore } from '@/scripts/customer/stores/user'
import { useI18n } from 'vue-i18n'
import {
  helpers,
  sameAs,
  email,
  required,
  minLength,
} from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useRoute } from 'vue-router'

const userStore = useUserStore()
const globalStore = useGlobalStore()
const route = useRoute()
const { t, tm } = useI18n()

// Local State
let imgFiles = ref([])
let isSaving = ref(false)
let avatarFileBlob = ref(null)
let isShowPassword = ref(false)
let isShowConfirmPassword = ref(false)
const isCustomerAvatarRemoved = ref(false)

if (userStore.userForm.avatar) {
  imgFiles.value.push({
    image: userStore.userForm.avatar,
  })
}

// Validation
const rules = computed(() => {
  return {
    userForm: {
      name: {
        required: helpers.withMessage(t('validation.required'), required),
        minLength: helpers.withMessage(
          t('validation.name_min_length', { count: 3 }),
          minLength(3)
        ),
      },
      email: {
        required: helpers.withMessage(t('validation.required'), required),
        email: helpers.withMessage(t('validation.email_incorrect'), email),
      },
      password: {
        minLength: helpers.withMessage(
          t('validation.password_min_length', { count: 8 }),
          minLength(8)
        ),
      },
      confirm_password: {
        sameAsPassword: helpers.withMessage(
          t('validation.password_incorrect'),
          sameAs(userStore.userForm.password)
        ),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => userStore)
)

// created

// methods

function onFileInputChange(fileName, file) {
  avatarFileBlob.value = file
}

function onFileInputRemove() {
  avatarFileBlob.value = null
  isCustomerAvatarRemoved.value = true
}

function updateCustomerData() {
  v$.value.userForm.$touch()

  if (v$.value.userForm.$invalid) {
    return true
  }
  isSaving.value = true

  let data = new FormData()
  data.append('name', userStore.userForm.name)
  data.append('email', userStore.userForm.email)

  if (
    userStore.userForm.password != null &&
    userStore.userForm.password !== undefined &&
    userStore.userForm.password !== ''
  ) {
    data.append('password', userStore.userForm.password)
  }
  if (avatarFileBlob.value) {
    data.append('customer_avatar', avatarFileBlob.value)
  }
  data.append('is_customer_avatar_removed', isCustomerAvatarRemoved.value)

  userStore
    .updateCurrentUser({
      data,
      message: tm('settings.account_settings.updated_message'),
    })
    .then((res) => {
      if (res.data.data) {
        isSaving.value = false
        userStore.$patch((state) => {
          state.userForm.password = ''
          state.userForm.confirm_password = ''
        })
        avatarFileBlob.value = null
        isCustomerAvatarRemoved.value = false
      }
    })
    .catch((error) => {
      isSaving.value = false
    })
}
</script>
