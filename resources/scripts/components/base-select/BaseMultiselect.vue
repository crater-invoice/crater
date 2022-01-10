<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      class="w-full"
      style="height: 40px"
    />
  </BaseContentPlaceholders>
  <div
    v-else
    :id="id"
    ref="multiselect"
    :tabindex="tabindex"
    :class="classList.container"
    @focusin="activate"
    @focusout="deactivate"
    @keydown="handleKeydown"
    @focus="handleFocus"
  >
    <!-- Search -->
    <template v-if="mode !== 'tags' && searchable && !disabled">
      <input
        ref="input"
        :type="inputType"
        :modelValue="search"
        :value="search"
        :class="classList.search"
        :autocomplete="autocomplete"
        @input="handleSearchInput"
        @paste.stop="handlePaste"
      />
    </template>

    <!-- Tags (with search) -->
    <template v-if="mode == 'tags'">
      <div :class="classList.tags">
        <slot
          v-for="(option, i, key) in iv"
          name="tag"
          :option="option"
          :handleTagRemove="handleTagRemove"
          :disabled="disabled"
        >
          <span :key="key" :class="classList.tag">
            {{ option[label] }}
            <span
              v-if="!disabled"
              :class="classList.tagRemove"
              @mousedown.stop="handleTagRemove(option, $event)"
            >
              <span :class="classList.tagRemoveIcon"></span>
            </span>
          </span>
        </slot>

        <div :class="classList.tagsSearchWrapper">
          <!-- Used for measuring search width -->
          <span :class="classList.tagsSearchCopy">{{ search }}</span>

          <!-- Actual search input -->
          <input
            v-if="searchable && !disabled"
            ref="input"
            :type="inputType"
            :modelValue="search"
            :value="search"
            :class="classList.tagsSearch"
            :autocomplete="autocomplete"
            style="box-shadow: none !important"
            @input="handleSearchInput"
            @paste.stop="handlePaste"
          />
        </div>
      </div>
    </template>

    <!-- Single label -->
    <template v-if="mode == 'single' && hasSelected && !search && iv">
      <slot name="singlelabel" :value="iv">
        <div :class="classList.singleLabel">
          {{ iv[label] }}
        </div>
      </slot>
    </template>

    <!-- Multiple label -->
    <template v-if="mode == 'multiple' && hasSelected && !search">
      <slot name="multiplelabel" :values="iv">
        <div :class="classList.multipleLabel">
          {{ multipleLabelText }}
        </div>
      </slot>
    </template>

    <!-- Placeholder -->
    <template v-if="placeholder && !hasSelected && !search">
      <slot name="placeholder">
        <div :class="classList.placeholder">
          {{ placeholder }}
        </div>
      </slot>
    </template>

    <!-- Spinner -->
    <slot v-if="busy" name="spinner">
      <span :class="classList.spinner"></span>
    </slot>

    <!-- Clear -->
    <slot
      v-if="hasSelected && !disabled && canClear && !busy"
      name="clear"
      :clear="clear"
    >
      <span :class="classList.clear" @mousedown="clear"
        ><span :class="classList.clearIcon"></span
      ></span>
    </slot>

    <!-- Caret -->
    <slot v-if="caret" name="caret">
      <span
        :class="classList.caret"
        @mousedown.prevent.stop="handleCaretClick"
      ></span>
    </slot>

    <!-- Options -->
    <div :class="classList.dropdown" tabindex="-1">
      <div class="w-full overflow-y-auto">
        <slot name="beforelist" :options="fo"></slot>

        <ul :class="classList.options">
          <template v-if="groups">
            <li
              v-for="(group, i, key) in fg"
              :key="key"
              :class="classList.group"
            >
              <div
                :class="classList.groupLabel(group)"
                :data-pointed="isPointed(group)"
                @mouseenter="setPointer(group)"
                @click="handleGroupClick(group)"
              >
                <slot name="grouplabel" :group="group">
                  <span>{{ group[groupLabel] }}</span>
                </slot>
              </div>

              <ul :class="classList.groupOptions">
                <li
                  v-for="(option, i, key) in group.__VISIBLE__"
                  :key="key"
                  :class="classList.option(option, group)"
                  :data-pointed="isPointed(option)"
                  @mouseenter="setPointer(option)"
                  @click="handleOptionClick(option)"
                >
                  <slot name="option" :option="option" :search="search">
                    <span>{{ option[label] }}</span>
                  </slot>
                </li>
              </ul>
            </li>
          </template>
          <template v-else>
            <li
              v-for="(option, i, key) in fo"
              :key="key"
              :class="classList.option(option)"
              :data-pointed="isPointed(option)"
              @mouseenter="setPointer(option)"
              @click="handleOptionClick(option)"
            >
              <slot name="option" :option="option" :search="search">
                <span>{{ option[label] }}</span>
              </slot>
            </li>
          </template>
        </ul>

        <slot v-if="noOptions" name="nooptions">
          <div :class="classList.noOptions" v-html="noOptionsText"></div>
        </slot>

        <slot v-if="noResults" name="noresults">
          <div :class="classList.noResults" v-html="noResultsText"></div>
        </slot>

        <slot name="afterlist" :options="fo"> </slot>
      </div>
      <slot name="action"></slot>
    </div>

    <!-- Hacky input element to show HTML5 required warning -->
    <input
      v-if="required"
      :class="classList.fakeInput"
      tabindex="-1"
      :value="textValue"
      required
    />

    <!-- Native input support -->
    <template v-if="nativeSupport">
      <input
        v-if="mode == 'single'"
        type="hidden"
        :name="name"
        :value="plainValue !== undefined ? plainValue : ''"
      />
      <template v-else>
        <input
          v-for="(v, i) in plainValue"
          :key="i"
          type="hidden"
          :name="`${name}[]`"
          :value="v"
        />
      </template>
    </template>

    <!-- Create height for empty input -->
    <div :class="classList.spacer"></div>
  </div>
</template>

<script>
import useData from './composables/useData'
import useValue from './composables/useValue'
import useSearch from './composables/useSearch'
import usePointer from './composables/usePointer'
import useOptions from './composables/useOptions'
import usePointerAction from './composables/usePointerAction'
import useDropdown from './composables/useDropdown'
import useMultiselect from './composables/useMultiselect'
import useKeyboard from './composables/useKeyboard'
import useClasses from './composables/useClasses'

export default {
  name: 'BaseMultiselect',
  props: {
    preserveSearch: {
      type: Boolean,
      default: false,
    },
    initialSearch: {
      type: String,
      default: null,
    },
    contentLoading: {
      type: Boolean,
      default: false,
    },
    value: {
      required: false,
    },
    modelValue: {
      required: false,
    },
    options: {
      type: [Array, Object, Function],
      required: false,
      default: () => [],
    },
    id: {
      type: [String, Number],
      required: false,
    },
    name: {
      type: [String, Number],
      required: false,
      default: 'multiselect',
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    label: {
      type: String,
      required: false,
      default: 'label',
    },
    trackBy: {
      type: String,
      required: false,
      default: 'label',
    },
    valueProp: {
      type: String,
      required: false,
      default: 'value',
    },
    placeholder: {
      type: String,
      required: false,
      default: null,
    },
    mode: {
      type: String,
      required: false,
      default: 'single', // single|multiple|tags
    },
    searchable: {
      type: Boolean,
      required: false,
      default: false,
    },
    limit: {
      type: Number,
      required: false,
      default: -1,
    },
    hideSelected: {
      type: Boolean,
      required: false,
      default: true,
    },
    createTag: {
      type: Boolean,
      required: false,
      default: false,
    },
    appendNewTag: {
      type: Boolean,
      required: false,
      default: true,
    },
    caret: {
      type: Boolean,
      required: false,
      default: true,
    },
    loading: {
      type: Boolean,
      required: false,
      default: false,
    },
    noOptionsText: {
      type: String,
      required: false,
      default: 'The list is empty',
    },
    noResultsText: {
      type: String,
      required: false,
      default: 'No results found',
    },
    multipleLabel: {
      type: Function,
      required: false,
    },
    object: {
      type: Boolean,
      required: false,
      default: false,
    },
    delay: {
      type: Number,
      required: false,
      default: -1,
    },
    minChars: {
      type: Number,
      required: false,
      default: 0,
    },
    resolveOnLoad: {
      type: Boolean,
      required: false,
      default: true,
    },
    filterResults: {
      type: Boolean,
      required: false,
      default: true,
    },
    clearOnSearch: {
      type: Boolean,
      required: false,
      default: false,
    },
    clearOnSelect: {
      type: Boolean,
      required: false,
      default: true,
    },
    canDeselect: {
      type: Boolean,
      required: false,
      default: true,
    },
    canClear: {
      type: Boolean,
      required: false,
      default: false,
    },
    max: {
      type: Number,
      required: false,
      default: -1,
    },
    showOptions: {
      type: Boolean,
      required: false,
      default: true,
    },
    addTagOn: {
      type: Array,
      required: false,
      default: () => ['enter'],
    },
    required: {
      type: Boolean,
      required: false,
      default: false,
    },
    openDirection: {
      type: String,
      required: false,
      default: 'bottom',
    },
    nativeSupport: {
      type: Boolean,
      required: false,
      default: false,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
    classes: {
      type: Object,
      required: false,
      default: () => ({
        container:
          'p-0 relative mx-auto w-full flex items-center justify-end box-border cursor-pointer border border-gray-200 rounded-md bg-white text-sm leading-snug outline-none max-h-10',
        containerDisabled:
          'cursor-default bg-gray-200 bg-opacity-50 !text-gray-400',
        containerOpen: '',
        containerOpenTop: '',
        containerActive: 'ring-1 ring-primary-400 border-primary-400',
        containerInvalid:
          'border-red-400 ring-red-400 focus:ring-red-400 focus:border-red-400',
        containerInvalidActive: 'ring-1 border-red-400 ring-red-400',
        singleLabel:
          'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5',
        multipleLabel:
          'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5',
        search:
          'w-full absolute inset-0 outline-none appearance-none box-border border-0 text-sm font-sans bg-white rounded-md pl-3.5',
        tags: 'grow shrink flex flex-wrap mt-1 pl-2',
        tag: 'bg-primary-500 text-white text-sm font-semibold py-0.5 pl-2 rounded mr-1 mb-1 flex items-center whitespace-nowrap',
        tagDisabled: 'pr-2 !bg-gray-400 text-white',
        tagRemove:
          'flex items-center justify-center p-1 mx-0.5 rounded-sm hover:bg-black hover:bg-opacity-10 group',
        tagRemoveIcon:
          'bg-multiselect-remove text-white bg-center bg-no-repeat opacity-30 inline-block w-3 h-3 group-hover:opacity-60',
        tagsSearchWrapper: 'inline-block relative mx-1 mb-1 grow shrink h-full',
        tagsSearch:
          'absolute inset-0 border-0 focus:outline-none !shadow-none !focus:shadow-none appearance-none p-0 text-sm font-sans box-border w-full',
        tagsSearchCopy: 'invisible whitespace-pre-wrap inline-block h-px',
        placeholder:
          'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 text-gray-400 text-sm',
        caret:
          'bg-multiselect-caret bg-center bg-no-repeat w-5 h-5 py-px box-content z-5 relative mr-1 opacity-40 shrink-0 grow-0 transition-transform',
        caretOpen: 'rotate-180 pointer-events-auto',
        clear:
          'pr-3.5 relative z-10 opacity-40 transition duration-300 shrink-0 grow-0 flex hover:opacity-80',
        clearIcon:
          'bg-multiselect-remove bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block',
        spinner:
          'bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin shrink-0 grow-0',
        dropdown:
          'max-h-60 shadow-lg absolute -left-px -right-px -bottom-1 translate-y-full border border-gray-300 mt-1 overflow-y-auto z-50 bg-white flex flex-col rounded-md',
        dropdownTop:
          '-translate-y-full -top-2 bottom-auto flex-col-reverse rounded-md',
        dropdownHidden: 'hidden',
        options: 'flex flex-col p-0 m-0 list-none',
        optionsTop: 'flex-col-reverse',
        group: 'p-0 m-0',
        groupLabel:
          'flex text-sm box-border items-center justify-start text-left py-1 px-3 font-semibold bg-gray-200 cursor-default leading-normal',
        groupLabelPointable: 'cursor-pointer',
        groupLabelPointed: 'bg-gray-300 text-gray-700',
        groupLabelSelected: 'bg-primary-600 text-white',
        groupLabelDisabled: 'bg-gray-100 text-gray-300 cursor-not-allowed',
        groupLabelSelectedPointed: 'bg-primary-600 text-white opacity-90',
        groupLabelSelectedDisabled:
          'text-primary-100 bg-primary-600 bg-opacity-50 cursor-not-allowed',
        groupOptions: 'p-0 m-0',
        option:
          'flex items-center justify-start box-border text-left cursor-pointer text-sm leading-snug py-2 px-3',
        optionPointed: 'text-gray-800 bg-gray-100',
        optionSelected: 'text-white bg-primary-500',
        optionDisabled: 'text-gray-300 cursor-not-allowed',
        optionSelectedPointed: 'text-white bg-primary-500 opacity-90',
        optionSelectedDisabled:
          'text-primary-100 bg-primary-500 bg-opacity-50 cursor-not-allowed',
        noOptions: 'py-2 px-3 text-gray-600 bg-white',
        noResults: 'py-2 px-3 text-gray-600 bg-white',
        fakeInput:
          'bg-transparent absolute left-0 right-0 -bottom-px w-full h-px border-0 p-0 appearance-none outline-none text-transparent',
        spacer: 'h-9 py-px box-content',
      }),
    },
    strict: {
      type: Boolean,
      required: false,
      default: true,
    },
    closeOnSelect: {
      type: Boolean,
      required: false,
      default: true,
    },
    autocomplete: {
      type: String,
      required: false,
    },
    groups: {
      type: Boolean,
      required: false,
      default: false,
    },
    groupLabel: {
      type: String,
      required: false,
      default: 'label',
    },
    groupOptions: {
      type: String,
      required: false,
      default: 'options',
    },
    groupHideEmpty: {
      type: Boolean,
      required: false,
      default: false,
    },
    groupSelect: {
      type: Boolean,
      required: false,
      default: true,
    },
    inputType: {
      type: String,
      required: false,
      default: 'text',
    },
  },
  emits: [
    'open',
    'close',
    'select',
    'deselect',
    'input',
    'search-change',
    'tag',
    'update:modelValue',
    'change',
    'clear',
  ],
  setup(props, context) {
    const value = useValue(props, context)
    const pointer = usePointer(props, context)
    const dropdown = useDropdown(props, context)
    const search = useSearch(props, context)

    const data = useData(props, context, {
      iv: value.iv,
    })

    const multiselect = useMultiselect(props, context, {
      input: search.input,
      open: dropdown.open,
      close: dropdown.close,
      clearSearch: search.clearSearch,
    })

    const options = useOptions(props, context, {
      ev: value.ev,
      iv: value.iv,
      search: search.search,
      clearSearch: search.clearSearch,
      update: data.update,
      pointer: pointer.pointer,
      clearPointer: pointer.clearPointer,
      blur: multiselect.blur,
      deactivate: multiselect.deactivate,
    })

    const pointerAction = usePointerAction(props, context, {
      fo: options.fo,
      fg: options.fg,
      handleOptionClick: options.handleOptionClick,
      handleGroupClick: options.handleGroupClick,
      search: search.search,
      pointer: pointer.pointer,
      setPointer: pointer.setPointer,
      clearPointer: pointer.clearPointer,
      multiselect: multiselect.multiselect,
    })

    const keyboard = useKeyboard(props, context, {
      iv: value.iv,
      update: data.update,
      search: search.search,
      setPointer: pointer.setPointer,
      selectPointer: pointerAction.selectPointer,
      backwardPointer: pointerAction.backwardPointer,
      forwardPointer: pointerAction.forwardPointer,
      blur: multiselect.blur,
      fo: options.fo,
    })

    const classes = useClasses(props, context, {
      isOpen: dropdown.isOpen,
      isPointed: pointerAction.isPointed,
      canPointGroups: pointerAction.canPointGroups,
      isSelected: options.isSelected,
      isDisabled: options.isDisabled,
      isActive: multiselect.isActive,
      resolving: options.resolving,
      fo: options.fo,
    })

    return {
      ...value,
      ...dropdown,
      ...multiselect,
      ...pointer,
      ...data,
      ...search,
      ...options,
      ...pointerAction,
      ...keyboard,
      ...classes,
    }
  },
}
</script>
