export const invoices = (state) => state.invoices
export const selectAllField = (state) => state.selectAllField
export const getTemplateName = (state) => state.invoiceTemplateName
export const selectedInvoices = (state) => state.selectedInvoices
export const totalInvoices = (state) => state.totalInvoices
export const selectedCustomer = (state) => state.selectedCustomer
export const selectedNote = (state) => state.selectedNote
export const selectedItem = (state) => state.selectedItem
export const getInvoice = (state) => (id) => {
  let invId = parseInt(id)
  return state.invoices.find((invoice) => invoice.id === invId)
}
