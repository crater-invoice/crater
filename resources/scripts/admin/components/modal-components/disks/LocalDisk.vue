<template>
  <form action="" @submit.prevent="submitData">
    <div class="px-4 sm:px-8 py-6">
      <BaseInputGrid>
        <BaseInputGroup
          :label="$t('settings.disk.name')"
          :error="
            v$.localDiskConfig.name.$error &&
            v$.localDiskConfig.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="diskStore.localDiskConfig.name"
            type="text"
            name="name"
            :invalid="v$.localDiskConfig.name.$error"
            @input="v$.localDiskConfig.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.driver')"
          :error="
            v$.localDiskConfig.selected_driver.$error &&
            v$.localDiskConfig.selected_driver.$errors[0].$message
          "
          required
        >
          <BaseMultiselect
            v-model="selected_driver"
            value-prop="value"
            :invalid="v$.localDiskConfig.selected_driver.$error"
            :options="disks"
            searchable
            label="name"
            :can-deselect="false"
            @update:modelValue="onChangeDriver(data)"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.local_root')"
          :error="
            v$.localDiskConfig.root.$error &&
            v$.localDiskConfig.root.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.localDiskConfig.root"
            type="text"
            name="name"
            :invalid="v$.localDiskConfig.root.$error"
            placeholder="Ex./user/root/"
            @input="v$.localDiskConfig.root.$touch()"
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
import { computed, onBeforeUnmount, reactive, ref } from 'vue'
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

    let isLoading = ref(false)
    let set_as_default = ref(false)
    let selected_disk = ref('')
    let is_current_disk = ref(null)

    const selected_driver = computed({
      get: () => diskStore.selected_driver,
      set: (value) => {
        diskStore.selected_driver = value
        diskStore.localDiskConfig.selected_driver = value
      },
    })

    const rules = computed(() => {
      return {
        localDiskConfig: {
          name: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          selected_driver: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          root: {
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
      diskStore.localDiskConfig = {
        name: null,
        selected_driver: 'local',
        root: null,
      }
    })

    loadData()

    async function loadData() {
      isLoading.value = true

      let data = reactive({
        disk: 'local',
      })

      if (props.isEdit) {
        Object.assign(diskStore.localDiskConfig, modalStore.data)
        diskStore.localDiskConfig.root = modalStore.data.credentials
        set_as_default.value = modalStore.data.set_as_default

        if (set_as_default.value) {
          is_current_disk.value = true
        }
      } else {
        let diskData = await diskStore.fetchDiskEnv(data)
        Object.assign(diskStore.localDiskConfig, diskData.data)
      }

      selected_disk.value = props.disks.find((v) => v.value == 'local')
      isLoading.value = false
    }

    const isDisabled = computed(() => {
      return props.isEdit && set_as_default.value && is_current_disk.value
        ? true
        : false
    })

    async function submitData() {
      v$.value.localDiskConfig.$touch()

      if (v$.value.localDiskConfig.$invalid) {
        return true
      }

      let data = reactive({
        credentials: diskStore.localDiskConfig.root,
        name: diskStore.localDiskConfig.name,
        driver: diskStore.localDiskConfig.selected_driver,
        set_as_default: set_as_default.value,
      })

      emit('submit', data)
      return false
    }

    function onChangeDriver() {
      emit('onChangeDisk', diskStore.localDiskConfig.selected_driver)
    }

    return {
      v$,
      diskStore,
      modalStore,
      selected_driver,
      selected_disk,
      isLoading,
      set_as_default,
      is_current_disk,
      submitData,
      onChangeDriver,
      isDisabled,
    }
  },
}
</script>
