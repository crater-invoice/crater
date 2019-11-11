<template>
  <div class="main-content categoriescreate">
    <div class="page-header">
      <h3 class="page-title">{{ $t('categories.new_category') }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/expenses">{{ $tc('categorie.category',2) }}</router-link></li>
        <li class="breadcrumb-item"><a href="#">{{ $t('categories.new_category') }}</a></li>
      </ol>
      <div class="page-actions">
        <router-link slot="item-title" to="/admin/expenses">
          <base-button class="btn btn-primary" color="theme">
            <font-awesome-icon icon="backward" class="mr-2"/> {{ $t('general.go_back') }}
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
                <base-input
                  :invalid="$v.formData.name.$error"
                  v-model.trim="formData.name"
                  type="text"
                  name="name"
                  @input="$v.formData.name.$touch()"
                />
                <div v-if="$v.formData.name.$error">
                  <span v-if="!$v.formData.name.required" class="text-danger">{{ $t('validation.required') }}</span>
                  <span v-if="!$v.formData.name.minLength" class="text-danger"> {{ $tc('validation.name_min_length', $v.formData.name.$params.minLength.min, {count: $v.formData.name.$params.minLength.min}) }}</span>
                </div>
              </div>
              <div class="form-group">
                <label for="description">{{ $t('expenses.categories.description') }}</label>
                <base-text-area v-model="formData.description" rows="5" name="description" />
              </div>
              <base-button icon="save" type="submit" color="theme">
                {{ $t('general.save') }}
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
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3)
      }
    }
  },
  mounted () {
  },
  methods: {
    ...mapActions('category', [
      'loadData',
      'addCategory'
    ]),
    async submitCategoryData () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      let response = await this.addCategory({ ...this.formData })
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
