<template>
  <div class="h-full text-base site-wrapper">
    <base-notification />
    <div class="container mx-auto">
      <router-view></router-view>
    </div>
  </div>
</template>

<script type="text/babel">
export default {
  watch: {
    $route: 'onRouteChange',
  },
}
</script>
