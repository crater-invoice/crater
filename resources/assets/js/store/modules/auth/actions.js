import Ls from '@/services/ls'
import * as types from './mutation-types'
import * as userTypes from '../user/mutation-types'
import * as rootTypes from '../../mutation-types'
import router from '@/router.js'

export const login = ({ commit, dispatch, state }, data) => {
  let loginData = {
    username: data.email,
    password: data.password
  }
  return new Promise((resolve, reject) => {
    axios.post('/api/auth/login', loginData).then((response) => {
      let token = response.data.access_token
      Ls.set('auth.token', token)

      commit('user/' + userTypes.RESET_CURRENT_USER, null, { root: true })
      commit(rootTypes.UPDATE_APP_LOADING_STATUS, false, { root: true })

      commit(types.AUTH_SUCCESS, token)
      window.toastr['success']('Login Successful')
      resolve(response)
    }).catch(err => {
      if (err.response.data.error === 'invalid_credentials') {
        window.toastr['error']('Invalid Credentials')
      } else {
        // Something happened in setting up the request that triggered an Error
        console.log('Error', err.message)
      }

      commit(types.AUTH_ERROR, err.response)
      Ls.remove('auth.token')
      reject(err)
    })
  })
}

export const refreshToken = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    let data = {
      token: Ls.get('auth.token')
    }
    console.log('REFRESH ACTION')
    axios.post('/api/auth/refresh_token', data).then((response) => {
      let token = response.data.data.token
      Ls.set('auth.token', token)
      commit(types.REFRESH_SUCCESS, token)
      resolve(response)
    }).catch(err => {
      reject(err)
    })
  })
}

export const logout = ({ commit, dispatch, state }, noRequest = false) => {
  if (noRequest) {
    commit(types.AUTH_LOGOUT)
    Ls.remove('auth.token')
    router.push('/login')

    return true
  }

  return new Promise((resolve, reject) => {
    axios.get('/api/auth/logout').then((response) => {
      commit(types.AUTH_LOGOUT)
      Ls.remove('auth.token')
      router.push('/login')
      window.toastr['success']('Logged out!', 'Success')
    }).catch(err => {
      reject(err)
      commit(types.AUTH_LOGOUT)
      Ls.remove('auth.token')
      router.push('/login')
    })
  })
}

export const loginOnBoardingUser = ({ commit, dispatch, state }, token) => {
  commit(types.AUTH_SUCCESS, token)
}
