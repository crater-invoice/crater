import InputField from '../components/custom-fields/InputField.vue'
import SwitchField from '../components/custom-fields/SwitchField'
import TimeField from '../components/custom-fields/TimeField'
import DropdownField from '../components/custom-fields/DropdownField'
import DateTimeField from '../components/custom-fields/DateTimeField'
import DateField from '../components/custom-fields/DateField'
import TextAreaField from '../components/custom-fields/TextAreaField'
import UrlField from '../components/custom-fields/UrlField.vue'
import PhoneField from '../components/custom-fields/PhoneField.vue'
import NumberField from '../components/custom-fields/NumberField.vue'

import { mapActions } from 'vuex'

export default {
  components: {
    InputField,
    SwitchField,
    TimeField,
    DropdownField,
    DateTimeField,
    DateField,
    TextAreaField,
    UrlField,
    PhoneField,
    NumberField,
  },
  data() {
    return {
      formData: {
        customFields: [],
      },
      invalidFields: [],
      customFields: [],
    }
  },
  methods: {
    ...mapActions('customFields', ['fetchCustomFields']),
    async setInitialCustomFields(type = null) {
      let response = await this.fetchCustomFields({ type: type, limit: 'all' })
      this.customFields = response.data.customFields.data.map((_f) => {
        return { ..._f, cfid: _f.id }
      })
      this.setRemainingCustomFieldsValue()
    },
    setEditCustomFields(fields, customFields) {
      this.customFields = fields.map((field) => {
        field.label = field.custom_field.label
        field.cfid = field.custom_field.id
        field.options = field.custom_field.options
        field.placeholder = field.custom_field.placeholder
        field.is_required = field.custom_field.is_required
        field.order = field.custom_field.order
        return field
      })

      let currentCustomFieldIds = customFields.map((field) => field.id)
      let editCustomFieldIds = fields.map((field) => field.custom_field_id)
      let remainingCustomFieldIds = this.$utils.arrayDifference(
        currentCustomFieldIds,
        editCustomFieldIds
      )
      remainingCustomFieldIds.forEach((id) => {
        let data = customFields.find((field) => field.id == id)
        this.customFields.push({ ...data, cfid: data.id })
      })

      this.setRemainingCustomFieldsValue(true)
    },
    setCustomFieldValue(data) {
      let position = this.formData.customFields.findIndex(
        (field) => field.id == data.field.cfid
      )

      // check field has value so removed in invalidFields array
      let indexInInvalidField = this.invalidFields.findIndex(
        (field) => field.id == data.field.cfid
      )
      if (indexInInvalidField >= 0) {
        if (data.value) {
          this.invalidFields.splice(indexInInvalidField, 1)
        }
      }

      // set data in formData.customFields
      if (position >= 0) {
        this.formData.customFields[position].value = data.value
        return true
      }
      this.formData.customFields.push({
        id: data.field.cfid,
        value: data.value,
      })
      return true
    },
    setRemainingCustomFieldsValue() {
      let existingCustomFieldIds = this.formData.customFields.map((_f) => _f.id)
      let customFieldIds = this.customFields.map((_f) => _f.cfid)
      let remainingCustomFieldIds = this.$utils.arrayDifference(
        customFieldIds,
        existingCustomFieldIds
      )
      remainingCustomFieldIds.forEach((id) => {
        let field = this.customFields.find((field) => field.cfid == id)
        this.formData.customFields.push({
          id: field.cfid,
          isRequired: field.is_required,
          value: field.defaultAnswer,
        })
      })
      this.customFields = _.sortBy(this.customFields, (_cf) => _cf.order)
    },
    getInvalidFields() {
      return this.formData.customFields.filter(
        (field) =>
          field.isRequired &&
          (field.value == null || field.value == undefined || field.value == '')
      )
    },
    touchCustomField() {
      return new Promise((resolve, reject) => {
        try {
          if (this.getInvalidFields() <= 0) {
            resolve({ error: false })
          }
          this.invalidFields = this.getInvalidFields()
          resolve({ error: true })
        } catch (error) {
          reject(error)
        }
      })
    },
  },
}
