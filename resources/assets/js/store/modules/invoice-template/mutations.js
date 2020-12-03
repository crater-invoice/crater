import * as types from './mutation-types'

export default {
  [types.SET_INVOICE_TEMPLATES](state, templates) {
    state.invoiceTemplates = templates
  },
}
