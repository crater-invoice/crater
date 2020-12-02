<template>
  <div
    :class="{ 'bg-gray-400': showBgOverlay }"
    class="absolute top-0 left-0 z-20 flex items-center justify-center w-full h-full bg-opacity-25 base-loader"
  >
    <div class="absolute top-0 left-0 w-full h-full overlay">
      <div
        class="absolute flex items-center justify-center ball-scale-ripple-multiple"
      >
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    showBgOverlay: {
      default: false,
      type: Boolean,
    },
  },
}
</script>

<style lang="scss">
// function.scss

@function delay($interval, $count, $index) {
  @return ($index * $interval) - ($interval * $count);
}

// mixins.scss

@mixin global-bg() {
  background-color: #5851d8;
}

@mixin global-animation() {
  animation-fill-mode: both;
}

@mixin balls() {
  @include global-bg();

  width: 15px;
  height: 15px;
  border-radius: 100%;
  margin: 2px;
}

@mixin lines() {
  @include global-bg();

  width: $line-width;
  height: $line-height;
  border-radius: 2px;
  margin: $margin;
}

.base-loader {
  .overlay {
    @keyframes ball-scale-ripple-multiple {
      0% {
        transform: scale(0.1);
        opacity: 1;
      }
      70% {
        transform: scale(1);
        opacity: 0.7;
      }
      100% {
        opacity: 0;
      }
    }

    @mixin ball-scale-ripple-multiple($n: 3, $start: 0) {
      @for $i from $start through $n {
        > div:nth-child(#{$i}) {
          animation-delay: delay(0.2s, $n, $i - 1);
        }
      }
    }

    .loader {
      width: 100%;
      position: relative;
      min-height: 500px;
    }

    .ball-scale-ripple-multiple {
      transform: translateY(-25px);
      top: 50%;
      left: 50%;
      @include ball-scale-ripple-multiple();
      transform: translateY(-50px / 2);

      > div {
        @include global-animation();

        position: absolute;
        top: -2px;
        left: -26px;
        width: 50px;
        height: 50px;
        border-radius: 100%;
        border: 2px solid #5851d8;
        animation: ball-scale-ripple-multiple 1.25s 0s infinite
          cubic-bezier(0.21, 0.53, 0.56, 0.8);
      }
    }
  }

  &.table-loader .overlay {
    background: rgba(255, 255, 255, 0.5);
    height: calc(100% - 80px);
    top: 80px;
  }
}
</style>
