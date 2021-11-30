import { ref, onMounted, watchEffect } from 'vue'
import { createPopper } from '@popperjs/core'

export function usePopper(options) {
  let activator = ref(null)
  let container = ref(null)
  let popper = ref(null)

  onMounted(() => {
    watchEffect(onInvalidate => {
      if (!container.value) return
      if (!activator.value) return

      let containerEl = container.value.el || container.value
      let activatorEl = activator.value.el || activator.value

      if (!(activatorEl instanceof HTMLElement)) return
      if (!(containerEl instanceof HTMLElement)) return

      popper.value = createPopper(activatorEl, containerEl, options)

      onInvalidate(popper.value.destroy)
    })
  })

  return [activator, container, popper]
}
