<template>
  <div
    :class="containerClass"
    class="relative after:bg-gray-200 dark:after:bg-gray-700 after:absolute after:transform after:top-1/2 after:-translate-y-1/2 after:h-2 after:w-full"
  >
    <a
      v-for="(number, index) in steps"
      :key="index"
      :class="stepStyle(number)"
      class="z-10"
      href="#"
      @click.prevent="$emit('click', index)"
    >
      <svg
        v-if="currentStep > number"
        :class="iconClass"
        fill="currentColor"
        viewBox="0 0 20 20"
        @click="$emit('click', index)"
      >
        <path
          fill-rule="evenodd"
          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
          clip-rule="evenodd"
        ></path>
      </svg>
    </a>
  </div>
</template>

<script>
export default {
  props: {
    currentStep: {
      type: Number,
      default: null,
    },
    steps: {
      type: Number,
      default: null,
    },
    containerClass: {
      type: String,
      default: 'flex justify-between w-full my-10 max-w-xl mx-auto',
    },
    progress: {
      type: String,
      default: 'rounded-full float-left w-6 h-6 border-4 cursor-pointer',
    },
    currentStepClass: {
      type: String,
      default: 'bg-white border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:border-primary-600',
    },
    nextStepClass: {
      type: String,
      default: 'border-gray-200 bg-white dark:bg-gray-600 dark:border-gray-500',
    },
    previousStepClass: {
      type: String,
      default:
        'bg-primary-500 dark:bg-primary-600 border-primary-500 flex justify-center items-center dark:border-primary-600',
    },
    iconClass: {
      type: String,
      default:
        'flex items-center justify-center w-full h-full text-sm font-black text-center text-white dark:text-gray-400',
    },
  },

  emits: ['click'],

  setup(props) {
    function stepStyle(number) {
      if (props.currentStep === number) {
        return [props.currentStepClass, props.progress]
      }
      if (props.currentStep > number) {
        return [props.previousStepClass, props.progress]
      }
      if (props.currentStep < number) {
        return [props.nextStepClass, props.progress]
      }
      return [props.progress]
    }

    return {
      stepStyle,
    }
  },
}
</script>
