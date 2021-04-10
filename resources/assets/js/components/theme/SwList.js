export default {
  classes: {
    container: 'pr-4 pl-0 list-none',
    itemContainer:
      'cursor-pointer pb-2 pr-0 text-sm font-medium leading-5 text-gray-500 flex items-center',
    title: '',
    iconContainer: 'mr-3',
    listGroup: {
      container: 'p-0 list-none',
      titleContainer:
        'flex items-center justify-between pb-2 pr-0 text-sm font-medium leading-5 text-gray-500 cursor-pointer',
      title: 'text-sm',
      icon: 'w-5 h-5 leading-4 transform rotate-90',
      itemsContainer: 'pl-4 list-none',
      itemContainer:
        'cursor-pointer pb-2 pr-0 text-sm font-medium leading-5 text-gray-500 flex items-center',
    },
    active: {
      itemContainer:
        'cursor-pointer pb-2 pr-0 text-sm font-medium flex items-center leading-5 text-primary-500',
      listGroup: {
        container: 'p-0 list-none',
        titleContainer:
          'flex items-center justify-between pb-2 pr-0 text-sm font-medium leading-5 text-primary-500 cursor-pointer',
        title: 'text-sm',
        icon: 'w-5 h-5 leading-4 ',
        itemsContainer: 'pl-4 list-none',
        itemContainer:
          'cursor-pointer pb-2 pr-0 text-sm font-medium leading-5 text-gray-500 flex items-center',
      },
    },
  },
}
