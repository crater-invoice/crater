<template>
  <div class="main-content pdfsetting">
    <div class="page-header">
      <h3 class="page-title">{{ $t('settings.title') }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
        <li class="breadcrumb-item"><router-link slot="item-title" to="#">{{ $t('settings.pdf.title') }}</router-link></li>
      </ol>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <div class="caption">
              <h6>{{ $t('settings.pdf.title') }}</h6>
            </div>
            <div class="actions">
              <base-button color="theme" size="small" @click="submitData">
                {{ $t('general.update') }}
              </base-button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <label class="col-md-2 form-control-label">{{ $t('settings.pdf.footer_text') }} : </label>
              <div class="col-md-12">
                <input v-model="footerText" type="text" class="form-control">
              </div>
            </div>
            <div class="row pdfsetting__img-row">
              <label class="col-md-2 form-control-label">{{ $t('settings.pdf.pdf_layout') }} : </label>
              <div class="col-md-12">
                <image-radio :current-p-d-f="pdfSet" @selectedPDF="selectedPDF"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ImageRadio from '../components/ImageRadio.vue'
import { mapActions, mapMutations } from 'vuex'

export default {
  components: {
    'image-radio': ImageRadio
  },
  data () {
    return this.$store.state.pdf_setting
    // return {
    //   pdfSet: '1',
    //   footerText: null
    // }
  },
  mounted () {
    this.loadData()
  },
  methods: {
    ...mapActions('pdf_setting', [
      'loadData',
      'submitData'
    ]),
    // async submitData () {

    // },
    ...mapMutations('pdf_setting', [
      'selectedPDF'
    ])
  }
}
</script>
