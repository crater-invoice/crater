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
            :allow-empty="false"
            :searchable="true"
            :show-labels="false"
            @input="onChangeConnection"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('wizard.database.db_path')"
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
const { required } = require('vuelidate/lib/validators')

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
        database_name: null,
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
      database_name: {
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
    nameError() {
      if (!this.$v.databaseData.database_name.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_name.required) {
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
