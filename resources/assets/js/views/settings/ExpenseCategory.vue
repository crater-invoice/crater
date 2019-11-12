<template>

  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header d-flex justify-content-between">
        <div>
          <h3 class="page-title">{{ $t('settings.expense_category.title') }}</h3>
          <p class="page-sub-title">
            {{ $t('settings.expense_category.description') }}
          </p>
        </div>
        <base-button
          outline
          class="add-new-tax"
          color="theme"
          @click="openCategoryModal"
        >
          {{ $t('settings.expense_category.add_new_category') }}
        </base-button>
      </div>

      <table-component
        ref="table"
        :show-filter="false"
        :data="categories"
        table-class="table expense-category"
      >
        <table-column
          :label="$t('settings.expense_category.category_name')"
          show="name"
        />
        <table-column
          :sortable="true"
          :filterable="true"
          :label="$t('settings.expense_category.category_description')"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.expense_category.category_description') }}</span>
            <div class="notes">
              <div class="note">{{ row.description }}</div>
            </div>
          </template>
        </table-column>
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.expense_category.action') }}</span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>
                <div class="dropdown-item" @click="EditCategory(row.id)">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                  {{ $t('general.edit') }}
                </div>
              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeExpenseCategory(row.id)">
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

</template>
<script>

import { mapActions, mapGetters } from 'vuex'

export default {
  data () {
    return {
      id: null
    }
  },
  computed: {
    ...mapGetters('category', [
      'categories',
      'getCategoryById'
    ])
  },
  mounted () {
    this.fetchCategories()
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('category', [
      'fetchCategories',
      'fetchCategory',
      'deleteCategory'
    ]),
    async removeExpenseCategory (id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.expense_category.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let response = await this.deleteCategory(id)
          if (response.data.success) {
            window.toastr['success'](this.$tc('settings.expense_category.deleted_message'))
            this.id = null
            this.$refs.table.refresh()
            return true
          } window.toastr['error'](this.$t('settings.expense_category.already_in_use'))
        }
      })
    },
    openCategoryModal () {
      this.openModal({
        'title': 'Add Category',
        'componentName': 'CategoryModal'
      })
      this.$refs.table.refresh()
    },
    async EditCategory (id) {
      let response = await this.fetchCategory(id)
      this.openModal({
        'title': 'Edit Category',
        'componentName': 'CategoryModal',
        'id': id,
        'data': response.data.category
      })
      this.$refs.table.refresh()
    }
  }
}
</script>
