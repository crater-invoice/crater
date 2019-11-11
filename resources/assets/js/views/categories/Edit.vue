<template>
  <div class="main-content categoriescreate">
    <div class="page-header">
      <h3 v-if="!isEdit" class="page-title">{{ $t('categories.add_category') }}</h3>
      <h3 v-else class="page-title">{{ $t('navigation.edit') }} {{ $tc('navigation.category',1) }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('navigation.home') }}</router-link></li>
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/expenses">{{ $tc('navigation.category',2) }}</router-link></li>
        <li v-if="!isEdit" class="breadcrumb-item"><a href="#">{{ $t('expenses.categories.add_category') }} {{ $tc('navigation.category', 1) }}</a></li>
        <li v-else class="breadcrumb-item"><a href="#">{{ $t('navigation.edit') }} {{ $tc('navigation.category', 1) }}</a></li>
      </ol>
      <div class="page-actions">
        <router-link slot="item-title" to="/admin/expenses">
          <base-button icon="backward" color="theme">
            {{ $t('navigation.go_back') }}
          </base-button>
        </router-link>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <div class="card">
          <form action="" @submit.prevent="submitCategoryData">
            <div class="card-body">
              <div class="form-group">
                <label class="control-label">{{ $t('expenses.categories.title') }}</label><span class="text-danger"> *</span>
                <input
                  :class="{ error: $v.formData.name.$error }"
                  v-model.trim="formData.name"
                  type="text"
                  name="name"
                  class="form-control"
                  @input="$v.formData.name.$touch()"
                >
                <div v-if="$v.formData.name.$error">
                  <span v-if="!$v.formData.name.required" class="text-danger">{{ $t('validation.required') }}</span>
                  <span v-if="!$v.formData.name.minLength" class="text-danger"> {{ $tc('validation.name_min_length', $v.formData.name.$params.minLength.min, {count: $v.formData.name.$params.minLength.min}) }}</span>
                </div>
              </div>
              <div class="form-group">
                <label for="description">{{ $t('expenses.categories.description') }}</label>
                <textarea v-model="formData.description" class="form-control" rows="5" name="description" />
              </div>
              <base-button icon="save" color="theme" type="submit">
                {{ $t('navigation.save') }}
              </base-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { validationMixin } from 'vuelidate'
import { mapActions } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return {
      formData: {
        name: null,
        description: null
      }
    }
  },
  computed: {
    isEdit () {
      if (this.$route.name === 'categoryedit') {
        return true
      }
      return false
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3)
      }
    }
  },
  mounted () {
    this.fetchInitialData()
  },
  methods: {
    ...mapActions('category', [
      'loadData',
      'addCategory',
      'editCategory'
    ]),
    async fetchInitialData () {
      let response = await this.loadData(this.$route.params.id)
      this.formData.name = response.data.category.name
      this.formData.description = response.data.category.description
    },
    async submitCategoryData () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      let response = await this.editCategory({ id: this.$route.params.id, editData: { ...this.formData } })
      if (response.data.success) {
        window.toastr['success'](response.data.success)
        this.$router.push('/admin/expenses')
        return true
      }
      window.toastr['error'](response.data.error)
      return true
    }
  }
}
</script>
