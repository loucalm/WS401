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

    <div
      class="relative mx-auto flex min-h-[calc(100vh-1.5rem)] w-full max-w-97.5 flex-col sm:min-h-[calc(100vh-4rem)] sm:max-w-160 lg:max-w-2xl"
    >
      <div class="flex justify-end mb-2">
        <div class="flex items-center gap-1 rounded-xl border border-grey/20 bg-white/80 p-1 shadow-sm">
          <button
            @click="locale !== 'fr' && toggleLocale()"
            class="rounded-lg px-3 py-1.5 font-ui text-[14px] font-bold uppercase tracking-wide transition-all duration-200"
            :class="locale === 'fr' ? 'bg-main text-white shadow-sm' : 'text-grey hover:text-black'"
          >FR</button>
          <button
            @click="locale !== 'en' && toggleLocale()"
            class="rounded-lg px-3 py-1.5 font-ui text-[14px] font-bold uppercase tracking-wide transition-all duration-200"
            :class="locale === 'en' ? 'bg-main text-white shadow-sm' : 'text-grey hover:text-black'"
          >EN</button>
        </div>
      </div>

      <div class="flex flex-col items-center text-center">
        <img :src="logoUrl" alt="Carbon logo" class="h-24 w-auto sm:h-36" />

        <h1
          class="font-title mt-4 max-w-[15ch] text-[1.45rem] uppercase leading-[1.05] text-main text-balance sm:mt-5 sm:max-w-none sm:text-title-h3"
        >
          {{ t("auth.register_title") }}
        </h1>

        <p
          class="font-ui mt-5 max-w-[18ch] text-[0.95rem] font-bold uppercase leading-[1.18] tracking-[0.01em] text-black text-balance sm:mt-8 sm:max-w-none sm:text-body-24"
        >
          {{ t("auth.register_subtitle") }}
        </p>
      </div>

      <form
        class="mt-4 space-y-3.5 sm:mt-8 sm:space-y-5"
        @submit.prevent="handleRegister"
      >
        <input
          v-model="username"
          type="text"
          :placeholder="t('auth.username')"
          required
          class="font-ui w-full rounded-3xl border border-main bg-white px-4 py-3 text-[0.95rem] text-black shadow-[0_8px_14px_rgba(17,125,111,0.16)] outline-none placeholder:text-grey focus:border-main focus:ring-2 focus:ring-main/20 sm:px-5 sm:py-4 sm:text-body-24"
        />
        <input
          v-model="email"
          type="email"
          :placeholder="t('auth.email')"
          required
          class="font-ui w-full rounded-3xl border border-main bg-white px-4 py-3 text-[0.95rem] text-black shadow-[0_8px_14px_rgba(17,125,111,0.16)] outline-none placeholder:text-grey focus:border-main focus:ring-2 focus:ring-main/20 sm:px-5 sm:py-4 sm:text-body-24"
        />
        <div class="relative">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            :placeholder="t('auth.password')"
            required
            class="font-ui w-full rounded-3xl border border-main bg-[#fff8c9] px-4 py-3 pr-12 text-[0.95rem] text-black shadow-[0_8px_14px_rgba(17,125,111,0.16)] outline-none placeholder:text-grey focus:border-main focus:ring-2 focus:ring-main/20 sm:px-5 sm:py-4 sm:pr-14 sm:text-body-24"
          />
          <button
            type="button"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-grey hover:text-black"
            :aria-label="showPassword ? t('auth.hide_password') : t('auth.show_password')"
            @click="showPassword = !showPassword"
          >
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>
        <div class="relative">
          <input
            v-model="confirmPassword"
            :type="showConfirmPassword ? 'text' : 'password'"
            :placeholder="t('auth.confirm_password')"
            required
            class="font-ui w-full rounded-3xl border border-main bg-white px-4 py-3 pr-12 text-[0.95rem] text-black shadow-[0_8px_14px_rgba(17,125,111,0.16)] outline-none placeholder:text-grey focus:border-main focus:ring-2 focus:ring-main/20 sm:px-5 sm:py-4 sm:pr-14 sm:text-body-24"
          />
          <button
            type="button"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-grey hover:text-black"
            :aria-label="showConfirmPassword ? t('auth.hide_password') : t('auth.show_password')"
            @click="showConfirmPassword = !showConfirmPassword"
          >
            <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>

        <button
          type="submit"
          class="font-ui w-full rounded-[1.35rem] bg-main px-5 py-3 text-[0.98rem] font-bold uppercase tracking-[0.04em] text-white transition hover:brightness-105 sm:px-6 sm:py-4 sm:text-body-24"
        >
          {{ t("auth.signup_button") }}
        </button>
      </form>

      <p
        v-if="errorMessage"
        class="font-ui text-body-12 mt-4 rounded-2xl border border-systeme/25 bg-systeme/10 px-4 py-3 text-systeme sm:text-body-16"
      >
        {{ errorMessage }}
      </p>
      <p
        v-if="successMessage"
        class="font-ui text-body-12 mt-4 rounded-2xl border border-main/20 bg-main/10 px-4 py-3 text-main sm:text-body-16"
      >
        {{ successMessage }}
      </p>

      <button
        type="button"
        @click="retourAulogin"
        class="font-ui mt-4 block w-full text-center text-[0.95rem] font-bold uppercase text-main underline underline-offset-4 sm:mt-5 sm:text-body-24"
      >
        {{ t("auth.go_login") }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import logoUrl from "../assets/logo.svg";
import backgroundUrl from "../assets/img/background.png";

const username = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const router = useRouter();
const { t, locale } = useI18n();

const toggleLocale = () => {
  locale.value = locale.value === 'fr' ? 'en' : 'fr';
  localStorage.setItem('locale', locale.value);
};

const retourAulogin = () => {
  localStorage.removeItem("jwt_token");
  router.push("/login");
};

const handleRegister = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  if (password.value !== confirmPassword.value) {
    errorMessage.value = t("auth.errors.password_mismatch");
    return;
  }

  try {
    await axios.post("http://localhost:8000/api/register", {
      username: username.value,
      email: email.value,
      password: password.value,
    });

    successMessage.value = t("auth.success.register");

    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (error) {
    if (error.response && error.response.status === 409) {
      errorMessage.value = t("auth.errors.email_used");
    } else {
      errorMessage.value = t("auth.errors.register");
    }
  }
};
</script>
