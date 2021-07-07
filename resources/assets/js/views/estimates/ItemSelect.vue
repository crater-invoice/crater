<template>
  <div class="flex-1 text-sm">
    <div
      v-if="item.item_id"
      class="relative flex items-center h-10 pl-2 bg-gray-200 border border-gray-200 border-solid rounded"
    >
      {{ item.name }}

      <span
        class="absolute text-gray-400 cursor-pointer"
        style="top: 8px; right: 10px"
        @click="deselectItem"
      >
        <x-circle-icon class="h-5" />
      </span>
    </div>
    <sw-select
      v-else
      ref="baseSelect"
      v-model="itemSelect"
      :options="items"
      :loading="loading"
      :show-labels="false"
      :preserve-search="true"
      :initial-search="item.name"
      :invalid="invalid"
      :placeholder="$t('estimates.item.select_an_item')"
      label="name"
      class="multi-select-item"
      @value="onTextChange"
      @select="onSelect"
    >
      <div slot="afterList">
        <button
          type="button"
          class="flex items-center justify-center w-full p-3 bg-gray-200 border-none outline-none"
          @click="openItemModal"
        >
          <shopping-cart-icon class="h-5 text-primary-400" />
          <label class="ml-2 text-sm leading-none text-primary-400">{{
            $t('general.add_new_item')
          }}</label>
        </button>
      </div>
    </sw-select>
    <div class="w-full pt-1 text-xs text-light">
      <sw-textarea
        v-autoresize
        v-model.trim="item.description"
        :invalid-description="invalidDescription"
        :placeholder="$t('estimates.item.type_item_description')"
        type="text"
        rows="1"
        variant="inv-desc"
        class="w-full text-gray-600 border-none resize-none"
        @input="$emit('onDesriptionInput')"
      />
      <div v-if="invalidDescription">
        <span class="text-danger">{{
          $tc('validation.description_maxlength')
        }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon, ShoppingCartIcon } from '@vue-hero-icons/solid'
const { maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    XCircleIcon,
    ShoppingCartIcon,
  },
  props: {
    item: {
      type: Object,
      required: true,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
    invalidDescription: {
      type: Boolean,
      required: false,
      default: false,
    },
    taxPerItem: {
      type: String,
      default: '',
    },
    taxes: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      itemSelect: null,
      loading: false,
    }
  },
  computed: {
    ...mapGetters('item', ['items']),
  },
  watch: {
    invalidDescription(newValue) {
      console.log(newValue)
    },
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('item', ['fetchItems']),
    async searchItems(search) {
      let data = {
        search,
        filter: {
          name: search,
          unit: '',
          price: '',
        },
        orderByField: '',
        orderBy: '',
        page: 1,
      }

      if (this.item) {
        data.item_id = this.item.item_id
      }

      this.loading = true

      await this.fetchItems(data)

      this.loading = false
    },
    onTextChange(val) {
      this.searchItems(val)

      this.$emit('search', val)
    },
    openItemModal() {
      this.$emit('onSelectItem')
      this.openModal({
        title: this.$t('items.add_item'),
        componentName: 'ItemModal',
        data: { taxPerItem: this.taxPerItem, taxes: this.taxes },
      })
    },
    onSelect(val) {
      this.$emit('select', val)
      this.fetchItems()
    },
    deselectItem() {
      this.itemSelect = null
      this.$emit('deselect')
    },
  },
}
</script>
