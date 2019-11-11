<template>
  <div class="error-box">
    <div class="row">
      <div class="col-sm-12 text-sm-center">
        <h1>{{ $t('general.four_zero_four') }}</h1>
        <h5>{{ $t('general.you_got_lost') }}</h5>
        <router-link
          class="btn btn-lg bg-yellow text-white"
          to="/">
          <font-awesome-icon icon="arrow-left" class="icon text-white mr-2"/> {{ $t('general.go_home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted () {
    this.setLayoutClasses()
  },
  destroyed () {
    $('body').removeClass('page-error-404')
  },
  methods: {
    setLayoutClasses () {
      let body = $('body')
      body.addClass('page-error-404')
    }
  }
}
</script>
