export default {
  toggleSidebar () {
    let icon = document.getElementsByClassName('hamburger')[0]
    document.body.classList.toggle('sidebar-open')
    icon.classList.toggle('is-active')
  },

  addClass (el, className) {
    if (el.classList) el.classList.add(className)
    else el.className += ' ' + className
  },

  hasClass (el, className) {
    const hasClass = el.classList
      ? el.classList.contains(className)
      : new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className)

    return hasClass
  },

  reset (prefix) {
    let regx = new RegExp('\\b' + prefix + '(.*)?\\b', 'g')
    document.body.className = document.body.className.replace(regx, '')
  },

  setLayout (layoutName) {
    this.reset('layout-')
    document.body.classList.add('layout-' + layoutName)
  },

  setSkin (skinName) {
    this.reset('skin-')
    document.body.classList.add('skin-' + skinName)
  },

  setLogo (logoSrc) {
    document.getElementById('logo-desk').src = logoSrc
  },

  formatMoney (amount, currency = 0) {
    if (!currency) {
      currency = {precision: 2, thousand_separator: ',', decimal_separator: '.', symbol: '$'}
    }

    amount = amount / 100

    let {precision, decimal_separator, thousand_separator, symbol} = currency

    try {
      precision = Math.abs(precision)
      precision = isNaN(precision) ? 2 : precision

      const negativeSign = amount < 0 ? '-' : ''

      let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(precision)).toString()
      let j = (i.length > 3) ? i.length % 3 : 0

      let moneySymbol = `<span style="font-family: sans-serif">${symbol}</span>`

      return moneySymbol + ' ' + negativeSign + (j ? i.substr(0, j) + thousand_separator : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand_separator) + (precision ? decimal_separator + Math.abs(amount - i).toFixed(precision).slice(2) : '')
    } catch (e) {
      console.log(e)
    }
  },

  formatGraphMoney (amount, currency = 0) {
    if (!currency) {
      currency = {precision: 2, thousand_separator: ',', decimal_separator: '.', symbol: '$'}
    }

    amount = amount / 100

    let {precision, decimal_separator, thousand_separator, symbol} = currency

    try {
      precision = Math.abs(precision)
      precision = isNaN(precision) ? 2 : precision

      const negativeSign = amount < 0 ? '-' : ''

      let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(precision)).toString()
      let j = (i.length > 3) ? i.length % 3 : 0

      let moneySymbol = `${symbol}`

      return moneySymbol + ' ' + negativeSign + (j ? i.substr(0, j) + thousand_separator : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand_separator) + (precision ? decimal_separator + Math.abs(amount - i).toFixed(precision).slice(2) : '')
    } catch (e) {
      console.log(e)
    }
  }
}
