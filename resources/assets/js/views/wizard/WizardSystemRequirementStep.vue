<template>
  <sw-wizard-step
    :title="$t('wizard.req.system_req')"
    :description="$t('wizard.req.system_req_desc')"
  >
    <div class="w-full md:w-2/3">
      <div class="mb-6">
        <div
          v-if="phpSupportInfo"
          class="grid grid-flow-row grid-cols-3 p-3 border border-gray-200 lg:gap-24 sm:gap-4"
        >
          <div class="col-span-2 text-sm">
            {{
              $t('wizard.req.php_req_version', {
                version: phpSupportInfo.minimum,
              })
            }}
          </div>
          <div class="text-right">
            {{ phpSupportInfo.current }}
            <span
              v-if="phpSupportInfo.supported"
              class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-success"
            />
            <span
              v-else
              class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-danger"
            />
          </div>
        </div>
        <div v-if="requirements">
          <div
            v-for="(requirement, index) in requirements"
            :key="index"
            class="grid grid-flow-row grid-cols-3 p-3 border border-gray-200 lg:gap-24 sm:gap-4"
          >
            <div class="col-span-2 text-sm">
              {{ index }}
            </div>
            <div class="text-right">
              <span
                v-if="requirement"
                class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-success"
              />
              <span
                v-else
                class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-danger"
              />
            </div>
          </div>
        </div>
      </div>
      <sw-button
        v-if="hasNext"
        class="mt-4 pull-right"
        variant="primary"
        @click="next"
      >
        {{ $t('wizard.continue') }}
        <arrow-right-icon class="h-5 ml-2 -mr-1" />
      </sw-button>
      <sw-button
        v-if="!requirements"
        :loading="isLoading"
        :disabled="isLoading"
        class="mt-4"
        variant="primary"
        @click="getRequirements"
      >
        {{ $t('wizard.req.check_req') }}
      </sw-button>
    </div>
  </sw-wizard-step>
</template>

<script>
import { ArrowRightIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    ArrowRightIcon,
  },
  data() {
    return {
      requirements: null,
      phpSupportInfo: null,
      isLoading: false,
      isShow: true,
    }
  },
  computed: {
    hasNext() {
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
    },
  },
  methods: {
    listToggle() {
      this.isShow = !this.isShow
    },
    async getRequirements() {
      this.isLoading = true

      let response = await window.axios.get(
        '/api/v1/onboarding/requirements',
        this.profileData
      )

      if (response.data) {
        this.requirements = response.data.requirements.requirements.php
        this.phpSupportInfo = response.data.phpSupportInfo
        this.isLoading = false
      }
    },
    async next() {
      this.isLoading = true
      await this.$emit('next')
      this.isLoading = false
    },
  },
}
</script>
