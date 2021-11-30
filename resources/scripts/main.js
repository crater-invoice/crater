import '../sass/crater.scss'
import 'v-tooltip/dist/v-tooltip.css'
import '@/scripts/plugins/axios.js'
import * as pinia from 'pinia'
import * as Vue from 'vue'
import * as router from 'vue-router'

window.pinia = pinia

import Crater from './Crater'

window.Vue = Vue
window.router = router

window.Crater = new Crater()
