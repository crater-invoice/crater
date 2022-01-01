<template>
  <li class="pr-2 text-sm">
    <router-link
      class="
        m-0
        mr-2
        text-sm
        font-medium
        leading-5
        text-gray-900
        outline-none
        focus:ring-2 focus:ring-offset-2 focus:ring-primary-400
      "
      :to="to"
    >
      {{ title }}
    </router-link>

    <span v-if="!active" class="px-1">/</span>
  </li>
</template>

<script setup>
let name = 'BaseBreadcrumItem'

const props = defineProps({
  title: {
    type: String,
    default: String,
  },
  to: {
    type: String,
    default: '#',
  },
  active: {
    type: Boolean,
    default: false,
    required: false,
  },
})
</script>
