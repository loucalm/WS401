<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28 shadow-[0_0_0_1px_rgba(0,0,0,0.04)] lg:my-0 lg:rounded-none lg:px-10"
    >
      <!-- Profile Header -->
      <section class="px-4 pt-6 pb-4">
        <div class="flex items-start justify-between">
          <div class="rounded-xl border border-grey/20 bg-white px-4 py-2.5 shadow-[0_3px_8px_rgba(0,0,0,0.08)]">
            <p class="font-ui text-[14px] font-bold text-main leading-none uppercase tracking-wide">My Points</p>
            <p class="mt-1 font-ui text-[15px] font-bold text-main leading-none">+ 560 points</p>
          </div>

          <div class="flex gap-3.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-main-light text-main cursor-pointer">
              <Icon icon="ph:share-network" class="h-5.5 w-5.5" />
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#F4F4F4] text-grey cursor-pointer">
              <Icon icon="ph:bell" class="h-5.5 w-5.5" />
            </div>
          </div>
        </div>
      </section>

      <!-- User Avatar -->
      <div class="flex justify-center py-4">
        <div class="h-36 w-36 rounded-full border-4 border-main/85 bg-white shadow-md overflow-hidden flex items-center justify-center">
          <img
            :src="personaSrc"
            alt="Profile Avatar"
            class="h-full w-full object-cover scale-125 grayscale"
          />
        </div>
      </div>

      <!-- User Info Card -->
      <section class="px-7 pb-4">
        <div class="rounded-2xl border border-grey/15 bg-white px-7 py-6 text-center shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
          <h1 class="font-title text-[40px] leading-tight text-black tracking-tight">Rafamaru Teixeira</h1>
          <p class="font-ui text-[16px] font-medium text-main mt-1.5">Climate Protector - Lvl 12</p>

          <div class="mt-5 flex items-center justify-around border-t border-grey/10 pt-5">
            <p class="font-ui text-[15px] text-black">
              <span class="font-bold">Friends :</span> 25
            </p>
            <div class="h-7 w-px bg-grey/20"></div>
            <p class="font-ui text-[15px] text-black">
              <span class="font-bold">Saved :</span> +120kg CO2
            </p>
          </div>
        </div>
      </section>

      <!-- Data Cards Grid -->
      <section class="px-4 pt-4">
        <div class="grid grid-cols-2 gap-4">
          <!-- Weekly Comparison Card -->
          <div class="rounded-3xl border border-main bg-main p-4 text-white min-h-56 flex flex-col">
            <h3 class="font-semibold text-[16px] font-ui">Weekly comparison</h3>
            
            <div class="mt-4 flex-1 flex flex-col justify-center">
              <div class="bg-white/20 rounded-2xl p-4 mb-3">
                <p class="font-ui text-[14px] font-medium opacity-90">You</p>
                <p class="font-title text-[28px] font-semibold mt-1">+ 5kg</p>
              </div>

              <div class="bg-white/10 rounded-2xl p-4">
                <p class="font-ui text-[14px] font-medium opacity-90 flex items-center gap-2">
                  <Icon icon="ph:users" class="h-4 w-4" />
                  National average
                </p>
                <p class="font-title text-[28px] font-semibold mt-1">+ 3kg</p>
              </div>
            </div>
          </div>

          <!-- Progression Card -->
          <div class="rounded-3xl border border-main bg-main p-4 text-white min-h-56 flex flex-col">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-[16px] font-ui">Progression</h3>
              <div class="flex gap-2">
                <button class="text-[12px] px-2 py-1 rounded-full bg-white/20 font-ui font-semibold hover:bg-white/30">day</button>
                <button class="text-[12px] px-2 py-1 rounded-full bg-white/40 font-ui font-semibold">week</button>
                <button class="text-[12px] px-2 py-1 rounded-full bg-white/20 font-ui font-semibold hover:bg-white/30">month</button>
              </div>
            </div>

            <div class="mt-4 flex-1 flex flex-col justify-end pb-2">
              <div class="mb-3">
                <p class="font-ui text-[12px] opacity-80 mb-2">kg CO2</p>
              </div>
              <div class="h-32 flex items-end gap-1">
                <div class="flex-1 bg-white/30 rounded-md" style="height: 40%;"></div>
                <div class="flex-1 bg-white/50 rounded-md" style="height: 60%;"></div>
                <div class="flex-1 bg-white/50 rounded-md" style="height: 55%;"></div>
                <div class="flex-1 bg-white/60 rounded-md" style="height: 75%;"></div>
              </div>
              <div class="flex gap-1 justify-between text-[11px] font-ui mt-2 opacity-70">
                <span>Sem.10</span>
                <span>Sem.11</span>
                <span>Sem.12</span>
                <span>Sem.13</span>
              </div>
            </div>
          </div>

          <!-- Empty Card 1 -->
          <div class="rounded-3xl border border-main bg-main min-h-56"></div>

          <!-- Empty Card 2 -->
          <div class="rounded-3xl border border-main bg-main min-h-56"></div>
        </div>
      </section>
    </main>

    <BottomNav active="data" />
  </div>
</template>

<script setup>
import { Icon } from "@iconify/vue";
import BottomNav from "./BottomNav.vue";
import personaSrc from "../assets/img/persona.jpg";
</script>

<style scoped>
:root {
  --color-main: #117d6f;
}
</style>
