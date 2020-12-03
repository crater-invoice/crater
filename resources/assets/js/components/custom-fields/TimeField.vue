<template>
  <div>
    <base-time-picker
      v-model="time"
      :set-value="defaultValue"
      :invalid="$v.time.$error"
      :placeholder="placeholder"
      :tabindex="tabindex"
      hide-clear-button
      @input="onTimeSelect"
    />
    <div v-if="$v.time.$error">
      <span v-if="!$v.time.required" class="text-sm text-danger">
        {{ $t('validation.required') }}
      </span>
    </div>
  </div>
</template>
<script>
const { required, requiredIf } = require('vuelidate/lib/validators')

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
    tabindex: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      time: null,
      defaultValue: '00:00:00',
      placeholder: '',
      invalidFieldIds: [],
    }
  },
  validations: {
    time: {
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
        (this.$v.inputValue.$error && !this.$v.inputValue.required)
      ) {
        return true
      }
      return false
    },
  },
  watch: {
    field: {
      handler: 'handleData',
      deep: true,
    },
    invalidFields: {
      handler: 'setInvalidFieldIds',
      deep: true,
    },
  },
  mounted() {
    this.placeholder =
      this.field && this.field.placeholder ? this.field.placeholder : ''
    this.handleData()
  },
  methods: {
    handleData() {
      this.defaultValue =
        this.field && this.field.defaultAnswer
          ? this.field.defaultAnswer
          : '00-00-00'
      this.time =
        this.field && this.field.defaultAnswer
          ? this.field.defaultAnswer
          : '00-00-00'
    },
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
    onTimeSelect() {
      this.$v.time.$touch()
      this.$emit('update', { field: this.field, value: this.time })
    },
  },
}
</script>
