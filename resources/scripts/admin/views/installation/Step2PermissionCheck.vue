<template>
  <BaseWizardStep
    :title="$t('wizard.permissions.permissions')"
    :description="$t('wizard.permissions.permission_desc')"
  >
    <!-- Content Placeholders -->
    <BaseContentPlaceholders v-if="isFetchingInitialData">
      <div
        v-for="(permission, index) in 3"
        :key="index"
        class="
          grid grid-flow-row grid-cols-3
          lg:gap-24
          sm:gap-4
          border border-gray-200
        "
      >
        <BaseContentPlaceholdersText :lines="1" class="col-span-4 p-3" />
      </div>
      <BaseContentPlaceholdersBox
        :rounded="true"
        class="mt-10"
        style="width: 96px; height: 42px"
      />
    </BaseContentPlaceholders>
    <!-- End of Content Placeholder -->

    <div v-else class="relative">
      <div
        v-for="(permission, index) in permissions"
        :key="index"
        class="border border-gray-200"
      >
        <div class="grid grid-flow-row grid-cols-3 lg:gap-24 sm:gap-4">
          <div class="col-span-2 p-3">
            {{ permission.folder }}
          </div>
          <div class="p-3 text-right">
            <span
              v-if="permission.isSet"
              class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-green-500"
            />
            <span
              v-else
              class="inline-block w-4 h-4 ml-3 mr-2 rounded-full bg-red-500"
            />
            <span>{{ permission.permission }}</span>
          </div>
        </div>
      </div>

      <BaseButton
        v-show="!isFetchingInitialData"
        class="mt-10"
        :loading="isSaving"
        :disabled="isSaving"
        @click="next"
      >
        <template #left="slotProps">
          <BaseIcon name="ArrowRightIcon" :class="slotProps.class" />
        </template>
        {{ $t('wizard.continue') }}
      </BaseButton>
    </div>
  </BaseWizardStep>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useInstallationStore } from '@/scripts/admin/stores/installation'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useI18n } from 'vue-i18n'

const emit = defineEmits(['next'])

let isFetchingInitialData = ref(false)
let isSaving = ref(false)
let permissions = ref([])
const { tm, t } = useI18n()

const installationStore = useInstallationStore()
const dialogStore = useDialogStore()

onMounted(() => {
  getPermissions()
})

async function getPermissions() {
  isFetchingInitialData.value = true

  const res = await installationStore.fetchInstallationPermissions()

  permissions.value = res.data.permissions.permissions

  if (res.data && res.data.permissions.errors) {
    setTimeout(() => {
      dialogStore
        .openDialog({
          title: tm('wizard.permissions.permission_confirm_title'),
          message: t('wizard.permissions.permission_confirm_desc'),
          yesLabel: 'OK',
          noLabel: 'Cancel',
          variant: 'danger',
          hideNoButton: false,
          size: 'lg',
        })
        .then((res) => {
          if (res.data) {
            isFetchingInitialData.value = false
          }
        })
    }, 500)
  }

  isFetchingInitialData.value = false
}

function next() {
  isSaving.value = true
  emit('next')
  isSaving.value = false
}
</script>
