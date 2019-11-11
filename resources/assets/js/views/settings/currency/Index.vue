<template>
  <div class="main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $tc('navigation.currency', 2) }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link
            slot="item-title"
            to="/admin/dashboard">
            {{ $t('navigation.home') }}
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link
            slot="item-title"
            to="#">
            {{ $tc('navigation.currency', 2) }}
          </router-link>
        </li>
      </ol>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <div class="card">
          <div class="card-header">
            <div class="caption">
              <h6>{{ $t('settings.currencies.select_currency') }}:</h6>
            </div>
          </div>
          <div class="card-body">
            <div class="form-group">
              <select
                v-model.trim="currencyId"
                class="form-control"
                @change="selectCurrency()"
              >
                <option
                  v-for="(currency, index) in currencies"
                  :key="index"
                  :value="currency.id"
                >
                  {{ currency.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <div class="caption">
              <h6>{{ $t('settings.currencies.currencies_list') }}</h6>
            </div>
            <div class="actions">
              <router-link slot="item-title" to="currencies/create">
                <base-button icon="plus" color="theme" size="small">
                  {{ $t('navigation.add') }} {{ $t('navigation.new') }}
                </base-button>
              </router-link>
            </div>
          </div>
          <div class="card-body">
            <table-component
              ref="table"
              :data="currencies"
              table-class="table"
              sort-by="name"
              sort-order="asc"
            >
              <table-column :label="$t('settings.currencies.name')" show="name" />
              <table-column :label="$t('settings.currencies.code')" show="code" />
              <table-column :label="$t('settings.currencies.symbol')" show="symbol" />
              <table-column :label="$t('settings.currencies.precision')" show="precision" />
              <table-column :label="$t('settings.currencies.thousand_separator')" show="thousand_separator" />
              <table-column :label="$t('settings.currencies.decimal_separator')" show="decimal_separator" />
              <table-column
                :sortable="false"
                :filterable="false"
                :label="$t('settings.currencies.position')"
              >
                <template slot-scope="row">
                  <span v-if="row.swap_currency_symbol === 0">{{ $t('settings.currencies.right') }}</span>
                  <span v-if="row.swap_currency_symbol === 1">{{ $t('settings.currencies.left') }}</span>
                </template>
              </table-column>
              <table-column
                :sortable="false"
                :filterable="false"
                :label="$t('settings.currencies.action')"
              >
                <template slot-scope="row">
                  <div class="table__actions">
                    <router-link slot="item-title" :to="{path: `currencies/${row.id}/edit`}">{{ $t('navigation.edit') }}</router-link>
                    <div class="table__item--cursor-pointer" @click="removeItems(row.id)">{{ $t('navigation.delete') }}</div>
                  </div>
                </template>
              </table-column>
            </table-component>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions } from 'vuex'

export default {
  data () {
    return this.$store.state.currency
  },
  mounted () {
    this.indexLoadData()
  },
  methods: {
    ...mapActions('currency', [
      'indexLoadData',
      'removeItems',
      'selectCurrency'
    ])
  }
}
</script>
