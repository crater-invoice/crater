import addressStub from '@/scripts/stub/address.js'

export default function () {
  return {
    name: '',
    contact_name: '',
    email: '',
    phone: null,
    currency_id: null,
    website: null,
    billing: { ...addressStub },
    shipping: { ...addressStub },
    customFields: [],
    fields: []
  }
}
