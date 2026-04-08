<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28 shadow-[0_0_0_1px_rgba(0,0,0,0.04)] lg:my-0 lg:rounded-none lg:px-10 relative"
    >
      <section
        class="relative overflow-hidden rounded-b-[36px] bg-main-light px-4 pb-6 pt-4"
      >
        <div
          class="absolute inset-x-0 bottom-6 h-36 bg-[radial-gradient(circle_at_20%_40%,rgba(17,125,111,0.18),transparent_32%),radial-gradient(circle_at_80%_30%,rgba(17,125,111,0.12),transparent_28%)] opacity-80"
        ></div>

        <div class="relative flex flex-col items-center">
          <div class="relative h-72 w-72 transition-all duration-300">
            <div
              class="absolute inset-0 rounded-full"
              :style="gaugeRingStyle"
            ></div>

            <div
              class="absolute inset-[10px] rounded-full bg-white shadow-[inset_0_8px_18px_rgba(255,255,255,0.45)]"
            >
              <div
                class="absolute inset-[8px] rounded-full border border-dashed border-[#a9a9a9]"
              ></div>
            </div>

            <div
              class="absolute z-20 box-border h-7 w-7 rounded-full border-4 shadow-[0_2px_8px_rgba(0,0,0,0.22)]"
              :style="gaugeMarkerStyle"
            ></div>

            <div
              class="absolute inset-[26px] z-10 flex flex-col items-center justify-center text-center"
            >
              <img
                :src="mascotSrc"
                alt="Mascot"
                class="mb-2 h-24 w-24 object-contain"
              />
              <p class="font-title text-[35px] leading-none text-black sm:text-[39px]">
                {{ formattedAnimatedDailyCo2 }} kg
              </p>
              <p class="mt-2 text-[18px] text-grey sm:text-[21px]">{{ t("dashboard.co2_daily") }}</p>
              <p class="mt-1 text-[11px] sm:text-[12px]" :class="gaugeProgressTextClass">
                {{ t("dashboard.target_progress", { percent: Math.round(progressPercent) }) }}
              </p>
            </div>
          </div>

          <div
            class="relative z-10 mt-4 w-full rounded-2xl border border-grey/20 bg-white px-4 py-2 text-center shadow-[0_8px_18px_rgba(0,0,0,0.14)]"
          >
            <p class="font-title text-[27px] leading-none text-main sm:text-[30px]">
              {{ t("dashboard.points_title") }}
            </p>
            <p class="mt-1 font-ui text-[17px] font-bold text-main sm:text-[19px]">
              {{ t("dashboard.points_value", { count: 560 }) }}
            </p>
          </div>

          <div
            class="mt-3 w-full overflow-hidden rounded-2xl border border-grey/20 bg-white shadow-[0_8px_18px_rgba(0,0,0,0.14)]"
          >
            <button
              type="button"
              class="flex w-full items-center justify-between px-4 py-3 text-[13px] font-medium text-black sm:text-[14px]"
              @click="isLeaderboardOpen = !isLeaderboardOpen"
            >
              <span>{{ t("dashboard.leaderboard_title") }}</span>
              <span
                class="text-[17px] leading-none transition-transform duration-200 sm:text-[19px]"
                :class="isLeaderboardOpen ? 'rotate-180' : ''"
                >⌄</span
              >
            </button>

            <transition name="leaderboard-collapse">
              <div
                v-show="isLeaderboardOpen"
                class="leaderboard-panel border-t border-grey/15 px-3 pb-3 pt-2"
              >
                <p
                  v-if="leaderboard.length === 0"
                  class="py-2 text-center text-[12px] text-grey sm:text-[13px]"
                >
                  {{ t("dashboard.leaderboard_empty") }}
                </p>

                <ul v-else class="space-y-2">
                  <li
                    v-for="(friend, index) in leaderboard"
                    :key="friend.id"
                    class="flex items-center justify-between rounded-xl px-3 py-2"
                    :class="
                      friend.isCurrentUser
                        ? 'bg-main-light ring-1 ring-main/25'
                        : 'bg-main-light/65'
                    "
                  >
                    <div class="flex min-w-0 items-center gap-2">
                      <span
                        class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold text-white sm:text-[12px]"
                        :class="rankBadgeClass(index)"
                      >
                        {{ index + 1 }}
                      </span>
                      <div class="min-w-0">
                        <p class="truncate text-[13px] font-medium text-black sm:text-[14px]">
                          {{ friend.name }}
                          <span
                            v-if="friend.isCurrentUser"
                            class="ml-1 text-[11px] font-semibold text-main sm:text-[12px]"
                            >{{ t("dashboard.you") }}</span
                          >
                        </p>
                        <p class="text-[11px] text-grey sm:text-[12px]">
                          {{ t("dashboard.activities_today", { count: friend.activityCount }) }}
                        </p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-[12px] font-semibold text-main sm:text-[13px]">
                        {{ friend.points }} pts
                      </p>
                      <p class="text-[11px] text-grey sm:text-[12px]">{{ friend.co2 }}</p>
                    </div>
                  </li>
                </ul>
              </div>
            </transition>
          </div>
        </div>
      </section>

      <section class="px-4 pt-5">
        <h2 class="font-ui text-[17px] font-semibold text-black sm:text-[19px]">
          {{ t("dashboard.latest_activities") }}
        </h2>

        <div
          v-if="loading"
          class="mt-4 rounded-2xl border border-grey/15 bg-white px-4 py-4 text-center text-[14px] text-grey shadow-[0_6px_16px_rgba(0,0,0,0.14)] sm:text-[15px]"
        >
          {{ t("dashboard.loading_activities") }}
        </div>

        <div
          v-else-if="activities.length === 0"
          class="mt-4 rounded-2xl border border-grey/15 bg-white px-4 py-4 text-center text-[14px] text-grey shadow-[0_6px_16px_rgba(0,0,0,0.14)] sm:text-[15px]"
        >
          {{ t("dashboard.no_activity") }}
        </div>

        <div v-else class="mt-4 space-y-4">
          <article
            v-for="activity in activities"
            :key="activity.id"
            class="flex items-center gap-4 rounded-2xl border border-grey/15 bg-white px-3 py-3 shadow-[0_6px_16px_rgba(0,0,0,0.14)]"
          >
            <div
              class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-main-light text-main"
            >
              <Icon :icon="activity.icon" class="h-10 w-10" />
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-ui text-body-16 font-medium text-black">
                    {{ activity.title }}
                  </p>
                  <p
                    class="mt-1 text-[14px] font-medium sm:text-[15px]"
                    :class="activity.pointsClass"
                  >
                    {{ activity.points }}
                  </p>
                </div>
                <p class="shrink-0 font-ui text-[17px] font-medium text-black sm:text-[19px]">
                  {{ activity.quantityLabel }}
                </p>
              </div>

              <p
                class="mt-1 text-right text-[14px] font-medium sm:text-[15px]"
                :class="activity.co2Class"
              >
                {{ activity.co2 }}
              </p>
            </div>
          </article>
        </div>
      </section>

      <Transition name="pop">
        <div v-if="showOvertookPopup" class="fixed inset-0 z-[100] flex items-center justify-center px-6">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showOvertookPopup = false"></div>
          <div class="relative w-full max-w-sm overflow-hidden rounded-[32px] bg-white p-8 text-center shadow-2xl animate-zoom">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-systeme/10 text-systeme">
              <Icon icon="ph:trend-down-bold" class="h-10 w-10" />
            </div>
            <h3 class="font-title text-[24px] leading-tight text-black">
              {{ t("dashboard.overtook_title", "Watch out!") }}
            </h3>
            <p class="mt-4 text-body-16 text-grey">
              {{ t("dashboard.overtook_message", "Your friend just overtook you in the general ranking!") }}
            </p>
            <button type="button" class="mt-8 w-full rounded-2xl bg-main py-4 font-ui text-[18px] font-bold text-white transition-transform active:scale-95 shadow-lg shadow-main/20" @click="showOvertookPopup = false">
              {{ t("dashboard.overtook_button", "I'm coming for them!") }}
            </button>
          </div>
        </div>
      </Transition>
    </main>

    <BottomNav active="home" />
  </div>
</template>

<script setup>
import axios from "axios";
import { Icon } from "@iconify/vue";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import mascotSrc from "../assets/mascotte_neutre.svg";
import BottomNav from "./BottomNav.vue";

const API_BASE = "http://localhost:8000/api";
const router = useRouter();
const { t, te, locale } = useI18n();

const loading = ref(true);
const dailyCo2 = ref(0);
const animatedDailyCo2 = ref(0);
const dailyTargetKg = ref(200);
const activities = ref([]);
const leaderboard = ref([]);
const isLeaderboardOpen = ref(false);
const showOvertookPopup = ref(false); 

let animationFrameId = null;

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

const normalizeToken = (rawToken) => {
  if (typeof rawToken !== "string") return "";
  return rawToken.trim().replace(/^"+|"+$/g, "");
};

const normalizeKey = (value = "") =>
  value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");

const translateActivityLabel = (label = "") => {
  const key = `taxonomy.activities.${normalizeKey(label).replace(/-/g, "_")}`;
  return te(key) ? t(key) : label;
};

const translateDietLabel = (label = "") => {
  const key = `taxonomy.diets.${normalizeKey(label).replace(/-/g, "_")}`;
  return te(key) ? t(key) : label;
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

const formatKg = (value) => {
  const numberLocale = locale.value === "fr" ? "fr-FR" : "en-US";
  return new Intl.NumberFormat(numberLocale, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
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

const iconForActivity = (activityType) => {
  if (activityType?.icon) return activityType.icon;

  const subCategory = (activityType?.subCategory || "").toLowerCase();
  if (subCategory.includes("transport")) return "mdi:car";
  if (subCategory.includes("energy")) return "mdi:flash";
  if (subCategory.includes("top") || subCategory.includes("shoe")) {
    return "tabler:shirt-filled";
  }

  return "mdi:leaf";
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

const toDashboardActivity = (entry, entryItemsByIri, activityTypesByIri) => {
  const itemIris = Array.isArray(entry?.entryItems) ? entry.entryItems : [];
  const item = itemIris.length > 0 ? entryItemsByIri.get(itemIris[0]) : null;
  const activityType = item ? activityTypesByIri.get(item.activityType) : null;
  const co2 = entryCo2(entry, entryItemsByIri, activityTypesByIri);
  const qty = Number(item?.quantity || 0);
  const unit = activityType?.unitLabel || "unit";
  const points = Math.max(0, Math.round((2 - co2) * 60));

  return {
    id: entry.id,
    title:
      (activityType?.name ? translateActivityLabel(activityType.name) : "") ||
      (entry?.details?.transportMode
        ? translateActivityLabel(entry.details.transportMode)
        : "") ||
      (entry?.details?.source ? translateActivityLabel(entry.details.source) : "") ||
      (entry?.details?.diet ? translateDietLabel(entry.details.diet) : "") ||
      t("dashboard.activity_fallback", { id: entry.id }),
    icon: iconForActivity(activityType),
    points: `+${points} n2e points`,
    pointsClass: points > 0 ? "text-main" : "text-grey",
    quantityLabel: qty > 0 ? `${formatKg(qty)} ${unit}` : "-",
    co2: `${co2 > 0 ? "+" : ""}${formatKg(co2)} kg CO2`,
    co2Class: co2 > 2 ? "text-systeme" : "text-main",
  };
};

const animateDailyCo2 = (nextValue) => {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
    animationFrameId = null;
  }

  const startValue = animatedDailyCo2.value;
  const duration = 850;
  const startTime = performance.now();

  const frame = (time) => {
    const elapsed = time - startTime;
    const t = Math.min(1, elapsed / duration);
    const eased = 1 - Math.pow(1 - t, 3);
    animatedDailyCo2.value = startValue + (nextValue - startValue) * eased;

    if (t < 1) {
      animationFrameId = requestAnimationFrame(frame);
    } else {
      animatedDailyCo2.value = nextValue;
      animationFrameId = null;
    }
  };

  animationFrameId = requestAnimationFrame(frame);
};

const progressPercent = computed(() => {
  if (dailyTargetKg.value <= 0) return 0;
  return Math.min((animatedDailyCo2.value / dailyTargetKg.value) * 100, 100);
});

const GAUGE_ARC_START_DEG = 210;
const GAUGE_ARC_SWEEP_DEG = 300;

const gaugePalette = computed(() => {
  const p = Math.max(0, Math.min(progressPercent.value, 100));

  if (p >= 90) {
    return {
      solid: "rgba(212,10,23,0.98)",
      track: "rgba(212,10,23,0.28)",
      markerBorder: "#d40a17",
      markerFill: "#fdeced",
      progressClass: "text-systeme",
    };
  }

  if (p >= 65) {
    return {
      solid: "rgba(210,139,20,0.98)",
      track: "rgba(210,139,20,0.30)",
      markerBorder: "#d28b14",
      markerFill: "#fff4df",
      progressClass: "text-[#d28b14]",
    };
  }

  return {
    solid: "rgba(17,125,111,0.95)",
    track: "rgba(74,184,173,0.55)",
    markerBorder: "#117d6f",
    markerFill: "#eaf6f6",
    progressClass: "text-main",
  };
});

const gaugeProgressTextClass = computed(() => gaugePalette.value.progressClass);

const gaugeRingStyle = computed(() => {
  const p = Math.max(0, Math.min(progressPercent.value, 100));
  const palette = gaugePalette.value;
  const activeDeg = (p / 100) * GAUGE_ARC_SWEEP_DEG;
  return {
    background: `conic-gradient(from ${GAUGE_ARC_START_DEG}deg, ${palette.solid} 0deg, ${palette.solid} ${activeDeg}deg, ${palette.track} ${activeDeg}deg, ${palette.track} ${GAUGE_ARC_SWEEP_DEG}deg, transparent ${GAUGE_ARC_SWEEP_DEG}deg, transparent 360deg)`,
  };
});

const gaugeMarkerStyle = computed(() => {
  const p = Math.max(0, Math.min(progressPercent.value, 100));
  const markerAngleDeg = GAUGE_ARC_START_DEG + (p / 100) * GAUGE_ARC_SWEEP_DEG;
  const angleInRadians = (markerAngleDeg - 90) * (Math.PI / 180);
  const center = 144;
  const radius = 139;
  const markerHalfSize = 14;
  const x = center + radius * Math.cos(angleInRadians) - markerHalfSize;
  const y = center + radius * Math.sin(angleInRadians) - markerHalfSize;
  const palette = gaugePalette.value;

  return {
    left: `${x}px`,
    top: `${y}px`,
    borderColor: palette.markerBorder,
    backgroundColor: palette.markerFill,
  };
});

const formattedAnimatedDailyCo2 = computed(() =>
  formatKg(animatedDailyCo2.value),
);

const formatLeaderboardName = (user) => {
  if (user?.username) return user.username;
  return `User #${user?.id ?? "-"}`;
};

const rankBadgeClass = (index) => {
  if (index === 0) return "bg-[#d4af37]";
  if (index === 1) return "bg-[#a2acbc]";
  if (index === 2) return "bg-[#b97745]";
  return "bg-main";
};

const loadDashboardData = async () => {
  loading.value = true;
  try {
    const token = normalizeToken(localStorage.getItem("jwt_token"));
    if (!token) {
      activities.value = [];
      dailyCo2.value = 0;
      leaderboard.value = [];
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    const jwtPayload = parseJwtPayload(token);
    const nowInSeconds = Math.floor(Date.now() / 1000);
    if (!jwtPayload?.exp || jwtPayload.exp <= nowInSeconds) {
      localStorage.removeItem("jwt_token");
      activities.value = [];
      dailyCo2.value = 0;
      leaderboard.value = [];
      router.push("/login");
      return;
    }

    const currentEmail = jwtPayload?.email || jwtPayload?.username || "";
    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: "application/ld+json",
    };

    const [users, entries, entryItems, activityTypes] = await Promise.all([
      fetchAll("users", headers),
      fetchAll("entries", headers),
      fetchAll("entry_items", headers),
      fetchAll("activity_types", headers),
    ]);

    const currentUser = users.find((user) => user.email === currentEmail);
    const currentUserIri = currentUser?.["@id"];
    if (!currentUserIri) {
      activities.value = [];
      dailyCo2.value = 0;
      return;
    }

    dailyTargetKg.value = 200;

    const entryItemsByIri = new Map(
      entryItems.map((item) => [item["@id"], item]),
    );
    const activityTypesByIri = new Map(
      activityTypes.map((type) => [type["@id"], type]),
    );

    const userEntries = entries
      .filter((entry) => entry.owner === currentUserIri)
      .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

    const todayEntries = userEntries.filter((entry) =>
      isSameDay(entry.createdAt),
    );
    dailyCo2.value = todayEntries.reduce(
      (sum, entry) =>
        sum + entryCo2(entry, entryItemsByIri, activityTypesByIri),
      0,
    );

    activities.value = userEntries
      .slice(0, 3)
      .map((entry) =>
        toDashboardActivity(entry, entryItemsByIri, activityTypesByIri),
      );

    leaderboard.value = users
      .map((user) => {
        const userIri = user?.["@id"];
        const todayUserEntries = entries.filter(
          (entry) => entry.owner === userIri && isSameDay(entry.createdAt),
        );

        const todayUserCo2 = todayUserEntries.reduce(
          (sum, entry) =>
            sum + entryCo2(entry, entryItemsByIri, activityTypesByIri),
          0,
        );

        const points = Math.max(0, Math.round(200 - todayUserCo2 * 10));

        return {
          id: user.id,
          name: formatLeaderboardName(user),
          points,
          todayUserCo2,
          activityCount: todayUserEntries.length,
          isCurrentUser: userIri === currentUserIri,
          co2: `${formatKg(todayUserCo2)} kg CO2`,
        };
      })
      .sort((a, b) => {
        if (b.points !== a.points) return b.points - a.points;
        return a.todayUserCo2 - b.todayUserCo2;
      })
      .slice(0, 5);

   //pop up//
    setTimeout(() => {
      const userRank = leaderboard.value.findIndex(u => u.isCurrentUser);
      if (userRank > 0) {
        showOvertookPopup.value = true;
      }
    }, 1200);

  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    console.error("Dashboard data loading error:", error);
    activities.value = [];
    dailyCo2.value = 0;
    leaderboard.value = [];
  } finally {
    loading.value = false;
  }
};

watch(dailyCo2, (value) => {
  animateDailyCo2(Math.max(0, value));
});

onMounted(loadDashboardData);

onBeforeUnmount(() => {
  if (animationFrameId) cancelAnimationFrame(animationFrameId);
});
</script>

<style scoped>

.leaderboard-collapse-enter-active,
.leaderboard-collapse-leave-active {
  transition:
    opacity 0.24s ease,
    transform 0.24s ease,
    max-height 0.28s ease;
  overflow: hidden;
}

.leaderboard-collapse-enter-from,
.leaderboard-collapse-leave-to {
  opacity: 0;
  transform: translateY(-6px);
  max-height: 0;
}

.leaderboard-collapse-enter-to,
.leaderboard-collapse-leave-from {
  opacity: 1;
  transform: translateY(0);
  max-height: 320px;
}


.pop-enter-active {
  transition: opacity 0.3s ease-out;
}
.pop-leave-active {
  transition: opacity 0.2s ease-in;
}
.pop-enter-from,
.pop-leave-to {
  opacity: 0;
}

.animate-zoom {
  animation: zoom-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes zoom-in {
  from { transform: scale(0.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>