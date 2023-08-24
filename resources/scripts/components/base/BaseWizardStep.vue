<template>
  <div :class="stepContainerClass">
    <div v-if="title || description">
      <p v-if="title" :class="stepTitleClass">
        {{ title }}
      </p>
      <p v-if="description" :class="stepDescriptionClass">
        {{ description }}
      </p>
    </div>
    <slot />
    <BaseDarkHighlight class="z-[-1]"/>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: null,
  },
  description: {
    type: String,
    default: null,
  },
  stepContainerClass: {
    type: String,
    default:
      'w-full p-8 mb-8 bg-white border border-gray-200 border-solid rounded dark:from-gray-700/70 dark:to-gray-800/70 dark:bg-transparent dark:backdrop-blur-xl dark:shadow-glass dark:border dark:border-white/10',
  },
  stepTitleClass: {
    type: String,
    default: 'text-2xl not-italic font-semibold leading-7 text-black dark:text-white',
  },
  stepDescriptionClass: {
    type: String,
    default:
      'w-full mt-2.5 mb-8 text-sm not-italic leading-snug text-gray-500 dark:text-gray-300 lg:w-7/12 md:w-7/12 sm:w-7/12',
  },
})
</script>
