<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <main class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28 shadow-[0_0_0_1px_rgba(0,0,0,0.04)]">
      
      <Transition name="fade">
        <div v-if="zoomedCard" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-md flex items-center justify-center p-4">
          <button @click="zoomedCard = null" class="absolute top-8 right-6 z-[110] bg-white rounded-full p-3 shadow-xl active:scale-90 transition-transform">
            <Icon icon="ph:x-bold" class="h-8 w-8 text-main" />
          </button>

          <div class="w-full max-w-lg bg-main rounded-[2.5rem] p-6 shadow-2xl animate-zoom">
            <h2 class="text-white font-title text-title-h4 uppercase text-center mb-6">{{ getTitle(zoomedCard) }}</h2>
            <div class="bg-white rounded-3xl p-6 min-h-[380px] flex flex-col justify-center">
              
              <div v-if="zoomedCard === 'weekly'" class="w-full flex flex-col gap-6 items-center text-black">
                <div class="p-6 bg-main-light rounded-2xl w-full text-center border border-main/10">
                  <p class="text-body-12 font-bold text-grey uppercase tracking-widest mb-2">Total Impact</p>
                  <h3 class="font-title text-title-h2 text-main">+ 5.23 KG CO2</h3>
                  <p class="text-body-12 text-grey font-medium mt-2 italic text-center">Current week analysis</p>
                </div>
                <div class="w-full space-y-3">
                  <div class="flex justify-between items-center p-4 bg-secondary-light/20 rounded-2xl border border-grey/10">
                    <div class="flex items-center gap-3 font-bold text-body-16"><Icon icon="ph:trend-down-bold" class="text-main h-6 w-6" />Performance</div>
                    <span class="text-main font-bold">-12% vs last wk</span>
                  </div>
                  <div class="flex justify-between items-center p-4 bg-secondary-light/20 rounded-2xl border border-grey/10">
                    <div class="flex items-center gap-3 font-bold text-body-16"><Icon icon="ph:users-three-bold" class="text-main h-6 w-6" />Community</div>
                    <span class="text-grey font-bold">Top 15%</span>
                  </div>
                </div>
              </div>

              <div v-if="zoomedCard === 'progression'" class="w-full space-y-6">
                <div class="flex bg-main-light rounded-full p-1">
                  <button v-for="p in ['day', 'week', 'month']" :key="p" @click="activePeriod = p" class="flex-1 py-2 rounded-full text-body-12 font-bold uppercase" :class="activePeriod === p ? 'bg-main text-white' : 'text-main'">{{p}}</button>
                </div>
                <div class="h-48 flex items-end justify-between gap-3 px-2">
                  <div v-for="(val, i) in currentData.values" :key="i" class="w-full bg-main rounded-t-xl relative transition-all duration-700" :style="{ height: `${(val / currentData.max) * 100}%` }">
                    <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-body-12 font-bold text-main whitespace-nowrap">{{val}} kg</span>
                  </div>
                </div>
                <div class="flex justify-between font-bold text-body-12 text-main uppercase">
                  <span v-for="l in currentData.labels" :key="l">{{l}}</span>
                </div>
              </div>

              <div v-if="zoomedCard === 'challenges'" class="w-full space-y-6">
                <div v-for="c in challenges" :key="c.title" class="space-y-2 text-black text-body-16">
                  <div class="flex justify-between font-bold">
                    <p>{{ c.title }}</p>
                    <p class="text-main">{{ c.progress }}%</p>
                  </div>
                  <div class="bg-grey/15 h-4 rounded-full overflow-hidden shadow-inner relative">
                    <div class="absolute inset-y-0 left-0 bg-main h-full rounded-full transition-all duration-1000" :style="{ width: c.progress + '%' }"></div>
                  </div>
                </div>
                <div class="h-px bg-grey/10 w-full my-4"></div>
                <p class="text-body-12 font-bold text-grey uppercase tracking-widest text-center">Your Achievement Badges</p>
                <div class="flex justify-around items-center">
                  <div v-for="b in badges" :key="b.name" class="flex flex-col items-center gap-2">
                    <div class="h-14 w-14 rounded-full flex items-center justify-center text-main border-2 border-main/20" :class="b.locked ? 'bg-grey/10 grayscale opacity-40' : 'bg-main-light'">
                      <Icon :icon="b.icon" class="h-8 w-8" />
                    </div>
                    <span class="text-body-8 font-bold text-black text-center uppercase">{{ b.name }}</span>
                  </div>
                </div>
              </div>

              <div v-if="zoomedCard === 'split'" class="h-80 w-full">
                <Pie :data="chartData" :options="chartOptions" />
              </div>

            </div>
          </div>
        </div>
      </Transition>

      <header class="flex flex-col items-center pt-8 px-4 relative z-10">
        <div class="relative h-32 w-32 rounded-full border-4 border-main overflow-hidden shadow-lg">
          <img :src="personaSrc" alt="Profile" class="h-full w-full object-cover" />
        </div>
        
        <div class="mt-4 w-full rounded-3xl p-6 shadow-xl text-center border border-grey/5 bg-white">
          <h1 class="font-title text-title-h3 uppercase leading-none text-black tracking-tight">Rafamaru Teixeira</h1>
          <p class="text-main font-medium mt-1 text-body-16">Climate Protector - Lvl 12</p>
          <div class="h-px bg-grey/20 w-full my-4"></div>
          <div class="flex justify-around items-center text-black">
            <div class="text-center">
              <p class="text-body-12 font-bold uppercase">Friends :</p>
              <p class="text-body-16 font-semibold">25</p>
            </div>
            <div class="w-px bg-grey/20 h-10"></div>
            <div class="text-center">
              <p class="text-body-12 font-bold uppercase">Saved :</p>
              <p class="text-body-16 font-semibold">+120kg CO2</p>
            </div>
          </div>
        </div>
      </header>

      <section class="mt-8 px-4 grid grid-cols-1 gap-6 relative z-20">
        
        <article @click="zoomedCard = 'weekly'" class="group bg-main rounded-[2rem] p-6 flex flex-col shadow-lg cursor-pointer active:scale-95 transition-transform">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-body-16">Weekly comparison</h2>
            <Icon icon="ph:magnifying-glass-plus-bold" class="text-white/40 h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity" />
          </div>
          <div class="bg-white/90 rounded-2xl p-6 flex items-center justify-around text-center">
            <div>
              <p class="text-body-12 text-black font-bold uppercase mb-1">You</p>
              <div class="bg-main text-white py-3 px-6 rounded-xl font-title text-title-h4 shadow-inner">+ 5KG</div>
            </div>
            <div class="h-16 w-px bg-grey/20"></div>
            <div>
              <p class="text-body-12 text-grey font-bold uppercase mb-1">National average</p>
              <div class="flex items-center justify-center gap-2 bg-main-light text-main py-3 px-5 rounded-xl font-bold text-body-16">
                <Icon icon="ph:users-three-fill" /> + 3kg
              </div>
            </div>
          </div>
        </article>

        <article @click="zoomedCard = 'progression'" class="group bg-main rounded-[2rem] p-6 shadow-lg flex flex-col cursor-pointer active:scale-95 transition-transform">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-body-16">Progression</h2>
            <Icon icon="ph:magnifying-glass-plus-bold" class="text-white/40 h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity" />
          </div>
          <div class="bg-white/95 rounded-2xl p-5 flex flex-col justify-between overflow-hidden">
            <p class="text-body-12 text-grey font-bold italic mb-4 uppercase tracking-tighter">kg CO2</p>
            <div class="flex items-end justify-between h-32 px-1 gap-2">
              <div v-for="(val, index) in currentData.values" :key="index" class="w-full bg-main rounded-t-lg" :style="{ height: `${(val / currentData.max) * 100}%`, opacity: 0.4 + (index * 0.15) }"></div>
            </div>
            <div class="flex justify-between mt-4">
               <div v-for="(label, index) in currentData.labels" :key="index" class="text-body-8 text-center font-bold text-black flex-1 uppercase leading-tight">
                 {{ label }}<br/><span class="font-normal text-grey">{{ currentData.values[index] }}kg</span>
               </div>
            </div>
          </div>
        </article>

      </section>
    </main>

    <BottomNav active="pulse" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Icon } from "@iconify/vue";
import BottomNav from "./BottomNav.vue";
import personaSrc from "../assets/img/persona.jpg";
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const zoomedCard = ref(null);
const activePeriod = ref('day');

const getTitle = (id) => {
  const t = { weekly: 'Weekly comparison', progression: 'Progression Details', challenges: 'Challenges & Badges', split: 'Footprint Split' };
  return t[id];
};

// labels matching original request
const statsData = {
  day: { labels: ['Morning', 'Noon', 'After.', 'Eve.'], values: [0.4, 2.1, 1.2, 0.8], max: 3 },
  week: { labels: ['Wk.10', 'Wk.11', 'Wk.12', 'Wk.13'], values: [2, 3, 8, 12], max: 15 },
  month: { labels: ['Jan.', 'Feb.', 'Mar.', 'Apr.'], values: [45, 38, 52, 30], max: 60 }
};

const challenges = [
  { title: "1 week without a car", icon: "ph:bicycle-bold", progress: 80 },
  { title: "3 days vegetarian", icon: "ph:leaf-bold", progress: 45 }
];

const badges = [
  { name: 'Eco-Root', icon: 'ph:leaf-fill', locked: false },
  { name: 'Cyclist', icon: 'ph:bicycle-fill', locked: false },
  { name: 'Energy', icon: 'ph:lightning-fill', locked: true }
];

const currentData = computed(() => statsData[activePeriod.value]);
const chartData = computed(() => ({
  labels: ['Transport', 'Food', 'Shopping', 'Energy'],
  datasets: [{ backgroundColor: ['#117d6f', '#19B4A0', '#4B6481', '#a1a1aa'], borderWidth: 2, borderColor: '#ffffff', data: [45, 25, 18, 12] }]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, family: 'Luciole' }, color: '#117d6f', padding: 15 } } }
};
</script>

<style scoped>
/* Mapping logic helpers for theme variables from app.css */
.font-title { font-family: var(--font-title); }
.font-ui { font-family: var(--font-ui); }
.text-main { color: var(--color-main); }
.text-grey { color: var(--color-grey); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@keyframes zoomIn {
  from { opacity: 0; transform: scale(0.9) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-zoom { animation: zoomIn 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
</style>