<template>
  <div v-if="estimate" class="main-content estimate-view-page">
    <div class="page-header">
      <h3 class="page-title"> {{ estimate.estimate_number }}</h3>
      <div class="page-actions row">
        <div class="col-xs-2 mr-3">
          <base-button
            v-if="estimate.status === 'DRAFT'"
            :loading="isMarkAsSent"
            :disabled="isMarkAsSent"
            :outline="true"
            color="theme"
            @click="onMarkAsSent"
          >
            {{ $t('estimates.mark_as_sent') }}
          </base-button>
        </div>
        <div class="col-xs-2">
          <base-button
            v-if="estimate.status === 'DRAFT'"
            :loading="isSendingEmail"
            :disabled="isSendingEmail"
            :outline="true"
            color="theme"
            @click="onSendEstimate"
          >
            {{ $t('estimates.send_estimate') }}
          </base-button>
        </div>
        <v-dropdown :close-on-select="false" align="left" class="filter-container">
          <a slot="activator" href="#">
            <base-button color="theme">
              <font-awesome-icon icon="ellipsis-h" />
            </base-button>
          </a>
          <v-dropdown-item>
            <router-link :to="{path: `/admin/estimates/${$route.params.id}/edit`}" class="dropdown-item">
              <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon"/>
              {{ $t('general.edit') }}
            </router-link>
            <div class="dropdown-item" @click="removeEstimate($route.params.id)">
              <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
              {{ $t('general.delete') }}
            </div>
          </v-dropdown-item>
        </v-dropdown>
      </div>
    </div>
    <div class="estimate-sidebar">
      <base-loader v-if="isSearching" />
      <div v-else class="side-header">
        <base-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          input-class="inv-search"
          icon="search"
          type="text"
          align-icon="right"
          @input="onSearched()"
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
                id="filter_estimate_date"
                v-model="searchData.orderByField"
                type="radio"
                name="filter"
                class="inv-radio"
                value="estimate_date"
                @change="onSearched"
              >
              <label class="inv-label" for="filter_estimate_date">{{ $t('reports.estimates.estimate_date') }}</label>
            </div>
            <div class="filter-items">
              <input
                id="filter_due_date"
                v-model="searchData.orderByField"
                type="radio"
                name="filter"
                class="inv-radio"
                value="expiry_date"
                @change="onSearched"
              >
              <label class="inv-label" for="filter_due_date">{{ $t('estimates.due_date') }}</label>
            </div>
            <div class="filter-items">
              <input
                id="filter_estimate_number"
                v-model="searchData.orderByField"
                type="radio"
                name="filter"
                class="inv-radio"
                value="estimate_number"
                @change="onSearched"
              >
              <label class="inv-label" for="filter_estimate_number">{{ $t('estimates.estimate_number') }}</label>
            </div>
          </v-dropdown>
          <base-button v-tooltip.top-center="{ content: getOrderName }" class="inv-button inv-filter-sorting-btn" color="default" size="medium" @click="sortData">
            <font-awesome-icon v-if="getOrderBy" icon="sort-amount-up" />
            <font-awesome-icon v-else icon="sort-amount-down" />
          </base-button>
        </div>
      </div>
      <div class="side-content">
        <router-link
          v-for="(estimate,index) in estimates"
          :to="`/admin/estimates/${estimate.id}/view`"
          :key="index"
          class="side-estimate"
        >
          <div class="left">
            <div class="inv-name">{{ estimate.user.name }}</div>
            <div class="inv-number">{{ estimate.estimate_number }}</div>
            <div :class="'est-status-'+estimate.status.toLowerCase()"class="inv-status">{{ estimate.status }}</div>
          </div>
          <div class="right">
            <div class="inv-amount" v-html="$utils.formatMoney(estimate.total, estimate.user.currency)" />
            <div class="inv-date">{{ estimate.formattedEstimateDate }}</div>
          </div>
        </router-link>
        <p v-if="!estimates.length" class="no-result">
          {{ $t('estimates.no_matching_estimates') }}
        </p>
      </div>
    </div>
    <div class="estimate-view-page-container">
      <iframe :src="`${shareableLink}`" class="frame-style"/>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
const _ = require('lodash')

export default {
  data () {
    return {
      id: null,
      count: null,
      estimates: [],
      estimate: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null
      },
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      isMarkAsSent: false,
      isSendingEmail: false,
      isRequestOnGoing: false,
      isSearching: false
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
      return `/estimates/pdf/${this.estimate.unique_hash}`
    }
  },
  watch: {
    $route (to, from) {
      this.loadEstimate()
    }
  },
  created () {
    this.loadEstimates()
    this.loadEstimate()
    this.onSearched = _.debounce(this.onSearched, 500)
  },
  methods: {
    ...mapActions('estimate', [
      'fetchEstimates',
      'getRecord',
      'searchEstimate',
      'markAsSent',
      'sendEmail',
      'deleteEstimate',
      'selectEstimate',
      'fetchViewEstimate'
    ]),
    async loadEstimates () {
      let response = await this.fetchEstimates()
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
    },
    async loadEstimate () {
      let response = await this.fetchViewEstimate(this.$route.params.id)

      if (response.data) {
        this.estimate = response.data.estimate
      }
    },
    async onSearched () {
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
      let response = await this.searchEstimate(data)
      this.isSearching = false
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
    },
    sortData () {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearched()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearched()
      return true
    },
    async onMarkAsSent () {
      window.swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          this.isMarkAsSent = true
          let response = await this.markAsSent({id: this.estimate.id})
          this.isMarkAsSent = false
          if (response.data) {
            window.toastr['success'](this.$tc('estimates.mark_as_sent_successfully'))
          }
        }
      })
    },
    async onSendEstimate (id) {
      window.swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_send_estimate'),
        icon: '/assets/icon/paper-plane-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          this.isSendingEmail = true
          let response = await this.sendEmail({id: this.estimate.id})
          this.isSendingEmail = false
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
    },
    async removeEstimate (id) {
      window.swal({
        title: 'Deleted',
        text: 'you will not be able to recover this estimate!',
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let request = await this.deleteEstimate(id)
          if (request.data.success) {
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
            this.$router.push('/admin/estimates')
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    }
  }
}
</script>
