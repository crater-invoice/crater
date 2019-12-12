<template>
  <div class="template-container" v-if="isAppLoaded">
    <base-modal />
    <site-header/>
    <site-sidebar type="basic"/>

    <transition
      name="fade"
      mode="out-in">
      <router-view />
    </transition>
    <site-footer/>
  </div>
  <div v-else class="template-container">
    <font-awesome-icon icon="spinner" class="fa-spin"/>
  </div>
</template>
<script type="text/babel">
import SiteHeader from './partials/TheSiteHeader.vue'
import SiteFooter from './partials/TheSiteFooter.vue'
import SiteSidebar from './partials/TheSiteSidebar.vue'
import Layout from '../../helpers/layout'
import BaseModal from '../../components/base/modal/BaseModal'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    SiteHeader, SiteSidebar, SiteFooter, BaseModal
  },
  data () {
    return {
      'header': 'header'
    }
  },
  computed: {
    ...mapGetters([
      'isAppLoaded'
    ]),

    ...mapGetters('company', {
      selectedCompany: 'getSelectedCompany',
      companies: 'getCompanies'
    }),

    isShow () {
      return true
    }
  },
  mounted () {
    Layout.set('layout-default')
  },

  created () {
    this.bootstrap().then((res) => {
      this.setInitialCompany()
    })
  },

  methods: {
    ...mapActions(['bootstrap']),
    ...mapActions('company', ['setSelectedCompany']),
    setInitialCompany () {
      let selectedCompany = Ls.get('selectedCompany') !== null

      if (selectedCompany) {
        let foundCompany = this.companies.find((company) => company.id === parseInt(selectedCompany))

        if (foundCompany) {
          this.setSelectedCompany(foundCompany)
          return
        }
      }

      this.setSelectedCompany(this.companies[0])
    }
  }
}
</script>
<style lang="scss" scoped>
body {
  background-color: #f8f8f8;
}
</style>
