<template>
   <button
    type="button"
    class="
      bg-white
      p-1.5
      rounded-full
      text-gray-400
      hover:text-gray-500
      focus:outline-none
      focus:ring-2
      focus:ring-primary-500
      dark:bg-transparent
      dark:focus:ring-gray-500
    "
    @click="onChange"
  >

    <BaseIcon v-if="!globalStore.isDarkModeOn" class="h-5 w-5 text-yellow-500" name="SunIcon" />
    <BaseIcon v-else class="h-5 w-5 text-primary-400" name="MoonIcon" />
  </button>
</template>
<script setup>
import { computed } from 'vue'
import { useGlobalStore } from '@/scripts/customer/stores/global'

const globalStore = useGlobalStore()

const enabled = computed(
  ()=>
    localStorage.getItem('theme') === 'dark' ||
    document.documentElement.classList.contains('dark')
)

globalStore.isDarkModeOn = enabled.value

function onChange() {
  globalStore.isDarkModeOn = !globalStore.isDarkModeOn

  if (globalStore.isDarkModeOn) {
    localStorage.theme = 'dark'
    document.documentElement.classList.add('dark')
    document.documentElement.style.setProperty('color-scheme', 'dark')
  } else {
    localStorage.theme = 'light'
    document.documentElement.classList.remove('dark')
    document.documentElement.style.setProperty('color-scheme', 'light')

  }
}
</script>

