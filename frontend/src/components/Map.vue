<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <main class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-32 shadow-[0_0_0_1px_rgba(0,0,0,0.04)] relative overflow-hidden">
      
      <div class="sticky top-0 z-40 flex items-center justify-between bg-white px-6 py-4 shadow-sm border-b border-grey/5">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 bg-main-light rounded-xl flex items-center justify-center text-main">
            <Icon icon="ph:map-trifold-bold" class="h-6 w-6" />
          </div>
          <h1 class="text-title-h4 text-black uppercase tracking-tight font-title"> {{ t("map.title") }}</h1>
        </div>
        
        <button 
          type="button" 
          class="flex h-12 w-12 items-center justify-center rounded-2xl transition-all active:scale-90 shadow-sm"
          :class="showLeaderboard ? 'bg-main text-white' : 'bg-main-light text-main'"
          @click="showLeaderboard = !showLeaderboard"
        >
          <Icon :icon="showLeaderboard ? 'ph:users-three-fill' : 'ph:users-three-bold'" class="h-7 w-7" />
        </button>
      </div>

      <Transition name="slide-down">
        <div v-if="showLeaderboard" class="absolute inset-x-0 top-[73px] z-30 bg-white border-b border-grey/10 shadow-2xl max-h-[60vh] overflow-y-auto rounded-b-[2.5rem]">
          <div class="p-6 space-y-4">
            <p class="text-body-12 text-grey font-bold uppercase tracking-widest px-2">{{ t("map.active_friends_online") }}</p>
            <div class="space-y-3">
              <article
                v-for="friend in friends"
                :key="friend.id"
                @click="focusFriend(friend)"
                class="flex items-center gap-4 rounded-2xl border border-grey/10 bg-main-light/30 px-4 py-3 active:scale-[0.98] transition-transform cursor-pointer"
              >
                <div class="relative h-12 w-12 shrink-0">
                  <img :src="friend.avatar" class="h-full w-full rounded-full object-cover border-2 border-white shadow-sm" />
                  <div v-if="friend.online" class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full bg-main border-2 border-white"></div>
                </div>
                <div class="flex-1">
                  <p class="font-bold text-black text-body-16 leading-tight">{{ friend.name }}</p>
                  <p class="text-body-12 text-grey font-medium uppercase">{{ t("map.rank_place", { rank: friend.rank }) }}</p>
                </div>
                <p class="font-title text-main font-bold text-body-16">{{ t("map.saved_kg", { weight: friend.weight }) }}</p>
              </article>
            </div>
          </div>
        </div>
      </Transition>

      <div 
        class="flex-1 relative bg-main-light/50 overflow-hidden touch-none" 
        @mousedown="startDragging" 
        @mousemove="drag" 
        @mouseup="stopDragging" 
        @mouseleave="stopDragging"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="stopDragging"
      >
        <div 
          class="absolute inset-0 flex items-center justify-center transition-transform duration-300 ease-out"
          :style="{ transform: `translate(${translateX}px, ${translateY}px) scale(${zoomLevel})` }"
        >
          <div class="relative inline-block">
            <img
              src="@/assets/img/svg/cartefrance.svg"
              alt="France Map"
              class="max-h-[80vh] w-auto pointer-events-none drop-shadow-2xl"
            />

            <div
              v-for="friend in friends"
              :key="friend.id"
              :style="{
                position: 'absolute',
                top: friend.position.top,
                left: friend.position.left,
                transform: `translate(-50%, -50%) scale(${Math.max(0.5, 1 / (zoomLevel * 0.8))})`,
              }"
              class="h-14 w-14 rounded-full border-4 border-white shadow-xl cursor-pointer active:scale-95 transition-all z-10"
              @click.stop="selectFriend(friend)"
            >
              <img :src="friend.avatar" :alt="friend.name" class="h-full w-full rounded-full object-cover" />
              <div v-if="friend.online" class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-main border-2 border-white animate-pulse"></div>
            </div>
          </div>
        </div>

        <div class="absolute bottom-10 right-6 flex flex-col gap-3 z-20">
          <button class="h-14 w-14 bg-white text-main rounded-2xl shadow-xl flex items-center justify-center font-bold text-2xl active:scale-90 border border-grey/5" @click="zoomIn">+</button>
          <button class="h-14 w-14 bg-white text-main rounded-2xl shadow-xl flex items-center justify-center font-bold text-2xl active:scale-90 border border-grey/5" @click="zoomOut">-</button>
        </div>

        <Transition name="fade">
          <div v-if="showPopup" class="absolute inset-x-6 bottom-10 z-30 rounded-[2rem] bg-white p-5 shadow-2xl border border-main/10 flex items-center gap-5 animate-zoom">
            <div class="h-20 w-20 rounded-full border-4 border-main-light overflow-hidden shrink-0 shadow-sm">
              <img :src="selectedUser.avatar" class="h-full w-full object-cover" />
            </div>
            <div class="flex-1 text-black text-left">
              <h3 class="font-title text-title-h4 uppercase leading-none">{{ selectedUser.name }}</h3>
              <p class="text-main font-bold text-body-16 mt-1">{{ t("map.saved_kg", { weight: selectedUser.weight }) }}</p>
              <p class="text-grey text-body-12 uppercase font-bold mt-1 tracking-tighter">{{ t("map.rank_label", { rank: selectedUser.rank }) }}</p>
            </div>
            <button @click="showPopup = false" class="bg-grey/10 p-2 rounded-full text-grey active:scale-75 transition-transform">
              <Icon icon="ph:x-bold" />
            </button>
          </div>
        </Transition>
      </div>

      <BottomNav active="map" />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Icon } from "@iconify/vue";
import { useI18n } from "vue-i18n";
import BottomNav from "./BottomNav.vue";

// Import images according to your assets structure
import rafaAvatar from "@/assets/img/persona.jpg";
// Note: Ensure persona2.jpg and persona3.jpg exist in your img folder!
import kenzaAvatar from "@/assets/img/persona2.jpg"; 
import kenzoAvatar from "@/assets/img/persona3.jpg";

const { t } = useI18n();

const zoomLevel = ref(1.2);
const translateX = ref(0);
const translateY = ref(0);
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const showLeaderboard = ref(false);
const showPopup = ref(false);
const selectedUser = ref({});

const friends = ref([
  { id: 1, name: "Rafamaru", rank: "1st", weight: 42.8, online: true, avatar: rafaAvatar, position: { top: "32%", left: "38%" } },
  { id: 2, name: "Kenza", rank: "5th", weight: 15.2, online: false, avatar: kenzaAvatar, position: { top: "58%", left: "62%" } },
  { id: 3, name: "Kenzo", rank: "6th", weight: 19.5, online: true, avatar: kenzoAvatar, position: { top: "45%", left: "42%" } },
]);

const initiateDrag = (clientX, clientY) => {
  isDragging.value = true;
  dragStart.value = { x: clientX - translateX.value, y: clientY - translateY.value };
};

const processMove = (clientX, clientY) => {
  if (!isDragging.value) return;
  translateX.value = clientX - dragStart.value.x;
  translateY.value = clientY - dragStart.value.y;
};

const startDragging = (e) => initiateDrag(e.clientX, e.clientY);
const drag = (e) => processMove(e.clientX, e.clientY);

const handleTouchStart = (e) => { if (e.touches.length === 1) initiateDrag(e.touches[0].clientX, e.touches[0].clientY); };
const handleTouchMove = (e) => { if (e.touches.length === 1) processMove(e.touches[0].clientX, e.touches[0].clientY); };
const stopDragging = () => isDragging.value = false;

const zoomIn = () => zoomLevel.value = Math.min(zoomLevel.value + 0.4, 4);
const zoomOut = () => zoomLevel.value = Math.max(zoomLevel.value - 0.4, 0.6);

const selectFriend = (friend) => {
  selectedUser.value = friend;
  showPopup.value = true;
};

const focusFriend = (friend) => {
  selectFriend(friend);
  showLeaderboard.value = false;
  translateX.value = 0;
  translateY.value = 0;
};
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-down-enter-from, .slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.font-title { font-family: var(--font-title); }
.text-main { color: var(--color-main); }
.bg-main { background-color: var(--color-main); }
.bg-main-light { background-color: var(--color-main-light); }

@keyframes zoomIn {
  from { opacity: 0; transform: scale(0.9) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-zoom { animation: zoomIn 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
</style>