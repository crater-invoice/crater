<template>
  <form @submit.prevent="submitData">
    <div class="px-8 py-6">
      <BaseInputGrid>
        <BaseInputGroup
          :label="$t('settings.disk.name')"
          :error="
            v$.s3DiskConfigData.name.$error &&
            v$.s3DiskConfigData.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="diskStore.s3DiskConfigData.name"
            type="text"
            name="name"
            :invalid="v$.s3DiskConfigData.name.$error"
            @input="v$.s3DiskConfigData.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.driver')"
          :error="
            v$.s3DiskConfigData.selected_driver.$error &&
            v$.s3DiskConfigData.selected_driver.$errors[0].$message
          "
          required
        >
          <BaseMultiselect
            v-model="selected_driver"
            :invalid="v$.s3DiskConfigData.selected_driver.$error"
            value-prop="value"
            :options="disks"
            searchable
            label="name"
            :can-deselect="false"
            @update:modelValue="onChangeDriver(data)"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.aws_root')"
          :error="
            v$.s3DiskConfigData.root.$error &&
            v$.s3DiskConfigData.root.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.s3DiskConfigData.root"
            type="text"
            name="name"
            placeholder="Ex. /user/root/"
            :invalid="v$.s3DiskConfigData.root.$error"
            @input="v$.s3DiskConfigData.root.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.aws_key')"
          :error="
            v$.s3DiskConfigData.key.$error &&
            v$.s3DiskConfigData.key.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.s3DiskConfigData.key"
            type="text"
            name="name"
            placeholder="Ex. KEIS4S39SERSDS"
            :invalid="v$.s3DiskConfigData.key.$error"
            @input="v$.s3DiskConfigData.key.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.aws_secret')"
          :error="
            v$.s3DiskConfigData.secret.$error &&
            v$.s3DiskConfigData.secret.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.s3DiskConfigData.secret"
            type="text"
            name="name"
            placeholder="Ex. ********"
            :invalid="v$.s3DiskConfigData.secret.$error"
            @input="v$.s3DiskConfigData.secret.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.aws_region')"
          :error="
            v$.s3DiskConfigData.region.$error &&
            v$.s3DiskConfigData.region.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.s3DiskConfigData.region"
            type="text"
            name="name"
            placeholder="Ex. us-west"
            :invalid="v$.s3DiskConfigData.region.$error"
            @input="v$.s3DiskConfigData.region.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.aws_bucket')"
          :error="
            v$.s3DiskConfigData.bucket.$error &&
            v$.s3DiskConfigData.bucket.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.s3DiskConfigData.bucket"
            type="text"
            name="name"
            placeholder="Ex. AppName"
            :invalid="v$.s3DiskConfigData.bucket.$error"
            @input="v$.s3DiskConfigData.bucket.$touch()"
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

    let set_as_default = ref(false)
    let isLoading = ref(false)
    let selected_disk = ref(null)
    let is_current_disk = ref(null)

    const selected_driver = computed({
      get: () => diskStore.selected_driver,
      set: (value) => {
        diskStore.selected_driver = value
        diskStore.s3DiskConfigData.selected_driver = value
      },
    })

    const rules = computed(() => {
      return {
        s3DiskConfigData: {
          name: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          root: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          key: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          secret: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          region: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          bucket: {
            required: helpers.withMessage(t('validation.required'), required),
          },
          selected_driver: {
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
      diskStore.s3DiskConfigData = {
        name: null,
        selected_driver: 's3',
        key: null,
        secret: null,
        region: null,
        bucket: null,
        root: null,
      }
    })

    loadData()

    async function loadData() {
      isLoading.value = true
      let data = reactive({
        disk: 's3',
      })

      if (props.isEdit) {
        Object.assign(diskStore.s3DiskConfigData, modalStore.data)
        set_as_default.value = modalStore.data.set_as_default

        if (set_as_default.value) {
          is_current_disk.value = true
        }
      } else {
        let diskData = await diskStore.fetchDiskEnv(data)
        Object.assign(diskStore.s3DiskConfigData, diskData.data)
      }
      selected_disk.value = props.disks.find((v) => v.value == 's3')
      isLoading.value = false
    }

    const isDisabled = computed(() => {
      return props.isEdit && set_as_default.value && is_current_disk.value
        ? true
        : false
    })

    async function submitData() {
      v$.value.s3DiskConfigData.$touch()
      if (v$.value.s3DiskConfigData.$invalid) {
        return true
      }

      let data = {
        credentials: diskStore.s3DiskConfigData,
        name: diskStore.s3DiskConfigData.name,
        driver: selected_disk.value.value,
        set_as_default: set_as_default.value,
      }

      emit('submit', data)
      return false
    }

    function onChangeDriver() {
      emit('onChangeDisk', diskStore.s3DiskConfigData.selected_driver)
    }

    return {
      v$,
      diskStore,
      modalStore,
      set_as_default,
      isLoading,
      selected_disk,
      selected_driver,
      is_current_disk,
      loadData,
      submitData,
      onChangeDriver,
      isDisabled,
    }
  },
}
</script>
