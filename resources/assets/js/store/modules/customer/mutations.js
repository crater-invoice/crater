import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_CUSTOMERS](state, customers) {
    state.customers = customers
  },

  [types.SET_TOTAL_CUSTOMERS](state, totalCustomers) {
    state.totalCustomers = totalCustomers
  },

  [types.ADD_CUSTOMER](state, data) {
    state.customers.push(data.customer)
  },

  [types.UPDATE_CUSTOMER](state, data) {
    let pos = state.customers.findIndex(
      (customer) => customer.id === data.customer.id
    )

    state.customers[pos] = data.customer
  },

  [types.DELETE_CUSTOMER](state, id) {
    let index = state.customers.findIndex((customer) => customer.id === id)
    state.customers.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_CUSTOMERS](state, selectedCustomers) {
    selectedCustomers.forEach((customer) => {
      let index = state.customers.findIndex((_cust) => _cust.id === customer.id)
      state.customers.splice(index, 1)
    })

    state.selectedCustomers = []
  },

  [types.SET_SELECTED_CUSTOMERS](state, data) {
    state.selectedCustomers = data
  },

  [types.RESET_SELECTED_CUSTOMER](state, data) {
    state.selectedCustomer = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_SELECTED_VIEW_CUSTOMER](state, selectedViewCustomer) {
    state.selectedViewCustomer = selectedViewCustomer
  },
}
