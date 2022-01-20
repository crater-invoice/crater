<template>
  <BaseModal :show="modalActive" @close="closeModal" @open="setData">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalTitle }}
        <BaseIcon
          name="XIcon"
          class="h-6 w-6 text-gray-500 cursor-pointer"
          @click="closeModal"
        />
      </div>
    </template>
    <div class="px-8 py-8 sm:p-6">
      <div
        v-if="modalStore.data"
        class="grid grid-cols-3 gap-2 p-1 overflow-x-auto"
      >
        <div
          v-for="(template, index) in modalStore.data.templates"
          :key="index"
          :class="{
            'border border-solid border-primary-500':
              selectedTemplate === template.name,
          }"
          class="
            relative
            flex flex-col
            m-2
            border border-gray-200 border-solid
            cursor-pointer
            hover:border-primary-300
          "
        >
          <img
            :src="template.path"
            :alt="template.name"
            class="w-full"
            @click="selectedTemplate = template.name"
          />
          <img
            v-if="selectedTemplate === template.name"
            :alt="template.name"
            class="absolute z-10 w-5 h-5 text-primary-500"
            style="top: -6px; right: -5px"
            :src="getTickImage()"
          />
          <span
            :class="[
              'w-full p-1 bg-gray-200 text-sm text-center absolute bottom-0 left-0',
              {
                'text-primary-500 bg-primary-100':
                  selectedTemplate === template.name,
                'text-gray-600': selectedTemplate != template.name,
              },
            ]"
          >
            {{ template.name }}
          </span>
        </div>
      </div>
    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <BaseButton class="mr-3" variant="primary-outline" @click="closeModal">
        {{ $t('general.cancel') }}
      </BaseButton>
      <BaseButton variant="primary" @click="chooseTemplate()">
        <template #left="slotProps">
          <BaseIcon name="SaveIcon" :class="slotProps.class" />
        </template>
        {{ $t('general.choose') }}
      </BaseButton>
    </div>
  </BaseModal>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useModalStore } from '@/scripts/stores/modal'

const modalStore = useModalStore()

const selectedTemplate = ref('')

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'SelectTemplate'
})

const modalTitle = computed(() => {
  return modalStore.title
})

function setData() {
  if (modalStore.data.store[modalStore.data.storeProp].template_name) {
    selectedTemplate.value =
      modalStore.data.store[modalStore.data.storeProp].template_name
  } else {
    selectedTemplate.value = modalStore.data.templates[0]
  }
}

async function chooseTemplate() {
  await modalStore.data.store.setTemplate(selectedTemplate.value)
  closeModal()
}

function getTickImage() {
  const imgUrl = new URL('/img/tick.png', import.meta.url)
  return imgUrl
}

function closeModal() {
  modalStore.closeModal()

  setTimeout(() => {
    modalStore.$reset()
  }, 300)
}
</script>
