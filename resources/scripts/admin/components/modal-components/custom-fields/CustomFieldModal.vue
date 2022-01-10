<template>
  <BaseModal :show="modalActive" @open="setData">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}

        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeCustomFieldModal"
        />
      </div>
    </template>

    <form action="" @submit.prevent="submitCustomFieldData">
      <div class="overflow-y-auto max-h-[550px]">
        <div class="px-4 md:px-8 py-8 overflow-y-auto sm:p-6">
          <BaseInputGrid layout="one-column">
            <BaseInputGroup
              :label="$t('settings.custom_fields.name')"
              required
              :error="
                v$.currentCustomField.name.$error &&
                v$.currentCustomField.name.$errors[0].$message
              "
            >
              <BaseInput
                ref="name"
                v-model="customFieldStore.currentCustomField.name"
                :invalid="v$.currentCustomField.name.$error"
                @input="v$.currentCustomField.name.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('settings.custom_fields.model')"
              :error="
                v$.currentCustomField.model_type.$error &&
                v$.currentCustomField.model_type.$errors[0].$message
              "
              :help-text="
                customFieldStore.currentCustomField.in_use
                  ? $t('settings.custom_fields.model_in_use')
                  : ''
              "
              required
            >
              <BaseMultiselect
                v-model="customFieldStore.currentCustomField.model_type"
                :options="modelTypes"
                :can-deselect="false"
                :invalid="v$.currentCustomField.model_type.$error"
                :searchable="true"
                :disabled="customFieldStore.currentCustomField.in_use"
                @input="v$.currentCustomField.model_type.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup
              class="flex items-center space-x-4"
              :label="$t('settings.custom_fields.required')"
            >
              <BaseSwitch v-model="isRequiredField" />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('settings.custom_fields.type')"
              :error="
                v$.currentCustomField.type.$error &&
                v$.currentCustomField.type.$errors[0].$message
              "
              :help-text="
                customFieldStore.currentCustomField.in_use
                  ? $t('settings.custom_fields.type_in_use')
                  : ''
              "
              required
            >
              <BaseMultiselect
                v-model="selectedType"
                :options="dataTypes"
                :invalid="v$.currentCustomField.type.$error"
                :disabled="customFieldStore.currentCustomField.in_use"
                :searchable="true"
                :can-deselect="false"
                object
                @update:modelValue="onSelectedTypeChange"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('settings.custom_fields.label')"
              required
              :error="
                v$.currentCustomField.label.$error &&
                v$.currentCustomField.label.$errors[0].$message
              "
            >
              <BaseInput
                v-model="customFieldStore.currentCustomField.label"
                :invalid="v$.currentCustomField.label.$error"
                @input="v$.currentCustomField.label.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup
              v-if="isDropdownSelected"
              :label="$t('settings.custom_fields.options')"
            >
              <OptionCreate @onAdd="addNewOption" />

              <div
                v-for="(option, index) in customFieldStore.currentCustomField
                  .options"
                :key="index"
                class="flex items-center mt-5"
              >
                <BaseInput v-model="option.name" class="w-64" />

                <BaseIcon
                  name="MinusCircleIcon"
                  class="ml-1 cursor-pointer"
                  :class="
                    customFieldStore.currentCustomField.in_use
                      ? 'text-gray-300'
                      : 'text-red-300'
                  "
                  @click="removeOption(index)"
                />
              </div>
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('settings.custom_fields.default_value')"
              class="relative"
            >
              <component
                :is="defaultValueComponent"
                v-model="customFieldStore.currentCustomField.default_answer"
                :options="customFieldStore.currentCustomField.options"
                :default-date-time="
                  customFieldStore.currentCustomField.dateTimeValue
                "
              />
            </BaseInputGroup>

            <BaseInputGroup
              v-if="!isSwitchTypeSelected"
              :label="$t('settings.custom_fields.placeholder')"
            >
              <BaseInput
                v-model="customFieldStore.currentCustomField.placeholder"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('settings.custom_fields.order')"
              :error="
                v$.currentCustomField.order.$error &&
                v$.currentCustomField.order.$errors[0].$message
              "
              required
            >
              <BaseInput
                v-model="customFieldStore.currentCustomField.order"
                type="number"
                :invalid="v$.currentCustomField.order.$error"
                @input="v$.currentCustomField.order.$touch()"
              />
            </BaseInputGroup>
          </BaseInputGrid>
        </div>
      </div>

      <div
        class="
          z-0
          flex
          justify-end
          p-4
          border-t border-solid border-gray-light border-modal-bg
        "
      >
        <BaseButton
          class="mr-3"
          type="button"
          variant="primary-outline"
          @click="closeCustomFieldModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton
          variant="primary"
          :loading="isSaving"
          :disabled="isSaving"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              :class="slotProps.class"
              name="SaveIcon"
            />
          </template>
          {{
            !customFieldStore.isEdit ? $t('general.save') : $t('general.update')
          }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { reactive, ref, computed, defineAsyncComponent } from 'vue'
import OptionCreate from './OptionsCreate.vue'
import moment from 'moment'
import useVuelidate from '@vuelidate/core'
import { required, numeric, helpers } from '@vuelidate/validators'
import { useModalStore } from '@/scripts/stores/modal'
import { useCustomFieldStore } from '@/scripts/admin/stores/custom-field'
import { useI18n } from 'vue-i18n'

const modalStore = useModalStore()
const customFieldStore = useCustomFieldStore()
const { t } = useI18n()

let isSaving = ref(false)

const modelTypes = reactive([
  'Customer',
  'Invoice',
  'Estimate',
  'Expense',
  'Payment',
])

const dataTypes = reactive([
  { label: 'Text', value: 'Input' },
  { label: 'Textarea', value: 'TextArea' },
  { label: 'Phone', value: 'Phone' },
  { label: 'URL', value: 'Url' },
  { label: 'Number', value: 'Number' },
  { label: 'Select Field', value: 'Dropdown' },
  { label: 'Switch Toggle', value: 'Switch' },
  { label: 'Date', value: 'Date' },
  { label: 'Time', value: 'Time' },
  { label: 'Date & Time', value: 'DateTime' },
])

let selectedType = ref(dataTypes[0])

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'CustomFieldModal'
})

const isSwitchTypeSelected = computed(
  () => selectedType.value && selectedType.value.label === 'Switch Toggle'
)

const isDropdownSelected = computed(
  () => selectedType.value && selectedType.value.label === 'Select Field'
)

const defaultValueComponent = computed(() => {
  if (customFieldStore.currentCustomField.type) {
    return defineAsyncComponent(() =>
      import(
        `../../custom-fields/types/${customFieldStore.currentCustomField.type}Type.vue`
      )
    )
  }

  return false
})

const isRequiredField = computed({
  get: () => customFieldStore.currentCustomField.is_required === 1,
  set: (value) => {
    const intVal = value ? 1 : 0
    customFieldStore.currentCustomField.is_required = intVal
  },
})

const rules = computed(() => {
  return {
    currentCustomField: {
      type: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      name: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      label: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      model_type: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      order: {
        required: helpers.withMessage(t('validation.required'), required),
        numeric: helpers.withMessage(t('validation.numbers_only'), numeric),
      },
      type: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => customFieldStore)
)

function setData() {
  if (customFieldStore.isEdit) {
    selectedType.value = dataTypes.find(
      (type) => type.value == customFieldStore.currentCustomField.type
    )
  } else {
    customFieldStore.currentCustomField.model_type = modelTypes[0]

    customFieldStore.currentCustomField.type = dataTypes[0].value
    selectedType.value = dataTypes[0]
  }
}

async function submitCustomFieldData() {
  v$.value.currentCustomField.$touch()

  if (v$.value.currentCustomField.$invalid) {
    return true
  }

  isSaving.value = true

  let data = {
    ...customFieldStore.currentCustomField,
  }

  if (customFieldStore.currentCustomField.options) {
    data.options = customFieldStore.currentCustomField.options.map(
      (option) => option.name
    )
  }

  if (data.type == 'Time' && typeof data.default_answer == 'object') {
    let HH =
      data && data.default_answer && data.default_answer.HH
        ? data.default_answer.HH
        : null
    let mm =
      data && data.default_answer && data.default_answer.mm
        ? data.default_answer.mm
        : null
    let ss =
      data && data.default_answer && data.default_answer.ss
        ? data.default_answer.ss
        : null

    data.default_answer = `${HH}:${mm}`
  }

  const action = customFieldStore.isEdit
    ? customFieldStore.updateCustomField
    : customFieldStore.addCustomField

  await action(data)

  isSaving.value = false

  modalStore.refreshData ? modalStore.refreshData() : ''

  closeCustomFieldModal()
}

function addNewOption(option) {
  customFieldStore.currentCustomField.options = [
    { name: option },
    ...customFieldStore.currentCustomField.options,
  ]
}

function removeOption(index) {
  if (customFieldStore.isEdit && customFieldStore.currentCustomField.in_use) {
    return
  }

  const option = customFieldStore.currentCustomField.options[index]

  if (option.name === customFieldStore.currentCustomField.default_answer) {
    customFieldStore.currentCustomField.default_answer = null
  }

  customFieldStore.currentCustomField.options.splice(index, 1)
}

function onChangeReset() {
  customFieldStore.$patch((state) => {
    state.currentCustomField.default_answer = null
    state.currentCustomField.is_required = false
    state.currentCustomField.placeholder = null
    state.currentCustomField.options = []
  })

  v$.value.$reset()
}

function onSelectedTypeChange(data) {
  customFieldStore.currentCustomField.type = data.value
}

function closeCustomFieldModal() {
  modalStore.closeModal()

  setTimeout(() => {
    customFieldStore.resetCurrentCustomField()
    v$.value.$reset()
  }, 300)
}
</script>
