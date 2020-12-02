<template>
  <div>
    <base-loader v-if="!getDashboardDataLoaded" />

    <div class="grid grid-cols-1 gap-6 mt-10 xl:grid-cols-2">
      <!-- Due Invoices -->
      <div class="due-invoices">
        <div class="relative z-10 flex items-center justify-between">
          <h6 class="mb-0 text-xl font-semibold leading-normal">
            {{ $t('dashboard.recent_invoices_card.title') }}
          </h6>

          <sw-button
            tag-name="router-link"
            to="/admin/invoices"
            variant="primary-outline"
          >
            {{ $t('dashboard.recent_invoices_card.view_all') }}
          </sw-button>
        </div>

        <sw-table-component
          ref="inv_table"
          :data="getDueInvoices"
          :show-filter="false"
          table-class="table"
        >
          <sw-table-column
            :sortable="true"
            :label="$t('dashboard.recent_invoices_card.due_on')"
            show="formattedDueDate"
          >
            <template slot-scope="row">
              <span>{{ $t('dashboard.recent_invoices_card.due_on') }}</span>
              <span class="mt-6">{{ row.formattedDueDate }}</span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('dashboard.recent_invoices_card.customer')"
            show="user.name"
          >
            <template slot-scope="row">
              <span>{{ $t('dashboard.recent_invoices_card.customer') }}</span>
              <router-link
                :to="{ path: `invoices/${row.id}/view` }"
                class="font-medium text-primary-500"
              >
                {{ row.user.name }}
              </router-link>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('invoices.status')"
            sort-as="status"
          >
            <template slot-scope="row">
              <span> {{ $t('invoices.status') }}</span>

              <sw-badge
                :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                :color="$utils.getBadgeStatusColor(row.status).color"
              >
                {{
                  row.status != 'PARTIALLY_PAID'
                    ? row.status
                    : row.status.replace('_', ' ')
                }}
              </sw-badge>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('dashboard.recent_invoices_card.amount_due')"
            show="due_amount"
            sort-as="due_amount"
          >
            <template slot-scope="row">
              <span>{{ $t('dashboard.recent_invoices_card.amount_due') }}</span>

              <div
                v-html="$utils.formatMoney(row.due_amount, row.user.currency)"
              />
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown dashboard-recent-invoice-options no-click"
          >
            <sw-dropdown slot-scope="row">
              <dot-icon slot="activator" />
              <sw-dropdown-item
                tag-name="router-link"
                :to="`invoices/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`invoices/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.view') }}
              </sw-dropdown-item>

              <!-- <sw-dropdown-item
                v-if="row.status == 'DRAFT'"
                @click="sendInvoice(row.id)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.send_invoice') }}
              </sw-dropdown-item> -->

              <sw-dropdown-item
                v-if="row.status === 'DRAFT'"
                @click="sentInvoice(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.mark_as_sent') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeInvoice(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </sw-table-column>
        </sw-table-component>
      </div>

      <!-- Recent Estimates -->
      <div class="recent-estimates">
        <div class="relative z-10 flex items-center justify-between">
          <h6 class="mb-0 text-xl font-semibold leading-normal">
            {{ $t('dashboard.recent_estimate_card.title') }}
          </h6>

          <sw-button
            tag-name="router-link"
            to="/admin/estimates"
            variant="primary-outline"
          >
            {{ $t('dashboard.recent_estimate_card.view_all') }}
          </sw-button>
        </div>

        <sw-table-component
          ref="est_table"
          :data="getRecentEstimates"
          :show-filter="false"
          table-class="table"
        >
          <sw-table-column
            :sortable="true"
            :label="$t('dashboard.recent_estimate_card.date')"
            show="formattedExpiryDate"
          >
            <template slot-scope="row">
              <span>{{ $t('dashboard.recent_estimate_card.date') }}</span>
              <span class="mt-6">{{ row.formattedExpiryDate }}</span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('dashboard.recent_estimate_card.customer')"
            show="user.name"
          >
            <template slot-scope="row">
              <span>{{ $t('dashboard.recent_estimate_card.customer') }}</span>
              <router-link
                :to="{ path: `estimates/${row.id}/view` }"
                class="font-medium text-primary-500"
              >
                {{ row.user.name }}
              </router-link>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('estimates.status')"
            show="status"
          >
            <template slot-scope="row">
              <span> {{ $t('estimates.status') }}</span>

              <sw-badge
                :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
                :color="$utils.getBadgeStatusColor(row.status).color"
                class="px-3 py-1"
              >
                {{ row.status }}
              </sw-badge>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('dashboard.recent_estimate_card.amount_due')"
            show="total"
            sort-as="total"
          >
            <template slot-scope="row">
              <span>{{ $t('dashboard.recent_estimate_card.amount_due') }}</span>

              <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown no-click"
          >
            <sw-dropdown slot-scope="row">
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`estimates/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`estimates/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="convertInToinvoice(row.id)">
                <document-text-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.convert_to_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="onMarkAsSent(row.id)">
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_sent') }}
              </sw-dropdown-item>
              <!--
              <sw-dropdown-item
                v-if="row.status !== 'SENT'"
                @click="sendEstimate(row.id)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.send_estimate') }}
              </sw-dropdown-item> -->

              <sw-dropdown-item
                v-if="row.status !== 'ACCEPTED'"
                @click="onMarkAsAccepted(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_accepted') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status !== 'REJECTED'"
                @click="onMarkAsRejected(row.id)"
              >
                <x-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_rejected') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeEstimate(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </sw-table-column>
        </sw-table-component>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import {
  PencilIcon,
  EyeIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  DocumentTextIcon,
  XCircleIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    PencilIcon,
    EyeIcon,
    PaperAirplaneIcon,
    CheckCircleIcon,
    TrashIcon,
    DocumentTextIcon,
    XCircleIcon,
  },

  data() {
    return {
      ...this.$store.state.dashboard,
      currency: {
        precision: 2,
        thousand_separator: ',',
        decimal_separator: '.',
        symbol: '$',
      },
      isLoaded: false,
      fetching: false,
    }
  },

  computed: {
    ...mapGetters('user', {
      user: 'currentUser',
    }),

    ...mapGetters('dashboard', [
      'getDashboardDataLoaded',
      'getDueInvoices',
      'getRecentEstimates',
    ]),
  },

  methods: {
    ...mapActions('dashboard', ['loadData']),

    ...mapActions('invoice', ['deleteInvoice', 'sendEmail', 'markAsSent']),

    ...mapActions('estimate', [
      'deleteEstimate',
      'markAsAccepted',
      'markAsRejected',
      'convertToInvoice',
    ]),

    ...mapActions('estimate', {
      sendEstimateEmail: 'sendEmail',
      markEstimateAsSent: 'markAsSent',
    }),

    async loadData(params) {
      await this.$store.dispatch('dashboard/loadData', params)
      this.isLoaded = true
    },

    async removeEstimate(id) {
      this.id = id
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('estimates.confirm_delete', 1),
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willDelete) => {
          if (willDelete) {
            let res = await this.deleteEstimate({ ids: [this.id] })
            if (res.data.success) {
              window.toastr['success'](this.$tc('estimates.deleted_message', 1))
              this.refreshEstTable()
            } else if (res.data.error) {
              window.toastr['error'](res.data.message)
            }
          }
        })
    },

    refreshInvTable() {
      this.$refs.inv_table.refresh()
    },

    refreshEstTable() {
      this.$refs.est_table.refresh()
    },

    async convertInToinvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_conversion'),
          icon: '/assets/icon/file-alt-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willDelete) => {
          if (willDelete) {
            let res = await this.convertToInvoice(id)
            this.selectAllField = false

            if (res.data) {
              window.toastr['success'](this.$t('estimates.conversion_message'))
              this.$router.push(`invoices/${res.data.invoice.id}/edit`)
            } else if (res.data.error) {
              window.toastr['error'](res.data.message)
            }
          }
        })
    },

    async onMarkAsSent(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_sent'),
          icon: '/assets/icon/check-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willMarkAsSent) => {
          if (willMarkAsSent) {
            const data = {
              id: id,
              status: 'SENT',
            }

            let response = await this.markEstimateAsSent(data)
            this.refreshEstTable()

            if (response.data) {
              window.toastr['success'](
                this.$tc('estimates.mark_as_sent_successfully')
              )
            }
          }
        })
    },

    async removeInvoice(id) {
      this.id = id
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc('invoices.confirm_delete'),
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willDelete) => {
          if (willDelete) {
            let res = await this.deleteInvoice({ ids: [this.id] })
            if (res.data.success) {
              window.toastr['success'](this.$tc('invoices.deleted_message'))
              this.refreshInvTable()
            } else if (res.data.error) {
              window.toastr['error'](res.data.message)
            }
          }
        })
    },

    async sendInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.confirm_send'),
          icon: '/assets/icon/paper-plane-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willSendInvoice) => {
          if (willSendInvoice) {
            const data = {
              id: id,
            }
            let response = await this.sendEmail(data)
            this.refreshInvTable()

            if (response.data.success) {
              window.toastr['success'](
                this.$tc('invoices.send_invoice_successfully')
              )
              return true
            }

            if (response.data.error === 'user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('invoices.user_email_does_not_exist')
              )
              return false
            }
            window.toastr['error'](this.$tc('invoices.something_went_wrong'))
          }
        })
    },

    async sentInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.invoice_mark_as_sent'),
          icon: '/assets/icon/check-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willMarkAsSend) => {
          if (willMarkAsSend) {
            const data = {
              id: id,
              status: 'SENT',
            }
            let response = await this.markAsSent(data)

            this.refreshInvTable()
            if (response.data) {
              window.toastr['success'](
                this.$tc('invoices.mark_as_sent_successfully')
              )
            }
          }
        })
    },

    async onMarkAsAccepted(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_accepted'),
          icon: '/assets/icon/check-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (markedAsRejected) => {
          if (markedAsRejected) {
            const data = {
              id: id,
            }
            let response = await this.markAsAccepted(data)
            this.refreshEstTable()

            if (response.data) {
              this.refreshEstTable()
              window.toastr['success'](
                this.$tc('estimates.marked_as_accepted_message')
              )
            }
          }
        })
    },

    async onMarkAsRejected(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_mark_as_rejected'),
          icon: '/assets/icon/times-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (markedAsRejected) => {
          if (markedAsRejected) {
            const data = {
              id: id,
            }
            let response = await this.markAsRejected(data)
            this.refreshEstTable()

            if (response.data) {
              this.refreshEstTable()
              window.toastr['success'](
                this.$tc('estimates.marked_as_rejected_message')
              )
            }
          }
        })
    },

    async sendEstimate(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('estimates.confirm_send_estimate'),
          icon: '/assets/icon/paper-plane-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (willSendEstimate) => {
          if (willSendEstimate) {
            const data = {
              id: id,
            }
            let response = await this.sendEstimateEmail(data)
            this.refreshEstTable()

            if (response.data.success) {
              window.toastr['success'](
                this.$tc('estimates.send_estimate_successfully')
              )
              return true
            }

            if (response.data.error === 'user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('estimates.user_email_does_not_exist')
              )
              return true
            }
            window.toastr['error'](this.$tc('estimates.something_went_wrong'))
          }
        })
    },
  },
}
</script>
