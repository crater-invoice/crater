import moment from 'moment'

export default {
  expense_category_id: null,
  expense_date: moment().format('YYYY-MM-DD'),
  amount: 100,
  notes: '',
  attachment_receipt: null,
  customer_id: '',
  currency_id: '',
  payment_method_id: '',
  receiptFiles: [],
  customFields: [],
  fields: [],
  in_use: false,
  selectedCurrency: null
}
