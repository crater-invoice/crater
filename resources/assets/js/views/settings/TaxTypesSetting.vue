<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.tax_types.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.tax_types.description') }}
        </p>
      </div>
      <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button size="lg" variant="primary-outline" @click="openTaxModal">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.tax_types.add_new_tax') }}
        </sw-button>
      </div>
    </div>

    <sw-table-component
      ref="table"
      :show-filter="false"
      :data="fetchData"
      table-class="table"
      variant="gray"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.tax_types.tax_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.tax_name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_types.compound_tax')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.compound_tax') }}</span>
          <sw-badge
            :bg-color="
              $utils.getBadgeStatusColor(row.compound_tax ? 'YES' : 'NO')
                .bgColor
            "
            :color="
              $utils.getBadgeStatusColor(row.compound_tax ? 'YES' : 'NO').color
            "
          >
            {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
          </sw-badge>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_types.percent')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.percent') }}</span>
          {{ row.percent }} %
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

            <sw-dropdown-item @click="editTax(row.id)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeTax(row.id)">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>

    <sw-divider class="my-8" />

    <div class="flex mt-2">
      <div class="relative w-12">
        <sw-switch
          v-model="formData.tax_per_item"
          class="absolute"
          style="top: -20px"
          @change="setTax"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black box-title">
          {{ $t('settings.tax_types.tax_per_item') }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-4 text-gray-500"
          style="max-width: 480px"
        >
          {{ $t('settings.tax_types.tax_setting_description') }}
        </p>
      </div>
    </div>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  data() {
    return {
      formData: {
        tax_per_item: false,
      },
      isRequestOnGoing: false,
    }
  },

  computed: {
    ...mapGetters('taxType', ['taxTypes', 'getTaxTypeById']),
  },

  mounted() {
    this.getTaxSetting()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType',
      'fetchTaxTypes',
    ]),
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),

    ...mapActions('notification', ['showNotification']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchTaxTypes(data)

      return {
        data: response.data.taxTypes.data,
        pagination: {
          totalPages: response.data.taxTypes.last_page,
          currentPage: page,
          count: response.data.taxTypes.count,
        },
      }
    },

    async getTaxSetting() {
      let response = await this.fetchCompanySettings(['tax_per_item'])
      if (response.data) {
        this.formData.tax_per_item = response.data.tax_per_item === 'YES'
      }
    },

    async setTax(val) {
      let data = {
        settings: {
          tax_per_item: this.formData.tax_per_item ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        this.showNotification({
          type: 'success',
          message: this.$t('general.setting_updated'),
        })
      }
    },

    async removeTax(id, index) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.tax_types.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let response = await this.deleteTaxType(id)
          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$t('settings.tax_types.deleted_message'),
            })
            this.$refs.table.refresh()
            return true
          }
          this.showNotification({
            type: 'error',
            message: this.$t('settings.tax_types.already_in_use'),
          })
        }
      })
    },

    openTaxModal() {
      this.openModal({
        title: this.$t('settings.tax_types.add_tax'),
        componentName: 'TaxTypeModal',
        refreshData: this.$refs.table.refresh,
      })
    },

    async editTax(id) {
      let response = await this.fetchTaxType(id)
      this.openModal({
        title: this.$t('settings.tax_types.edit_tax'),
        componentName: 'TaxTypeModal',
        id: id,
        data: response.data.taxType,
        refreshData: this.$refs.table.refresh,
      })
    },
  },
}
</script>
