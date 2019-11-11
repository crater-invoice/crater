<template>
  <div class="main-content">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.title') }}</h3>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
          <li class="breadcrumb-item"><router-link slot="item-title" to="#">{{ $t('settings.general') }}</router-link></li>
        </ol>
      </div>
      <form action="" @submit.prevent="submitData">
        <div class="row">
          <div class="col-sm-8">
            <div class="card">
              <div class="card-header">
                <div class="caption">
                  <h6>{{ $t('settings.general') }}</h6>
                </div>
                <div class="actions">
                  <base-button icon="backward" color="theme" size="small" type="submit">
                    {{ $t('general.save') }}
                  </base-button>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <label class="col-md-2 form-control-label">{{ $t('settings.language') }}: </label>
                  <div class="col-md-10">
                    <setting-dropdown
                      :options="languages"
                      :get-data="settings"
                      :current-data="settings.language"
                      type="languages"
                    />
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 form-control-label">{{ $t('settings.primary_currency') }}: </label>
                  <div class="col-md-10">
                    <setting-dropdown
                      :options="currencies"
                      :get-data="settings"
                      :current-data="settings.currency"
                      type="currencies"
                    />
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-2 form-control-label">{{ $t('settings.timezone') }}: </label>
                  <div class="col-md-10">
                    <setting-dropdown
                      :options="time_zones"
                      :get-data="settings"
                      :current-data="settings.time_zone"
                      type="time_zones"
                    />
                  </div>
                </div>
                <div class="form-body">
                  <div class="form-group row">
                    <label class="col-md-2 form-control-label">{{ $t('settings.date_format') }}: </label>
                    <div class="col-md-10">
                      <setting-dropdown
                        :options="date_formats"
                        :get-data="settings"
                        :current-data="settings.date_format"
                        type="date_formats"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import SettingDropdown from '../components/SettingListBox.vue'
import { mapActions } from 'vuex'

export default {
  components: {
    'setting-dropdown': SettingDropdown
  },
  data () {
    return this.$store.state.general
  },
  mounted () {
    this.loadData()
  },
  methods: {
    ...mapActions('general', [
      'loadData',
      'submitData'
    ])
  }
}
</script>
