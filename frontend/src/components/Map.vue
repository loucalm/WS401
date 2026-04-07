<template>
  <div class="min-h-screen bg-white text-black pb-40">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white shadow-[0_0_0_1px_rgba(0,0,0,0.04)] lg:my-0 lg:rounded-none lg:px-10"
    >
      <!-- Header -->
      <div
        class="sticky top-0 z-20 flex items-center justify-between rounded-b-3xl bg-white px-4 py-3 shadow-sm"
      >
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg transition-colors hover:bg-main-light"
          @click="closeMap"
        >
          <Icon icon="material-symbols:close" class="h-6 w-6 text-black" />
        </button>
        <h1 class="text-title-h4 text-black">Map</h1>
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg transition-colors hover:bg-main-light"
          @click="toggleFriendsTab"
        >
          <Icon icon="material-symbols:group" class="h-6 w-6 text-black" />
        </button>
      </div>

     
      <div class="flex-1 overflow-y-auto px-4 py-6">
        
        <div class="relative mx-auto w-full max-w-4xl">
          <div class="relative">
           
            <div
              class="map-container"
              @mousedown="startDragging"
              @mousemove="drag"
              @mouseup="stopDragging"
              @mouseleave="stopDragging"
            >
              <img
                src="@/assets/img/svg/cartefrance.svg"
                alt="Carte de France"
                class="w-full h-auto drop-shadow-md map-svg"
                :style="{
                  transform: `scale(${zoomLevel}) translate(${translateX}px, ${translateY}px)`
                }"
              />

              <!-- Icônes rondes sur la carte -->
              <div
                v-for="friend in friends"
                :key="friend.id"
                :style="{
                  position: 'absolute',
                  top: friend.position.top,
                  left: friend.position.left,
                  transform: 'translate(-50%, -50%)',
                }"
                class="absolute h-12 w-12 rounded-full border-2 border-white shadow-md cursor-pointer"
                @click="selectFriend(friend)"
              >
                <img
                  :src="friend.avatar"
                  :alt="friend.name"
                  class="h-full w-full rounded-full object-cover"
                />
              </div>
            </div>

          
            <div class="absolute top-4 right-4 flex flex-col gap-2">
              <button
                class="p-2 bg-main text-white rounded-full shadow-md hover:bg-main-dark"
                @click="zoomIn"
              >
                +
              </button>
              <button
                class="p-2 bg-main text-white rounded-full shadow-md hover:bg-main-dark"
                @click="zoomOut"
              >
                -
              </button>
            </div>

         
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
                  <p class="font-bold text-black text-body-16">
                    {{ selectedUser.name }}
                  </p>
                  <p class="text-grey text-body-12">{{ selectedUser.rank }}</p>
                  <p class="font-bold text-main text-body-16">
                    {{ selectedUser.weight }} kg
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

     
        <transition name="slide-fade">
          <div
            v-if="showLeaderboard"
            class="relative mx-auto w-full max-w-4xl mt-8 bg-white p-4 rounded-2xl shadow-md"
          >
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-title-h3 text-black">Leaderboard</h2>
              <button
                class="text-grey hover:text-black"
                @click="toggleLeaderboard"
              >
                <Icon icon="material-symbols:close" class="h-6 w-6" />
              </button>
            </div>
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
                    <p class="font-bold text-black text-body-16">
                      {{ friend.name }}
                    </p>
                    <p class="text-grey text-body-12">{{ friend.weight }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <div
                    :class="[
                      'h-3 w-3 rounded-full',
                      friend.online ? 'bg-main' : 'bg-systeme',
                    ]"
                  ></div>
                  <span class="text-grey text-body-12">
                    {{ friend.online ? "Online" : "Offline" }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Bottom Navigation -->
      <BottomNav @select="handleNavSelect" />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import BottomNav from "./BottomNav.vue";

const router = useRouter();
const zoomLevel = ref(1);
const translateX = ref(0);
const translateY = ref(0);
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const showLeaderboard = ref(true);
const showPopup = ref(false);
const selectedUser = ref({
  name: "",
  rank: "",
  weight: "",
  avatar: "",
});
const friends = ref([
  {
    id: 1,
    name: "Kenzo",
    rank: "6e",
    weight: 19,
    online: true,
    avatar: "https://via.placeholder.com/100?text=Kenzo",
    position: { top: "40%", left: "30%" },
  },
  {
    id: 2,
    name: "Kenza",
    rank: "5e",
    weight: 15,
    online: false,
    avatar: "https://via.placeholder.com/100?text=Kenza",
    position: { top: "50%", left: "60%" },
  },
]);

function zoomIn() {
  zoomLevel.value = Math.min(zoomLevel.value + 0.2, 3);
}

function zoomOut() {
  zoomLevel.value = Math.max(zoomLevel.value - 0.2, 1);
}

function startDragging(event) {
  isDragging.value = true;
  dragStart.value = { x: event.clientX, y: event.clientY };
}

function drag(event) {
  if (!isDragging.value) return;
  const deltaX = event.clientX - dragStart.value.x;
  const deltaY = event.clientY - dragStart.value.y;
  translateX.value += deltaX / zoomLevel.value;
  translateY.value += deltaY / zoomLevel.value;
  dragStart.value = { x: event.clientX, y: event.clientY };
}

function stopDragging() {
  isDragging.value = false;
}

function selectFriend(friend) {
  selectedUser.value = friend;
  showPopup.value = true;
}

function toggleLeaderboard() {
  showLeaderboard.value = !showLeaderboard.value;
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
  transition: transform 0.3s ease;
}
.map-container {
  overflow: hidden;
  position: relative;
  width: 100%;
  height: 100%;
}
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>
