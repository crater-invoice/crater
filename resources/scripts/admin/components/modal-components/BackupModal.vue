<template>
  <BaseModal :show="modalActive" @close="onCancel" @open="loadData">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="onCancel"
        />
      </div>
    </template>

    <form @submit.prevent="createNewBackup">
      <div class="p-6">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$t('settings.backup.select_backup_type')"
            :error="
              v$.currentBackupData.option.$error &&
              v$.currentBackupData.option.$errors[0].$message
            "
            horizontal
            required
            class="py-2"
          >
            <BaseMultiselect
              v-model="backupStore.currentBackupData.option"
              :options="options"
              :can-deselect="false"
              :placeholder="$t('settings.backup.select_backup_type')"
              searchable
            />
          </BaseInputGroup>
          <BaseInputGroup
            :label="$t('settings.disk.select_disk')"
            :error="
              v$.currentBackupData.selected_disk.$error &&
              v$.currentBackupData.selected_disk.$errors[0].$message
            "
            horizontal
            required
            class="py-2"
          >
            <BaseMultiselect
              v-model="backupStore.currentBackupData.selected_disk"
              :content-loading="isFetchingInitialData"
              :options="getDisksOptions"
              :searchable="true"
              :allow-empty="false"
              label="name"
              value-prop="id"
              :placeholder="$t('settings.disk.select_disk')"
              track-by="name"
              object
            />
          </BaseInputGroup>
        </BaseInputGrid>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="onCancel"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton
          :loading="isCreateLoading"
          :disabled="isCreateLoading"
          variant="primary"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isCreateLoading"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.create') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { useBackupStore } from '@/scripts/admin/stores/backup'
import { useI18n } from 'vue-i18n'
import { computed, reactive, ref } from 'vue'
import { useModalStore } from '@/scripts/stores/modal'
import { useDiskStore } from '@/scripts/admin/stores/disk'
import { required, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'

let table = ref(null)
let isSaving = ref(false)
let isCreateLoading = ref(false)
let isFetchingInitialData = ref(false)
const options = reactive(['full', 'only-db', 'only-files'])

const backupStore = useBackupStore()
const modalStore = useModalStore()
const diskStore = useDiskStore()
const { t } = useI18n()

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'BackupModal'
})

const getDisksOptions = computed(() => {
  return diskStore.disks.map((disk) => {
    return {
      ...disk,
      name: disk.name + ' — ' + '[' + disk.driver + ']',
    }
  })
})

const rules = computed(() => {
  return {
    currentBackupData: {
      option: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      selected_disk: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => backupStore)
)

async function createNewBackup() {
  v$.value.currentBackupData.$touch()
  if (v$.value.currentBackupData.$invalid) {
    return true
  }

  let data = {
    option: backupStore.currentBackupData.option,
    file_disk_id: backupStore.currentBackupData.selected_disk.id,
  }
  try {
    isCreateLoading.value = true
    let res = await backupStore.createBackup(data)
    if (res.data) {
      isCreateLoading.value = false
      modalStore.refreshData ? modalStore.refreshData() : ''
      modalStore.closeModal()
    }
  } catch (e) {
    isCreateLoading.value = false
  }
}

async function loadData() {
  isFetchingInitialData.value = true
  let res = await diskStore.fetchDisks({ limit: 'all' })
  backupStore.currentBackupData.selected_disk = res.data.data[0]
  isFetchingInitialData.value = false
}

function onCancel() {
  modalStore.closeModal()

  setTimeout(() => {
    v$.value.$reset()
    backupStore.$reset()
  })
}
</script>
