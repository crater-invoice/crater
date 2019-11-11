export const categories = (state) => state.categories

export const getCategoryById = (state) => (id) => {
  return state.categories.find(category => category.id === id)
}