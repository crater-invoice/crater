

<template>
  <SwitchGroup as="li" class="py-4 flex items-center justify-between">
    <div class="flex flex-col">
      <SwitchLabel
        as="p"
        class="p-0 mb-1 text-sm leading-snug text-black font-medium"
        passive
      >
        {{ title }}
      </SwitchLabel>
      <SwitchDescription class="text-sm text-gray-500">
        {{ description }}
      </SwitchDescription>
    </div>
    <Switch
      :disabled="disabled"
      :model-value="modelValue"
      :class="[
        modelValue ? 'bg-primary-500' : 'bg-gray-200',
        'ml-4 relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500',
      ]"
      @update:modelValue="onUpdate"
    >
      <span
        aria-hidden="true"
        :class="[
          modelValue ? 'translate-x-5' : 'translate-x-0',
          'inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition ease-in-out duration-200',
        ]"
      />
    </Switch>
  </SwitchGroup>
</template>

<script setup>
import {
  Switch,
  SwitchDescription,
  SwitchGroup,
  SwitchLabel,
} from '@headlessui/vue'

defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  modelValue: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

function onUpdate(value) {
  emit('update:modelValue', value)
}
</script>
