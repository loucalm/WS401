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
      {{ stepTitle }}
    </h2>

    <div class="relative mt-7">
      <button
        type="button"
        class="flex w-full items-center justify-between rounded-[18px] border border-main bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
        @click="$emit('toggle-transport-menu')"
      >
        <span class="font-ui text-[26px] leading-none text-black">{{
          transportMode
        }}</span>
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.6"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="h-7 w-7 text-black transition-transform"
          :class="isTransportMenuOpen ? 'rotate-180' : ''"
          aria-hidden="true"
        >
          <path d="m5 9 7 7 7-7" />
        </svg>
      </button>

      <div
        v-if="isTransportMenuOpen"
        class="absolute left-0 right-0 top-[calc(100%+10px)] z-10 max-h-65 overflow-y-auto overflow-x-hidden rounded-[18px] border border-main bg-white shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
      >
        <button
          v-for="option in transportOptions"
          :key="option"
          type="button"
          class="flex w-full items-center justify-between border-b border-main-light px-4 py-3 text-left last:border-b-0"
          @click="$emit('select-transport-mode', option)"
        >
          <span class="font-ui text-[22px] leading-none text-black">{{
            option
          }}</span>
          <span
            v-if="transportMode === option"
            class="font-ui text-[18px] leading-none text-main"
            >OK</span
          >
        </button>
      </div>
    </div>

    <div class="mt-8 flex items-center gap-3">
      <h3 class="font-ui text-[28px] leading-none text-black">Distance</h3>
    </div>

    <div class="mt-8 flex justify-center">
      <div
        v-if="!isManualDistanceOpen"
        class="relative inline-flex min-h-33 min-w-37 items-center justify-center rounded-[999px] bg-white px-8 py-9 shadow-[0_10px_18px_rgba(0,0,0,0.18)]"
      >
        <span class="font-ui text-[36px] leading-none text-black"
          >{{ distance }} KM</span
        >
      </div>

      <div
        v-else
        class="flex w-full max-w-55 min-h-33 items-center gap-3 rounded-[999px] bg-white px-4 py-9 shadow-[0_10px_18px_rgba(0,0,0,0.18)]"
      >
        <input
          v-model="manualDistanceValue"
          type="number"
          min="0"
          step="1"
          class="w-full bg-transparent font-ui text-[26px] leading-none text-black outline-none"
          @keyup.enter="applyManualDistance"
        />
        <span class="font-ui text-[18px] leading-none text-black">KM</span>
      </div>
    </div>

    <div v-if="isManualDistanceOpen" class="mt-3 flex justify-center gap-3">
      <button
        type="button"
        class="rounded-[18px] bg-main px-4 py-2 font-ui text-[18px] leading-none text-white"
        @click="applyManualDistance"
      >
        Confirm
      </button>
      <button
        type="button"
        class="rounded-[18px] border border-main bg-white px-4 py-2 font-ui text-[18px] leading-none text-main"
        @click="cancelManualDistance"
      >
        Cancel
      </button>
    </div>

    <div class="mt-10 flex items-center justify-between gap-3 px-1">
      <button
        type="button"
        class="flex h-14 min-w-18.5 items-center justify-center rounded-3xl bg-main-light px-4 font-ui text-[30px] leading-none text-main"
        @click="$emit('adjust-distance', -5)"
      >
        -5
      </button>
      <button
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-main-light font-ui text-[30px] leading-none text-main"
        @click="$emit('adjust-distance', -1)"
      >
        -
      </button>
      <button
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-main-light text-main"
        aria-label="Edit distance"
        @click="toggleManualDistance"
      >
        <Icon icon="lucide:edit" class="h-6 w-6" />
      </button>
      <button
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-main-light font-ui text-[30px] leading-none text-main"
        @click="$emit('adjust-distance', 1)"
      >
        +
      </button>
      <button
        type="button"
        class="flex h-14 min-w-18.5 items-center justify-center rounded-3xl bg-main-light px-4 font-ui text-[30px] leading-none text-main"
        @click="$emit('adjust-distance', 5)"
      >
        +5
      </button>
    </div>
  </section>
</template>

<script>
import { Icon } from "@iconify/vue";

export default {
  components: {
    Icon,
  },
  emits: [
    "toggle-transport-menu",
    "select-transport-mode",
    "adjust-distance",
    "update-distance",
  ],
  props: {
    stepTitle: {
      type: String,
      required: true,
    },
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
    transportMode: {
      type: String,
      required: true,
    },
    transportOptions: {
      type: Array,
      required: true,
    },
    isTransportMenuOpen: {
      type: Boolean,
      required: true,
    },
    distance: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      isManualDistanceOpen: false,
      manualDistanceValue: this.distance,
    };
  },
  watch: {
    distance(newValue) {
      if (!this.isManualDistanceOpen) {
        this.manualDistanceValue = newValue;
      }
    },
  },
  methods: {
    toggleManualDistance() {
      this.isManualDistanceOpen = !this.isManualDistanceOpen;
      this.manualDistanceValue = this.distance;
    },
    applyManualDistance() {
      const parsedDistance = Number(this.manualDistanceValue);
      if (Number.isNaN(parsedDistance) || parsedDistance < 0) return;

      this.$emit("update-distance", parsedDistance);
      this.isManualDistanceOpen = false;
    },
    cancelManualDistance() {
      this.manualDistanceValue = this.distance;
      this.isManualDistanceOpen = false;
    },
  },
};
</script>
