<template>
  <th
    v-if="this.isVisible"
    slot-scope="col"
    :aria-sort="ariaSort"
    :aria-disabled="ariaDisabled"
    :class="headerClass"
    role="columnheader"
    @click="clicked"
  >
    {{ label }}
  </th>
</template>

<script>
import { classList } from '../helpers'
export default {
  props: ['column', 'sort'],

  computed: {
    ariaDisabled () {
      if (!this.column.isSortable()) {
        return 'true'
      }
      return false
    },

    ariaSort () {
      if (!this.column.isSortable ()) {
        return false
      }

      if ((this.column.sortAs || this.column.show) !== this.sort.fieldName) {
        return 'none'
      }

      return this.sort.order === 'asc' ? 'ascending' : 'descending';
    },

    headerClass () {
      if (!this.column.isSortable()) {
        return classList('table-component__th', this.column.headerClass);
      }

      if ((this.column.sortAs || this.column.show) !== this.sort.fieldName) {
        return classList('table-component__th table-component__th--sort', this.column.headerClass);
      }

      return classList(`table-component__th table-component__th--sort-${this.sort.order}`, this.column.headerClass);
    },

    isVisible () {
      return !this.column.hidden
    },

    label () {
      if (this.column.label === null) {
        return this.column.show
      }
      return this.column.label
    }
  },

  methods: {
    clicked () {
      if (this.column.isSortable()) {
        this.$emit('click', this.column)
      }
    }
  }
}
</script>
