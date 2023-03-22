<template>
  <MenuItem v-slot="{ active }" v-bind="$attrs">
    <a
      href="#"
      class="group flex items-center px-4 py-2 text-sm font-normal" :class="[
        active
          ? 'bg-gray-100 text-gray-900 dark:text-white dark:bg-gray-700'
          : 'text-gray-700 dark:text-gray-200',
      ]"
    >
      <slot :active="active" :class="defaultIconClass" />
    </a>
  </MenuItem>
</template>

<script setup>
import { MenuItem } from '@headlessui/vue'
const defaultIconClass
  = 'w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-white'
</script>
