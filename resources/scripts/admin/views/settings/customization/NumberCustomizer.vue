<template>
  <h6 class="text-gray-900 text-lg font-medium">
    {{ $t(`settings.customization.${type}s.${type}_number_format`) }}
  </h6>
  <p class="mt-1 text-sm text-gray-500">
    {{
      $t(`settings.customization.${type}s.${type}_number_format_description`)
    }}
  </p>

  <div class="overflow-x-auto">
    <table class="w-full mt-6 table-fixed">
      <colgroup>
        <col style="width: 4%" />
        <col style="width: 45%" />
        <col style="width: 27%" />
        <col style="width: 24%" />
      </colgroup>

      <thead>
        <tr>
          <th
            class="
              px-5
              py-3
              text-sm
              not-italic
              font-medium
              leading-5
              text-left text-gray-700
              border-t border-b border-gray-200 border-solid
            "
          ></th>
          <th
            class="
              px-5
              py-3
              text-sm
              not-italic
              font-medium
              leading-5
              text-left text-gray-700
              border-t border-b border-gray-200 border-solid
            "
          >
            Component
          </th>
          <th
            class="
              px-5
              py-3
              text-sm
              not-italic
              font-medium
              leading-5
              text-left text-gray-700
              border-t border-b border-gray-200 border-solid
            "
          >
            Parameter
          </th>
          <th
            class="
              px-5
              py-3
              text-sm
              not-italic
              font-medium
              leading-5
              text-left text-gray-700
              border-t border-b border-gray-200 border-solid
            "
          ></th>
        </tr>
      </thead>
      <draggable
        v-model="selectedFields"
        class="divide-y divide-gray-200"
        item-key="id"
        tag="tbody"
        handle=".handle"
        filter=".ignore-element"
      >
        <template #item="{ element }">
          <tr class="relative">
            <td class="text-gray-300 cursor-move handle align-middle">
              <DragIcon />
            </td>
            <td class="px-5 py-4">
              <label
                class="
                  block
                  text-sm
                  not-italic
                  font-medium
                  text-primary-800
                  whitespace-nowrap
                  mr-2
                  min-w-[200px]
                "
              >
                {{ element.label }}
              </label>

              <p class="text-xs text-gray-500 mt-1">
                {{ element.description }}
              </p>
            </td>
            <td class="px-5 py-4 text-left align-middle">
              <BaseInputGroup
                :label="element.paramLabel"
                class="lg:col-span-3"
                required
              >
                <BaseInput
                  v-model="element.value"
                  :disabled="element.inputDisabled"
                  :type="element.inputType"
                  @update:modelValue="onUpdate($event, element)"
                />
              </BaseInputGroup>
            </td>

            <td class="px-5 py-4 text-right align-middle pt-10">
              <BaseButton
                variant="white"
                @click.prevent="removeComponent(element)"
              >
                Remove
                <template #left="slotProps">
                  <BaseIcon
                    name="XIcon"
                    class="!sm:m-0"
                    :class="slotProps.class"
                  />
                </template>
              </BaseButton>
            </td>
          </tr>
        </template>
        <template #footer>
          <tr>
            <td colspan="2" class="px-5 py-4">
              <BaseInputGroup
                :label="
                  $t(`settings.customization.${type}s.preview_${type}_number`)
                "
              >
                <BaseInput
                  v-model="nextNumber"
                  disabled
                  :loading="isFetchingNextNumber"
                />
              </BaseInputGroup>
            </td>
            <td class="px-5 py-4 text-right align-middle" colspan="2">
              <BaseDropdown wrapper-class="flex items-center justify-end mt-5">
                <template #activator>
                  <BaseButton variant="primary-outline">
                    <template #left="slotProps">
                      <BaseIcon :class="slotProps.class" name="PlusIcon" />
                    </template>

                    {{ $t('settings.customization.add_new_component') }}
                  </BaseButton>
                </template>

                <BaseDropdownItem
                  v-for="field in computedFields"
                  :key="field.label"
                  @click.prevent="onSelectField(field)"
                >
                  {{ field.label }}
                </BaseDropdownItem>
              </BaseDropdown>
            </td>
          </tr>
        </template>
      </draggable>
    </table>
  </div>

  <BaseButton
    :loading="isSaving"
    :disabled="isSaving"
    variant="primary"
    type="submit"
    class="mt-4"
    @click="submitForm"
  >
    <template #left="slotProps">
      <BaseIcon v-if="!isSaving" :class="slotProps.class" name="SaveIcon" />
    </template>
    {{ $t('settings.customization.save') }}
  </BaseButton>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'
import Guid from 'guid'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useGlobalStore } from '@/scripts/admin/stores/global'

import DragIcon from '@/scripts/components/icons/DragIcon.vue'

const props = defineProps({
  type: {
    type: String,
    required: true,
  },
  typeStore: {
    type: Object,
    required: true,
  },
  defaultSeries: {
    type: String,
    default: 'INV',
  },
})

const { t } = useI18n()
const companyStore = useCompanyStore()
const globalStore = useGlobalStore()

const selectedFields = ref([])
const isSaving = ref(false)

const allFields = ref([
  {
    label: t('settings.customization.series'),
    description: t('settings.customization.series_description'),
    name: 'SERIES',
    paramLabel: t('settings.customization.series_param_label'),
    value: props.defaultSeries,
    inputDisabled: false,
    inputType: 'text',
    allowMultiple: false,
  },
  {
    label: t('settings.customization.sequence'),
    description: t('settings.customization.sequence_description'),
    name: 'SEQUENCE',
    paramLabel: t('settings.customization.sequence_param_label'),
    value: '6',
    inputDisabled: false,
    inputType: 'number',
    allowMultiple: false,
  },
  {
    label: t('settings.customization.delimiter'),
    description: t('settings.customization.delimiter_description'),
    name: 'DELIMITER',
    paramLabel: t('settings.customization.delimiter_param_label'),
    value: '-',
    inputDisabled: false,
    inputType: 'text',
    allowMultiple: true,
  },
  {
    label: t('settings.customization.customer_series'),
    description: t('settings.customization.customer_series_description'),
    name: 'CUSTOMER_SERIES',
    paramLabel: '',
    value: '',
    inputDisabled: true,
    inputType: 'text',
    allowMultiple: false,
  },
  {
    label: t('settings.customization.customer_sequence'),
    description: t('settings.customization.customer_sequence_description'),
    name: 'CUSTOMER_SEQUENCE',
    paramLabel: t('settings.customization.customer_sequence_param_label'),
    value: '6',
    inputDisabled: false,
    inputType: 'number',
    allowMultiple: false,
  },
  {
    label: t('settings.customization.date_format'),
    description: t('settings.customization.date_format_description'),
    name: 'DATE_FORMAT',
    paramLabel: t('settings.customization.date_format_param_label'),
    value: 'Y',
    inputDisabled: false,
    inputType: 'text',
    allowMultiple: true,
  },
  {
    label: t('settings.customization.random_sequence'),
    description: t('settings.customization.random_sequence_description'),
    name: 'RANDOM_SEQUENCE',
    paramLabel: t('settings.customization.random_sequence_param_label'),
    value: '6',
    inputDisabled: false,
    inputType: 'number',
    allowMultiple: false,
  },
])

const computedFields = computed(() => {
  return allFields.value.filter(function (obj) {
    return !selectedFields.value.some(function (obj2) {
      if (obj.allowMultiple) {
        return false
      }

      return obj.name == obj2.name
    })
  })
})

const nextNumber = ref('')
const isFetchingNextNumber = ref(false)
const isLoadingPlaceholders = ref(false)

const getNumberFormat = computed(() => {
  let format = ''

  selectedFields.value.forEach((field) => {
    let fieldString = `{{${field.name}`

    if (field.value) {
      fieldString += `:${field.value}`
    }

    format += `${fieldString}}}`
  })

  return format
})

watch(selectedFields, (val) => {
  fetchNextNumber()
})

setInitialFields()

async function setInitialFields() {
  let data = {
    format: companyStore.selectedCompanySettings[`${props.type}_number_format`],
  }

  isLoadingPlaceholders.value = true

  let res = await globalStore.fetchPlaceholders(data)

  res.data.placeholders.forEach((placeholder) => {
    let found = allFields.value.find((field) => {
      return field.name === placeholder.name
    })

    const value = placeholder.value ?? ''

    selectedFields.value.push({ ...found, value, id: Guid.raw() })
  })

  isLoadingPlaceholders.value = false

  fetchNextNumber()
}

function isFieldAdded(field) {
  return selectedFields.value.find((v) => v.name === field.name)
}

function onSelectField(field) {
  if (isFieldAdded(field) && !field.allowMultiple) {
    return
  }

  selectedFields.value.push({ ...field, id: Guid.raw() })

  fetchNextNumber()
}

function removeComponent(component) {
  selectedFields.value = selectedFields.value.filter(function (el) {
    return component.id !== el.id
  })
}

function onUpdate(val, element) {
  switch (element.name) {
    case 'SERIES':
      if (val.length >= 6) {
        val = val.substring(0, 6)
      }
      break
    case 'DELIMITER':
      if (val.length >= 1) {
        val = val.substring(0, 1)
      }
      break
  }

  setTimeout(() => {
    element.value = val

    fetchNextNumber()
  }, 100)
}

const fetchNextNumber = useDebounceFn(() => {
  getNextNumber()
}, 500)

async function getNextNumber() {
  if (!getNumberFormat.value) {
    nextNumber.value = ''
    return
  }

  let data = {
    key: props.type,
    format: getNumberFormat.value,
  }

  isFetchingNextNumber.value = true

  let res = await props.typeStore.getNextNumber(data)

  isFetchingNextNumber.value = false

  if (res.data) {
    nextNumber.value = res.data.nextNumber
  }
}

async function submitForm() {
  if (isFetchingNextNumber.value || isLoadingPlaceholders.value) {
    return
  }

  isSaving.value = true

  let data = { settings: {} }

  data.settings[props.type + '_number_format'] = getNumberFormat.value

  await companyStore.updateCompanySettings({
    data,
    message: `settings.customization.${props.type}s.${props.type}_settings_updated`,
  })

  isSaving.value = false

  return true
}
</script>
