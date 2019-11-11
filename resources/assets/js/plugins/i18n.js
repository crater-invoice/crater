import Vue from 'vue'
import VueI18n from 'vue-i18n'
import en from './en'
import fr from './fr'
import es from './es'

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: 'en',
  messages: {
    en,
    fr,
    es
  }
})

export default i18n
