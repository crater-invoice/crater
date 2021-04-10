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
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.expense_category.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let response = await this.deleteCategory(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$tc('settings.expense_category.deleted_message')
            )
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.expense_category.already_in_use')
          )
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
