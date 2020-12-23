<template>
  <div class="tax-select">
    <div class="flex flex-col w-full px-4 py-4">
      <div class="relative flex w-full mb-2">
        <sw-input
          v-model="textSearch"
          :placeholder="$t('general.search')"
          focus
          class="text-black"
          icon="search"
          type="text"
        />
      </div>
      <div
        v-if="filteredNotes.length > 0"
        class="relative flex flex-col overflow-auto sw-scroll list"
        style="max-height: 112px"
      >
        <div
          v-for="(note, index) in filteredNotes"
          :key="index"
          class="flex justify-between p-4 border-b border-gray-200 border-solid cursor-pointer list-item last:border-b-0 hover:bg-gray-100"
          @click="selectNote(index)"
        >
          <label
            class="inline-block m-0 text-base font-normal leading-tight text-black font-base"
          >
            {{ note.name }}
          </label>
        </div>
      </div>
      <div v-else class="flex justify-center p-5 text-gray-400">
        <label class="m-0">{{ $t('general.no_note_found') }}</label>
      </div>
    </div>

    <button
      type="button"
      class="flex items-center justify-center w-full px-2 py-3 bg-gray-200 border-none outline-none hover:bg-gray-300"
      @click="openNoteModal"
    >
      <check-circle-icon class="h-5" />
      <label
        class="m-0 ml-1 text-sm leading-none cursor-pointer font-base text-primary-400"
      >
        {{ $t('settings.customization.notes.add_new_note') }}
      </label>
    </button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { CheckCircleIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    CheckCircleIcon,
  },
  props: {
    type: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      textSearch: null,
    }
  },
  computed: {
    ...mapGetters('notes', ['notes']),

    filteredNotes() {
      if (this.textSearch) {
        var textSearch = this.textSearch
        return this.notes.filter(function (el) {
          return el.name.toLowerCase().indexOf(textSearch.toLowerCase()) !== -1
        })
      } else {
        return this.notes
      }
    },
  },
  created() {
    this.fetchInitialData()
  },
  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('notes', ['fetchNotes']),
    selectNote(index) {
      this.$emit('select', { ...this.notes[index] })
    },

    async fetchInitialData() {
      await this.fetchNotes({
        filter: {},
        orderByField: '',
        orderBy: '',
        type: this.type ? this.type : '',
      })
    },

    openNoteModal() {
      this.openModal({
        title: this.$t('settings.customization.notes.add_note'),
        componentName: 'NoteSelectModal',
        variant: 'lg',
        data: this.type,
      })
    },
  },
}
</script>
