<template>
  <div>
    <sw-input
      :invalid="$v.inputValue.$error || isInvalid"
      :placeholder="placeholder"
      :tabindex="tabindex"
      v-model="inputValue"
      type="url"
      @input="handleInput"
      @change="handleChange"
    />
    <div v-if="$v.inputValue.$error || isInvalid">
      <span v-if="!$v.inputValue.url" class="text-sm text-danger">
        {{ $t('validation.invalid_url') }}
      </span>
      <span v-if="isInvalid" class="text-sm text-danger">
        {{ $t('validation.required') }}
      </span>
    </div>
  </div>
</template>
<script>
const { required, url, requiredIf } = require('vuelidate/lib/validators')

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
      url,
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
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
    handleInput() {
      this.$v.inputValue.$touch()
      this.$emit('update', { field: this.field, value: this.inputValue })
    },
    handleChange() {
      this.$v.inputValue.$touch()
      this.$emit('update', { field: this.field, value: this.inputValue })
    },
  },
}
</script>
