<template>
  <div
    :tabindex="searchable ? -1 : tabindex"
    :class="multiSelectStyle"
    :aria-owns="'listbox-' + id"
    role="combobox"
    @focus="activate()"
    @blur="searchable ? false : deactivate()"
    @keydown.self.down.prevent="pointerForward()"
    @keydown.self.up.prevent="pointerBackward()"
    @keypress.enter.tab.stop.self="addPointerElement($event)"
    @keyup.esc="deactivate()"
  >
    <slot :toggle="toggle" name="caret">
      <div :class="multiselectSelectStyle" @mousedown.prevent.stop="toggle()" />
    </slot>
    <!-- <slot name="clear" :search="search"></slot> -->
    <div ref="tags" :class="multiSelectTagsStyle">
      <slot
        :search="search"
        :remove="removeElement"
        :values="visibleValues"
        :is-open="isOpen"
        name="selection"
      >
        <div
          v-show="visibleValues.length > 0"
          :class="multiselectTagsWrapStyle"
        >
          <template v-for="(option, index) of visibleValues" @mousedown.prevent>
            <slot
              :option="option"
              :search="search"
              :remove="removeElement"
              name="tag"
            >
              <span :key="index" :class="multiselectTagStyle">
                <span v-text="getOptionLabel(option)" />
                <i
                  :class="multiselectTagIconStyle"
                  tabindex="1"
                  @keypress.enter.prevent="removeElement(option)"
                  @mousedown.prevent="removeElement(option)"
                />
              </span>
            </slot>
          </template>
        </div>
        <template v-if="internalValue && internalValue.length > limit">
          <slot name="limit">
            <strong
              :class="multiselectStrongStyle"
              v-text="limitText(internalValue.length - limit)"
            />
          </slot>
        </template>
      </slot>
      <transition name="multiselect__loading">
        <slot name="loading">
          <div v-show="loading" :class="multiselectSpinnerStyle" />
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
        :aria-controls="'listbox-' + id"
        :class="multiselectInputStyle"
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
      />
      <span
        v-if="isSingleLabelVisible"
        :class="multiselectSingleStyle"
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
        :class="multiselectContentWrapperStyle"
        tabindex="-1"
        @focus="activate"
        @mousedown.prevent
      >
        <ul
          :style="contentStyle"
          :id="'listbox-' + id"
          :class="multiselectContentStyle"
          role="listbox"
        >
          <slot name="beforeList" />
          <li v-if="multiple && max === internalValue.length">
            <span :class="multiselectOptionStyle">
              <slot name="maxElements">
                {{ $t('validation.maximum_options_error', { max: max }) }}
              </slot>
            </span>
          </li>
          <template v-if="!max || internalValue.length < max">
            <li
              v-for="(option, index) of filteredOptions"
              :key="index"
              :id="id + '-' + index"
              :role="
                !(option && (option.$isLabel || option.$isDisabled))
                  ? 'option'
                  : null
              "
              :class="multiselectElementStyle"
            >
              <span
                v-if="!(option && (option.$isLabel || option.$isDisabled))"
                :class="optionHighlight(index, option)"
                :data-select="
                  option && option.isTag ? tagPlaceholder : selectLabelText
                "
                :data-selected="selectedLabelText"
                :data-deselect="deselectLabelText"
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
                @mouseenter.self="groupSelect && pointerSet(index)"
                @mousedown.prevent="selectGroup(option)"
              >
                <slot :option="option" :search="search" name="option">
                  <span>{{ getOptionLabel(option) }}</span>
                </slot>
              </span>
            </li>
          </template>
          <li
            v-if="showNoOptions && (options.length === 0 && !search && !loading)"
          >
            <span :class="multiselectOptionStyle">
              <slot name="noOptions">{{ $t('general.list_is_empty') }}</slot>
            </span>
          </li>
        </ul>
        <slot name="afterList" />
      </div>
    </transition>
  </div>
</template>

<script>
import CraterTheme from '../theme/index'
import multiselectMixin from './multiselectMixin'
import pointerMixin from './pointerMixin'

const {
  activeBaseSelectContainer,
  disabledBaseSelectContainer,
  baseSelectContainer,
  multiSelect,
  disabledMultiSelect,
  multiSelectTags,
  multiSelectTagsInvalid,
  multiSelectTagsDefaultColor,
  disabledMultiSelectTags,
  multiselectTagsWrap,
  multiselectTag,
  multiselectTagIcon,
  multiselectStrong,
  multiselectSpinner,
  multiselectInput,
  multiselectSingle,
  multiselectContentWrapper,
  multiselectContent,
  multiselectOption,
  multiselectElement,
} = CraterTheme.BaseSelect

export default {
  name: 'VueMultiselect',
  mixins: [multiselectMixin, pointerMixin],
  props: {
    /**
     * name attribute to match optional label element
     * @default ''
     * @type {String}
     */
    name: {
      type: String,
      default: '',
    },
    /**
     * String to show when pointing to an option
     * @default 'Press enter to select'
     * @type {String}
     */
    selectLabel: {
      type: String,
      default: '',
    },
    /**
     * String to show when pointing to an option
     * @default 'Press enter to select'
     * @type {String}
     */
    selectGroupLabel: {
      type: String,
      default: '',
    },
    /**
     * String to show next to selected option
     * @default 'Selected'
     * @type {String}
     */
    selectedLabel: {
      type: String,
      default: 'Selected',
    },
    /**
     * String to show when pointing to an already selected option
     * @default 'Press enter to remove'
     * @type {String}
     */
    deselectLabel: {
      type: String,
      default: 'Press enter to remove',
    },
    /**
     * String to show when pointing to an already selected option
     * @default 'Press enter to remove'
     * @type {String}
     */
    deselectGroupLabel: {
      type: String,
      default: 'Press enter to deselect group',
    },
    /**
     * Decide whether to show pointer labels
     * @default true
     * @type {Boolean}
     */
    showLabels: {
      type: Boolean,
      default: true,
    },
    /**
     * Limit the display of selected options. The rest will be hidden within the limitText string.
     * @default 99999
     * @type {Integer}
     */
    limit: {
      type: Number,
      default: 99999,
    },
    /**
     * Sets maxHeight style value of the dropdown
     * @default 300
     * @type {Integer}
     */
    maxHeight: {
      type: Number,
      default: 300,
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
      default: (count) => `and ${count} more`,
    },
    /**
     * Set true to trigger the loading spinner.
     * @default False
     * @type {Boolean}
     */
    loading: {
      type: Boolean,
      default: false,
    },
    /**
     * Disables the multiselect if true.
     * @default false
     * @type {Boolean}
     */
    disabled: {
      type: Boolean,
      default: false,
    },
    /**
     * Fixed opening direction
     * @default ''
     * @type {String}
     */
    openDirection: {
      type: String,
      default: '',
    },
    /**
     * Shows slot with message about empty options
     * @default true
     * @type {Boolean}
     */
    showNoOptions: {
      type: Boolean,
      default: true,
    },
    showNoResults: {
      type: Boolean,
      default: true,
    },
    tabindex: {
      type: Number,
      default: 0,
    },
    invalid: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    isSingleLabelVisible() {
      return (
        (this.singleValue || this.singleValue === 0) &&
        (!this.isOpen || !this.searchable) &&
        !this.visibleValues.length
      )
    },
    isPlaceholderVisible() {
      return !this.internalValue.length && (!this.searchable || !this.isOpen)
    },
    visibleValues() {
      return this.multiple ? this.internalValue.slice(0, this.limit) : []
    },
    singleValue() {
      return this.internalValue[0]
    },
    deselectLabelText() {
      return this.showLabels ? this.deselectLabel : ''
    },
    deselectGroupLabelText() {
      return this.showLabels ? this.deselectGroupLabel : ''
    },
    selectLabelText() {
      return this.showLabels ? this.selectLabel : ''
    },
    selectGroupLabelText() {
      return this.showLabels ? this.selectGroupLabel : ''
    },
    selectedLabelText() {
      return this.showLabels ? this.selectedLabel : ''
    },
    inputStyle() {
      if (
        this.searchable ||
        (this.multiple && this.value && this.value.length)
      ) {
        // Hide input by setting the width to 0 allowing it to receive focus

        return this.isOpen
          ? { width: '100%' }
          : this.value
          ? { width: '0', position: 'absolute', padding: '0' }
          : ''
      }
    },
    contentStyle() {
      return this.options.length
        ? { display: 'inline-block' }
        : { display: 'block' }
    },
    isAbove() {
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
    showSearchInput() {
      return (
        this.searchable &&
        (this.hasSingleSelectedSlot &&
        (this.visibleSingleValue || this.visibleSingleValue === 0)
          ? this.isOpen
          : true)
      )
    },
    multiSelectStyle() {
      let style = ['multiselect--active', baseSelectContainer]
      if (this.isOpen) {
        style.push(activeBaseSelectContainer)
      }
      if (this.disabled) {
        style.push(disabledBaseSelectContainer)
      }
      if (this.isAbove) {
        style.push('multiselect--above')
      }
      return style
    },
    multiselectSelectStyle() {
      let style = [multiSelect]

      if (this.disabled) {
        style.push(disabledMultiSelect)
      }

      return style
    },
    multiSelectTagsStyle() {
      let style = [multiSelectTags]

      if (this.invalid) {
        style.push(multiSelectTagsInvalid)
      } else {
        style.push(multiSelectTagsDefaultColor)
      }

      if (this.disabled) {
        style.push(disabledMultiSelectTags)
      }

      return style
    },
    multiselectTagsWrapStyle() {
      return [multiselectTagsWrap]
    },
    multiselectTagStyle() {
      return [multiselectTag]
    },
    multiselectTagIconStyle() {
      return [multiselectTagIcon]
    },
    multiselectStrongStyle() {
      return [multiselectStrong]
    },
    multiselectSpinnerStyle() {
      return [multiselectSpinner]
    },
    multiselectInputStyle() {
      return [multiselectInput]
    },
    multiselectSingleStyle() {
      return [multiselectSingle]
    },
    multiselectContentWrapperStyle() {
      return [multiselectContentWrapper]
    },
    multiselectContentStyle() {
      return [multiselectContent]
    },
    multiselectOptionStyle() {
      return [multiselectOption]
    },
    multiselectElementStyle() {
      return [multiselectElement]
    },
  },
}
</script>
<style lang="scss">
fieldset[disabled] .multiselect {
  pointer-events: none;
}

.multiselect {
  min-height: 40px;
}

.multiselect__spinner {
  right: 1px;
  top: 1px;
}

.multiselect__spinner:before,
.multiselect__spinner:after {
  position: absolute;
  content: '';
  top: 50%;
  left: 50%;
  margin: -8px 0 0 -8px;
  z-index: 5;
  width: 16px;
  height: 16px;
  border-radius: 100%;
  border-color: #41b883 transparent transparent;
  border-style: solid;
  border-width: 2px;
  box-shadow: 0 0 0 1px transparent;
}

.multiselect__spinner:before {
  animation: spinning 2.4s cubic-bezier(0.41, 0.26, 0.2, 0.62);
  animation-iteration-count: infinite;
}

.multiselect__spinner:after {
  animation: spinning 2.4s cubic-bezier(0.51, 0.09, 0.21, 0.8);
  animation-iteration-count: infinite;
}

.multiselect__loading-enter-active,
.multiselect__loading-leave-active {
  transition: opacity 0.4s ease-in-out;
  opacity: 1;
}

.multiselect__loading-enter,
.multiselect__loading-leave-active {
  opacity: 0;
}

.multiselect,
.multiselect__input,
.multiselect__single {
  font-family: inherit;
  // font-size: 14px;
  touch-action: manipulation;
}

.multiselect {
  box-sizing: content-box;
  display: block;
  position: relative;
  width: 100%;
  min-height: 40px;
  text-align: left;
  color: #35495e;
}

.multiselect * {
  box-sizing: border-box;
}

.multiselect:focus {
  border: 1px solid #817ae3 !important;
}

.multiselect--disabled {
  pointer-events: none;
  opacity: 0.6;
}

.multiselect--active:not(.multiselect--above) .multiselect__current,
.multiselect--active:not(.multiselect--above) .multiselect__input,
.multiselect--active:not(.multiselect--above) .multiselect__tags {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}

.multiselect--active .multiselect__select {
  transform: rotateZ(180deg);
}
.multiselect--above.multiselect--active .multiselect__current,
.multiselect--above.multiselect--active .multiselect__input,
.multiselect--above.multiselect--active .multiselect__tags {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.multiselect__input,
.multiselect__single {
  min-height: 20px;
  transition: border 0.1s ease;
}

.multiselect__input::placeholder {
  color: #b9c1d1;
}

.multiselect__tag ~ .multiselect__input,
.multiselect__tag ~ .multiselect__single {
  width: auto;
}
.multiselect__input:hover,
.multiselect__single:hover {
  border-color: #cfcfcf;
}
.multiselect__input:focus,
.multiselect__single:focus {
  border-color: #a8a8a8;
  outline: none;
}
.multiselect__tag {
  background: #41b883;
  text-overflow: ellipsis;
}
.multiselect__tag-icon {
  font-style: initial;
}
.multiselect__tag-icon:after {
  content: '×';
  color: #266d4d;
  font-size: 14px;
}
.multiselect__tag-icon:focus,
.multiselect__tag-icon:hover {
  background: #369a6e;
}
.multiselect__tag-icon:focus:after,
.multiselect__tag-icon:hover:after {
  color: white;
}
.multiselect__current {
  line-height: 16px;
  min-height: 40px;
  box-sizing: border-box;
  display: block;
  overflow: hidden;
  padding: 8px 12px 0;
  padding-right: 30px;
  white-space: nowrap;
  margin: 0;
  text-decoration: none;
  border-radius: 5px;
  border: 1px solid #ebf1fa;
  cursor: pointer;
}
.multiselect__select {
  right: 1px;
  top: 1px;
  transition: transform 0.2s;
}
.multiselect__select:before {
  position: relative;
  right: 0;
  top: 65%;
  color: #a5acc1;
  margin-top: 4px;
  border-style: solid;
  border-width: 5px 5px 0 5px;
  border-color: #a5acc1 transparent transparent transparent;
  content: '';
}
.multiselect__placeholder {
  color: #b9c1d1;
  display: inline-block;
  margin-bottom: 10px;
  padding-top: 2px;
}
.multiselect--active .multiselect__placeholder {
  display: none;
}
.multiselect__content-wrapper {
  max-height: 240px;
  -webkit-overflow-scrolling: touch;
}
.multiselect--above .multiselect__content-wrapper {
  bottom: 100%;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  border-bottom: none;
  border-top: 1px solid #e8e8e8;
}
.multiselect__content::webkit-scrollbar {
  display: none;
}
.multiselect__option {
  min-height: 40px;
}
.multiselect__option:after {
  top: 0;
  right: 0;
  position: absolute;
  line-height: 40px;
  padding-right: 12px;
  padding-left: 20px;
  font-size: 13px;
}
.multiselect__option--highlight {
  background: #41b883;
  outline: none;
  color: white;
}
.multiselect__option--highlight:after {
  content: attr(data-select);
  background: #41b883;
  color: white;
}
.multiselect__option--selected {
  background: #f3f3f3;
  color: #35495e;
  font-weight: bold;
}
.multiselect__option--selected:after {
  content: attr(data-selected);
  color: silver;
}
.multiselect__option--selected.multiselect__option--highlight {
  background: #ff6a6a;
  color: #fff;
}
.multiselect__option--selected.multiselect__option--highlight:after {
  background: #ff6a6a;
  content: attr(data-deselect);
  color: #fff;
}

.multiselect--disabled .multiselect__current,
.multiselect--disabled .multiselect__select {
  background: #ebf1fa;
  color: #b9c1d1;
}

.multiselect--disabled .multiselect__input,
.multiselect--disabled .multiselect__single {
  background: #ebf1fa;
  color: #b9c1d1;
}

.multiselect__option--disabled {
  background: transparent !important;
  color: #dddddd !important;
  cursor: text;
  pointer-events: none;
}

.multiselect__option--group {
  background: #ededed;
  color: #35495e;
}

.multiselect__option--group.multiselect__option--highlight {
  background: #35495e;
  color: #fff;
}

.multiselect__option--group.multiselect__option--highlight:after {
  background: #35495e;
}

.multiselect__option--disabled.multiselect__option--highlight {
  background: #dedede;
}

.multiselect__option--group-selected.multiselect__option--highlight {
  background: #ff6a6a;
  color: #fff;
}

.multiselect__option--group-selected.multiselect__option--highlight:after {
  background: #ff6a6a;
  content: attr(data-deselect);
  color: #fff;
}

.multiselect-enter-active,
.multiselect-leave-active {
  transition: all 0.15s ease;
}

.multiselect-enter,
.multiselect-leave-active {
  opacity: 0;
}

*[dir='rtl'] .multiselect {
  text-align: right;
}

*[dir='rtl'] .multiselect__select {
  right: auto;
  left: 1px;
}

*[dir='rtl'] .multiselect__tags {
  padding: 8px 8px 0px 40px;
}

*[dir='rtl'] .multiselect__content {
  text-align: right;
}

*[dir='rtl'] .multiselect__option:after {
  right: auto;
  left: 0;
}

*[dir='rtl'] .multiselect__clear {
  right: auto;
  left: 12px;
}

*[dir='rtl'] .multiselect__spinner {
  right: auto;
  left: 1px;
}

@keyframes spinning {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(2turn);
  }
}

.multiselect {
  .multiselect__option--highlight {
    background: #5851d8;
    color: #040405;
    font-weight: normal !important;

    &.multiselect__option--selected {
      background: #ebf1fa;
      color: #040405;
      font-size: 1rem;
      font-weight: normal !important;

      &::after {
        background: #040405;
        color: #fff;
      }
    }

    &::after {
      background: #040405;
      color: #fff;
    }
  }

  .multiselect__option--selected {
    font-weight: normal !important;
    background: #ebf1fa;
  }

  .multiselect__tags-wrap .multiselect__tag {
    background: #5851d8;
    color: #040405;

    .multiselect__tag-icon {
      &:hover {
        background: #5851d8;
      }

      &::after {
        color: #040405;
      }
    }
  }

  &.error {
    border: 1px solid #fb7178;
    border-radius: 5px;
  }
}
</style>
