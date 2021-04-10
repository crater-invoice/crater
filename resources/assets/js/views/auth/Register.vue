<template>
  <form id="registerForm" action="" method="post">
    <!-- {{ csrf_field() }} -->
    <div class="form-group">
      <input
        :placeholder="$t('login.enter_email')"
        type="email"
        class="form-control form-control-danger"
        name="email"
      />
    </div>
    <div class="form-group">
      <input
        id="password"
        type="password"
        class="form-control form-control-danger"
        placeholder="Enter Password"
        name="password"
      />
    </div>
    <div class="form-group">
      <input
        type="password"
        class="form-control form-control-danger"
        placeholder="Retype Password"
        name="password_confirmation"
      />
    </div>
    <sw-button class="btn btn-login btn-full">{{
      $t('login.register')
    }}</sw-button>
  </form>
</template>
<script type="text/babel">
export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    }
  },
  methods: {
    validateBeforeSubmit(e) {
      this.$validator.validateAll().then((result) => {
        if (result) {
          // eslint-disable-next-line
          alert('Form Submitted!')
        }
      })
    },
  },
}
</script>
