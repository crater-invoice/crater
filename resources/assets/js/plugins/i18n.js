import Vue from 'vue'
import VueI18n from 'vue-i18n'
import en from './en.json'
import fr from './fr.json'
import es from './es.json'
import ar from './ar.json'
import de from './de.json'
import pt_BR from './pt-br.json'
import it from './it.json'
import sr from './sr.json'
import nl from './nl.json'
import lv from './lv.json'
import sv from './sv.json'

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: 'en',
  messages: {
    en,
    fr,
    es,
    ar,
    de,
    pt_BR,
    it,
    sr,
    nl,
    lv,
    sv,
  },
})

export default i18n
