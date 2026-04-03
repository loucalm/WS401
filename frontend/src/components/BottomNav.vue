<template>
  <nav class="fixed inset-x-0 bottom-0 z-30 px-4">
    <div
      class="mx-auto flex w-full max-w-190 items-end justify-between rounded-t-4xl bg-main px-5 pb-4 pt-5 text-white shadow-[0_-8px_24px_rgba(0,0,0,0.2)]"
    >
      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95 transition-colors hover:text-white"
        :class="active === 'home' ? 'text-white' : ''"
        @click="$emit('select', 'home')"
      >
        <Icon icon="material-symbols:home-rounded" class="h-8 w-8" />
      </button>

      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95 transition-colors hover:text-white"
        :class="active === 'pulse' ? 'text-white' : ''"
        @click="$emit('select', 'pulse')"
      >
        <Icon icon="cuida:heart-rate-outline" class="h-8 w-8" />
      </button>

      <button
        type="button"
        class="-mt-10 flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-main text-white shadow-[0_10px_24px_rgba(0,0,0,0.22)] transition-transform hover:scale-[1.03]"
        :class="active === 'add' ? 'ring-2 ring-white/40' : ''"
        @click="$emit('select', 'add')"
      >
        <Icon icon="prime:plus" class="h-13 w-13" />
      </button>

      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95 transition-colors hover:text-white"
        :class="active === 'map' ? 'text-white' : ''"
        @click="$emit('select', 'map')"
      >
        <Icon icon="material-symbols:map-outline" class="h-8 w-8" />
      </button>

      <button
        type="button"
        class="flex h-10 w-10 items-center justify-center rounded-full text-white/95 transition-colors hover:text-white"
        :class="active === 'profile' ? 'text-white' : ''"
        @click="$emit('select', 'profile')"
      >
        <Icon icon="mdi:user" class="h-8 w-8" />
      </button>
    </div>
  </nav>
</template>

<script setup>
import { Icon } from "@iconify/vue";

defineProps({
  active: {
    type: String,
    default: "home",
  },
});

defineEmits(["select"]);
</script>
