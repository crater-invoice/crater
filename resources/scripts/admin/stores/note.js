import axios from 'axios'
import { defineStore } from 'pinia'
import { handleError } from '@/scripts/helpers/error-handling'

export const useNotesStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'notes',

    state: () => ({
      notes: [],
      currentNote: {
        id: null,
        type: '',
        name: '',
        notes: '',
      },
    }),

    getters: {
      isEdit: (state) => (state.currentNote.id ? true : false),
    },

    actions: {
      resetCurrentNote() {
        this.currentNote = {
          type: '',
          name: '',
          notes: '',
        }
      },

      fetchNotes(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/notes`, { params })
            .then((response) => {
              this.notes = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchNote(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/notes/${id}`)
            .then((response) => {
              this.currentNote = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addNote(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/notes', data)
            .then((response) => {
              this.notes.push(response.data)
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateNote(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/notes/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.notes.findIndex(
                  (notes) => notes.id === response.data.data.id
                )
                this.notes[pos] = data.notes
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteNote(id) {
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/notes/${id}`)
            .then((response) => {
              let index = this.notes.findIndex((note) => note.id === id)
              this.notes.splice(index, 1)
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },
    },
  })()
}
