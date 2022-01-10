<template>
  <TaxationAddressModal @addTax="addSalesTax" />
</template>

<script setup>
import {} from '@/scripts/admin/stores/recurring-invoice'
import { useModalStore } from '@/scripts/stores/modal'
import { computed, watch, onMounted, ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useTaxTypeStore } from '@/scripts/admin/stores/tax-type'
import TaxationAddressModal from '@/scripts/admin/components/modal-components/TaxationAddressModal.vue'
const SALES_TAX_US = 'Sales Tax'
const SALES_TAX_MODULE = 'MODULE'
const modalStore = useModalStore()
const companyStore = useCompanyStore()
const taxTypeStore = useTaxTypeStore()
const { t } = useI18n()
import { isEqual, pick } from 'lodash'

const fetchingTax = ref(false)

const props = defineProps({
  isEdit: {
    type: Boolean,
    default: null,
  },
  type: {
    type: String,
    default: null,
  },
  customer: {
    type: [Object],
    default: null,
  },
  store: {
    type: Object,
    default: null,
  },
  storeProp: {
    type: String,
    default: null,
  },
})

const isSalesTaxTypeBilling = computed(() => {
  return props.isEdit
    ? props.store[props.storeProp].sales_tax_address_type === 'billing'
    : companyStore.selectedCompanySettings.sales_tax_address_type === 'billing'
})

const salesTaxEnabled = computed(() => {
  return companyStore.selectedCompanySettings.sales_tax_us_enabled === 'YES'
})

const salesTaxCustomerLevel = computed(() => {
  return props.isEdit
    ? props.store[props.storeProp].sales_tax_type === 'customer_level'
    : companyStore.selectedCompanySettings.sales_tax_type === 'customer_level'
})

const salesTaxCompanyLevel = computed(() => {
  return props.isEdit
    ? props.store[props.storeProp].sales_tax_type === 'company_level'
    : companyStore.selectedCompanySettings.sales_tax_type === 'company_level'
})

const addressData = computed(() => {
  if (salesTaxCustomerLevel.value && isAddressAvailable.value) {
    let address = isSalesTaxTypeBilling.value
      ? props.customer.billing
      : props.customer.shipping
    return {
      address: pick(address, ['address_street_1', 'city', 'state', 'zip']),
      customer_id: props.customer.id,
    }
  } else if (salesTaxCompanyLevel.value && isAddressAvailable.value) {
    return {
      address: pick(address, ['address_street_1', 'city', 'state', 'zip']),
    }
  }
})

const isAddressAvailable = computed(() => {
  if (salesTaxCustomerLevel.value) {
    let address = isSalesTaxTypeBilling.value
      ? props.customer?.billing
      : props.customer?.shipping

    return hasAddress(address)
  } else if (salesTaxCompanyLevel.value) {
    return hasAddress(companyStore.selectedCompany.address)
  }
  return false
})

watch(
  () => props.customer,
  (v, o) => {
    if (v && o && salesTaxCustomerLevel.value) {
      // call if customer changed address
      isCustomerAddressChanged(v, o)
      return
    }
    if (!isAddressAvailable.value && salesTaxCustomerLevel.value && v) {
      setTimeout(() => {
        openAddressModal()
      }, 500)
    } else if (salesTaxCustomerLevel.value && v) {
      fetchSalesTax()
    } else if (salesTaxCustomerLevel.value && !v) {
      removeSalesTax()
    }
  }
)

// Open modal for company address
onMounted(() => {
  if (salesTaxCompanyLevel.value) {
    isAddressAvailable.value ? fetchSalesTax() : openAddressModal()
  }
})

function hasAddress(address) {
  if (!address) return false

  return (
    address.address_street_1 && address.city && address.state && address.zip
  )
}

function isCustomerAddressChanged(newV, oldV) {
  const newData = isSalesTaxTypeBilling.value ? newV.billing : newV.shipping
  const oldData = isSalesTaxTypeBilling.value ? oldV.billing : oldV.shipping

  const newAdd = pick(newData, ['address_street_1', 'city', 'state', 'zip'])
  const oldAdd = pick(oldData, ['address_street_1', 'city', 'state', 'zip'])
  !isEqual(newAdd, oldAdd) ? fetchSalesTax() : ''
}

function openAddressModal() {
  if (!salesTaxEnabled.value) return
  let modalData = null
  let title = ''
  if (salesTaxCustomerLevel.value) {
    if (isSalesTaxTypeBilling.value) {
      modalData = props.customer?.billing
      title = t('settings.taxations.add_billing_address')
    } else {
      modalData = props.customer?.shipping
      title = t('settings.taxations.add_shipping_address')
    }
  } else {
    modalData = companyStore.selectedCompany.address
    title = t('settings.taxations.add_company_address')
  }

  modalStore.openModal({
    title: title,
    content: t('settings.taxations.modal_description'),
    componentName: 'TaxationAddressModal',
    data: modalData,
    id: salesTaxCustomerLevel.value ? props.customer.id : '',
  })
}

async function fetchSalesTax() {
  if (!salesTaxEnabled.value) return

  fetchingTax.value = true
  await taxTypeStore
    .fetchSalesTax(addressData.value)
    .then((res) => {
      addSalesTax(res.data.data)
      fetchingTax.value = false
    })
    .catch((err) => {
      if (err.response.data.error) {
        setTimeout(() => {
          openAddressModal()
        }, 500)
      }
      fetchingTax.value = false
    })
}

function addSalesTax(tax) {
  tax.tax_type_id = tax.id

  const i = props.store[props.storeProp].taxes.findIndex(
    (_t) => _t.name === SALES_TAX_US && _t.type === SALES_TAX_MODULE
  )

  if (i > -1) {
    Object.assign(props.store[props.storeProp].taxes[i], tax)
  } else {
    props.store[props.storeProp].taxes.push(tax)
  }
}

function removeSalesTax() {
  // remove from total taxes
  const i = props.store[props.storeProp].taxes.findIndex(
    (_t) => _t.name === SALES_TAX_US && _t.type === SALES_TAX_MODULE
  )
  i > -1 ? props.store[props.storeProp].taxes.splice(i, 1) : ''

  //  remove from tax-type list
  let pos = taxTypeStore.taxTypes.findIndex(
    (_t) => _t.name === SALES_TAX_US && _t.type === SALES_TAX_MODULE
  )

  pos > -1 ? taxTypeStore.taxTypes.splice(pos, 1) : ''
}
</script>
