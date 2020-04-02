export const taxTypes = (state) => state.taxTypes

export const getTaxTypeById = (state) => (id) => {
  return state.taxTypes.find(taxType => taxType.id === id)
}
