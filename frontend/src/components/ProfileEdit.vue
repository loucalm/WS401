<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-105 flex-col bg-white pb-24"
    >
      <header class="px-7 pt-7">
        <router-link
          to="/profile"
          class="inline-flex items-center gap-2 rounded-full border border-grey/15 bg-white px-4 py-2 font-ui text-[14px] font-semibold text-main shadow-[0_3px_8px_rgba(0,0,0,0.06)]"
        >
          <Icon icon="ph:caret-left" class="h-4 w-4" />
          {{ t("profile_edit.back") }}
        </router-link>

        <div class="mt-6">
          <p
            class="font-ui text-[13px] font-bold uppercase tracking-[0.22em] text-main/80"
          >
            {{ t("profile_edit.kicker") }}
          </p>
          <h1 class="mt-2 font-title text-[34px] leading-tight text-black">
            {{ t("profile_edit.title") }}
          </h1>
          <p
            class="mt-2 max-w-[28ch] font-ui text-[15px] leading-6 text-grey-700"
          >
            {{ t("profile_edit.subtitle") }}
          </p>
        </div>
      </header>

      <section class="px-7 mt-6">
        <div
          class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)]"
        >
          <div class="flex items-center gap-4">
            <div
              class="h-20 w-20 overflow-hidden rounded-2xl border border-grey/15 bg-[#F6F6F6] shadow-sm"
            >
              <img
                :src="picturePreviewSrc"
                :alt="t('profile_edit.avatar_alt')"
                class="h-full w-full object-cover"
              />
            </div>

            <div class="min-w-0 flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("profile_edit.photo_label") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("profile_edit.photo_hint") }}
              </p>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
            <button
              v-for="option in profilePictureOptions"
              :key="option.value"
              type="button"
              @click="selectPicture(option.value)"
              class="group rounded-2xl border p-2 text-left transition"
              :class="
                selectedPicture === option.value
                  ? 'border-main bg-main/5 shadow-[0_8px_20px_rgba(0,0,0,0.08)]'
                  : 'border-grey/15 bg-white hover:border-main/40'
              "
            >
              <div class="overflow-hidden rounded-xl bg-[#F6F6F6]">
                <img
                  :src="option.src"
                  :alt="option.label"
                  class="h-24 w-full object-cover transition duration-200 group-hover:scale-[1.03]"
                />
              </div>
              <p
                class="mt-2 truncate font-ui text-[13px] font-semibold text-black"
              >
                {{ option.label }}
              </p>
            </button>
          </div>

          <button
            type="button"
            @click="clearPicture"
            class="mt-4 rounded-xl border border-grey/15 px-4 py-2.5 font-ui text-[14px] font-semibold text-grey-700"
          >
            {{ t("profile_edit.remove_photo") }}
          </button>
        </div>
      </section>

      <form
        class="flex flex-col gap-5 px-7 mt-6"
        @submit.prevent="handleSubmit"
      >
        <div
          class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)]"
        >
          <h2 class="font-ui text-body-16 font-bold text-black">
            {{ t("profile_edit.account_section") }}
          </h2>

          <div class="mt-4 space-y-4">
            <label class="block">
              <span
                class="mb-2 block font-ui text-[13px] font-semibold text-grey-700"
              >
                {{ t("profile_edit.username_label") }}
              </span>
              <input
                v-model.trim="form.username"
                type="text"
                class="w-full rounded-2xl border border-grey/15 bg-[#FAFAFA] px-4 py-3 font-ui text-[15px] text-black outline-none transition focus:border-main"
                :placeholder="t('profile_edit.username_placeholder')"
                autocomplete="nickname"
              />
            </label>

            <label class="block">
              <span
                class="mb-2 block font-ui text-[13px] font-semibold text-grey-700"
              >
                {{ t("profile_edit.target_co2_label") }}
              </span>
              <div
                class="flex items-center gap-3 rounded-2xl border border-grey/15 bg-[#FAFAFA] px-4 py-3 transition focus-within:border-main"
              >
                <input
                  v-model.number="form.targetCo2Kg"
                  type="number"
                  min="0"
                  step="0.1"
                  class="w-full bg-transparent font-ui text-[15px] text-black outline-none"
                  :placeholder="t('profile_edit.target_co2_placeholder')"
                />
                <span
                  class="shrink-0 font-ui text-[13px] font-semibold text-grey-700"
                >
                  kg CO2 / jour
                </span>
              </div>
            </label>
          </div>
        </div>

        <div
          class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)]"
        >
          <h2 class="font-ui text-body-16 font-bold text-black">
            {{ t("profile_edit.password_section") }}
          </h2>

          <p class="mt-2 font-ui text-[13px] leading-5 text-grey-700">
            {{ t("profile_edit.password_hint") }}
          </p>

          <div class="mt-4 space-y-4">
            <label class="block">
              <span
                class="mb-2 block font-ui text-[13px] font-semibold text-grey-700"
              >
                {{ t("profile_edit.current_password_label") }}
              </span>
              <input
                v-model="form.currentPassword"
                type="password"
                class="w-full rounded-2xl border border-grey/15 bg-[#FAFAFA] px-4 py-3 font-ui text-[15px] text-black outline-none transition focus:border-main"
                :placeholder="t('profile_edit.current_password_placeholder')"
                autocomplete="current-password"
              />
            </label>

            <label class="block">
              <span
                class="mb-2 block font-ui text-[13px] font-semibold text-grey-700"
              >
                {{ t("profile_edit.new_password_label") }}
              </span>
              <input
                v-model="form.newPassword"
                type="password"
                class="w-full rounded-2xl border border-grey/15 bg-[#FAFAFA] px-4 py-3 font-ui text-[15px] text-black outline-none transition focus:border-main"
                :placeholder="t('profile_edit.new_password_placeholder')"
                autocomplete="new-password"
              />
            </label>

            <label class="block">
              <span
                class="mb-2 block font-ui text-[13px] font-semibold text-grey-700"
              >
                {{ t("profile_edit.confirm_password_label") }}
              </span>
              <input
                v-model="form.confirmPassword"
                type="password"
                class="w-full rounded-2xl border border-grey/15 bg-[#FAFAFA] px-4 py-3 font-ui text-[15px] text-black outline-none transition focus:border-main"
                :placeholder="t('profile_edit.confirm_password_placeholder')"
                autocomplete="new-password"
              />
            </label>
          </div>
        </div>

        <div
          v-if="errorMessage"
          class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 font-ui text-[14px] text-red-700"
        >
          {{ errorMessage }}
        </div>

        <div
          v-if="successMessage"
          class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 font-ui text-[14px] text-green-700"
        >
          {{ successMessage }}
        </div>

        <button
          type="submit"
          class="mt-2 w-full rounded-2xl bg-main py-4 font-ui text-[18px] font-bold uppercase tracking-wider text-white shadow-[0_10px_24px_rgba(0,0,0,0.12)] transition active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
          :disabled="saving"
        >
          {{ saving ? t("profile_edit.saving") : t("profile_edit.save") }}
        </button>
      </form>
    </main>
  </div>
</template>

<script setup>
import { Icon } from "@iconify/vue";
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import {
  profilePictureOptions,
  resolveProfilePictureSrc,
} from "../utils/profilePictures.js";

const API_BASE = "http://localhost:8000/api";

const router = useRouter();
const { t } = useI18n();

const saving = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const picturePreviewSrc = ref(resolveProfilePictureSrc(null));
const selectedPicture = ref("");
const pictureDirty = ref(false);

const form = reactive({
  username: "",
  targetCo2Kg: 2,
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
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

    const users = await fetchAll("users", headers);
    const currentUser = users.find((user) => user.email === currentEmail);

    if (!currentUser) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    form.username = currentUser.username || "";
    const targetRaw = Number(currentUser.targetCo2 ?? 2000);
    form.targetCo2Kg = targetRaw > 50 ? targetRaw / 1000 : targetRaw;
    const currentPictureFile =
      (currentUser.profilePicture || "").split("/").pop() || "";
    selectedPicture.value = profilePictureOptions.some(
      (option) => option.value === currentPictureFile,
    )
      ? currentPictureFile
      : "";
    pictureDirty.value = false;
    picturePreviewSrc.value = resolveProfilePictureSrc(
      currentUser.profilePicture,
    );
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    errorMessage.value = t("profile_edit.feedback.load_error");
    console.error("Profile edit load error:", error);
  }
};

const selectPicture = (value) => {
  selectedPicture.value = value;
  pictureDirty.value = true;
  picturePreviewSrc.value = resolveProfilePictureSrc(value);
};

const clearPicture = () => {
  selectedPicture.value = "";
  pictureDirty.value = true;
  picturePreviewSrc.value = resolveProfilePictureSrc(null);
};

const handleSubmit = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  const trimmedUsername = form.username.trim();
  if (!trimmedUsername) {
    errorMessage.value = t("profile_edit.feedback.username_required");
    return;
  }

  if (
    !Number.isFinite(Number(form.targetCo2Kg)) ||
    Number(form.targetCo2Kg) <= 0
  ) {
    errorMessage.value = t("profile_edit.feedback.target_invalid");
    return;
  }

  if (form.newPassword || form.confirmPassword || form.currentPassword) {
    if (!form.currentPassword.trim()) {
      errorMessage.value = t("profile_edit.feedback.current_password_required");
      return;
    }

    if (!form.newPassword.trim()) {
      errorMessage.value = t("profile_edit.feedback.new_password_required");
      return;
    }

    if (form.newPassword !== form.confirmPassword) {
      errorMessage.value = t("profile_edit.feedback.password_mismatch");
      return;
    }
  }

  const token = normalizeToken(localStorage.getItem("jwt_token"));
  if (!token) {
    router.push("/login");
    return;
  }

  saving.value = true;

  try {
    const payload = new FormData();
    payload.append("username", trimmedUsername);
    payload.append("targetCo2", String(form.targetCo2Kg));

    if (pictureDirty.value) {
      if (selectedPicture.value) {
        payload.append("profilePicture", selectedPicture.value);
      } else {
        payload.append("clearPicture", "1");
      }
    }

    if (form.newPassword.trim()) {
      payload.append("currentPassword", form.currentPassword);
      payload.append("newPassword", form.newPassword);
      payload.append("confirmPassword", form.confirmPassword);
    }

    await axios.post(`${API_BASE}/me/profile`, payload, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    successMessage.value = t("profile_edit.feedback.saved");
    form.currentPassword = "";
    form.newPassword = "";
    form.confirmPassword = "";
    pictureDirty.value = false;
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("jwt_token");
      router.push("/login");
      return;
    }

    errorMessage.value =
      error?.response?.data?.message || t("profile_edit.feedback.save_error");
    console.error("Profile edit save error:", error);
  } finally {
    saving.value = false;
  }
};

onMounted(loadProfile);
</script>
