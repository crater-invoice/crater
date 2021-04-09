<template>
  <div>
    <div class="flex flex-wrap justify-end mt-8 lg:flex-nowrap">
      <sw-button size="lg" variant="primary-outline" @click="addItemUnit">
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('settings.customization.items.add_item_unit') }}
      </sw-button>
    </div>

    <sw-table-component
      ref="table"
      :data="fetchData"
      :show-filter="false"
      variant="gray"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.customization.items.unit_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.items.unit_name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.action') }}</span>
          <sw-dropdown>
            <dot-icon slot="activator" class="h-5 mr-3 text-primary-800" />

            <sw-dropdown-item @click="editItemUnit(row)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeItemUnit(row.id)">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')

export default {
  components: {
    TrashIcon,
    PlusIcon,
    PencilIcon,
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('item', ['deleteItemUnit', 'fetchItemUnits']),

    ...mapActions('notification', ['showNotification']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchItemUnits(data)

      return {
        data: response.data.units.data,
        pagination: {
          totalPages: response.data.units.last_page,
          currentPage: page,
          count: response.data.units.count,
        },
      }
    },

    async addItemUnit() {
      this.openModal({
        title: this.$t('settings.customization.items.add_item_unit'),
        componentName: 'ItemUnit',
        refreshData: this.$refs.table.refresh,
      })
    },

    async editItemUnit(data) {
      this.openModal({
        title: this.$t('settings.customization.items.edit_item_unit'),
        componentName: 'ItemUnit',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    async removeItemUnit(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.items.item_unit_confirm_delete'),
        icon: 'question',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let response = await this.deleteItemUnit(id)

          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$t('settings.customization.items.deleted_message'),
            })
            this.$refs.table.refresh()
            return true
          }
          this.showNotification({
            type: 'error',
            message: this.$t('settings.customization.items.already_in_use'),
          })
        }
      })
    },
  },
}
</script>
