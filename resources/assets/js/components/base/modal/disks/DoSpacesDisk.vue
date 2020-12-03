<template>
  <form @submit.prevent="submitData">
    <div class="px-8 py-6">
      <div class="grid gap-6 grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$t('settings.disk.name')"
          :error="nameError"
          required
        >
          <sw-input
            v-model="name"
            type="text"
            name="name"
            class="mt-2"
            :invalid="$v.name.$error"
            @input="$v.name.$touch()"
          />
        </sw-input-group>
        <sw-input-group :label="$t('settings.disk.driver')" required>
          <sw-select
            v-model="selected_disk"
            :invalid="$v.selected_disk.$error"
            :options="disks"
            :searchable="true"
            :allow-empty="false"
            :show-labels="false"
            class="mt-2"
            track-by="value"
            label="name"
            @input="onChangeDriver"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.disk.do_spaces_root')"
          :error="rootError"
          required
        >
          <sw-input
            v-model.trim="diskConfigData.root"
            :invalid="$v.diskConfigData.root.$error"
            type="text"
            name="name"
            class="mt-2"
            placeholder="Ex. /user/root/"
            @input="$v.diskConfigData.root.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.disk.do_spaces_key')"
          :error="keyError"
          required
        >
          <sw-input
            v-model.trim="diskConfigData.key"
            :invalid="$v.diskConfigData.key.$error"
            type="text"
            name="name"
            placeholder="Ex. KEIS4S39SERSDS"
            class="mt-2"
            @input="$v.diskConfigData.key.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.disk.do_spaces_secret')"
          :error="secretError"
          required
        >
          <sw-input
            v-model.trim="diskConfigData.secret"
            :invalid="$v.diskConfigData.secret.$error"
            type="text"
            name="name"
            placeholder="Ex. ********"
            class="mt-2"
            @input="$v.diskConfigData.secret.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.disk.do_spaces_region')"
          :error="regionError"
          required
        >
          <sw-input
            v-model.trim="diskConfigData.region"
            :invalid="$v.diskConfigData.region.$error"
            type="text"
            name="name"
            class="mt-2"
            placeholder="Ex. nyc3"
            @input="$v.diskConfigData.region.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.disk.do_spaces_endpoint')"
          :error="endpointError"
          required
        >
          <sw-input
            v-model.trim="diskConfigData.endpoint"
            :invalid="$v.diskConfigData.endpoint.$error"
            type="text"
            name="name"
            class="mt-2"
            placeholder="Ex. https://nyc3.digitaloceanspaces.com"
            @input="$v.diskConfigData.endpoint.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.disk.do_spaces_bucket')"
          :error="bucketError"
          required
        >
          <sw-input
            v-model.trim="diskConfigData.bucket"
            :invalid="$v.diskConfigData.bucket.$error"
            type="text"
            name="name"
            class="mt-2"
            placeholder="Ex. my-new-space"
            @input="$v.diskConfigData.bucket.$touch()"
          />
        </sw-input-group>
      </div>
      <div class="flex items-center mt-6" v-if="!isDisabled">
        <div class="relative flex items-center w-12">
          <sw-switch class="flex" v-model="set_as_default"/>
        </div>
        <div class="ml-4 right">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.disk.is_default') }}
          </p>
        </div>
      </div>
    </div>
    <slot :disk-data="{ isLoading, submitData }" />
  </form>
</template>
<script>
const { required, url } = require('vuelidate/lib/validators')
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    isEdit: {
      type: Boolean,
      require: true,
      default: false,
    },
    loading: {
      type: Boolean,
      require: true,
      default: false,
    },
    disks: {
      type: Array,
      require: true,
      default: Array,
    },
  },
  data() {
    return {
      diskConfigData: {
        selected_driver: 'doSpaces',
        key: '',
        secret: '',
        region: '',
        bucket: '',
        endpoint: '',
        root: '',
      },
      name: '',
      isLoading: false,
      set_as_default: false,
      selected_disk: null,
      is_current_disk: null,
    }
  },
  validations: {
    diskConfigData: {
      key: {
        required,
      },
      secret: {
        required,
      },
      region: {
        required,
      },
      bucket: {
        required,
      },
      root: {
        required,
      },
      endpoint: {
        required,
        url
      },
    },
    name: {
      required,
    },
    selected_disk: {
      required,
    },
  },

  computed: {
    ...mapGetters('modal', ['modalData']),

    nameError() {
      if (!this.$v.name.$error) {
        return ''
      }
      if (!this.$v.name.required) {
        return this.$tc('validation.required')
      }
    },
    typeError() {
      if (!this.$v.diskConfigData.type.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.type.required) {
        return this.$tc('validation.required')
      }
    },
    keyError() {
      if (!this.$v.diskConfigData.key.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.key.required) {
        return this.$tc('validation.required')
      }
    },
    secretError() {
      if (!this.$v.diskConfigData.secret.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.secret.required) {
        return this.$tc('validation.required')
      }
    },
    regionError() {
      if (!this.$v.diskConfigData.region.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.region.required) {
        return this.$tc('validation.required')
      }
    },
    bucketError() {
      if (!this.$v.diskConfigData.bucket.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.bucket.required) {
        return this.$tc('validation.required')
      }
    },
    rootError() {
      if (!this.$v.diskConfigData.root.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.root.required) {
        return this.$tc('validation.required')
      }
    },
    endpointError() {
      if (!this.$v.diskConfigData.endpoint.$error) {
        return ''
      }

      if (!this.$v.diskConfigData.endpoint.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.diskConfigData.endpoint.url) {
        return this.$tc('validation.invalid_url')
      }
    },
    isDisabled() {
      return (this.isEdit && this.set_as_default && this.is_current_disk) ? true : false
    }
  },

  created() {
    this.loadData()
  },

  methods: {
    ...mapActions('disks', ['fetchDiskEnv', 'updateDisk']),

    async loadData() {
      this.isLoading = true
      let data = {
        disk: 'doSpaces',
      }

      if(this.isEdit) {
        this.diskConfigData = JSON.parse(this.modalData.credentials)
        this.set_as_default = this.modalData.set_as_default
        if(this.set_as_default) {
          this.is_current_disk = true
        }
        this.name = this.modalData.name
      } else {
        let diskData = await this.fetchDiskEnv(data)

        this.diskConfigData = diskData.data
      }
      this.selected_disk = this.disks.find((v) => v.value == 'doSpaces')

      this.isLoading = false
    },

    async submitData() {
      this.$v.$touch()
      if (this.$v.$invalid) {
        return
      }

      let data = {
        credentials: this.diskConfigData,
        name: this.name,
        driver: this.selected_disk.value,
        set_as_default: this.set_as_default,
      }

      this.$emit('submit', data)

      return false
    },

    onChangeDriver() {
      this.$emit('on-change-disk', this.selected_disk)
    },
  },
}
</script>
