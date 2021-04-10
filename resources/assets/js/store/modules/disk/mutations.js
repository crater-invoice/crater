import * as types from './mutation-types'

export default {
  [types.SET_DISKS](state, disks) {
    state.disks = disks
  },

  [types.SET_DISK_DRIVER](state, data) {
    state.diskDrivers = data.drivers
  },

  [types.ADD_DISKS](state, data) {
    state.disks.push(data.disk)
  },

  [types.DELETE_DISK](state, id) {
    let pos = state.disks.findIndex((disk) => disk.id === id)
    state.disks.splice(pos, 1)
  },
}
