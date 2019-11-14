<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.update_app.title') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.update_app.description') }}
        </p>
        <p class="page-sub-title">Current version: 1.0.0</p>
        <base-button size="large" color="theme" @click="checkUpdate">
          <font-awesome-icon :class="{'update': isUpdateAvail}" style="margin-right: 5px;" icon="sync-alt" />
          {{ $t('settings.update_app.check_update') }}
        </base-button>
        <div v-if="isUpdateAvail" class="mt-4 content">
          <label class="input-label">{{ $t('settings.update_app.avail_update') }}</label>
          <p class="page-sub-title">Latest version: 2.0.0</p>
          <base-button size="large" color="theme" @click="onUpdateApp">
            {{ $t('settings.update_app.update') }}
          </base-button>
        </div>
        <div v-if="isShowProgressBar" class="progress mt-4">
          <div
            :style="[{'width': progress+'%'}]"
            class="progress-bar progress-bar-striped progress-bar-animated"
            role="progressbar"
            aria-valuenow="0"
            aria-valuemin="0"
            aria-valuemax="100"
          />
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
      isUpdateAvail: false,
      progress: 10,
      interval: null
    }
  },
  watch: {
  },
  mounted () {
  },
  methods: {
    onUpdateApp () {
      this.isShowProgressBar = true
      this.interval = setInterval(() => {
        if (this.progress >= 100) {
          clearInterval(this.interval)
          setTimeout(() => {
            this.isShowProgressBar = false
          }, 1000)
        }
        this.progress += 10
      }, 250)
    },
    checkUpdate () {
      this.isUpdateAvail = !this.isUpdateAvail
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
