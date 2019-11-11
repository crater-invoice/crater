import * as types from './mutation-types'

export default {
  [types.SET_EXPENSES] (state, expenses) {
    state.expenses = expenses
  },

  [types.ADD_EXPENSE] (state, expense) {
    state.expenses.push(expense)
  },

  [types.UPDATE_EXPENSE] (state, data) {
    let pos = state.expenses.findIndex(expense => expense.id === data.expense.id)

    state.expenses[pos] = data.expense
  },

  [types.DELETE_EXPENSE] (state, id) {
    let index = state.expenses.findIndex(expense => expense.id === id)
    state.expenses.splice(index, 1)
  },

  [types.SET_SELECT_ALL] (state, selectAll) {
    if (selectAll) {
      state.expenses.filter(expense => {
        state.selectedRow.push(expense.id)
      })
    } else {
      state.selectedRow = []
    }
  },

  [types.SET_TOTAL_EXPENSES] (state, totalExpenses) {
    state.totalExpenses = totalExpenses
  },

  [types.DELETE_EXPENSE] (state, id) {
    let index = state.expenses.findIndex(expense => expense.id === id)
    state.expenses.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_EXPENSES] (state, selectedExpenses) {
    selectedExpenses.forEach((expense) => {
      let index = state.expenses.findIndex(_exp => _exp.id === expense.id)
      state.expenses.splice(index, 1)
    })

    state.selectedExpenses = []
  },

  [types.SET_SELECTED_EXPENSES] (state, data) {
    state.selectedExpenses = data
  },

  [types.SET_SELECT_ALL_STATE] (state, data) {
    state.selectAllField = data
  }
}
