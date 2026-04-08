<template>
  <div class="min-h-screen bg-white text-black">
    <main
        class="mx-auto flex min-h-screen w-full max-w-125 flex-col bg-white pb-24"
    >
      <div class="flex items-start justify-between px-7 pt-7">
        <div class="rounded-xl border border-grey/20 bg-white px-4 py-2.5 shadow-[0_3px_8px_rgba(0,0,0,0.08)]">
          <p class="font-ui text-[14px] font-bold text-main leading-none uppercase tracking-wide">{{ t("profile.my_points") }}</p>
          <p class="mt-1 font-ui text-[15px] font-bold text-main leading-none">{{ t("profile.points_value", { count: 560 }) }}</p>
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

      <div class="flex justify-center mt-6">
        <div class="h-36 w-36 rounded-full border-4 border-main/85 bg-white shadow-md overflow-hidden flex items-center justify-center">
          <img
              :src="personaSrc"
              alt="Profile Avatar"
              class="h-full w-full object-cover scale-125 grayscale"
          />
        </div>
      </div>

      <section class="px-7 mt-6">
        <div class="rounded-2xl border border-grey/15 bg-white px-7 py-6 text-center shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
          <h1 class="font-title text-[40px] leading-tight text-black tracking-tight">Rafamaru Teixeira</h1>
          <p class="font-ui text-[16px] font-medium text-main mt-1.5">{{ t("profile.level", { lvl: 12 }) }}</p>

          <div class="mt-5 flex items-center justify-around border-t border-grey/10 pt-5">
            <p class="font-ui text-[15px] text-black">
              <span class="font-bold">{{ t("profile.friends_count") }}</span> 25
            </p>
            <div class="h-7 w-px bg-grey/20"></div>
            <p class="font-ui text-[15px] text-black">
              <span class="font-bold">{{ t("profile.saved_co2") }}</span> +120kg CO2
            </p>
          </div>
        </div>
      </section>

      <section class="flex flex-col gap-4 px-7 mt-7">
        <router-link
            to="/profile/edit"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-main-light text-main">
            <Icon icon="ph:gear" class="h-6 w-6" />
          </div>
          <span class="ml-4.5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-2 italic">
            {{ t("profile.personal_info") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <router-link
            to="/profile/location"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:map-pin" class="h-6 w-6" />
          </div>
          <span class="ml-4.5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-2 italic">
            {{ t("profile.location") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <router-link
            to="/profile/security"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:shield-check" class="h-6 w-6" />
          </div>
          <span class="ml-4.5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-2 italic">
            {{ t("profile.security_privacy") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <router-link
            to="/friends"
            class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:user-plus" class="h-6 w-6" />
          </div>
          <span class="ml-4.5 flex-1 font-ui text-[18px] font-semibold text-black underline underline-offset-2 italic">
            {{ t("profile.friends") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <button
            @click="toggleLocale"
            class="flex w-full items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main">
            <Icon icon="ph:globe" class="h-6 w-6" />
          </div>
          <span class="ml-4.5 flex-1 text-left font-ui text-[18px] font-semibold text-black underline underline-offset-2 italic">
            {{ t("profile.language") }}
          </span>
          <span class="font-ui text-[15px] font-bold text-main uppercase tracking-wide">
            {{ locale === 'fr' ? 'EN' : 'FR' }}
          </span>
        </button>
      </section>

      <section class="px-7 mt-8 mb-4">
        <button
            @click="handleLogout"
            class="w-full rounded-2xl border-2 border-systeme py-4 font-ui text-[18px] font-bold uppercase tracking-wider text-systeme transition-colors active:bg-systeme/10"
        >
          {{ t("profile.logout") }}
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
import { useI18n } from "vue-i18n";
import personaSrc from "../assets/img/persona.jpg";

const router = useRouter();
const { t, locale } = useI18n();

const toggleLocale = () => {
  locale.value = locale.value === 'fr' ? 'en' : 'fr';
  localStorage.setItem('locale', locale.value);
};

const handleLogout = () => {
  localStorage.removeItem("jwt_token");
  router.push("/login");
};
</script>