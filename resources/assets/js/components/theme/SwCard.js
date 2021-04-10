export default {
  classes: {
    container: 'bg-white rounded shadow',
    header: 'px-5 py-4 text-black border-b border-solid border-gray-300',
    body: 'px-4 py-5 sm:p-8',
    footer: 'px-5 py-4 border-t border-solid sm:px-6 border-gray-300',
  },

  variants: {
    customerCard: {
      body: 'px-4 py-5 sm:p-8',
    },
    settingCard: {
      header: 'px-4 pt-5 sm:px-8 sm:pt-8',
      footer: 'px-5 border-t-none py-4 sm:px-6',
    },
  },
}
