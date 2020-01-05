<template>
  <div v-if="payment" class="main-content payment-view-page">
    <div class="page-header">
      <h3 class="page-title"> {{ payment.payment_number }}</h3>
      <div class="page-actions row">
        <base-button
          :loading="isSendingEmail"
          :disabled="isSendingEmail"
          :outline="true"
          color="theme"
          @click="onPaymentSend"
        >
          {{ $t('payments.send_payment_receipt') }}
        </base-button>
        <v-dropdown :close-on-select="false" align="left" class="filter-container">
          <a slot="activator" href="#">
            <base-button color="theme">
              <font-awesome-icon icon="ellipsis-h" />
            </base-button>
          </a>
          <v-dropdown-item>
            <router-link :to="{path: `/admin/payments/${$route.params.id}/edit`}" class="dropdown-item">
              <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon"/>
              {{ $t('general.edit') }}
            </router-link>
            <div class="dropdown-item" @click="removePayment($route.params.id)">
              <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
              {{ $t('general.delete') }}
            </div>
          </v-dropdown-item>
        </v-dropdown>
      </div>
    </div>
    <div class="payment-sidebar">
      <div class="side-header">
        <base-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          input-class="inv-search"
          icon="search"
          type="text"
          align-icon="right"
          @input="onSearch"
        />
        <div
          class="btn-group ml-3"
          role="group"
          aria-label="First group"
        >
          <v-dropdown :close-on-select="false" align="left" class="filter-container">
            <a slot="activator" href="#">
              <base-button class="inv-button inv-filter-fields-btn" color="default" size="medium">
                <font-awesome-icon icon="filter" />
              </base-button>
            </a>
            <div class="filter-title">
              {{ $t('general.sort_by') }}
            </div>
            <div class="filter-items">
              <input
                id="filter_invoice_number"
                v-model="searchData.orderByField"
                type="radio"
                name="filter"
                class="inv-radio"
                value="invoice_number"
                @change="onSearch"
              >
              <label class="inv-label" for="filter_invoice_number">{{ $t('invoices.title') }}</label>
            </div>
            <div class="filter-items">
              <input
                id="filter_payment_date"
                v-model="searchData.orderByField"
                type="radio"
                name="filter"
                class="inv-radio"
                value="payment_date"
                @change="onSearch"
              >
              <label class="inv-label" for="filter_payment_date">{{ $t('payments.date') }}</label>
            </div>
            <div class="filter-items">
              <input
                id="filter_payment_number"
                v-model="searchData.orderByField"
                type="radio"
                name="filter"
                class="inv-radio"
                value="payment_number"
                @change="onSearch"
              >
              <label class="inv-label" for="filter_payment_number">{{ $t('payments.payment_number') }}</label>
            </div>
          </v-dropdown>
          <base-button v-tooltip.top-center="{ content: getOrderName }" class="inv-button inv-filter-sorting-btn" color="default" size="medium" @click="sortData">
            <font-awesome-icon v-if="getOrderBy" icon="sort-amount-up" />
            <font-awesome-icon v-else icon="sort-amount-down" />
          </base-button>
        </div>
      </div>
      <base-loader v-if="isSearching" />
      <div v-else class="side-content">
        <router-link
          v-for="(payment,index) in payments"
          :to="`/admin/payments/${payment.id}/view`"
          :key="index"
          class="side-payment"
        >
          <div class="left">
            <div class="inv-name">{{ payment.user.name }}</div>
            <div class="inv-number">{{ payment.payment_number }}</div>
            <div class="inv-number">{{ payment.invoice_number }}</div>
          </div>
          <div class="right">
            <div class="inv-amount" v-html="$utils.formatMoney(payment.amount, payment.user.currency)" />
            <div class="inv-date">{{ payment.formattedPaymentDate }}</div>
            <!-- <div class="inv-number">{{ payment.payment_method.name }}</div> -->
          </div>
        </router-link>
        <p v-if="!payments.length" class="no-result">
          {{ $t('payments.no_matching_invoices') }}
        </p>
      </div>
    </div>
    <div class="payment-view-page-container" >
      <iframe :src="`${shareableLink}`" class="frame-style"/>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
const _ = require('lodash')
export default {
  data () {
    return {
      id: null,
      count: null,
      payments: [],
      payment: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null
      },
      isRequestOnGoing: false,
      isSearching: false,
      isSendingEmail: false,
      isMarkingAsSent: false
    }
  },
  computed: {
    getOrderBy () {
      if (this.searchData.orderBy === 'asc' || this.searchData.orderBy == null) {
        return true
      }
      return false
    },
    getOrderName () {
      if (this.getOrderBy) {
        return this.$t('general.ascending')
      }
      return this.$t('general.descending')
    },
    shareableLink () {
      return `/payments/pdf/${this.payment.unique_hash}`
    }
  },
  watch: {
    $route (to, from) {
      this.loadPayment()
    }
  },
  created () {
    this.loadPayments()
    this.loadPayment()
    this.onSearch = _.debounce(this.onSearch, 500)
  },
  methods: {
    // ...mapActions('invoice', [
    //   'fetchInvoices',
    //   'getRecord',
    //   'searchInvoice',
    //   'markAsSent',
    //   'sendEmail',
    //   'deleteInvoice',
    //   'fetchViewInvoice'
    // ]),
    ...mapActions('payment', [
      'fetchPayments',
      'fetchPayment',
      'sendEmail',
      'deletePayment',
      'searchPayment'
    ]),
    async loadPayments () {
      let response = await this.fetchPayments()
      if (response.data) {
        this.payments = response.data.payments.data
      }
    },
    async loadPayment () {
      let response = await this.fetchPayment(this.$route.params.id)

      if (response.data) {
        this.payment = response.data.payment
      }
    },
    async onSearch () {
      let data = ''
      if (this.searchData.searchText !== '' && this.searchData.searchText !== null && this.searchData.searchText !== undefined) {
        data += `search=${this.searchData.searchText}&`
      }

      if (this.searchData.orderBy !== null && this.searchData.orderBy !== undefined) {
        data += `orderBy=${this.searchData.orderBy}&`
      }

      if (this.searchData.orderByField !== null && this.searchData.orderByField !== undefined) {
        data += `orderByField=${this.searchData.orderByField}`
      }
      this.isSearching = true
      let response = await this.searchPayment(data)
      this.isSearching = false
      if (response.data) {
        this.payments = response.data.payments.data
      }
    },
    sortData () {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearch()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearch()
      return true
    },
    async onPaymentSend () {
      window.swal({
        title: this.$tc('general.are_you_sure'),
        text: this.$tc('payments.confirm_send_payment'),
        icon: '/assets/icon/paper-plane-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          this.isSendingEmail = true
          let response = await this.sendEmail({id: this.payment.id})
          this.isSendingEmail = false
          if (response.data.success) {
            window.toastr['success'](this.$tc('payments.send_payment_successfully'))
            return true
          }
          if (response.data.error === 'user_email_does_not_exist') {
            window.toastr['error'](this.$tc('payments.user_email_does_not_exist'))
            return false
          }
          window.toastr['error'](this.$tc('payments.something_went_wrong'))
        }
      })
    },
    async removePayment (id) {
      this.id = id
      window.swal({
        title: 'Deleted',
        text: 'you will not be able to recover this payment!',
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let request = await this.deletePayment(this.id)
          if (request.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 1))
            this.$router.push('/admin/payments')
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    }
  }
}
</script>
