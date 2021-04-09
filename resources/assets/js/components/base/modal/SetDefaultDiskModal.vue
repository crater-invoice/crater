<template>
  <div class="file-disk-modal">
    <form @submit.prevent="submitData">
      <div class="px-8 py-6">
        <sw-input-group :label="$t('settings.disk.driver')" required>
          <sw-select
            v-model="selected_disk"
            :options="getDisks"
            :searchable="true"
            :allow-empty="false"
            :show-labels="false"
            :custom-label="getCustomLabel"
            class="mt-2"
            track-by="id"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-solid border-gray-light"
      >
        <sw-button
          class="mr-3 text-sm"
          type="button"
          variant="primary-outline"
          @click="closeDisk"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          icon="save"
          type="submit"
          variant="primary"
          class="text-sm"
        >
          {{ $t('general.save') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import Dropbox from './disks/DropboxDisk'
import Local from './disks/LocalDisk'
import S3 from './disks/S3Disk'
import DoSpaces from './disks/DoSpacesDisk'

const {
  required,
  minLength,
  email,
  numeric,
  url,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    Dropbox,
    Local,
    S3,
    DoSpaces,
  },

  data() {
    return {
      isLoading: false,
      set_as_default: false,
      name: 'local',
      formData: null,
      selected_disk: null,
      diskConfigData: {},
    }
  },

  computed: {
    ...mapGetters('modal', ['modalData', 'refreshData']),

    ...mapGetters('disks', ['getDisks']),
  },

  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('disks', ['fetchDisks', 'setDefaultDisk']),

    ...mapActions('modal', ['closeModal']),

    ...mapActions('notification', ['showNotification']),

    async loadData() {
      this.loading = true

      let res = await this.fetchDisks()
      this.selected_disk = res.data.disks.find((v) => v.set_as_default == true)

      this.loading = false
    },

    async submitData() {
      this.isLoading = true

      let response = await this.setDefaultDisk(this.selected_disk)

      if (response.data.success) {
        this.refreshData()
        this.closeDisk()
        this.showNotification({
          type: 'success',
          message: this.$t('settings.disk.success'),
        })
      }
      this.isLoading = true
    },

    closeDisk() {
      this.closeModal()
    },

    getCustomLabel({ driver, name }) {
      return `${name} — [${driver}]`
    },
  },
}
</script>
