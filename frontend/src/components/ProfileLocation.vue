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
            {{ t("profile_location.current_position") }}
          </p>
          <p class="mt-1 font-ui text-body-12 text-grey">
            {{ t("profile_location.instructions") }}
          </p>

          <div class="mt-4 grid grid-cols-2 gap-3">
            <label class="block">
              <span class="font-ui text-body-12 text-grey">{{
                t("profile_location.latitude")
              }}</span>
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
              <span class="font-ui text-body-12 text-grey">{{
                t("profile_location.longitude")
              }}</span>
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
              :disabled="isSaving"
              @click="saveLocation"
            >
              {{
                isSaving
                  ? t("profile_location.saving")
                  : t("profile_location.save")
              }}
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
            <div
              class="location-map h-full w-full"
              :aria-label="t('profile_location.map_alt')"
              v-html="mapSvg"
            ></div>

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
import mapSvg from "@/assets/img/svg/cartefrance.svg?raw";

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

const FRANCE_MIN_LAT = 41.0;
const FRANCE_MAX_LAT = 51.5;
const FRANCE_MIN_LON = -5.5;
const FRANCE_MAX_LON = 9.8;
const SVG_WIDTH = 1000;
const SVG_HEIGHT = 960;

const CALIBRATION_POINTS = [
  { lat: 41.85199361188721, lon: -4.398182747852199, x: 90.9, y: 876.3 },
  { lat: 45.74064508013562, lon: 2.9480086609209915, x: 545.5, y: 542.7 },
  { lat: 50.601459415446136, lon: 8.824961787939543, x: 909.1, y: 91.3 },
];

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const solve3x3 = (matrix, values) => {
  const augmented = matrix.map((row, index) => [...row, values[index]]);

  for (let pivotIndex = 0; pivotIndex < 3; pivotIndex += 1) {
    let maxRow = pivotIndex;
    for (let rowIndex = pivotIndex + 1; rowIndex < 3; rowIndex += 1) {
      if (
        Math.abs(augmented[rowIndex][pivotIndex]) >
        Math.abs(augmented[maxRow][pivotIndex])
      ) {
        maxRow = rowIndex;
      }
    }

    [augmented[pivotIndex], augmented[maxRow]] = [
      augmented[maxRow],
      augmented[pivotIndex],
    ];

    const pivot = augmented[pivotIndex][pivotIndex];
    if (Math.abs(pivot) < 1e-12) return null;

    for (let columnIndex = pivotIndex; columnIndex < 4; columnIndex += 1) {
      augmented[pivotIndex][columnIndex] /= pivot;
    }

    for (let rowIndex = 0; rowIndex < 3; rowIndex += 1) {
      if (rowIndex === pivotIndex) continue;
      const factor = augmented[rowIndex][pivotIndex];
      for (let columnIndex = pivotIndex; columnIndex < 4; columnIndex += 1) {
        augmented[rowIndex][columnIndex] -=
          factor * augmented[pivotIndex][columnIndex];
      }
    }
  }

  return augmented.map((row) => row[3]);
};

const buildProjection = () => {
  const sourceMatrix = CALIBRATION_POINTS.map((point) => [
    point.lon,
    point.lat,
    1,
  ]);
  const xCoefficients = solve3x3(
    sourceMatrix,
    CALIBRATION_POINTS.map((point) => point.x),
  );
  const yCoefficients = solve3x3(
    sourceMatrix,
    CALIBRATION_POINTS.map((point) => point.y),
  );

  if (!xCoefficients || !yCoefficients) return null;

  return {
    toSvg: (latValue, lonValue) => ({
      x:
        xCoefficients[0] * lonValue +
        xCoefficients[1] * latValue +
        xCoefficients[2],
      y:
        yCoefficients[0] * lonValue +
        yCoefficients[1] * latValue +
        yCoefficients[2],
    }),
    toGeo: (xValue, yValue) => {
      const determinant =
        xCoefficients[0] * yCoefficients[1] -
        xCoefficients[1] * yCoefficients[0];

      if (Math.abs(determinant) < 1e-12) return null;

      const lonValue =
        ((xValue - xCoefficients[2]) * yCoefficients[1] -
          xCoefficients[1] * (yValue - yCoefficients[2])) /
        determinant;
      const latValue =
        (xCoefficients[0] * (yValue - yCoefficients[2]) -
          (xValue - xCoefficients[2]) * yCoefficients[0]) /
        determinant;

      return { lat: latValue, lon: lonValue };
    },
  };
};

const projection = buildProjection();

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
  if (!projection) return null;

  const lat = Number(latitude.value);
  const lon = Number(longitude.value);

  if (Number.isNaN(lat) || Number.isNaN(lon)) return null;
  if (lat < FRANCE_MIN_LAT || lat > FRANCE_MAX_LAT) return null;
  if (lon < FRANCE_MIN_LON || lon > FRANCE_MAX_LON) return null;

  const svgPoint = projection.toSvg(lat, lon);
  if (!svgPoint) return null;

  return {
    top: `${clamp((svgPoint.y / SVG_HEIGHT) * 100, 0, 100)}%`,
    left: `${clamp((svgPoint.x / SVG_WIDTH) * 100, 0, 100)}%`,
  };
});

const onMapClick = (event) => {
  if (!mapElement.value || !projection) return;

  const clickedPath = event.target?.closest?.("path");
  if (!clickedPath || !mapElement.value.contains(clickedPath)) return;

  const svgElement = mapElement.value.querySelector("svg");
  if (!svgElement) return;

  const svgRect = svgElement.getBoundingClientRect();
  if (!svgRect.width || !svgRect.height) return;

  if (
    event.clientX < svgRect.left ||
    event.clientX > svgRect.right ||
    event.clientY < svgRect.top ||
    event.clientY > svgRect.bottom
  ) {
    return;
  }

  const xRatio = clamp((event.clientX - svgRect.left) / svgRect.width, 0, 1);
  const yRatio = clamp((event.clientY - svgRect.top) / svgRect.height, 0, 1);

  const computedLon =
    FRANCE_MIN_LON + xRatio * (FRANCE_MAX_LON - FRANCE_MIN_LON);
  const computedLat =
    FRANCE_MAX_LAT - yRatio * (FRANCE_MAX_LAT - FRANCE_MIN_LAT);

  const svgX = clamp(xRatio * SVG_WIDTH, 0, SVG_WIDTH);
  const svgY = clamp(yRatio * SVG_HEIGHT, 0, SVG_HEIGHT);
  const geoPoint = projection.toGeo(svgX, svgY);

  latitude.value = Number((geoPoint?.lat ?? computedLat).toFixed(6));
  longitude.value = Number((geoPoint?.lon ?? computedLon).toFixed(6));
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
      feedbackMessage.value = t("profile_location.feedback.user_not_found");
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

    feedbackMessage.value = t("profile_location.feedback.load_error");
    feedbackClass.value = "text-systeme";
  }
};

const useBrowserLocation = () => {
  if (!navigator.geolocation) {
    feedbackMessage.value = t(
      "profile_location.feedback.geolocation_unavailable",
    );
    feedbackClass.value = "text-systeme";
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      latitude.value = Number(position.coords.latitude.toFixed(6));
      longitude.value = Number(position.coords.longitude.toFixed(6));
      feedbackMessage.value = t("profile_location.feedback.position_detected");
      feedbackClass.value = "text-main";
    },
    () => {
      feedbackMessage.value = t("profile_location.feedback.position_error");
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
    feedbackMessage.value = t("profile_location.feedback.invalid_coordinates");
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

    feedbackMessage.value = t("profile_location.feedback.saved");
    feedbackClass.value = "text-main";
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    feedbackMessage.value = t("profile_location.feedback.save_error");
    feedbackClass.value = "text-systeme";
  } finally {
    isSaving.value = false;
  }
};

onMounted(loadUserLocation);
</script>

<style scoped>
.location-map :deep(svg) {
  display: block;
  height: 100%;
  width: auto;
  max-width: 100%;
  margin: 0 auto;
}

.location-map :deep(path) {
  cursor: crosshair;
}

.location-map :deep(g#label_points),
.location-map :deep(g#points) {
  pointer-events: none;
}
</style>
