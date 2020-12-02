export const customers = (state) => state.customers
export const selectAllField = (state) => state.selectAllField
export const selectedCustomers = (state) => state.selectedCustomers
export const totalCustomers = (state) => state.totalCustomers
export const getCustomer = (state) => (id) => {
  let CstId = parseInt(id)
  return state.customers.find((customer) => customer.id === CstId)
}
export const selectedViewCustomer = (state) => state.selectedViewCustomer
