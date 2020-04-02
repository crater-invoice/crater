<template>
  <div class="site-wrapper">
    <div class="container">
      <router-view></router-view>
    </div>
  </div>
</template>

<script type="text/babel">
export default {
  watch: {
    $route: 'onRouteChange'
  },
  mounted () {
    this.setLayoutBackground()
  },

  destroyed () {
    document.body.style.backgroundColor = '#EBF1FA'
  },
  methods: {
    setLayoutBackground () {
      document.body.style.backgroundColor = '#f9fbff'
    }
  }
}
</script>
