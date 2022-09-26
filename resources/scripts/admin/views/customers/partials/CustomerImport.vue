<template>
  <input type="file" ref="importUploadFile" @change="importCustomers()"/>
  <BaseButton
    @click="$refs.importUploadFile.click()"
  >
    <template #left="slotProps">
      <BaseIcon name="PlusIcon" :class="slotProps.class"/>
    </template>
    {{ $t('customers.import_customers') }}
  </BaseButton>
</template>

<script setup>
import { ref } from 'vue'
import { useCustomerStore } from '@/scripts/admin/stores/customer'

const customerStore = useCustomerStore()
const importUploadFile = ref()

function importCustomers() {
  const file = importUploadFile.value?.files[0] ?? undefined
  if (!file) {
    return
  }

  const data = new FormData()
  data.append('import_upload', new Blob([file]), file.name)

  customerStore.importCustomers(data)
}
</script>

<style scoped>
input[type=file] {
  display: none;
}
</style>
