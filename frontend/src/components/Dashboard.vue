<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28 shadow-[0_0_0_1px_rgba(0,0,0,0.04)] lg:my-0 lg:rounded-none lg:px-10"
    >
      <section
        class="relative overflow-hidden rounded-b-[36px] bg-main-light px-4 pb-6 pt-4"
      >
        <div
          class="absolute inset-x-0 bottom-6 h-36 bg-[radial-gradient(circle_at_20%_40%,rgba(17,125,111,0.18),transparent_32%),radial-gradient(circle_at_80%_30%,rgba(17,125,111,0.12),transparent_28%)] opacity-80"
        ></div>

        <div class="relative flex flex-col items-center">
          <div
            class="relative flex h-64 w-64 items-center justify-center rounded-full bg-white shadow-[0_0_0_1px_rgba(17,125,111,0.12)] transition-all duration-300"
            :style="gaugeStyle"
          >
            <div
              class="absolute inset-4 rounded-full border border-dashed border-grey/50"
            ></div>
            <div
              class="absolute -right-2 top-1/2 h-7 w-7 -translate-y-1/2 rounded-full border-4 border-main bg-white"
            ></div>

            <div
              class="relative z-10 flex flex-col items-center justify-center text-center"
            >
              <img
                :src="mascotSrc"
                alt="Mascot"
                class="mb-2 h-24 w-24 object-contain"
              />
              <p class="font-title text-[38px] leading-none text-black">
                {{ formattedAnimatedDailyCo2 }} kg
              </p>
              <p class="mt-2 text-[14px] text-grey">CO2 / Daily</p>
              <p class="text-body-12 mt-1 text-main">
                {{ Math.round(progressPercent) }}% of daily target
              </p>
            </div>
          </div>

          <div
            class="relative z-10 mt-4 w-full rounded-2xl border border-grey/20 bg-white px-4 py-2 text-center shadow-[0_8px_18px_rgba(0,0,0,0.14)]"
          >
            <p class="font-title text-[28px] leading-none text-main">
              MY POINTS
            </p>
            <p class="mt-1 font-ui text-[18px] font-bold text-main">
              + 560 n2e points
            </p>
          </div>

          <button
            type="button"
            class="mt-3 flex w-full items-center justify-center rounded-full border border-grey/20 bg-white px-4 py-2 text-[13px] font-medium text-black shadow-[0_8px_18px_rgba(0,0,0,0.14)]"
          >
            Friends Leaderboard (live)
            <span class="ml-2 text-[18px] leading-none">⌄</span>
          </button>
        </div>
      </section>

      <section class="px-4 pt-5">
        <h2 class="font-ui text-[18px] font-semibold text-black">
          Latest Activities
        </h2>

        <div v-if="loading" class="mt-4 rounded-2xl border border-grey/15 bg-white px-4 py-4 text-center text-[14px] text-grey shadow-[0_6px_16px_rgba(0,0,0,0.14)]">
          Loading activities...
        </div>

        <div v-else-if="activities.length === 0" class="mt-4 rounded-2xl border border-grey/15 bg-white px-4 py-4 text-center text-[14px] text-grey shadow-[0_6px_16px_rgba(0,0,0,0.14)]">
          No activity yet for today. Add one from Activities.
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
                    class="mt-1 text-[14px] font-medium"
                    :class="activity.pointsClass"
                  >
                    {{ activity.points }}
                  </p>
                </div>
                <p class="shrink-0 font-ui text-[18px] font-medium text-black">
                  {{ activity.quantityLabel }}
                </p>
              </div>

              <p
                class="mt-1 text-right text-[14px] font-medium"
                :class="activity.co2Class"
              >
                {{ activity.co2 }}
              </p>
            </div>
          </article>
        </div>
      </section>
    </main>

    <BottomNav active="home" />
  </div>
</template>

<script setup>
import axios from "axios";
import { Icon } from "@iconify/vue";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import mascotSrc from "../assets/mascotte_neutre.svg";
import BottomNav from "./BottomNav.vue";

const API_BASE = "http://localhost:8000/api";

const loading = ref(true);
const dailyCo2 = ref(0);
const animatedDailyCo2 = ref(0);
const dailyTargetKg = ref(30);
const activities = ref([]);

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
  return new Intl.NumberFormat("fr-FR", {
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
      activityType?.name ||
      entry?.details?.transportMode ||
      entry?.details?.source ||
      entry?.details?.diet ||
      `Activity #${entry.id}`,
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

const gaugeStyle = computed(() => {
  const p = Math.max(0, Math.min(progressPercent.value, 100));
  return {
    background: `conic-gradient(rgba(17,125,111,0.95) ${p}%, rgba(17,125,111,0.15) ${p}% 100%)`,
    padding: "10px",
  };
});

const formattedAnimatedDailyCo2 = computed(() =>
  formatKg(animatedDailyCo2.value),
);

const loadDashboardData = async () => {
  loading.value = true;
  try {
    const token = localStorage.getItem("jwt_token");
    if (!token) {
      activities.value = [];
      dailyCo2.value = 0;
      return;
    }

    const jwtPayload = parseJwtPayload(token);
    const currentEmail = jwtPayload?.username || "";
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

    dailyTargetKg.value = Number(currentUser?.targetCo2 || 2000) / 365;

    const entryItemsByIri = new Map(
      entryItems.map((item) => [item["@id"], item]),
    );
    const activityTypesByIri = new Map(
      activityTypes.map((type) => [type["@id"], type]),
    );

    const userEntries = entries
      .filter((entry) => entry.owner === currentUserIri)
      .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

    const todayEntries = userEntries.filter((entry) => isSameDay(entry.createdAt));
    dailyCo2.value = todayEntries.reduce(
      (sum, entry) => sum + entryCo2(entry, entryItemsByIri, activityTypesByIri),
      0,
    );

    activities.value = userEntries
      .slice(0, 3)
      .map((entry) => toDashboardActivity(entry, entryItemsByIri, activityTypesByIri));
  } catch (error) {
    console.error("Dashboard data loading error:", error);
    activities.value = [];
    dailyCo2.value = 0;
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
