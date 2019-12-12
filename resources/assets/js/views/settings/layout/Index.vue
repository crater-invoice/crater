<template>
  <div class="invoice-create-page main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $tc('settings.setting',1) }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/settings/user-profile">{{ $tc('settings.setting', 2) }}</router-link></li>
      </ol>
    </div>
    <div class="row settings-container">
      <div class="col-lg-3 settings-sidebar-container">
        <ol class="settings-sidebar">
          <li v-for="(menuItem, index) in menuItems" :key="index" class="settings-menu-item">
            <router-link :class="['link-color', {'active-setting': hasActiveUrl(menuItem.link)}]" :to="menuItem.link">
              <font-awesome-icon :icon="[menuItem.iconType, menuItem.icon]" class="setting-icon"/>
              <span class="menu-title ml-3">{{ $t(menuItem.title) }}</span>
            </router-link>
          </li>
        </ol>
      </div>
      <div class="col-lg-9">
        <transition
          name="fade"
          mode="out-in">
          <router-view/>
        </transition>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data () {
    return {
      menuItems: [
        {
          link: '/admin/settings/user-profile',
          title: 'settings.menu_title.account_settings',
          icon: 'user',
          iconType: 'far'
        },
        {
          link: '/admin/settings/company-info',
          title: 'settings.menu_title.company_information',
          icon: 'building',
          iconType: 'far'
        },
        {
          link: '/admin/settings/customization',
          title: 'settings.menu_title.customization',
          icon: 'edit',
          iconType: 'fa'
        },
        {
          link: '/admin/settings/preferences',
          title: 'settings.menu_title.preferences',
          icon: 'cog',
          iconType: 'fas'
        },
        {
          link: '/admin/settings/tax-types',
          title: 'settings.menu_title.tax_types',
          icon: 'check-circle',
          iconType: 'far'
        },
        {
          link: '/admin/settings/expense-category',
          title: 'settings.menu_title.expense_category',
          icon: 'list-alt',
          iconType: 'far'
        },
        {
          link: '/admin/settings/mail-configuration',
          title: 'settings.mail.mail_config',
          icon: 'envelope',
          iconType: 'fa'
        },
        {
          link: '/admin/settings/notifications',
          title: 'settings.menu_title.notifications',
          icon: 'bell',
          iconType: 'far'
        },
        {
          link: '/admin/settings/update-app',
          title: 'settings.menu_title.update_app',
          icon: 'sync-alt',
          iconType: 'fas'
        }
      ]
    }
  },
  watch: {
    '$route.path' (newValue) {
      if (newValue === '/admin/settings') {
        this.$router.push('/admin/settings/user-profile')
      }
    }
  },
  created () {
    if (this.$route.path === '/admin/settings') {
      this.$router.push('/admin/settings/user-profile')
    }
  },
  methods: {
    hasActiveUrl (url) {
      return this.$route.path.indexOf(url) > -1
    }
  }
}
</script>
