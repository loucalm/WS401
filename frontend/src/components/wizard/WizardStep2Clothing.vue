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
      Clothing
    </h2>

    <h3 class="mt-8 font-ui text-[28px] leading-none text-black">
      Purchasing type :
    </h3>

    <div class="mt-5 grid grid-cols-3 gap-4">
      <button
        v-for="option in clothingPurchaseOptions"
        :key="option.id"
        type="button"
        class="text-center transition-transform duration-200 hover:-translate-y-0.5"
        @click="$emit('select-clothing-purchase', option.id)"
      >
        <div
          class="flex aspect-square flex-col items-center justify-center gap-2 rounded-[20px] border-4 bg-cloth-light transition-all duration-200"
          :class="
            selectedClothingPurchase === option.id
              ? 'border-cloth bg-white'
              : 'border-transparent'
          "
        >
          <span
            v-if="option.svgMarkup"
            class="text-cloth [&>svg]:h-12 [&>svg]:w-12"
            v-html="option.svgMarkup"
          ></span>
          <p
            class="px-2 text-center font-ui text-body-16 leading-none text-cloth"
          >
            {{ option.label }}
          </p>
        </div>
      </button>
    </div>

    <p class="mt-5 font-ui text-[30px] leading-none text-black">
      {{ selectedClothingPurchaseLabel }}
    </p>

    <div class="mt-4 flex justify-center">
      <button
        type="button"
        class="rounded-[14px] border-2 border-cloth bg-white px-3 py-2 font-ui text-body-16 leading-none text-cloth transition-all duration-200 hover:bg-cloth-light"
        @click="$emit('reset-clothing-selection')"
      >
        Deselect all
      </button>
    </div>

    <div class="mt-8 space-y-7 pb-4">
      <div v-for="section in clothingSections" :key="section.id">
        <p class="font-ui text-[24px] leading-none text-black">
          {{ section.label }} :
        </p>
        <div class="mt-4 grid grid-cols-3 gap-4">
          <button
            v-for="option in section.options"
            :key="option.id"
            type="button"
            class="text-left transition-transform duration-200 hover:-translate-y-0.5"
            @click="$emit('select-clothing-option', section.id, option.id)"
          >
            <div
              class="flex min-h-31 flex-col items-center justify-center rounded-[18px] border-4 bg-cloth-light px-2 py-3 transition-all duration-200"
              :class="
                (selectedClothingOptions[section.id] || []).includes(option.id)
                  ? 'border-cloth bg-white'
                  : 'border-transparent'
              "
            >
              <Icon
                :icon="resolveIcon(option.icon)"
                class="h-12 w-12 text-cloth"
              />
              <p
                class="mt-3 text-center font-ui text-body-16 leading-none text-cloth"
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

export default {
  components: { Icon },
  emits: [
    "select-clothing-purchase",
    "select-clothing-option",
    "reset-clothing-selection",
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
    selectedClothingPurchase: {
      type: String,
      required: true,
    },
    clothingPurchaseOptions: {
      type: Array,
      required: true,
    },
    selectedClothingPurchaseLabel: {
      type: String,
      required: true,
    },
    clothingSections: {
      type: Array,
      required: true,
    },
    selectedClothingOptions: {
      type: Object,
      required: true,
    },
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
