<template>
  <NoteModal />
  <div class="w-full">
    <Popover v-slot="{ isOpen }">
      <PopoverButton
        v-if="userStore.hasAbilities(abilities.VIEW_NOTE)"
        :class="isOpen ? '' : 'text-opacity-90'"
        class="
          flex
          items-center
          z-10
          font-medium
          text-primary-400
          focus:outline-none focus:border-none
        "
        @click="fetchInitialData"
      >
        <BaseIcon
          name="PlusIcon"
          class="w-4 h-4 font-medium text-primary-400"
        />
        {{ $t('general.insert_note') }}
      </PopoverButton>

      <!-- Note Select Popup -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-1 opacity-0"
      >
        <PopoverPanel
          v-slot="{ close }"
          class="
            absolute
            z-20
            px-4
            mt-3
            sm:px-0
            w-screen
            max-w-full
            left-0
            top-3
          "
        >
          <div
            class="
              overflow-hidden
              rounded-md
              shadow-lg
              ring-1 ring-black ring-opacity-5
            "
          >
            <div class="relative grid bg-white">
              <div class="relative p-4">
                <BaseInput
                  v-model="textSearch"
                  :placeholder="$t('general.search')"
                  type="text"
                  class="text-black"
                >
                </BaseInput>
              </div>

              <div
                v-if="filteredNotes.length > 0"
                class="relative flex flex-col overflow-auto list max-h-36"
              >
                <div
                  v-for="(note, index) in filteredNotes"
                  :key="index"
                  tabindex="2"
                  class="
                    px-6
                    py-4
                    border-b border-gray-200 border-solid
                    cursor-pointer
                    hover:bg-gray-100 hover:cursor-pointer
                    last:border-b-0
                  "
                  @click="selectNote(index, close)"
                >
                  <div class="flex justify-between px-2">
                    <label
                      class="
                        m-0
                        text-base
                        font-semibold
                        leading-tight
                        text-gray-700
                        cursor-pointer
                      "
                    >
                      {{ note.name }}
                    </label>
                  </div>
                </div>
              </div>
              <div v-else class="flex justify-center p-5 text-gray-400">
                <label class="text-base text-gray-500">
                  {{ $t('general.no_note_found') }}
                </label>
              </div>
            </div>
            <button
              v-if="userStore.hasAbilities(abilities.MANAGE_NOTE)"
              type="button"
              class="
                h-10
                flex
                items-center
                justify-center
                w-full
                px-2
                py-3
                bg-gray-200
                border-none
                outline-none
              "
              @click="openNoteModal"
            >
              <BaseIcon name="CheckCircleIcon" class="text-primary-400" />
              <label
                class="
                  m-0
                  ml-3
                  text-sm
                  leading-none
                  cursor-pointer
                  font-base
                  text-primary-400
                "
              >
                {{ $t('settings.customization.notes.add_new_note') }}
              </label>
            </button>
          </div>
        </PopoverPanel>
      </transition>
    </Popover>
  </div>
</template>

<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { computed, ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useNotesStore } from '@/scripts/admin/stores/note'
import { useModalStore } from '@/scripts/stores/modal'
import NoteModal from '@/scripts/admin/components/modal-components/NoteModal.vue'
import { useUserStore } from '@/scripts/admin/stores/user'
import abilities from '@/scripts/admin/stub/abilities'

const props = defineProps({
  type: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['select'])

const table = ref(null)
const { t } = useI18n()
const textSearch = ref(null)

const modalStore = useModalStore()
const noteStore = useNotesStore()
const userStore = useUserStore()

const filteredNotes = computed(() => {
  if (textSearch.value) {
    return noteStore.notes.filter(function (el) {
      return (
        el.name.toLowerCase().indexOf(textSearch.value.toLowerCase()) !== -1
      )
    })
  } else {
    return noteStore.notes
  }
})

async function fetchInitialData() {
  await noteStore.fetchNotes({
    filter: {},
    orderByField: '',
    orderBy: '',
    type: props.type ? props.type : '',
  })
}

function selectNote(data, close) {
  emit('select', { ...noteStore.notes[data] })

  textSearch.value = null
  close()
}

function openNoteModal() {
  modalStore.openModal({
    title: t('settings.customization.notes.add_note'),
    componentName: 'NoteModal',
    size: 'lg',
    data: props.type,
  })
}
</script>
