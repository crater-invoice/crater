import Guid from 'guid'
import estimateItemStub from './estimate-item'
import taxStub from './tax'

export default function () {
  return {
    id: null,
    customer: null,
    template_name: '',
    tax_per_item: null,
    sales_tax_type: null,
    sales_tax_address_type: null,
    discount_per_item: null,
    estimate_date: '',
    expiry_date: '',
    estimate_number: '',
    customer_id: null,
    sub_total: 0,
    total: 0,
    tax: 0,
    notes: '',
    discount_type: 'fixed',
    discount_val: 0,
    reference_number: null,
    discount: 0,
    items: [
      {
        ...estimateItemStub,
        id: Guid.raw(),
        taxes: [{ ...taxStub, id: Guid.raw() }],
      },
    ],
    taxes: [],
    customFields: [],
    fields: [],
    selectedNote: null,
    selectedCurrency: '',
  }
}
