<template>
  <div
    class="rounded-3xl border border-white/10 bg-white/5 p-5 text-slate-100 shadow-lg shadow-black/20 backdrop-blur"
  >
    <div class="flex flex-wrap gap-3">
      <button
        @click="preferences.toggleUnitSystem"
        class="rounded-2xl bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300"
      >
        {{ t("switch_system") }}
      </button>
      <button
        @click="locale = locale === 'fr' ? 'en' : 'fr'"
        class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10"
      >
        FR / EN
      </button>
    </div>

    <p class="mt-4 text-sm text-slate-300">
      <strong class="text-white">{{ t("weight") }} :</strong>
      {{ displayWeight }} {{ t(`units.${currentWeightUnit}`) }}
    </p>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { usePreferencesStore } from "../preferences";

const { t, locale } = useI18n();
const preferences = usePreferencesStore();

const props = defineProps({
  weightInKg: {
    type: Number,
    default: 75,
  },
});

const displayWeight = computed(() => {
  if (preferences.unitSystem === "imperial") {
    return (props.weightInKg * 2.20462).toFixed(1);
  }

  return props.weightInKg;
});

const currentWeightUnit = computed(() => {
  return preferences.unitSystem === "imperial" ? "lbs" : "kg";
});
</script>
