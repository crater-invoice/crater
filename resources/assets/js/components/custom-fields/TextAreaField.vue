<template>
  <div>
    <sw-textarea
      v-model="text"
      :invalid="isInvalid"
      :placeholder="placeholder"
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
      text: null,
      placeholder: '',
      invalidFieldIds: [],
    }
  },
  validations: {
    text: {
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
        (this.$v.text.$error && !this.$v.text.required)
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
    this.text = this.field && this.field.defaultAnswer
    this.placeholder =
      this.field && this.field.placeholder ? this.field.placeholder : ''
  },
  methods: {
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
    handleInput() {
      this.$v.text.$touch()
      this.$emit('update', { field: this.field, value: this.text })
    },
    handleChange() {
      this.$v.text.$touch()
      this.$emit('update', { field: this.field, value: this.text })
    },
  },
}
</script>
