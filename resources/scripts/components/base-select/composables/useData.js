import { toRefs } from 'vue'
import isNullish from './../utils/isNullish'

export default function useData(props, context, dep) {
  const { object, valueProp, mode } = toRefs(props)

  // ============ DEPENDENCIES ============

  const iv = dep.iv

  // =============== METHODS ==============

  const update = (val) => {
    // Setting object(s) as internal value
    iv.value = makeInternal(val)

    // Setting object(s) or plain value as external
    // value based on `option` setting
    const externalVal = makeExternal(val)

    context.emit('change', externalVal)
    context.emit('input', externalVal)
    context.emit('update:modelValue', externalVal)
  }

  // no export
  const makeExternal = (val) => {
    // If external value should be object
    // no transformation is required
    if (object.value) {
      return val
    }

    // No need to transform if empty value
    if (isNullish(val)) {
      return val
    }

    // If external should be plain transform
    // value object to plain values
    return !Array.isArray(val) ? val[valueProp.value] : val.map(v => v[valueProp.value])
  }

  // no export
  const makeInternal = (val) => {
    if (isNullish(val)) {
      return mode.value === 'single' ? {} : []
    }

    return val
  }

  return {
    update,
  }
}
