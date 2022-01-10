<template>
  <div class="rounded-md bg-red-50 p-4">
    <div class="flex">
      <div class="shrink-0">
        <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-red-800">
          {{ errorTitle }}
        </h3>
        <div class="mt-2 text-sm text-red-700">
          <ul role="list" class="list-disc pl-5 space-y-1">
            <li v-for="(error, key) in errors" :key="key">
              {{ error }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { XCircleIcon } from '@heroicons/vue/solid'

const props = defineProps({
  errorTitle: {
    type: String,
    default: 'There were some errors with your submission',
  },
  errors: {
    type: Array,
    default: null,
  },
})
</script>
