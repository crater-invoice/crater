import Vue from 'vue'

Vue.directive('click-outside', {
  bind: function (el, binding, vnode) {
    el.event = function (event) {
      // here I check that click was outside the el and his childrens
      if (!(el === event.target || el.contains(event.target))) {
        // and if it did, call method provided in attribute value
        vnode.context[binding.expression](event)
      }
    }
    document.body.addEventListener('click', el.event)
  },
  unbind: function (el) {
    document.body.removeEventListener('click', el.event)
  },
})

Vue.directive('autoresize', {
  inserted: function (el) {
    el.style.height = el.scrollHeight + 'px'
    if (el.style.overflow && el.style.overflow.y) {
      el.style.overflow.y = 'hidden'
    }
    el.style.resize = 'none'
    function OnInput() {
      this.style.height = 'auto'
      this.style.height = this.scrollHeight + 'px'
      this.scrollTop = this.scrollHeight
      window.scrollTo(window.scrollLeft, this.scrollTop + this.scrollHeight)
    }
    el.addEventListener('input', OnInput, false)
  },
})
