<template>
  <form @submit.prevent="submitData">
    <div class="px-8 py-6">
      <BaseInputGrid>
        <BaseInputGroup
          :label="$t('settings.disk.name')"
          :error="
            v$.doSpaceDiskConfig.name.$error &&
            v$.doSpaceDiskConfig.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="diskStore.doSpaceDiskConfig.name"
            type="text"
            name="name"
            :invalid="v$.doSpaceDiskConfig.name.$error"
            @input="v$.doSpaceDiskConfig.name.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.driver')"
          :error="
            v$.doSpaceDiskConfig.selected_driver.$error &&
            v$.doSpaceDiskConfig.selected_driver.$errors[0].$message
          "
          required
        >
          <BaseMultiselect
            v-model="selected_driver"
            :invalid="v$.doSpaceDiskConfig.selected_driver.$error"
            value-prop="value"
            :options="disks"
            searchable
            label="name"
            :can-deselect="false"
            track-by="name"
            @update:modelValue="onChangeDriver(data)"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.do_spaces_root')"
          :error="
            v$.doSpaceDiskConfig.root.$error &&
            v$.doSpaceDiskConfig.root.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.doSpaceDiskConfig.root"
            type="text"
            name="name"
            placeholder="Ex. /user/root/"
            :invalid="v$.doSpaceDiskConfig.root.$error"
            @input="v$.doSpaceDiskConfig.root.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.do_spaces_key')"
          :error="
            v$.doSpaceDiskConfig.key.$error &&
            v$.doSpaceDiskConfig.key.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.doSpaceDiskConfig.key"
            type="text"
            name="name"
            placeholder="Ex. KEIS4S39SERSDS"
            :invalid="v$.doSpaceDiskConfig.key.$error"
            @input="v$.doSpaceDiskConfig.key.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.do_spaces_secret')"
          :error="
            v$.doSpaceDiskConfig.secret.$error &&
            v$.doSpaceDiskConfig.secret.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.doSpaceDiskConfig.secret"
            type="text"
            name="name"
            placeholder="Ex. ********"
            :invalid="v$.doSpaceDiskConfig.secret.$error"
            @input="v$.doSpaceDiskConfig.secret.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.do_spaces_region')"
          :error="
            v$.doSpaceDiskConfig.region.$error &&
            v$.doSpaceDiskConfig.region.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.doSpaceDiskConfig.region"
            type="text"
            name="name"
            placeholder="Ex. nyc3"
            :invalid="v$.doSpaceDiskConfig.region.$error"
            @input="v$.doSpaceDiskConfig.region.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.do_spaces_endpoint')"
          :error="
            v$.doSpaceDiskConfig.endpoint.$error &&
            v$.doSpaceDiskConfig.endpoint.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.doSpaceDiskConfig.endpoint"
            type="text"
            name="name"
            placeholder="Ex. https://nyc3.digitaloceanspaces.com"
            :invalid="v$.doSpaceDiskConfig.endpoint.$error"
            @input="v$.doSpaceDiskConfig.endpoint.$touch()"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$t('settings.disk.do_spaces_bucket')"
          :error="
            v$.doSpaceDiskConfig.bucket.$error &&
            v$.doSpaceDiskConfig.bucket.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model.trim="diskStore.doSpaceDiskConfig.bucket"
            type="text"
            name="name"
            placeholder="Ex. my-new-space"
            :invalid="v$.doSpaceDiskConfig.bucket.$error"
            @input="v$.doSpaceDiskConfig.bucket.$touch()"
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
import { required, url, helpers } from '@vuelidate/validators'
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
        diskStore.doSpaceDiskConfig.selected_driver = value
      },
    })

    const rules = computed(() => {
      return {
        doSpaceDiskConfig: {
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
          endpoint: {
            required: helpers.withMessage(t('validation.required'), required),
            url: helpers.withMessage(t('validation.invalid_url'), url),
          },
          bucket: {
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
      diskStore.doSpaceDiskConfig = {
        name: null,
        selected_driver: 'doSpaces',
        key: null,
        secret: null,
        region: null,
        bucket: null,
        endpoint: null,
        root: null,
      }
    })

    loadData()

    async function loadData() {
      isLoading.value = true
      let data = reactive({
        disk: 'doSpaces',
      })

      if (props.isEdit) {
        Object.assign(
          diskStore.doSpaceDiskConfig,
          JSON.parse(modalStore.data.credentials)
        )
        set_as_default.value = modalStore.data.set_as_default

        if (set_as_default.value) {
          is_current_disk.value = true
        }
      } else {
        let diskData = await diskStore.fetchDiskEnv(data)
        Object.assign(diskStore.doSpaceDiskConfig, diskData.data)
      }
      selected_disk.value = props.disks.find((v) => v.value == 'doSpaces')
      isLoading.value = false
    }

    const isDisabled = computed(() => {
      return props.isEdit && set_as_default.value && is_current_disk.value
        ? true
        : false
    })

    async function submitData() {
      v$.value.doSpaceDiskConfig.$touch()
      if (v$.value.doSpaceDiskConfig.$invalid) {
        return true
      }

      let data = {
        credentials: diskStore.doSpaceDiskConfig,
        name: diskStore.doSpaceDiskConfig.name,
        driver: selected_disk.value.value,
        set_as_default: set_as_default.value,
      }
      emit('submit', data)
      return false
    }

    function onChangeDriver() {
      emit('onChangeDisk', diskStore.doSpaceDiskConfig.selected_driver)
    }

    return {
      v$,
      diskStore,
      selected_driver,
      isLoading,
      set_as_default,
      selected_disk,
      is_current_disk,
      loadData,
      submitData,
      onChangeDriver,
      isDisabled,
    }
  },
}
</script>
