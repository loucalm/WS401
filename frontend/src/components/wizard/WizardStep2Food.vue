<template>
  <section class="flex-1 overflow-y-auto px-4 pb-30 pt-3">
    <div class="mx-auto w-full px-1">
      <div class="flex w-full" :class="stepIndicatorContainerClass">
        <div
          class="[&>svg]:h-20 [&>svg]:w-auto [&>svg]:max-w-full"
          :class="stepIndicatorColorClass"
          v-html="stepIndicatorSvgMarkup"
        ></div>
      </div>
    </div>

    <h2 class="mt-5 text-center font-ui text-[34px] leading-none text-black">
      {{ t("wizard.food") }}
    </h2>

    <h3 class="mt-8 font-ui text-[28px] leading-none text-black">{{ t("wizard.diet") }}</h3>

    <div class="mt-5 grid grid-cols-3 gap-4">
      <button
        v-for="option in foodConsumptionOptions"
        :key="option.id"
        type="button"
        class="text-left transition-transform duration-200 hover:-translate-y-0.5"
        @click="$emit('select-food-consumption', option.id)"
      >
        <div
          class="flex aspect-square min-h-31 flex-col items-center justify-center gap-1 rounded-[20px] border-2 bg-white transition-all duration-200"
          :class="
            selectedFoodConsumption === option.id
              ? 'border-tertiary bg-white'
              : option.cardClass
          "
        >
          <img
            v-if="option.image"
            :src="option.image"
            :alt="option.label"
            class="h-14 w-14 object-contain"
          />
          <p
            class="px-3 text-center font-ui text-body-16 leading-none text-black"
          >
            {{ option.label }}
          </p>
        </div>
      </button>
    </div>

    <p class="mt-5 font-ui text-[30px] leading-none text-black">
      {{ selectedFoodConsumptionLabel }}
    </p>

    <div class="mt-4 flex justify-center">
      <button
        type="button"
        class="rounded-[14px] border-2 border-tertiary bg-white px-3 py-2 font-ui text-body-16 leading-none text-tertiary transition-all duration-200 hover:bg-tertiary-light"
        @click="$emit('reset-food-selection')"
      >
        {{ t("wizard.deselect_all") }}
      </button>
    </div>

    <div class="mt-8 space-y-7 pb-4">
      <div v-for="section in foodSections" :key="section.id">
        <p class="font-ui text-[24px] leading-none text-black">
          {{ section.label }} :
        </p>
        <div class="mt-4 grid grid-cols-3 gap-4">
          <button
            v-for="option in section.options"
            :key="option.id"
            type="button"
            class="text-left transition-transform duration-200 hover:-translate-y-0.5"
            @click="$emit('select-food-option', section.id, option.id)"
          >
            <div
              class="flex min-h-31 flex-col items-center justify-center rounded-[18px] border-4 bg-tertiary-light px-2 py-3 transition-all duration-200"
              :class="
                (selectedFoodOptions[section.id] || []).includes(option.id)
                  ? 'border-tertiary bg-white'
                  : 'border-transparent'
              "
            >
              <Icon
                :icon="resolveIcon(option.icon)"
                class="h-12 w-12 text-tertiary"
              />
              <p
                class="mt-3 text-center font-ui text-body-16 leading-none text-tertiary"
              >
                {{ option.label }}
              </p>
            </div>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { Icon } from "@iconify/vue";
import { useI18n } from "vue-i18n";

export default {
  components: { Icon },
  emits: [
    "select-food-consumption",
    "select-food-option",
    "reset-food-selection",
  ],
  props: {
    stepIndicatorSvgMarkup: {
      type: String,
      required: true,
    },
    stepIndicatorColorClass: {
      type: String,
      required: true,
    },
    stepIndicatorContainerClass: {
      type: String,
      required: true,
    },
    selectedFoodConsumption: {
      type: String,
      required: true,
    },
    foodConsumptionOptions: {
      type: Array,
      required: true,
    },
    selectedFoodConsumptionLabel: {
      type: String,
      required: true,
    },
    foodSections: {
      type: Array,
      required: true,
    },
    selectedFoodOptions: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const { t } = useI18n();
    return { t };
  },
  methods: {
    resolveIcon(rawIcon) {
      const value = (rawIcon || "").toString().trim().toLowerCase();
      if (!value) return "lucide:circle-help";

      if (value.includes(":")) return value;

      return `mdi:${value.replace(/\s+/g, "-")}`;
    },
  },
};
</script>
