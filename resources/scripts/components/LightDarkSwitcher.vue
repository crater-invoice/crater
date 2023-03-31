<!-- This example requires Tailwind CSS v2.0+ -->
<script lang="ts" setup>
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { computed, ref } from 'vue'
defineProps({
  showLabel: {
    type: Boolean,
    default: true,
  },
  vertical: {
    type: Boolean,
    default: false,
  },
})

const globalStore = useGlobalStore()

const enabled = computed({
  get: () => globalStore.isDarkModeOn,
  set: (value) => {
    console.log(value)
    if (value) {
      localStorage.theme = 'dark'
      document.documentElement.classList.add('dark')
      document.documentElement.style.setProperty('color-scheme', 'dark')
    } else {
      localStorage.theme = 'light'
      document.documentElement.classList.remove('dark')
      document.documentElement.style.setProperty('color-scheme', 'light')
    }
    globalStore.isDarkModeOn = value
  },
})
</script>

<template>
  <div class="w-full flex justify-center">
    <SwitchGroup
      as="div"
      class="flex items-center"
      :class="vertical ? 'flex-col justify-center' : 'flex-row'"
    >
      <Switch
        v-model="enabled"
        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:ring-offset-gray-700"
        :class="[enabled ? 'bg-primary-600' : 'bg-gray-200']"
      >
        <span class="sr-only">Use setting</span>
        <span
          class="pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
          :class="[enabled ? 'translate-x-5' : 'translate-x-0']"
        >
          <span
            class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
            :class="[
              enabled
                ? 'opacity-0 ease-out duration-100'
                : 'opacity-100 ease-in duration-200',
            ]"
            aria-hidden="true"
          >
            <BaseIcon class="h-3 w-3 text-yellow-500" name="SunIcon" />
          </span>
          <span
            class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity"
            :class="[
              enabled
                ? 'opacity-100 ease-in duration-200'
                : 'opacity-0 ease-out duration-100',
            ]"
            aria-hidden="true"
          >
            <BaseIcon class="h-3 w-3 text-primary-500" name="MoonIcon" />
          </span>
        </span>
      </Switch>
      <SwitchLabel
        v-if="showLabel"
        as="span"
        class="cursor-pointer"
        :class="vertical ? 'px-1 text-center mt-2' : 'ml-3'"
      >
        <span
          v-if="enabled"
          class="text-sm font-medium text-gray-500 dark:text-gray-400"
        >
          Dark Mode
        </span>
        <span v-else class="text-sm font-medium text-gray-500">
          Light Mode
        </span>
      </SwitchLabel>
    </SwitchGroup>
  </div>
</template>
