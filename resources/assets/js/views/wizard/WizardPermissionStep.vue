<template>
  <sw-wizard-step
    :title="$t('wizard.permissions.permissions')"
    :description="$t('wizard.permissions.permission_desc')"
  >
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <div class="relative">
      <div
        v-for="(permission, index) in permissions"
        :key="index"
        class="border border-gray-200"
      >
        <div class="grid grid-flow-row grid-cols-3 lg:gap-24 sm:gap-4">
          <div class="col-span-2 p-3">
            {{ permission.folder }}
          </div>
          <div class="p-3 text-right">
            <span
              v-if="permission.isSet"
              class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-success"
            />
            <span
              v-else
              class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-danger"
            />
            <span>{{ permission.permission }}</span>
          </div>
        </div>
      </div>

      <sw-button
        v-show="!isFetching"
        :loading="isLoading"
        :disabled="isLoading"
        class="mt-10"
        variant="primary"
        @click="next"
      >
        {{ $t('wizard.continue') }}
        <arrow-right-icon class="h-5 ml-2 -mr-1" />
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
      isFetching: false,
      isLoading: false,
      permissions: [],
      errors: false,
    }
  },
  created() {
    this.getPermissions()
  },
  methods: {
    async getPermissions() {
      this.isLoading = this.isFetching = true

      let response = await window.axios.get(
        '/api/v1/onboarding/permissions',
        this.profileData
      )

      if (response.data) {
        this.permissions = response.data.permissions.permissions
        this.errors = response.data.permissions.errors
        let self = this

        if (this.errors) {
          this.$swal({
            title: this.$t('wizard.permissions.permission_confirm_title'),
            text: this.$t('wizard.permissions.permission_confirm_desc'),
            icon: 'warning',
            showCancelButton: true,
            showConfirmButton: true,
          }).then(async (result) => {
            if (result.value) {
              self.isLoading = this.isFetching = false
            }
          })
        } else {
          this.isLoading = this.isFetching = false
        }

        this.isLoading = this.isFetching = false
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
