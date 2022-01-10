<template>
  <BaseWizardStep
    :title="$t('wizard.account_info')"
    :description="$t('wizard.account_info_desc')"
  >
    <form action="" @submit.prevent="next">
      <div class="grid grid-cols-1 mb-4 md:grid-cols-2 md:mb-6">
        <BaseInputGroup
          :label="$tc('settings.account_settings.profile_picture')"
        >
          <BaseFileUploader
            :avatar="true"
            :preview-image="avatarUrl"
            @change="onFileInputChange"
            @remove="onFileInputRemove"
          />
        </BaseInputGroup>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
        <BaseInputGroup
          :label="$t('wizard.name')"
          :error="
            v$.userForm.name.$error && v$.userForm.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="userForm.name"
            :invalid="v$.userForm.name.$error"
            type="text"
            name="name"
            @input="v$.userForm.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('wizard.email')"
          :error="
            v$.userForm.email.$error && v$.userForm.email.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="userForm.email"
            :invalid="v$.userForm.email.$error"
            type="text"
            name="email"
            @input="v$.userForm.email.$touch()"
          />
        </BaseInputGroup>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
        <BaseInputGroup
          :label="$t('wizard.password')"
          :error="
            v$.userForm.password.$error &&
            v$.userForm.password.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="userForm.password"
            :invalid="v$.userForm.password.$error"
            :type="isShowPassword ? 'text' : 'password'"
            name="password"
            @input="v$.userForm.password.$touch()"
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
              />
            </template>
          </BaseInput>
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('wizard.confirm_password')"
          :error="
            v$.userForm.confirm_password.$error &&
            v$.userForm.confirm_password.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="userForm.confirm_password"
            :invalid="v$.userForm.confirm_password.$error"
            :type="isShowConfirmPassword ? 'text' : 'password'"
            name="confirm_password"
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
              />
            </template>
          </BaseInput>
        </BaseInputGroup>
      </div>

      <BaseButton :loading="isSaving" :disabled="isSaving" class="mt-4">
        <template #left="slotProps">
          <BaseIcon v-if="!isSaving" name="SaveIcon" :class="slotProps.class" />
        </template>
        {{ $t('wizard.save_cont') }}
      </BaseButton>
    </form>
  </BaseWizardStep>
</template>

<script setup>
import {
  helpers,
  required,
  requiredIf,
  sameAs,
  minLength,
  email,
} from '@vuelidate/validators'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useVuelidate } from '@vuelidate/core'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const emit = defineEmits(['next'])

let isSaving = ref(false)
const isShowPassword = ref(false)
const isShowConfirmPassword = ref(false)
let avatarUrl = ref('')
let avatarFileBlob = ref(null)

const userStore = useUserStore()
const companyStore = useCompanyStore()

const { t } = useI18n()

const userForm = computed(() => {
  return userStore.userForm
})

const rules = computed(() => {
  return {
    userForm: {
      name: {
        required: helpers.withMessage(t('validation.required'), required),
      },
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
      confirm_password: {
        required: helpers.withMessage(
          t('validation.required'),
          requiredIf(userStore.userForm.password)
        ),
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

function onFileInputChange(fileName, file) {
  avatarFileBlob.value = file
}

function onFileInputRemove() {
  avatarFileBlob.value = null
}

async function next() {
  v$.value.userForm.$touch()
  if (v$.value.userForm.$invalid) {
    return true
  }

  isSaving.value = true
  let res = await userStore.updateCurrentUser(userForm.value)
  isSaving.value = false

  if (res.data.data) {
    if (avatarFileBlob.value) {
      let avatarData = new FormData()

      avatarData.append('admin_avatar', avatarFileBlob.value)

      await userStore.uploadAvatar(avatarData)
    }

    const company = res.data.data.companies[0]

    await companyStore.setSelectedCompany(company)
    emit('next', 6)
  }
}
</script>
