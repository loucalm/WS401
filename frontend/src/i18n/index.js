import { createI18n } from 'vue-i18n'
import fr from './locales/fr'
import en from './locales/en'

const defaultLocale = localStorage.getItem('locale') || 'fr'

export const i18n = createI18n({
  legacy: false,
  locale: defaultLocale,
  fallbackLocale: 'en',
  messages: {
    fr,
    en,
  },
})

export default i18n