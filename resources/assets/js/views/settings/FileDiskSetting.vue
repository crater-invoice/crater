<template>
  <div class="setting-main-container backup">
    <sw-card variant="setting-card">
      <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
        <div>
          <h6 class="sw-section-title">
            {{ $tc('settings.disk.title', 1) }}
          </h6>
          <p
            class="mt-2 text-sm leading-snug text-gray-500"
            style="max-width: 680px"
          >
            {{ $t('settings.disk.description') }}
          </p>
        </div>
        <div class="mt-4 lg:mt-0 lg:ml-2">
          <sw-button
            variant="primary-outline"
            size="lg"
            @click="openCreateDiskModal"
          >
            <plus-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('settings.disk.new_disk') }}
          </sw-button>
        </div>
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        variant="gray"
        table-class="table tax-table"
        class="mt-0 mb-3"
      >
        <sw-table-column :label="$t('settings.disk.disk_name')" show="name">
          <template slot-scope="row">
            <span>{{ $t('settings.disk.disk_name') }}</span>
            <span class="mt-6">{{ row.name }}</span>
          </template>
        </sw-table-column>
        <sw-table-column
          :label="$t('settings.disk.filesystem_driver')"
          show="driver"
        />
        <sw-table-column :label="$t('settings.disk.disk_type')" show="type" />
        <sw-table-column
          :sortable="false"
          :filterable="false"
          :label="$t('settings.disk.is_default')"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.disk.is_default') }}</span>
            <sw-badge
              :bg-color="
                $utils.getBadgeStatusColor(row.set_as_default ? 'YES' : 'NO')
                  .bgColor
              "
              :color="
                $utils.getBadgeStatusColor(row.set_as_default ? 'YES' : 'NO')
                  .color
              "
            >
              {{ row.set_as_default ? 'Yes' : 'No'.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.disk.action') }}</span>
            <sw-dropdown v-if="isShowAction(row)">
              <a slot="activator" href="#">
                <dot-icon />
              </a>

              <sw-dropdown-item
                v-if="!row.set_as_default"
                @click="setDefaultDiskData(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('settings.disk.set_default_disk') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.type !== 'SYSTEM'"
                @click="openEditDiskModal(row)"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.type !== 'SYSTEM' && !row.set_as_default"
                @click="removeDisk(row.id)"
              >
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>

      <sw-divider class="mt-6 mb-4" />

      <h3 class="mb-5 text-lg font-medium text-black">
        {{ $t('settings.disk.disk_settings') }}
      </h3>

      <div class="flex">
        <div class="relative w-12">
          <sw-switch
            v-model="save_pdf_to_disk"
            class="absolute"
            style="top: -18px"
            @change="setDiskSettings"
          />
        </div>

        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.disk.save_pdf_to_disk') }}
          </p>

          <p class="max-w-lg p-0 m-0 text-xs leading-tight text-gray-500">
            {{ $t('settings.disk.disk_setting_description') }}
          </p>
        </div>
      </div>
    </sw-card>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  CheckCircleIcon,
  PlusIcon,
  TrashIcon,
  PencilIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CheckCircleIcon,
    PlusIcon,
    TrashIcon,
    PencilIcon,
  },

  data() {
    return {
      disk: 'local',
      save_pdf_to_disk: true,
      loading: false,
      disks: [],
    }
  },

  mounted() {
    this.getDiskSetting()
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('disks', ['fetchDisks', 'updateDisk', 'deleteFileDisk']),

    ...mapActions('company', ['updateCompanySettings', 'fetchCompanySettings']),

    ...mapActions('notification', ['showNotification']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchDisks(data)

      return {
        data: response.data.disks.data,
        pagination: {
          totalPages: response.data.disks.last_page,
          currentPage: page,
          count: response.data.disks.count,
        },
      }
    },

    isShowAction(disk) {
      if (!disk.set_as_default) return true

      if (disk.type == 'SYSTEM' && disk.set_as_default) return false

      return true
    },

    openCreateDiskModal() {
      this.openModal({
        title: this.$t('settings.disk.new_disk'),
        componentName: 'FileDiskModal',
        variant: 'lg',
        refreshData: this.refreshTable,
      })
    },

    openEditDiskModal(data) {
      this.openModal({
        title: this.$t('settings.disk.edit_file_disk'),
        componentName: 'FileDiskModal',
        variant: 'lg',
        id: data.id,
        data,
        refreshData: this.refreshTable,
      })
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    async getDiskSetting(val) {
      let response = await this.fetchCompanySettings(['save_pdf_to_disk'])

      if (response.data) {
        this.save_pdf_to_disk =
          response.data.save_pdf_to_disk === 'YES' ? true : false
      }
    },

    async setDiskSettings() {
      let data = {
        settings: {
          save_pdf_to_disk: this.save_pdf_to_disk ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        this.showNotification({
          type: 'success',
          message: this.$t('general.setting_updated'),
        })
      }
      this.$refs.table.refresh()
    },

    async setDefaultDiskData(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.disk.set_default_disk_confirm'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            class="w-6 h-6"
            focusable="false"
            data-prefix="fas"
            data-icon="check-circle"
            class="svg-inline--fa fa-check-circle fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#55547A"
              d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          this.loading = true
          let data = {
            set_as_default: true,
            id,
          }
          let response = await this.updateDisk(data)

          if (response.data.success) {
            this.refreshTable()
            this.showNotification({
              type: 'success',
              message: this.$t('settings.disk.success_set_default_disk'),
            })
          }
        }
      })
    },

    async removeDisk(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.disk.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let response = await this.deleteFileDisk(id)
          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$t('settings.disk.deleted_message'),
            })
            this.refreshTable()
            return true
          }
        }
      })
    },
  },
}
</script>
