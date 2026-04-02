<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28 shadow-[0_0_0_1px_rgba(0,0,0,0.04)] lg:my-0 lg:rounded-none lg:px-10"
    >
      <section
        class="relative overflow-hidden rounded-b-[36px] bg-main-light px-4 pb-6 pt-4"
      >
        <div
          class="absolute inset-x-0 bottom-6 h-36 bg-[radial-gradient(circle_at_20%_40%,rgba(17,125,111,0.18),transparent_32%),radial-gradient(circle_at_80%_30%,rgba(17,125,111,0.12),transparent_28%)] opacity-80"
        ></div>

        <div class="relative flex flex-col items-center">
          <div
            class="relative flex h-64 w-64 items-center justify-center rounded-full border-8 border-main/85 bg-white shadow-[0_0_0_1px_rgba(17,125,111,0.12)]"
          >
            <div
              class="absolute inset-4 rounded-full border border-dashed border-grey/50"
            ></div>
            <div
              class="absolute -right-2 top-1/2 h-7 w-7 -translate-y-1/2 rounded-full border-4 border-main bg-white"
            ></div>

            <div
              class="relative z-10 flex flex-col items-center justify-center text-center"
            >
              <img
                :src="mascotSrc"
                alt="Mascot"
                class="mb-2 h-24 w-24 object-contain"
              />
              <p class="font-title text-[38px] leading-none text-black">
                30 kg
              </p>
              <p class="mt-2 text-[14px] text-grey">CO2 / Daily</p>
            </div>
          </div>

          <div
            class="relative z-10 mt-4 w-full rounded-2xl border border-grey/20 bg-white px-4 py-2 text-center shadow-[0_8px_18px_rgba(0,0,0,0.14)]"
          >
            <p class="font-title text-[28px] leading-none text-main">
              MY POINTS
            </p>
            <p class="mt-1 font-ui text-[18px] font-bold text-main">
              + 560 n2e points
            </p>
          </div>

          <button
            type="button"
            class="mt-3 flex w-full items-center justify-center rounded-full border border-grey/20 bg-white px-4 py-2 text-[13px] font-medium text-black shadow-[0_8px_18px_rgba(0,0,0,0.14)]"
          >
            Friends Leaderboard (live)
            <span class="ml-2 text-[18px] leading-none">⌄</span>
          </button>
        </div>
      </section>

      <section class="px-4 pt-5">
        <h2 class="font-ui text-[18px] font-semibold text-black">
          Latest Activities
        </h2>

        <div class="mt-4 space-y-4">
          <article
            v-for="activity in activities"
            :key="activity.title"
            class="flex items-center gap-4 rounded-2xl border border-grey/15 bg-white px-3 py-3 shadow-[0_6px_16px_rgba(0,0,0,0.14)]"
          >
            <div
              class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-main-light text-main"
            >
              <component :is="activity.icon" class="h-8 w-8" />
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-ui text-body-16 font-medium text-black">
                    {{ activity.title }}
                  </p>
                  <p
                    class="mt-1 text-[14px] font-medium"
                    :class="activity.pointsClass"
                  >
                    {{ activity.points }}
                  </p>
                </div>
                <p class="shrink-0 font-ui text-[18px] font-medium text-black">
                  {{ activity.distance }}
                </p>
              </div>

              <p
                class="mt-1 text-right text-[14px] font-medium"
                :class="activity.co2Class"
              >
                {{ activity.co2 }}
              </p>
            </div>
          </article>
        </div>
      </section>
    </main>

    <BottomNav active="home" />
  </div>
</template>

<script setup>
import mascotSrc from "../assets/mascotte_neutre.svg";
import BottomNav from "./BottomNav.vue";

const ElectricCarIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M4 14.5h16l-1.2-4.1A2 2 0 0 0 16.9 9H7.1a2 2 0 0 0-1.9 1.4L4 14.5Z" />
      <path d="M5 14.5v3" />
      <path d="M19 14.5v3" />
      <circle cx="7.5" cy="17.5" r="1.2" />
      <circle cx="16.5" cy="17.5" r="1.2" />
      <path d="M10.5 7.5 9 10h2l-1 2.5" />
    </svg>
  `,
};

const CarIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M4 14.5h16l-1.2-4.1A2 2 0 0 0 16.9 9H7.1a2 2 0 0 0-1.9 1.4L4 14.5Z" />
      <path d="M5 14.5v3" />
      <path d="M19 14.5v3" />
      <circle cx="7.5" cy="17.5" r="1.2" />
      <circle cx="16.5" cy="17.5" r="1.2" />
    </svg>
  `,
};

const RunIcon = {
  template: `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <circle cx="15" cy="5" r="2" />
      <path d="M9 21l2-5 3-2 2 3 3 1" />
      <path d="M9 16l4-3 2-4" />
      <path d="M7 20h3" />
      <path d="M16 12l3-1" />
    </svg>
  `,
};

const activities = [
  {
    title: "Electric car",
    points: "+150 n2e points",
    pointsClass: "text-main",
    distance: "25 km",
    co2: "-3,5 kg CO2",
    co2Class: "text-main",
    icon: ElectricCarIcon,
  },
  {
    title: "Car",
    points: "+0 n2e points",
    pointsClass: "text-grey",
    distance: "16 km",
    co2: "+4,2 kg CO2",
    co2Class: "text-systeme",
    icon: CarIcon,
  },
  {
    title: "Run",
    points: "+120 n2e points",
    pointsClass: "text-main",
    distance: "6 km",
    co2: "0,00 kg CO2",
    co2Class: "text-main",
    icon: RunIcon,
  },
];
</script>
