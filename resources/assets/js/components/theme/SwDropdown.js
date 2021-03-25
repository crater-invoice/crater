export default {
  classes: {
    container: 'relative sw-dropdown',
    activator: 'cursor-pointer',
    divider: 'border-t border-solid border-gray-200 my-2 mx-0 overflow-hidden',
    itemContainer:
      'z-10 p-2 max-h-60 text-base text-left list-none rounded border-0 shadow bg-white text-black overflow-auto sw-scroll',
    item:
      'flex p-2 text-sm font-light text-left text-black bg-transparent rounded cursor-pointer none hover:bg-gray-200 whitespace-nowrap',
    itemIcon: 'w-5 h-5 mr-3 text-secondary',
  },
  variants: {
    searchDropdown: {
      activator: 'cursor-pointer',
      container: 'relative',
      divider:
        'border-t border-solid border-gray-200 my-2 mx-0 overflow-hidden',
      item:
        'flex p-0 text-sm font-light text-left text-black bg-transparent rounded cursor-pointer none hover:bg-gray-200 whitespace-nowrap',
      itemContainer:
        'z-10 p-2 text-base text-left list-none rounded border-0 shadow bg-white text-black',
      itemIcon: 'w-5 h-5 mr-3 text-secondary',
    },
  },
}
