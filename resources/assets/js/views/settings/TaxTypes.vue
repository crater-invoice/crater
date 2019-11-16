<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header d-flex justify-content-between">
        <div>
          <h3 class="page-title">
            {{ $t('settings.tax_types.title') }}
          </h3>
          <p class="page-sub-title">
            {{ $t('settings.tax_types.description') }}
          </p>
        </div>
        <base-button
          outline
          class="add-new-tax"
          color="theme"
          @click="openTaxModal"
        >
          {{ $t('settings.tax_types.add_new_tax') }}
        </base-button>
      </div>

      <table-component
        ref="table"
        :show-filter="false"
        :data="taxTypes"
        table-class="table tax-table"
        class="mb-3"
      >
        <table-column
          :sortable="true"
          :label="$t('settings.tax_types.tax_name')"
          show="name"
        />
        <table-column
          :sortable="true"
          :filterable="true"
          :label="$t('settings.tax_types.compound_tax')"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.tax_types.compound_tax') }}</span>
            <div class="compound-tax">
              {{ row.compound_tax ? 'Yes' : 'No' }}
            </div>
          </template>
        </table-column>
        <table-column
          :sortable="true"
          :filterable="true"
          :label="$t('settings.tax_types.percent')"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.tax_types.percent') }}</span>
            {{ row.percent }} %
          </template>
        </table-column>
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.tax_types.action') }}</span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>
                <div class="dropdown-item" @click="EditTax(row.id)">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                  {{ $t('general.edit') }}
                </div>
              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeTax(row.id)">
                  <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                  {{ $t('general.delete') }}
                </div>
              </v-dropdown-item>
            </v-dropdown>
          </template>
        </table-column>
      </table-component>
      <hr>
      <div class="page-header mt-3">
        <h3 class="page-title">
          {{ $t('settings.tax_types.tax_settings') }}
        </h3>
        <div class="flex-box">
          <div class="left">
            <base-switch
              v-model="formData.tax_per_item"
              class="btn-switch"
              @change="setTax"
            />
          </div>
          <div class="right ml-15">
            <p class="box-title">  {{ $t('settings.tax_types.tax_per_item') }} </p>
            <p class="box-desc">  {{ $t('settings.tax_types.tax_setting_description') }} </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data () {
    return {
      id: null,
      formData: {
        tax_per_item: false
      }
    }
  },
  computed: {
    ...mapGetters('taxType', [
      'taxTypes',
      'getTaxTypeById'
    ])
  },
  mounted () {
    this.getTaxSetting()
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType'
    ]),
    async getTaxSetting (val) {
      let response = await axios.get('/api/settings/get-setting?key=tax_per_item')

      if (response.data && response.data.tax_per_item === 'YES') {
        this.formData.tax_per_item = true
      } else {
        this.formData.tax_per_item = false
      }
    },
    async setTax (val) {
      let data = {
        key: 'tax_per_item',
        value: this.formData.tax_per_item ? 'YES' : 'NO'
      }
      let response = await axios.put('/api/settings/update-setting', data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    async removeTax (id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.tax_types.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteTaxType(id)
          if (response.data.success) {
            window.toastr['success'](this.$t('settings.tax_types.deleted_message'))
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](this.$t('settings.tax_types.already_in_use'))
        }
      })
    },
    openTaxModal () {
      this.openModal({
        'title': this.$t('settings.tax_types.add_tax'),
        'componentName': 'TaxTypeModal'
      })
      this.$refs.table.refresh()
    },
    async EditTax (id) {
      let response = await this.fetchTaxType(id)
      this.openModal({
        'title': 'Edit Tax',
        'componentName': 'TaxTypeModal',
        'id': id,
        'data': response.data.taxType
      })
      this.$refs.table.refresh()
    }
  }
}
</script>
