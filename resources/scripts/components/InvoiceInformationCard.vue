<template>
  <div class="bg-white shadow overflow-hidden rounded-lg mt-6 dark:bg-gray-800">
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
        {{ $t('invoices.invoice_information') }}
      </h3>
    </div>
    <div v-if="invoice"
      class="
        border-t
        border-gray-200
        px-4 py-5 sm:p-0
        dark:border-gray-600
      "
    >
      <dl class="sm:divide-y sm:divide-gray-200 sm:dark:divide-gray-500">
        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
          <dt class="text-sm font-medium text-gray-500 dark:dark:text-gray-400">
            {{ $t('general.from') }}
          </dt>
          <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
            {{ invoice.company.name }}
          </dd>
        </div>
        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
          <dt class="text-sm font-medium text-gray-500 dark:dark:text-gray-400">
            {{ $t('general.to') }}
          </dt>
          <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
            {{ invoice.customer.name }}
          </dd>
        </div>
        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
          <dt class="text-sm font-medium text-gray-500 capitalize dark:dark:text-gray-400">
            {{ $t('invoices.paid_status').toLowerCase() }}
          </dt>
          <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
            <BaseInvoiceStatusBadge
              :status="invoice.paid_status"
              class="px-3 py-1"
            >
              {{ invoice.paid_status }}
            </BaseInvoiceStatusBadge>
          </dd>
        </div>
        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
          <dt class="text-sm font-medium text-gray-500 dark:dark:text-gray-400">
            {{ $t('invoices.total') }}
          </dt>
          <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
            <BaseFormatMoney
              :currency="invoice.currency"
              :amount="invoice.total"
            />
          </dd>
        </div>
        <div
          v-if="invoice.formatted_notes"
          class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
        >
          <dt class="text-sm font-medium text-gray-500">
            {{ $t('invoices.notes') }}
          </dt>
          <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
            <span v-html="invoice.formatted_notes"></span>
          </dd>
        </div>
      </dl>
    </div>
    <div v-else class="w-full flex items-center justify-center p-5">
      <BaseSpinner class="text-primary-500 h-10 w-10" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  invoice: {
    type: [Object, null],
    required: true,
  },
})
</script>
