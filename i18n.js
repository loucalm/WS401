import { createI18n } from 'vue-i18n'

const messages = {
  fr: {
    weight: 'Poids',
    height: 'Taille',
    switch_system: 'Changer d\'unités',
    units: {
      kg: 'kg',
      lbs: 'lbs',
      cm: 'cm',
      in: 'po'
    }
  },
  en: {
    weight: 'Weight',
    height: 'Height',
    switch_system: 'Switch Units',
    units: {
      kg: 'kg',
      lbs: 'lbs',
      cm: 'cm',
      in: 'in'
    }
  }
}

export const i18n = createI18n({
  legacy: false, 
  locale: 'fr', 
  fallbackLocale: 'en',
  messages,
})