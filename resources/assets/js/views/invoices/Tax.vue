<template>
  <div class="tax-row">
    <div class="d-flex align-items-center tax-select">
      <label class="bd-highlight pr-2 mb-0" align="right">
        {{ $t('general.tax') }}
      </label>
      <base-select
        v-model="selectedTax"
        :options="filteredTypes"
        :allow-empty="false"
        :show-labels="false"
        :custom-label="customLabel"
        :placeholder="$t('general.select_a_tax')"
        track-by="name"
        label="name"
        @select="(val) => onSelectTax(val)"
      >
        <div slot="afterList">
          <button type="button" class="list-add-button" @click="openTaxModal">
            <font-awesome-icon class="icon" icon="check-circle" />
            <label>{{ $t('invoices.add_new_tax') }}</label>
          </button>
        </div>
      </base-select> <br>
    </div>
    <div class="text-right tax-amount" v-html="$utils.formatMoney(taxAmount, currency)" />
    <div class="remove-icon-wrapper">
      <font-awesome-icon
        v-if="taxes.length && index !== taxes.length - 1"
        class="remove-icon"
        icon="trash-alt"
        @click="removeTax"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    index: {
      type: Number,
      required: true
    },
    taxData: {
      type: Object,
      required: true
    },
    taxes: {
      type: Array,
      default: []
    },
    total: {
      type: Number,
      default: 0
    },
    totalTax: {
      type: Number,
      default: 0
    },
    currency: {
      type: [Object, String],
      required: true
    }
  },
  data () {
    return {
      tax: {...this.taxData},
      selectedTax: null
    }
  },
  computed: {
    ...mapGetters('taxType', [
      'taxTypes'
    ]),
    filteredTypes () {
      const clonedTypes = this.taxTypes.map(a => ({...a}))

      return clonedTypes.map((taxType) => {
        let found = this.taxes.find(tax => tax.tax_type_id === taxType.id)

        if (found) {
          taxType.$isDisabled = true
        } else {
          taxType.$isDisabled = false
        }

        return taxType
      })
    },
    taxAmount () {
      if (this.tax.compound_tax && this.total) {
        return ((this.total + this.totalTax) * this.tax.percent) / 100
      }

      if (this.total && this.tax.percent) {
        return (this.total * this.tax.percent) / 100
      }

      return 0
    }
  },
  watch: {
    total: {
      handler: 'updateTax'
    },
    totalTax: {
      handler: 'updateTax'
    }
  },
  created () {
    if (this.taxData.tax_type_id > 0) {
      this.selectedTax = this.taxTypes.find(_type => _type.id === this.taxData.tax_type_id)
    }

    this.updateTax()
    window.hub.$on('newTax', (val) => {
      if (!this.selectedTax) {
        this.selectedTax = val
        this.onSelectTax(val)
      }
    })
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    customLabel ({ name, percent }) {
      return `${name} - ${percent}%`
    },
    onSelectTax (val) {
      this.tax.percent = val.percent
      this.tax.tax_type_id = val.id
      this.tax.compound_tax = val.compound_tax
      this.tax.name = val.name

      this.updateTax()
    },
    updateTax () {
      if (this.tax.tax_type_id === 0) {
        return
      }

      this.$emit('update', {
        'index': this.index,
        'item': {
          ...this.tax,
          amount: this.taxAmount
        }
      })
    },
    removeTax () {
      this.$emit('remove', this.index, this.tax)
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
