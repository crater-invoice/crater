<template>
  <div>
    <base-date-picker
      v-model="dateTime"
      :invalid="isInvalid"
      :enable-time="true"
      :placeholder="placeholder"
      @input="onChanged"
    />
    <span v-if="isInvalid" class="text-sm text-danger">
      {{ $t('validation.required') }}
    </span>
  </div>
</template>
<script>
const { required, requiredIf } = require('vuelidate/lib/validators')
import moment from 'moment'

export default {
  props: {
    field: {
      type: Object,
      default: null,
      required: true,
    },
    invalidFields: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      dateTime: null,
      placeholder: '',
      defaultValue: null,
      invalidFieldIds: [],
    }
  },
  validations: {
    dateTime: {
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
        (this.$v.dateTime.$error && !this.$v.dateTime.required)
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
  },
  mounted() {
    this.dateTime =
      this.field && this.field.defaultAnswer
        ? moment(this.field.defaultAnswer).format('YYYY-MM-DD H:m')
        : moment().format('YYYY-MM-DD H:m')
    this.placeholder =
      this.field && this.field.placeholder ? this.field.placeholder : ''
  },
  methods: {
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
    onChanged() {
      this.$v.dateTime.$touch()
      this.$emit('update', { field: this.field, value: this.dateTime })
    },
  },
}
</script>
