<template>
  <div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
    <div
      class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-4xl items-center justify-center"
    >
      <div
        class="grid w-full gap-8 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-950/70 p-8 shadow-2xl shadow-black/40 backdrop-blur-xl lg:grid-cols-[0.95fr_1.05fr] lg:p-10"
      >
        <div
          class="flex items-center justify-center rounded-[1.75rem] border border-white/10 bg-white/5 p-6"
        >
          <img
            src="../assets/img/404.png"
            alt="Page introuvable"
            class="max-h-80 w-full max-w-sm object-contain"
          />
        </div>

        <div class="flex flex-col justify-center">
          <p
            class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-300/80"
          >
            Erreur 404
          </p>
          <h1 class="mt-3 text-4xl font-bold text-white sm:text-5xl">
            Oups ! Page introuvable
          </h1>
          <p class="mt-4 max-w-xl text-base leading-7 text-slate-400">
            Il semble que vous ayez pris un chemin qui n'existe pas encore sur
            notre carte écologique.
          </p>

          <router-link
            to="/dashboard"
            class="mt-8 inline-flex w-fit items-center justify-center rounded-2xl bg-emerald-400 px-5 py-3 font-semibold text-slate-950 transition hover:bg-emerald-300"
          >
            Retour au Dashboard
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup></script>
