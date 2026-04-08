<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-28 shadow-[0_0_0_1px_rgba(0,0,0,0.04)]"
    >
      <Transition name="fade">
        <div
          v-if="zoomedCard"
          class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-md flex items-center justify-center p-4"
        >
          <button
            @click="zoomedCard = null"
            class="absolute top-8 right-6 z-[110] bg-white rounded-full p-3 shadow-xl active:scale-90 transition-transform"
          >
            <Icon icon="ph:x-bold" class="h-8 w-8 text-main" />
          </button>

          <div
            class="w-full max-w-lg bg-main rounded-[2.5rem] p-6 shadow-2xl animate-zoom"
          >
            <h2
              class="text-white font-title text-title-h4 uppercase text-center mb-6"
            >
              {{ getTitle(zoomedCard) }}
            </h2>
            <div
              class="bg-white rounded-3xl p-6 min-h-[380px] flex flex-col justify-center"
            >
              <div
                v-if="zoomedCard === 'weekly'"
                class="w-full flex flex-col gap-6 items-center text-black"
              >
                <div
                  class="p-6 bg-main-light rounded-2xl w-full text-center border border-main/10"
                >
                  <p
                    class="text-body-12 font-bold text-grey uppercase tracking-widest mb-2"
                  >
                    {{ t("data.total_impact") }}
                  </p>
                  <h3 class="font-title text-title-h2 text-main">
                    {{ weeklyCo2Label }} CO2
                  </h3>
                  <p
                    class="text-body-12 text-grey font-medium mt-2 italic text-center"
                  >
                    {{ t("data.current_week_analysis") }}
                  </p>
                </div>
                <div class="w-full space-y-3">
                  <div
                    class="flex justify-between items-center p-4 bg-secondary-light/20 rounded-2xl border border-grey/10"
                  >
                    <div class="flex items-center gap-3 font-bold text-body-16">
                      <Icon
                        icon="ph:trend-down-bold"
                        class="text-main h-6 w-6"
                      />{{ t("data.performance") }}
                    </div>
                    <span class="text-main font-bold">-12% vs last wk</span>
                  </div>
                  <div
                    class="flex justify-between items-center p-4 bg-secondary-light/20 rounded-2xl border border-grey/10"
                  >
                    <div class="flex items-center gap-3 font-bold text-body-16">
                      <Icon
                        icon="ph:users-three-bold"
                        class="text-main h-6 w-6"
                      />{{ t("data.community") }}
                    </div>
                    <span class="text-grey font-bold">Top 15%</span>
                  </div>
                </div>
              </div>

              <div v-if="zoomedCard === 'progression'" class="w-full space-y-6">
                <div class="flex bg-main-light rounded-full p-1">
                  <button
                    v-for="p in ['day', 'week', 'month']"
                    :key="p"
                    @click="activePeriod = p"
                    class="flex-1 py-2 rounded-full text-body-12 font-bold uppercase"
                    :class="
                      activePeriod === p ? 'bg-main text-white' : 'text-main'
                    "
                  >
                    {{ t(`data.${p}`) }}
                  </button>
                </div>
                <div class="h-48 flex items-end justify-between gap-3 px-2">
                  <div
                    v-for="(val, i) in currentData.values"
                    :key="i"
                    class="w-full bg-main rounded-t-xl relative transition-all duration-700"
                    :style="{ height: `${(val / currentData.max) * 100}%` }"
                  >
                    <span
                      class="absolute -top-7 left-1/2 -translate-x-1/2 text-body-12 font-bold text-main whitespace-nowrap"
                      >{{ val }} kg</span
                    >
                  </div>
                </div>
                <div
                  class="flex justify-between font-bold text-body-12 text-main uppercase"
                >
                  <span v-for="l in currentData.labels" :key="l">{{ l }}</span>
                </div>
              </div>

              <div v-if="zoomedCard === 'challenges'" class="w-full space-y-6">
                <div
                  v-for="c in challenges"
                  :key="c.title"
                  class="space-y-2 text-black text-body-16"
                >
                  <div class="flex justify-between font-bold">
                    <p>{{ c.title }}</p>
                    <p class="text-main">{{ c.progress }}%</p>
                  </div>
                  <div
                    class="bg-grey/15 h-4 rounded-full overflow-hidden shadow-inner relative"
                  >
                    <div
                      class="absolute inset-y-0 left-0 bg-main h-full rounded-full transition-all duration-1000"
                      :style="{ width: c.progress + '%' }"
                    ></div>
                  </div>
                </div>
                <div class="h-px bg-grey/10 w-full my-4"></div>
                <p
                  class="text-body-12 font-bold text-grey uppercase tracking-widest text-center"
                >
                  {{ t("data.your_badges") }}
                </p>
                <div class="flex justify-around items-center">
                  <div
                    v-for="b in badges"
                    :key="b.name"
                    class="flex flex-col items-center gap-2"
                  >
                    <div
                      class="h-14 w-14 rounded-full flex items-center justify-center text-main border-2 border-main/20"
                      :class="
                        b.locked
                          ? 'bg-grey/10 grayscale opacity-40'
                          : 'bg-main-light'
                      "
                    >
                      <Icon :icon="b.icon" class="h-8 w-8" />
                    </div>
                    <span
                      class="text-body-8 font-bold text-black text-center uppercase"
                      >{{ b.name }}</span
                    >
                  </div>
                </div>
              </div>

              <div v-if="zoomedCard === 'split'" class="h-80 w-full">
                <Pie :data="chartData" :options="chartOptions" />
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <header class="flex flex-col items-center pt-8 px-4 relative z-10">
        <div
          class="relative h-32 w-32 rounded-full border-4 border-main overflow-hidden shadow-lg"
        >
          <img
            :src="personaSrc"
            alt="Profile"
            class="h-full w-full object-cover"
          />
        </div>

        <div
          class="mt-4 w-full rounded-3xl p-6 shadow-xl text-center border border-grey/5 bg-white"
        >
          <h1
            class="font-title text-title-h3 uppercase leading-none text-black tracking-tight"
          >
            {{ profileUsername }}
          </h1>
          <p class="text-main font-medium mt-1 text-body-16">
            {{ t("data.level", { lvl: profileLevel }) }}
          </p>
          <div class="h-px bg-grey/20 w-full my-4"></div>
          <div class="flex justify-around items-center text-black">
            <div class="text-center">
              <p class="text-body-12 font-bold uppercase">
                {{ t("data.friends_label") }} :
              </p>
              <p class="text-body-16 font-semibold">{{ friendsCount }}</p>
            </div>
            <div class="w-px bg-grey/20 h-10"></div>
            <div class="text-center">
              <p class="text-body-12 font-bold uppercase">
                {{ t("data.saved_label") }} :
              </p>
              <p class="text-body-16 font-semibold">{{ totalCo2Label }}</p>
            </div>
          </div>
        </div>
      </header>

      <section class="mt-8 px-4 grid grid-cols-1 gap-6 relative z-20">
        <article
          @click="zoomedCard = 'weekly'"
          class="group bg-main rounded-[2rem] p-6 flex flex-col shadow-lg cursor-pointer active:scale-95 transition-transform"
        >
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-body-16">
              {{ t("data.weekly_comparison") }}
            </h2>
            <Icon
              icon="ph:magnifying-glass-plus-bold"
              class="text-white/40 h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
            />
          </div>
          <div
            class="bg-white/90 rounded-2xl p-6 flex items-center justify-around text-center"
          >
            <div>
              <p class="text-body-12 text-black font-bold uppercase mb-1">
                {{ t("data.you") }}
              </p>
              <div
                class="bg-main text-white py-3 px-6 rounded-xl font-title text-title-h4 shadow-inner"
              >
                {{ weeklyCo2Label }}
              </div>
            </div>
            <div class="h-16 w-px bg-grey/20"></div>
            <div>
              <p class="text-body-12 text-grey font-bold uppercase mb-1">
                {{ t("data.national_average") }}
              </p>
              <div
                class="flex items-center justify-center gap-2 bg-main-light text-main py-3 px-5 rounded-xl font-bold text-body-16"
              >
                <Icon icon="ph:users-three-fill" /> + 3kg
              </div>
            </div>
          </div>
        </article>

        <article
          @click="zoomedCard = 'progression'"
          class="group bg-main rounded-[2rem] p-6 shadow-lg flex flex-col cursor-pointer active:scale-95 transition-transform"
        >
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-body-16">
              {{ t("data.progression") }}
            </h2>
            <Icon
              icon="ph:magnifying-glass-plus-bold"
              class="text-white/40 h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
            />
          </div>
          <div
            class="bg-white/95 rounded-2xl p-5 flex flex-col justify-between overflow-hidden"
          >
            <p
              class="text-body-14 text-grey font-bold italic mb-4 uppercase tracking-tighter"
            >
              kg CO2
            </p>
            <div class="flex items-end justify-between h-32 px-1 gap-2">
              <div
                v-for="(val, index) in currentData.values"
                :key="index"
                class="w-full bg-main rounded-t-lg"
                :style="{
                  height: `${(val / currentData.max) * 100}%`,
                  opacity: 0.4 + index * 0.15,
                }"
              ></div>
            </div>
            <div class="flex justify-between mt-4">
              <div
                v-for="(label, index) in currentData.labels"
                :key="index"
                class="text-body-10 text-center font-bold text-black flex-1 uppercase leading-tight"
              >
                {{ label }}<br /><span
                  class="text-body-10 font-normal text-grey"
                  >{{ currentData.values[index] }}kg</span
                >
              </div>
            </div>
          </div>
        </article>

        <article
          @click="zoomedCard = 'challenges'"
          class="group bg-main rounded-[2rem] p-6 shadow-lg flex flex-col cursor-pointer active:scale-95 transition-transform"
        >
          <div class="flex justify-between items-center mb-5">
            <h2 class="text-white font-bold text-body-16">
              {{ t("data.challenges_achievement") }}
            </h2>
            <Icon
              icon="ph:magnifying-glass-plus-bold"
              class="text-white/40 h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
            />
          </div>
          <div
            class="bg-white rounded-3xl p-6 flex flex-col justify-between h-full gap-6"
          >
            <div class="flex flex-col gap-5">
              <div
                v-for="c in challenges"
                :key="c.title"
                class="flex flex-col gap-2"
              >
                <div class="flex items-center gap-3 text-body-12">
                  <Icon :icon="c.icon" class="h-6 w-6 text-main shrink-0" />
                  <p class="font-bold text-black underline">{{ c.title }}</p>
                </div>
                <div class="flex items-center gap-4">
                  <div
                    class="flex-1 bg-grey/15 h-3 rounded-full overflow-hidden shadow-inner relative"
                  >
                    <div
                      class="absolute inset-y-0 left-0 bg-main h-full rounded-full transition-all duration-1000"
                      :style="{ width: c.progress + '%' }"
                    ></div>
                  </div>
                  <span class="text-body-12 font-bold text-main"
                    >{{ c.progress }}%</span
                  >
                </div>
              </div>
            </div>
            <div class="h-px bg-grey/10 w-full"></div>
            <p
              class="text-body-8 font-bold text-grey uppercase tracking-widest text-center"
            >
              {{ t("data.recent_badges") }}
            </p>
            <div class="flex justify-around items-center">
              <div
                v-for="b in badges.slice(0, 3)"
                :key="b.name"
                class="h-12 w-12 rounded-full flex items-center justify-center shadow-sm"
                :class="
                  b.locked
                    ? 'bg-grey/20 opacity-40'
                    : 'bg-main-light text-main border border-main/10'
                "
              >
                <Icon :icon="b.icon" class="h-7 w-7" />
              </div>
            </div>
            <div
              class="bg-main-light rounded-xl p-3 border border-main/10 text-center"
            >
              <p class="text-body-12 text-main leading-tight italic">
                "{{ t("data.eco_tip") }}"
              </p>
            </div>
          </div>
        </article>

        <article
          @click="zoomedCard = 'split'"
          class="group bg-main rounded-[2rem] p-6 flex flex-col shadow-lg cursor-pointer active:scale-95 transition-transform"
        >
          <div class="flex justify-between items-center mb-1">
            <h2 class="text-white font-bold text-body-16 leading-tight">
              {{ t("data.co2_split") }}
            </h2>
            <Icon
              icon="ph:magnifying-glass-plus-bold"
              class="text-white/40 h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
            />
          </div>
          <p
            class="text-body-12 text-white/70 italic uppercase mb-4 tracking-wider"
          >
            {{ t("data.breakdown_by_category") }}
          </p>
          <div
            class="bg-white/90 rounded-2xl p-6 min-h-[250px] flex items-center justify-center"
          >
            <Pie :data="chartData" :options="chartOptions" />
          </div>
        </article>
      </section>
    </main>

    <BottomNav active="pulse" />
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, computed, onMounted, watch } from "vue";
import { Icon } from "@iconify/vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import BottomNav from "./BottomNav.vue";
import personaSrc from "../assets/img/profil-pic/persona.jpg";
import { Pie } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const router = useRouter();
const { t, locale } = useI18n();

const API_BASE = "http://localhost:8000/api";

const zoomedCard = ref(null);
const activePeriod = ref("week");

// ─── State ────────────────────────────────────────────────────────────────
const profileUsername = ref("");
const profileLevel = ref(1);
const friendsCount = ref(0);
const totalCo2 = ref(0);
const weeklyCo2 = ref(0);
const progressionByPeriod = ref({
  day: { labels: [], values: [], max: 1 },
  week: { labels: [], values: [], max: 1 },
  month: { labels: [], values: [], max: 1 },
});
const co2ByCategory = ref({
  transport: 0,
  food: 0,
  clothing: 0,
  consumption: 0,
});

// ─── JWT / API helpers ────────────────────────────────────────────────────
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

const extractCollection = (data) =>
  data?.member ?? data?.["hydra:member"] ?? (Array.isArray(data) ? data : []);

const fetchAll = async (resource, headers) => {
  const all = [];
  const seen = new Set();
  let page = 1;
  while (true) {
    const { data } = await axios.get(`${API_BASE}/${resource}?page=${page}`, {
      headers,
    });
    const chunk = extractCollection(data);
    if (chunk.length === 0) break;
    let added = 0;
    for (const item of chunk) {
      const iri = item?.["@id"];
      if (!iri || seen.has(iri)) continue;
      seen.add(iri);
      all.push(item);
      added++;
    }
    if (added === 0) break;
    page++;
  }
  return all;
};

// ─── Data load ────────────────────────────────────────────────────────────
const loadData = async () => {
  try {
    const token = normalizeToken(localStorage.getItem("jwt_token"));
    if (!token) {
      router.push("/login");
      return;
    }

    const jwtPayload = parseJwtPayload(token);
    if (!jwtPayload?.exp || jwtPayload.exp <= Math.floor(Date.now() / 1000)) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    const currentEmail = jwtPayload?.email || jwtPayload?.username || "";
    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: "application/ld+json",
    };

    const [users, entries, entryItems, activityTypes, categories, friendships] =
      await Promise.all([
        fetchAll("users", headers),
        fetchAll("entries", headers),
        fetchAll("entry_items", headers),
        fetchAll("activity_types", headers),
        fetchAll("categories", headers),
        fetchAll("friendships", headers),
      ]);

    const currentUser = users.find((u) => u.email === currentEmail);
    const currentUserIri = currentUser?.["@id"];
    if (!currentUserIri) return;

    profileUsername.value = currentUser?.username || currentEmail.split("@")[0];

    const entryItemsByIri = new Map(entryItems.map((i) => [i["@id"], i]));
    const activityTypesByIri = new Map(activityTypes.map((a) => [a["@id"], a]));
    const categoriesByIri = new Map(categories.map((c) => [c["@id"], c]));

    const userEntries = entries.filter((e) => e.owner === currentUserIri);
    profileLevel.value = Math.max(1, Math.floor(userEntries.length / 3) + 1);

    friendsCount.value = friendships.filter(
      (f) =>
        (f.sender === currentUserIri || f.receiver === currentUserIri) &&
        f.status === "accepted",
    ).length;

    const calcCo2 = (entry) => {
      const iris = Array.isArray(entry?.entryItems) ? entry.entryItems : [];
      if (iris.length === 0) return Number(entry?.totalCo2 || 0);
      let sum = 0;
      for (const iri of iris) {
        const item = entryItemsByIri.get(iri);
        if (!item) continue;
        sum +=
          Number(item.quantity || 0) *
          Number(activityTypesByIri.get(item.activityType)?.co2Factor || 0);
      }
      return sum;
    };

    totalCo2.value =
      Math.round(userEntries.reduce((s, e) => s + calcCo2(e), 0) * 10) / 10;

    // Weekly CO2 (lundi → maintenant)
    const now = new Date();
    const startOfWeek = new Date(now);
    const dow = now.getDay();
    startOfWeek.setDate(now.getDate() - (dow === 0 ? 6 : dow - 1));
    startOfWeek.setHours(0, 0, 0, 0);
    weeklyCo2.value =
      Math.round(
        userEntries
          .filter((e) => e.createdAt && new Date(e.createdAt) >= startOfWeek)
          .reduce((s, e) => s + calcCo2(e), 0) * 10,
      ) / 10;

    // CO2 par catégorie (camembert)
    const catMap = { transport: 0, food: 0, clothing: 0, consumption: 0 };
    for (const entry of userEntries) {
      for (const iri of Array.isArray(entry?.entryItems)
        ? entry.entryItems
        : []) {
        const item = entryItemsByIri.get(iri);
        if (!item) continue;
        const type = activityTypesByIri.get(item.activityType);
        if (!type) continue;
        const catName = (
          typeof type.category === "string"
            ? categoriesByIri.get(type.category)?.name
            : type.category?.name || ""
        ).toLowerCase();
        const co2 = Number(item.quantity || 0) * Number(type.co2Factor || 0);
        if (catName.includes("travel") || catName.includes("transport"))
          catMap.transport += co2;
        else if (catName.includes("food") || catName.includes("aliment"))
          catMap.food += co2;
        else if (catName.includes("cloth") || catName.includes("vetement"))
          catMap.clothing += co2;
        else catMap.consumption += co2;
      }
    }
    co2ByCategory.value = {
      transport: Math.round(catMap.transport * 10) / 10,
      food: Math.round(catMap.food * 10) / 10,
      clothing: Math.round(catMap.clothing * 10) / 10,
      consumption: Math.round(catMap.consumption * 10) / 10,
    };

    // Progression : 4 derniers jours / semaines / mois
    const getWeekNum = (d) => {
      const tmp = new Date(d);
      tmp.setHours(0, 0, 0, 0);
      tmp.setDate(tmp.getDate() + 4 - (tmp.getDay() || 7));
      return Math.ceil(
        ((tmp - new Date(tmp.getFullYear(), 0, 1)) / 86400000 + 1) / 7,
      );
    };
    const nl = locale.value === "fr" ? "fr-FR" : "en-US";

    // Jours
    const dayMap = new Map();
    for (let i = 3; i >= 0; i--) {
      const d = new Date(now);
      d.setDate(now.getDate() - i);
      dayMap.set(d.toISOString().slice(0, 10), 0);
    }
    for (const e of userEntries) {
      const k = e.createdAt?.slice(0, 10);
      if (dayMap.has(k)) dayMap.set(k, dayMap.get(k) + calcCo2(e));
    }
    const dayVals = [...dayMap.values()].map((v) => Math.round(v * 10) / 10);

    // Semaines
    const weekMap = new Map();
    for (let i = 3; i >= 0; i--) {
      const d = new Date(now);
      d.setDate(now.getDate() - i * 7);
      const k = `${d.getFullYear()}-W${String(getWeekNum(d)).padStart(2, "0")}`;
      if (!weekMap.has(k)) weekMap.set(k, 0);
    }
    for (const e of userEntries) {
      if (!e.createdAt) continue;
      const d = new Date(e.createdAt);
      const k = `${d.getFullYear()}-W${String(getWeekNum(d)).padStart(2, "0")}`;
      if (weekMap.has(k)) weekMap.set(k, weekMap.get(k) + calcCo2(e));
    }
    const weekVals = [...weekMap.values()].map((v) => Math.round(v * 10) / 10);

    // Mois
    const monthMap = new Map();
    for (let i = 3; i >= 0; i--) {
      const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
      monthMap.set(
        `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`,
        0,
      );
    }
    for (const e of userEntries) {
      if (!e.createdAt) continue;
      const d = new Date(e.createdAt);
      const k = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
      if (monthMap.has(k)) monthMap.set(k, monthMap.get(k) + calcCo2(e));
    }
    const monthVals = [...monthMap.values()].map(
      (v) => Math.round(v * 10) / 10,
    );

    progressionByPeriod.value = {
      day: {
        labels: [...dayMap.keys()].map((k) =>
          new Date(k).toLocaleDateString(nl, {
            day: "2-digit",
            month: "2-digit",
          }),
        ),
        values: dayVals,
        max: Math.max(...dayVals, 1),
      },
      week: {
        labels: [...weekMap.keys()].map((k) => `S.${k.split("-W")[1]}`),
        values: weekVals,
        max: Math.max(...weekVals, 1),
      },
      month: {
        labels: [...monthMap.keys()].map((k) => {
          const [y, m] = k.split("-");
          return new Date(+y, +m - 1, 1).toLocaleDateString(nl, {
            month: "short",
          });
        }),
        values: monthVals,
        max: Math.max(...monthVals, 1),
      },
    };
  } catch (err) {
    if (err?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
    } else {
      console.error("Data page load error:", err);
    }
  }
};

onMounted(loadData);
watch(locale, loadData);

// ─── Computed UI ──────────────────────────────────────────────────────────
const totalCo2Label = computed(() => `+${totalCo2.value} kg CO2`);
const weeklyCo2Label = computed(() => `+ ${weeklyCo2.value} kg`);
const currentData = computed(
  () => progressionByPeriod.value[activePeriod.value],
);

const getTitle = (id) => {
  const labels = {
    weekly: t("data.weekly_comparison"),
    progression: t("data.progression_details"),
    challenges: t("data.challenges_achievement"),
    split: t("data.co2_split"),
  };
  return labels[id];
};


const statsData = {
  day: { labels: ['Morning', 'Noon', 'After.', 'Eve.'], values: [0.4, 2.1, 1.2, 0.8], max: 3 },
  week: { labels: ['Wk.10', 'Wk.11', 'Wk.12', 'Wk.13'], values: [2, 3, 8, 12], max: 15 },
  month: { labels: ['Jan.', 'Feb.', 'Mar.', 'Apr.'], values: [45, 38, 52, 30], max: 60 }
};

const challenges = computed(() => [
  {
    title: t("data.challenges.car_free_week"),
    icon: "ph:bicycle-bold",
    progress: 80,
  },
  {
    title: t("data.challenges.vegetarian_days"),
    icon: "ph:leaf-bold",
    progress: 45,
  },
]);

const badges = computed(() => [
  { name: t("data.badges.eco_root"), icon: "ph:leaf-fill", locked: false },
  { name: t("data.badges.cyclist"), icon: "ph:bicycle-fill", locked: false },
  { name: t("data.badges.energy"), icon: "ph:lightning-fill", locked: true },
]);

const chartData = computed(() => {
  const cat = co2ByCategory.value;
  const total = cat.transport + cat.food + cat.clothing + cat.consumption;
  const pct = (v) => (total > 0 ? Math.round((v / total) * 100) : 0);
  return {
    labels: [
      t("data.chart.transport"),
      t("data.chart.food"),
      t("data.chart.shopping"),
      t("data.chart.energy"),
    ],
    datasets: [
      {
        backgroundColor: ["#117d6f", "#19B4A0", "#4B6481", "#a1a1aa"],
        borderWidth: 2,
        borderColor: "#ffffff",
        data: [
          pct(cat.transport),
          pct(cat.food),
          pct(cat.clothing),
          pct(cat.consumption),
        ],
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: "bottom",
      labels: {
        boxWidth: 10,
        font: { size: 10, family: "Luciole" },
        color: "#117d6f",
        padding: 15,
      },
    },
  },
};
</script>

<style scoped>
.font-title { font-family: var(--font-title); }
.font-ui { font-family: var(--font-ui); }
.text-main { color: var(--color-main); }
.text-grey { color: var(--color-grey); }

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes zoomIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
.animate-zoom {
  animation: zoomIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
