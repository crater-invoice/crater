<template>
  <div class="relative">
    <BaseCard container-class="px-4 py-5 sm:px-8 sm:py-2">
      <BaseTabGroup>
        <BaseTab
          tab-panel-container="py-4 mt-px"
          :title="$t('settings.customization.invoices.title')"
        >
          <InvoicesTab />
        </BaseTab>

        <BaseTab
          tab-panel-container="py-4 mt-px"
          :title="$t('settings.customization.estimates.title')"
        >
          <EstimatesTab />
        </BaseTab>

        <BaseTab
          tab-panel-container="py-4 mt-px"
          :title="$t('settings.customization.payments.title')"
        >
          <PaymentsTab />
        </BaseTab>

        <BaseTab
          tab-panel-container="py-4 mt-px"
          :title="$t('settings.customization.items.title')"
        >
          <ItemsTab />
        </BaseTab>
      </BaseTabGroup>
    </BaseCard>
  </div>
</template>

<script setup>
import InvoicesTab from '@/scripts/admin/views/settings/customization/invoices/InvoicesTab.vue'
import EstimatesTab from '@/scripts/admin/views/settings/customization/estimates/EstimatesTab.vue'
import PaymentsTab from '@/scripts/admin/views/settings/customization/payments/PaymentsTab.vue'
import ItemsTab from '@/scripts/admin/views/settings/customization/items/ItemsTab.vue'
</script>
