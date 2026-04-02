<template>
  <div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
    <div
      class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-6xl items-center justify-center"
    >
      <div
        class="grid w-full overflow-hidden rounded-[2rem] border border-white/10 bg-slate-950/70 shadow-2xl shadow-black/40 backdrop-blur-xl lg:grid-cols-[0.9fr_1.1fr]"
      >
        <section
          class="flex items-center justify-center bg-slate-900/80 p-8 sm:p-10 lg:p-14"
        >
          <div
            class="w-full max-w-md rounded-[1.75rem] border border-white/10 bg-slate-950/90 p-6 shadow-xl shadow-black/30 sm:p-8"
          >
            <div class="mb-8 flex items-center gap-4">
              <img
                :src="logoUrl"
                alt="Carbon Logo"
                class="h-14 w-14 rounded-2xl bg-white/90 p-2 shadow-lg shadow-emerald-500/20"
              />
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-300/80"
                >
                  Création
                </p>
                <h2 class="mt-2 text-2xl font-bold text-white">
                  Rejoindre l'application
                </h2>
              </div>
            </div>

            <div class="space-y-4">
              <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm font-semibold text-emerald-300">
                  Suivez votre impact
                </p>
                <p class="mt-1 text-sm leading-6 text-slate-400">
                  Gardez vos émissions sous contrôle avec des mesures claires et
                  visuelles.
                </p>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm font-semibold text-sky-300">
                  Relevez des défis
                </p>
                <p class="mt-1 text-sm leading-6 text-slate-400">
                  Comparez vos performances et progressez avec vos amis.
                </p>
              </div>
            </div>
          </div>
        </section>

        <section class="flex items-center justify-center p-8 sm:p-10 lg:p-14">
          <div
            class="w-full max-w-md rounded-[1.75rem] border border-white/10 bg-slate-950/90 p-6 shadow-xl shadow-black/30 sm:p-8"
          >
            <div class="mb-8">
              <p
                class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-300/80"
              >
                Inscription
              </p>
              <h1 class="mt-3 text-2xl font-bold text-white">
                Créer un nouveau compte
              </h1>
              <p class="mt-2 text-sm leading-6 text-slate-400">
                Remplissez les champs ci-dessous pour accéder à votre espace
                personnel.
              </p>
            </div>

            <form class="space-y-4" @submit.prevent="handleRegister">
              <input
                v-model="username"
                type="text"
                placeholder="Pseudo"
                required
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500 outline-none transition focus:border-sky-400 focus:bg-white/10"
              />
              <input
                v-model="email"
                type="email"
                placeholder="Email"
                required
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500 outline-none transition focus:border-sky-400 focus:bg-white/10"
              />
              <input
                v-model="password"
                type="password"
                placeholder="Mot de passe"
                required
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500 outline-none transition focus:border-sky-400 focus:bg-white/10"
              />
              <input
                v-model="confirmPassword"
                type="password"
                placeholder="Confirmer le mot de passe"
                required
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500 outline-none transition focus:border-sky-400 focus:bg-white/10"
              />

              <button
                type="submit"
                class="flex w-full items-center justify-center rounded-2xl bg-sky-400 px-4 py-3 font-semibold text-slate-950 transition hover:bg-sky-300"
              >
                S'inscrire
              </button>
            </form>

            <p
              v-if="errorMessage"
              class="mt-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-200"
            >
              {{ errorMessage }}
            </p>
            <p
              v-if="successMessage"
              class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100"
            >
              {{ successMessage }}
            </p>

            <button
              type="button"
              @click="retourAulogin"
              class="mt-8 w-full rounded-2xl border border-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:border-white/20 hover:bg-white/5"
            >
              Déjà un compte ? Se connecter
            </button>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import logoUrl from "../assets/logo.svg";

const username = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const errorMessage = ref("");
const successMessage = ref("");
const router = useRouter();

const retourAulogin = () => {
  localStorage.removeItem("jwt_token");
  router.push("/login");
};

const handleRegister = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  if (password.value !== confirmPassword.value) {
    errorMessage.value = "Les mots de passe ne correspondent pas.";
    return;
  }

  try {
    await axios.post("http://localhost:8000/api/register", {
      username: username.value,
      email: email.value,
      password: password.value,
    });

    successMessage.value = "Compte créé avec succès ! Redirection...";

    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (error) {
    if (error.response && error.response.status === 409) {
      errorMessage.value = "Cet email est déjà utilisé.";
    } else {
      errorMessage.value = "Erreur lors de la création du compte.";
    }
  }
};
</script>
