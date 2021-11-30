import { computed, toRefs } from 'vue'

export default function useClasses(props, context, dependencies) {
  const refs = toRefs(props)
  const { disabled, openDirection, showOptions, invalid } = refs

  // ============ DEPENDENCIES ============

  const isOpen = dependencies.isOpen
  const isPointed = dependencies.isPointed
  const isSelected = dependencies.isSelected
  const isDisabled = dependencies.isDisabled
  const isActive = dependencies.isActive
  const canPointGroups = dependencies.canPointGroups
  const resolving = dependencies.resolving
  const fo = dependencies.fo
  const isInvalid = invalid

  const classes = {
    container: 'multiselect',
    containerDisabled: 'is-disabled',
    containerOpen: 'is-open',
    containerOpenTop: 'is-open-top',
    containerActive: 'is-active',
    containerInvalid: 'is-invalid',
    containerInvalidActive: 'is-invalid-active',
    singleLabel: 'multiselect-single-label',
    multipleLabel: 'multiselect-multiple-label',
    search: 'multiselect-search',
    tags: 'multiselect-tags',
    tag: 'multiselect-tag',
    tagDisabled: 'is-disabled',
    tagRemove: 'multiselect-tag-remove',
    tagRemoveIcon: 'multiselect-tag-remove-icon',
    tagsSearchWrapper: 'multiselect-tags-search-wrapper',
    tagsSearch: 'multiselect-tags-search',
    tagsSearchCopy: 'multiselect-tags-search-copy',
    placeholder: 'multiselect-placeholder',
    caret: 'multiselect-caret',
    caretOpen: 'is-open',
    clear: 'multiselect-clear',
    clearIcon: 'multiselect-clear-icon',
    spinner: 'multiselect-spinner',
    dropdown: 'multiselect-dropdown',
    dropdownTop: 'is-top',
    dropdownHidden: 'is-hidden',
    options: 'multiselect-options',
    optionsTop: 'is-top',
    group: 'multiselect-group',
    groupLabel: 'multiselect-group-label',
    groupLabelPointable: 'is-pointable',
    groupLabelPointed: 'is-pointed',
    groupLabelSelected: 'is-selected',
    groupLabelDisabled: 'is-disabled',
    groupLabelSelectedPointed: 'is-selected is-pointed',
    groupLabelSelectedDisabled: 'is-selected is-disabled',
    groupOptions: 'multiselect-group-options',
    option: 'multiselect-option',
    optionPointed: 'is-pointed',
    optionSelected: 'is-selected',
    optionDisabled: 'is-disabled',
    optionSelectedPointed: 'is-selected is-pointed',
    optionSelectedDisabled: 'is-selected is-disabled',
    noOptions: 'multiselect-no-options',
    noResults: 'multiselect-no-results',
    fakeInput: 'multiselect-fake-input',
    spacer: 'multiselect-spacer',
    ...refs.classes.value,
  }

  // ============== COMPUTED ==============

  const showDropdown = computed(() => {
    return !!(
      isOpen.value &&
      showOptions.value &&
      (!resolving.value || (resolving.value && fo.value.length))
    )
  })

  const classList = computed(() => {
    return {
      container: [classes.container]
        .concat(disabled.value ? classes.containerDisabled : [])
        .concat(
          showDropdown.value && openDirection.value === 'top'
            ? classes.containerOpenTop
            : []
        )
        .concat(
          showDropdown.value && openDirection.value !== 'top'
            ? classes.containerOpen
            : []
        )
        .concat(isActive.value ? classes.containerActive : [])
        .concat(invalid.value ? classes.containerInvalid : []),
      spacer: classes.spacer,
      singleLabel: classes.singleLabel,
      multipleLabel: classes.multipleLabel,
      search: classes.search,
      tags: classes.tags,
      tag: [classes.tag].concat(disabled.value ? classes.tagDisabled : []),
      tagRemove: classes.tagRemove,
      tagRemoveIcon: classes.tagRemoveIcon,
      tagsSearchWrapper: classes.tagsSearchWrapper,
      tagsSearch: classes.tagsSearch,
      tagsSearchCopy: classes.tagsSearchCopy,
      placeholder: classes.placeholder,
      caret: [classes.caret].concat(isOpen.value ? classes.caretOpen : []),
      clear: classes.clear,
      clearIcon: classes.clearIcon,
      spinner: classes.spinner,
      dropdown: [classes.dropdown]
        .concat(openDirection.value === 'top' ? classes.dropdownTop : [])
        .concat(
          !isOpen.value || !showOptions.value || !showDropdown.value
            ? classes.dropdownHidden
            : []
        ),
      options: [classes.options].concat(
        openDirection.value === 'top' ? classes.optionsTop : []
      ),
      group: classes.group,
      groupLabel: (g) => {
        let groupLabel = [classes.groupLabel]

        if (isPointed(g)) {
          groupLabel.push(
            isSelected(g)
              ? classes.groupLabelSelectedPointed
              : classes.groupLabelPointed
          )
        } else if (isSelected(g) && canPointGroups.value) {
          groupLabel.push(
            isDisabled(g)
              ? classes.groupLabelSelectedDisabled
              : classes.groupLabelSelected
          )
        } else if (isDisabled(g)) {
          groupLabel.push(classes.groupLabelDisabled)
        }

        if (canPointGroups.value) {
          groupLabel.push(classes.groupLabelPointable)
        }

        return groupLabel
      },
      groupOptions: classes.groupOptions,
      option: (o, g) => {
        let option = [classes.option]

        if (isPointed(o)) {
          option.push(
            isSelected(o)
              ? classes.optionSelectedPointed
              : classes.optionPointed
          )
        } else if (isSelected(o)) {
          option.push(
            isDisabled(o)
              ? classes.optionSelectedDisabled
              : classes.optionSelected
          )
        } else if (isDisabled(o) || (g && isDisabled(g))) {
          option.push(classes.optionDisabled)
        }

        return option
      },
      noOptions: classes.noOptions,
      noResults: classes.noResults,
      fakeInput: classes.fakeInput,
    }
  })

  return {
    classList,
    showDropdown,
  }
}
