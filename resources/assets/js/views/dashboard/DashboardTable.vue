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
                  $utils.getStatusTranslation(
                    row.status != 'PARTIALLY_PAID'
                      ? row.status
                      : row.status.replace('_', ' ')
                  )
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
                :to="`invoices/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`invoices/${row.id}/view`"
                tag-name="router-link"
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
                :to="`estimates/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`estimates/${row.id}/view`"
                tag-name="router-link"
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

    ...mapActions('notification', ['showNotification']),

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
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.deleteEstimate({ ids: [this.id] })
          if (res.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.deleted_message', 1),
            })
            this.refreshEstTable()
          } else if (res.data.error) {
            this.showNotification({
              type: 'error',
              message: res.data.message,
            })
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
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            viewBox="0 0 384 512"
            class="w-6 h-6"
            data-prefix="fas"
            data-icon="file-alt"
            class="svg-inline--fa fa-file-alt fa-w-12"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill="#55547A"
              d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.convertToInvoice(id)
          this.selectAllField = false

          if (res.data) {
            this.showNotification({
              type: 'success',
              message: this.$t('estimates.conversion_message'),
            })
            this.$router.push(`invoices/${res.data.invoice.id}/edit`)
          } else if (res.data.error) {
            this.showNotification({
              type: 'error',
              message: res.data.message,
            })
          }
        }
      })
    },

    async onMarkAsSent(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
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
          const data = {
            id: id,
            status: 'SENT',
          }

          let response = await this.markEstimateAsSent(data)
          this.refreshEstTable()

          if (response.data) {
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.mark_as_sent_successfully'),
            })
          }
        }
      })
    },

    async removeInvoice(id) {
      this.id = id
      window
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.deleteInvoice({ ids: [this.id] })
          if (res.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.deleted_message'),
            })
            this.refreshInvTable()
          } else if (res.data.error) {
            this.showNotification({
              type: 'error',
              message: res.data.message,
            })
          }
        }
      })
    },

    async sendInvoice(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_send'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            focusable="false"
            class="w-6 h-6"
            data-prefix="fas"
            data-icon="paper-plane"
            class="svg-inline--fa fa-paper-plane fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#55547A"
              d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          const data = {
            id: id,
          }
          let response = await this.sendEmail(data)
          this.refreshInvTable()

          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.send_invoice_successfully'),
            })
            return true
          }

          if (response.data.error === 'user_email_does_not_exist') {
            this.showNotification({
              type: 'error',
              message: this.$tc('invoices.user_email_does_not_exist'),
            })
            return false
          }
          this.showNotification({
            type: 'error',
            message: this.$tc('invoices.something_went_wrong'),
          })
        }
      })
    },

    async sentInvoice(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
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
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSent(data)

          this.refreshInvTable()
          if (response.data) {
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.mark_as_sent_successfully'),
            })
          }
        }
      })
    },

    async onMarkAsAccepted(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
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
          const data = {
            id: id,
          }
          let response = await this.markAsAccepted(data)
          this.refreshEstTable()

          if (response.data) {
            this.refreshEstTable()
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.marked_as_accepted_message'),
            })
          }
        }
      })
    },

    async onMarkAsRejected(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
        icon: 'error',
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
              fill="#DC2626"
              d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          const data = {
            id: id,
          }
          let response = await this.markAsRejected(data)
          this.refreshEstTable()

          if (response.data) {
            this.refreshEstTable()
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.marked_as_rejected_message'),
            })
          }
        }
      })
    },

    async sendEstimate(id) {
      window
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_send_estimate'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            focusable="false"
            class="w-6 h-6"
            data-prefix="fas"
            data-icon="paper-plane"
            class="svg-inline--fa fa-paper-plane fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#55547A"
              d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          const data = {
            id: id,
          }
          let response = await this.sendEstimateEmail(data)
          this.refreshEstTable()

          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.send_estimate_successfully'),
            })
            return true
          }

          if (response.data.error === 'user_email_does_not_exist') {
            this.showNotification({
              type: 'error',
              message: this.$tc('estimates.user_email_does_not_exist'),
            })
            return true
          }
          this.showNotification({
            type: 'error',
            message: this.$tc('estimates.something_went_wrong'),
          })
        }
      })
    },
  },
}
</script>
