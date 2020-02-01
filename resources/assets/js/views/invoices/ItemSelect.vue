<template>
  <div class="item-selector">
    <div v-if="item.item_id" class="selected-item">
      {{ item.name }}

      <span class="deselect-icon" @click="deselectItem">
        <font-awesome-icon icon="times-circle" />
      </span>
    </div>
    <base-select
      v-else
      ref="baseSelect"
      v-model="itemSelect"
      :options="items"
      :show-labels="false"
      :preserve-search="true"
      :initial-search="item.name"
      :invalid="invalid"
      :placeholder="$t('invoices.item.select_an_item')"
      label="name"
      class="multi-select-item"
      @value="onTextChange"
      @select="(val) => $emit('select', val)"
    >
      <div slot="afterList">
        <button type="button" class="list-add-button" @click="openItemModal">
          <font-awesome-icon class="icon" icon="cart-plus" />
          <label>{{ $t('general.add_new_item') }}</label>
        </button>
      </div>
    </base-select>
    <div class="item-description">
      <base-text-area
        v-autoresize
        v-model="item.description"
        :invalid-description="invalidDescription"
        :placeholder="$t('invoices.item.type_item_description')"
        type="text"
        rows="1"
        class="description-input"
        @input="$emit('onDesriptionInput')"
      />
      <div v-if="invalidDescription">
        <span class="text-danger">{{ $tc('validation.description_maxlength') }}</span>
      </div>
      <!-- <textarea type="text" v-autoresize rows="1" class="description-input" v-model="item.description" placeholder="Type Item Description (optional)" /> -->
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    item: {
      type: Object,
      required: true
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false
    },
    invalidDescription: {
      type: Boolean,
      required: false,
      default: false
    },
    taxPerItem: {
      type: String,
      default: ''
    },
    taxes: {
      type: Array,
      default: null
    }
  },
  data () {
    return {
      itemSelect: null,
      loading: false
    }
  },
  computed: {
    ...mapGetters('item', [
      'items'
    ])
  },
  watch: {
    invalidDescription (newValue) {
      console.log(newValue)
    }
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('item', [
      'fetchItems'
    ]),
    async searchItems (search) {
      let data = {
        filter: {
          name: search,
          unit: '',
          price: ''
        },
        orderByField: '',
        orderBy: '',
        page: 1
      }

      this.loading = true

      await this.fetchItems(data)

      this.loading = false
    },
    onTextChange (val) {
      this.searchItems(val)

      this.$emit('search', val)
    },
    openItemModal () {
      this.$emit('onSelectItem')
      this.openModal({
        'title': 'Add Item',
        'componentName': 'ItemModal',
        'data': {taxPerItem: this.taxPerItem, taxes: this.taxes}
      })
    },
    deselectItem () {
      this.itemSelect = null
      this.$emit('deselect')
    }
  }
}
</script>
