<template>
  <nav class="fixed inset-x-0 bottom-0 z-20 bg-transparent px-4 pb-4">
    <div
      class="mx-auto flex w-full max-w-105 items-end justify-between rounded-t-4xl bg-main px-6 pb-4 pt-5 text-white shadow-[0_-8px_24px_rgba(0,0,0,0.2)]"
    >
      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95"
        :class="active === 'home' ? 'text-white' : ''"
        @click="$emit('select', 'home')"
      >
        <HomeIcon class="h-8 w-8" />
      </button>

      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95"
        :class="active === 'pulse' ? 'text-white' : ''"
        @click="$emit('select', 'pulse')"
      >
        <PulseIcon class="h-8 w-8" />
      </button>

      <button
        type="button"
        class="-mt-10 flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-main text-white shadow-[0_10px_24px_rgba(0,0,0,0.22)]"
        :class="active === 'add' ? 'ring-2 ring-white/40' : ''"
        @click="$emit('select', 'add')"
      >
        <PlusIcon class="h-10 w-10" />
      </button>

      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95"
        :class="active === 'map' ? 'text-white' : ''"
        @click="$emit('select', 'map')"
      >
        <MapIcon class="h-8 w-8" />
      </button>

      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95"
        :class="active === 'profile' ? 'text-white' : ''"
        @click="$emit('select', 'profile')"
      >
        <ProfileIcon class="h-8 w-8" />
      </button>
    </div>
  </nav>
</template>

<script setup>
defineProps({
  active: {
    type: String,
    default: "home",
  },
});

defineEmits(["select"]);

const HomeIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M3 11.5 12 4l9 7.5" />
      <path d="M5 10.8V20h14v-9.2" />
      <path d="M9.2 20v-6h5.6v6" />
    </svg>
  `,
};

const PulseIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M2 12h4l2.2-5 4.2 10 2.6-5H22" />
    </svg>
  `,
};

const PlusIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 5v14" />
      <path d="M5 12h14" />
    </svg>
  `,
};

const MapIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M9 4 3 7v13l6-3 6 3 6-3V4l-6 3-6-3Z" />
      <path d="M9 4v13" />
      <path d="M15 7v13" />
    </svg>
  `,
};

const ProfileIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M20 21a8 8 0 0 0-16 0" />
      <circle cx="12" cy="8" r="4" />
    </svg>
  `,
};
</script>
