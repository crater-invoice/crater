<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <sw-page-header :title="pageTitle" class="mb-3">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="/admin/dashboard" />
        <sw-breadcrumb-item :title="$tc('users.user', 2)" to="/admin/users" />
        <sw-breadcrumb-item
          v-if="$route.name === 'users.edit'"
          :title="$t('users.edit_user')"
          to="#"
          active
        />
        <sw-breadcrumb-item
          v-else
          :title="$t('users.new_user')"
          to="#"
          active
        />
      </sw-breadcrumb>
      <template slot="actions"></template>
    </sw-page-header>

    <div class="grid grid-cols-12">
      <div class="col-span-12 md:col-span-8">
        <form action="" @submit.prevent="submitUser">
          <sw-card>
            <sw-input-group
              :label="$t('users.name')"
              :error="nameError"
              class="mb-4"
              required
            >
              <sw-input
                v-model.trim="formData.name"
                :invalid="$v.formData.name.$error"
                class="mt-2"
                focus
                type="text"
                name="name"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('users.email')"
              :error="emailError"
              class="mt-4"
              required
            >
              <sw-input
                :invalid="$v.formData.email.$error"
                v-model.trim="formData.email"
                type="text"
                name="email"
                tab-index="3"
                @input="$v.formData.email.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$tc('users.password')"
              :error="passwordError"
              required
              class="mt-4"
            >
              <sw-input
                v-model="formData.password"
                :invalid="$v.formData.password.$error"
                type="password"
                class="mt-2"
                @input="$v.formData.password.$touch()"
              />
            </sw-input-group>

            <sw-input-group :label="$t('users.phone')" class="mt-4 mb-6">
              <sw-input
                v-model.trim="formData.phone"
                type="text"
                name="phone"
                tab-index="4"
              />
            </sw-input-group>

            <div class="mt-6 mb-4">
              <sw-button
                :loading="isLoading"
                variant="primary"
                type="submit"
                size="lg"
                class="flex justify-center w-full md:w-auto"
              >
                <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
                {{ isEdit ? $t('users.update_user') : $t('users.save_user') }}
              </sw-button>
            </div>
          </sw-card>
        </form>
      </div>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
} = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      title: 'Add User',

      formData: {
        name: '',
        email: null,
        password: null,
        phone: null,
      },
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'users.edit') {
        return this.$t('users.edit_user')
      }
      return this.$t('users.new_user')
    },

    isEdit() {
      if (this.$route.name === 'users.edit') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },

    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }

      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
    },

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },
  },

  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    if (this.isEdit) {
      this.loadEditData()
    }
  },

  mounted() {
    this.$v.formData.$reset()
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        email,
        required,
      },

      password: {
        required: requiredIf(function () {
          return !this.isEdit
        }),
        minLength: minLength(8),
      },
    },
  },

  methods: {
    ...mapActions('users', ['addUser', 'fetchUser', 'updateUser']),

    ...mapActions('notification', ['showNotification']),

    async loadEditData() {
      let response = await this.fetchUser(this.$route.params.id)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.user }
      }
    },

    async submitUser() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      try {
        let response
        this.isLoading = true
        if (this.isEdit) {
          response = await this.updateUser(this.formData)
          let data
          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('users.updated_message'),
            })
            this.$router.push('/admin/users')
            this.isLoading = false
          }
          if (response.data.error) {
            this.showNotification({
              type: 'error',
              message: this.$t('validation.email_already_taken'),
            })
          }
        } else {
          response = await this.addUser(this.formData)
          let data
          if (response.data.success) {
            this.isLoading = false
            if (!this.isEdit) {
              this.showNotification({
                type: 'success',
                message: this.$tc('users.created_message'),
              })
              this.$router.push('/admin/users')
              return true
            }
          }
        }
      } catch (err) {
        if (err.response.data.errors.email) {
          this.isLoading = false
        }
      }
    },
  },
}
</script>
