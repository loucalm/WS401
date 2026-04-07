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
      Consumption
    </h2>

    <div class="relative mt-7">
      <button
        type="button"
        class="flex w-full items-center justify-between rounded-[18px] border-4 border-secondary bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
        @click="$emit('toggle-consumption-menu')"
      >
        <span class="font-ui text-[26px] leading-none text-black">{{
          consumptionSource
        }}</span>
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.6"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="h-7 w-7 text-black transition-transform"
          :class="isConsumptionMenuOpen ? 'rotate-180' : ''"
          aria-hidden="true"
        >
          <path d="m5 9 7 7 7-7" />
        </svg>
      </button>

      <div
        v-if="isConsumptionMenuOpen"
        class="absolute left-0 right-0 top-[calc(100%+10px)] z-10 overflow-hidden rounded-[18px] border-4 border-secondary bg-white shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
      >
        <button
          v-for="option in consumptionSources"
          :key="option"
          type="button"
          class="flex w-full items-center justify-between border-b-4 border-secondary-light px-4 py-3 text-left last:border-b-0"
          @click="$emit('select-consumption-source', option)"
        >
          <span class="font-ui text-[22px] leading-none text-black">{{
            option
          }}</span>
          <span
            v-if="consumptionSource === option"
            class="font-ui text-[18px] leading-none text-secondary"
            >OK</span
          >
        </button>
      </div>
    </div>

    <div class="mt-8 flex items-center gap-3">
      <h3 class="font-ui text-[28px] leading-none text-black">Surface</h3>
    </div>

    <div class="mt-8 flex justify-center">
      <div
        v-if="!isManualSurfaceOpen"
        class="inline-flex min-w-[210px] items-center justify-center rounded-[999px] bg-white px-10 py-9 shadow-[0_10px_18px_rgba(0,0,0,0.18)]"
      >
        <span class="font-ui text-[36px] leading-none text-black"
          >{{ surface }} M²</span
        >
      </div>

      <div
        v-else
        class="flex w-full max-w-[240px] items-center gap-3 rounded-[999px] bg-white px-4 py-4 shadow-[0_10px_18px_rgba(0,0,0,0.18)]"
      >
        <input
          v-model="manualSurfaceValue"
          type="number"
          min="0"
          step="1"
          class="w-full bg-transparent font-ui text-[26px] leading-none text-black outline-none"
          @keyup.enter="applyManualSurface"
        />
        <span class="font-ui text-[18px] leading-none text-black">M²</span>
      </div>
    </div>

    <div v-if="isManualSurfaceOpen" class="mt-3 flex justify-center gap-3">
      <button
        type="button"
        class="rounded-[18px] bg-secondary px-4 py-2 font-ui text-[18px] leading-none text-white"
        @click="applyManualSurface"
      >
        Confirm
      </button>
      <button
        type="button"
        class="rounded-[18px] border border-secondary bg-white px-4 py-2 font-ui text-[18px] leading-none text-secondary"
        @click="cancelManualSurface"
      >
        Cancel
      </button>
    </div>

    <div class="mt-10 flex items-center justify-between gap-3 px-1">
      <button
        type="button"
        class="flex h-14 min-w-[74px] items-center justify-center rounded-3xl bg-secondary-light px-4 font-ui text-[30px] leading-none text-secondary"
        @click="$emit('adjust-surface', -10)"
      >
        -10
      </button>
      <button
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-secondary-light font-ui text-[30px] leading-none text-secondary"
        @click="$emit('adjust-surface', -1)"
      >
        -
      </button>
      <button
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-secondary-light text-secondary"
        aria-label="Edit surface area"
        @click="toggleManualSurface"
      >
        <Icon icon="lucide:edit" class="h-6 w-6" />
      </button>
      <button
        type="button"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-secondary-light font-ui text-[30px] leading-none text-secondary"
        @click="$emit('adjust-surface', 1)"
      >
        +
      </button>
      <button
        type="button"
        class="flex h-14 min-w-[74px] items-center justify-center rounded-3xl bg-secondary-light px-4 font-ui text-[30px] leading-none text-secondary"
        @click="$emit('adjust-surface', 10)"
      >
        +10
      </button>
    </div>

    <div class="mt-9 flex items-center justify-between gap-3">
      <h3 class="font-ui text-[28px] leading-none text-black">Duration</h3>
      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary-light text-secondary"
        aria-label="Edit duration"
        @click="toggleManualDuration"
      >
        <Icon icon="lucide:edit" class="h-5 w-5" />
      </button>
    </div>

    <div class="mt-6">
      <input
        v-if="!isManualDurationOpen"
        :value="duration"
        type="range"
        min="0"
        max="24"
        step="1"
        class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-black/30 accent-secondary"
        @input="$emit('update-duration', Number($event.target.value))"
      />
      <div
        v-else
        class="flex w-full items-center gap-3 rounded-[18px] bg-white px-4 py-4 shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
      >
        <input
          v-model="manualDurationValue"
          type="number"
          min="0"
          max="24"
          step="1"
          class="w-full bg-transparent font-ui text-[26px] leading-none text-black outline-none"
          @keyup.enter="applyManualDuration"
        />
        <span class="font-ui text-[18px] leading-none text-black">H</span>
      </div>
      <div class="mt-2 flex items-center justify-between">
        <span
          v-for="mark in durationMarks"
          :key="mark"
          class="font-ui text-[18px] leading-none text-black"
          >{{ mark }}h</span
        >
      </div>
    </div>

    <div v-if="isManualDurationOpen" class="mt-3 flex justify-center gap-3">
      <button
        type="button"
        class="rounded-[18px] bg-secondary px-4 py-2 font-ui text-[18px] leading-none text-white"
        @click="applyManualDuration"
      >
        Confirm
      </button>
      <button
        type="button"
        class="rounded-[18px] border border-secondary bg-white px-4 py-2 font-ui text-[18px] leading-none text-secondary"
        @click="cancelManualDuration"
      >
        Cancel
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
    "toggle-consumption-menu",
    "select-consumption-source",
    "adjust-surface",
    "update-duration",
    "update-surface",
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
    consumptionSource: {
      type: String,
      required: true,
    },
    consumptionSources: {
      type: Array,
      required: true,
    },
    isConsumptionMenuOpen: {
      type: Boolean,
      required: true,
    },
    surface: {
      type: Number,
      required: true,
    },
    duration: {
      type: Number,
      required: true,
    },
    durationMarks: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      isManualSurfaceOpen: false,
      manualSurfaceValue: this.surface,
      isManualDurationOpen: false,
      manualDurationValue: this.duration,
    };
  },
  watch: {
    surface(newValue) {
      if (!this.isManualSurfaceOpen) {
        this.manualSurfaceValue = newValue;
      }
    },
    duration(newValue) {
      if (!this.isManualDurationOpen) {
        this.manualDurationValue = newValue;
      }
    },
  },
  methods: {
    toggleManualSurface() {
      this.isManualSurfaceOpen = !this.isManualSurfaceOpen;
      this.manualSurfaceValue = this.surface;
    },
    applyManualSurface() {
      const parsedSurface = Number(this.manualSurfaceValue);
      if (Number.isNaN(parsedSurface) || parsedSurface < 0) return;

      this.$emit("update-surface", parsedSurface);
      this.isManualSurfaceOpen = false;
    },
    cancelManualSurface() {
      this.manualSurfaceValue = this.surface;
      this.isManualSurfaceOpen = false;
    },
    toggleManualDuration() {
      this.isManualDurationOpen = !this.isManualDurationOpen;
      this.manualDurationValue = this.duration;
    },
    applyManualDuration() {
      const parsedDuration = Number(this.manualDurationValue);
      if (
        Number.isNaN(parsedDuration) ||
        parsedDuration < 0 ||
        parsedDuration > 24
      )
        return;

      this.$emit("update-duration", parsedDuration);
      this.isManualDurationOpen = false;
    },
    cancelManualDuration() {
      this.manualDurationValue = this.duration;
      this.isManualDurationOpen = false;
    },
  },
};
</script>
