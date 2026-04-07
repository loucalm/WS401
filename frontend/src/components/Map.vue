<template>
  <div class="min-h-screen bg-white text-black pb-40">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white shadow-[0_0_0_1px_rgba(0,0,0,0.04)] lg:my-0 lg:rounded-none lg:px-10"
    >
      <!-- Header -->
      <div class="sticky top-0 z-20 flex items-center justify-between rounded-b-3xl bg-white px-4 py-3 shadow-sm">
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg transition-colors hover:bg-main-light"
          @click="closeMap"
        >
          <Icon icon="material-symbols:close" class="h-6 w-6 text-black" />
        </button>
        <h1 class="text-title-h4 text-black">Map</h1>
        <div class="w-10"></div>
      </div>

      <!-- Map Container -->
      <div class="flex-1 overflow-y-auto px-4 py-6">
        <!-- Carte SVG -->
        <div class="relative mx-auto w-full max-w-2xl">
          <div class="relative">
            <!-- Carte de France en SVG -->
            <img
              src="@/assets/img/svg/cartefrance.svg"
              alt="Carte de France"
              class="w-full h-auto drop-shadow-md map-svg"
            />

            <!-- Popup info utilisateur -->
            <div
              v-if="showPopup"
              class="absolute left-1/2 top-1/3 z-10 -translate-x-1/2 rounded-2xl bg-white px-4 py-3 shadow-lg border border-grey/20"
            >
              <button
                type="button"
                class="absolute right-2 top-2 text-grey hover:text-black"
                @click="showPopup = false"
              >
                <Icon icon="material-symbols:close" class="h-5 w-5" />
              </button>
              <div class="flex items-center gap-3 pr-6">
                <img
                  :src="selectedUser.avatar"
                  :alt="selectedUser.name"
                  class="h-12 w-12 rounded-full object-cover"
                />
                <div>
                  <p class="font-bold text-black text-body-16">{{ selectedUser.name }}</p>
                  <p class="text-grey text-body-12">{{ selectedUser.rank }}</p>
                  <p class="font-bold text-main text-body-16">{{ selectedUser.weight }} kg</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Friends Section -->
        <div class="mt-8">
          <h2 class="text-title-h3 text-black mb-4">Friends</h2>
          <div class="space-y-3">
            <div
              v-for="friend in friends"
              :key="friend.id"
              class="flex items-center justify-between rounded-2xl bg-white p-4 border border-grey/10 shadow-sm hover:shadow-md transition-shadow"
            >
              <div class="flex items-center gap-3">
                <img
                  :src="friend.avatar"
                  :alt="friend.name"
                  class="h-12 w-12 rounded-full object-cover"
                />
                <div>
                  <p class="font-bold text-black text-body-16">{{ friend.name }}</p>
                  <p class="text-grey text-body-12">{{ friend.weight }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <div
                  :class="[
                    'h-3 w-3 rounded-full',
                    friend.online ? 'bg-main' : 'bg-systeme'
                  ]"
                ></div>
                <span class="text-grey text-body-12">
                  {{ friend.online ? 'Online' : 'Offline' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Bottom Navigation -->
    <BottomNav @select="handleNavSelect" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'
import BottomNav from './BottomNav.vue'

const router = useRouter()
const showPopup = ref(false)
const selectedUser = ref({
  name: 'Jules.F',
  rank: '2nd',
  weight: '4',
  avatar: 'https://via.placeholder.com/100?text=Jules'
})

const friends = ref([
  {
    id: 1,
    name: 'Kenzo',
    weight: '6e : 19 kg',
    online: true,
    avatar: 'https://via.placeholder.com/100?text=Kenzo'
  },
  {
    id: 2,
    name: 'Kenza',
    weight: '5e : 15 kg',
    online: false,
    avatar: 'https://via.placeholder.com/100?text=Kenza'
  }
])

function closeMap() {
  router.push({ name: 'dashboard' })
}

function handleNavSelect(target) {
  // Autres navigations gérées par BottomNav
}
</script>

<style scoped>
/* Les styles sont gérés par Tailwind et app.css */
.map-svg {
  background: white;
  border-radius: 0.5rem;
}

/* Styles pour les régions SVG */
:deep(path) {
  fill: #6f9c76;
  stroke: #ffffff;
  stroke-width: 0.5;
  cursor: pointer;
  transition: fill 0.3s ease;
}

:deep(path:hover) {
  fill: #5a8563;
}
</style>

