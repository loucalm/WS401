import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePreferencesStore = defineStore('preferences', () => {

  const unitSystem = ref('metric') 


  function toggleUnitSystem() {
    unitSystem.value = unitSystem.value === 'metric' ? 'imperial' : 'metric'
  }

  return { unitSystem, toggleUnitSystem }
})