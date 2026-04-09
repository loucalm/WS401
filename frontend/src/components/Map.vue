<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-0 shadow-[0_0_0_1px_rgba(0,0,0,0.04)] relative overflow-hidden"
    >
      <div
        class="sticky top-0 z-40 flex items-center justify-center bg-white px-6 py-4 shadow-sm border-b border-grey/5"
      >
        <h1
          class="text-title-h4 text-center text-black uppercase tracking-tight font-title"
        >
          {{ t("map.title") }}
        </h1>

        <button
          type="button"
          class="absolute right-6 flex h-12 w-12 items-center justify-center rounded-2xl transition-all active:scale-90 shadow-sm"
          :class="
            showLeaderboard ? 'bg-main text-white' : 'bg-main-light text-main'
          "
          @click="showLeaderboard = !showLeaderboard"
        >
          <Icon
            :icon="
              showLeaderboard ? 'ph:users-three-fill' : 'ph:users-three-bold'
            "
            class="h-7 w-7"
          />
        </button>
      </div>

      <Transition name="slide-down">
        <div
          v-if="showLeaderboard"
          class="absolute inset-x-0 top-18.25 z-30 bg-white border-b border-grey/10 shadow-2xl max-h-[60vh] overflow-y-auto rounded-b-4xl"
        >
          <div class="p-6 space-y-4">
            <p
              class="text-body-12 text-grey font-bold uppercase tracking-widest px-2"
            >
              {{ t("map.active_friends_online") }}
            </p>
            <div class="space-y-3">
              <article
                v-for="friend in leaderboardUsers"
                :key="friend.id"
                @click="focusFriend(friend)"
                class="flex items-center gap-4 rounded-2xl border border-grey/10 bg-main-light/30 px-4 py-3 transition-transform"
                :class="
                  friend.hasLocation
                    ? 'active:scale-[0.98] cursor-pointer'
                    : 'cursor-default opacity-90'
                "
              >
                <div class="relative h-12 w-12 shrink-0">
                  <img
                    :src="friend.avatar"
                    class="h-full w-full rounded-full object-cover border-2 border-white shadow-sm"
                  />
                  <div
                    v-if="friend.online"
                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full bg-main border-2 border-white"
                  ></div>
                </div>
                <div class="flex-1">
                  <p class="font-bold text-black text-body-16 leading-tight">
                    {{ friend.name }}
                  </p>
                  <p class="text-body-12 text-grey font-medium uppercase">
                    {{
                      t("map.rank_place", {
                        rank: friend.rank,
                        total: friend.total,
                      })
                    }}
                  </p>
                </div>
                <p class="font-title text-main font-bold text-body-16">
                  {{ t("map.points_value", { points: friend.points }) }}
                </p>
              </article>
            </div>
          </div>
        </div>
      </Transition>

      <div
        ref="mapViewportRef"
        class="flex-1 relative bg-main-light/50 overflow-hidden touch-none"
        @mousedown="startDragging"
        @mousemove="drag"
        @mouseup="stopDragging"
        @mouseleave="stopDragging"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="stopDragging"
      >
        <div
          class="absolute inset-0 flex items-center justify-center will-change-transform"
          :class="
            cameraAnimating && !isDragging
              ? 'transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]'
              : ''
          "
          :style="{
            transform: `translate(${translateX}px, ${translateY}px) scale(${zoomLevel})`,
          }"
        >
          <div class="relative inline-block">
            <img
              src="@/assets/img/svg/cartefrance.svg"
              alt="France Map"
              class="max-h-[80vh] w-auto pointer-events-none drop-shadow-2xl"
            />

            <div
              v-for="friend in friends"
              :key="friend.id"
              :data-user-id="friend.id"
              :style="{
                position: 'absolute',
                top: friend.position.top,
                left: friend.position.left,
                transform: `translate(-50%, -50%) scale(${Math.max(0.5, 1 / (zoomLevel * 0.8))})`,
              }"
              class="h-14 w-14 rounded-full border-4 border-white shadow-xl cursor-pointer active:scale-95 transition-all z-10"
              @click.stop="focusFriend(friend)"
            >
              <img
                :src="friend.avatar"
                :alt="friend.name"
                class="h-full w-full rounded-full object-cover"
              />
              <div
                v-if="friend.online"
                class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-main border-2 border-white animate-pulse"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="absolute bottom-28 right-6 flex flex-col gap-3 z-20 sm:bottom-32"
        >
          <button
            class="h-14 w-14 bg-main text-white rounded-2xl shadow-xl flex items-center justify-center active:scale-90 border border-main/10"
            :title="t('map.recenter')"
            :aria-label="t('map.recenter')"
            @click="recenterOnCurrentUser"
          >
            <Icon icon="ph:crosshair-simple-bold" class="h-7 w-7" />
          </button>
          <button
            class="h-14 w-14 bg-white text-main rounded-2xl shadow-xl flex items-center justify-center font-bold text-2xl active:scale-90 border border-grey/5"
            @click="zoomIn"
          >
            <Icon icon="tabler:plus" class="h-7 w-7" />
          </button>
          <button
            class="h-14 w-14 bg-white text-main rounded-2xl shadow-xl flex items-center justify-center font-bold text-2xl active:scale-90 border border-grey/5"
            @click="zoomOut"
          >
            <Icon icon="tabler:minus" class="h-7 w-7" />
          </button>
        </div>

        <Transition name="fade">
          <div
            v-if="showPopup"
            class="absolute inset-x-6 bottom-28 z-30 rounded-4xl bg-white p-5 shadow-2xl border border-main/10 flex items-center gap-5 animate-zoom sm:bottom-32"
          >
            <div
              class="h-20 w-20 rounded-full border-4 border-main-light overflow-hidden shrink-0 shadow-sm"
            >
              <img
                :src="selectedUser.avatar"
                class="h-full w-full object-cover"
              />
            </div>
            <div class="flex-1 text-black text-left">
              <h3 class="font-title text-title-h4 uppercase leading-none">
                {{ selectedUser.name }}
              </h3>
              <p class="text-main font-bold text-body-16 mt-1">
                {{ t("map.points_value", { points: selectedUser.points }) }}
              </p>
              <p
                class="text-grey text-body-12 uppercase font-bold mt-1 tracking-tighter"
              >
                {{
                  t("map.rank_label", {
                    rank: selectedUser.rank,
                    total: selectedUser.total,
                  })
                }}
              </p>
            </div>
            <button
              @click="showPopup = false"
              class="bg-grey/10 p-2 rounded-full text-grey active:scale-75 transition-transform"
            >
              <Icon icon="ph:x-bold" />
            </button>
          </div>
        </Transition>
      </div>

      <BottomNav active="map" />
    </main>
  </div>
</template>

<script setup>
import { nextTick, ref } from "vue";
import { Icon } from "@iconify/vue";
import { useI18n } from "vue-i18n";
import axios from "axios";
import { onBeforeUnmount, onMounted } from "vue";
import { useRouter } from "vue-router";
import BottomNav from "./BottomNav.vue";
import { resolveProfilePictureSrc } from "../utils/profilePictures";

const { t } = useI18n();
const router = useRouter();
const API_BASE = "http://localhost:8000/api";

const zoomLevel = ref(1.2);
const translateX = ref(0);
const translateY = ref(0);
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const showLeaderboard = ref(false);
const showPopup = ref(false);
const selectedUser = ref({});
const mapViewportRef = ref(null);
const cameraAnimating = ref(false);
let refreshIntervalId = null;

const friends = ref([]);
const leaderboardUsers = ref([]);

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

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const toFranceMapPosition = (lat, lon) => {
  const minLat = 41.0;
  const maxLat = 51.5;
  const minLon = -5.5;
  const maxLon = 9.8;

  if (
    Number.isNaN(lat) ||
    Number.isNaN(lon) ||
    lat < minLat ||
    lat > maxLat ||
    lon < minLon ||
    lon > maxLon
  ) {
    return null;
  }

  const top = ((maxLat - lat) / (maxLat - minLat)) * 100;
  const left = ((lon - minLon) / (maxLon - minLon)) * 100;

  return {
    top: `${clamp(top, 8, 92)}%`,
    left: `${clamp(left, 8, 92)}%`,
  };
};

const loadFriendsFromApi = async () => {
  const token = normalizeToken(localStorage.getItem("jwt_token"));
  if (!token) {
    router.push("/login");
    return;
  }

  const payload = parseJwtPayload(token);
  const nowInSeconds = Math.floor(Date.now() / 1000);
  if (!payload?.exp || payload.exp <= nowInSeconds) {
    localStorage.removeItem("jwt_token");
    router.push("/login");
    return;
  }
  const currentEmail = payload?.email || payload?.username || "";

  const headers = {
    Authorization: `Bearer ${token}`,
    Accept: "application/ld+json",
  };

  try {
    const leaderboardResponse = await axios.get(
      `${API_BASE}/leaderboard?scope=friends&period=daily`,
      { headers },
    );

    const rankedUsers = Array.isArray(leaderboardResponse.data?.users)
      ? leaderboardResponse.data.users
      : [];
    const totalRanked = Number(
      leaderboardResponse.data?.totalUsers || rankedUsers.length,
    );

    leaderboardUsers.value = rankedUsers.map((user) => {
      const position = toFranceMapPosition(user.latitude, user.longitude);
      return {
        id: user.id,
        name: user.name,
        rank: user.rank || "-",
        total: totalRanked,
        points: Number(user.points || 0),
        online: Boolean(user.isCurrentUser),
        avatar: resolveProfilePictureSrc(user.profilePicture),
        hasLocation: Boolean(position),
        position,
      };
    });

    const usersWithLocation = leaderboardUsers.value
      .filter((user) => user.hasLocation)
      .filter(Boolean);

    if (usersWithLocation.length === 0) {
      friends.value = [];
      if (showPopup.value && selectedUser.value?.id) {
        const refreshedSelected = leaderboardUsers.value.find(
          (user) => user.id === selectedUser.value.id,
        );
        if (refreshedSelected) {
          selectedUser.value = refreshedSelected;
        } else {
          showPopup.value = false;
        }
      }
      return;
    }

    if (showPopup.value && selectedUser.value?.id) {
      const refreshedSelected = leaderboardUsers.value.find(
        (user) => user.id === selectedUser.value.id,
      );
      if (refreshedSelected) {
        selectedUser.value = refreshedSelected;
      } else {
        showPopup.value = false;
      }
    }

    friends.value = usersWithLocation;
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
    }
  }
};

const initiateDrag = (clientX, clientY) => {
  cameraAnimating.value = false;
  isDragging.value = true;
  dragStart.value = {
    x: clientX - translateX.value,
    y: clientY - translateY.value,
  };
};

const processMove = (clientX, clientY) => {
  if (!isDragging.value) return;
  translateX.value = clientX - dragStart.value.x;
  translateY.value = clientY - dragStart.value.y;
};

const startDragging = (e) => initiateDrag(e.clientX, e.clientY);
const drag = (e) => processMove(e.clientX, e.clientY);

const handleTouchStart = (e) => {
  if (e.touches.length === 1)
    initiateDrag(e.touches[0].clientX, e.touches[0].clientY);
};
const handleTouchMove = (e) => {
  if (e.touches.length === 1)
    processMove(e.touches[0].clientX, e.touches[0].clientY);
};
const stopDragging = () => (isDragging.value = false);

const zoomIn = () => (zoomLevel.value = Math.min(zoomLevel.value + 0.4, 4));
const zoomOut = () => (zoomLevel.value = Math.max(zoomLevel.value - 0.4, 0.6));

const selectFriend = (friend) => {
  selectedUser.value = friend;
  showPopup.value = true;
};

const centerMarkerByUserId = (userId) => {
  const viewportEl = mapViewportRef.value;
  if (!viewportEl) return false;

  const markerEl = viewportEl.querySelector(`[data-user-id="${userId}"]`);
  if (!markerEl) return false;

  const viewportRect = viewportEl.getBoundingClientRect();
  const markerRect = markerEl.getBoundingClientRect();

  const viewportCenterX = viewportRect.left + viewportRect.width / 2;
  const viewportCenterY = viewportRect.top + viewportRect.height / 2;
  const markerCenterX = markerRect.left + markerRect.width / 2;
  const markerCenterY = markerRect.top + markerRect.height / 2;

  translateX.value += viewportCenterX - markerCenterX;
  translateY.value += viewportCenterY - markerCenterY;
  return true;
};

const centerOnFriend = async (friend, showDetails = true) => {
  if (!friend) return;
  if (showDetails) selectFriend(friend);
  showLeaderboard.value = false;
  cameraAnimating.value = true;

  const targetZoom = Math.min(2.2, 4);
  zoomLevel.value = Math.max(zoomLevel.value, targetZoom);

  await nextTick();
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      if (!centerMarkerByUserId(friend.id)) {
        translateX.value = 0;
        translateY.value = 0;
      }

      setTimeout(() => {
        cameraAnimating.value = false;
      }, 520);
    });
  });
};

const focusFriend = (friend) => {
  if (!friend?.hasLocation) {
    selectFriend(friend);
    showLeaderboard.value = false;
    return;
  }
  centerOnFriend(friend, true);
};

const recenterOnCurrentUser = () => {
  const currentUser = friends.value.find((friend) => friend.online);
  if (!currentUser) {
    translateX.value = 0;
    translateY.value = 0;
    zoomLevel.value = 1.2;
    return;
  }

  zoomLevel.value = Math.max(zoomLevel.value, 1.2);
  centerOnFriend(currentUser, false);
};

onMounted(async () => {
  await loadFriendsFromApi();
  refreshIntervalId = setInterval(() => {
    loadFriendsFromApi();
  }, 5000);
});

onBeforeUnmount(() => {
  if (refreshIntervalId) clearInterval(refreshIntervalId);
});
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-down-enter-from,
.slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.font-title {
  font-family: var(--font-title);
}
.text-main {
  color: var(--color-main);
}
.bg-main {
  background-color: var(--color-main);
}
.bg-main-light {
  background-color: var(--color-main-light);
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
