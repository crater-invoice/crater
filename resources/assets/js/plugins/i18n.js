import Vue from 'vue'
import VueI18n from 'vue-i18n'
import en from './en.json'
import fr from './fr.json'
import es from './es.json'
import ar from './ar.json'
import de from './de.json'
import ja from './ja.json'
import pt_BR from './pt-br.json'
import pt from './pt.json'
import it from './it.json'
import sr from './sr.json'
import nl from './nl.json'
import ko from './ko.json'
import lv from './lv.json'
import sv from './sv.json'
import sk from './sk.json'
import vi from './vi.json'

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: 'en',
  fallbackLocale: 'en',
  messages: {
    en,
    fr,
    es,
    ar,
    de,
    ja,
    pt_BR,
    pt,
    it,
    sr,
    nl,
    ko,
    lv,
    sv,
    sk,
    vi,
  },
})

export default i18n
