<template>
  <div id="app" class="main-content dashboard">
    <div class="row">
      <div class="dash-item col-sm-6">
        <router-link slot="item-title" to="/admin/invoices">
          <div class="dashbox">
            <div class="desc">
              <span
                v-if="isLoaded"
                class="amount"
              >
                <div v-html="$utils.formatMoney(getTotalDueAmount, defaultCurrency)"/>
              </span>
              <span class="title">
                {{ $t('dashboard.cards.due_amount') }}
              </span>
            </div>
            <div class="icon">
              <dollar-icon class="card-icon" />
            </div>
          </div>
        </router-link>
      </div>
      <div class="dash-item col-sm-6">
        <router-link slot="item-title" to="/admin/customers">
          <div class="dashbox">
            <div class="desc">
              <span v-if="isLoaded"
                    class="amount" >
                {{ getContacts }}
              </span>
              <span class="title">
                {{ $t('dashboard.cards.customers') }}
              </span>
            </div>
            <div class="icon">
              <contact-icon class="card-icon" />
            </div>
          </div>
        </router-link>
      </div>
      <div class="dash-item col-sm-6">
        <router-link slot="item-title" to="/admin/invoices">
          <div class="dashbox">
            <div class="desc">
              <span v-if="isLoaded"
                    class="amount">
                {{ getInvoices }}
              </span>
              <span class="title">
                {{ $t('dashboard.cards.invoices') }}
              </span>
            </div>
            <div class="icon">
              <invoice-icon class="card-icon" />
            </div>
          </div>
        </router-link>
      </div>
      <div class="dash-item col-sm-6">
        <router-link slot="item-title" to="/admin/estimates">
          <div class="dashbox">
            <div class="desc">
              <span v-if="isLoaded"
                    class="amount">
                {{ getEstimates }}
              </span>
              <span class="title">
                {{ $t('dashboard.cards.estimates') }}
              </span>
            </div>
            <div class="icon">
              <estimate-icon class="card-icon" />
            </div>
          </div>
        </router-link>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 mt-2">
        <div class="card dashboard-card">
          <div class="graph-body">
            <div class="card-body col-md-12 col-lg-12 col-xl-10">
              <div class="card-header">
                <h6><i class="fa fa-line-chart text-primary"/>{{ $t('dashboard.monthly_chart.title') }} </h6>
                <div class="year-selector">
                  <base-select
                    v-model="selectedYear"
                    :options="years"
                    :allow-empty="false"
                    :show-labels="false"
                    :placeholder="$t('dashboard.select_year')"
                  />
                </div>
              </div>
              <line-chart
                v-if="isLoaded"
                :format-money="$utils.formatMoney"
                :format-graph-money="$utils.formatGraphMoney"
                :invoices="getChartInvoices"
                :expenses="getChartExpenses"
                :receipts="getReceiptTotals"
                :income="getNetProfits"
                :labels="getChartMonths"
                class=""
              />
            </div>
            <div class="chart-desc col-md-12 col-lg-12 col-xl-2">
              <div class="stats">
                <div class="description">
                  <span class="title"> {{ $t('dashboard.chart_info.total_sales') }} </span>
                  <br>
                  <span v-if="isLoaded" class="total">
                    <div v-html="$utils.formatMoney(getTotalSales, defaultCurrency)"/>
                  </span>
                </div>
                <div class="description">
                  <span class="title"> {{ $t('dashboard.chart_info.total_receipts') }} </span>
                  <br>
                  <span v-if="isLoaded" class="total" style="color:#00C99C;">
                    <div v-html="$utils.formatMoney(getTotalReceipts, defaultCurrency)"/>
                  </span>
                </div>
                <div class="description">
                  <span class="title"> {{ $t('dashboard.chart_info.total_expense') }} </span>
                  <br>
                  <span v-if="isLoaded" class="total" style="color:#FB7178;">
                    <div v-html="$utils.formatMoney(getTotalExpenses, defaultCurrency)"/>
                  </span>
                </div>
                <div class="description">
                  <span class="title"> {{ $t('dashboard.chart_info.net_income') }} </span>
                  <br>
                  <span class="total" style="color:#5851D8;">
                    <div v-html="$utils.formatMoney(getNetProfit, defaultCurrency)"/>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <base-loader v-if="!getLoadedData"/>
    <div class="row table-row">
      <div class="col-lg-12 col-xl-6 mt-2">
        <div class="table-header">
          <h6 class="table-title">
            {{ $t('dashboard.recent_invoices_card.title') }}
          </h6>
          <router-link to="/admin/invoices">
            <base-button
              :outline="true"
              color="theme"
              class="btn-sm"
            >
              {{ $t('dashboard.recent_invoices_card.view_all') }}
            </base-button>
          </router-link>
        </div>
        <div class="dashboard-table">
          <table-component
            ref="inv_table"
            :data="getDueInvoices"
            :show-filter="false"
            table-class="table"
            class="dashboard"
          >
            <table-column :label="$t('dashboard.recent_invoices_card.due_on')" show="formattedDueDate" />
            <table-column :label="$t('dashboard.recent_invoices_card.customer')" show="user.name" />
            <table-column
              :label="$t('invoices.status')"
              sort-as="status"
            >
              <template slot-scope="row" >
                <span> {{ $t('invoices.status') }}</span>
                <span :class="'inv-status-'+row.status.toLowerCase()">{{ (row.status != 'PARTIALLY_PAID')? row.status : row.status.replace('_', ' ') }}</span>
              </template>
            </table-column>
            <table-column :label="$t('dashboard.recent_invoices_card.amount_due')" show="due_amount" sort-as="due_amount">
              <template slot-scope="row">
                <span>{{ $t('dashboard.recent_invoices_card.amount_due') }}</span>
                <div v-html="$utils.formatMoney(row.due_amount, row.user.currency)"/>
              </template>
            </table-column>
            <table-column
              :sortable="false"
              :filterable="false"
              cell-class="action-dropdown dashboard-recent-invoice-options no-click"
            >
              <template slot-scope="row">
                <v-dropdown>
                  <a slot="activator" href="#/">
                    <dot-icon />
                  </a>
                  <v-dropdown-item>
                    <router-link :to="{path: `invoices/${row.id}/edit`}" class="dropdown-item">
                      <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon"/>
                      {{ $t('general.edit') }}
                    </router-link>
                    <router-link :to="{path: `invoices/${row.id}/view`}" class="dropdown-item">
                      <font-awesome-icon icon="eye" class="dropdown-item-icon" />
                      {{ $t('invoices.view') }}
                    </router-link>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status == 'DRAFT'">
                    <a class="dropdown-item" href="#/" @click="sendInvoice(row.id)" >
                      <font-awesome-icon icon="envelope" class="dropdown-item-icon" />
                      {{ $t('invoices.send_invoice') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status === 'DRAFT'">
                    <a class="dropdown-item" href="#/" @click="sentInvoice(row.id)">
                      <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                      {{ $t('invoices.mark_as_sent') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item>
                    <div class="dropdown-item" @click="removeInvoice(row.id)">
                      <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                      {{ $t('general.delete') }}
                    </div>
                  </v-dropdown-item>
                </v-dropdown>
              </template>
            </table-column>
          </table-component>
        </div>
      </div>
      <div class="col-lg-12 col-xl-6 mt-2 mob-table">
        <div class="table-header">
          <h6 class="table-title">
            {{ $t('dashboard.recent_estimate_card.title') }}
          </h6>
          <router-link to="/admin/estimates">
            <base-button
              :outline="true"
              color="theme"
              class="btn-sm"
            >
              {{ $t('dashboard.recent_estimate_card.view_all') }}
            </base-button>
          </router-link>
        </div>
        <div class="dashboard-table">
          <table-component
            ref="est_table"
            :data="getRecentEstimates"
            :show-filter="false"
            table-class="table"
          >
            <table-column :label="$t('dashboard.recent_estimate_card.date')" show="formattedExpiryDate" />
            <table-column :label="$t('dashboard.recent_estimate_card.customer')" show="user.name" />
            <table-column
              :label="$t('estimates.status')"
              show="status" >
              <template slot-scope="row" >
                <span> {{ $t('estimates.status') }}</span>
                <span :class="'est-status-'+row.status.toLowerCase()">{{ row.status }}</span>
              </template>
            </table-column>
            <table-column :label="$t('dashboard.recent_estimate_card.amount_due')" show="total" sort-as="total">
              <template slot-scope="row">
                <span>{{ $t('dashboard.recent_estimate_card.amount_due') }}</span>
                <div v-html="$utils.formatMoney(row.total, row.user.currency)"/>
              </template>
            </table-column>
            <table-column
              :sortable="false"
              :filterable="false"
              cell-class="action-dropdown no-click"
            >
              <template slot-scope="row">
                <v-dropdown>
                  <a slot="activator" href="#/">
                    <dot-icon />
                  </a>
                  <v-dropdown-item>
                    <router-link :to="{path: `estimates/${row.id}/edit`}" class="dropdown-item">
                      <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                      {{ $t('general.edit') }}
                    </router-link>
                  </v-dropdown-item>
                  <v-dropdown-item>
                    <div class="dropdown-item" @click="removeEstimate(row.id)">
                      <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                      {{ $t('general.delete') }}
                    </div>
                  </v-dropdown-item>
                  <v-dropdown-item>
                    <router-link :to="{path: `estimates/${row.id}/view`}" class="dropdown-item">
                      <font-awesome-icon icon="eye" class="dropdown-item-icon" />
                      {{ $t('general.view') }}
                    </router-link>
                  </v-dropdown-item>
                  <v-dropdown-item>
                    <a class="dropdown-item" href="#/" @click="convertInToinvoice(row.id)">
                      <font-awesome-icon icon="file-alt" class="dropdown-item-icon" />
                      {{ $t('estimates.convert_to_invoice') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status === 'DRAFT'">
                    <a class="dropdown-item" href="#/" @click.self="onMarkAsSent(row.id)">
                      <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                      {{ $t('estimates.mark_as_sent') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status !== 'SENT'">
                    <a class="dropdown-item" href="#/" @click.self="sendEstimate(row.id)">
                      <font-awesome-icon icon="paper-plane" class="dropdown-item-icon" />
                      {{ $t('estimates.send_estimate') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status !== 'ACCEPTED'">
                    <a class="dropdown-item" href="#/" @click.self="onMarkAsAccepted(row.id)">
                      <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                      {{ $t('estimates.mark_as_accepted') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status !== 'REJECTED'">
                    <a class="dropdown-item" href="#/" @click.self="onMarkAsRejected(row.id)">
                      <font-awesome-icon icon="times-circle" class="dropdown-item-icon" />
                      {{ $t('estimates.mark_as_rejected') }}
                    </a>
                  </v-dropdown-item>
                </v-dropdown>
              </template>
            </table-column>
          </table-component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import LineChart from '../../components/chartjs/LineChart'
import DollarIcon from '../../components/icon/DollarIcon'
import ContactIcon from '../../components/icon/ContactIcon'
import InvoiceIcon from '../../components/icon/InvoiceIcon'
import EstimateIcon from '../../components/icon/EstimateIcon'

export default {
  components: {
    LineChart,
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon
  },
  data () {
    return {
      incomeTotal: null,
      ...this.$store.state.dashboard,
      currency: { precision: 2, thousand_separator: ',', decimal_separator: '.', symbol: '$' },
      isLoaded: false,
      fetching: false,
      years: ['This year', 'Previous year'],
      selectedYear: 'This year'
    }
  },
  computed: {
    ...mapGetters('user', {
      'user': 'currentUser'
    }),
    ...mapGetters('dashboard', [
      'getChartMonths',
      'getChartInvoices',
      'getChartExpenses',
      'getNetProfits',
      'getReceiptTotals',
      'getContacts',
      'getInvoices',
      'getEstimates',
      'getTotalDueAmount',
      'getTotalSales',
      'getTotalReceipts',
      'getTotalExpenses',
      'getNetProfit',
      'getLoadedData',
      'getDueInvoices',
      'getRecentEstimates'
    ]),
    ...mapGetters('currency', [
      'defaultCurrency'
    ])
  },
  watch: {
    selectedYear (val) {
      if (val === 'Previous year') {
        let params = {previous_year: true}
        this.loadData(params)
      } else {
        this.loadData()
      }
    }
  },
  created () {
    this.loadChart()
    this.loadData()
  },
  methods: {
    ...mapActions('dashboard', [
      'getChart',
      'loadData'
    ]),
    ...mapActions('invoice', [
      'deleteInvoice',
      'sendEmail',
      'markAsSent'
    ]),
    ...mapActions('estimate', [
      'deleteEstimate',
      'markAsAccepted',
      'markAsRejected',
      'convertToInvoice'
    ]),
    ...mapActions('estimate', {
      'sendEstimateEmail': 'sendEmail',
      'markEstimateAsSent': 'markAsSent'
    }),

    async loadChart () {
      await this.$store.dispatch('dashboard/getChart')
      this.isLoaded = true
    },

    async loadData (params) {
      await this.$store.dispatch('dashboard/loadData', params)
      this.isLoaded = true
    },

    async removeEstimate (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteEstimate(this.id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
            this.refreshEstTable()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    refreshInvTable () {
      this.$refs.inv_table.refresh()
    },

    refreshEstTable () {
      this.$refs.est_table.refresh()
    },

    async convertInToinvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
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

    async onMarkAsSent (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willMarkAsSent) => {
        if (willMarkAsSent) {
          const data = {
            id: id
          }
          let response = await this.markEstimateAsSent(data)
          this.refreshEstTable()
          if (response.data) {
            window.toastr['success'](this.$tc('estimates.mark_as_sent_successfully'))
          }
        }
      })
    },

    async removeInvoice (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteInvoice(this.id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.refreshInvTable()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async sendInvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_send'),
        icon: '/assets/icon/paper-plane-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willSendInvoice) => {
        if (willSendInvoice) {
          const data = {
            id: id
          }
          let response = await this.sendEmail(data)
          this.refreshInvTable()
          if (response.data.success) {
            window.toastr['success'](this.$tc('invoices.send_invoice_successfully'))
            return true
          }
          if (response.data.error === 'user_email_does_not_exist') {
            window.toastr['error'](this.$tc('invoices.user_email_does_not_exist'))
            return false
          }
          window.toastr['error'](this.$tc('invoices.something_went_wrong'))
        }
      })
    },

    async sentInvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willMarkAsSend) => {
        if (willMarkAsSend) {
          const data = {
            id: id
          }
          let response = await this.markAsSent(data)
          this.refreshInvTable()
          if (response.data) {
            window.toastr['success'](this.$tc('invoices.mark_as_sent_successfully'))
          }
        }
      })
    },

    async onMarkAsAccepted (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id
          }
          let response = await this.markAsAccepted(data)
          this.refreshEstTable()
          if (response.data) {
            this.refreshEstTable()
            window.toastr['success'](this.$tc('estimates.marked_as_accepted_message'))
          }
        }
      })
    },

    async onMarkAsRejected (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
        icon: '/assets/icon/times-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id
          }
          let response = await this.markAsRejected(data)
          this.refreshEstTable()
          if (response.data) {
            this.refreshEstTable()
            window.toastr['success'](this.$tc('estimates.marked_as_rejected_message'))
          }
        }
      })
    },

    async sendEstimate (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_send_estimate'),
        icon: '/assets/icon/paper-plane-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willSendEstimate) => {
        if (willSendEstimate) {
          const data = {
            id: id
          }
          let response = await this.sendEstimateEmail(data)
          this.refreshEstTable()
          if (response.data.success) {
            window.toastr['success'](this.$tc('estimates.send_estimate_successfully'))
            return true
          }
          if (response.data.error === 'user_email_does_not_exist') {
            window.toastr['error'](this.$tc('estimates.user_email_does_not_exist'))
            return true
          }
          window.toastr['error'](this.$tc('estimates.something_went_wrong'))
        }
      })
    }

  }
}
</script>
