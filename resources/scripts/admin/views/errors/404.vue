<template>
  <div class="w-full h-screen">
    <div class="flex items-center justify-center w-full h-full">
      <div class="flex flex-col items-center justify-center">
        <h1 class="text-primary-500" style="font-size: 10rem">
          {{ $t('general.four_zero_four') }}
        </h1>
        <h5 class="mb-10 text-3xl text-primary-500">
          {{ $t('general.you_got_lost') }}
        </h5>
        <router-link
          class="
            flex
            items-center
            w-32
            h-12
            px-3
            py-1
            text-base
            font-medium
            leading-none
            text-center text-white
            rounded
            whitespace-nowrap
            bg-primary-500
            btn-lg
            hover:text-white
          "
          :to="path"
        >
          <BaseIcon name="ArrowLeftIcon" class="mr-2 text-white icon" />

          {{ $t('general.go_home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const path = computed(() => {
  let data = route.path.indexOf('customer')

  if (data > -1 && route.params.company) {
    return `/${route.params.company}/customer/dashboard`
  } else if (route.params.catchAll) {
    let index = route.params.catchAll.indexOf('/')
    if (index > -1) {
      let slug = route.params.catchAll.substring(index, 0)
      return `/${slug}/customer/dashboard`
    } else {
      return '/'
    }
  } else {
    return `/admin/dashboard`
  }
})
</script>
