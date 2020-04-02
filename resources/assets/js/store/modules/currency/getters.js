export const currencies = (state) => state.currencies
export const defaultCurrency = (state) => state.defaultCurrency
export const defaultCurrencyForInput = (state) => {
  if (state.defaultCurrency) {
    return {
      decimal: state.defaultCurrency.decimal_separator,
      thousands: state.defaultCurrency.thousand_separator,
      prefix: state.defaultCurrency.symbol + ' ',
      precision: state.defaultCurrency.precision,
      masked: false
    }
  }

  return {
    decimal: '.',
    thousands: ',',
    prefix: '$ ',
    precision: 2,
    masked: false
  }
}
