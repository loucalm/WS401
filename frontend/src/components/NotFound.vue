<template>
  <div
    class="relative min-h-screen overflow-hidden bg-main-light px-3 py-3 sm:px-8 sm:py-8 lg:px-16 xl:px-24"
  >
    <img
      :src="backgroundUrl"
      alt=""
      class="pointer-events-none absolute inset-x-0 bottom-0 h-full w-full max-w-none object-cover opacity-15"
      aria-hidden="true"
    />

    <main
      class="relative mx-auto flex min-h-[calc(100vh-1.5rem)] w-full max-w-97.5 flex-col items-center justify-center sm:min-h-[calc(100vh-4rem)] sm:max-w-160 lg:max-w-2xl"
    >
      <div class="flex flex-col items-center text-center">
        <p
          class="font-title text-[2.2rem] uppercase leading-none text-main sm:text-title-h2"
        >
          404
        </p>
        <h1
          class="font-title mt-2 max-w-[14ch] text-[1.45rem] uppercase leading-[1.05] text-main text-balance sm:mt-4 sm:max-w-none sm:text-title-h3"
        >
          {{ t("not_found.title") }}
        </h1>
        <p
          class="font-ui mt-4 max-w-[20ch] text-[0.95rem] font-bold uppercase leading-[1.18] tracking-[0.01em] text-black text-balance sm:mt-6 sm:max-w-none sm:text-body-24"
        >
          {{ t("not_found.subtitle") }}
        </p>
      </div>

      <div
        class="mt-6 w-full rounded-4xl bg-white px-4 py-5 shadow-[0_12px_28px_rgba(0,0,0,0.14)] sm:mt-10 sm:px-6 sm:py-6"
      >
        <img
          src="../assets/img/404.png"
          alt="Page introuvable"
          class="mx-auto w-full max-w-[18rem] object-contain sm:max-w-88"
        />
      </div>

      <router-link
        to="/login"
        class="font-ui mt-6 inline-flex w-full items-center justify-center rounded-[1.35rem] bg-main px-5 py-3 text-[0.98rem] font-bold uppercase tracking-[0.04em] text-white transition hover:brightness-105 sm:mt-8 sm:w-auto sm:px-8 sm:py-4 sm:text-body-24"
      >
        {{ t("not_found.back_login") }}
      </router-link>
    </main>
  </div>
</template>

<script setup>
import backgroundUrl from "../assets/img/background.png";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
</script>
