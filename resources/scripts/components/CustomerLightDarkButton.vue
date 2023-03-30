<template>
   <button
    type="button"
    class="
      flex
      h-8
      w-8
      items-center
      justify-center
      rounded-md
      transition
      hover:bg-zinc-900/5
      dark:hover:bg-white/5
    "
    @click="onChange"
  >
    <BaseIcon v-if="!globalStore.isDarkModeOn" class="h-5 w-5 text-black" name="SunIcon" />
    <BaseIcon v-else class="h-5 w-5 text-white" name="MoonIcon" />
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

