<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.update_app.title') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.update_app.description') }}
        </p>
        <base-button size="large" icon="sync-alt" color="theme" @click="onUpdateApp">
          {{ $t('settings.update_app.update') }}
        </base-button>
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
    }
  }
}
</script>
