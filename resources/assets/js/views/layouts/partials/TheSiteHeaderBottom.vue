<template>
  <div class="header-bottom">
    <div class="header-nav vue-dropdown-menu">
      <v-dropdown active-url="/admin/dashboard">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-dashboard"/>{{ $t('navigation.dashboard') }}
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/dashboard/basic">
              Basic
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/dashboard/ecommerce">
              Ecommerce
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/dashboard/finance">
              Finance
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/layouts">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-th-large"/>Layouts
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/layouts/sidebar">
              Sidebar
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/layouts/horizontal">
              Horizontal
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/layouts/icons-sidebar">
              Icon Sidebar
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/basic-ui">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-star"/>Basic UI
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/buttons">
              Buttons
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/cards">
              Cards
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/tabs">
              Tabs &amp; Accordians
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/typography">
              Typography
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/tables">
              Tables
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/modals">
              Modals
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/basic-ui/progress-bars">
              Progress Bar
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/components">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-puzzle-piece"/>Components
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/calendar">
              Calendar
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/datatables">
              Jquery Datatables
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/mail-box">
              MailBox
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/calendar">
              Calendar
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/datatables">
              Jquery Datatables
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/image-cropper">
              ImageCropper
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/image-zoom">
              ImageZoom
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/nestable-list">
              Nestable List
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/nestable-tree">
              Nestable Tree
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/notifications">
              Notifications
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown active-url="/admin/layouts">
          <template slot="title">
            <a href="#">
              <i class="icon-fa icon-fa-th-large"/>Layouts
              <span class="icon-fa arrow icon-fa-fw"/>
            </a>
          </template>
          <v-dropdown-item>
            <template slot="item-title">
              <router-link to="/admin/layouts/sidebar">
                Sidebar
              </router-link>
            </template>
          </v-dropdown-item>
          <v-dropdown-item>
            <template slot="item-title">
              <router-link to="/admin/layouts/horizontal">
                Horizontal
              </router-link>
            </template>
          </v-dropdown-item>
        </v-dropdown>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/sweet-modals">
              Sweet Modals
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/image-zoom">
              ImageZoom
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/components/mail-box">
              MailBox
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/chart">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-bar-chart"/>Charts
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/charts/amchart">
              AM Charts
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/charts/chartjs">
              Chart JS
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/charts/gauge">
              Gauges
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/charts/morris">
              Morris
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/charts/sparkline">
              Sparkline
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/icons">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-eye"/>Icons
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/icons/icomoon">
              IcoMoon
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/icons/fontawesome">
              Font Awesome
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/forms">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-rocket"/>Form
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/general">
              General Elements
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/advanced">
              Advanced Elements
            </router-link>
          </template>
        </v-dropdown-item><v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/layouts">
              Form Layouts
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/validation">
              Form Validation
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/wizards">
              Form Wizard
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/wizards-2">
              Form Wizard 2
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/wizards-3">
              Form Wizard 3
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/editors">
              Editors
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/vee">
              Vee Validate
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/forms/vuelidate">
              Vuelidate
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/gallery">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-image"/>Gallery
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/gallery/grid">
              Grid
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/gallery/masonry-grid">
              Masonry Grid
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/users">
        <template slot="title">
          <a href="#">
            <i class="icon-fa icon-fa-user"/>Users
            <span class="icon-fa arrow icon-fa-fw"/>
          </a>
        </template>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/users/profile">
              Profile
            </router-link>
          </template>
        </v-dropdown-item>
        <v-dropdown-item>
          <template slot="item-title">
            <router-link to="/admin/users">
              All Users
            </router-link>
          </template>
        </v-dropdown-item>
      </v-dropdown>
      <v-dropdown active-url="/admin/todo-item">
        <template slot="title">
          <router-link to="/admin/todo-item">
            <i class="icon-fa icon-fa-check"/>Todos
          </router-link>
        </template>
      </v-dropdown>
      <v-dropdown active-url="/admin/settings">
        <template slot="title">
          <router-link to="/admin/settings">
            <i class="icon-fa icon-fa-cogs"/>Settings
          </router-link>
        </template>
      </v-dropdown>
    </div>
  </div>
</template>

<script type="text/babel">
import VDropdown from '../../../components/dropdown/VDropdown'
import VDropdownItem from '../../../components/dropdown/VDropdownItem'

export default {
  components: {
    VDropdown,
    VDropdownItem
  },
  data () {
    return {
      sidebar: 'sidebar'
    }
  }
}
</script>
