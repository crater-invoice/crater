<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.expense_category.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.expense_category.description') }}
        </p>
      </div>
      <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button
          variant="primary-outline"
          size="lg"
          @click="addExpenseCategory"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.expense_category.add_new_category') }}
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
        :label="$t('settings.expense_category.category_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.expense_category.category_name') }}}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.expense_category.category_description')"
      >
        <template slot-scope="row">
          <span>{{
            $t('settings.expense_category.category_description')
          }}</span>
          <div class="w-48 overflow-hidden notes">
            <div
              class="overflow-hidden whitespace-nowrap"
              style="text-overflow: ellipsis"
            >
              {{ row.description }}
            </div>
          </div>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.expense_category.action') }}</span>
          <sw-dropdown>
            <dot-icon slot="activator" class="h-5" />

            <sw-dropdown-item @click="editExpenseCategory(row.id)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeExpenseCategory(row.id)">
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
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  computed: {
    ...mapGetters('category', ['categories', 'getCategoryById']),
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('category', [
      'fetchCategories',
      'fetchCategory',
      'deleteCategory',
    ]),

    ...mapActions('notification', ['showNotification']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchCategories(data)
      this.isRequestOngoing = false

      return {
        data: response.data.categories.data,
        pagination: {
          totalPages: response.data.categories.last_page,
          currentPage: page,
          count: response.data.categories.count,
        },
      }
    },

    async removeExpenseCategory(id, index) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.expense_category.confirm_delete'),
        icon: 'question',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>s`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let response = await this.deleteCategory(id)
          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('settings.expense_category.deleted_message'),
            })
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          this.showNotification({
            type: 'error',
            message: this.$t('settings.expense_category.already_in_use'),
          })
        }
      })
    },

    addExpenseCategory() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModal',
        refreshData: this.$refs.table.refresh,
      })
    },

    async editExpenseCategory(id) {
      let response = await this.fetchCategory(id)
      this.openModal({
        title: this.$t('settings.expense_category.edit_category'),
        componentName: 'CategoryModal',
        id: id,
        data: response.data.category,
        refreshData: this.$refs.table.refresh,
      })
    },
  },
}
</script>
