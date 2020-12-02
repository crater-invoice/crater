<template>
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center text-base" style="flex: 4">
      <label class="pr-2 mb-0" align="right">
        {{ $t('estimates.tax') }}
      </label>
      <sw-select
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
          <button
            type="button"
            class="flex items-center justify-center w-full px-2 py-2 bg-gray-200 border-none outline-none"
            @click="openTaxModal"
          >
            <check-circle-icon class="h-5 text-primary-400" />
            <label class="ml-2 text-sm leading-none text-primary-400">{{
              $t('estimates.add_new_tax')
            }}</label>
          </button>
        </div>
      </sw-select>
      <br />
    </div>
    <div class="text-sm text-right" style="flex: 3">
      <div v-html="$utils.formatMoney(taxAmount, currency)" />
    </div>
    <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
      <trash-icon
        v-if="taxes.length && index !== taxes.length - 1"
        class="h-5 text-gray-700"
        icon="trash-alt"
        @click="removeTax"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon, TrashIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    CheckCircleIcon,
    TrashIcon,
  },
  props: {
    index: {
      type: Number,
      required: true,
    },
    taxData: {
      type: Object,
      required: true,
    },
    taxes: {
      type: Array,
      default: [],
    },
    total: {
      type: Number,
      default: 0,
    },
    totalTax: {
      type: Number,
      default: 0,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
  },
  data() {
    return {
      tax: { ...this.taxData },
      selectedTax: null,
    }
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes']),
    filteredTypes() {
      const clonedTypes = this.taxTypes.map((a) => ({ ...a }))

      return clonedTypes.map((taxType) => {
        let found = this.taxes.find((tax) => tax.tax_type_id === taxType.id)

        if (found) {
          taxType.$isDisabled = true
        } else {
          taxType.$isDisabled = false
        }

        return taxType
      })
    },
    taxAmount() {
      if (this.tax.compound_tax && this.total) {
        return ((this.total + this.totalTax) * this.tax.percent) / 100
      }

      if (this.total && this.tax.percent) {
        return (this.total * this.tax.percent) / 100
      }

      return 0
    },
  },
  watch: {
    total: {
      handler: 'updateTax',
    },
    totalTax: {
      handler: 'updateTax',
    },
  },
  created() {
    if (this.taxData.tax_type_id > 0) {
      this.selectedTax = this.taxTypes.find(
        (_type) => _type.id === this.taxData.tax_type_id
      )
    }

    window.hub.$on('newTax', (val) => {
      if (!this.selectedTax) {
        this.selectedTax = val
        this.onSelectTax(val)
      }
    })

    this.updateTax()
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    customLabel({ name, percent }) {
      return `${name} - ${percent}%`
    },
    onSelectTax(val) {
      this.tax.percent = val.percent
      this.tax.tax_type_id = val.id
      this.tax.compound_tax = val.compound_tax
      this.tax.name = val.name

      this.updateTax()
    },
    updateTax() {
      if (this.tax.tax_type_id === 0) {
        return
      }

      this.$emit('update', {
        index: this.index,
        item: {
          ...this.tax,
          amount: this.taxAmount,
        },
      })
    },
    removeTax() {
      this.$emit('remove', this.index, this.tax)
    },
    openTaxModal() {
      this.openModal({
        title: this.$t('settings.tax_types.add_tax'),
        componentName: 'TaxTypeModal',
      })
    },
  },
}
</script>
