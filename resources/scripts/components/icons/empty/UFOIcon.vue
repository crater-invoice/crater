<template>
  <svg
    width="110"
    height="110"
    viewBox="0 0 110 110"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M55 13.75C24.6245 13.75 0 22.9848 0 34.375C0 45.7652 24.6245 55 55 55C85.3755 55 110 45.7652 110 34.375C110 22.9848 85.3755 13.75 55 13.75ZM55 15.4688C86.8708 15.4688 108.281 25.245 108.281 34.375C108.281 43.505 86.8708 53.2812 55 53.2812C23.1292 53.2812 1.71875 43.505 1.71875 34.375C1.71875 25.245 23.1292 15.4688 55 15.4688Z"
      :class="secondaryFillColor"
    />
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M54.9999 1.71875C66.0842 1.71875 75.7452 7.92172 80.697 17.038L82.732 17.2081C77.6737 7.01078 67.1549 0 54.9999 0C42.7985 0 32.2454 7.06406 27.2095 17.3267L29.2479 17.1411C34.1824 7.96812 43.8745 1.71875 54.9999 1.71875Z"
      :class="primaryFillColor"
    />
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M55 96.25C40.7619 96.25 25.7812 99.3283 25.7812 103.125C25.7812 106.922 40.7619 110 55 110C69.2381 110 84.2188 106.922 84.2188 103.125C84.2188 99.3283 69.2381 96.25 55 96.25ZM55 97.9688C70.4602 97.9688 81.5959 101.317 82.4811 103.125C81.5959 104.933 70.4602 108.281 55 108.281C39.5398 108.281 28.4041 104.933 27.5189 103.125C28.4041 101.317 39.5398 97.9688 55 97.9688Z"
      :class="primaryFillColor"
    />
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M27.4756 103.328L25.8049 102.922L41.2737 39.3286L42.9443 39.7342L27.4756 103.328Z"
      :class="primaryFillColor"
    />
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M82.5247 103.328L67.0559 39.7342L68.7265 39.3286L84.1953 102.922L82.5247 103.328Z"
      :class="primaryFillColor"
    />
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M68.75 39.5312C68.75 42.3792 62.5934 44.6875 55 44.6875C47.4066 44.6875 41.25 42.3792 41.25 39.5312C41.25 36.6833 47.4066 34.375 55 34.375C62.5934 34.375 68.75 36.6833 68.75 39.5312Z"
      :class="secondaryFillColor"
    />
  </svg>
</template>

<script setup>
const props = defineProps({
  primaryFillColor: {
    type: String,
    default: 'fill-primary-500',
  },
  secondaryFillColor: {
    type: String,
    default: 'fill-gray-600',
  },
})
</script>
