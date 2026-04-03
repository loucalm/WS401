<template>
  <div
    class="relative min-h-screen overflow-hidden bg-main-light px-3 py-3 sm:px-8 sm:py-10 lg:px-16 xl:px-24"
  >
    <img
      :src="backgroundUrl"
      alt=""
      class="pointer-events-none absolute inset-x-0 bottom-0 h-full w-full max-w-none object-cover opacity-15"
      aria-hidden="true"
    />

    <div
      class="relative mx-auto flex min-h-[calc(100vh-1.5rem)] w-full max-w-97.5 flex-col sm:min-h-[calc(100vh-5rem)] sm:max-w-160 lg:max-w-2xl"
    >
      <div class="flex flex-col items-center text-center">
        <img :src="logoUrl" alt="Carbon logo" class="h-24 w-auto sm:h-36" />

        <h1
          class="font-title mt-4 max-w-[15ch] text-[1.45rem] uppercase leading-[1.05] text-main text-balance sm:mt-5 sm:max-w-none sm:text-title-h3"
        >
          CONNECT TO YOUR ZERO EMISSIONS JOURNEY
        </h1>

        <p
          class="font-ui mt-5 max-w-[18ch] text-[0.95rem] font-bold uppercase leading-[1.18] tracking-[0.01em] text-black text-balance sm:mt-8 sm:max-w-none sm:text-body-24"
        >
          LOG INTO YOUR ACCOUNT
        </p>
      </div>

      <form
        class="mt-4 space-y-3.5 sm:mt-8 sm:space-y-6"
        @submit.prevent="handleLogin"
      >
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          required
          class="font-ui w-full rounded-3xl border border-main bg-white px-4 py-3 text-[0.95rem] text-black shadow-[0_8px_14px_rgba(17,125,111,0.16)] outline-none placeholder:text-grey focus:border-main focus:ring-2 focus:ring-main/20 sm:px-5 sm:py-4 sm:text-body-24"
        />
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          required
          class="font-ui w-full rounded-3xl border border-main bg-[#fff8c9] px-4 py-3 text-[0.95rem] text-black shadow-[0_8px_14px_rgba(17,125,111,0.16)] outline-none placeholder:text-grey focus:border-main focus:ring-2 focus:ring-main/20 sm:px-5 sm:py-4 sm:text-body-24"
        />

        <button
          type="submit"
          class="font-ui w-full rounded-[1.35rem] bg-main px-5 py-3 text-[0.98rem] font-bold uppercase tracking-[0.04em] text-white transition hover:brightness-105 sm:px-6 sm:py-4 sm:text-body-24"
        >
          SIGN IN
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
        @click="goToRegister"
        class="font-ui mt-4 block w-full text-center text-[0.95rem] font-bold uppercase text-main underline underline-offset-4 sm:mt-5 sm:text-body-24"
      >
        LOG IN
      </button>

      <div class="mt-7 space-y-4 sm:mt-14 sm:space-y-7">
        <article
          class="rounded-[1.7rem] bg-white p-3.5 shadow-[0_10px_24px_rgba(0,0,0,0.16)] sm:p-5"
        >
          <div class="flex items-center gap-2.5 px-1 sm:gap-4 sm:px-2">
            <img
              :src="fabienUrl"
              alt="Fabien"
              class="h-14 w-14 rounded-full object-cover sm:h-20 sm:w-20"
            />
            <div>
              <p
                class="font-ui text-[1.05rem] font-bold text-black sm:text-body-32"
              >
                Fabien
              </p>
              <p class="font-ui text-body-12 text-grey sm:text-body-24">
                pre-existing
              </p>
            </div>
          </div>

          <button
            type="button"
            class="font-ui mt-3.5 w-full rounded-[1.05rem] bg-main px-4 py-2.5 text-[0.9rem] font-bold uppercase tracking-[0.03em] text-white transition hover:brightness-105 sm:mt-5 sm:py-4 sm:text-body-24"
          >
            SIGN IN AS FABIEN
          </button>
        </article>

        <article
          class="rounded-[1.7rem] bg-white p-3.5 shadow-[0_10px_24px_rgba(0,0,0,0.16)] sm:p-5"
        >
          <div class="flex items-center gap-2.5 px-1 sm:gap-4 sm:px-2">
            <img
              :src="briceUrl"
              alt="Brice"
              class="h-14 w-14 rounded-full object-cover sm:h-20 sm:w-20"
            />
            <div>
              <p
                class="font-ui text-[1.05rem] font-bold text-black sm:text-body-32"
              >
                Brice
              </p>
              <p class="font-ui text-body-12 text-grey sm:text-body-24">
                pre-existing
              </p>
            </div>
          </div>

          <button
            type="button"
            class="font-ui mt-3.5 w-full rounded-[1.05rem] bg-main px-4 py-2.5 text-[0.9rem] font-bold uppercase tracking-[0.03em] text-white transition hover:brightness-105 sm:mt-5 sm:py-4 sm:text-body-24"
          >
            SIGN IN AS BRICE
          </button>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import logoUrl from "../assets/logo.svg";
import backgroundUrl from "../assets/img/background.png";
import fabienUrl from "../assets/img/fabien.png";
import briceUrl from "../assets/img/brice.png";

const email = ref("");
const password = ref("");
const errorMessage = ref("");
const successMessage = ref("");
const router = useRouter();

const goToRegister = () => {
  router.push("/register");
};

const handleLogin = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  try {
    const response = await axios.post("http://localhost:8000/api/login_check", {
      email: email.value,
      password: password.value,
    });

    const token = response.data.token;

    localStorage.setItem("jwt_token", token);
    successMessage.value = "Login successful!";

    router.push("/dashboard");
  } catch (error) {
    if (error.response && error.response.status === 401) {
      errorMessage.value = "Invalid credentials.";
    } else {
      errorMessage.value = "Server connection error.";
    }
  }
};
</script>
