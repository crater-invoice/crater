<template>
  <div class="card-body">
    <p class="form-title">{{ $t('wizard.req.system_req') }}</p>
    <p class="form-desc">{{ $t('wizard.req.system_req_desc') }}</p>
    <div v-if="phpSupportInfo" class="d-flex justify-content-start">
      <div class="col-md-6">
        <div class="row list-items">
          <div class="col-md-9 left-item">
            {{ $t('wizard.req.php_req_version', { version: phpSupportInfo.minimum }) }}
          </div>
          <div class="col-md-3 right-item justify-content-end">
            {{ phpSupportInfo.current }}
            <span v-if="phpSupportInfo.supported" class="verified"/>
            <span v-else class="not-verified"/>
          </div>
        </div>
      </div>
    </div>
    <div v-if="requirements" class="d-flex justify-content-start">
      <div class="col-md-6">
        <div
          v-for="(requirement, index) in requirements"
          :key="index"
          class="row list-items"
        >

          <div class="col-md-9 left-item">
            {{ index }}
          </div>
          <div class="col-md-3 right-item  justify-content-end">
            <span v-if="requirement" class="verified"/>
            <span v-else class="not-verified"/>
          </div>
        </div>
      </div>
    </div>
    <base-button
      v-if="hasNext"
      :loading="loading"
      class="pull-right mt-4"
      icon="arrow-right"
      color="theme"
      right-icon
      @click="next"
    >
      {{ $t('wizard.continue') }}
    </base-button>
    <base-button
      v-if="!requirements"
      :loading="loading"
      class="pull-right mt-4"
      color="theme"
      @click="getRequirements"
    >
      {{ $t('wizard.req.check_req') }}
    </base-button>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  data () {
    return {
      requirements: null,
      phpSupportInfo: null,
      loading: false,
      isShow: true
    }
  },
  computed: {
    hasNext () {
      if (this.requirements) {
        let isRequired = true
        for (const key in this.requirements) {
          if (!this.requirements[key]) {
            isRequired = false
          }
        }
        return this.requirements && this.phpSupportInfo.supported && isRequired
      }
      return false
    }
  },
  methods: {
    listToggle  () {
      this.isShow = !this.isShow
    },
    async getRequirements () {
      this.loading = true

      let response = await window.axios.get('/api/admin/onboarding/requirements', this.profileData)

      if (response.data) {
        this.requirements = response.data.requirements.requirements.php
        this.phpSupportInfo = response.data.phpSupportInfo
        this.loading = false
      }
    },
    async next () {
      this.loading = true
      await this.$emit('next')
      this.loading = false
    }
  }
}
</script>
