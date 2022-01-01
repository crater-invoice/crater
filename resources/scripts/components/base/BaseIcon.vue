<template>
  <component v-if="isLoaded" :is="heroIcons[name]" class="h-5 w-5" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import * as heroIcons from '@heroicons/vue/outline'

const isLoaded = ref(false)

const props = defineProps({
  name: {
    type: String,
  },
})

onMounted(() => {
  isLoaded.value = true
})
</script>
