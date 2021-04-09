<template>
  <div class="file-disk-modal">
    <div v-if="getDiskDrivers.length">
      <component
        :is="selected_disk"
        :loading="isLoading"
        :disks="getDiskDrivers"
        :is-edit="isEdit"
        @on-change-disk="(disk) => (selected_disk = disk.value)"
        @submit="createNewDisk"
      >
        <template v-slot="slotProps">
          <div
            class="z-0 flex justify-end p-4 border-t border-solid border-gray-light"
          >
            <sw-button
              class="mr-3 text-sm"
              variant="primary-outline"
              type="button"
              @click="closeDisk"
            >
              {{ $t('general.cancel') }}
            </sw-button>
            <sw-button
              :loading="isRequestFire(slotProps)"
              :disabled="isRequestFire(slotProps)"
              variant="primary"
              type="submit"
            >
              <save-icon v-if="!isRequestFire(slotProps)" class="mr-2" />
              {{ $t('general.save') }}
            </sw-button>
          </div>
        </template>
      </component>
    </div>
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
      isEdit: false,
      set_as_default: false,
      name: 'local',
      formData: {},
      selected_disk: 'local',
      diskConfigData: {},
    }
  },

  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),

    ...mapGetters('disks', ['getDiskDrivers']),
  },

  created() {
    if (this.modalDataID) {
      this.isEdit = true
    }
    this.loadData()
  },

  methods: {
    ...mapActions('disks', ['fetchDiskDrivers', 'createDisk', 'updateDisk']),

    ...mapActions('modal', ['closeModal']),

    ...mapActions('notification', ['showNotification']),

    isRequestFire(slotProps) {
      return slotProps && (slotProps.diskData.isLoading || this.isLoading)
    },

    async loadData() {
      this.isLoading = true

      let res = await this.fetchDiskDrivers()
      if (this.isEdit) {
        this.selected_disk = this.modalData.driver
      } else {
        this.selected_disk = res.data.drivers[0].value
      }

      this.isLoading = false
    },

    async createNewDisk(data) {
      this.isLoading = true

      let formData = {
        id: this.modalDataID,
        ...data,
      }
      let response
      if (this.isEdit) {
        response = await this.updateDisk(formData)
      } else {
        response = await this.createDisk(formData)
      }

      if (response.data.success) {
        this.refreshData()
        this.closeDisk()
        if (this.isEdit) {
          this.showNotification({
            type: 'success',
            message: this.$t('settings.disk.success_update'),
          })
        } else {
          this.showNotification({
            type: 'success',
            message: this.$t('settings.disk.success_create'),
          })
        }
      } else {
        this.showNotification({
          type: 'error',
          message: this.$t('settings.disk.invalid_disk_credentials'),
        })
      }
      this.isLoading = false
    },

    closeDisk() {
      this.closeModal()
    },
  },
}
</script>
