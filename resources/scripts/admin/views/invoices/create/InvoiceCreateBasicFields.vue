<template>
  <div class="grid grid-cols-12 gap-8 mt-6 mb-8">
    <BaseCustomerSelectPopup
      v-model="invoiceStore.newInvoice.customer"
      :valid="v.customer_id"
      :content-loading="isLoading"
      type="invoice"
      class="col-span-12 lg:col-span-5 pr-0"
    />

    <BaseInputGrid class="col-span-12 lg:col-span-7">
      <BaseInputGroup
        :label="$t('invoices.invoice_date')"
        :content-loading="isLoading"
        required
        :error="v.invoice_date.$error && v.invoice_date.$errors[0].$message"
      >
        <BaseDatePicker
          v-model="invoiceStore.newInvoice.invoice_date"
          :content-loading="isLoading"
          :calendar-button="true"
          calendar-button-icon="calendar"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('invoices.due_date')"
        :content-loading="isLoading"
      >
        <BaseDatePicker
          v-model="invoiceStore.newInvoice.due_date"
          :content-loading="isLoading"
          :calendar-button="true"
          calendar-button-icon="calendar"
        />
      </BaseInputGroup>

      <BaseInputGroup
        :label="$t('invoices.invoice_number')"
        :content-loading="isLoading"
        :error="v.invoice_number.$error && v.invoice_number.$errors[0].$message"
        required
      >
        <BaseInput
          v-model="invoiceStore.newInvoice.invoice_number"
          :content-loading="isLoading"
          @input="v.invoice_number.$touch()"
        />
      </BaseInputGroup>

      <div class="flex mt-7">
        <div class="relative w-20 mt-8">
          <BaseSwitch
            v-model="invoiceStore.newInvoice.credit"
            class="absolute -top-4"
          />
        </div>

        <div class="ml-2">
          <p class="p-0 mb-1 leading-snug text-left text-black">
            {{ $t('invoices.is_credit') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-left text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('invoices.is_credit_desc') }}
          </p>
        </div>
      </div>

      <ExchangeRateConverter
        :store="invoiceStore"
        store-prop="newInvoice"
        :v="v"
        :is-loading="isLoading"
        :is-edit="isEdit"
        :customer-currency="invoiceStore.newInvoice.currency_id"
      />
    </BaseInputGrid>
  </div>
</template>

<script setup>
import ExchangeRateConverter from '@/scripts/admin/components/estimate-invoice-common/ExchangeRateConverter.vue'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'

const props = defineProps({
  v: {
    type: Object,
    default: null,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
})

const invoiceStore = useInvoiceStore()
</script>
