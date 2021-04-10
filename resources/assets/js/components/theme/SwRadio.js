export default {
  classes: {
    container: 'radio',
    label: 'cursor-pointer',
    input:
      'cursor-pointer flex-shrink-0 inline-block text-primary-500 align-middle bg-white border border-gray-300 rounded-full outline-none appearance-none select-none transition duration-200 ease-in-out',
  },
  variants: {
    success: {
      input:
        'cursor-pointer flex-shrink-0 inline-block text-success align-middle bg-white border border-gray-300 rounded-full outline-none appearance-none select-none transition duration-200 ease-in-out',
      label: 'cursor-pointer',
    },
    danger: {
      input:
        'cursor-pointer flex-shrink-0 inline-block text-danger align-middle bg-white border border-gray-300 rounded-full outline-none appearance-none select-none transition duration-200 ease-in-out',
      label: 'cursor-pointer',
    },
  },
  sizes: {
    sm: {
      input: 'w-4 h-4',
      label: 'ml-2',
    },
    default: {
      input: 'w-6 h-6',
      label: 'ml-3 text-lg',
    },
    lg: {
      input: 'w-8 h-8',
      label: 'ml-4 text-xl',
    },
  },
}
