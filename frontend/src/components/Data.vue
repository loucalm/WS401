<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <div
      v-if="isInitialLoading"
      class="mx-auto flex min-h-screen w-full max-w-105 items-center justify-center bg-white"
    >
      <Spinner :label="t('common.loading')" />
    </div>

    <template v-else>
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
                      <div
                        class="flex items-center gap-3 font-bold text-body-16"
                      >
                        <Icon
                          icon="ph:trend-down-bold"
                          class="text-main h-6 w-6"
                        />{{ t("data.performance") }}
                      </div>
                      <span class="text-main font-bold">{{
                        weeklyTrendLabel
                      }}</span>
                    </div>
                    <div
                      class="flex justify-between items-center p-4 bg-secondary-light/20 rounded-2xl border border-grey/10"
                    >
                      <div
                        class="flex items-center gap-3 font-bold text-body-16"
                      >
                        <Icon
                          icon="ph:users-three-bold"
                          class="text-main h-6 w-6"
                        />{{ t("data.community") }}
                      </div>
                      <span class="text-grey font-bold">{{
                        communityFriendsLabel
                      }}</span>
                    </div>
                  </div>
                </div>

                <div
                  v-if="zoomedCard === 'progression'"
                  class="w-full space-y-6"
                >
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
                  <div
                    v-if="hasProgressionData"
                    class="h-48 flex items-end justify-between gap-3 px-2"
                  >
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
                  <p
                    v-else
                    class="rounded-2xl border border-grey/15 bg-main-light/40 py-10 text-center font-ui text-body-14 font-semibold text-grey"
                  >
                    {{ t("data.no_data") }}
                  </p>
                  <div
                    class="flex justify-between font-bold text-body-12 text-main uppercase"
                  >
                    <span v-for="l in currentData.labels" :key="l">{{
                      l
                    }}</span>
                  </div>
                </div>

                <div
                  v-if="zoomedCard === 'challenges'"
                  class="w-full space-y-6"
                >
                  <div
                    v-for="c in challenges"
                    :key="c.title"
                    class="space-y-2 text-black text-body-16"
                  >
                    <div class="flex justify-between font-bold">
                      <p>{{ c.title }}</p>
                      <p class="text-main">{{ c.progress }}%</p>
                    </div>
                    <p class="text-body-12 text-grey font-medium">
                      {{ c.subtitle }}
                    </p>
                    <div
                      class="bg-grey/15 h-4 rounded-full overflow-hidden shadow-inner relative"
                    >
                      <div
                        class="absolute inset-y-0 left-0 bg-main h-full rounded-full transition-all duration-1000"
                        :style="{ width: c.progress + '%' }"
                      ></div>
                    </div>
                    <div
                      class="flex items-center justify-between text-body-12 font-semibold text-grey"
                    >
                      <span>{{ c.currentLabel }}</span>
                      <span>{{ c.goalLabel }}</span>
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

                <div
                  v-if="zoomedCard === 'split' && hasPieData"
                  class="h-80 w-full"
                >
                  <Pie :data="chartData" :options="chartOptions" />
                </div>
                <div
                  v-else-if="zoomedCard === 'split'"
                  class="flex h-80 w-full items-center justify-center rounded-2xl border border-grey/15 bg-main-light/30"
                >
                  <p class="font-ui text-body-16 font-semibold text-grey">
                    {{ t("data.no_data") }}
                  </p>
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
              :src="profilePictureSrc"
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
                  {{ t("data.total_points_label") }} :
                </p>
                <p class="text-body-16 font-semibold">{{ totalPointsLabel }}</p>
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
              <div
                v-if="hasProgressionData"
                class="flex items-end justify-between h-32 px-1 gap-2"
              >
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
              <div
                v-else
                class="flex h-32 items-center justify-center rounded-2xl border border-grey/15 bg-main-light/40"
              >
                <p class="font-ui text-body-14 font-semibold text-grey">
                  {{ t("data.no_data") }}
                </p>
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
              <Pie
                v-if="hasPieData"
                :data="chartData"
                :options="chartOptions"
              />
              <p v-else class="font-ui text-body-16 font-semibold text-grey">
                {{ t("data.no_data") }}
              </p>
            </div>
          </article>
        </section>

        <section class="mt-8 px-4 pb-2">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="font-title text-title-h4 uppercase text-black">
              {{ t("data.all_activities") }}
            </h2>
            <p class="font-ui text-body-12 text-grey">
              {{ dataActivities.length }}
            </p>
          </div>

          <div
            v-if="dataActivities.length === 0"
            class="rounded-2xl border border-grey/15 bg-white px-4 py-6 text-center text-body-14 font-semibold text-grey shadow-[0_6px_16px_rgba(0,0,0,0.1)]"
          >
            {{ t("data.no_data") }}
          </div>

          <div v-else class="space-y-3">
            <article
              v-for="activity in dataActivities"
              :key="activity.id"
              class="rounded-2xl border border-grey/15 bg-white px-4 py-3 shadow-[0_6px_16px_rgba(0,0,0,0.1)]"
            >
              <div class="flex items-start gap-3">
                <div
                  class="mt-0.5 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-main-light text-main"
                >
                  <Icon :icon="activity.icon" class="h-7 w-7" />
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                      <p
                        class="truncate font-ui text-body-16 font-semibold text-black"
                      >
                        {{ activity.title }}
                      </p>
                      <p class="mt-0.5 font-ui text-body-12 text-grey">
                        {{ activity.dateLabel }}
                      </p>
                    </div>
                    <p
                      class="shrink-0 font-ui text-body-14 font-semibold text-black"
                    >
                      {{ activity.quantityLabel }}
                    </p>
                  </div>

                  <div class="mt-1 flex items-center justify-between">
                    <p
                      class="font-ui text-body-14 font-semibold"
                      :class="activity.pointsClass"
                    >
                      {{ activity.points }}
                    </p>
                    <p
                      class="font-ui text-body-14 font-semibold"
                      :class="activity.co2Class"
                    >
                      {{ activity.co2 }}
                    </p>
                  </div>

                  <div class="mt-2 flex justify-end">
                    <button
                      type="button"
                      class="text-body-12 font-semibold uppercase tracking-wide text-systeme disabled:cursor-not-allowed disabled:opacity-50"
                      :disabled="deletingActivityIds.includes(activity.id)"
                      @click="requestDeleteActivity(activity)"
                    >
                      {{
                        deletingActivityIds.includes(activity.id)
                          ? t("data.deleting_activity")
                          : t("data.delete_activity")
                      }}
                    </button>
                  </div>
                </div>
              </div>
            </article>
          </div>
        </section>
      </main>

      <Transition name="fade">
        <div
          v-if="isDeleteModalOpen"
          class="fixed inset-0 z-120 flex items-end justify-center bg-black/45 p-4 backdrop-blur-sm sm:items-center"
        >
          <div
            class="w-full max-w-sm rounded-3xl border border-white/60 bg-white p-5 shadow-2xl"
          >
            <div class="flex items-start gap-3">
              <div
                class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-systeme/12 text-systeme"
              >
                <Icon icon="ph:warning-circle-bold" class="h-6 w-6" />
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-title text-title-h4 leading-none text-black">
                  {{ t("data.delete_activity_title") }}
                </h3>
                <p class="mt-2 font-ui text-body-14 leading-5 text-grey">
                  {{ t("data.delete_activity_confirm") }}
                </p>
                <p class="mt-2 font-ui text-body-12 font-semibold text-black">
                  {{ activityToDelete?.title || "" }}
                </p>
              </div>
            </div>

            <div class="mt-5 flex gap-3">
              <button
                type="button"
                class="flex-1 rounded-2xl border border-grey/20 px-4 py-3 font-ui text-body-14 font-semibold text-grey transition active:scale-[0.99] disabled:opacity-50"
                :disabled="isDeleteInProgress"
                @click="closeDeleteModal"
              >
                {{ t("data.delete_activity_cancel") }}
              </button>
              <button
                type="button"
                class="flex-1 rounded-2xl bg-systeme px-4 py-3 font-ui text-body-14 font-bold text-white shadow-[0_8px_18px_rgba(0,0,0,0.2)] transition active:scale-[0.99] disabled:opacity-50"
                :disabled="isDeleteInProgress"
                @click="confirmDeleteActivity"
              >
                {{
                  isDeleteInProgress
                    ? t("data.deleting_activity")
                    : t("data.delete_activity_confirm_button")
                }}
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <BottomNav active="pulse" />
    </template>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, computed, onMounted, watch } from "vue";
import { Icon } from "@iconify/vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import BottomNav from "./BottomNav.vue";
import Spinner from "./Spinner.vue";
import { resolveProfilePictureSrc } from "../utils/profilePictures.js";
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
const isInitialLoading = ref(true);

// ─── State ────────────────────────────────────────────────────────────────
const profileUsername = ref("");
const profilePictureSrc = ref(resolveProfilePictureSrc(null));
const profileLevel = ref(1);
const friendsCount = ref(0);
const totalPoints = ref(0);
const totalCo2 = ref(0);
const weeklyCo2 = ref(0);
const dailyTargetKg = ref(10);
const activeDaysLast7 = ref(0);
const clothingEntriesCount = ref(0);
const clothingSecondHandCount = ref(0);
const weeklyTrendPercent = ref(null);
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
const dataActivities = ref([]);
const deletingActivityIds = ref([]);
const isDeleteModalOpen = ref(false);
const activityToDelete = ref(null);
const isDeleteInProgress = computed(() =>
  deletingActivityIds.value.includes(activityToDelete.value?.id),
);

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

const toIri = (value) => {
  if (typeof value === "string") return value;
  if (value && typeof value === "object" && typeof value["@id"] === "string") {
    return value["@id"];
  }
  return "";
};

const normalizeText = (value = "") =>
  value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "");

const resolveCategoryName = (activityType, categoriesByIri) => {
  if (!activityType) return "";

  if (
    typeof activityType.category === "object" &&
    activityType.category?.name
  ) {
    return activityType.category.name;
  }

  if (typeof activityType.category === "string") {
    return categoriesByIri.get(activityType.category)?.name || "";
  }

  return "";
};

const isClothingActivityType = (activityType, categoriesByIri) => {
  const categoryName = normalizeText(
    resolveCategoryName(activityType, categoriesByIri),
  );
  const subCategory = normalizeText(activityType?.subCategory || "");
  const unit = normalizeText(activityType?.unitLabel || "");

  return (
    categoryName.includes("cloth") ||
    subCategory.includes("cloth") ||
    subCategory.includes("shoe") ||
    subCategory.includes("top") ||
    subCategory.includes("bottom") ||
    subCategory.includes("underwear") ||
    subCategory.includes("accessory") ||
    unit === "item" ||
    unit === "pair"
  );
};

const resolveClothingPurchaseLabel = (details = {}) => {
  const purchaseValue =
    typeof details?.purchaseType === "string"
      ? details.purchaseType
      : typeof details?.source === "string"
        ? details.source
        : "";

  const normalized = normalizeText(purchaseValue);
  if (!normalized) return "";

  if (normalized.includes("second")) {
    return t("wizard.purchase_options.second_hand");
  }

  if (normalized.includes("online") || normalized.includes("ligne")) {
    return t("wizard.purchase_options.online");
  }

  if (
    normalized.includes("shop") ||
    normalized.includes("store") ||
    normalized.includes("boutique") ||
    normalized.includes("magasin")
  ) {
    return t("wizard.purchase_options.shop");
  }

  return purchaseValue;
};

// ─── Data load ────────────────────────────────────────────────────────────
const loadData = async ({ showLoader = false } = {}) => {
  if (showLoader) {
    isInitialLoading.value = true;
  }

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

    const [
      users,
      entries,
      entryItems,
      activityTypes,
      categories,
      friendships,
      summary,
    ] = await Promise.all([
      fetchAll("users", headers),
      fetchAll("entries", headers),
      fetchAll("entry_items", headers),
      fetchAll("activity_types", headers),
      fetchAll("categories", headers),
      fetchAll("friendships", headers),
      axios
        .get(`${API_BASE}/me/gamification-summary`, { headers })
        .then((response) => response.data)
        .catch(() => null),
    ]);

    const currentUser = users.find((u) => u.email === currentEmail);
    const currentUserIri = currentUser?.["@id"];
    if (!currentUserIri) return;

    profileUsername.value = currentUser?.username || currentEmail.split("@")[0];
    profilePictureSrc.value = resolveProfilePictureSrc(
      currentUser.profilePicture,
    );

    const entryItemsByIri = new Map(entryItems.map((i) => [i["@id"], i]));
    const activityTypesByIri = new Map(activityTypes.map((a) => [a["@id"], a]));
    const categoriesByIri = new Map(categories.map((c) => [c["@id"], c]));

    const userEntries = entries.filter(
      (e) => toIri(e.owner) === currentUserIri,
    );
    profileLevel.value = Number(summary?.level || 1);
    totalPoints.value = Number(summary?.xpTotal || 0);
    dailyTargetKg.value = Number(summary?.targetCo2Kg || 10);

    friendsCount.value = friendships.filter(
      (f) =>
        (toIri(f.sender) === currentUserIri ||
          toIri(f.receiver) === currentUserIri) &&
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

    const normalizeKey = (value = "") =>
      value
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, "_")
        .replace(/^_+|_+$/g, "");

    const translateActivityLabel = (label = "") => {
      const key = `taxonomy.activities.${normalizeKey(label)}`;
      return t(key) !== key ? t(key) : label;
    };

    const translateDietLabel = (label = "") => {
      const key = `taxonomy.diets.${normalizeKey(label)}`;
      return t(key) !== key ? t(key) : label;
    };

    const iconForActivity = (activityType, hasDiet) => {
      if (hasDiet) return "mdi:silverware-fork-knife";
      if (activityType?.icon) return activityType.icon;

      const subCategory = (activityType?.subCategory || "").toLowerCase();
      if (subCategory.includes("transport")) return "mdi:car";
      if (subCategory.includes("energy")) return "mdi:flash";
      if (subCategory.includes("shoe") || subCategory.includes("top")) {
        return "tabler:shirt-filled";
      }

      return "mdi:leaf";
    };

    const toDataActivity = (entry) => {
      const itemIris = Array.isArray(entry?.entryItems) ? entry.entryItems : [];
      const item =
        itemIris.length > 0 ? entryItemsByIri.get(itemIris[0]) : null;
      const activityType = item
        ? activityTypesByIri.get(item.activityType)
        : null;
      const hasDiet = typeof entry?.details?.diet === "string";
      const isClothingEntry =
        itemIris.some((iri) => {
          const currentItem = entryItemsByIri.get(iri);
          const currentType = activityTypesByIri.get(currentItem?.activityType);
          return isClothingActivityType(currentType, categoriesByIri);
        }) || typeof entry?.details?.purchaseType === "string";

      const purchaseLabel = isClothingEntry
        ? resolveClothingPurchaseLabel(entry?.details)
        : "";

      const defaultTitle =
        (activityType?.name ? translateActivityLabel(activityType.name) : "") ||
        (entry?.details?.transportMode
          ? translateActivityLabel(entry.details.transportMode)
          : "") ||
        (entry?.details?.source
          ? translateActivityLabel(entry.details.source)
          : "") ||
        (hasDiet ? translateDietLabel(entry.details.diet) : "") ||
        t("dashboard.activity_fallback", { id: entry.id });

      const clothingTitle = purchaseLabel
        ? `${t("taxonomy.categories.clothing")} - ${purchaseLabel}`
        : t("taxonomy.categories.clothing");
      const foodTitle = hasDiet
        ? translateDietLabel(entry.details.diet)
        : t("taxonomy.categories.food");

      const qty = Number(item?.quantity || 0);
      const unit = activityType?.unitLabel || t("dashboard.unit");
      const foodTotalQty = itemIris.reduce((total, iri) => {
        const currentItem = entryItemsByIri.get(iri);
        return total + Number(currentItem?.quantity || 0);
      }, 0);
      const co2 = calcCo2(entry);
      const points = Math.max(0, Math.round(Number(entry?.value || 0)));

      return {
        id: entry.id,
        entryIri: entry?.["@id"] || `/api/entries/${entry.id}`,
        title: isClothingEntry
          ? clothingTitle
          : hasDiet
            ? foodTitle
            : defaultTitle,
        icon: iconForActivity(activityType, hasDiet),
        points: `+${points} n2e points`,
        pointsClass: points > 0 ? "text-main" : "text-grey",
        quantityLabel: hasDiet
          ? foodTotalQty > 0
            ? `${foodTotalQty.toFixed(2)} ${unit}`
            : "-"
          : qty > 0
            ? `${qty.toFixed(2)} ${unit}`
            : "-",
        co2: `${co2 > 0 ? "+" : ""}${co2.toFixed(2)} kg CO2`,
        co2Class: co2 > 2 ? "text-systeme" : "text-main",
        dateLabel: entry?.createdAt
          ? new Date(entry.createdAt).toLocaleString(
              locale.value === "fr" ? "fr-FR" : "en-US",
              {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
              },
            )
          : "-",
      };
    };

    totalCo2.value =
      Math.round(userEntries.reduce((s, e) => s + calcCo2(e), 0) * 10) / 10;

    dataActivities.value = userEntries
      .slice()
      .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
      .map((entry) => toDataActivity(entry));

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

    const sevenDaysAgo = new Date(now);
    sevenDaysAgo.setDate(now.getDate() - 6);
    sevenDaysAgo.setHours(0, 0, 0, 0);

    const previousWeekStart = new Date(startOfWeek);
    previousWeekStart.setDate(startOfWeek.getDate() - 7);

    const activeDayKeys = new Set();
    let previousWeekTotal = 0;
    let clothingTotal = 0;
    let clothingSecondHandTotal = 0;

    for (const entry of userEntries) {
      const createdAt = entry.createdAt ? new Date(entry.createdAt) : null;
      if (createdAt && createdAt >= sevenDaysAgo) {
        activeDayKeys.add(entry.createdAt.slice(0, 10));
      }

      if (
        createdAt &&
        createdAt >= previousWeekStart &&
        createdAt < startOfWeek
      ) {
        previousWeekTotal += calcCo2(entry);
      }

      const entryItemIris = Array.isArray(entry.entryItems)
        ? entry.entryItems
        : [];
      const isClothingEntry = entryItemIris.some((iri) => {
        const item = entryItemsByIri.get(iri);
        const type = activityTypesByIri.get(item?.activityType);
        return isClothingActivityType(type, categoriesByIri);
      });

      if (!isClothingEntry) continue;

      clothingTotal += 1;
      const purchaseLabel = normalizeText(
        resolveClothingPurchaseLabel(entry.details),
      );
      if (purchaseLabel.includes("second")) {
        clothingSecondHandTotal += 1;
      }
    }

    activeDaysLast7.value = activeDayKeys.size;
    clothingEntriesCount.value = clothingTotal;
    clothingSecondHandCount.value = clothingSecondHandTotal;
    weeklyTrendPercent.value =
      previousWeekTotal > 0
        ? Math.round(
            ((weeklyCo2.value - previousWeekTotal) / previousWeekTotal) * 100,
          )
        : null;

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
  } finally {
    if (showLoader) {
      isInitialLoading.value = false;
    }
  }
};

onMounted(() => {
  loadData({ showLoader: true });
});

watch(locale, () => {
  loadData();
});

const requestDeleteActivity = (activity) => {
  if (!activity?.id) return;
  activityToDelete.value = activity;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = (force = false) => {
  if (!force && isDeleteInProgress.value) return;
  isDeleteModalOpen.value = false;
  activityToDelete.value = null;
};

const confirmDeleteActivity = async () => {
  const activity = activityToDelete.value;
  if (!activity?.entryIri || !activity?.id) return;

  const token = normalizeToken(localStorage.getItem("jwt_token"));
  if (!token) {
    router.push("/login");
    return;
  }

  deletingActivityIds.value = [...deletingActivityIds.value, activity.id];

  try {
    await axios.delete(`http://localhost:8000${activity.entryIri}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    closeDeleteModal(true);
    await loadData();
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    console.error("Data activity delete error:", error);
  } finally {
    deletingActivityIds.value = deletingActivityIds.value.filter(
      (id) => id !== activity.id,
    );
  }
};

// ─── Computed UI ──────────────────────────────────────────────────────────
const totalPointsLabel = computed(() => `+${totalPoints.value} pts`);
const weeklyCo2Label = computed(() => `+ ${weeklyCo2.value} kg`);
const weeklyTrendLabel = computed(() => {
  if (weeklyTrendPercent.value === null) {
    return t("data.challenges.no_previous_week");
  }

  const value = Math.abs(weeklyTrendPercent.value);
  return weeklyTrendPercent.value <= 0
    ? t("data.challenges.trend_down", { value })
    : t("data.challenges.trend_up", { value });
});
const communityFriendsLabel = computed(() =>
  t("data.community_friends", { count: friendsCount.value }),
);
const currentData = computed(
  () => progressionByPeriod.value[activePeriod.value],
);
const hasProgressionData = computed(() =>
  currentData.value.values.some((value) => Number(value) > 0),
);
const hasPieData = computed(() => {
  const cat = co2ByCategory.value;
  return cat.transport + cat.food + cat.clothing + cat.consumption > 0;
});

const getTitle = (id) => {
  const labels = {
    weekly: t("data.weekly_comparison"),
    progression: t("data.progression_details"),
    challenges: t("data.challenges_achievement"),
    split: t("data.co2_split"),
  };
  return labels[id];
};

const challenges = computed(() => {
  const activeDaysGoal = 4;
  const weeklyGoal = Math.max(1, dailyTargetKg.value * 7);
  const secondHandGoal = 60;

  const secondHandShare =
    clothingEntriesCount.value > 0
      ? Math.round(
          (clothingSecondHandCount.value / clothingEntriesCount.value) * 100,
        )
      : 0;

  return [
    {
      title: t("data.challenges.active_days_title"),
      subtitle: t("data.challenges.active_days_subtitle"),
      icon: "ph:calendar-check-bold",
      progress: Math.min(
        100,
        Math.round((activeDaysLast7.value / activeDaysGoal) * 100),
      ),
      currentLabel: t("data.challenges.active_days_current", {
        current: activeDaysLast7.value,
        goal: activeDaysGoal,
      }),
      goalLabel: t("data.challenges.active_days_goal", {
        goal: activeDaysGoal,
      }),
    },
    {
      title: t("data.challenges.weekly_target_title"),
      subtitle: t("data.challenges.weekly_target_subtitle"),
      icon: "ph:trend-down-bold",
      progress:
        weeklyCo2.value <= weeklyGoal
          ? 100
          : Math.max(
              0,
              Math.round(
                100 - ((weeklyCo2.value - weeklyGoal) / weeklyGoal) * 100,
              ),
            ),
      currentLabel: t("data.challenges.weekly_target_current", {
        current: weeklyCo2.value.toFixed(1),
        goal: weeklyGoal.toFixed(1),
      }),
      goalLabel: t("data.challenges.weekly_target_goal"),
    },
    {
      title: t("data.challenges.second_hand_title"),
      subtitle: t("data.challenges.second_hand_subtitle"),
      icon: "ph:tag-bold",
      progress: Math.min(
        100,
        Math.round((secondHandShare / secondHandGoal) * 100),
      ),
      currentLabel: t("data.challenges.second_hand_current", {
        current: secondHandShare,
        goal: secondHandGoal,
      }),
      goalLabel: t("data.challenges.second_hand_goal", {
        goal: secondHandGoal,
      }),
    },
  ];
});

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
.font-title {
  font-family: var(--font-title);
}
.font-ui {
  font-family: var(--font-ui);
}
.text-main {
  color: var(--color-main);
}
.text-grey {
  color: var(--color-grey);
}

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
