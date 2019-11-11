<template>
  <div id="app" class="main-content">
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
            ref="table"
            :data="getDueInvoices"
            :show-filter="false"
            table-class="table"
            class="dashboard"
          >
            <table-column :label="$t('dashboard.recent_invoices_card.due_on')" show="formattedDueDate" />
            <table-column :label="$t('dashboard.recent_invoices_card.customer')" show="user.name" />
            <table-column :label="$t('dashboard.recent_invoices_card.amount_due')" sort-as="due_amount">
              <template slot-scope="row">
                <span>{{ $t('dashboard.recent_invoices_card.amount_due') }}</span>
                <div v-html="$utils.formatMoney(row.due_amount, row.user.currency)"/>
              </template>
            </table-column>
            <table-column
              :sortable="false"
              :filterable="false"
              cell-class="action-dropdown no-click"
            >
              <template slot-scope="row">
                <v-dropdown>
                  <a slot="activator" href="#">
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
                  <v-dropdown-item>
                    <a class="dropdown-item" href="#" @click="sendInvoice(row.id)" >
                      <font-awesome-icon icon="envelope" class="dropdown-item-icon" />
                      {{ $t('invoices.send_invoice') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status === 'DRAFT'">
                    <a class="dropdown-item" href="#" @click="sentInvoice(row.id)">
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
            ref="table"
            :data="getRecentEstimates"
            :show-filter="false"
            table-class="table"
          >
            <table-column :label="$t('dashboard.recent_estimate_card.date')" show="formattedExpiryDate" />
            <table-column :label="$t('dashboard.recent_estimate_card.customer')" show="user.name" />
            <table-column :label="$t('dashboard.recent_estimate_card.amount_due')" sort-as="total">
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
                  <a slot="activator" href="#">
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
                    <a class="dropdown-item" href="#" @click="convertInToinvoice(row.id)">
                      <font-awesome-icon icon="envelope" class="dropdown-item-icon" />
                      {{ $t('estimates.convert_to_invoice') }}
                    </a>
                  </v-dropdown-item>
                  <v-dropdown-item v-if="row.status === 'DRAFT'">
                    <a class="dropdown-item" href="#" @click.self="onMarkAsSent(row.id)">
                      <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                      {{ $t('estimates.mark_as_sent') }}
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
    ...mapActions('estimate', [
      'deleteEstimate',
      'convertToInvoice'
    ]),
    ...mapActions('invoice', [
      'deleteInvoice',
      'sendEmail',
      'markAsSent'
    ]),

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
        icon: 'error',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteEstimate(this.id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
            this.$refs.table.refresh()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async convertInToinvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: 'error',
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
      const data = {
        id: id
      }
      let response = await this.markAsSent(data)
      this.$refs.table.refresh()
      if (response.data) {
        window.toastr['success'](this.$tc('estimates.mark_as_sent'))
      }
    },

    async removeInvoice (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: 'error',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteInvoice(this.id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.$refs.table.refresh()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async sendInvoice (id) {
      const data = {
        id: id
      }
      let response = await this.sendEmail(data)
      this.$refs.table.refresh()
      if (response.data) {
        window.toastr['success'](this.$tc('invoices.send_invoice'))
      }
    },
    async sentInvoice (id) {
      const data = {
        id: id
      }
      let response = await this.markAsSent(data)
      this.$refs.table.refresh()
      if (response.data) {
        window.toastr['success'](this.$tc('invoices.mark_as_sent'))
      }
    }

  }
}
</script>
