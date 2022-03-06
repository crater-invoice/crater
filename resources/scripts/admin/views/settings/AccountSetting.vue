<template>
  <form class="relative" @submit.prevent="updateUserData">
    <BaseSettingCard
      :title="$t('settings.account_settings.account_settings')"
      :description="$t('settings.account_settings.section_description')"
    >
      <BaseInputGrid>
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
          :error="v$.name.$error && v$.name.$errors[0].$message"
          required
        >
          <BaseInput
            v-model="userForm.name"
            :invalid="v$.name.$error"
            @input="v$.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.account_settings.email')"
          :error="v$.email.$error && v$.email.$errors[0].$message"
          required
        >
          <BaseInput
            v-model="userForm.email"
            :invalid="v$.email.$error"
            @input="v$.email.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :error="v$.password.$error && v$.password.$errors[0].$message"
          :label="$tc('settings.account_settings.password')"
        >
          <BaseInput
            v-model="userForm.password"
            type="password"
            @input="v$.password.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.account_settings.confirm_password')"
          :error="
            v$.confirm_password.$error &&
            v$.confirm_password.$errors[0].$message
          "
        >
          <BaseInput
            v-model="userForm.confirm_password"
            type="password"
            @input="v$.confirm_password.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup :label="$tc('settings.language')">
          <BaseMultiselect
            v-model="userForm.language"
            :options="globalStore.config.languages"
            label="name"
            value-prop="code"
            track-by="name"
            open-direction="top"
          />
        </BaseInputGroup>
      </BaseInputGrid>

      <BaseButton :loading="isSaving" :disabled="isSaving" class="mt-6">
        <template #left="slotProps">
          <BaseIcon
            v-if="!isSaving"
            name="SaveIcon"
            :class="slotProps.class"
          ></BaseIcon>
        </template>
        {{ $tc('settings.company_info.save') }}
      </BaseButton>
    </BaseSettingCard>
  </form>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useI18n } from 'vue-i18n'
import {
  helpers,
  sameAs,
  email,
  required,
  minLength,
} from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const userStore = useUserStore()
const globalStore = useGlobalStore()
const companyStore = useCompanyStore()
const { t } = useI18n()

let isSaving = ref(false)
let avatarFileBlob = ref(null)
let imgFiles = ref([])
const isAdminAvatarRemoved = ref(false)

if (userStore.currentUser.avatar) {
  imgFiles.value.push({
    image: userStore.currentUser.avatar,
  })
}

const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    email: {
      required: helpers.withMessage(t('validation.required'), required),
      email: helpers.withMessage(t('validation.email_incorrect'), email),
    },
    password: {
      minLength: helpers.withMessage(
        t('validation.password_length', { count: 8 }),
        minLength(8)
      ),
    },
    confirm_password: {
      sameAsPassword: helpers.withMessage(
        t('validation.password_incorrect'),
        sameAs(userForm.password)
      ),
    },
  }
})

const userForm = reactive({
  name: userStore.currentUser.name,
  email: userStore.currentUser.email,
  language:
    userStore.currentUserSettings.language ||
    companyStore.selectedCompanySettings.language,
  password: '',
  confirm_password: '',
})

const v$ = useVuelidate(
  rules,
  computed(() => userForm)
)

function onFileInputChange(fileName, file) {
  avatarFileBlob.value = file
}

function onFileInputRemove() {
  avatarFileBlob.value = null
  isAdminAvatarRemoved.value = true
}

async function updateUserData() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true

  let data = {
    name: userForm.name,
    email: userForm.email,
  }

  try {
    if (
      userForm.password != null &&
      userForm.password !== undefined &&
      userForm.password !== ''
    ) {
      data = { ...data, password: userForm.password }
    }
    // Update Language if changed

    if (userStore.currentUserSettings.language !== userForm.language) {
      await userStore.updateUserSettings({
        settings: {
          language: userForm.language,
        },
      })
    }

    let response = await userStore.updateCurrentUser(data)

    if (response.data.data) {
      isSaving.value = false

      if (avatarFileBlob.value || isAdminAvatarRemoved.value) {
        let avatarData = new FormData()

        if (avatarFileBlob.value) {
          avatarData.append('admin_avatar', avatarFileBlob.value)
        }
        avatarData.append('is_admin_avatar_removed', isAdminAvatarRemoved.value)

        await userStore.uploadAvatar(avatarData)
        avatarFileBlob.value = null
        isAdminAvatarRemoved.value = false
      }

      userForm.password = ''
      userForm.confirm_password = ''
    }
  } catch (error) {
    isSaving.value = false
    return true
  }
}
</script>
