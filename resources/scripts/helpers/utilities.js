import i18n from '../plugins/i18n'
const { global } = i18n
import { useNotificationStore } from '@/scripts/stores/notification'
import { isArray } from 'lodash'

export default {
  isImageFile(fileType) {
    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']

    return validImageTypes.includes(fileType)
  },
  addClass(el, className) {
    if (el.classList) el.classList.add(className)
    else el.className += ' ' + className
  },
  hasClass(el, className) {
    const hasClass = el.classList
      ? el.classList.contains(className)
      : new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className)

    return hasClass
  },

  formatMoney(amount, currency = 0) {
    if (!currency) {
      currency = {
        precision: 2,
        thousand_separator: ',',
        decimal_separator: '.',
        symbol: '$',
      }
    }

    amount = amount / 100

    let {
      precision,
      decimal_separator,
      thousand_separator,
      symbol,
      swap_currency_symbol,
    } = currency

    try {
      precision = Math.abs(precision)
      precision = isNaN(precision) ? 2 : precision

      const negativeSign = amount < 0 ? '-' : ''

      let i = parseInt(
        (amount = Math.abs(Number(amount) || 0).toFixed(precision))
      ).toString()
      let j = i.length > 3 ? i.length % 3 : 0

      let moneySymbol = `${symbol}`
      let thousandText = j ? i.substr(0, j) + thousand_separator : ''
      let amountText = i
        .substr(j)
        .replace(/(\d{3})(?=\d)/g, '$1' + thousand_separator)
      let precisionText = precision
        ? decimal_separator +
        Math.abs(amount - i)
          .toFixed(precision)
          .slice(2)
        : ''
      let combinedAmountText =
        negativeSign + thousandText + amountText + precisionText

      return swap_currency_symbol
        ? combinedAmountText + ' ' + moneySymbol
        : moneySymbol + ' ' + combinedAmountText
    } catch (e) {
      console.error(e)
    }
  },

  // Merge two objects but only existing properties
  mergeSettings(target, source) {
    Object.keys(source).forEach(function (key) {
      if (key in target) {
        // or target.hasOwnProperty(key)
        target[key] = source[key]
      }
    })
  },

  isValidUrl(url) {
    const localhostRegex = /^https?:\/\/(localhost|127\.0\.0\.1)/;
    return localhostRegex.test(url) || !!new URL(url);
  },

  isValidDomainUrl(url) {
    const localhostRegex = /(localhost|127\.0\.0\.1)/;
    return localhostRegex.test(url) || !!new URL(url);
  },

  fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement('textarea')
    textArea.value = text
    // Avoid scrolling to bottom
    textArea.style.top = '0'
    textArea.style.left = '0'
    textArea.style.position = 'fixed'
    document.body.appendChild(textArea)
    textArea.focus()
    textArea.select()
    try {
      var successful = document.execCommand('copy')
      var msg = successful ? 'successful' : 'unsuccessful'
      console.log('Fallback: Copying text command was ' + msg)
    } catch (err) {
      console.error('Fallback: Oops, unable to copy', err)
    }
    document.body.removeChild(textArea)
  },
  copyTextToClipboard(text) {
    if (!navigator.clipboard) {
      this.fallbackCopyTextToClipboard(text)
      return
    }
    navigator.clipboard.writeText(text).then(
      function () {
        return true
      },
      function (err) {
        return false
      }
    )
  },
  arrayDifference(array1, array2) {
    return array1?.filter((i) => {
      return array2?.indexOf(i) < 0
    })
  },
  getBadgeStatusColor(status) {
    const statusColors = {
      DRAFT: { bgColor: '#F8EDCB', color: '#744210' },
      PAID: { bgColor: '#D5EED0', color: '#276749' },
      UNPAID: { bgColor: '#F8EDC', color: '#744210' },
      SENT: { bgColor: 'rgba(246, 208, 154, 0.4)', color: '#975a16' },
      REJECTED: { bgColor: '#E1E0EA', color: '#1A1841' },
      ACCEPTED: { bgColor: '#D5EED0', color: '#276749' },
      VIEWED: { bgColor: '#C9E3EC', color: '#2c5282' },
      EXPIRED: { bgColor: '#FED7D7', color: '#c53030' },
      'PARTIALLY PAID': { bgColor: '#C9E3EC', color: '#2c5282' },
      COMPLETED: { bgColor: '#D5EED0', color: '#276749' },
      DUE: { bgColor: '#F8EDCB', color: '#744210' },
      YES: { bgColor: '#D5EED0', color: '#276749' },
      NO: { bgColor: '#FED7D7', color: '#c53030' },
    };

    return statusColors[status];
  },
  getStatusTranslation(status) {
    const statusMessages = {
      DRAFT: 'general.draft',
      PAID: 'invoices.paid',
      UNPAID: 'invoices.unpaid',
      SENT: 'general.sent',
      REJECTED: 'estimates.rejected',
      ACCEPTED: 'estimates.accepted',
      VIEWED: 'invoices.viewed',
      EXPIRED: 'estimates.expired',
      'PARTIALLY PAID': 'estimates.partially_paid',
      COMPLETED: 'invoices.completed',
      DUE: 'general.due',
    };
    const messageKey = statusMessages[status];
    if (messageKey) {
      return global.t(messageKey);
    }
    return status;
  },
  toFormData(object) {
    const formData = new FormData()

    Object.keys(object).forEach((key) => {
      if (isArray(object[key])) {
        formData.append(key, JSON.stringify(object[key]))
      } else {
        // Convert null to empty strings (because formData does not support null values and converts it to string)
        if (object[key] === null) {
          object[key] = ''
        }

        formData.append(key, object[key])
      }
    })

    return formData
  },

}
