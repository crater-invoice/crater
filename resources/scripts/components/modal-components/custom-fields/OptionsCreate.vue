<template>
  <div class="flex items-center mt-1">
    <BaseInput
      v-model="option"
      type="text"
      class="w-full md:w-96"
      :placeholder="$t('settings.custom_fields.press_enter_to_add')"
      @click="onAddOption"
      @keydown.enter.prevent.stop="onAddOption"
    />

    <BaseIcon
      name="PlusCircleIcon"
      class="ml-1 text-primary-500 cursor-pointer"
      @click="onAddOption"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['onAdd'])

const option = ref(null)

function onAddOption() {
  if (option.value == null || option.value == '' || option.value == undefined) {
    return true
  }

  emit('onAdd', option.value)

  option.value = null
}
</script>
