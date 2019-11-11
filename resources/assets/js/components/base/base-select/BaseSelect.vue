<template>
  <div
    :tabindex="searchable ? -1 : tabindex"
    :class="{'multiselect--active': isOpen, 'multiselect--disabled': disabled, 'multiselect--above': isAbove }"
    :aria-owns="'listbox-'+id"
    class="base-select multiselect"
    role="combobox"
    @focus="activate()"
    @blur="searchable ? false : deactivate()"
    @keydown.self.down.prevent="pointerForward()"
    @keydown.self.up.prevent="pointerBackward()"
    @keypress.enter.tab.stop.self="addPointerElement($event)"
    @keyup.esc="deactivate()"
  >
    <slot :toggle="toggle" name="caret">
      <div class="multiselect__select" @mousedown.prevent.stop="toggle()" />
    </slot>
    <!-- <slot name="clear" :search="search"></slot> -->
    <div ref="tags" :class="{'in-valid': invalid}" class="multiselect__tags">
      <slot
        :search="search"
        :remove="removeElement"
        :values="visibleValues"
        :is-open="isOpen"
        name="selection"
      >
        <div v-show="visibleValues.length > 0" class="multiselect__tags-wrap">
          <template v-for="(option, index) of visibleValues" @mousedown.prevent>
            <slot :option="option" :search="search" :remove="removeElement" name="tag">
              <span :key="index" class="multiselect__tag">
                <span v-text="getOptionLabel(option)"/>
                <i class="multiselect__tag-icon" tabindex="1" @keypress.enter.prevent="removeElement(option)" @mousedown.prevent="removeElement(option)"/>
              </span>
            </slot>
          </template>
        </div>
        <template v-if="internalValue && internalValue.length > limit">
          <slot name="limit">
            <strong class="multiselect__strong" v-text="limitText(internalValue.length - limit)"/>
          </slot>
        </template>
      </slot>
      <transition name="multiselect__loading">
        <slot name="loading">
          <div v-show="loading" class="multiselect__spinner"/>
        </slot>
      </transition>
      <input
        ref="search"
        :name="name"
        :id="id"
        :placeholder="placeholder"
        :style="inputStyle"
        :value="search"
        :disabled="disabled"
        :tabindex="tabindex"
        :aria-controls="'listbox-'+id"
        :class="['multiselect__input']"
        type="text"
        autocomplete="off"
        spellcheck="false"
        @input="updateSearch($event.target.value)"
        @focus.prevent="activate()"
        @blur.prevent="deactivate()"
        @keyup.esc="deactivate()"
        @keydown.down.prevent="pointerForward()"
        @keydown.up.prevent="pointerBackward()"
        @keypress.enter.prevent.stop.self="addPointerElement($event)"
        @keydown.delete.stop="removeLastElement()"
      >
      <span
        v-if="isSingleLabelVisible"
        class="multiselect__single"
        @mousedown.prevent="toggle"
      >
        <slot :option="singleValue" name="singleLabel">
          <template>{{ currentOptionLabel }}</template>
        </slot>
      </span>
    </div>
    <transition name="multiselect">
      <div
        v-show="isOpen"
        ref="list"
        :style="{ maxHeight: optimizedHeight + 'px' }"
        class="multiselect__content-wrapper"
        tabindex="-1"
        @focus="activate"
        @mousedown.prevent
      >
        <ul :style="contentStyle" :id="'listbox-'+id" class="multiselect__content" role="listbox">
          <slot name="beforeList"/>
          <li v-if="multiple && max === internalValue.length">
            <span class="multiselect__option">
              <slot name="maxElements"> {{ $t('validation.maximum_options_error', { max: max }) }} </slot>
            </span>
          </li>
          <template v-if="!max || internalValue.length < max">
            <li
              v-for="(option, index) of filteredOptions"
              :key="index"
              :id="id + '-' + index"
              :role="!(option && (option.$isLabel || option.$isDisabled)) ? 'option' : null"
              class="multiselect__element"
            >
              <span
                v-if="!(option && (option.$isLabel || option.$isDisabled))"
                :class="optionHighlight(index, option)"
                :data-select="option && option.isTag ? tagPlaceholder : selectLabelText"
                :data-selected="selectedLabelText"
                :data-deselect="deselectLabelText"
                class="multiselect__option"
                @click.stop="select(option)"
                @mouseenter.self="pointerSet(index)"
              >
                <slot :option="option" :search="search" name="option">
                  <span>{{ getOptionLabel(option) }}</span>
                </slot>
              </span>
              <span
                v-if="option && (option.$isLabel || option.$isDisabled)"
                :data-select="groupSelect && selectGroupLabelText"
                :data-deselect="groupSelect && deselectGroupLabelText"
                :class="groupHighlight(index, option)"
                class="multiselect__option"
                @mouseenter.self="groupSelect && pointerSet(index)"
                @mousedown.prevent="selectGroup(option)"
              >
                <slot :option="option" :search="search" name="option">
                  <span>{{ getOptionLabel(option) }}</span>
                </slot>
              </span>
            </li>
          </template>
          <li v-if="showNoOptions && (options.length === 0 && !search && !loading)">
            <span class="multiselect__option">
              <slot name="noOptions">{{ $t('general.list_is_empty') }}</slot>
            </span>
          </li>
        </ul>
        <slot name="afterList"/>
      </div>
    </transition>
  </div>
</template>

<script>
import multiselectMixin from './multiselectMixin'
import pointerMixin from './pointerMixin'

export default {
  name: 'vue-multiselect',
  mixins: [multiselectMixin, pointerMixin],
  props: {
    /**
     * name attribute to match optional label element
     * @default ''
     * @type {String}
     */
    name: {
      type: String,
      default: ''
    },
    /**
     * String to show when pointing to an option
     * @default 'Press enter to select'
     * @type {String}
     */
    selectLabel: {
      type: String,
      default: ''
    },
    /**
     * String to show when pointing to an option
     * @default 'Press enter to select'
     * @type {String}
     */
    selectGroupLabel: {
      type: String,
      default: ''
    },
    /**
     * String to show next to selected option
     * @default 'Selected'
     * @type {String}
     */
    selectedLabel: {
      type: String,
      default: 'Selected'
    },
    /**
     * String to show when pointing to an already selected option
     * @default 'Press enter to remove'
     * @type {String}
     */
    deselectLabel: {
      type: String,
      default: 'Press enter to remove'
    },
    /**
     * String to show when pointing to an already selected option
     * @default 'Press enter to remove'
     * @type {String}
     */
    deselectGroupLabel: {
      type: String,
      default: 'Press enter to deselect group'
    },
    /**
     * Decide whether to show pointer labels
     * @default true
     * @type {Boolean}
     */
    showLabels: {
      type: Boolean,
      default: true
    },
    /**
     * Limit the display of selected options. The rest will be hidden within the limitText string.
     * @default 99999
     * @type {Integer}
     */
    limit: {
      type: Number,
      default: 99999
    },
    /**
     * Sets maxHeight style value of the dropdown
     * @default 300
     * @type {Integer}
     */
    maxHeight: {
      type: Number,
      default: 300
    },
    /**
     * Function that process the message shown when selected
     * elements pass the defined limit.
     * @default 'and * more'
     * @param {Int} count Number of elements more than limit
     * @type {Function}
     */
    limitText: {
      type: Function,
      default: count => `and ${count} more`
    },
    /**
     * Set true to trigger the loading spinner.
     * @default False
     * @type {Boolean}
     */
    loading: {
      type: Boolean,
      default: false
    },
    /**
     * Disables the multiselect if true.
     * @default false
     * @type {Boolean}
     */
    disabled: {
      type: Boolean,
      default: false
    },
    /**
     * Fixed opening direction
     * @default ''
     * @type {String}
     */
    openDirection: {
      type: String,
      default: ''
    },
    /**
     * Shows slot with message about empty options
     * @default true
     * @type {Boolean}
     */
    showNoOptions: {
      type: Boolean,
      default: true
    },
    showNoResults: {
      type: Boolean,
      default: true
    },
    tabindex: {
      type: Number,
      default: 0
    },
    invalid: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    isSingleLabelVisible () {
      return (
        (this.singleValue || this.singleValue === 0) &&
        (!this.isOpen || !this.searchable) &&
        !this.visibleValues.length
      )
    },
    isPlaceholderVisible () {
      return !this.internalValue.length && (!this.searchable || !this.isOpen)
    },
    visibleValues () {
      return this.multiple ? this.internalValue.slice(0, this.limit) : []
    },
    singleValue () {
      return this.internalValue[0]
    },
    deselectLabelText () {
      return this.showLabels ? this.deselectLabel : ''
    },
    deselectGroupLabelText () {
      return this.showLabels ? this.deselectGroupLabel : ''
    },
    selectLabelText () {
      return this.showLabels ? this.selectLabel : ''
    },
    selectGroupLabelText () {
      return this.showLabels ? this.selectGroupLabel : ''
    },
    selectedLabelText () {
      return this.showLabels ? this.selectedLabel : ''
    },
    inputStyle () {
      if (
        this.searchable ||
        (this.multiple && this.value && this.value.length)
      ) {
        // Hide input by setting the width to 0 allowing it to receive focus

        return this.isOpen
          ? { width: '100%' }
          : ((this.value) ? { width: '0', position: 'absolute', padding: '0' } : '')
      }
    },
    contentStyle () {
      return this.options.length
        ? { display: 'inline-block' }
        : { display: 'block' }
    },
    isAbove () {
      if (this.openDirection === 'above' || this.openDirection === 'top') {
        return true
      } else if (
        this.openDirection === 'below' ||
        this.openDirection === 'bottom'
      ) {
        return false
      } else {
        return this.preferredOpenDirection === 'above'
      }
    },
    showSearchInput () {
      return (
        this.searchable &&
        (this.hasSingleSelectedSlot &&
        (this.visibleSingleValue || this.visibleSingleValue === 0)
          ? this.isOpen
          : true)
      )
    }
  }
}
</script>
