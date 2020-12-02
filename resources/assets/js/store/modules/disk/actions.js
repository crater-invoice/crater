import * as types from './mutation-types'

export const fetchDisks = ({ commit }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/disks`, { params })
      .then((response) => {
        commit(types.SET_DISKS, response.data.disks.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchDiskDrivers = ({ commit }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/disk/drivers`)
      .then((response) => {
        commit(types.SET_DISK_DRIVER, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchDiskEnv = ({ commit }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/disks/${data.disk}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const createDisk = ({ commit }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/disks`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateDisk = ({ commit }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/disks/${data.id}`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteFileDisk = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/disks/${id}`)
      .then((response) => {
        if (response.data.success) {
          commit(types.DELETE_DISK, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}
