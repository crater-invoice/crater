import { ref, toRefs, computed, watch, nextTick } from 'vue'
import normalize from './../utils/normalize'
import isObject from './../utils/isObject'
import isNullish from './../utils/isNullish'
import arraysEqual from './../utils/arraysEqual'

export default function useOptions(props, context, dep) {
  const {
    options, mode, trackBy, limit, hideSelected, createTag, label,
    appendNewTag, multipleLabel, object, loading, delay, resolveOnLoad,
    minChars, filterResults, clearOnSearch, clearOnSelect, valueProp,
    canDeselect, max, strict, closeOnSelect, groups: groupped, groupLabel,
    groupOptions, groupHideEmpty, groupSelect,
  } = toRefs(props)

  // ============ DEPENDENCIES ============

  const iv = dep.iv
  const ev = dep.ev
  const search = dep.search
  const clearSearch = dep.clearSearch
  const update = dep.update
  const pointer = dep.pointer
  const clearPointer = dep.clearPointer
  const blur = dep.blur
  const deactivate = dep.deactivate

  // ================ DATA ================

  // no export
  // appendedOptions
  const ap = ref([])

  // no export
  // resolvedOptions
  const ro = ref([])

  const resolving = ref(false)

  // ============== COMPUTED ==============

  // no export
  // extendedOptions
  const eo = computed(() => {
    if (groupped.value) {
      let groups = ro.value || /* istanbul ignore next */[]

      let eo = []

      groups.forEach((group) => {
        optionsToArray(group[groupOptions.value]).forEach((option) => {
          eo.push(Object.assign({}, option, group.disabled ? { disabled: true } : {}))
        })
      })

      return eo
    } else {
      let eo = optionsToArray(ro.value || [])

      if (ap.value.length) {
        eo = eo.concat(ap.value)
      }

      return eo
    }
  })

  const fg = computed(() => {
    if (!groupped.value) {
      return []
    }

    return filterGroups((ro.value || /* istanbul ignore next */[]).map((group) => {
      const arrayOptions = optionsToArray(group[groupOptions.value])

      return {
        ...group,
        group: true,
        [groupOptions.value]: filterOptions(arrayOptions, false).map(o => Object.assign({}, o, group.disabled ? { disabled: true } : {})),
        __VISIBLE__: filterOptions(arrayOptions).map(o => Object.assign({}, o, group.disabled ? { disabled: true } : {})),
      }
      // Difference between __VISIBLE__ and {groupOptions}: visible does not contain selected options when hideSelected=true
    }))
  })

  // filteredOptions
  const fo = computed(() => {
    let options = eo.value

    if (createdTag.value.length) {
      options = createdTag.value.concat(options)
    }

    options = filterOptions(options)

    if (limit.value > 0) {
      options = options.slice(0, limit.value)
    }

    return options
  })

  const hasSelected = computed(() => {
    switch (mode.value) {
      case 'single':
        return !isNullish(iv.value[valueProp.value])

      case 'multiple':
      case 'tags':
        return !isNullish(iv.value) && iv.value.length > 0
    }
  })

  const multipleLabelText = computed(() => {
    return multipleLabel !== undefined && multipleLabel.value !== undefined
      ? multipleLabel.value(iv.value)
      : (iv.value && iv.value.length > 1 ? `${iv.value.length} options selected` : `1 option selected`)
  })

  const noOptions = computed(() => {
    return !eo.value.length && !resolving.value && !createdTag.value.length
  })


  const noResults = computed(() => {
    return eo.value.length > 0 && fo.value.length == 0 && ((search.value && groupped.value) || !groupped.value)
  })

  // no export
  const createdTag = computed(() => {
    if (createTag.value === false || !search.value) {
      return []
    }

    return getOptionByTrackBy(search.value) !== -1 ? [] : [{
      [valueProp.value]: search.value,
      [label.value]: search.value,
      [trackBy.value]: search.value,
    }]
  })

  // no export
  const nullValue = computed(() => {
    switch (mode.value) {
      case 'single':
        return null

      case 'multiple':
      case 'tags':
        return []
    }
  })

  const busy = computed(() => {
    return loading.value || resolving.value
  })

  // =============== METHODS ==============

  /**
   * @param {array|object|string|number} option
   */
  const select = (option) => {
    if (typeof option !== 'object') {
      option = getOption(option)
    }

    switch (mode.value) {
      case 'single':
        update(option)
        break

      case 'multiple':
      case 'tags':
        update((iv.value).concat(option))
        break
    }

    context.emit('select', finalValue(option), option)
  }

  const deselect = (option) => {
    if (typeof option !== 'object') {
      option = getOption(option)
    }

    switch (mode.value) {
      case 'single':
        clear()
        break

      case 'tags':
      case 'multiple':
        update(Array.isArray(option)
          ? iv.value.filter(v => option.map(o => o[valueProp.value]).indexOf(v[valueProp.value]) === -1)
          : iv.value.filter(v => v[valueProp.value] != option[valueProp.value]))
        break
    }

    context.emit('deselect', finalValue(option), option)
  }

  // no export
  const finalValue = (option) => {
    return object.value ? option : option[valueProp.value]
  }

  const remove = (option) => {
    deselect(option)
  }

  const handleTagRemove = (option, e) => {
    if (e.button !== 0) {
      e.preventDefault()
      return
    }

    remove(option)
  }

  const clear = () => {
    context.emit('clear')
    update(nullValue.value)
  }

  const isSelected = (option) => {
    if (option.group !== undefined) {
      return mode.value === 'single' ? false : areAllSelected(option[groupOptions.value]) && option[groupOptions.value].length
    }

    switch (mode.value) {
      case 'single':
        return !isNullish(iv.value) && iv.value[valueProp.value] == option[valueProp.value]

      case 'tags':
      case 'multiple':
        return !isNullish(iv.value) && iv.value.map(o => o[valueProp.value]).indexOf(option[valueProp.value]) !== -1
    }
  }

  const isDisabled = (option) => {
    return option.disabled === true
  }

  const isMax = () => {
    if (max === undefined || max.value === -1 || (!hasSelected.value && max.value > 0)) {
      return false
    }

    return iv.value.length >= max.value
  }

  const handleOptionClick = (option) => {
    if (isDisabled(option)) {
      return
    }

    switch (mode.value) {
      case 'single':
        if (isSelected(option)) {
          if (canDeselect.value) {
            deselect(option)
          }
          return
        }

        blur()
        select(option)
        break

      case 'multiple':
        if (isSelected(option)) {
          deselect(option)
          return
        }

        if (isMax()) {
          return
        }

        select(option)

        if (clearOnSelect.value) {
          clearSearch()
        }

        if (hideSelected.value) {
          clearPointer()
        }

        // If we need to close the dropdown on select we also need
        // to blur the input, otherwise further searches will not
        // display any options
        if (closeOnSelect.value) {
          blur()
        }
        break

      case 'tags':
        if (isSelected(option)) {
          deselect(option)
          return
        }

        if (isMax()) {
          return
        }

        if (getOption(option[valueProp.value]) === undefined && createTag.value) {
          context.emit('tag', option[valueProp.value])

          if (appendNewTag.value) {
            appendOption(option)
          }

          clearSearch()
        }

        if (clearOnSelect.value) {
          clearSearch()
        }

        select(option)

        if (hideSelected.value) {
          clearPointer()
        }

        // If we need to close the dropdown on select we also need
        // to blur the input, otherwise further searches will not
        // display any options
        if (closeOnSelect.value) {
          blur()
        }
        break
    }

    if (closeOnSelect.value) {
      deactivate()
    }
  }

  const handleGroupClick = (group) => {
    if (isDisabled(group) || mode.value === 'single' || !groupSelect.value) {
      return
    }

    switch (mode.value) {
      case 'multiple':
      case 'tags':
        if (areAllEnabledSelected(group[groupOptions.value])) {
          deselect(group[groupOptions.value])
        } else {
          select(group[groupOptions.value]
            .filter(o => iv.value.map(v => v[valueProp.value]).indexOf(o[valueProp.value]) === -1)
            .filter(o => !o.disabled)
            .filter((o, k) => iv.value.length + 1 + k <= max.value || max.value === -1)
          )
        }
        break
    }

    if (closeOnSelect.value) {
      deactivate()
    }
  }

  // no export
  const areAllEnabledSelected = (options) => {
    return options.find(o => !isSelected(o) && !o.disabled) === undefined
  }

  // no export
  const areAllSelected = (options) => {
    return options.find(o => !isSelected(o)) === undefined
  }

  const getOption = (val) => {
    return eo.value[eo.value.map(o => String(o[valueProp.value])).indexOf(String(val))]
  }

  // no export
  const getOptionByTrackBy = (val, norm = true) => {
    return eo.value.map(o => o[trackBy.value]).indexOf(val)
  }

  // no export
  const shouldHideOption = (option) => {
    return ['tags', 'multiple'].indexOf(mode.value) !== -1 && hideSelected.value && isSelected(option)
  }

  // no export
  const appendOption = (option) => {
    ap.value.push(option)
  }

  // no export
  const filterGroups = (groups) => {
    // If the search has value we need to filter among
    // he ones that are visible to the user to avoid
    // displaying groups which technically have options
    // based on search but that option is already selected.
    return groupHideEmpty.value
      ? groups.filter(g => search.value
        ? g.__VISIBLE__.length
        : g[groupOptions.value].length
      )
      : groups.filter(g => search.value ? g.__VISIBLE__.length : true)
  }

  // no export
  const filterOptions = (options, excludeHideSelected = true) => {
    let fo = options

    if (search.value && filterResults.value) {
      fo = fo.filter((option) => {
        return normalize(option[trackBy.value], strict.value).indexOf(normalize(search.value, strict.value)) !== -1
      })
    }

    if (hideSelected.value && excludeHideSelected) {
      fo = fo.filter((option) => !shouldHideOption(option))
    }

    return fo
  }

  // no export
  const optionsToArray = (options) => {
    let uo = options

    // Transforming an object to an array of objects
    if (isObject(uo)) {
      uo = Object.keys(uo).map((key) => {
        let val = uo[key]

        return { [valueProp.value]: key, [trackBy.value]: val, [label.value]: val }
      })
    }

    // Transforming an plain arrays to an array of objects
    uo = uo.map((val) => {
      return typeof val === 'object' ? val : { [valueProp.value]: val, [trackBy.value]: val, [label.value]: val }
    })

    return uo
  }

  // no export
  const initInternalValue = () => {
    if (!isNullish(ev.value)) {
      iv.value = makeInternal(ev.value)
    }
  }

  const resolveOptions = (callback) => {
    resolving.value = true

    options.value(search.value).then((response) => {
      ro.value = response

      if (typeof callback == 'function') {
        callback(response)
      }

      resolving.value = false
    })
  }

  // no export
  const refreshLabels = () => {
    if (!hasSelected.value) {
      return
    }

    if (mode.value === 'single') {
      let newLabel = getOption(iv.value[valueProp.value])[label.value]

      iv.value[label.value] = newLabel

      if (object.value) {
        ev.value[label.value] = newLabel
      }
    } else {
      iv.value.forEach((val, i) => {
        let newLabel = getOption(iv.value[i][valueProp.value])[label.value]

        iv.value[i][label.value] = newLabel

        if (object.value) {
          ev.value[i][label.value] = newLabel
        }
      })
    }
  }

  const refreshOptions = (callback) => {
    resolveOptions(callback)
  }

  // no export
  const makeInternal = (val) => {
    if (isNullish(val)) {
      return mode.value === 'single' ? {} : []
    }

    if (object.value) {
      return val
    }

    // If external should be plain transform
    // value object to plain values
    return mode.value === 'single' ? getOption(val) || {} : val.filter(v => !!getOption(v)).map(v => getOption(v))
  }

  // ================ HOOKS ===============

  if (mode.value !== 'single' && !isNullish(ev.value) && !Array.isArray(ev.value)) {
    throw new Error(`v-model must be an array when using "${mode.value}" mode`)
  }

  if (options && typeof options.value == 'function') {
    if (resolveOnLoad.value) {
      resolveOptions(initInternalValue)
    } else if (object.value == true) {
      initInternalValue()
    }
  }
  else {
    ro.value = options.value

    initInternalValue()
  }

  // ============== WATCHERS ==============

  if (delay.value > -1) {
    watch(search, (query) => {
      if (query.length < minChars.value) {
        return
      }

      resolving.value = true

      if (clearOnSearch.value) {
        ro.value = []
      }
      setTimeout(() => {
        if (query != search.value) {
          return
        }

        options.value(search.value).then((response) => {
          if (query == search.value) {
            ro.value = response
            pointer.value = fo.value.filter(o => o.disabled !== true)[0] || null
            resolving.value = false
          }
        })
      }, delay.value)

    }, { flush: 'sync' })
  }

  watch(ev, (newValue) => {
    if (isNullish(newValue)) {
      iv.value = makeInternal(newValue)
      return
    }

    switch (mode.value) {
      case 'single':
        if (object.value ? newValue[valueProp.value] != iv.value[valueProp.value] : newValue != iv.value[valueProp.value]) {
          iv.value = makeInternal(newValue)
        }
        break

      case 'multiple':
      case 'tags':
        if (!arraysEqual(object.value ? newValue.map(o => o[valueProp.value]) : newValue, iv.value.map(o => o[valueProp.value]))) {
          iv.value = makeInternal(newValue)
        }
        break
    }
  }, { deep: true })

  if (typeof props.options !== 'function') {
    watch(options, (n, o) => {
      ro.value = props.options

      if (!Object.keys(iv.value).length) {
        initInternalValue()
      }

      refreshLabels()
    })
  }

  return {
    fo,
    filteredOptions: fo,
    hasSelected,
    multipleLabelText,
    eo,
    extendedOptions: eo,
    fg,
    filteredGroups: fg,
    noOptions,
    noResults,
    resolving,
    busy,
    select,
    deselect,
    remove,
    clear,
    isSelected,
    isDisabled,
    isMax,
    getOption,
    handleOptionClick,
    handleGroupClick,
    handleTagRemove,
    refreshOptions,
    resolveOptions,
  }
}
