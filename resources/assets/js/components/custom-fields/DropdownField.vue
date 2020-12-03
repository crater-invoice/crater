<template>
  <div>
    <sw-select
      v-model="selectedValue"
      :options="options"
      :searchable="true"
      :show-labels="false"
      :allow-empty="true"
      :invalid="isInvalid"
      :placeholder="placeholder"
      :tabindex="tabindex"
      @select="onSelectedValueChanged"
    />
    <span v-if="isInvalid" class="text-sm text-danger">
      {{ $t('validation.required') }}
    </span>
  </div>
</template>
<script>
const { required, requiredIf } = require('vuelidate/lib/validators')

export default {
  props: {
    field: {
      type: Object,
      default: null,
      require: true,
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
      selectedValue: null,
      options: [],
      placeholder: '',
      invalidFieldIds: [],
    }
  },
  validations: {
    selectedValue: {
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
        (this.$v.selectedValue.$error && !this.$v.selectedValue.required)
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
    this.options = this.field && this.field.options ? this.field.options : []
    this.selectedValue = this.field && this.field.defaultAnswer
    this.placeholder = this.field && this.field.placeholder
  },
  methods: {
    setInvalidFieldIds() {
      this.invalidFieldIds = this.invalidFields.map((field) => field.id)
    },
    onSelectedValueChanged(data) {
      this.$v.selectedValue.$touch()
      this.$emit('update', { field: this.field, value: data })
    },
  },
}
</script>
