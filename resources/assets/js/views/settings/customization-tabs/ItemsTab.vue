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
      variant="gray"
      :data="fetchData"
      :show-filter="false"
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
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.items.item_unit_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteItemUnit(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.items.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.items.already_in_use')
          )
        }
      })
    },
  },
}
</script>
