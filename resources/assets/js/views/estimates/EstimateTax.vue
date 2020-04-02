<template>
  <div class="section mt-2">
    <label class="estimate-label">
      {{ tax.name }} ({{ tax.percent }}%)
    </label>
    <label class="estimate-amount">
      <div v-html="$utils.formatMoney(tax.amount, currency)" />

      <font-awesome-icon
        class="ml-2"
        icon="trash-alt"
        @click="$emit('remove', index)"
      />
    </label>
  </div>
</template>

<script>
export default {
  props: {
    index: {
      type: Number,
      required: true
    },
    tax: {
      type: Object,
      required: true
    },
    taxes: {
      type: Array,
      required: true
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
  computed: {
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
  methods: {
    updateTax () {
      this.$emit('update', {
        'index': this.index,
        'item': {
          ...this.tax,
          amount: this.taxAmount
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
