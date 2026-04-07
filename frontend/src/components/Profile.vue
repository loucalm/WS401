<template>
  <div class="min-h-screen bg-white text-black">
    <main
        class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28"
    >

      <!-- Header row -->
      <div class="flex items-start justify-between px-4 pt-3">

        <!-- My Points : petite bulle compacte -->
        <div class="rounded-xl border border-grey/20 bg-white px-3 py-1.5 shadow-[0_2px_8px_rgba(0,0,0,0.10)]">
          <p class="font-ui text-[10px] font-bold text-main leading-none">My Points</p>
          <p class="mt-0.5 font-ui text-[10px] font-bold text-main leading-none">+ 560 points</p>
        </div>

        <!-- Icons -->
        <div class="flex gap-2">
          <div class="flex h-8 w-8 items-center justify-center rounded-full bg-main-light text-main">
            <Icon icon="ph:share-network" class="h-4 w-4" />
          </div>
          <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#F4F4F4] text-grey">
            <Icon icon="ph:bell" class="h-4 w-4" />
          </div>
        </div>
      </div>

      <!-- Avatar -->
      <div class="flex justify-center mt-2">
        <div class="h-24 w-24 rounded-full border-4 border-main/85 bg-white shadow-md overflow-hidden">
          <img
              src="https://i.pravatar.cc/256?u=rafamaru"
              alt="Profile Avatar"
              class="h-full w-full object-cover"
          />
        </div>
      </div>

      <!-- Name card -->
      <section class="px-4 mt-3">
        <div class="rounded-2xl border border-grey/15 bg-white px-4 py-3 text-center shadow-[0_4px_12px_rgba(0,0,0,0.08)]">
          <h1 class="font-title text-[20px] leading-none text-black">Rafamaru Teixeira</h1>
          <p class="font-ui text-[12px] font-medium text-main mt-1">Climate Protector - Lvl 12</p>
          <div class="mt-2 flex items-center justify-around border-t border-grey/10 pt-2">
            <p class="font-ui text-[11px] text-black"><span class="font-bold">Friends :</span> 25</p>
            <div class="h-5 w-px bg-grey/20"></div>
            <p class="font-ui text-[11px] text-black"><span class="font-bold">Saved :</span> +120kg CO2</p>
          </div>
        </div>
      </section>

      <!-- Menu items -->
      <section class="flex flex-col gap-2 px-4 mt-3">
        <router-link
            to="/profile/edit"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-3 py-2.5 shadow-[0_2px_8px_rgba(0,0,0,0.07)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-main-light text-main">
            <Icon icon="ph:gear" class="h-4 w-4" />
          </div>
          <span class="ml-3 flex-1 font-ui text-[13px] font-semibold text-black underline underline-offset-2 italic">
            {{ $t('profile.personal_info') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-4 w-4" />
        </router-link>

        <router-link
            to="/profile/location"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-3 py-2.5 shadow-[0_2px_8px_rgba(0,0,0,0.07)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:map-pin" class="h-4 w-4" />
          </div>
          <span class="ml-3 flex-1 font-ui text-[13px] font-semibold text-black underline underline-offset-2 italic">
            {{ $t('profile.location') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-4 w-4" />
        </router-link>

        <router-link
            to="/profile/security"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-3 py-2.5 shadow-[0_2px_8px_rgba(0,0,0,0.07)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:shield-check" class="h-4 w-4" />
          </div>
          <span class="ml-3 flex-1 font-ui text-[13px] font-semibold text-black underline underline-offset-2 italic">
            {{ $t('profile.security_privacy') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-4 w-4" />
        </router-link>

        <router-link
            to="/friends"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-3 py-2.5 shadow-[0_2px_8px_rgba(0,0,0,0.07)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:user-plus" class="h-4 w-4" />
          </div>
          <span class="ml-3 flex-1 font-ui text-[13px] font-semibold text-black underline underline-offset-2 italic">
            {{ $t('profile.friends') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-4 w-4" />
        </router-link>
      </section>

      <!-- Logout -->
      <section class="px-4 mt-4">
        <button
            @click="handleLogout"
            class="w-full rounded-xl border-2 border-systeme py-2.5 font-ui text-[13px] font-bold uppercase tracking-wider text-systeme transition-colors active:bg-systeme/10"
        >
          {{ $t('profile.logout') }}
        </button>
      </section>

    </main>

    <BottomNav active="profile" />
  </div>
</template>

<script setup>
import { Icon } from "@iconify/vue";
import BottomNav from "./BottomNav.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const handleLogout = () => {
  localStorage.removeItem("jwt_token");
  router.push("/login");
};
</script>
