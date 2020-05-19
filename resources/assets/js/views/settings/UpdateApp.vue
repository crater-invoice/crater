<template>
  <div class="setting-main-container update-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.update_app.title') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.update_app.description') }}
        </p>
        <label class="input-label">{{
          $t('settings.update_app.current_version')
        }}</label
        ><br />
        <label class="version mb-4">{{ currentVersion }}</label>
        <base-button
          :outline="true"
          :disabled="isCheckingforUpdate || isUpdating"
          size="large"
          color="theme"
          class="mb-4"
          @click="checkUpdate"
        >
          <font-awesome-icon
            :class="{ update: isCheckingforUpdate }"
            style="margin-right: 10px;"
            icon="sync-alt"
          />
          {{ $t('settings.update_app.check_update') }}
        </base-button>
        <hr />
        <div v-show="!isUpdating" v-if="isUpdateAvailable" class="mt-4 content">
          <h3 class="page-title mb-3">
            {{ $t('settings.update_app.avail_update') }}
          </h3>
          <label class="input-label">{{
            $t('settings.update_app.next_version')
          }}</label
          ><br />
          <label class="version">{{ updateData.version }}</label>
          <p class="page-sub-title" style="white-space: pre-wrap;">{{ description }}</p>
          <base-button
            size="large"
            icon="rocket"
            color="theme"
            @click="onUpdateApp"
          >
            {{ $t('settings.update_app.update') }}
          </base-button>
        </div>
        <div v-if="isUpdating" class="mt-4 content">
          <div class="d-flex flex-row justify-content-between">
            <div>
              <h3 class="page-title">
                {{ $t('settings.update_app.update_progress') }}
              </h3>
              <p class="page-sub-title">
                {{ $t('settings.update_app.progress_text') }}
              </p>
            </div>
            <font-awesome-icon icon="spinner" class="update-spinner fa-spin" />
          </div>
          <ul class="update-steps-container">
            <li class="update-step" v-for="step in updateSteps">
              <p class="update-step-text">{{ $t(step.translationKey) }}</p>
              <div class="update-status-container">
                <span v-if="step.time" class="update-time">{{
                  step.time
                }}</span>
                <span :class="'update-status status-' + getStatus(step)">{{
                  getStatus(step)
                }}</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
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
      updateSteps: [
        {
          translationKey: 'settings.update_app.download_zip_file',
          stepUrl: '/api/update/download',
          time: null,
          started: false,
          completed: false,
        },
        {
          translationKey: 'settings.update_app.unzipping_package',
          stepUrl: '/api/update/unzip',
          time: null,
          started: false,
          completed: false,
        },
        {
          translationKey: 'settings.update_app.copying_files',
          stepUrl: '/api/update/copy',
          time: null,
          started: false,
          completed: false,
        },
        {
          translationKey: 'settings.update_app.running_migrations',
          stepUrl: '/api/update/migrate',
          time: null,
          started: false,
          completed: false,
        },
        {
          translationKey: 'settings.update_app.finishing_update',
          stepUrl: '/api/update/finish',
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
    }
  },
  created() {
    window.addEventListener('beforeunload', (event) => {
      if (this.isUpdating) {
        event.returnValue = 'Update is in progress!'
      }
    })
  },
  mounted() {
    window.axios.get('/api/settings/app/version').then((res) => {
      this.currentVersion = res.data.version
    })
  },
  methods: {
    closeHandler() {
      console.log('closing')
    },
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
    async checkUpdate() {
      try {
        this.isCheckingforUpdate = true
        let response = await window.axios.get('/api/check/update')
        this.isCheckingforUpdate = false

        if (!response.data.version) {
          window.toastr['info'](this.$t('settings.update_app.latest_message'))

          return
        }

        if (response.data) {
          this.updateData.isMinor = response.data.is_minor
          this.updateData.version = response.data.version.version
          this.description = response.data.version.description
          this.isUpdateAvailable = true
        }
      } catch (e) {
        this.isUpdateAvailable = false
        this.isCheckingforUpdate = false
        window.toastr['error']('Something went wrong')
      }
    },
    async onUpdateApp() {
      let path = null

      for (let index = 0; index < this.updateSteps.length; index++) {
        let currentStep = this.updateSteps[index]
        try {
          this.isUpdating = true
          currentStep.started = true
          let updateParams = {
            version: this.updateData.version,
            installed: this.currentVersion,
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
            window.toastr['success'](
              this.$t('settings.update_app.update_success')
            )

            setTimeout(() => {
              location.reload()
            }, 3000)
          }
        } catch (error) {
          currentStep.started = false
          currentStep.completed = true
          window.toastr['error'](this.$t('validation.something_went_wrong'))
          this.onUpdateFailed(currentStep.translationKey)
          return false
        }
      }
    },
    onUpdateFailed(translationKey) {
      let stepName = this.$t(translationKey) 
      swal({
        title: this.$t('settings.update_app.update_failed'),
        text: this.$tc('settings.update_app.update_failed_text', stepName, {step: stepName}),
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

<style scoped>
.update {
  transform: rotate(360deg);
  animation: rotating 1s linear infinite;
}

@keyframes rotating {
  0% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
