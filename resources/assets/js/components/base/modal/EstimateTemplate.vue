<template>
  <div class="template-modal">
    <div class="card-body">
      <div class="template-container">
        <div
          v-for="(template,index) in modalData"
          :key="index"
          :class="{'selected-template': selectedTemplate === template.id}"
          class="template-img"
        >
          <img
            :src="template.path"
            alt="template-image"
            height="200" width="140"
            @click="selectedTemplate = template.id"
          >
          <img
            v-if="selectedTemplate === template.id"
            class="check-icon"
            src="/assets/img/tick.png"
          >
        </div>
      </div>
    </div>
    <div class="card-footer">
      <base-button outline class="mr-3" color="theme" @click="closeEstimateModal">
        {{ $t('general.cancel') }}
      </base-button>
      <base-button
        :loading="isLoading"
        color="theme"
        @click="chooseTemplate()"
      >
        {{ $t('general.choose') }}
      </base-button>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data () {
    return {
      selectedTemplate: 1,
      isLoading: false
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalData'
    ]),
    ...mapGetters('estimate', [
      'getTemplateId'
    ])
  },
  mounted () {
    this.selectedTemplate = this.getTemplateId
  },
  methods: {
    ...mapActions('estimate', [
      'setTemplate'
    ]),
    ...mapActions('modal', [
      'closeModal',
      'resetModalData'
    ]),
    async chooseTemplate () {
      this.isLoading = true
      let resp = await this.setTemplate(this.selectedTemplate)
      if (resp) {
        this.isLoading = false
        this.resetModalData()
        this.closeModal()
      }
    },
    closeEstimateModal () {
      this.selectedTemplate = this.getTemplateId
      this.closeModal()
      this.resetModalData()
    }
  }
}
</script>
