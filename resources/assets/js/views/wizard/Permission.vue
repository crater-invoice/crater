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
      v-if="isContinue"
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
export default {
  data () {
    return {
      loading: false,
      permissions: [],
      errors: false,
      isContinue: false
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
        let self = this

        if (this.errors) {
          swal({
            title: this.$t('wizard.permissions.permission_confirm_title'),
            text: this.$t('wizard.permissions.permission_confirm_desc'),
            icon: 'warning',
            buttons: true,
            dangerMode: true
          }).then(async (willConfirm) => {
            if (willConfirm) {
              self.isContinue = true
            }
          })
        } else {
          this.isContinue = true
        }

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
