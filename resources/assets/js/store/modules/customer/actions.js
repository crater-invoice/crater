import * as types from './mutation-types'

export const fetchCustomers = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/customers`, {params}).then((response) => {
      commit(types.BOOTSTRAP_CUSTOMERS, response.data.customers.data)
      commit(types.SET_TOTAL_CUSTOMERS, response.data.customers.total)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const fetchCustomer = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/customers/${id}/edit`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const addCustomer = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post('/api/customers', data).then((response) => {
      commit(types.ADD_CUSTOMER, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const updateCustomer = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.put(`/api/customers/${data.id}`, data).then((response) => {
      if(response.data.success){
        commit(types.UPDATE_CUSTOMER, response.data)
      }
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deleteCustomer = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/customers/${id}`).then((response) => {
      commit(types.DELETE_CUSTOMER, id)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deleteMultipleCustomers = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/customers/delete`, {'id': state.selectedCustomers}).then((response) => {
      commit(types.DELETE_MULTIPLE_CUSTOMERS, state.selectedCustomers)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllCustomers = ({ commit, dispatch, state }) => {
  if (state.selectedCustomers.length === state.customers.length) {
    commit(types.SET_SELECTED_CUSTOMERS, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allCustomerIds = state.customers.map(cust => cust.id)
    commit(types.SET_SELECTED_CUSTOMERS, allCustomerIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const selectCustomer = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_CUSTOMERS, data)
  if (state.selectedCustomers.length === state.customers.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const resetSelectedCustomer = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_CUSTOMER)
}
