<template>
  <header class="site-header">
    <a href="/" class="brand-main">
      <img
        id="logo-white"
        src="/assets/img/logo-white.png"
        alt="Crater Logo"
        class="d-none d-md-inline"
      >
      <img
        id="logo-mobile"
        src="/assets/img/crater-white-small.png"
        alt="Laraspace Logo"
        class="d-md-none">
    </a>

    <a
      href="#"
      class="nav-toggle"
      @click="onNavToggle"
    >
      <div class="hamburger hamburger--arrowturn">
        <div class="hamburger-box">
          <div class="hamburger-inner"/>
        </div>
      </div>
    </a>
    <ul class="action-list">
      <li>
        <v-dropdown :show-arrow="false">
          <a slot="activator" href="#">
            <font-awesome-icon icon="plus" />
          </a>
          <v-dropdown-item>
            <router-link class="dropdown-item" to="/admin/invoices/create">
              <font-awesome-icon icon="file-alt" class="dropdown-item-icon" /> <span> {{ $t('invoices.new_invoice') }} </span>
            </router-link>
          </v-dropdown-item>
          <v-dropdown-item>
            <router-link class="dropdown-item" to="/admin/estimates/create">
              <font-awesome-icon class="dropdown-item-icon" icon="file" /> <span> {{ $t('estimates.new_estimate') }} </span>
            </router-link>
          </v-dropdown-item>
          <v-dropdown-item>
            <router-link class="dropdown-item" to="/admin/customers/create">
              <font-awesome-icon class="dropdown-item-icon" icon="user" />  <span> {{ $t('customers.new_customer') }} </span>
            </router-link>
          </v-dropdown-item>
        </v-dropdown>
      </li>
      <li>
        <v-dropdown :show-arrow="false">
          <a
            slot="activator"
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            class="avatar"
          >
            <img :src="profilePicture" alt="Avatar">
          </a>
          <v-dropdown-item>
            <router-link class="dropdown-item" to="/admin/settings">
              <font-awesome-icon icon="cogs" class="dropdown-item-icon"/> <span> {{ $t('navigation.settings') }} </span>
            </router-link>
          </v-dropdown-item>
          <v-dropdown-item>
            <a
              href="#"
              class="dropdown-item"
              @click.prevent="logout"
            >
              <font-awesome-icon icon="sign-out-alt" class="dropdown-item-icon"/> <span> {{ $t('navigation.logout') }} </span>
            </a>
          </v-dropdown-item>
        </v-dropdown>
      </li>
    </ul>
  </header>
</template>
<script type="text/babel">
import { mapGetters, mapActions } from 'vuex'

export default {
  computed: {
    ...mapGetters('userProfile', [
      'user'
    ]),
    profilePicture () {
      if (this.user && this.user.avatar !== null) {
        return this.user.avatar
      } else {
        return '/images/default-avatar.jpg'
      }
    }
  },
  created () {
    this.loadData()
  },
  methods: {
    ...mapActions('userProfile', [
      'loadData'
    ]),
    ...mapActions({
      companySelect: 'changeCompany'
    }),
    ...mapActions('auth', [
      'logout'
    ]),
    onNavToggle () {
      this.$utils.toggleSidebar()
    }
  }
}
</script>
