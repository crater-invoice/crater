<template>
  <div class="card-body permissions">
    <p class="form-title">{{ $t('wizard.permissions.permissions') }}</p>
    <p class="form-desc">{{ $t('wizard.permissions.permission_desc') }}</p>
    <div class="d-flex justify-content-start">
      <div class="lists col-md-6">
        <div
          v-for="(permission, index) in permissions"
          :key="index"
          class="row list-items"
        >

          <div class="col-sm-9 left-item">
            {{ permission.folder }}
          </div>
          <div class="col-sm-3 right-item">
            <span v-if="permission.isSet" class="verified"/>
            <span v-else class="not-verified"/>
            <span>{{ permission.permission }}</span>
          </div>
        </div>
      </div>
    </div>
    <base-button
      v-if="!errors"
      class="pull-right mt-5"
      icon="arrow-right"
      right-icon
      color="theme"
      @click="next"
    >
      {{ $t('wizard.continue') }}
    </base-button>
  </div>
</template>
<script>
import Ls from '../../services/ls'

export default {
  data () {
    return {
      loading: false,
      permissions: [],
      errors: false
    }
  },
  created () {
    this.getPermissions()
  },
  methods: {
    async getPermissions () {
      this.loading = true

      let response = await window.axios.get('/api/admin/onboarding/permissions', this.profileData)

      if (response.data) {
        this.permissions = response.data.permissions.permissions
        this.errors = response.data.permissions.errors
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
