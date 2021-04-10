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
        <sw-input-group :label="$tc('settings.disk.driver')" required>
          <sw-select
            v-model="selected_disk"
            :invalid="$v.selected_disk.$error"
            :options="disks"
            :searchable="true"
            :allow-empty="false"
            :show-labels="false"
            track-by="value"
            label="name"
            class="mt-2"
            @input="onChangeDriver"
          />
        </sw-input-group>

        <sw-input-group :label="$t('settings.disk.local_root')" required>
          <sw-input
            v-model.trim="diskConfigData.root"
            type="text"
            name="name"
            placeholder="Ex. /user/root/"
            class="mt-2"
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
const { required, edisk } = require('vuelidate/lib/validators')
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
        selected_driver: 'local',
        root: '',
      },
      name: '',
      isLoading: false,
      set_as_default: false,
      selected_disk: null,
      is_current_disk: null,
      is_current_disk: null,
    }
  },
  validations: {
    diskConfigData: {
      root: {
        required,
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
        disk: 'local',
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
      this.selected_disk = this.disks.find((v) => v.value == 'local')

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
