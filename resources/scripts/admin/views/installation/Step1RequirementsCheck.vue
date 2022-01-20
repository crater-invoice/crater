<template>
  <BaseWizardStep
    :title="$t('wizard.req.system_req')"
    :description="$t('wizard.req.system_req_desc')"
  >
    <div class="w-full md:w-2/3">
      <div class="mb-6">
        <div
          v-if="phpSupportInfo"
          class="grid grid-flow-row grid-cols-3 p-3 border border-gray-200  lg:gap-24 sm:gap-4"
        >
          <div class="col-span-2 text-sm">
            {{
              $t('wizard.req.php_req_version', {
                version: phpSupportInfo.minimum,
              })
            }}
          </div>
          <div class="text-right">
            {{ phpSupportInfo.current }}
            <span
              v-if="phpSupportInfo.supported"
              class="inline-block w-4 h-4 ml-3 mr-2 bg-green-500 rounded-full"
            />
            <span
              v-else
              class="inline-block w-4 h-4 ml-3 mr-2 bg-red-500 rounded-full"
            />
          </div>
        </div>
        <div v-if="requirements">
          <div
            v-for="(requirement, index) in requirements"
            :key="index"
            class="grid grid-flow-row grid-cols-3 p-3 border border-gray-200  lg:gap-24 sm:gap-4"
          >
            <div class="col-span-2 text-sm">
              {{ index }}
            </div>
            <div class="text-right">
              <span
                v-if="requirement"
                class="inline-block w-4 h-4 ml-3 mr-2 bg-green-500 rounded-full"
              />
              <span
                v-else
                class="inline-block w-4 h-4 ml-3 mr-2 bg-red-500 rounded-full"
              />
            </div>
          </div>
        </div>
      </div>

      <BaseButton v-if="hasNext" @click="next">
        {{ $t('wizard.continue') }}
        <template #left="slotProps">
          <BaseIcon name="ArrowRightIcon" :class="slotProps.class" />
        </template>
      </BaseButton>

      <BaseButton
        v-if="!requirements"
        :loading="isSaving"
        :disabled="isSaving"
        @click="getRequirements"
      >
        {{ $t('wizard.req.check_req') }}
      </BaseButton>
    </div>
  </BaseWizardStep>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useInstallationStore } from '@/scripts/admin/stores/installation.js'

const emit = defineEmits(['next'])

const requirements = ref('')
const phpSupportInfo = ref('')
const isSaving = ref(false)
const isShow = ref(true)

const installationStore = useInstallationStore()

const hasNext = computed(() => {
  if (requirements.value) {
    let isRequired = true
    for (const key in requirements.value) {
      if (!requirements.value[key]) {
        isRequired = false
      }
      return requirements.value && phpSupportInfo.value.supported && isRequired
    }
  }
  return false
})

async function getRequirements() {
  isSaving.value = true
  const response = await installationStore.fetchInstallationRequirements()

  if (response.data) {
    requirements.value = response?.data?.requirements?.requirements?.php
    phpSupportInfo.value = response?.data?.phpSupportInfo
  }
}

function next() {
  isSaving.value = true
  emit('next')
  isSaving.value = false
}
</script>
