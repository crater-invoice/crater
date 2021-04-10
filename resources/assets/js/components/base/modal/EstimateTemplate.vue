<template>
  <div class="template-modal">
    <div class="px-8 py-8 sm:p-6">
      <div class="grid grid-cols-3 gap-2 p-1 overflow-x-auto sw-scroll">
        <div
          v-for="(template, index) in modalData"
          :key="index"
          :class="{
            'border border-solid border-primary-500':
              selectedTemplate === template.id,
          }"
          class="relative flex flex-col m-2 border border-gray-200 border-solid cursor-pointer hover:border-primary-300"
          @click="selectedTemplate = template.id"
        >
          <img :src="template.path" alt="template-image" />
          <img
            v-if="selectedTemplate === template.id"
            class="absolute z-10 w-5 h-5 text-primary-500"
            style="top: -6px; right: -5px"
            src="/assets/img/tick.png"
          />
          <span
            :class="[
              'w-full p-1 bg-gray-200 text-sm text-center absolute bottom-0 left-0',
              {
                'text-primary-500 bg-primary-100':
                  selectedTemplate === template.id,
                'text-gray-600': selectedTemplate != template.id,
              },
            ]"
          >
            {{ template.name }}
          </span>
        </div>
      </div>
    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        @click="closeEstimateModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button variant="primary" @click="chooseTemplate()">
        {{ $t('general.choose') }}
      </sw-button>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data() {
    return {
      selectedTemplate: 1,
      isLoading: false,
    }
  },
  computed: {
    ...mapGetters('modal', ['modalData']),
    ...mapGetters('estimate', ['getTemplateId']),
  },
  mounted() {
    this.selectedTemplate = this.getTemplateId
  },
  methods: {
    ...mapActions('estimate', ['setTemplate']),
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    async chooseTemplate() {
      this.isLoading = true
      let resp = await this.setTemplate(this.selectedTemplate)
      if (resp) {
        this.isLoading = false
        this.resetModalData()
        this.closeModal()
      }
    },
    closeEstimateModal() {
      this.selectedTemplate = this.getTemplateId
      this.closeModal()
      this.resetModalData()
    },
  },
}
</script>
