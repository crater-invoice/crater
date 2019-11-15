<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.update_app.title') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.update_app.description') }}
        </p>
        <label class="input-label">Current version</label><br>
        <label class="version">1.0.0</label>
        <base-button :outline="true" size="large" color="theme" @click="checkUpdate">
          <font-awesome-icon :class="{'update': isUpdateAvail}" style="margin-right: 5px;" icon="sync-alt" />
          {{ $t('settings.update_app.check_update') }}
        </base-button>
        <hr>
        <div class="mt-4 content">
          <h3 class="page-title">{{ $t('settings.update_app.avail_update') }}</h3>
          <label class="input-label">{{ $t('settings.update_app.next_version') }}</label><br>
          <label class="version">{{ updateData.version }}</label>
          <p class="page-sub-title">
            {{ description }}
          </p>
          <base-button size="large" color="theme" @click="onUpdateApp">
            {{ $t('settings.update_app.update') }}
          </base-button>
        </div>
        <!-- <div>
          <h3 class="page-title">{{ $t('settings.update_app.update_progress') }}</h3>
          <p class="page-sub-title">
            {{ $t('settings.update_app.progress_text') }}
          </p>
          <font-awesome-icon icon="spinner" class="fa-spin"/>
        </div> -->
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data () {
    return {
      isShowProgressBar: false,
      isUpdateAvail: false,
      progress: 10,
      interval: null,
      description: '',
      updateData: {
        isMinor: Boolean,
        installed: '',
        version: ''
      }
    }
  },
  computed: {
  },
  watch: {
  },
  mounted () {
  },
  methods: {
    async onUpdateApp () {
      const data = this.updateData
      let response = await axios.post('/api/update', data)
      console.log(response.data)
    },
    async checkUpdate () {
      let response = await axios.get('/api/check/update')
      console.log(response.data)
      if (response.data) {
        this.updateData.isMinor = response.data.is_minor
        this.updateData.version = response.data.version.version
        this.updateData.description = response.data.version.description
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
