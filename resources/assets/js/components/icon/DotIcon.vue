<template>
  <div>
    <div class="dot-icon" @click="checktoggle">
      <span class="dot dot1" :class="{'move-right': toggle}" />

      <span class="dot dot2" />

      <span class="dot dot3" :class="{'move-left': toggle}" />

    </div>
  </div>
</template>
<script>
export default {
  data () {
    return {
      toggle: false
    }
  },
  methods: {
    checktoggle: function () {
      var v = this
      v.toggle = true
      setTimeout(function () {
        v.toggle = false
      }, 300)
    }
  }
}
</script>

<style>
  .dot-icon {
    display: flex;
    cursor: pointer;
    padding: 8px 5px 5px 5px;
    justify-content: flex-end;
  }

  .dot {
    display: inline-block;
    background: #A5ACC1;
    display: block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    position: relative;
  }

  .dot1 {
    margin-right: 3px;
  }

  .dot2 {
    margin-right: 3px;
  }

  .move-right {
    animation: moveright 0.2s;
    animation-fill-mode: forwards;
  }

  .move-left {
    animation: moveleft 0.2s;
    animation-fill-mode: forwards;
  }

  @keyframes moveleft {
    from {left: 0px;}
    to {left: -18px;}
  }

  @keyframes moveright {
    from {left: 0px;}
    to {left: 18px;}
  }
</style>
