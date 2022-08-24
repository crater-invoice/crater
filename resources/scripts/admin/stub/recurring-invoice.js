import Guid from 'guid'
import recurringInvoiceItemStub from './recurring-invoice-item'
import taxStub from './tax'
import { useI18n } from 'vue-i18n'

export default function () {
  const { t } = useI18n()
  return {
    currency: null,
    customer: null,

    customer_id: null,
    invoice_template_id: 1,
    sub_total: 0,
    total: 0,
    tax: 0,
    notes: '',
    discount_type: 'fixed',
    discount_val: 0,
    discount: 0,
    starts_at: null,
    send_automatically: true,
    status: 'ACTIVE',
    company_id: null,
    next_invoice_at: null,
    next_invoice_date: null,
    frequency: '0 0 * * 0',
    limit_count: null,
    limit_by: 'NONE',
    limit_date: null,
    exchange_rate: null,
    tax_per_item: null,
    discount_per_item: null,
    template_name: null,
    items: [
      {
        ...recurringInvoiceItemStub,
        id: Guid.raw(),
        taxes: [{ ...taxStub, id: Guid.raw() }],
      },
    ],
    taxes: [],
    customFields: [],
    fields: [],
    invoices: [],
    selectedNote: null,
    selectedFrequency: { label: t('recurring_invoices.frequency.every_week'), value: '0 0 * * 0' },
    selectedInvoice: null,
  }
}
