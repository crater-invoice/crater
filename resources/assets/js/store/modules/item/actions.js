import * as types from './mutation-types'

export const fetchItems = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/items`, { params })
      .then((response) => {
        commit(types.BOOTSTRAP_ITEMS, response.data.items.data)
        commit(types.SET_TOTAL_ITEMS, response.data.itemTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchItem = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/items/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addItem = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/items', data)
      .then((response) => {
        commit(types.ADD_ITEM, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateItem = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/items/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_ITEM, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteItem = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/items/delete`, id)
      .then((response) => {
        commit(types.DELETE_ITEM, id)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteMultipleItems = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/items/delete`, { ids: state.selectedItems })
      .then((response) => {
        commit(types.DELETE_MULTIPLE_ITEMS, state.selectedItems)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllItems = ({ commit, dispatch, state }) => {
  if (state.selectedItems.length === state.items.length) {
    commit(types.SET_SELECTED_ITEMS, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allItemIds = state.items.map((item) => item.id)
    commit(types.SET_SELECTED_ITEMS, allItemIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const selectItem = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_ITEMS, data)
  if (state.selectedItems.length === state.items.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const addItemUnit = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/units`, data)
      .then((response) => {
        commit(types.ADD_ITEM_UNIT, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateItemUnit = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/units/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_ITEM_UNIT, response.data.unit)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchItemUnits = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/units`, { params })
      .then((response) => {
        commit(types.SET_ITEM_UNITS, response.data.units.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fatchItemUnit = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/units/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteItemUnit = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/units/${id}`)
      .then((response) => {
        if (!response.data.error) {
          commit(types.DELETE_ITEM_UNIT, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
