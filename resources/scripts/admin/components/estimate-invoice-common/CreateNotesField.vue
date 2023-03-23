<template>
  <div class="mb-6">
    <div
      class="z-20 text-sm font-semibold leading-5 text-primary-400 float-right"
    >
      <SelectNotePopup :type="type" @select="onSelectNote" />
    </div>
    <BaseLabel class="!text-gray-800 !mb-4 ">
      {{ $t('invoices.notes') }}
    </BaseLabel>
    <BaseCustomInput
      v-model="store[storeProp].notes"
      :content-loading="store.isFetchingInitialSettings"
      :fields="fields"
      class="mt-1"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SelectNotePopup from '../SelectNotePopup.vue'

const props = defineProps({
  store: {
    type: Object,
    default: null,
  },
  storeProp: {
    type: String,
    default: '',
  },
  fields: {
    type: Object,
    default: null,
  },
  type: {
    type: String,
    default: null,
  },
})

function onSelectNote(data) {
  props.store[props.storeProp].notes = '' + data.notes
}
</script>
