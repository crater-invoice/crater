const settings = {
  tableClass: '',
  theadClass: '',
  tbodyClass: '',
  headerClass: '',
  cellClass: '',
  filterInputClass: '',
  filterPlaceholder: 'Filter table…',
  filterNoResults: 'There are no matching rows'
}

export function mergeSettings (newSettings) {
  for (const setting in newSettings) {
    settings[setting] = newSettings[setting]
  }
}

export default settings
