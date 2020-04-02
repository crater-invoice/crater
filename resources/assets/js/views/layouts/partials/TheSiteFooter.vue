<template>
  <footer class="site-footer">
    <div class="text-right">
      {{ $t('general.powered_by') }}
      <a
        href="http://bytefury.com/"
        target="_blank">{{ $t('general.bytefury') }}
      </a>
    </div>
  </footer>
</template>

<script type="text/babel">
export default {
  data () {
    return {
      footer: 'footer'
    }
  }
}
</script>
