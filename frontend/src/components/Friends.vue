<template>
  <div class="min-h-screen bg-white text-black font-ui">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-24"
    >
      <header
        class="sticky top-0 z-20 border-b border-grey/10 bg-white px-6 py-4 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <button
              type="button"
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-main-light text-main"
              @click="router.push('/profile')"
            >
              <Icon icon="ph:arrow-left-bold" class="h-5 w-5" />
            </button>
            <h1
              class="font-title text-title-h4 uppercase tracking-tight text-black"
            >
              {{ t("friends_page.title") }}
            </h1>
          </div>

          <button
            type="button"
            class="rounded-xl bg-main px-3 py-2 text-body-12 font-bold uppercase tracking-wide text-white"
            @click="loadFriendsPageData"
          >
            {{ t("friends_page.refresh") }}
          </button>
        </div>

        <div
          class="mt-4 flex items-center gap-2 rounded-2xl border border-grey/15 bg-main-light/35 px-3 py-2"
        >
          <Icon icon="ph:magnifying-glass" class="h-5 w-5 text-grey" />
          <input
            v-model.trim="searchTerm"
            type="text"
            class="w-full bg-transparent text-[14px] text-black outline-none placeholder:text-grey"
            :placeholder="t('friends_page.search')"
          />
        </div>
      </header>

      <section class="flex-1 px-6 py-4">
        <p
          v-if="feedbackMessage"
          class="mb-4 rounded-xl border border-main/15 bg-main-light/35 px-3 py-2 text-[13px] text-main"
        >
          {{ feedbackMessage }}
        </p>

        <p
          v-if="loading"
          class="rounded-xl border border-grey/15 bg-white px-4 py-3 text-[14px] text-grey shadow-[0_4px_12px_rgba(0,0,0,0.06)]"
        >
          {{ t("friends_page.loading") }}
        </p>

        <div v-else class="space-y-6">
          <div class="space-y-2">
            <p
              class="px-1 text-body-12 font-bold uppercase tracking-wider text-grey"
            >
              {{ t("friends_page.received_requests") }}
            </p>

            <p
              v-if="incomingPendingRequests.length === 0"
              class="rounded-xl border border-grey/15 bg-white px-4 py-3 text-[14px] text-grey shadow-[0_4px_12px_rgba(0,0,0,0.06)]"
            >
              {{ t("friends_page.no_received_requests") }}
            </p>

            <div v-else class="space-y-3">
              <article
                v-for="request in incomingPendingRequests"
                :key="request.id"
                class="flex items-center gap-3 rounded-2xl border border-grey/15 bg-main-light/35 px-3 py-3"
              >
                <div
                  class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-main"
                >
                  <Icon icon="ph:user-circle" class="h-6 w-6" />
                </div>

                <div class="min-w-0 flex-1">
                  <p class="truncate text-[15px] font-semibold text-black">
                    {{ request.senderName }}
                  </p>
                  <p class="truncate text-body-12 text-grey">
                    {{ request.senderEmail }}
                  </p>
                </div>

                <button
                  type="button"
                  class="rounded-xl bg-main px-3 py-2 text-body-12 font-bold uppercase tracking-wide text-white active:scale-95"
                  :disabled="acceptingIds.has(request.id)"
                  @click="acceptFriendRequest(request)"
                >
                  {{
                    acceptingIds.has(request.id)
                      ? t("friends_page.accepting")
                      : t("friends_page.accept")
                  }}
                </button>
              </article>
            </div>
          </div>

          <div class="space-y-2">
            <p
              class="px-1 text-body-12 font-bold uppercase tracking-wider text-grey"
            >
              {{ t("friends_page.site_users") }}
            </p>

            <p
              v-if="filteredUsers.length === 0"
              class="rounded-xl border border-grey/15 bg-white px-4 py-3 text-[14px] text-grey shadow-[0_4px_12px_rgba(0,0,0,0.06)]"
            >
              {{ t("friends_page.empty") }}
            </p>

            <div v-else class="space-y-3">
              <article
                v-for="user in filteredUsers"
                :key="user.id"
                class="flex items-center gap-3 rounded-2xl border border-grey/15 bg-white px-3 py-3 shadow-[0_4px_12px_rgba(0,0,0,0.06)]"
              >
                <div
                  class="flex h-11 w-11 items-center justify-center rounded-full bg-main-light text-main"
                >
                  <Icon icon="ph:user" class="h-6 w-6" />
                </div>

                <div class="min-w-0 flex-1">
                  <p class="truncate text-[15px] font-semibold text-black">
                    {{ user.displayName }}
                  </p>
                  <p class="truncate text-body-12 text-grey">
                    {{ user.email }}
                  </p>
                </div>

                <button
                  v-if="friendshipState(user) === 'none'"
                  type="button"
                  class="rounded-xl bg-main px-3 py-2 text-body-12 font-bold uppercase tracking-wide text-white active:scale-95"
                  :disabled="sendingIds.has(user.id)"
                  @click="sendFriendRequest(user)"
                >
                  {{
                    sendingIds.has(user.id)
                      ? t("friends_page.sending")
                      : t("friends_page.add")
                  }}
                </button>

                <span
                  v-else-if="friendshipState(user) === 'pending'"
                  class="rounded-xl bg-[#F4F4F4] px-3 py-2 text-[11px] font-bold uppercase tracking-wide text-grey"
                >
                  {{ t("friends_page.pending") }}
                </span>

                <button
                  v-else
                  type="button"
                  class="rounded-xl bg-red-50 px-3 py-2 text-[11px] font-bold uppercase tracking-wide text-red-500 active:scale-95"
                  :disabled="removingIds.has(user.id)"
                  @click="removeFriend(user)"
                >
                  {{
                    removingIds.has(user.id)
                      ? t("friends_page.removing")
                      : t("friends_page.remove")
                  }}
                </button>
              </article>
            </div>
          </div>
        </div>
      </section>
    </main>

    <BottomNav active="profile" />
  </div>
</template>

<script setup>
import axios from "axios";
import { Icon } from "@iconify/vue";
import { computed, onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import BottomNav from "./BottomNav.vue";

const API_BASE = "http://localhost:8000/api";
const router = useRouter();
const { t } = useI18n();

const loading = ref(true);
const searchTerm = ref("");
const feedbackMessage = ref("");
const users = ref([]);
const friendships = ref([]);
const sendingIds = ref(new Set());
const acceptingIds = ref(new Set());
const removingIds = ref(new Set());
const currentUser = ref(null);

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

const BACKEND_ORIGIN = API_BASE.replace(/\/api$/, "");

const toApiUrl = (iriOrUrl) => {
  if (!iriOrUrl || typeof iriOrUrl !== "string") return "";
  if (iriOrUrl.startsWith("http://") || iriOrUrl.startsWith("https://")) {
    return iriOrUrl;
  }
  return `${BACKEND_ORIGIN}${iriOrUrl.startsWith("/") ? "" : "/"}${iriOrUrl}`;
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

const normalizeName = (value) => (value || "").toString().toLowerCase();

const filteredUsers = computed(() => {
  const term = normalizeName(searchTerm.value);
  if (!term) return users.value;

  return users.value.filter((user) => {
    const byName = normalizeName(user.displayName).includes(term);
    const byEmail = normalizeName(user.email).includes(term);
    return byName || byEmail;
  });
});

const usersByIri = computed(() =>
  users.value.reduce((acc, user) => {
    acc[user.iri] = user;
    return acc;
  }, {}),
);

const incomingPendingRequests = computed(() => {
  const me = currentUser.value?.["@id"];
  if (!me) return [];

  return friendships.value
    .filter(
      (friendship) =>
        friendship?.receiver === me && friendship?.status === "pending",
    )
    .map((friendship) => {
      const sender = usersByIri.value[friendship.sender];
      return {
        id: friendship.id,
        iri: friendship["@id"] || `${API_BASE}/friendships/${friendship.id}`,
        senderName: sender?.displayName || t("friends_page.unknown_user"),
        senderEmail: sender?.email || "-",
      };
    });
});

const getFriendship = (user) => {
  const me = currentUser.value?.["@id"];
  if (!me) return null;
  return (
    friendships.value.find((friendship) => {
      const sender = friendship?.sender;
      const receiver = friendship?.receiver;
      return (
        (sender === me && receiver === user.iri) ||
        (sender === user.iri && receiver === me)
      );
    }) ?? null
  );
};

const friendshipState = (user) => {
  const relation = getFriendship(user);
  if (!relation) return "none";
  if (relation.status === "accepted") return "accepted";
  return "pending";
};

const loadFriendsPageData = async () => {
  loading.value = true;
  feedbackMessage.value = "";

  try {
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

    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: "application/ld+json",
    };

    const [allUsers, allFriendships] = await Promise.all([
      fetchAll("users", headers),
      fetchAll("friendships", headers),
    ]);

    const me = allUsers.find(
      (user) => user.email === (payload.email || payload.username || ""),
    );

    if (!me) {
      feedbackMessage.value = t("friends_page.current_user_missing");
      users.value = [];
      friendships.value = [];
      return;
    }

    currentUser.value = me;
    friendships.value = allFriendships;

    users.value = allUsers
      .filter((user) => user?.["@id"] !== me["@id"])
      .map((user) => ({
        id: user.id,
        iri: user["@id"],
        email: user.email,
        displayName:
          user.username ||
          user.email ||
          t("friends_page.user_fallback", { id: user.id }),
      }))
      .sort((a, b) => a.displayName.localeCompare(b.displayName));
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    feedbackMessage.value = t("friends_page.load_error");
    console.error("Friends page loading error:", error);
  } finally {
    loading.value = false;
  }
};

const sendFriendRequest = async (user) => {
  const me = currentUser.value;
  if (!me) return;

  const currentlySending = new Set(sendingIds.value);
  currentlySending.add(user.id);
  sendingIds.value = currentlySending;

  try {
    const token = normalizeToken(localStorage.getItem("jwt_token"));
    const headers = {
      Authorization: `Bearer ${token}`,
      Accept: "application/ld+json",
      "Content-Type": "application/ld+json",
    };

    const payload = {
      sender: me["@id"],
      receiver: user.iri,
      status: "pending",
    };

    const response = await axios.post(`${API_BASE}/friendships`, payload, {
      headers,
    });

    friendships.value = [response.data, ...friendships.value];
    feedbackMessage.value = t("friends_page.request_sent", {
      name: user.displayName,
    });
  } catch (error) {
    const status = error?.response?.status;

    if (status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    feedbackMessage.value = t("friends_page.request_error");
    console.error("Friend request error:", error);
  } finally {
    const updated = new Set(sendingIds.value);
    updated.delete(user.id);
    sendingIds.value = updated;
  }
};

const acceptFriendRequest = async (request) => {
  const current = new Set(acceptingIds.value);
  current.add(request.id);
  acceptingIds.value = current;

  try {
    const token = normalizeToken(localStorage.getItem("jwt_token"));
    const patchHeaders = {
      Authorization: `Bearer ${token}`,
      Accept: "application/ld+json",
      "Content-Type": "application/merge-patch+json",
    };

    const patchPayload = { status: "accepted" };
    const patchResponse = await axios.patch(
      toApiUrl(request.iri),
      patchPayload,
      {
        headers: patchHeaders,
      },
    );

    friendships.value = friendships.value.map((friendship) =>
      friendship.id === request.id ? patchResponse.data : friendship,
    );

    feedbackMessage.value = t("friends_page.request_accepted", {
      name: request.senderName,
    });
  } catch (error) {
    const status = error?.response?.status;
    if (status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    feedbackMessage.value = t("friends_page.accept_error");
    console.error("Friend request accept error:", error);
  } finally {
    const updated = new Set(acceptingIds.value);
    updated.delete(request.id);
    acceptingIds.value = updated;
  }
};

const removeFriend = async (user) => {
  const relation = getFriendship(user);
  if (!relation) return;

  const friendshipIri =
    relation["@id"] || `${API_BASE}/friendships/${relation.id}`;

  const current = new Set(removingIds.value);
  current.add(user.id);
  removingIds.value = current;

  try {
    const token = normalizeToken(localStorage.getItem("jwt_token"));
    await axios.delete(toApiUrl(friendshipIri), {
      headers: { Authorization: `Bearer ${token}` },
    });

    friendships.value = friendships.value.filter(
      (friendship) => friendship.id !== relation.id,
    );
    feedbackMessage.value = t("friends_page.remove_success", {
      name: user.displayName,
    });
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }
    feedbackMessage.value = t("friends_page.remove_error");
    console.error("Remove friend error:", error);
  } finally {
    const updated = new Set(removingIds.value);
    updated.delete(user.id);
    removingIds.value = updated;
  }
};

onMounted(loadFriendsPageData);
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
.bg-main {
  background-color: var(--color-main);
}
.bg-main-light {
  background-color: var(--color-main-light);
}
</style>
