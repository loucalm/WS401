<template>
  <RouterView />

  <transition name="session-pop">
    <div
      v-if="isSessionPromptOpen"
      class="fixed inset-0 z-[120] flex items-center justify-center bg-black/40 px-4"
    >
      <div
        class="w-full max-w-sm rounded-3xl border border-black/10 bg-white p-5 shadow-[0_20px_40px_rgba(0,0,0,0.2)]"
      >
        <h2 class="font-ui text-[22px] leading-tight text-black">
          {{ t("session.expiring_title") }}
        </h2>
        <p class="mt-2 text-body-14 text-grey">
          {{
            t("session.expiring_message", {
              minutes: expirationMinutes,
              seconds: expirationSeconds,
            })
          }}
        </p>

        <div class="mt-5 flex gap-3">
          <button
            type="button"
            class="flex-1 rounded-xl border border-grey/30 px-3 py-2 text-body-14 text-grey"
            :disabled="isRefreshingSession"
            @click="logoutNow"
          >
            {{ t("session.logout_now") }}
          </button>
          <button
            type="button"
            class="flex-1 rounded-xl bg-main px-3 py-2 text-body-14 font-semibold text-white"
            :disabled="isRefreshingSession"
            @click="keepConnected"
          >
            {{
              isRefreshingSession
                ? t("session.renewing")
                : t("session.stay_connected")
            }}
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import axios from "axios";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { RouterView, useRoute, useRouter } from "vue-router";

const API_BASE = "http://localhost:8000/api";
const WARNING_SECONDS = 120;
const CHECK_INTERVAL_MS = 1000;

const route = useRoute();
const router = useRouter();
const { t } = useI18n();

const isSessionPromptOpen = ref(false);
const isRefreshingSession = ref(false);
const warnedTokenExp = ref(0);
const tokenExpMs = ref(0);

let checkIntervalId = null;

const expirationSecondsLeft = computed(() => {
  if (!tokenExpMs.value) return 0;
  return Math.max(0, Math.floor((tokenExpMs.value - Date.now()) / 1000));
});

const expirationMinutes = computed(() =>
  Math.max(0, Math.floor(expirationSecondsLeft.value / 60)),
);

const expirationSeconds = computed(() =>
  Math.max(0, expirationSecondsLeft.value % 60),
);

const isPublicRoute = computed(() => {
  const routeName = route.name ? String(route.name) : "";
  return routeName === "login" || routeName === "register";
});

const normalizeToken = (rawToken) => {
  if (typeof rawToken !== "string") return "";
  return rawToken.trim().replace(/^"+|"+$/g, "");
};

const parseJwtPayload = (token) => {
  try {
    const payload = token.split(".")[1];
    if (!payload) return null;
    const normalized = payload.replace(/-/g, "+").replace(/_/g, "/");
    const padded = normalized + "=".repeat((4 - (normalized.length % 4)) % 4);
    return JSON.parse(atob(padded));
  } catch {
    return null;
  }
};

const closePrompt = () => {
  isSessionPromptOpen.value = false;
  isRefreshingSession.value = false;
};

const logoutNow = async () => {
  closePrompt();
  tokenExpMs.value = 0;
  warnedTokenExp.value = 0;
  localStorage.removeItem("jwt_token");

  if (!isPublicRoute.value) {
    await router.push({ name: "login" });
  }
};

const evaluateTokenSession = async () => {
  const stored = localStorage.getItem("jwt_token");
  const token = normalizeToken(stored);

  if (!token) {
    closePrompt();
    tokenExpMs.value = 0;
    warnedTokenExp.value = 0;
    return;
  }

  if (stored !== token) {
    localStorage.setItem("jwt_token", token);
  }

  const payload = parseJwtPayload(token);
  if (!payload?.exp) {
    await logoutNow();
    return;
  }

  tokenExpMs.value = payload.exp * 1000;
  const secondsLeft = Math.floor((tokenExpMs.value - Date.now()) / 1000);

  if (secondsLeft <= 0) {
    await logoutNow();
    return;
  }

  if (secondsLeft <= WARNING_SECONDS && warnedTokenExp.value !== payload.exp) {
    warnedTokenExp.value = payload.exp;
    isSessionPromptOpen.value = true;
  }
};

const keepConnected = async () => {
  if (isRefreshingSession.value) return;

  const token = normalizeToken(localStorage.getItem("jwt_token"));
  if (!token) {
    await logoutNow();
    return;
  }

  isRefreshingSession.value = true;

  try {
    const response = await axios.post(
      `${API_BASE}/token/refresh-authenticated`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      },
    );

    const refreshedToken = normalizeToken(response?.data?.token);
    if (!refreshedToken) {
      throw new Error("Missing refreshed token");
    }

    localStorage.setItem("jwt_token", refreshedToken);
    warnedTokenExp.value = 0;
    closePrompt();
    await evaluateTokenSession();
  } catch {
    await logoutNow();
  } finally {
    isRefreshingSession.value = false;
  }
};

watch(
  () => route.fullPath,
  () => {
    evaluateTokenSession();
  },
);

onMounted(() => {
  evaluateTokenSession();
  checkIntervalId = setInterval(() => {
    evaluateTokenSession();
  }, CHECK_INTERVAL_MS);
});

onBeforeUnmount(() => {
  if (checkIntervalId) {
    clearInterval(checkIntervalId);
    checkIntervalId = null;
  }
});
</script>

<style scoped>
.session-pop-enter-active,
.session-pop-leave-active {
  transition: all 0.2s ease;
}

.session-pop-enter-from,
.session-pop-leave-to {
  opacity: 0;
  transform: scale(0.98);
}
</style>
