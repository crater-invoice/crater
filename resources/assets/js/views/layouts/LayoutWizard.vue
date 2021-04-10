<template>
  <div class="site-wrapper h-full text-base">
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
