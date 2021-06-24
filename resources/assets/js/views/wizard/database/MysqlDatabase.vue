<template>
  <form action="" @submit.prevent="next()">
    <div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:mb-6 md:mb-6">
        <sw-input-group
          :label="$t('wizard.database.app_url')"
          :error="urlError"
          required
        >
          <sw-input
            :invalid="$v.databaseData.app_url.$error"
            v-model.trim="databaseData.app_url"
            type="text"
            name="name"
            @input="$v.databaseData.app_url.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.database.connection')"
          :error="connectionError"
          required
        >
          <sw-select
            v-model="databaseData.database_connection"
            :invalid="$v.databaseData.database_connection.$error"
            :options="connections"
            :searchable="true"
            :allow-empty="false"
            :show-labels="false"
            @input="onChangeConnection"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.database.port')"
          :error="portError"
          required
        >
          <sw-input
            :invalid="$v.databaseData.database_port.$error"
            v-model.trim="databaseData.database_port"
            type="text"
            name="database_port"
            @input="$v.databaseData.database_port.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.database.db_name')"
          :error="nameError"
          required
        >
          <sw-input
            :invalid="$v.databaseData.database_name.$error"
            v-model.trim="databaseData.database_name"
            type="text"
            name="database_name"
            @input="$v.databaseData.database_name.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.database.username')"
          :error="usernameError"
          required
        >
          <sw-input
            :invalid="$v.databaseData.database_username.$error"
            v-model.trim="databaseData.database_username"
            type="text"
            name="database_username"
            @input="$v.databaseData.database_username.$touch()"
          />
        </sw-input-group>

        <sw-input-group :label="$t('wizard.database.password')">
          <sw-input
            v-model.trim="databaseData.database_password"
            type="password"
            name="name"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
        <sw-input-group
          :label="$t('wizard.database.host')"
          :error="hostnameError"
          required
        >
          <sw-input
            :invalid="$v.databaseData.database_hostname.$error"
            v-model.trim="databaseData.database_hostname"
            type="text"
            name="database_hostname"
            @input="$v.databaseData.database_hostname.$touch()"
          />
        </sw-input-group>
      </div>
      <sw-button
        v-show="!isFetching"
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        class="mt-4"
        type="submit"
      >
        <save-icon v-if="!isLoading" class="h-5 mr-2" />
        {{ $t('wizard.save_cont') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { SaveIcon } from '@vue-hero-icons/outline'
import { validationMixin } from 'vuelidate'
const { required, numeric, url } = require('vuelidate/lib/validators')

export default {
  components: {
    SaveIcon,
  },
  props: {
    configData: {
      type: Object,
      require: true,
      default: Object,
    },
    isLoading: {
      type: Boolean,
      require: true,
      default: false,
    },
    isFetching: {
      type: Boolean,
      require: true,
      default: false,
    },
  },
  data() {
    return {
      databaseData: {
        database_connection: 'mysql',
        database_hostname: '127.0.0.1',
        database_port: '3306',
        database_name: null,
        database_username: null,
        database_password: null,
        app_url: window.location.origin,
      },
      connections: ['sqlite', 'mysql', 'pgsql'],
    }
  },
  validations: {
    databaseData: {
      database_connection: {
        required,
      },
      database_hostname: {
        required,
      },
      database_port: {
        required,
        numeric,
      },
      database_name: {
        required,
      },
      database_username: {
        required,
      },
      app_url: {
        required,
        isUrl(val) {
          return this.$utils.checkValidUrl(val)
        },
      },
    },
  },
  computed: {
    urlError() {
      if (!this.$v.databaseData.app_url.$error) {
        return ''
      }

      if (!this.$v.databaseData.app_url.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.databaseData.app_url.isUrl) {
        return this.$tc('validation.invalid_url')
      }
    },
    connectionError() {
      if (!this.$v.databaseData.database_connection.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_connection.required) {
        return this.$tc('validation.required')
      }
    },
    portError() {
      if (!this.$v.databaseData.database_port.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_port.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.databaseData.database_port.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    nameError() {
      if (!this.$v.databaseData.database_name.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_name.required) {
        return this.$tc('validation.required')
      }
    },
    usernameError() {
      if (!this.$v.databaseData.database_username.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_username.required) {
        return this.$tc('validation.required')
      }
    },
    hostnameError() {
      if (!this.$v.databaseData.database_hostname.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_hostname.required) {
        return this.$tc('validation.required')
      }
    },
  },
  mounted() {
    for (const key in this.databaseData) {
      if (this.configData.hasOwnProperty(key)) {
        this.databaseData[key] = this.configData[key]
      }
    }
  },
  methods: {
    async next() {
      this.$v.databaseData.$touch()
      if (!this.$v.databaseData.$invalid) {
        this.$emit('submit-data', this.databaseData)
      }
      return false
    },
    onChangeConnection() {
      this.$v.databaseData.database_connection.$touch()
      this.$emit('on-change-driver', this.databaseData.database_connection)
    },
  },
}
</script>
