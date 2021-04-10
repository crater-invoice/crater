<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.menu_title.custom_fields') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.custom_fields.section_description') }}
        </p>
      </div>

      <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button variant="primary-outline" size="lg" @click="addCustomField">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.custom_fields.add_custom_field') }}
        </sw-button>
      </div>
    </div>

    <sw-table-component
      ref="table"
      variant="gray"
      :show-filter="false"
      :data="fetchData"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.custom_fields.name')"
        show="name"
      />

      <sw-table-column
        :sortable="true"
        :label="$t('settings.custom_fields.label')"
        show="label"
      />

      <sw-table-column
        :sortable="true"
        :label="$t('settings.custom_fields.model')"
        show="model_type"
      />

      <sw-table-column
        :sortable="true"
        :label="$t('settings.custom_fields.type')"
        show="type.label"
      />

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.custom_fields.required')"
        show="is_required"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.custom_fields.required') }}</span>
          <sw-badge
            :bg-color="
              $utils.getBadgeStatusColor(row.is_required ? 'YES' : 'NO').bgColor
            "
            :color="
              $utils.getBadgeStatusColor(row.is_required ? 'YES' : 'NO').color
            "
          >
            {{
              row.is_required
                ? $t('settings.custom_fields.yes')
                : $t('settings.custom_fields.no').replace('_', ' ')
            }}
          </sw-badge>
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
            <dot-icon slot="activator" />

            <sw-dropdown-item @click="editCustomField(row.id)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeCustomField(row.id)">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PencilIcon, TrashIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },

  methods: {
    ...mapActions('customFields', ['fetchCustomFields', 'deleteCustomFields']),

    ...mapActions('modal', ['openModal']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchCustomFields(data)

      return {
        data: response.data.customFields.data,
        pagination: {
          totalPages: response.data.customFields.last_page,
          currentPage: page,
          count: response.data.customFields.count,
        },
      }
    },

    async removeCustomField(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.custom_fields.custom_field_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteCustomFields(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.custom_fields.deleted_message')
            )
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.custom_fields.already_in_use')
          )
        }
      })
    },

    addCustomField() {
      this.openModal({
        title: this.$t('settings.custom_fields.add_custom_field'),
        componentName: 'CustomFieldModal',
        refreshData: this.$refs.table.refresh,
      })
    },

    editCustomField(id) {
      this.openModal({
        title: this.$t('settings.custom_fields.edit_custom_field'),
        componentName: 'CustomFieldModal',
        id: id,
        refreshData: this.$refs.table.refresh,
      })
    },
  },
}
</script>
