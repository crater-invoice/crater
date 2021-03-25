<template>
  <div class="w-full h-full">
    <div class="flex items-center justify-center w-full h-full">
      <div class="flex flex-col items-center justify-center">
        <h1 class="text-primary-500" style="font-size: 10rem">
          {{ $t('general.four_zero_four') }}
        </h1>
        <h5 class="mb-10 text-3xl text-primary-500">
          {{ $t('general.you_got_lost') }}
        </h5>
        <router-link
          class="flex items-center w-32 h-12 px-2 py-1 text-base font-medium leading-none text-center text-white whitespace-nowrap rounded bg-primary-500 btn-lg hover:text-white"
          to="/admin/dashboard"
        >
          <arrow-left-icon class="mr-2 text-white icon" />
          {{ $t('general.go_home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ArrowLeftIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    ArrowLeftIcon,
  },
  mounted() {
    this.setLayoutClasses()
  },
  destroyed() {
    let body = document.getElementsByTagName('body')
    body[0].classList -= ' bg-black'
  },
  methods: {
    setLayoutClasses() {
      let body = document.getElementsByTagName('body')
      body[0].classList += ' bg-black'
    },
  },
}
</script>
