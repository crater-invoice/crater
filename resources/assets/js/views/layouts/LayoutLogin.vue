<template>
  <div class="grid h-full grid-cols-12 overflow-y-hidden bg-gray-100">
    <base-notification />
    <div
      class="flex items-center justify-center w-full max-w-sm col-span-12 p-4 mx-auto text-gray-900 md:p-8 md:col-span-6 lg:col-span-4 flex-2 md:pb-48 md:pt-40"
    >
      <div class="w-full">
        <a href="/admin">
          <img
            src="/assets/img/crater-logo.png"
            class="block w-48 h-auto max-w-full mb-32 text-primary-400"
            alt="Crater Logo"
          />
        </a>
        <router-view></router-view>
        <div
          class="pt-24 mt-0 text-sm not-italic font-medium leading-relaxed text-left text-gray-500 md:pt-40"
        >
          <p class="mb-3">{{ $t('layout_login.copyright_crater') }}</p>
        </div>
      </div>
    </div>

    <div
      class="relative flex-col items-center justify-center hidden w-full h-full pl-10 bg-no-repeat bg-cover md:col-span-6 lg:col-span-8 md:flex content-box"
    >
      <div class="pl-20 xl:pl-0">
        <h1
          class="hidden mb-3 text-3xl font-bold leading-normal text-white xl:text-5xl xl:leading-tight md:none lg:block"
        >
          {{ $t('layout_login.super_simple_invoicing') }} <br />
          {{ $t('layout_login.for_freelancer') }} <br />
          {{ $t('layout_login.small_businesses') }} <br />
        </h1>
        <p
          class="hidden text-sm not-italic font-normal leading-normal text-gray-100 xl:text-base xl:leading-6 md:none lg:block"
        >
          {{ $t('layout_login.crater_help') }}<br />
          {{ $t('layout_login.invoices_and_estimates') }}<br />
        </p>
      </div>

      <div class="absolute z-50 w-full bg-no-repeat content-bottom" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
.content-box {
  background-image: url('/images/login-vector1.svg');
}

.content-bottom {
  background-image: url('/images/login-vector3.svg');
  background-size: 100% 100%;
  height: 300px;
  right: 32%;
  bottom: 0;
}

.content-box::before {
  background-image: url('/images/frame.svg');
  content: '';
  background-size: 100% 100%;
  background-repeat: no-repeat;
  height: 300px;
  right: 0;
  position: absolute;
  top: 0;
  width: 420px;
  z-index: 1;
}

.content-box::after {
  background-image: url('/images/login-vector2.svg');
  content: '';
  background-size: cover;
  background-repeat: no-repeat;
  height: 100%;
  width: 100%;
  right: 7.5%;
  position: absolute;
}
</style>
