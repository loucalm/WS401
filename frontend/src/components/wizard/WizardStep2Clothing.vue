<template>
  <section class="flex-1 overflow-y-auto px-4 pb-30 pt-3">
    <div class="mx-auto w-full max-w-[700px] px-1">
      <div
        class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
        :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
        v-html="stepIndicatorSvgMarkup"
      ></div>
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
        class="text-center"
        @click="$emit('select-clothing-purchase', option.id)"
      >
        <div
          class="flex aspect-square items-center justify-center rounded-[20px] border-2 bg-cloth-light"
          :class="
            selectedClothingPurchase === option.id
              ? 'border-cloth bg-white'
              : 'border-transparent'
          "
        >
          <p
            class="px-2 text-center font-ui text-[18px] leading-none text-cloth"
          >
            {{ option.label }}
          </p>
        </div>
      </button>
    </div>

    <p class="mt-5 font-ui text-[30px] leading-none text-black">
      {{ selectedClothingPurchaseLabel }}
    </p>

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
            class="text-center"
            @click="$emit('select-clothing-option', section.id, option.id)"
          >
            <div
              class="flex h-26 items-center justify-center rounded-[18px] border-2 bg-cloth-light"
              :class="
                selectedClothingOptions[section.id] === option.id
                  ? 'border-cloth bg-white'
                  : 'border-transparent'
              "
            >
              <p
                class="px-2 text-center font-ui text-[18px] leading-none text-black"
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
export default {
  emits: ["select-clothing-purchase", "select-clothing-option"],
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
};
</script>
