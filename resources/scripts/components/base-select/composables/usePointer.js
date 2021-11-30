import { ref, toRefs } from 'vue'

export default function usePointer(props, context, dep) {
  const { groupSelect, mode, groups } = toRefs(props)

  // ================ DATA ================

  const pointer = ref(null)

  // =============== METHODS ==============

  const setPointer = (option) => {
    if (option === undefined || (option !== null && option.disabled)) {
      return
    }

    if (groups.value && option && option.group && (mode.value === 'single' || !groupSelect.value)) {
      return
    }

    pointer.value = option
  }

  const clearPointer = () => {
    setPointer(null)
  }

  return {
    pointer,
    setPointer,
    clearPointer,
  }
}
