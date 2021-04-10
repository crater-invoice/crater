<template>
  <base-page>
    <div class="pb-6">
      <sw-page-header :title="$tc('settings.setting', 1)">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('settings.setting', 2)"
            to="/admin/settings/user-profile"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>
    </div>

    <div class="w-full mb-6 select-wrapper xl:hidden">
      <sw-select
        :options="menuItems"
        v-model="currentSetting"
        :searchable="true"
        :show-labels="false"
        :allow-empty="false"
        :custom-label="getCustomLabel"
        @input="navigateToSetting"
      />
    </div>

    <div class="grid md:grid-cols-12">
      <div class="hidden col-span-3 mt-1 xl:block">
        <sw-list>
          <sw-list-item
            v-for="(menuItem, index) in menuItems"
            :title="$t(menuItem.title)"
            :key="index"
            :to="menuItem.link"
            :active="hasActiveUrl(menuItem.link)"
            tag-name="router-link"
            class="py-3"
          >
            <component slot="icon" :is="menuItem.icon" class="h-5" />
          </sw-list-item>
        </sw-list>
      </div>

      <div class="col-span-12 xl:col-span-9">
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </div>
    </div>
  </base-page>
</template>

<script>
import {
  UserIcon,
  OfficeBuildingIcon,
  BellIcon,
  CheckCircleIcon,
  ClipboardListIcon,
  CubeIcon,
  ClipboardCheckIcon,
} from '@vue-hero-icons/outline'

import {
  RefreshIcon,
  CogIcon,
  MailIcon,
  PencilAltIcon,
  CloudUploadIcon,
  FolderIcon,
  DatabaseIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    UserIcon,
    OfficeBuildingIcon,
    PencilAltIcon,
    CogIcon,
    CheckCircleIcon,
    ClipboardListIcon,
    MailIcon,
    BellIcon,
    FolderIcon,
    RefreshIcon,
    CubeIcon,
    CloudUploadIcon,
    DatabaseIcon,
    CreditCardIcon,
    ClipboardCheckIcon,
  },

  data() {
    return {
      currentSetting: {
        link: '/admin/settings/user-profile',
        title: 'settings.menu_title.account_settings',
        icon: 'user-icon',
      },
      menuItems: [
        {
          link: '/admin/settings/user-profile',
          title: 'settings.menu_title.account_settings',
          icon: 'user-icon',
        },
        {
          link: '/admin/settings/company-info',
          title: 'settings.menu_title.company_information',
          icon: 'office-building-icon',
        },
        {
          link: '/admin/settings/preferences',
          title: 'settings.menu_title.preferences',
          icon: 'cog-icon',
        },
        {
          link: '/admin/settings/customization',
          title: 'settings.menu_title.customization',
          icon: 'pencil-alt-icon',
        },
        {
          link: '/admin/settings/notifications',
          title: 'settings.menu_title.notifications',
          icon: 'bell-icon',
        },
        {
          link: '/admin/settings/tax-types',
          title: 'settings.menu_title.tax_types',
          icon: 'check-circle-icon',
        },
        {
          link: '/admin/settings/payment-mode',
          title: 'settings.menu_title.payment_modes',
          icon: 'credit-card-icon',
        },
        {
          link: '/admin/settings/custom-fields',
          title: 'settings.menu_title.custom_fields',
          icon: 'cube-icon',
        },
        {
          link: '/admin/settings/notes',
          title: 'settings.menu_title.notes',
          icon: 'clipboard-check-icon',
        },
        {
          link: '/admin/settings/expense-category',
          title: 'settings.menu_title.expense_category',
          icon: 'clipboard-list-icon',
        },

        {
          link: '/admin/settings/mail-configuration',
          title: 'settings.mail.mail_config',
          icon: 'mail-icon',
        },
        {
          link: '/admin/settings/file-disk',
          title: 'settings.menu_title.file_disk',
          icon: 'folder-icon',
        },
        {
          link: '/admin/settings/backup',
          title: 'settings.menu_title.backup',
          icon: 'database-icon',
        },
        {
          link: '/admin/settings/update-app',
          title: 'settings.menu_title.update_app',
          icon: 'refresh-icon',
        },
      ],
    }
  },

  watch: {
    '$route.path'(newValue) {
      if (newValue === '/admin/settings') {
        this.$router.push('/admin/settings/user-profile')
      }
    },
  },

  mounted() {
    this.currentSetting = this.menuItems.find(
      (item) => item.link == this.$route.path
    )
  },

  created() {
    if (this.$route.path === '/admin/settings') {
      this.$router.push('/admin/settings/user-profile')
    }
  },

  methods: {
    getCustomLabel({ title }) {
      return this.$t(title)
    },
    hasActiveUrl(url) {
      return this.$route.path.indexOf(url) > -1
    },
    navigateToSetting(setting) {
      this.$router.push(setting.link)
    },
  },
}
</script>
