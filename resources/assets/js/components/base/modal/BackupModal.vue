<template>
  <div class="relative customer-modal">
    <base-loader
      v-if="isRequestOngoing"
      :show-bg-overlay="true"
      class="h-130"
    />
    <form @submit.prevent="createNewBackup">
      <div class="p-6">
        <sw-input-group
          :label="$t('settings.backup.select_backup_type')"
          :error="optionError"
          horizontal
          required
          class="py-2"
        >
          <sw-select
            v-model="formData.option"
            :options="options"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.backup.select_backup_type')"
            :allow-empty="false"
            :max-height="100"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.disk.select_disk')"
          :error="selectDiskError"
          horizontal
          required
          class="py-2"
        >
          <sw-select
            v-model="formData.selected_disk"
            :options="getDisks"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.disk.select_disk')"
            :allow-empty="false"
            :preselect-first="true"
            :custom-label="getCustomLabel"
            :max-height="100"
            :loading="isLoading"
            track-by="id"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="cancelBackup"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isCreateLoading"
          :disabled="isCreateLoading"
          variant="primary"
          type="submit"
        >
          <save-icon v-if="!isCreateLoading" class="mr-2" />
          {{ $t('general.create') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import AddressStub from '../../../stub/address'
import _ from 'lodash'

const { required } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      isCreateLoading: false,
      isRequestOngoing: false,
      formData: {
        option: 'full',
        selected_disk: { driver: 'local' },
      },
      options: ['full', 'only-db', 'only-files'],
    }
  },

  validations: {
    formData: {
      option: {
        required,
      },
      selected_disk: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters('disks', ['getDisks']),

    ...mapGetters('modal', ['refreshData']),

    optionError() {
      if (!this.$v.formData.option.$error) {
        return ''
      }

      if (!this.$v.formData.option) {
        return this.$tc('validation.required')
      }
    },

    selectDiskError() {
      if (!this.$v.formData.selected_disk.$error) {
        return ''
      }

      if (!this.$v.formData.selected_disk) {
        return this.$tc('validation.required')
      }
    },
  },

  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('backup', ['createBackup']),

    ...mapActions('disks', ['fetchDisks']),

    ...mapActions('modal', ['closeModal']),

    ...mapActions('notification', ['showNotification']),

    getCustomLabel({ driver, name }) {
      return `${name} — [${driver}]`
    },

    async createNewBackup() {
      let data = {
        option: this.formData.option,
        file_disk_id: this.formData.selected_disk.id,
      }

      try {
        this.isCreateLoading = true
        await this.createBackup(data)
        this.isCreateLoading = false
        this.showNotification({
          type: 'success',
          message: this.$t('settings.backup.created_message'),
        })
        this.refreshData ? this.refreshData() : ''
        this.cancelBackup()
      } catch (e) {
        this.isCreateLoading = false
        this.showNotification({
          type: 'error',
          message: e.response.data.message,
        })
      }
    },

    async loadData() {
      this.isRequestOngoing = true

      let res = await this.fetchDisks({ limit: 'all' })
      this.formData.selected_disk = res.data.disks.data[0]
      this.isRequestOngoing = false
    },

    cancelBackup() {
      this.closeModal()
    },
  },
}
</script>
