<template>
  <div>
    <sw-input
      :type="type"
      :invalid="isInvalid"
      :placeholder="placeholder"
      v-model="inputValue"
      :tabindex="tabindex"
      @input="handleInput"
      @change="handleChange"
    />
    <span v-if="isInvalid" class="text-sm text-danger">
      {{ $t('validation.required') }}
    </span>
  </div>
</template>
<script>
const {
  required,
  minLength,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  props: {
    type: {
      type: String,
      default: 'text',
      required: false,
    },
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
      inputValue: null,
      placeholder: '',
      invalidFieldIds: [],
    }
  },
  validations: {
    inputValue: {
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
    this.inputValue = this.field && this.field.defaultAnswer
    this.placeholder =
      this.field && this.field.placeholder ? this.field.placeholder : ''
  },
  methods: {
    handleData() {
      this.inputValue = this.field && this.field.defaultAnswer
      this.placeholder =
        this.field && this.field.placeholder ? this.field.placeholder : ''
    },
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
    handleInput() {
      this.$emit('update', { field: this.field, value: this.inputValue })
      this.$v.inputValue.$touch()
    },
    handleChange() {
      this.$v.inputValue.$touch()
      this.$emit('update', { field: this.field, value: this.inputValue })
    },
  },
}
</script>
