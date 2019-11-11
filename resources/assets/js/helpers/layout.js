export default {
  toggleSidebar () {
    var icon = $('.hamburger').first()
    $('body').toggleClass('sidebar-open')
    icon.toggleClass('is-active')
  },
  reset () {
    $('body').removeClass(function (index, css) {
      return (css.match(/(^|\s)layout-\S+/g) || []).join(' ')
    })
  },
  set (layoutName) {
    this.reset()
    $('body').addClass(layoutName)
  }
}
