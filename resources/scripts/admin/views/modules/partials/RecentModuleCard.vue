<template>
  <router-link class="relative group" :to="`/admin/modules/${data.slug}`">
    <div class="relative group">
      <div class="aspect-w-4 aspect-h-3 rounded-lg overflow-hidden bg-gray-100">
        <img :src="data.cover" class="object-center object-cover" />
        <div
          class="flex items-end opacity-0 p-4 group-hover:opacity-100"
          aria-hidden="true"
        >
          <div
            class="
              w-full
              bg-white bg-opacity-75
              backdrop-filter backdrop-blur
              py-2
              px-4
              rounded-md
              text-sm
              font-medium
              text-primary-500 text-center
            "
          >
            {{ $t('modules.view_module') }}
          </div>
        </div>
      </div>
      <div
        class="
          mt-4
          flex
          items-center
          justify-between
          text-base
          font-medium
          text-gray-900
          space-x-8
          cursor-pointer
        "
      >
        <h3 class="text-primary-500 font-bold">
          <span aria-hidden="true" class="absolute inset-0"></span>
          {{ data.name }}
        </h3>
        <p class="text-primary-500 font-bold">
          $ {{ data.monthly_price / 100 }}
        </p>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const props = defineProps({
  data: {
    type: Object,
    default: null,
    required: true,
  },
})
</script>
