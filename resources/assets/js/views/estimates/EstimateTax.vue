<template>
  <div class="flex items-center justify-between w-full mt-2 text-sm">
    <label class="font-semibold leading-5 text-gray-500 uppercase">
      {{ tax.name }} ({{ tax.percent }}%)
    </label>
    <label class="flex items-center justify-center text-lg text-black">
      <div v-html="$utils.formatMoney(tax.amount, currency)" />
      <trash-icon class="h-5 ml-2" @click="$emit('remove', index)" />
    </label>
  </div>
</template>

<script>
import { TrashIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    TrashIcon,
  },
  props: {
    index: {
      type: Number,
      required: true,
    },
    tax: {
      type: Object,
      required: true,
    },
    taxes: {
      type: Array,
      required: true,
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
  computed: {
    taxAmount() {
      if (this.tax.compound_tax && this.total) {
        return Math.round(
          ((this.total + this.totalTax) * this.tax.percent) / 100
        )
      }

      if (this.total && this.tax.percent) {
        return Math.round((this.total * this.tax.percent) / 100)
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
  methods: {
    updateTax() {
      this.$emit('update', {
        index: this.index,
        item: {
          ...this.tax,
          amount: this.taxAmount,
        },
      })
    },
  },
}
</script>
