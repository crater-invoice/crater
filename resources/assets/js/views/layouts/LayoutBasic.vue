<template>
  <div v-if="isAppLoaded" class="h-full">
    <base-modal />
    <base-notification />
    <site-header />
    <div class="flex h-screen pt-16 pb-10 overflow-hidden">
      <site-sidebar />
      <router-view />
    </div>
    <site-footer />
  </div>
  <div v-else class="h-full">
    <refresh-icon class="h-6 animate-spin" />
  </div>
</template>

<script type="text/babel">
import SiteHeader from './partials/TheSiteHeader.vue'
import SiteFooter from './partials/TheSiteFooter.vue'
import SiteSidebar from './partials/TheSiteSidebar.vue'
import BaseModal from '../../components/base/modal/BaseModal'
import { RefreshIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    SiteHeader,
    SiteSidebar,
    SiteFooter,
    BaseModal,
    RefreshIcon,
  },

  computed: {
    ...mapGetters(['isAppLoaded']),

    ...mapGetters('company', {
      selectedCompany: 'getSelectedCompany',
    }),

    isShow() {
      return true
    },
  },

  created() {
    this.bootstrap().then(() => {
      this.setInitialCompany()
    })
  },

  methods: {
    ...mapActions(['bootstrap']),

    ...mapActions('company', ['setSelectedCompany']),

    setInitialCompany() {
      this.setSelectedCompany(this.selectedCompany)
    },
  },
}
</script>
