import BaseButton from './BaseButton.vue'
import ItemModal from './modal/ItemModal.vue'
import BaseModal from './modal/BaseModal.vue'
import BaseDatePicker from './base-date-picker/BaseDatePicker.vue'
import BaseInput from './BaseInput.vue'
import BaseSwitch from './BaseSwitch.vue'
import BaseTextArea from './BaseTextArea.vue'
import BaseSelect from './base-select/BaseSelect.vue'
import BaseLoader from './BaseLoader.vue'
import BaseCustomerSelect from './BaseCustomerSelect.vue'
import BasePrefixInput from './BasePrefixInput.vue'

import BasePopup from './popup/BasePopup.vue'
import CustomerSelectPopup from './popup/CustomerSelectPopup.vue'
import TaxSelectPopup from './popup/TaxSelectPopup.vue'

import {TableColumn, TableComponent} from './base-table/index'

Vue.component('base-button', BaseButton)
Vue.component('item-modal', ItemModal)
Vue.component('base-modal', BaseModal)
Vue.component('base-date-picker', BaseDatePicker)
Vue.component('base-input', BaseInput)
Vue.component('base-switch', BaseSwitch)
Vue.component('base-text-area', BaseTextArea)
Vue.component('base-loader', BaseLoader)
Vue.component('base-prefix-input', BasePrefixInput)

Vue.component('table-component', TableComponent)
Vue.component('table-column', TableColumn)

Vue.component('base-select', BaseSelect)
Vue.component('base-customer-select', BaseCustomerSelect)

Vue.component('base-popup', BasePopup)
Vue.component('customer-select-popup', CustomerSelectPopup)
Vue.component('tax-select-popup', TaxSelectPopup)
