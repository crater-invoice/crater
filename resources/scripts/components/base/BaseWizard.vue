<template>
  <div class="w-full">
    <slot name="nav">
      <WizardNavigation
        :current-step="currentStep"
        :steps="steps"
        @click="(stepIndex) => $emit('click', stepIndex)"
      />
    </slot>

    <div :class="wizardStepsContainerClass">
      <slot />
    </div>
  </div>
</template>

<script setup>
import WizardNavigation from './BaseWizardNavigation.vue'

const props = defineProps({
  wizardStepsContainerClass: {
    type: String,
    default: 'relative flex items-center justify-center',
  },
  currentStep: {
    type: Number,
    default: 0,
  },
  steps: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['click'])
</script>
