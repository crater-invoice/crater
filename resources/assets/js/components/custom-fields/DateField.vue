<template>
  <div>
    <base-date-picker
      v-model="date"
      :calendar-button="true"
      :invalid="isInvalid"
      :placeholder="placeholder"
      calendar-button-icon="calendar"
      @input="onDateChanged"
    />
    <span v-if="isInvalid" class="text-sm text-danger">
      {{ $t('validation.required') }}
    </span>
  </div>
</template>
<script>
import moment from 'moment'
const { required, requiredIf } = require('vuelidate/lib/validators')

export default {
  props: {
    field: {
      type: Object,
      default: null,
      required: true,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
    invalidFields: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      date: null,
      placeholder: '',
      invalidFieldIds: [],
    }
  },
  validations: {
    date: {
      required: requiredIf('isRequired'),
    },
  },
  computed: {
    isRequired() {
      if (this.field && this.field.is_required) {
        return true
      }
      return false
    },
    isInvalid() {
      if (
        this.invalidFieldIds.indexOf(this.field.cfid) >= 0 ||
        (this.$v.date.$error && !this.$v.date.required)
      ) {
        return true
      }
      return false
    },
  },
  watch: {
    invalidFields: {
      handler: 'setInvalidFieldIds',
      deep: true,
    },
    field: {
      handler: 'handleData',
      deep: true,
    },
  },
  created() {
    this.date =
      this.field && this.field.defaultAnswer && this.field.defaultAnswer
    this.placeholder =
      this.field && this.field.placeholder ? this.field.placeholder : ''
  },
  methods: {
    handleData() {
      this.date =
        this.field && this.field.defaultAnswer
          ? this.field.defaultAnswer
          : new Date()
      this.placeholder =
        this.field && this.field.placeholder ? this.field.placeholder : ''
    },
    onDateChanged(date) {
      this.$v.date.$touch()
      this.$emit('update', {
        field: this.field,
        value: moment(date).format('YYYY-MM-DD'),
      })
    },
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
  },
}
</script>
