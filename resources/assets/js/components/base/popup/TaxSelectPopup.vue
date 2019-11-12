<template>
  <div class="tax-select">
    <div class="main-section">
      <div class="search-bar">
        <base-input
          v-model="textSearch"
          :placeholder="$t('general.search')"
          focus
          icon="search"
          class="search-input"
          type="text"
        />
      </div>
      <div v-if="filteredTaxType.length > 0" class="list" >
        <div
          v-for="(taxType, index) in filteredTaxType"
          :key="index"
          :class="{'item-disabled': taxes.find(val => {return val.tax_type_id === taxType.id})}"
          class="list-item"
          @click="selectTaxType(index)"
        >
          <label>{{ taxType.name }}</label>
          <label>{{ taxType.percent }} %</label>

        </div>
      </div>
      <div v-else class="no-data-label">
        <label>{{ $t('general.no_tax_found') }}</label>
      </div>
    </div>

    <button type="button" class="list-add-button" @click="openTaxModal">
      <font-awesome-icon class="icon" icon="check-circle" />
      <label>{{ $t('invoices.add_new_tax') }}</label>
    </button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    taxes: {
      type: Array,
      required: false,
      default: null
    }
  },
  data () {
    return {
      textSearch: null
    }
  },
  computed: {
    ...mapGetters('taxType', [
      'taxTypes'
    ]),
    filteredTaxType () {
      if (this.textSearch) {
        var textSearch = this.textSearch
        return this.taxTypes.filter(function (el) {
          return el.name.toLowerCase().indexOf(textSearch.toLowerCase()) !== -1
        })
      } else {
        return this.taxTypes
      }
    }
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    selectTaxType (index) {
      this.$emit('select', {...this.taxTypes[index]})
    },
    openTaxModal () {
      this.openModal({
        'title': this.$t('settings.tax_types.add_tax'),
        'componentName': 'TaxTypeModal'
      })
    }
  }
}
</script>
