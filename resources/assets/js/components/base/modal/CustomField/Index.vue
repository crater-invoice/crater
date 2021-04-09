<template>
  <div class="custom-field-modal">
    <form action="" @submit.prevent="submitCustomFieldData">
      <div
        class="px-8 py-8 overflow-y-auto sw-scroll sm:p-6"
        style="max-height: 600px"
      >
        <sw-input-group
          :label="$t('settings.custom_fields.name')"
          :error="nameError"
          horizontal
          required
        >
          <sw-input
            ref="name"
            :invalid="$v.formData.name.$error"
            v-model="formData.name"
            type="text"
            @input="$v.formData.name.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.custom_fields.model')"
          :error="modalTypeError"
          class="mt-5"
          horizontal
          required
        >
          <sw-select
            v-model="formData.model_type"
            :options="modelTypes"
            :invalid="$v.formData.model_type.$error"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            @input="$v.formData.model_type.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.custom_fields.required')"
          class="mt-5"
          horizontal
        >
          <sw-switch v-model="formData.is_required" style="margin-top: -20px" />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.custom_fields.type')"
          :error="dataTypeError"
          class="mt-5"
          horizontal
          required
        >
          <sw-select
            v-model="selectType"
            :options="dataTypes"
            :invalid="$v.selectType.$error"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            track-by="label"
            label="label"
            @input="onSelectTypeChange"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.custom_fields.label')"
          :error="labelError"
          class="mt-5"
          horizontal
          required
        >
          <sw-input
            ref="name"
            :invalid="$v.formData.label.$error"
            v-model="formData.label"
            type="text"
            @input="$v.formData.label.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          v-if="isDropdownSelected"
          :label="$t('settings.custom_fields.options')"
          class="mt-5"
          horizontal
        >
          <option-create @onAdd="addNewOptions" />
          <div
            v-for="(option, index) in formData.options"
            :key="index"
            class="flex items-center"
            style="margin-top: 5px"
          >
            <sw-input v-model="option.name" type="text" style="width: 90%" />
            <minus-circle-icon
              class="ml-1 cursor-pointer icon text-danger"
              @click="removeOption(index)"
            />
          </div>
        </sw-input-group>
        <sw-input-group
          v-if="formData.type"
          :label="$t('settings.custom_fields.default_value')"
          horizontal
          class="relative mt-5"
        >
          <component
            :value="formData.default_answer"
            :is="formData.type + 'Type'"
            :options="formData.options"
            :default-date-time="formData.dateTimeValue"
            v-model="formData.default_answer"
          />
        </sw-input-group>
        <sw-input-group
          v-if="!isSwitchTypeSelected"
          :label="$t('settings.custom_fields.placeholder')"
          class="mt-5"
          horizontal
        >
          <sw-input v-model="formData.placeholder" type="text" />
        </sw-input-group>
        <sw-input-group
          :label="$t('settings.custom_fields.order')"
          :error="orderError"
          class="mt-5"
          horizontal
        >
          <sw-input
            v-model="formData.order"
            :invalid="$v.formData.order.$error"
            type="number"
            @input="$v.formData.order.$touch()"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-solid border-gray-light border-modal-bg"
      >
        <sw-button
          class="mr-3"
          type="button"
          variant="primary-outline"
          @click="closeCategoryModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
        >
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ !isEdit ? $t('general.save') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { MinusCircleIcon } from '@vue-hero-icons/solid'
import InputType from './types/InputType'
import NumberType from './types/NumberType'
import SwitchType from './types/SwitchType'
import TextAreaType from './types/TextAreaType'
import TimeType from './types/TimeType'
import UrlType from './types/UrlType'
import PhoneType from './types/PhoneType'
import DateTimeType from './types/DateTimeType'
import DateType from './types/DateType'
import DropdownType from './types/DropdownType'

import OptionCreate from './types/OptionsCreate'
import moment from 'moment'

const {
  required,
  requiredIf,
  numeric,
  minLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    MinusCircleIcon,
    OptionCreate,
    InputType,
    NumberType,
    SwitchType,
    TextAreaType,
    TimeType,
    UrlType,
    PhoneType,
    DateTimeType,
    DateType,
    DropdownType,
  },
  data() {
    return {
      isEdit: false,
      dateType: 'custom',
      isLoading: false,
      relativeDateTypes: [
        'Today',
        'Tomorrow',
        'Yesterday',
        'Starting Date of Week',
        'Ending Date of Week',
        'Starting Date of Next Week',
        'Ending Date of Next Week',
        'Starting Date of Previous Week',
        'Ending Date of Previous Week',
        'Starting Date of Month',
        'Ending Date of Month',
        'Starting Date of Next Month',
        'Ending Date of Next Month',
        'Starting Date of Previous Month',
        'Ending Date of Previous Month',
        'Starting Date of Fiscal Month',
        'Ending Date of Fiscal Month',
      ],
      dataTypes: [
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
      ],
      modelTypes: ['Customer', 'Invoice', 'Estimate', 'Expense', 'Payment'],
      selectType: null,
      formData: {
        label: null,
        type: null,
        name: null,
        default_answer: null,
        is_required: false,
        placeholder: null,
        model_type: null,
        order: 1,
        options: [],
      },
    }
  },
  validations: {
    selectType: {
      required,
    },
    formData: {
      name: {
        required,
      },
      label: {
        required,
      },
      model_type: {
        required,
      },
      order: {
        required,
        numeric,
      },
      options: {
        required: requiredIf('isDropdownSelected'),
        minLength: minLength(1),
        $each: {
          name: {
            required: requiredIf('isDropdownSelected'),
            minLength: minLength(1),
          },
        },
      },
    },
  },
  computed: {
    ...mapGetters('modal', ['modalData', 'modalDataID', 'refreshData']),
    isDropdownSelected() {
      if (this.selectType && this.selectType.label === 'Select Field') {
        return true
      }
      return false
    },
    isSwitchTypeSelected() {
      if (this.selectType && this.selectType.label === 'Switch Toggle') {
        return true
      }
      return false
    },
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }

      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    labelError() {
      if (!this.$v.formData.label.$error) {
        return ''
      }

      if (!this.$v.formData.label.required) {
        return this.$tc('validation.required')
      }
    },
    modalTypeError() {
      if (!this.$v.formData.model_type.$error) {
        return ''
      }

      if (!this.$v.formData.model_type.required) {
        return this.$tc('validation.required')
      }
    },
    dataTypeError() {
      if (!this.$v.selectType.$error) {
        return ''
      }

      if (!this.$v.selectType.required) {
        return this.$tc('validation.required')
      }
    },
    hasPlaceHolder() {
      if (this.selectType.label == 'Switch Toggle') {
        return false
      }
      return true
    },
    orderError() {
      if (!this.$v.formData.order.$error) {
        return ''
      }

      if (!this.$v.formData.order.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.order.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
  },
  watch: {
    'formData.type'(newValue, oldvalue) {
      if (oldvalue != null || oldvalue != undefined) {
        this.onChangeReset()
      }
    },
    dateType(newValue, oldvalue) {
      if (oldvalue != null || oldvalue != undefined) {
        this.onChangeReset()
      }
    },
  },
  mounted() {
    if (this.modalDataID) {
      this.setData()
      return true
    }
    this.formData.model_type = this.modelTypes[0]
    this.selectType = this.dataTypes[0]
    this.formData.type = this.dataTypes[0].value
  },
  methods: {
    ...mapActions('customFields', [
      'addCustomField',
      'updateCustomField',
      'fetchCustomField',
    ]),
    ...mapActions('modal', ['closeModal']),
    ...mapActions('notification', ['showNotification']),
    resetFormData() {
      this.formData = {
        label: null,
        label: null,
        type: null,
        dateTimeValue: null,
        default_answer: null,
        is_required: false,
        placeholder: null,
        model_type: null,
        options: [{ name: '' }],
      }
      this.$v.$reset()
    },
    async submitCustomFieldData() {
      this.$v.selectType.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      let data = {
        ...this.formData,
        options: this.formData.options.map((option) => option.name),
        default_answer:
          this.isDropdownSelected && this.formData.default_answer
            ? this.formData.default_answer.name
            : this.formData.default_answer,
      }
      if (this.isSwitchTypeSelected && this.formData.default_answer == null) {
        data.default_answer = false
      }
      if (data.type == 'Date') {
        data.default_answer = data.default_answer
          ? moment(data.default_answer).format('YYYY-MM-DD')
          : null
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
        data.default_answer = `${HH}:${mm}:${ss}`
      }
      let response = null
      if (this.isEdit) {
        this.isLoading = true
        response = await this.updateCustomField(data)
        this.showNotification({
          type: 'success',
          message: this.$tc('settings.custom_fields.updated_message'),
        })
        this.refreshData()
        this.closeCategoryModal()
        return true
      }

      this.isLoading = true
      response = await this.addCustomField(data)

      this.showNotification({
        type: 'success',
        message: this.$tc('settings.custom_fields.added_message'),
      })
      this.refreshData()
      this.closeCategoryModal()
      return true
    },
    addNewOptions(option) {
      this.formData.options = [{ name: option }, ...this.formData.options]
    },
    removeOption(index) {
      this.formData.options.splice(index, 1)
    },
    async setData() {
      let response = await this.fetchCustomField(this.modalDataID)
      let fieldData = response.data.customField
      this.isEdit = true
      let data = {
        ...fieldData,
        options: [],
        dateTimeValue: fieldData.defaultAnswer,
        default_answer: fieldData.defaultAnswer,
        options: fieldData.options
          ? fieldData.options.map((option) => {
              return { name: option ? option : '' }
            })
          : [],
      }
      this.selectType = this.dataTypes.find(
        (type) => type.value == fieldData.type
      )
      if (data.type == 'Dropdown') {
        data.default_answer = { name: fieldData.defaultAnswer }
      }
      this.formData = { ...data }
    },
    onChangeReset() {
      this.formData = {
        ...this.formData,
        default_answer: null,
        is_required: false,
        placeholder: null,
        options: [],
      }
    },
    onSelectTypeChange(data) {
      this.formData.type = data.value
      this.$v.selectType.$touch()
    },
    closeCategoryModal() {
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>
