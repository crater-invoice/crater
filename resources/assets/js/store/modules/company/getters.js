export const getSelectedCompany = (state) => state.selectedCompany

export const getMomentDateFormat = (state) => state.momentDateFormat

export const getCarbonDateFormat = (state) => state.carbonDateFormat

export const itemDiscount = (state) => state.item_discount

export const defaultFiscalYear = (state) => state.defaultFiscalYear

export const defaultTimeZone = (state) => state.defaultTimeZone

export const defaultCurrency = (state) => state.defaultCurrency

export const defaultCurrencyForInput = (state) => {
  if (state.defaultCurrency) {
    return {
      decimal: state.defaultCurrency.decimal_separator,
      thousands: state.defaultCurrency.thousand_separator,
      prefix: state.defaultCurrency.symbol + ' ',
      precision: state.defaultCurrency.precision,
      masked: false,
    }
  }

  return {
    decimal: '.',
    thousands: ',',
    prefix: '$ ',
    precision: 2,
    masked: false,
  }
}
