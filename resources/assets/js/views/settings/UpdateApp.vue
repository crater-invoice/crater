<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.update_app.title') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.update_app.description') }}
        </p>
        <label class="input-label">{{ $t('settings.update_app.current_version') }}</label><br>
        <label class="version mb-4">{{ currentVersion }}</label>
        <base-button :outline="true" :disabled="isCheckingforUpdate || isUpdating" size="large" color="theme" class="mb-4" @click="checkUpdate">
          <font-awesome-icon :class="{'update': isCheckingforUpdate}" style="margin-right: 10px;" icon="sync-alt" />
          {{ $t('settings.update_app.check_update') }}
        </base-button>
        <hr>
        <div v-show="!isUpdating" v-if="isUpdateAvailable" class="mt-4 content">
          <h3 class="page-title mb-3">{{ $t('settings.update_app.avail_update') }}</h3>
          <label class="input-label">{{ $t('settings.update_app.next_version') }}</label><br>
          <label class="version">{{ updateData.version }}</label>
          <p class="page-sub-title" style="white-space: pre-wrap;">{{ description }}</p>
          <base-button size="large" icon="rocket" color="theme" @click="onUpdateApp">
            {{ $t('settings.update_app.update') }}
          </base-button>
        </div>
        <div v-if="isUpdating" class="mt-4 content">
          <h3 class="page-title">{{ $t('settings.update_app.update_progress') }}</h3>
          <p class="page-sub-title">
            {{ $t('settings.update_app.progress_text') }}
          </p>
          <font-awesome-icon icon="spinner" class="fa-spin"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      isShowProgressBar: false,
      isUpdateAvailable: false,
      isUpdating: false,
      isCheckingforUpdate: false,
      progress: 10,
      interval: null,
      description: '',
      currentVersion: '',
      updateData: {
        isMinor: Boolean,
        installed: '',
        version: ''
      }
    }
  },
  created () {
    window.addEventListener('beforeunload', (event) => {
      if (this.isUpdating) {
        event.returnValue = 'Update is in progress!'
      }
    })
  },
  mounted () {
    window.axios.get('/api/settings/app/version').then((res) => {
      this.currentVersion = res.data.version
    })
  },
  methods: {
    closeHandler () {
      console.log('closing')
    },
    async onUpdateApp () {
      try {
        this.isUpdating = true
        this.updateData.installed = this.currentVersion
        let res = await window.axios.post('/api/update', this.updateData)

        if (res.data.success) {
          setTimeout(async () => {
            await window.axios.post('/api/update/finish', this.updateData)

            window.toastr['success'](this.$t('settings.update_app.update_success'))
            this.currentVersion = this.updateData.version
            this.isUpdateAvailable = false

            setTimeout(() => {
              location.reload()
            }, 2000)
          }, 1000)
        } else {
          console.log(res.data)
          window.toastr['error'](res.data.error)
        }
      } catch (e) {
        console.log(e)
        window.toastr['error']('Something went wrong')
      }

      this.isUpdating = false
    },

    async checkUpdate () {
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
    }
  }
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
