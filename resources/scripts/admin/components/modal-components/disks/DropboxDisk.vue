<template>
  <form @submit.prevent="submitData">
    <div class="px-8 py-6">
      <BaseInputGrid>
        <BaseInputGroup
          :label="$t('settings.disk.name')"
          :error="
            v$.dropBoxDiskConfig.name.$error &&
            v$.dropBoxDiskConfig.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="diskStore.dropBoxDiskConfig.name"
            type="text"
            name="name"
            :invalid="v$.dropBoxDiskConfig.name.$error"
            @input="v$.dropBoxDiskConfig.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.driver')"
          :error="
            v$.dropBoxDiskConfig.selected_driver.$error &&
            v$.dropBoxDiskConfig.selected_driver.$errors[0].$message
          "
          required
        >
          <BaseMultiselect
            v-model="selected_driver"
            :invalid="v$.dropBoxDiskConfig.selected_driver.$error"
            value-prop="value"
            :options="disks"
            searchable
            label="name"
            :can-deselect="false"
            @update:modelValue="onChangeDriver(data)"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.dropbox_root')"
          :error="
            v$.dropBoxDiskConfig.root.$error &&
            v$.dropBoxDiskConfig.root.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.dropBoxDiskConfig.root"
            type="text"
            name="name"
            placeholder="Ex. /user/root/"
            :invalid="v$.dropBoxDiskConfig.root.$error"
            @input="v$.dropBoxDiskConfig.root.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.dropbox_token')"
          :error="
            v$.dropBoxDiskConfig.token.$error &&
            v$.dropBoxDiskConfig.token.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.dropBoxDiskConfig.token"
            type="text"
            name="name"
            :invalid="v$.dropBoxDiskConfig.token.$error"
            @input="v$.dropBoxDiskConfig.token.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.dropbox_key')"
          :error="
            v$.dropBoxDiskConfig.key.$error &&
            v$.dropBoxDiskConfig.key.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.dropBoxDiskConfig.key"
            type="text"
            name="name"
            placeholder="Ex. KEIS4S39SERSDS"
            :invalid="v$.dropBoxDiskConfig.key.$error"
            @input="v$.dropBoxDiskConfig.key.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.dropbox_secret')"
          :error="
            v$.dropBoxDiskConfig.secret.$error &&
            v$.dropBoxDiskConfig.secret.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.dropBoxDiskConfig.secret"
            type="text"
            name="name"
            placeholder="Ex. ********"
            :invalid="v$.dropBoxDiskConfig.secret.$error"
            @input="v$.dropBoxDiskConfig.secret.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.dropbox_app')"
          :error="
            v$.dropBoxDiskConfig.app.$error &&
            v$.dropBoxDiskConfig.app.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.dropBoxDiskConfig.app"
            type="text"
            name="name"
            :invalid="v$.dropBoxDiskConfig.app.$error"
            @input="v$.dropBoxDiskConfig.app.$touch()"
          />
        </BaseInputGroup>
      </BaseInputGrid>
      <div v-if="!isDisabled" class="flex items-center mt-6">
        <div class="relative flex items-center w-12">
          <BaseSwitch v-model="set_as_default" class="flex" />
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
import { useDiskStore } from '@/scripts/admin/stores/disk'
import { useModalStore } from '@/scripts/stores/modal'
import { reactive, ref, computed, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import useVuelidate from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
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

  emits: ['submit', 'onChangeDisk'],

  setup(props, { emit }) {
    const diskStore = useDiskStore()
    const modalStore = useModalStore()
    const { t } = useI18n()

    let set_as_default = ref(false)
    let isLoading = ref(false)
    let is_current_disk = ref(null)
    let selected_disk = ref(null)

    const selected_driver = computed({
      get: () => diskStore.selected_driver,
      set: (value) => {
        diskStore.selected_driver = value
        diskStore.dropBoxDiskConfig.selected_driver = value
      },
    })

    const rules = computed(() => {
      return {
        dropBoxDiskConfig: {
          root: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          key: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          secret: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          token: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          app: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          selected_driver: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          name: {
            required: helpers.withMessage(t('validation.required'), required),
          },
        },
      }
    })

    const v$ = useVuelidate(
      rules,
      computed(() => diskStore)
    )

    onBeforeUnmount(() => {
      diskStore.dropBoxDiskConfig = {
        name: null,
        selected_driver: 'dropbox',
        token: null,
        key: null,
        secret: null,
        app: null,
      }
    })

    loadData()

    async function loadData() {
      isLoading.value = true
      let data = reactive({
        disk: 'dropbox',
      })

      if (props.isEdit) {
        Object.assign(diskStore.dropBoxDiskConfig, modalStore.data)
        set_as_default.value = modalStore.data.set_as_default

        if (set_as_default.value) {
          is_current_disk.value = true
        }
      } else {
        let diskData = await diskStore.fetchDiskEnv(data)
        Object.assign(diskStore.dropBoxDiskConfig, diskData.data)
      }
      selected_disk.value = props.disks.find((v) => v.value == 'dropbox')
      isLoading.value = false
    }

    const isDisabled = computed(() => {
      return props.isEdit && set_as_default.value && is_current_disk.value
        ? true
        : false
    })

    async function submitData() {
      v$.value.dropBoxDiskConfig.$touch()
      if (v$.value.dropBoxDiskConfig.$invalid) {
        return true
      }
      let data = {
        credentials: diskStore.dropBoxDiskConfig,
        name: diskStore.dropBoxDiskConfig.name,
        driver: selected_disk.value.value,
        set_as_default: set_as_default.value,
      }

      emit('submit', data)
      return false
    }

    function onChangeDriver() {
      emit('onChangeDisk', diskStore.dropBoxDiskConfig.selected_driver)
    }

    return {
      v$,
      diskStore,
      selected_driver,
      set_as_default,
      isLoading,
      is_current_disk,
      selected_disk,
      isDisabled,
      loadData,
      submitData,
      onChangeDriver,
    }
  },
}
</script>
