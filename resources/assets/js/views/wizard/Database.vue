<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.database.database') }}</p>
      <p class="form-desc">{{ $t('wizard.database.desc') }}</p>
      <div class="row mt-5">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.app_url') }}</label>
          <span class="text-danger"> * </span>
          <base-input
            :invalid="$v.databaseData.app_url.$error"
            v-model.trim="databaseData.app_url"
            type="text"
            name="name"
            @input="$v.databaseData.app_url.$touch()"
          />
          <div v-if="$v.databaseData.app_url.$error">
            <span v-if="!$v.databaseData.app_url.required" class="text-danger">
              {{ $tc('validation.required') }}
            </span>
            <span v-if="!$v.databaseData.app_url.isUrl" class="text-danger">
              {{ $tc('validation.invalid_url') }}
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.connection') }}</label>
          <span class="text-danger"> *</span>
          <base-select
            v-model="databaseData.database_connection"
            :invalid="$v.databaseData.database_connection.$error"
            :options="connections"
            :searchable="true"
            :show-labels="false"
            @change="$v.databaseData.database_connection.$touch()"
          />
          <div v-if="$v.databaseData.database_connection.$error">
            <span v-if="!$v.databaseData.database_connection.required" class="text-danger">
              {{ $tc('validation.required') }}
            </span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.port') }}</label>
          <span class="text-danger"> *</span>
          <base-input
            :invalid="$v.databaseData.database_port.$error"
            v-model.trim="databaseData.database_port"
            type="text"
            name="database_port"
            @input="$v.databaseData.database_port.$touch()"
          />
          <div v-if="$v.databaseData.database_port.$error">
            <span v-if="!$v.databaseData.database_port.required" class="text-danger">
              {{ $tc('validation.required') }}
            </span>
            <span v-if="!$v.databaseData.database_port.numeric" class="text-danger">
              {{ $tc('validation.numbers_only') }}
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.db_name') }}</label>
          <span class="text-danger"> *</span>
          <base-input
            :invalid="$v.databaseData.database_name.$error"
            v-model.trim="databaseData.database_name"
            type="text"
            name="database_name"
            @input="$v.databaseData.database_name.$touch()"
          />
          <div v-if="$v.databaseData.database_name.$error">
            <span v-if="!$v.databaseData.database_name.required" class="text-danger">
              {{ $tc('validation.required') }}
            </span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.username') }}</label>
          <span class="text-danger"> *</span>
          <base-input
            :invalid="$v.databaseData.database_username.$error"
            v-model.trim="databaseData.database_username"
            type="text"
            name="database_username"
            @input="$v.databaseData.database_username.$touch()"
          />
          <div v-if="$v.databaseData.database_username.$error">
            <span v-if="!$v.databaseData.database_username.required" class="text-danger">
              {{ $tc('validation.required') }}
            </span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.password') }}</label>
          <base-input
            v-model.trim="databaseData.database_password"
            type="password"
            name="name"
          />
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.database.host') }}</label>
          <span class="text-danger"> *</span>
          <base-input
            :invalid="$v.databaseData.database_hostname.$error"
            v-model.trim="databaseData.database_hostname"
            type="text"
            name="database_hostname"
            @input="$v.databaseData.database_hostname.$touch()"
          />
          <div v-if="$v.databaseData.database_hostname.$error">
            <span v-if="!$v.databaseData.database_hostname.required" class="text-danger">
              {{ $tc('validation.required') }}
            </span>
          </div>
        </div>
      </div>
      <base-button
        :loading="loading"
        class="pull-right mt-5"
        icon="save"
        color="theme"
        type="submit"
      >
        {{ $t('wizard.save_cont') }}
      </base-button>
    </form>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
const { required, numeric, url } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  data () {
    return {
      databaseData: {
        database_connection: 'mysql',
        database_hostname: '127.0.0.1',
        database_port: '3306',
        database_name: null,
        database_username: null,
        database_password: null,
        app_url: window.location.origin
      },
      loading: false,
      connections: [
        'sqlite',
        'mysql',
        'pgsql',
        'sqlsrv'
      ]
    }
  },
  validations: {
    databaseData: {
      database_connection: {
        required
      },
      database_hostname: {
        required
      },
      database_port: {
        required,
        numeric
      },
      database_name: {
        required
      },
      database_username: {
        required
      },
      app_url: {
        required,
        isUrl (val) {
          return this.$utils.checkValidUrl(val)
        }
      }
    }
  },
  methods: {
    async next () {
      this.$v.databaseData.$touch()
      if (this.$v.databaseData.$invalid) {
        return true
      }
      this.loading = true
      try {
        let response = await window.axios.post('/api/admin/onboarding/environment/database', this.databaseData)
        if (response.data.success) {
          this.$emit('next')
          window.toastr['success'](this.$t('wizard.success.' + response.data.success))
          return true
        } else if (response.data.error) {
          window.toastr['error'](this.$t('wizard.errors.' + response.data.error))
        } else if (response.data.error_message) {
          window.toastr['error'](response.data.error_message)
        }
      } catch (e) {
        window.toastr['error'](e.response.data.message)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
