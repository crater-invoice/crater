<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.customization.payments.payment_modes') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.customization.payments.description') }}
        </p>
      </div>
      <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button variant="primary-outline" size="lg" @click="addPaymentMode">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.customization.payments.add_payment_mode') }}
        </sw-button>
      </div>
    </div>

    <sw-table-component
      ref="table"
      :show-filter="false"
      :data="fetchData"
      variant="gray"
    >
      <sw-table-column
        :label="$t('settings.customization.payments.mode_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.payments.mode_name') }}</span>
          <span class="mt-6"> {{ row.name }}</span>
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
            <dot-icon slot="activator" class="h-5" />

            <sw-dropdown-item @click="editPaymentMode(row)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removePaymentMode(row.id)">
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
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('payment', ['deletePaymentMode', 'fetchPaymentModes']),

    ...mapActions('notification', ['showNotification']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchPaymentModes(data)

      return {
        data: response.data.paymentMethods.data,
        pagination: {
          totalPages: response.data.paymentMethods.last_page,
          currentPage: page,
          count: response.data.paymentMethods.count,
        },
      }
    },

    addPaymentMode() {
      this.openModal({
        title: this.$t('settings.customization.payments.add_payment_mode'),
        componentName: 'PaymentMode',
        refreshData: this.$refs.table.refresh,
      })
    },

    editPaymentMode(data) {
      this.openModal({
        title: this.$t('settings.customization.payments.edit_payment_mode'),
        componentName: 'PaymentMode',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    removePaymentMode(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(
          'settings.customization.payments.payment_mode_confirm_delete'
        ),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let response = await this.deletePaymentMode(id)

          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$t(
                'settings.customization.payments.deleted_message'
              ),
            })
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          this.showNotification({
            type: 'error',
            message: this.$t('settings.customization.payments.already_in_use'),
          })
        }
      })
    },
  },
}
</script>
