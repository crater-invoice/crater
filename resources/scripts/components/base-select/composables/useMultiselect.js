import { ref, toRefs, computed } from 'vue'

export default function useMultiselect(props, context, dep) {
  const { searchable, disabled } = toRefs(props)

  // ============ DEPENDENCIES ============

  const input = dep.input
  const open = dep.open
  const close = dep.close
  const clearSearch = dep.clearSearch

  // ================ DATA ================

  const multiselect = ref(null)

  const isActive = ref(false)

  // ============== COMPUTED ==============

  const tabindex = computed(() => {
    return searchable.value || disabled.value ? -1 : 0
  })

  // =============== METHODS ==============

  const blur = () => {
    if (searchable.value) {
      input.value.blur()
    }

    multiselect.value.blur()
  }

  const handleFocus = () => {
    if (searchable.value && !disabled.value) {
      input.value.focus()
    }
  }

  const activate = () => {

    if (disabled.value) {
      return
    }

    isActive.value = true

    open()
  }

  const deactivate = () => {
    isActive.value = false

    setTimeout(() => {
      if (!isActive.value) {
        close()
        clearSearch()
      }
    }, 1)
  }

  const handleCaretClick = () => {
    if (isActive.value) {
      deactivate()
      blur()
    } else {
      activate()
    }
  }

  return {
    multiselect,
    tabindex,
    isActive,
    blur,
    handleFocus,
    activate,
    deactivate,
    handleCaretClick,
  }
}
