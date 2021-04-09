import Vue from 'vue'

import BaseModal from './modal/BaseModal.vue'
import BaseLoader from './BaseLoader.vue'
import BaseCustomerSelect from './BaseCustomerSelect.vue'

import BaseCustomInput from './BaseCustomInput.vue'

import CustomerSelectPopup from './popup/CustomerSelectPopup.vue'
import TaxSelectPopup from './popup/TaxSelectPopup.vue'
import NoteSelectPopup from './popup/NoteSelectPopup.vue'

import BaseDatePicker from '../base/BaseDatePicker.vue'
import BaseTimePicker from './BaseTimePicker.vue'
import BasePage from './BasePage.vue'
import BaseNotification from './BaseNotification.vue'

import GlobalSearch from '../GlobalSearch.vue'

import DotIcon from '../../components/icon/DotIcon.vue'
import SaveIcon from '../../components/icon/SaveIcon.vue'

import SwSelect from '@bytefury/spacewind/src/components/sw-select'

Vue.component('base-modal', BaseModal)
Vue.component('global-search', GlobalSearch)

Vue.component('base-page', BasePage)

Vue.component('base-date-picker', BaseDatePicker)

Vue.component('base-loader', BaseLoader)
Vue.component('sw-select', SwSelect)

Vue.component('base-customer-select', BaseCustomerSelect)
Vue.component('base-custom-input', BaseCustomInput)

Vue.component('customer-select-popup', CustomerSelectPopup)
Vue.component('tax-select-popup', TaxSelectPopup)
Vue.component('note-select-popup', NoteSelectPopup)

Vue.component('base-time-picker', BaseTimePicker)
Vue.component('base-notification', BaseNotification)

Vue.component('dot-icon', DotIcon)
Vue.component('save-icon', SaveIcon)
