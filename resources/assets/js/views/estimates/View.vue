<template>
  <base-page v-if="estimate" class="xl:pl-96">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <div class="mr-3 text-sm">
          <sw-button
            v-if="estimate.status === 'DRAFT'"
            :disabled="isMarkAsSent"
            variant="primary-outline"
            @click="onMarkAsSent"
          >
            {{ $t('estimates.mark_as_sent') }}
          </sw-button>
        </div>
        <sw-button
          v-if="estimate.status === 'DRAFT'"
          :disabled="isSendingEmail"
          variant="primary"
          class="text-sm"
          @click="onSendEstimate"
        >
          {{ $t('estimates.send_estimate') }}
        </sw-button>
        <sw-dropdown class="ml-3">
          <sw-button slot="activator" variant="primary">
            <dots-horizontal-icon class="h-5" />
          </sw-button>

          <sw-dropdown-item @click="copyPdfUrl">
            <link-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('general.copy_pdf_url') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            :to="`/admin/estimates/${$route.params.id}/edit`"
            tag-name="router-link"
          >
            <pencil-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('general.edit') }}
          </sw-dropdown-item>

          <sw-dropdown-item @click="removeEstimate($route.params.id)">
            <trash-icon class="h-5 mr-3 text-primary-800" />
            {{ $t('general.delete') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </template>
    </sw-page-header>

    <!-- sidebar -->
    <div
      class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-88 xl:block"
    >
      <div
        class="flex items-center justify-between px-4 pt-8 pb-2 border border-gray-200 border-solid height-full"
      >
        <sw-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          class="mb-6"
          type="text"
          variant="gray"
          @input="onSearched()"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>

        <div class="flex mb-6 ml-3" role="group" aria-label="First group">
          <sw-dropdown class="ml-3" position="bottom-start">
            <sw-button slot="activator" size="md" variant="gray-light">
              <filter-icon class="h-5" />
            </sw-button>

            <div
              class="px-2 py-1 pb-2 mb-1 mb-2 text-sm border-b border-gray-200 border-solid"
            >
              {{ $t('general.sort_by') }}
            </div>

            <sw-dropdown-item class="flex px-1 py-2 cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_estimate_date"
                  v-model="searchData.orderByField"
                  :label="$t('reports.estimates.estimate_date')"
                  size="sm"
                  name="filter"
                  value="estimate_date"
                  @change="onSearched"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-2 cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_due_date"
                  v-model="searchData.orderByField"
                  :label="$t('estimates.due_date')"
                  value="expiry_date"
                  size="sm"
                  name="filter"
                  @change="onSearched"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-2 cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_estimate_number"
                  v-model="searchData.orderByField"
                  :label="$t('estimates.estimate_number')"
                  value="estimate_number"
                  size="sm"
                  name="filter"
                  @change="onSearched"
                />
              </sw-input-group>
            </sw-dropdown-item>
          </sw-dropdown>

          <sw-button
            v-tooltip.top-center="{ content: getOrderName }"
            class="ml-1"
            size="md"
            variant="gray-light"
            @click="sortData"
          >
            <sort-ascending-icon v-if="getOrderBy" class="h-5" />
            <sort-descending-icon v-else class="h-5" />
          </sw-button>
        </div>
      </div>

      <base-loader v-if="isSearching" :show-bg-overlay="true" />

      <div
        v-else
        class="h-full pb-32 overflow-y-scroll border-l border-gray-200 border-solid sw-scroll"
      >
        <router-link
          v-for="(estimate, index) in estimates"
          :to="`/admin/estimates/${estimate.id}/view`"
          :id="'estimate-' + estimate.id"
          :key="index"
          :class="[
            'flex justify-between side-estimate p-4 cursor-pointer hover:bg-gray-100 items-center border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid': hasActiveUrl(
                estimate.id
              ),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
              {{ estimate.user.name }}
            </div>

            <div
              class="mt-1 mb-2 text-xs not-italic font-medium leading-5 text-gray-600"
            >
              {{ estimate.estimate_number }}
            </div>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(estimate.status).bgColor"
              :color="$utils.getBadgeStatusColor(estimate.status).color"
              class="px-1 text-xs"
            >
              {{ $utils.getStatusTranslation(estimate.status) }}
            </sw-badge>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="
                $utils.formatMoney(estimate.total, estimate.user.currency)
              "
            />

            <div
              class="text-sm not-italic font-normal leading-5 text-right text-gray-600 est-date"
            >
              {{ estimate.formattedEstimateDate }}
            </div>
          </div>
        </router-link>

        <p
          v-if="!estimates.length"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('estimates.no_matching_estimates') }}
        </p>
      </div>
    </div>

    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden sw-scroll"
      style="height: 75vh"
    >
      <iframe
        :src="`${shareableLink}`"
        class="flex-1 border border-gray-400 border-solid rounded-md frame-style"
      />
    </div>
  </base-page>
</template>

<script>
import { mapActions } from 'vuex'
import {
  DotsHorizontalIcon,
  FilterIcon,
  SortAscendingIcon,
  SortDescendingIcon,
  SearchIcon,
  LinkIcon,
  TrashIcon,
  PencilIcon,
} from '@vue-hero-icons/solid'
const _ = require('lodash')

export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    LinkIcon,
    TrashIcon,
    PencilIcon,
  },
  data() {
    return {
      id: null,
      count: null,
      estimates: [],
      estimate: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      isMarkAsSent: false,
      isSendingEmail: false,
      isRequestOnGoing: false,
      isSearching: false,
    }
  },
  computed: {
    pageTitle() {
      return this.estimate.estimate_number
    },
    getOrderBy() {
      if (
        this.searchData.orderBy === 'asc' ||
        this.searchData.orderBy == null
      ) {
        return true
      }
      return false
    },
    getOrderName() {
      if (this.getOrderBy) {
        return this.$t('general.ascending')
      }
      return this.$t('general.descending')
    },
    shareableLink() {
      return `/estimates/pdf/${this.estimate.unique_hash}`
    },
    getCurrentEstimateId() {
      if (this.estimate && this.estimate.id) {
        return this.estimate.id
      }
      return null
    },
  },
  watch: {
    $route(to, from) {
      this.loadEstimate()
    },
  },
  created() {
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
      'fetchViewEstimate',
    ]),

    ...mapActions('modal', ['openModal']),

    ...mapActions('notification', ['showNotification']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadEstimates() {
      let response = await this.fetchEstimates({ limit: 'all' })
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
      setTimeout(() => {
        this.scrollToEstimate()
      }, 500)
    },
    scrollToEstimate() {
      const el = document.getElementById(`estimate-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadEstimate() {
      let response = await this.fetchViewEstimate(this.$route.params.id)

      if (response.data) {
        this.estimate = { ...response.data.estimate }
      }
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/estimates/pdf/${this.estimate.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)
      this.showNotification({
        type: 'success',
        message: this.$tc('general.copied_pdf_url_clipboard'),
      })
    },
    async onSearched() {
      let data = ''
      if (
        this.searchData.searchText !== '' &&
        this.searchData.searchText !== null &&
        this.searchData.searchText !== undefined
      ) {
        data += `search=${this.searchData.searchText}&`
      }

      if (
        this.searchData.orderBy !== null &&
        this.searchData.orderBy !== undefined
      ) {
        data += `orderBy=${this.searchData.orderBy}&`
      }

      if (
        this.searchData.orderByField !== null &&
        this.searchData.orderByField !== undefined
      ) {
        data += `orderByField=${this.searchData.orderByField}`
      }
      this.isSearching = true
      let response = await this.searchEstimate(data)
      this.isSearching = false
      if (response.data) {
        this.estimates = response.data.estimates.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearched()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearched()
      return true
    },
    async onMarkAsSent() {
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
          this.isMarkAsSent = true
          let response = await this.markAsSent({
            id: this.estimate.id,
            status: 'SENT',
          })
          this.isMarkAsSent = false
          if (response.data) {
            this.estimate.status = 'SENT'
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.mark_as_sent_successfully'),
            })
          }
        }
      })
    },
    async onSendEstimate(id) {
      this.openModal({
        title: this.$t('estimates.send_estimate'),
        componentName: 'SendEstimateModal',
        id: this.estimate.id,
        data: this.estimate,
      })
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/estimates/pdf/${this.estimate.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)
      this.showNotification({
        type: 'success',
        message: this.$tc('general.copied_pdf_url_clipboard'),
      })
    },
    async removeEstimate(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: 'you will not be able to recover this estimate!',
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let request = await this.deleteEstimate({ ids: [id] })
          if (request.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.deleted_message', 1),
            })
            this.$router.push('/admin/estimates')
          } else if (request.data.error) {
            this.showNotification({
              type: 'error',
              message: request.data.message,
            })
          }
        }
      })
    },
  },
}
</script>
