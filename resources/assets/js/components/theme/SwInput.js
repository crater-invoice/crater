export default {
  variants: {
    gray: {
      container:
        'relative flex items-center w-full border border-solid rounded-md bg-gray-100 hover:border-gray-400',
      baseInput:
        'not-italic font-normal leading-tight text-left outline-none rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full h-8 px-0 py-0 text-xs bg-gray-100',
      rightIconInput:
        'not-italic font-normal leading-tight text-left outline-none min-w-0 rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full pl-3 pr-1 py-2 text-sm bg-gray-100',
      rightIconContainer:
        'flex flex-col justify-center align-middle pr-2 text-gray-400 min-w-0',
      containerFocusIn: 'border-primary-500 ',
      containerFocusOut: 'border-gray-300 focus:border-transparent',
    },

    searchInput: {
      container:
        'relative flex items-center w-full border border-solid rounded-md bg-white',
      baseInput:
        'not-italic font-normal leading-tight text-left outline-none rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full h-8 px-0 py-0 text-xs',
      rightIconInput:
        'not-italic font-normal leading-tight text-left outline-none rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full pl-3 pr-1 py-2 text-sm',
      leftIconInput:
        'not-italic font-normal leading-tight text-left outline-none rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full pl-1 pr-3 py-2 text-sm',
      rightIconContainer: 'flex flex-col justify-center align-middle pr-2',
      leftIconContainer: 'flex flex-col justify-center align-middle',
      containerFocusIn: 'border-primary-500',
      containerFocusOut: 'border-gray-300 focus:border-transparent',
    },
  },
}
