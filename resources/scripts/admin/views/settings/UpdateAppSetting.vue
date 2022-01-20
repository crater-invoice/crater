<template>
  <BaseSettingCard
    :title="$t('settings.update_app.title')"
    :description="$t('settings.update_app.description')"
  >
    <div class="pb-8 ml-0">
      <label class="text-sm not-italic font-medium input-label">
        {{ $t('settings.update_app.current_version') }}
      </label>

      <div
        class="
          box-border
          flex
          w-16
          p-3
          my-2
          text-sm text-gray-600
          bg-gray-200
          border border-gray-200 border-solid
          rounded-md
          version
        "
      >
        {{ currentVersion }}
      </div>

      <BaseButton
        :loading="isCheckingforUpdate"
        :disabled="isCheckingforUpdate || isUpdating"
        variant="primary-outline"
        class="mt-6"
        @click="checkUpdate"
      >
        {{ $t('settings.update_app.check_update') }}
      </BaseButton>

      <BaseDivider v-if="isUpdateAvailable" class="mt-6 mb-4" />

      <div v-show="!isUpdating" v-if="isUpdateAvailable" class="mt-4 content">
        <BaseHeading type="heading-title" class="mb-2">
          {{ $t('settings.update_app.avail_update') }}
        </BaseHeading>

        <div class="rounded-md bg-primary-50 p-4 mb-3">
          <div class="flex">
            <div class="shrink-0">
              <BaseIcon
                name="InformationCircleIcon"
                class="h-5 w-5 text-primary-400"
                aria-hidden="true"
              />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-primary-800">
                {{ $t('general.note') }}
              </h3>
              <div class="mt-2 text-sm text-primary-700">
                <p>
                  {{ $t('settings.update_app.update_warning') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <label class="text-sm not-italic font-medium input-label">
          {{ $t('settings.update_app.next_version') }}
        </label>
        <br />
        <div
          class="
            box-border
            flex
            w-16
            p-3
            my-2
            text-sm text-gray-600
            bg-gray-200
            border border-gray-200 border-solid
            rounded-md
            version
          "
        >
          {{ updateData.version }}
        </div>

        <div
          class="
            pl-5
            mt-4
            mb-8
            text-sm
            leading-snug
            text-gray-500
            update-description
          "
          style="white-space: pre-wrap; max-width: 480px"
          v-html="description"
        ></div>

        <label class="text-sm not-italic font-medium input-label">
          {{ $t('settings.update_app.requirements') }}
        </label>

        <table class="w-1/2 mt-2 border-2 border-gray-200 BaseTable-fixed">
          <tr
            v-for="(ext, i) in requiredExtentions"
            :key="i"
            class="p-2 border-2 border-gray-200"
          >
            <td width="70%" class="p-2 text-sm truncate">
              {{ i }}
            </td>
            <td width="30%" class="p-2 text-sm text-right">
              <span
                v-if="ext"
                class="inline-block w-4 h-4 ml-3 mr-2 bg-green-500 rounded-full"
              />
              <span
                v-else
                class="inline-block w-4 h-4 ml-3 mr-2 bg-red-500 rounded-full"
              />
            </td>
          </tr>
        </table>

        <BaseButton class="mt-10" variant="primary" @click="onUpdateApp">
          {{ $t('settings.update_app.update') }}
        </BaseButton>
      </div>

      <div v-if="isUpdating" class="relative flex justify-between mt-4 content">
        <div>
          <h6 class="m-0 mb-3 font-medium sw-section-title">
            {{ $t('settings.update_app.update_progress') }}
          </h6>
          <p
            class="mb-8 text-sm leading-snug text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('settings.update_app.progress_text') }}
          </p>
        </div>
        <LoadingIcon
          class="absolute right-0 h-6 m-1 animate-spin text-primary-400"
        />
      </div>
      <ul v-if="isUpdating" class="w-full p-0 list-none">
        <li
          v-for="step in updateSteps"
          :key="step.stepUrl"
          class="
            flex
            justify-between
            w-full
            py-3
            border-b border-gray-200 border-solid
            last:border-b-0
          "
        >
          <p class="m-0 text-sm leading-8">{{ $t(step.translationKey) }}</p>
          <div class="flex flex-row items-center">
            <span v-if="step.time" class="mr-3 text-xs text-gray-500">
              {{ step.time }}
            </span>
            <span
              :class="statusClass(step)"
              class="block py-1 text-sm text-center uppercase rounded-full"
              style="width: 88px"
            >
              {{ getStatus(step) }}
            </span>
          </div>
        </li>
      </ul>
    </div>
  </BaseSettingCard>
</template>

<script setup>
import { useNotificationStore } from '@/scripts/stores/notification'
import axios from 'axios'
import LoadingIcon from '@/scripts/components/icons/LoadingIcon.vue'
import { reactive, ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { handleError } from '@/scripts/helpers/error-handling'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useExchangeRateStore } from '@/scripts/admin/stores/exchange-rate'
import { useDialogStore } from '@/scripts/stores/dialog'

const notificationStore = useNotificationStore()
const dialogStore = useDialogStore()
const { t, tm } = useI18n()
const comapnyStore = useCompanyStore()
const exchangeRateStore = useExchangeRateStore()

let isUpdateAvailable = ref(false)
let isCheckingforUpdate = ref(false)
let description = ref('')
let currentVersion = ref('')
let requiredExtentions = ref(null)
let deletedFiles = ref(null)
let isUpdating = ref(false)

const updateSteps = reactive([
  {
    translationKey: 'settings.update_app.download_zip_file',
    stepUrl: '/api/v1/update/download',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'settings.update_app.unzipping_package',
    stepUrl: '/api/v1/update/unzip',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'settings.update_app.copying_files',
    stepUrl: '/api/v1/update/copy',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'settings.update_app.deleting_files',
    stepUrl: '/api/v1/update/delete',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'settings.update_app.running_migrations',
    stepUrl: '/api/v1/update/migrate',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'settings.update_app.finishing_update',
    stepUrl: '/api/v1/update/finish',
    time: null,
    started: false,
    completed: false,
  },
])

const updateData = reactive({
  isMinor: Boolean,
  installed: '',
  version: '',
})

let minPhpVesrion = ref(null)

window.addEventListener('beforeunload', (event) => {
  if (isUpdating.value) {
    event.returnValue = 'Update is in progress!'
  }
})

// Created

axios.get('/api/v1/app/version').then((res) => {
  currentVersion.value = res.data.version
})

// comapnyStore
//   .fetchCompanySettings(['bulk_exchange_rate_configured'])
//   .then((res) => {
//     isExchangeRateUpdated.value =
//       res.data.bulk_exchange_rate_configured === 'YES'
//   })

// Comuted props

const allowToUpdate = computed(() => {
  if (requiredExtentions.value !== null) {
    return Object.keys(requiredExtentions.value).every((k) => {
      return requiredExtentions.value[k]
    })
  }
  return true
})

function statusClass(step) {
  const status = getStatus(step)

  switch (status) {
    case 'pending':
      return 'text-primary-800 bg-gray-200'
    case 'finished':
      return 'text-teal-500 bg-teal-100'
    case 'running':
      return 'text-blue-400 bg-blue-100'
    case 'error':
      return 'text-danger bg-red-200'
    default:
      return ''
  }
}

async function checkUpdate() {
  try {
    isCheckingforUpdate.value = true
    let response = await axios.get('/api/v1/check/update')
    isCheckingforUpdate.value = false
    if (!response.data.version) {
      notificationStore.showNotification({
        title: 'Info!',
        type: 'info',
        message: t('settings.update_app.latest_message'),
      })
      return
    }

    if (response.data) {
      updateData.isMinor = response.data.is_minor
      updateData.version = response.data.version.version
      description.value = response.data.version.description
      requiredExtentions.value = response.data.version.extensions
      isUpdateAvailable.value = true
      minPhpVesrion.value = response.data.version.minimum_php_version
      deletedFiles.value = response.data.version.deleted_files
    }
  } catch (e) {
    isUpdateAvailable.value = false
    isCheckingforUpdate.value = false
    handleError(e)
  }
}

function onUpdateApp() {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.update_app.update_warning'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        let path = null
        if (!allowToUpdate.value) {
          notificationStore.showNotification({
            type: 'error',
            message:
              'Your current configuration does not match the update requirements. Please try again after all the requirements are fulfilled.',
          })
          return true
        }
        for (let index = 0; index < updateSteps.length; index++) {
          let currentStep = updateSteps[index]
          try {
            isUpdating.value = true
            currentStep.started = true
            let updateParams = {
              version: updateData.version,
              installed: currentVersion.value,
              deleted_files: deletedFiles.value,
              path: path || null,
            }

            let requestResponse = await axios.post(
              currentStep.stepUrl,
              updateParams
            )
            currentStep.completed = true
            if (requestResponse.data && requestResponse.data.path) {
              path = requestResponse.data.path
            }
            // on finish

            if (
              currentStep.translationKey ==
              'settings.update_app.finishing_update'
            ) {
              isUpdating.value = false
              notificationStore.showNotification({
                type: 'success',
                message: t('settings.update_app.update_success'),
              })

              setTimeout(() => {
                location.reload()
              }, 3000)
            }
          } catch (error) {
            currentStep.started = false
            currentStep.completed = true
            handleError(error)
            onUpdateFailed(currentStep.translationKey)
            return false
          }
        }
      }
    })
}

function onUpdateFailed(translationKey) {
  let stepName = t(translationKey)
  if (stepName.value) {
    onUpdateApp()
    return
  }
  isUpdating.value = false
}

function getStatus(step) {
  if (step.started && step.completed) {
    return 'finished'
  } else if (step.started && !step.completed) {
    return 'running'
  } else if (!step.started && !step.completed) {
    return 'pending'
  } else {
    return 'error'
  }
}
</script>

<style>
.update-description ul {
  list-style: disc !important;
}

.update-description li {
  margin-bottom: 4px;
}
</style>
