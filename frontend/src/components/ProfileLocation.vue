<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-125 flex-col bg-white pb-24"
    >
      <header class="flex items-center gap-3 px-6 pb-4 pt-6">
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-full bg-main-light text-main"
          @click="router.push('/profile')"
        >
          <Icon icon="ph:arrow-left" class="h-5 w-5" />
        </button>
        <h1 class="font-title text-[30px] leading-none text-black">
          {{ t("profile.location") }}
        </h1>
      </header>

      <section class="px-6">
        <div
          class="rounded-2xl border border-grey/15 bg-white p-4 shadow-[0_4px_12px_rgba(0,0,0,0.08)]"
        >
          <p class="font-ui text-body-16 font-semibold text-black">
            Position actuelle
          </p>
          <p class="mt-1 font-ui text-body-12 text-grey">
            Définis ta position pour l'afficher sur la carte.
          </p>

          <div class="mt-4 grid grid-cols-2 gap-3">
            <label class="block">
              <span class="font-ui text-body-12 text-grey">Latitude</span>
              <input
                v-model.number="latitude"
                type="number"
                step="0.000001"
                min="-90"
                max="90"
                class="mt-1 w-full rounded-xl border border-grey/20 bg-white px-3 py-2 font-ui text-body-16 text-black"
              />
            </label>

            <label class="block">
              <span class="font-ui text-body-12 text-grey">Longitude</span>
              <input
                v-model.number="longitude"
                type="number"
                step="0.000001"
                min="-180"
                max="180"
                class="mt-1 w-full rounded-xl border border-grey/20 bg-white px-3 py-2 font-ui text-body-16 text-black"
              />
            </label>
          </div>

          <div class="mt-4 flex gap-3">
            <button
              type="button"
              class="rounded-xl bg-main px-4 py-2 font-ui text-body-12 font-semibold text-white"
              @click="useBrowserLocation"
            >
              Utiliser ma position
            </button>

            <button
              type="button"
              class="rounded-xl border border-main px-4 py-2 font-ui text-body-12 font-semibold text-main"
              :disabled="isSaving"
              @click="saveLocation"
            >
              {{ isSaving ? "Enregistrement..." : "Enregistrer" }}
            </button>
          </div>

          <p
            v-if="feedbackMessage"
            class="mt-3 font-ui text-body-12"
            :class="feedbackClass"
          >
            {{ feedbackMessage }}
          </p>
        </div>
      </section>

      <section class="px-6 pb-4 pt-5">
        <div
          class="overflow-hidden rounded-2xl border border-grey/15 shadow-[0_4px_12px_rgba(0,0,0,0.08)]"
        >
          <div
            ref="mapElement"
            class="relative flex h-90 w-full cursor-crosshair select-none items-center justify-center bg-main-light/25"
            @click="onMapClick"
          >
            <img
              ref="mapImage"
              src="@/assets/img/svg/cartefrance.svg"
              alt="Carte de France"
              class="pointer-events-none h-full w-auto max-w-full"
            />

            <div
              v-if="markerPosition"
              class="absolute z-10 h-4 w-4 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white bg-main shadow"
              :style="{ top: markerPosition.top, left: markerPosition.left }"
            ></div>
          </div>
        </div>
      </section>

      <BottomNav active="profile" />
    </main>
  </div>
</template>

<script setup>
import { Icon } from "@iconify/vue";
import axios from "axios";
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import BottomNav from "./BottomNav.vue";

const API_BASE = "http://localhost:8000/api";
const router = useRouter();
const { t } = useI18n();

const latitude = ref(48.8566);
const longitude = ref(2.3522);
const currentUserIri = ref("");
const isSaving = ref(false);
const feedbackMessage = ref("");
const feedbackClass = ref("text-grey");
const mapElement = ref(null);
const mapImage = ref(null);

const FRANCE_MIN_LAT = 41.0;
const FRANCE_MAX_LAT = 51.5;
const FRANCE_MIN_LON = -5.5;
const FRANCE_MAX_LON = 9.8;

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

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

const authHeaders = computed(() => {
  const token = normalizeToken(localStorage.getItem("jwt_token"));
  return {
    Authorization: `Bearer ${token}`,
    Accept: "application/ld+json",
  };
});

const markerPosition = computed(() => {
  const lat = Number(latitude.value);
  const lon = Number(longitude.value);

  if (Number.isNaN(lat) || Number.isNaN(lon)) return null;
  if (lat < FRANCE_MIN_LAT || lat > FRANCE_MAX_LAT) return null;
  if (lon < FRANCE_MIN_LON || lon > FRANCE_MAX_LON) return null;

  const topPercent =
    ((FRANCE_MAX_LAT - lat) / (FRANCE_MAX_LAT - FRANCE_MIN_LAT)) * 100;
  const leftPercent =
    ((lon - FRANCE_MIN_LON) / (FRANCE_MAX_LON - FRANCE_MIN_LON)) * 100;

  return {
    top: `${clamp(topPercent, 0, 100)}%`,
    left: `${clamp(leftPercent, 0, 100)}%`,
  };
});

const onMapClick = (event) => {
  if (!mapElement.value || !mapImage.value) return;

  const imageRect = mapImage.value.getBoundingClientRect();
  if (!imageRect.width || !imageRect.height) return;

  if (
    event.clientX < imageRect.left ||
    event.clientX > imageRect.right ||
    event.clientY < imageRect.top ||
    event.clientY > imageRect.bottom
  ) {
    return;
  }

  const xRatio = clamp(
    (event.clientX - imageRect.left) / imageRect.width,
    0,
    1,
  );
  const yRatio = clamp(
    (event.clientY - imageRect.top) / imageRect.height,
    0,
    1,
  );

  const computedLon =
    FRANCE_MIN_LON + xRatio * (FRANCE_MAX_LON - FRANCE_MIN_LON);
  const computedLat =
    FRANCE_MAX_LAT - yRatio * (FRANCE_MAX_LAT - FRANCE_MIN_LAT);

  latitude.value = Number(computedLat.toFixed(6));
  longitude.value = Number(computedLon.toFixed(6));
  feedbackMessage.value = "Position sélectionnée sur la carte.";
  feedbackClass.value = "text-main";
};

const loadUserLocation = async () => {
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

  try {
    const users = await fetchAll("users", authHeaders.value);
    const currentUser = users.find((user) => user.email === currentEmail);

    if (!currentUser?.["@id"]) {
      feedbackMessage.value = "Utilisateur introuvable.";
      feedbackClass.value = "text-systeme";
      return;
    }

    currentUserIri.value = currentUser["@id"];

    if (typeof currentUser.latitude === "number")
      latitude.value = currentUser.latitude;
    if (typeof currentUser.longitude === "number")
      longitude.value = currentUser.longitude;
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    feedbackMessage.value = "Erreur lors du chargement de la position.";
    feedbackClass.value = "text-systeme";
  }
};

const useBrowserLocation = () => {
  if (!navigator.geolocation) {
    feedbackMessage.value =
      "La géolocalisation n'est pas disponible sur ce navigateur.";
    feedbackClass.value = "text-systeme";
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      latitude.value = Number(position.coords.latitude.toFixed(6));
      longitude.value = Number(position.coords.longitude.toFixed(6));
      feedbackMessage.value = "Position détectée avec succès.";
      feedbackClass.value = "text-main";
    },
    () => {
      feedbackMessage.value = "Impossible de récupérer votre position.";
      feedbackClass.value = "text-systeme";
    },
    { enableHighAccuracy: true, timeout: 10000 },
  );
};

const saveLocation = async () => {
  if (!currentUserIri.value) return;

  const lat = Number(latitude.value);
  const lon = Number(longitude.value);

  if (
    Number.isNaN(lat) ||
    lat < -90 ||
    lat > 90 ||
    Number.isNaN(lon) ||
    lon < -180 ||
    lon > 180
  ) {
    feedbackMessage.value = "Latitude/Longitude invalides.";
    feedbackClass.value = "text-systeme";
    return;
  }

  isSaving.value = true;
  try {
    await axios.patch(
      `http://localhost:8000${currentUserIri.value}`,
      { latitude: lat, longitude: lon },
      {
        headers: {
          ...authHeaders.value,
          "Content-Type": "application/merge-patch+json",
        },
      },
    );

    feedbackMessage.value = "Position enregistrée.";
    feedbackClass.value = "text-main";
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    feedbackMessage.value = "Erreur lors de l'enregistrement.";
    feedbackClass.value = "text-systeme";
  } finally {
    isSaving.value = false;
  }
};

onMounted(loadUserLocation);
</script>
