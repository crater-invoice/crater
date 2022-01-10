<template>
  <BasePage>
    <BasePageHeader :title="$t('modules.title')">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />
        <BaseBreadcrumbItem :title="$tc('modules.module', 2)" to="#" active />
      </BaseBreadcrumb>
    </BasePageHeader>

    <!-- Modules Section  -->
    <div v-if="hasApiToken && moduleStore.modules">
      <BaseTabGroup class="-mb-5" @change="setStatusFilter">
        <BaseTab :title="$t('general.all')" filter="" />
        <BaseTab :title="$t('modules.installed')" filter="INSTALLED" />
      </BaseTabGroup>

      <!-- Modules Card Placeholder  -->
      <div
        v-if="isFetchingModule"
        class="
          grid
          mt-6
          w-full
          grid-cols-1
          items-start
          gap-6
          lg:grid-cols-2
          xl:grid-cols-3
        "
      >
        <ModuleCardPlaceholder />
        <ModuleCardPlaceholder />
        <ModuleCardPlaceholder />
      </div>

      <!-- Modules Card  -->
      <div v-else>
        <div
          v-if="modules && modules.length"
          class="
            grid
            mt-6
            w-full
            grid-cols-1
            items-start
            gap-6
            lg:grid-cols-2
            xl:grid-cols-3
          "
        >
          <div v-for="(moduleData, idx) in modules" :key="idx">
            <ModuleCard :data="moduleData" />
          </div>
        </div>
        <div v-else class="mt-24">
          <label class="flex items-center justify-center text-gray-500">
            {{ $t('modules.no_modules_installed') }}
          </label>
        </div>
      </div>
    </div>

    <BaseCard v-else class="mt-6">
      <h6 class="text-gray-900 text-lg font-medium">
        {{ $t('modules.connect_installation') }}
      </h6>
      <p class="mt-1 text-sm text-gray-500">
        {{
          $t('modules.api_token_description', {
            url: globalStore.config.base_url.replace(/^http:\/\//, ''),
          })
        }}
      </p>

      <!-- Api Token Form  -->
      <div class="grid lg:grid-cols-2 mt-6">
        <form action="" class="mt-6" @submit.prevent="submitApiToken">
          <BaseInputGroup
            :label="$t('modules.api_token')"
            required
            :error="v$.api_token.$error && v$.api_token.$errors[0].$message"
          >
            <BaseInput
              v-model="moduleStore.currentUser.api_token"
              :invalid="v$.api_token.$error"
              @input="v$.api_token.$touch()"
            />
          </BaseInputGroup>

          <div class="flex space-x-2">
            <BaseButton class="mt-6" :loading="isSaving" type="submit">
              <template #left="slotProps">
                <BaseIcon name="SaveIcon" :class="slotProps.class" />
              </template>
              {{ $t('general.save') }}
            </BaseButton>

            <a
              :href="`${globalStore.config.base_url}/auth/customer/register`"
              class="mt-6 block"
              target="_blank"
            >
              <BaseButton variant="primary-outline" type="button">
                Sign up & Get Token
              </BaseButton>
            </a>
          </div>
        </form>
      </div>
    </BaseCard>
  </BasePage>
</template>

<script setup>
import { useModuleStore } from '@/scripts/admin/stores/module'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { computed, onMounted, reactive, ref, watchEffect } from 'vue'
import {
  required,
  minLength,
  maxLength,
  helpers,
  requiredUnless,
} from '@vuelidate/validators'
import ModuleCard from './partials/ModuleCard.vue'
import ModuleCardPlaceholder from './partials/ModuleCardPlaceholder.vue'
import { useVuelidate } from '@vuelidate/core'
import { useI18n } from 'vue-i18n'

const moduleStore = useModuleStore()
const globalStore = useGlobalStore()
const activeTab = ref('')

const { t } = useI18n()
let isSaving = ref(false)
let isFetchingModule = ref(false)

const rules = computed(() => {
  return {
    api_token: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
  }
})

const hasApiToken = computed(() => {
  if (moduleStore.apiToken) {
    fetchModulesData()
    return true
  }

  return false
})

const v$ = useVuelidate(
  rules,
  computed(() => moduleStore.currentUser)
)

const modules = computed(() => {
  if (activeTab.value === 'INSTALLED') {
    return moduleStore.modules.filter((_m) => _m.installed)
  }

  return moduleStore.modules
})

async function fetchModulesData() {
  isFetchingModule.value = true

  await moduleStore.fetchModules().then(() => {
    isFetchingModule.value = false
  })
}

async function submitApiToken() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true

  moduleStore
    .checkApiToken(moduleStore.currentUser.api_token)
    .then((response) => {
      if (response.data.success) {
        saveApiTokenToSettings()
        return
      }
      isSaving.value = false
      return
    })
}

async function saveApiTokenToSettings() {
  try {
    await globalStore
      .updateGlobalSettings({
        data: {
          settings: {
            api_token: moduleStore.currentUser.api_token,
          },
        },
        message: 'settings.preferences.updated_message',
      })
      .then((response) => {
        if (response.data.success) {
          moduleStore.apiToken = moduleStore.currentUser.api_token
          return
        }
      })

    isSaving.value = false
  } catch (err) {
    isSaving.value = false
    console.error(err)
    return
  }
}

function setStatusFilter(data) {
  activeTab.value = data.filter
}
</script>
