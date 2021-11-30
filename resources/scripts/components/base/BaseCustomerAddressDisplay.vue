<template>
  <div
    v-if="address"
    class="text-sm font-bold leading-5 text-black non-italic space-y-1"
  >
    <p v-if="address?.address_street_1">{{ address?.address_street_1 }},</p>

    <p v-if="address?.address_street_2">{{ address?.address_street_2 }},</p>

    <p v-if="address?.city">{{ address?.city }},</p>

    <p v-if="address?.state">{{ address?.state }},</p>

    <p v-if="address?.country?.name">{{ address?.country?.name }},</p>

    <p v-if="address?.zip">{{ address?.zip }}.</p>
  </div>
</template>

<script setup>
const props = defineProps({
  address: {
    type: Object,
    required: true,
  },
})
</script>
