<template>
  <div class="table-component">
    <div v-if="showFilter && filterableColumnExists" class="table-component__filter">
      <input
        :class="fullFilterInputClass"
        v-model="filter"
        :placeholder="filterPlaceholder"
        type="text"
      >
      <a v-if="filter" class="table-component__filter__clear" @click="filter = ''">×</a>
    </div>

    <div class="table-component__table-wrapper">
      <base-loader v-if="loading" class="table-loader" />

      <table :class="fullTableClass">
        <caption
          v-if="showCaption"
          class="table-component__table__caption"
          role="alert"
          aria-live="polite"
        >{{ ariaCaption }}</caption>
        <thead :class="fullTableHeadClass">
          <tr>
            <table-column-header
              v-for="column in columns"
              :key="column.show || column.show"
              :sort="sort"
              :column="column"
              @click="changeSorting"
            />
          </tr>
        </thead>
        <tbody :class="fullTableBodyClass">
          <table-row
            v-for="row in displayedRows"
            :key="row.vueTableComponentInternalRowId"
            :row="row"
            :columns="columns"
            @rowClick="emitRowClick"
          />
        </tbody>
        <tfoot>
          <slot :rows="rows" name="tfoot" />
        </tfoot>
      </table>
    </div>

    <div v-if="displayedRows.length === 0 && !loading" class="table-component__message">{{ filterNoResults }}</div>

    <div style="display:none;">
      <slot />
    </div>

    <pagination v-if="pagination && !loading" :pagination="pagination" @pageChange="pageChange" />
  </div>
</template>

<script>
import Column from '../classes/Column'
import expiringStorage from '../expiring-storage'
import Row from '../classes/Row'
import TableColumnHeader from './TableColumnHeader'
import TableRow from './TableRow'
import settings from '../settings'
import Pagination from './Pagination'
import { classList, pick } from '../helpers'

export default {
  components: {
    TableColumnHeader,
    TableRow,
    Pagination
  },

  props: {
    data: { default: () => [], type: [Array, Function] },

    showFilter: { type: Boolean, default: true },
    showCaption: { type: Boolean, default: true },

    sortBy: { default: '', type: String },
    sortOrder: { default: '', type: String },

    cacheKey: { default: null },
    cacheLifetime: { default: 5 },

    tableClass: { default: () => settings.tableClass },
    theadClass: { default: () => settings.theadClass },
    tbodyClass: { default: () => settings.tbodyClass },
    filterInputClass: { default: () => settings.filterInputClass },
    filterPlaceholder: { default: () => settings.filterPlaceholder },
    filterNoResults: { default: () => settings.filterNoResults }
  },

  data: () => ({
    columns: [],
    rows: [],
    filter: '',
    sort: {
      fieldName: '',
      order: ''
    },
    pagination: null,

    loading: false,
    localSettings: {}
  }),

  computed: {
    fullTableClass () {
      return classList('table-component__table', this.tableClass)
    },

    fullTableHeadClass () {
      return classList('table-component__table__head', this.theadClass)
    },

    fullTableBodyClass () {
      return classList('table-component__table__body', this.tbodyClass)
    },

    fullFilterInputClass () {
      return classList('table-component__filter__field', this.filterInputClass)
    },

    ariaCaption () {
      if (this.sort.fieldName === '') {
        return 'Table not sorted'
      }

      return (
        `Table sorted by ${this.sort.fieldName} ` +
        (this.sort.order === 'asc' ? '(ascending)' : '(descending)')
      )
    },

    usesLocalData () {
      return Array.isArray(this.data)
    },

    displayedRows () {
      if (!this.usesLocalData) {
        return this.sortedRows
      }

      if (!this.showFilter) {
        return this.sortedRows
      }

      if (!this.columns.filter(column => column.isFilterable()).length) {
        return this.sortedRows
      }

      return this.sortedRows.filter(row => row.passesFilter(this.filter))
    },

    sortedRows () {
      if (!this.usesLocalData) {
        return this.rows
      }

      if (this.sort.fieldName === '') {
        return this.rows
      }

      if (this.columns.length === 0) {
        return this.rows
      }

      const sortColumn = this.getColumn(this.sort.fieldName)

      if (!sortColumn) {
        return this.rows
      }

      return this.rows.sort(
        sortColumn.getSortPredicate(this.sort.order, this.columns)
      )
    },

    filterableColumnExists () {
      return this.columns.filter(c => c.isFilterable()).length > 0
    },

    storageKey () {
      return this.cacheKey
        ? `vue-table-component.${this.cacheKey}`
        : `vue-table-component.${window.location.host}${window.location.pathname}${this.cacheKey}`
    }
  },

  watch: {
    filter () {
      if (!this.usesLocalData) {
        this.mapDataToRows()
      }

      this.saveState()
    },

    data () {
      if (this.usesLocalData) {
        this.mapDataToRows()
      }
    }
  },

  created () {
    this.sort.order = this.sortOrder

    this.restoreState()
  },

  async mounted () {
    this.sort.fieldName = this.sortBy
    const columnComponents = this.$slots.default
      .filter(column => column.componentInstance)
      .map(column => column.componentInstance)

    this.columns = columnComponents.map(column => new Column(column))

    columnComponents.forEach(columnCom => {
      Object.keys(columnCom.$options.props).forEach(prop =>
        columnCom.$watch(prop, () => {
          this.columns = columnComponents.map(column => new Column(column))
        })
      )
    })

    await this.mapDataToRows()
  },

  methods: {
    async pageChange (page) {
      this.pagination.currentPage = page

      await this.mapDataToRows()
    },

    async mapDataToRows () {
      const data = this.usesLocalData
        ? this.prepareLocalData()
        : await this.fetchServerData()

      let rowId = 0

      this.rows = data
        .map(rowData => {
          rowData.vueTableComponentInternalRowId = rowId++
          return rowData
        })
        .map(rowData => new Row(rowData, this.columns))
    },

    prepareLocalData () {
      this.pagination = null

      return this.data
    },

    async fetchServerData () {
      const page = (this.pagination && this.pagination.currentPage) || 1
      this.loading = true

      const response = await this.data({
        filter: this.filter,
        sort: this.sort,
        page: page
      })

      this.pagination = response.pagination
      this.loading = false
      return response.data
    },

    async refresh () {
      if (this.pagination) {
        this.pagination.currentPage = 1
      }
      await this.mapDataToRows()
    },

    changeSorting (column) {
      if (this.sort.fieldName !== (column.sortAs || column.show)) {
        this.sort.fieldName = (column.sortAs || column.show)
        this.sort.order = 'asc'
      } else {
        this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
      }

      if (!this.usesLocalData) {
        this.mapDataToRows()
      }

      this.saveState()
    },

    getColumn (columnName) {
      return this.columns.find(column => column.show === columnName)
    },

    saveState () {
      expiringStorage.set(
        this.storageKey,
        pick(this.$data, ['filter', 'sort']),
        this.cacheLifetime
      )
    },

    restoreState () {
      const previousState = expiringStorage.get(this.storageKey)

      if (previousState === null) {
        return
      }

      this.sort = previousState.sort
      this.filter = previousState.filter

      this.saveState()
    },

    emitRowClick (row) {
      this.$emit('rowClick', row)
      this.$emit('row-click', row)
    }
  }
}
</script>
