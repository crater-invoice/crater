<template>
  <div class="tax-select">
    <div class="flex flex-col w-full p-4">
      <div class="relative flex w-full mb-2">
        <sw-input
          v-model="textSearch"
          :placeholder="$t('general.search')"
          focus
          class="text-black"
          icon="search"
          type="text"
        />
      </div>
      <div
        v-if="filteredTaxType.length > 0"
        class="relative flex flex-col overflow-auto sw-scroll list"
        style="max-height: 112px"
      >
        <div
          v-for="(taxType, index) in filteredTaxType"
          :key="index"
          :class="{
            'bg-gray-100 cursor-not-allowed opacity-50 pointer-events-none': taxes.find(
              (val) => {
                return val.tax_type_id === taxType.id
              }
            ),
          }"
          class="flex justify-between p-4 border-b border-gray-200 border-solid cursor-pointer list-item last:border-b-0 hover:bg-gray-100"
          @click="selectTaxType(index)"
        >
          <label
            class="inline-block m-0 text-base font-normal leading-tight text-black font-base"
          >
            {{ taxType.name }}
          </label>
          <label
            class="inline-block m-0 text-base font-normal leading-tight text-black font-base"
          >
            {{ taxType.percent }} %
          </label>
        </div>
      </div>
      <div v-else class="flex justify-center p-5 text-gray-400">
        <label class="m-0">{{ $t('general.no_tax_found') }}</label>
      </div>
    </div>

    <button
      type="button"
      class="flex items-center justify-center w-full px-2 py-3 bg-gray-200 border-none outline-none"
      @click="openTaxModal"
    >
      <check-circle-icon class="h-5" />
      <label
        class="m-0 ml-3 text-sm leading-none cursor-pointer font-base text-primary-400"
      >
        {{ $t('invoices.add_new_tax') }}
      </label>
    </button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    CheckCircleIcon,
  },
  props: {
    taxes: {
      type: Array,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      textSearch: null,
    }
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes']),
    filteredTaxType() {
      if (this.textSearch) {
        var textSearch = this.textSearch
        return this.taxTypes.filter(function (el) {
          return el.name.toLowerCase().indexOf(textSearch.toLowerCase()) !== -1
        })
      } else {
        return this.taxTypes
      }
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    selectTaxType(index) {
      this.$emit('select', { ...this.taxTypes[index] })
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
