import { computed, toRefs, ref } from 'vue'

export default function useValue(props, context) {
  const { value, modelValue, mode, valueProp } = toRefs(props)

  // ================ DATA ================

  // internalValue
  const iv = ref(mode.value !== 'single' ? [] : {})

  // ============== COMPUTED ==============

  /* istanbul ignore next */
  // externalValue
  const ev = context.expose !== undefined ? modelValue : value

  const plainValue = computed(() => {
    return mode.value === 'single' ? iv.value[valueProp.value] : iv.value.map(v => v[valueProp.value])
  })

  const textValue = computed(() => {
    return mode.value !== 'single' ? iv.value.map(v => v[valueProp.value]).join(',') : iv.value[valueProp.value]
  })

  return {
    iv,
    internalValue: iv,
    ev,
    externalValue: ev,
    textValue,
    plainValue,
  }
}
