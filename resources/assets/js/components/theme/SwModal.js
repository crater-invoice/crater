export default {
  classes: {
    overlayContainer:
      'fixed z-50 inset-0 overflow-y-auto sw-scroll bg-opacity-25 bg-gray-700 flex justify-center min-h-screen items-center text-center sm:p-0',
    centering: 'hidden sm:inline-block sm:align-middle sm:h-screen',
    base:
      'inline-block border-t-8 border-solid border-primary-500 w-full align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full m-6 sm:m-0',
    header:
      'py-4 px-6 h-16 text-black font-medium text-lg border-b border-solid border-gray-light flex justify-between items-center',
    body: 'modal body text-sm',
    footer:
      'border-t border-solid border-gray-light py-4 px-6 flex justify-end',
  },
  variants: {
    lg: {
      base:
        'inline-block border-t-8 border-solid border-primary-500 w-full align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full m-6 sm:m-0',
    },
  },
}
