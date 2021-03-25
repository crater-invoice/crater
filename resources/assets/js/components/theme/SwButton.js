export default {
  variants: {
    grayLight: {
      button:
        'inline-flex items-center justify-center text-gray-400 transition duration-150 ease-in-out border border-transparent focus:outline-none bg-gray-100 border border-gray-200 hover:bg-gray-200 hover:border-gray-400 hover:text-gray-600',
    },
    gray: {
      button:
        'inline-flex items-center justify-center text-gray-400 transition duration-150 ease-in-out border border-transparent focus:outline-none bg-gray-300 border border-gray-200',
    },
    white: {
      button:
        'inline-flex items-center justify-center text-black transition px-2 duration-150 ease-in-out border border-gray-300 border-solid focus:outline-none bg-white',
    },
  },
  sizes: {
    discount: {
      button: 'py-2 px-2 text-sm leading-5 rounded',
      loadingIcon: 'w-4 h-4 -ml-2',
    },
  },
}
