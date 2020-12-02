import * as types from './mutation-types'

export default {
  [types.SET_BACKUPS](state, data) {
    state.backups = data.backups
    state.backupDisks = data.disks
  },

  [types.SET_BACKUP_DISKS](state, data) {
    state.backupDisksInfo = data.disks
  },

  [types.ADD_BACKUPS](state, data) {
    state.backups.push(data.backup)
  },

  [types.DELETE_BACKUPS](state, id) {
    let index = state.backups.findIndex((backup) => backup.id === id)
    state.backups.splice(index, 1)
  },
}
