<template>
  <div class="measurement-card">
    <button @click="preferences.toggleUnitSystem">
      {{ t('switch_system') }}
    </button>
    <button @click="locale = locale === 'fr' ? 'en' : 'fr'">
      FR / EN
    </button>

    <p>
      <strong>{{ t('weight') }} :</strong> 
      {{ displayWeight }} {{ t(`units.${currentWeightUnit}`) }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePreferencesStore } from '../stores/preferences'


const { t, locale } = useI18n()
const preferences = usePreferencesStore()


const props = defineProps({
  weightInKg: {
    type: Number,
    default: 75 
  }
})


const displayWeight = computed(() => {
  if (preferences.unitSystem === 'imperial') {
    return (props.weightInKg * 2.20462).toFixed(1) 
  }
  return props.weightInKg
})


const currentWeightUnit = computed(() => {
  return preferences.unitSystem === 'imperial' ? 'lbs' : 'kg'
})
</script>