<template>
  <sw-card variant="setting-card">
    <template slot="header">
      <h6 class="sw-section-title">
        {{ $t('settings.update_app.title') }}
      </h6>
      <p
        class="mt-2 text-sm leading-snug text-gray-500"
        style="max-width: 680px"
      >
        {{ $t('settings.update_app.description') }}
      </p>
    </template>

    <div class="m-0">
      <label class="text-sm not-italic font-medium input-label">
        {{ $t('settings.update_app.current_version') }}
      </label>

      <label
        class="box-border flex w-16 p-3 my-2 text-sm text-gray-500 bg-gray-200 border border-gray-200 border-solid rounded-md version"
      >
        {{ currentVersion }}
      </label>

      <sw-button
        :loading="isCheckingforUpdate"
        :disabled="isCheckingforUpdate || isUpdating"
        variant="primary-outline"
        class="mt-6"
        @click="checkUpdate"
      >
        {{ $t('settings.update_app.check_update') }}
      </sw-button>

      <sw-divider v-if="isUpdateAvailable" class="mt-2 mb-4" />

      <div v-show="!isUpdating" v-if="isUpdateAvailable" class="mt-4 content">
        <h6 class="mb-8 sw-section-title">
          {{ $t('settings.update_app.avail_update') }}
        </h6>
        <label class="text-sm not-italic font-medium input-label">
          {{ $t('settings.update_app.next_version') }} </label
        ><br />
        <label
          class="box-border flex w-16 p-3 my-2 text-sm text-gray-500 bg-gray-200 border border-gray-200 border-solid rounded-md version"
        >
          {{ updateData.version }}
        </label>
        <div
          class="pl-5 mt-4 mb-8 text-sm leading-snug text-gray-500 update-description"
          style="white-space: pre-wrap; max-width: 480px"
          v-html="description"
        ></div>

        <label class="text-sm not-italic font-medium input-label">
          {{ $t('settings.update_app.requirements') }}
        </label>
        <table class="w-1/2 mt-2 border-2 border-gray-200 table-fixed">
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
                class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-success"
              />
              <span
                v-else
                class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-danger"
              />
            </td>
          </tr>
        </table>
        <sw-button
          size="lg"
          class="mt-10"
          variant="primary"
          @click="onUpdateApp"
        >
          {{ $t('settings.update_app.update') }}
        </sw-button>
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
        <loading-icon
          class="absolute right-0 h-6 m-1 animate-spin text-primary-400"
        />
      </div>
      <!--  -->
      <ul v-if="isUpdating" class="w-full p-0 list-none">
        <li
          v-for="step in updateSteps"
          class="flex justify-between w-full py-3 border-b border-gray-200 border-solid last:border-b-0"
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
  </sw-card>
</template>

<script>
import LoadingIcon from '../../components/icon/LoadingIcon'
import { mapActions } from 'vuex'
export default {
  components: {
    LoadingIcon,
  },

  data() {
    return {
      isShowProgressBar: false,
      isUpdateAvailable: false,
      isUpdating: false,
      isCheckingforUpdate: false,
      progress: 10,
      interval: null,
      description: '',
      currentVersion: '',
      requiredExtentions: null,
      deletedFiles: null,
      updateSteps: [
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
      ],
      updateData: {
        isMinor: Boolean,
        installed: '',
        version: '',
      },
      requiredExtentions: null,
      minPhpVesrion: null,
    }
  },
  computed: {
    allowToUpdate() {
      if (this.requiredExtentions !== null) {
        return Object.keys(this.requiredExtentions).every((k) => {
          return this.requiredExtentions[k]
        })
      }
    },
    hasUiUpdate() {
      return this.updateData.version != '4.0.0'
    },
  },
  created() {
    window.addEventListener('beforeunload', (event) => {
      if (this.isUpdating) {
        event.returnValue = 'Update is in progress!'
      }
    })
  },

  mounted() {
    window.axios.get('/api/v1/app/version').then((res) => {
      this.currentVersion = res.data.version
    })
  },

  methods: {
    ...mapActions('notification', ['showNotification']),

    getStatus(step) {
      if (step.started && step.completed) {
        return 'finished'
      } else if (step.started && !step.completed) {
        return 'running'
      } else if (!step.started && !step.completed) {
        return 'pending'
      } else {
        return 'error'
      }
    },

    statusClass(step) {
      const status = this.getStatus(step)

      switch (status) {
        case 'pending':
          return 'text-primary-800 bg-gray-200'
          break
        case 'finished':
          return 'text-teal-500 bg-teal-100'
          break
        case 'running':
          return 'text-blue-400 bg-blue-100'
          break
        case 'error':
          return 'text-danger bg-red-200'
          break
      }
    },

    async checkUpdate() {
      try {
        this.isCheckingforUpdate = true
        let response = await window.axios.get('/api/v1/check/update')
        this.isCheckingforUpdate = false

        if (!response.data.version) {
          this.showNotification({
            title: 'Info!',
            type: 'info',
            message: this.$t('settings.update_app.latest_message'),
          })
          return
        }

        if (response.data) {
          this.updateData.isMinor = response.data.is_minor
          this.updateData.version = response.data.version.version
          this.description = response.data.version.description
          this.requiredExtentions = response.data.version.extensions
          this.isUpdateAvailable = true
          this.requiredExtentions = response.data.version.extensions
          this.minPhpVesrion = response.data.version.minimum_php_version
          this.deletedFiles = response.data.version.deleted_files
        }
      } catch (e) {
        this.isUpdateAvailable = false
        this.isCheckingforUpdate = false
        this.showNotification({
          type: 'error',
          message: 'Something went wrong',
        })
      }
    },

    async onUpdateApp() {
      let path = null
      if (!this.allowToUpdate) {
        this.showNotification({
          type: 'error',
          message:
            'Your current configuration does not match the update requirements. Please try again after all the requirements are fulfilled.',
        })
        return true
      }
      for (let index = 0; index < this.updateSteps.length; index++) {
        let currentStep = this.updateSteps[index]
        try {
          this.isUpdating = true
          currentStep.started = true
          let updateParams = {
            version: this.updateData.version,
            installed: this.currentVersion,
            deleted_files: this.deletedFiles,
            path: path || null,
          }

          let requestResponse = await window.axios.post(
            currentStep.stepUrl,
            updateParams
          )
          currentStep.completed = true
          if (requestResponse.data && requestResponse.data.path) {
            path = requestResponse.data.path
          }

          // on finish
          if (
            currentStep.translationKey == 'settings.update_app.finishing_update'
          ) {
            this.isUpdating = false
            this.showNotification({
              type: 'success',
              message: this.$t('settings.update_app.update_success'),
            })

            setTimeout(() => {
              location.reload()
            }, 3000)
          }
        } catch (error) {
          currentStep.started = false
          currentStep.completed = true
          this.showNotification({
            type: 'error',
            message: this.$t('validation.something_went_wrong'),
          })
          this.onUpdateFailed(currentStep.translationKey)
          return false
        }
      }
    },

    onUpdateFailed(translationKey) {
      let stepName = this.$t(translationKey)
      this.$swal({
        title: this.$t('settings.update_app.update_failed'),
        text: this.$tc('settings.update_app.update_failed_text', stepName, {
          step: stepName,
        }),
        buttons: [this.$t('general.cancel'), this.$t('general.retry')],
      }).then(async (value) => {
        if (value) {
          this.onUpdateApp()
          return
        }
        this.isUpdating = false
      })
    },
  },
}
</script>

<style>
.update-description ul li {
  list-style: disc !important;
  margin-bottom: 4px;
}
</style>
