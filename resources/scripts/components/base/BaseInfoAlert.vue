<template>
  <div class="rounded-md bg-yellow-50 p-4 relative">
    <BaseIcon
      name="XIcon"
      class="h-5 w-5 text-yellow-500 absolute right-4 cursor-pointer"
      @click="$emit('hide')"
    />
    <div class="flex flex-col">
      <div class="flex">
        <div class="shrink-0">
          <BaseIcon
            name="ExclamationIcon"
            class="h-5 w-5 text-yellow-400"
            aria-hidden="true"
          />
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-yellow-800">
            {{ title }}
          </h3>
          <div class="mt-2 text-sm text-yellow-700">
            <ul role="list" class="list-disc pl-5 space-y-1">
              <li v-for="(list, key) in lists" :key="key">
                {{ list }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div v-if="actions.length" class="mt-4 ml-3">
        <div class="-mx-2 -my-1.5 flex flex-row-reverse">
          <button
            v-for="(action, i) in actions"
            :key="i"
            type="button"
            class="
              bg-yellow-50
              px-2
              py-1.5
              rounded-md
              text-sm
              font-medium
              text-yellow-800
              hover:bg-yellow-100
              focus:outline-none
              focus:ring-2
              focus:ring-offset-2
              focus:ring-offset-yellow-50
              focus:ring-yellow-600
              mr-3
            "
            @click="$emit(`${action}`)"
          >
            {{ action }}
          </button>
          <!-- <button
            v-if="actions[1]"
            type="button"
            class="
              ml-3
              bg-yellow-50
              px-2
              py-1.5
              rounded-md
              text-sm
              font-medium
              text-yellow-800
              hover:bg-yellow-100
              focus:outline-none
              focus:ring-2
              focus:ring-offset-2
              focus:ring-offset-yellow-50
              focus:ring-yellow-600
            "
            @click="$emit('action2')"
          >
            {{ actions[1] }}
          </button> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { XCircleIcon } from '@heroicons/vue/solid'

const emits = defineEmits(['hide'])
const props = defineProps({
  title: {
    type: String,
    default: 'There were some errors with your submission',
  },
  lists: {
    type: Array,
    default: null,
  },
  actions: {
    type: Array,
    default: () => ['Dismiss'],
  },
})
</script>
