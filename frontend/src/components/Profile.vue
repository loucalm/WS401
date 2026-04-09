<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-24"
    >
      <div class="flex items-start justify-between px-7 pt-7">
        <div
          class="rounded-xl border border-grey/20 bg-white px-4 py-2.5 shadow-[0_3px_8px_rgba(0,0,0,0.08)]"
        >
          <p
            class="font-ui text-[14px] font-bold text-main leading-none uppercase tracking-wide"
          >
            {{ t("profile.my_points") }}
          </p>
          <p class="mt-1 font-ui text-[15px] font-bold text-main leading-none">
            {{ t("profile.points_value", { count: profilePoints }) }}
          </p>
        </div>

        <div class="relative">
          <button
            type="button"
            class="relative flex h-10 w-10 items-center justify-center rounded-full bg-[#F4F4F4] text-grey shadow-sm transition-all active:scale-90"
            @click="isNotifOpen = !isNotifOpen"
            :aria-label="t('dashboard.notifications_title')"
            :title="t('dashboard.notifications_title')"
          >
            <Icon icon="ph:bell" class="h-5.5 w-5.5" />
            <span
              v-if="unreadNotifications > 0"
              class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-systeme px-1 text-[10px] font-bold text-white"
            >
              {{ unreadNotifications > 9 ? "9+" : unreadNotifications }}
            </span>
          </button>

          <transition name="notif-pop">
            <div
              v-if="isNotifOpen"
              class="absolute right-0 z-30 mt-2 w-72 rounded-2xl border border-grey/15 bg-white p-3 shadow-2xl"
            >
              <p
                class="px-1 text-body-12 font-bold uppercase tracking-wider text-grey"
              >
                {{ t("dashboard.notifications_title") }}
              </p>
              <div
                v-if="notifications.length === 0"
                class="px-1 py-3 text-body-12 text-grey"
              >
                {{ t("dashboard.notifications_empty") }}
              </div>
              <ul v-else class="mt-2 max-h-52 space-y-2 overflow-y-auto pr-1">
                <li
                  v-for="notif in notifications"
                  :key="notif.id"
                  class="rounded-xl border border-grey/10 bg-main-light/35 px-3 py-2"
                >
                  <p class="text-body-12 font-medium leading-snug text-black">
                    {{ notif.message }}
                  </p>
                  <p class="mt-1 text-[10px] uppercase tracking-wide text-grey">
                    {{ notif.timeLabel }}
                  </p>
                </li>
              </ul>
            </div>
          </transition>
        </div>
      </div>

      <div class="flex justify-center mt-6">
        <div
          class="h-36 w-36 rounded-full border-4 border-main/85 bg-white shadow-md overflow-hidden flex items-center justify-center"
        >
          <img
            :src="profilePictureSrc"
            alt="Profile Avatar"
            class="h-full w-full object-cover scale-125 grayscale"
          />
        </div>
      </div>

      <section class="px-7 mt-6">
        <div
          class="rounded-2xl border border-grey/15 bg-white px-7 py-6 text-center shadow-[0_4px_12px_rgba(0,0,0,0.06)]"
        >
          <h1
            class="font-title leading-tight text-black tracking-tight"
            style="font-size: 40px"
          >
            {{ profileDisplayName }}
          </h1>
          <p
            class="font-ui font-medium text-main mt-1.5"
            style="font-size: 16px"
          >
            {{ t("profile.level", { lvl: profileLevel }) }}
          </p>

          <div
            class="mt-5 flex items-center justify-around border-t border-grey/10 pt-5"
          >
            <p class="font-ui text-[15px] text-black">
              <span class="font-bold">{{ t("profile.friends_count") }}</span>
              {{ friendsCount }}
            </p>
            <div class="h-7 w-px bg-grey/20"></div>
            <p class="font-ui text-[15px] text-black">
              <span class="font-bold">{{ t("profile.saved_co2") }}</span>
              {{ savedCo2Label }}
            </p>
          </div>
        </div>
      </section>

      <section class="flex flex-col gap-4 px-7 mt-7">
        <router-link
          to="/profile/edit"
          class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div
            class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main"
          >
            <Icon icon="ph:gear" class="h-6 w-6" />
          </div>
          <span
            class="ml-4.5 flex-1 font-ui text-body-16 font-semibold text-black italic"
          >
            {{ t("profile.personal_info") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <router-link
          to="/profile/location"
          class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div
            class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main"
          >
            <Icon icon="ph:map-pin" class="h-6 w-6" />
          </div>
          <span
            class="ml-4.5 flex-1 font-ui text-body-16 font-semibold text-black italic"
          >
            {{ t("profile.location") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <router-link
          to="/profile/security"
          class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div
            class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main"
          >
            <Icon icon="ph:shield-check" class="h-6 w-6" />
          </div>
          <span
            class="ml-4.5 flex-1 font-ui text-body-16 font-semibold text-black italic"
          >
            {{ t("profile.security_privacy") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <router-link
          to="/friends"
          class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-transform active:scale-[0.98]"
        >
          <div
            class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main"
          >
            <Icon icon="ph:user-plus" class="h-6 w-6" />
          </div>
          <span
            class="ml-4.5 flex-1 font-ui text-body-16 font-semibold text-black italic"
          >
            {{ t("profile.friends") }}
          </span>
          <Icon icon="ph:caret-right" class="text-grey h-5 w-5" />
        </router-link>

        <div
          class="flex items-center rounded-xl border border-grey/15 bg-white px-4.5 py-4 shadow-[0_4px_10px_rgba(0,0,0,0.05)]"
        >
          <div
            class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#EFEFEF] text-main"
          >
            <Icon icon="ph:globe" class="h-6 w-6" />
          </div>
          <span
            class="ml-4.5 flex-1 text-left font-ui text-body-16 font-semibold text-black italic"
          >
            {{ t("profile.language") }}
          </span>
          <div
            class="flex items-center gap-1 rounded-xl border border-grey/20 bg-[#F4F4F4] p-1"
          >
            <button
              @click="locale !== 'fr' && toggleLocale()"
              class="rounded-lg px-3 py-1.5 font-ui text-[14px] font-bold uppercase tracking-wide transition-all duration-200"
              :class="
                locale === 'fr'
                  ? 'bg-main text-white shadow-sm'
                  : 'text-grey hover:text-black'
              "
            >
              FR
            </button>
            <button
              @click="locale !== 'en' && toggleLocale()"
              class="rounded-lg px-3 py-1.5 font-ui text-[14px] font-bold uppercase tracking-wide transition-all duration-200"
              :class="
                locale === 'en'
                  ? 'bg-main text-white shadow-sm'
                  : 'text-grey hover:text-black'
              "
            >
              EN
            </button>
          </div>
        </div>
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
import axios from "axios";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { resolveProfilePictureSrc } from "../utils/profilePictures.js";

const router = useRouter();
const { t, locale } = useI18n();
const API_BASE = "http://localhost:8000/api";

const profileDisplayName = ref("-");
const profilePictureSrc = ref(resolveProfilePictureSrc(null));
const profilePoints = ref(0);
const profileLevel = ref(1);
const friendsCount = ref(0);
const savedCo2Kg = ref(0);
const isNotifOpen = ref(false);
const notifications = ref([]);
const unreadNotifications = ref(0);
let refreshIntervalId = null;

const savedCo2Label = computed(() => `+${savedCo2Kg.value.toFixed(2)}kg CO2`);

watch(isNotifOpen, (open) => {
  if (open) unreadNotifications.value = 0;
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

const extractCollection = (responseData) => {
  if (responseData?.member) return responseData.member;
  if (responseData?.["hydra:member"]) return responseData["hydra:member"];
  return Array.isArray(responseData) ? responseData : [];
};

const fetchAll = async (resource, headers) => {
  const all = [];
  const seen = new Set();
  let page = 1;

  while (true) {
    const response = await axios.get(`${API_BASE}/${resource}?page=${page}`, {
      headers,
    });
    const chunk = extractCollection(response.data);
    if (chunk.length === 0) break;

    let added = 0;
    for (const item of chunk) {
      const iri = item?.["@id"];
      if (!iri || seen.has(iri)) continue;
      seen.add(iri);
      all.push(item);
      added += 1;
    }

    if (added === 0) break;
    page += 1;
  }

  return all;
};

const isSameDay = (isoDate) => {
  if (!isoDate) return false;
  const date = new Date(isoDate);
  const now = new Date();
  return (
    date.getFullYear() === now.getFullYear() &&
    date.getMonth() === now.getMonth() &&
    date.getDate() === now.getDate()
  );
};

const entryCo2 = (entry, entryItemsByIri, activityTypesByIri) => {
  const itemIris = Array.isArray(entry?.entryItems) ? entry.entryItems : [];
  if (itemIris.length === 0) return Number(entry?.totalCo2 || 0);

  let total = 0;
  for (const itemIri of itemIris) {
    const item = entryItemsByIri.get(itemIri);
    if (!item) continue;
    const qty = Number(item.quantity || 0);
    const type = activityTypesByIri.get(item.activityType);
    const factor = Number(type?.co2Factor || 0);
    total += qty * factor;
  }

  return total;
};

const loadProfile = async () => {
  try {
    const token = normalizeToken(localStorage.getItem("jwt_token"));
    if (!token) {
      router.push("/login");
      return;
    }

    const payload = parseJwtPayload(token);
    const currentEmail = payload?.email || payload?.username || "";
    if (!currentEmail) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: "application/ld+json",
    };

    const [users, entries, entryItems, activityTypes, friendships, summary] =
      await Promise.all([
        fetchAll("users", headers),
        fetchAll("entries", headers),
        fetchAll("entry_items", headers),
        fetchAll("activity_types", headers),
        fetchAll("friendships", headers),
        axios
          .get(`${API_BASE}/me/gamification-summary`, { headers })
          .then((response) => response.data)
          .catch(() => null),
      ]);

    const currentUser = users.find((user) => user.email === currentEmail);
    if (!currentUser) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    profilePictureSrc.value = resolveProfilePictureSrc(
      currentUser.profilePicture,
    );

    const currentUserIri = currentUser["@id"];
    profileDisplayName.value =
      currentUser.username || currentUser.email || t("nav.profile");

    const entryItemsByIri = new Map(
      entryItems.map((item) => [item["@id"], item]),
    );
    const activityTypesByIri = new Map(
      activityTypes.map((type) => [type["@id"], type]),
    );

    const userEntries = entries.filter(
      (entry) => entry.owner === currentUserIri,
    );
    const totalCo2Kg = userEntries.reduce(
      (sum, entry) =>
        sum + entryCo2(entry, entryItemsByIri, activityTypesByIri),
      0,
    );

    profilePoints.value = Number(
      summary?.dailyPoints || summary?.dailyScore || 0,
    );
    profileLevel.value = Number(summary?.level || 1);

    const targetRaw = Number(currentUser?.targetCo2 || 2000);
    const targetCo2Kg = targetRaw > 50 ? targetRaw / 1000 : targetRaw;
    savedCo2Kg.value = Math.max(0, targetCo2Kg - totalCo2Kg);

    friendsCount.value = friendships.filter((friendship) => {
      if (friendship?.status !== "accepted") return false;
      return (
        friendship?.sender === currentUserIri ||
        friendship?.receiver === currentUserIri
      );
    }).length;

    const usersByIri = new Map(users.map((user) => [user?.["@id"], user]));
    const pendingReceived = friendships
      .filter(
        (friendship) =>
          friendship?.status === "pending" &&
          friendship?.receiver === currentUserIri,
      )
      .map((friendship) => {
        const sender = usersByIri.get(friendship?.sender);
        const senderName =
          sender?.username || sender?.email || t("friends_page.unknown_user");

        return {
          id: friendship?.id || friendship?.["@id"] || Math.random(),
          message: t("friends_page.request_received_from", {
            name: senderName,
          }),
          timeLabel: friendship?.createdAt
            ? new Date(friendship.createdAt).toLocaleTimeString(
                locale.value === "fr" ? "fr-FR" : "en-US",
                { hour: "2-digit", minute: "2-digit" },
              )
            : "--:--",
        };
      });

    notifications.value = pendingReceived;
    if (!isNotifOpen.value) {
      unreadNotifications.value = pendingReceived.length;
    }
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    console.error("Profile loading error:", error);
  }
};

const toggleLocale = () => {
  locale.value = locale.value === "fr" ? "en" : "fr";
  localStorage.setItem("locale", locale.value);
};

const handleLogout = () => {
  localStorage.removeItem("jwt_token");
  router.push("/login");
};

onMounted(loadProfile);

onMounted(() => {
  refreshIntervalId = setInterval(() => {
    loadProfile();
  }, 5000);
});

onBeforeUnmount(() => {
  if (refreshIntervalId) clearInterval(refreshIntervalId);
});
</script>

<style scoped>
.notif-pop-enter-active,
.notif-pop-leave-active {
  transition: all 0.2s ease;
}

.notif-pop-enter-from,
.notif-pop-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.98);
}
</style>
