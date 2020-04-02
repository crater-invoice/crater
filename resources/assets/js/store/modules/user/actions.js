import * as types from './mutation-types'
import Ls from '@/services/ls'

export const updateCurrentUser = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    axios.post('/api/hq/users', data).then((response) => {
      commit(types.UPDATE_CURRENT_USER, response.data.user)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}
