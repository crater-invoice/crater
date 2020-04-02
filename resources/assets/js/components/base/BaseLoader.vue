<template>
  <div class="base-loader">
    <div class="spinner"/>
    <div class="overlay">
      <div class="loader-inner ball-scale-ripple-multiple">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
  </div>
</template>
<script>
export default {

}
</script>

<style scoped>
.overlay {
    height: 100%;
    width: 100%;
    background: rgba(255,255,255,0.4);
    position: absolute;
    top: 7%;
    left: 13%;
}
</style>
