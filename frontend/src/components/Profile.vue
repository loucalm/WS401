<template>
  <div class="min-h-screen bg-white text-black p-4 flex flex-col items-center">
    
    <main class="w-full max-w-lg bg-white rounded-3xl p-6 shadow-xl relative mt-12 pb-28">
      
      <div class="absolute -top-8 left-6">
        <p class="font-ui text-[14px] text-white/60">Compte</p>
      </div>

      <div class="flex items-start justify-between mb-8">
        
        <div class="rounded-3xl border border-grey/20 bg-white px-6 py-4 shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
          <p class="font-title text-[28px] leading-none text-main uppercase">
            {{ $t('profile.my_points') }}
          </p>
          <p class="mt-2 font-ui text-[20px] font-bold text-main">
            + 560 points
          </p>
        </div>

        <div class="flex gap-4 pt-2">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#E6F3EF] text-main">
            <Icon icon="ph:share-network" class="h-7 w-7" />
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#F4F4F4] text-grey">
            <Icon icon="ph:bell" class="h-7 w-7" />
          </div>
        </div>
      </div>

      <div class="relative flex flex-col items-center mb-10">
        <div class="relative z-10 h-64 w-64 rounded-full border-8 border-main/85 bg-white shadow-lg overflow-hidden flex items-center justify-center">
          <img 
            src="https://i.pravatar.cc/256?u=rafamaru" 
            alt="Profile Avatar" 
            class="h-full w-full object-cover"
          />
        </div>
      </div>

      <section class="mt-6 mb-12">
        <div class="rounded-[40px] border border-grey/15 bg-white px-8 py-10 text-center shadow-[0_12px_40px_rgba(0,0,0,0.16)]">
          <h1 class="font-title text-[40px] leading-none text-black uppercase tracking-tight">
            Rafamaru Teixeira
          </h1>
          <p class="font-ui text-[22px] font-medium text-main mt-4">
            Climate Protector - Lvl 12
          </p>
          
          <div class="mt-8 flex items-center justify-around border-t border-grey/10 pt-8">
            <div class="text-center">
              <p class="font-ui text-[20px] text-black">
                <span class="font-bold">Friends :</span> 25
              </p>
            </div>
            <div class="h-10 w-px bg-grey/20"></div>
            <div class="text-center">
              <p class="font-ui text-[20px] text-black">
                <span class="font-bold">Saved :</span> +120kg CO2
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="flex flex-col gap-4">
        <router-link to="/profile/edit" class="flex items-center rounded-3xl border border-grey/15 bg-white p-5 shadow-sm transition-transform active:scale-[0.98]">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#E6F3EF] text-main">
            <Icon icon="ph:gear" class="h-7 w-7" />
          </div>
          <span class="ml-5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-4 italic">
            {{ $t('profile.personal_info') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-6 w-6" />
        </router-link>

        <router-link to="/profile/location" class="flex items-center rounded-3xl border border-grey/15 bg-white p-5 shadow-sm transition-transform active:scale-[0.98]">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EFEFEF] text-main">
            <Icon icon="ph:map-pin" class="h-7 w-7" />
          </div>
          <span class="ml-5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-4 italic">
            {{ $t('profile.location') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-6 w-6" />
        </router-link>

        <router-link to="/profile/security" class="flex items-center rounded-3xl border border-grey/15 bg-white p-5 shadow-sm transition-transform active:scale-[0.98]">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EFEFEF] text-main">
            <Icon icon="ph:shield-check" class="h-7 w-7" />
          </div>
          <span class="ml-5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-4 italic">
            {{ $t('profile.security_privacy') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-6 w-6" />
        </router-link>

        <router-link to="/friends" class="flex items-center rounded-3xl border border-grey/15 bg-white p-5 shadow-sm transition-transform active:scale-[0.98]">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EFEFEF] text-main">
            <Icon icon="ph:users-three" class="h-7 w-7" />
          </div>
          <span class="ml-5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-4 italic">
            {{ $t('profile.friends') }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-6 w-6" />
        </router-link>
      </section>

      <section class="mt-12">
        <button 
          @click="handleLogout"
          class="w-full rounded-3xl border-2 border-systeme py-5 font-ui text-[18px] font-bold uppercase tracking-wider text-systeme transition-colors active:bg-systeme/10"
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
// Importation de l'image locale avec un chemin relatif
import personaSrc from "../assets/img/persona.jpg"; 

const router = useRouter();

const handleLogout = () => {
  localStorage.removeItem("jwt_token");
  router.push("/login");
};
</script>