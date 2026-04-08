<template>
  <div class="min-h-screen bg-white text-black">
    <main
      class="mx-auto flex min-h-screen w-full max-w-125 flex-col bg-white pb-24"
    >
      <!-- Header -->
      <header class="px-7 pt-7">
        <router-link
          to="/profile"
          class="inline-flex items-center gap-2 rounded-full border border-grey/15 bg-white px-4 py-2 font-ui text-[14px] font-semibold text-main shadow-[0_3px_8px_rgba(0,0,0,0.06)]"
        >
          <Icon icon="ph:caret-left" class="h-4 w-4" />
          {{ t("profile.security_privacy") }}
        </router-link>

        <div class="mt-6">
          <p
            class="font-ui text-[13px] font-bold uppercase tracking-[0.22em] text-main/80"
          >
            {{ t("security.kicker") }}
          </p>
          <h1 class="mt-2 font-title text-[34px] leading-tight text-black">
            {{ t("security.page_title") }}
          </h1>
          <p
            class="mt-2 max-w-[28ch] font-ui text-[15px] leading-6 text-grey-700"
          >
            {{ t("security.page_subtitle") }}
          </p>
        </div>
      </header>

      <!-- Security Section -->
      <section class="px-7 mt-6">
        <h2 class="font-ui text-[18px] font-bold text-black">
          {{ t("security.section_title") }}
        </h2>

        <div class="mt-4 space-y-3">
          <!-- Password Change -->
          <div
            class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)] flex items-center justify-between cursor-pointer transition hover:shadow-[0_12px_32px_rgba(0,0,0,0.1)]"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("security.change_password") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("security.change_password_desc") }}
              </p>
            </div>
            <Icon
              icon="ph:caret-right"
              class="ml-4 text-grey h-5 w-5 shrink-0"
            />
          </div>

          <!-- Sessions -->
          <div
            class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)] flex items-center justify-between cursor-pointer transition hover:shadow-[0_12px_32px_rgba(0,0,0,0.1)]"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("security.active_sessions") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("security.active_sessions_desc") }}
              </p>
            </div>
            <Icon
              icon="ph:caret-right"
              class="ml-4 text-grey h-5 w-5 shrink-0"
            />
          </div>

          <!-- Two Factor Auth -->
          <div
            class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)] flex items-center justify-between"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("security.two_factor") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("security.two_factor_desc") }}
              </p>
            </div>
            <button
              type="button"
              :class="[
                'ml-4 shrink-0 px-4 py-2 rounded-2xl font-ui font-semibold text-[14px] transition',
                twoFactorEnabled
                  ? 'bg-grey/10 text-grey-700'
                  : 'bg-main/10 text-main hover:bg-main/20',
              ]"
              @click="toggleTwoFactor"
            >
              {{
                twoFactorEnabled ? t("security.disable") : t("security.enable")
              }}
            </button>
          </div>
        </div>
      </section>

      <!-- Privacy Section -->
      <section class="px-7 mt-8">
        <h2 class="font-ui text-[18px] font-bold text-black">
          {{ t("privacy.section_title") }}
        </h2>

        <div class="mt-4 space-y-3">
          <!-- Data Download -->
          <div
            class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)] flex items-center justify-between"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("privacy.data_download") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("privacy.data_download_desc") }}
              </p>
            </div>
            <button
              type="button"
              class="ml-4 shrink-0 rounded-2xl border border-grey/30 bg-white px-4 py-2 font-ui font-semibold text-[14px] transition hover:bg-grey/5 active:bg-grey/10"
              @click="downloadData"
            >
              {{ t("privacy.download") }}
            </button>
          </div>

          <!-- Profile Visibility -->
          <div
            class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)] flex items-center justify-between"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("privacy.profile_visibility") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("privacy.profile_visibility_desc") }}
              </p>
            </div>
            <div class="ml-4 shrink-0">
              <button
                type="button"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                  profilePublic ? 'bg-main' : 'bg-grey/20',
                ]"
                @click="profilePublic = !profilePublic"
              >
                <span
                  :class="[
                    'inline-block h-5 w-5 transform rounded-full bg-white transition-transform',
                    profilePublic ? 'translate-x-5' : 'translate-x-0.5',
                  ]"
                />
              </button>
            </div>
          </div>

          <!-- Cookie Preferences -->
          <div
            class="rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)] flex items-center justify-between cursor-pointer transition hover:shadow-[0_12px_32px_rgba(0,0,0,0.1)]"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-black">
                {{ t("privacy.cookie_preferences") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                {{ t("privacy.cookie_preferences_desc") }}
              </p>
            </div>
            <Icon
              icon="ph:caret-right"
              class="ml-4 text-grey h-5 w-5 shrink-0"
            />
          </div>

          <!-- Data Deletion -->
          <div
            class="rounded-3xl border border-red-200 bg-red-50 p-5 shadow-[0_8px_24px_rgba(239,68,68,0.08)] flex items-center justify-between"
          >
            <div class="flex-1">
              <p class="font-ui text-[15px] font-semibold text-red-600">
                {{ t("privacy.delete_account") }}
              </p>
              <p class="mt-1 font-ui text-[13px] leading-5 text-red-500">
                {{ t("privacy.delete_account_desc") }}
              </p>
            </div>
            <button
              type="button"
              class="ml-4 shrink-0 rounded-2xl bg-red-600 px-4 py-2 font-ui font-semibold text-[14px] text-white transition hover:bg-red-700 active:bg-red-800"
              @click="requestDeleteAccount"
            >
              {{ t("privacy.delete") }}
            </button>
          </div>
        </div>
      </section>

      <!-- Info Section -->
      <section class="px-7 mt-8">
        <h2 class="font-ui text-[18px] font-bold text-black">
          {{ t("privacy.info_section_title") }}
        </h2>

        <div
          class="mt-4 rounded-3xl border border-grey/15 bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.06)]"
        >
          <div class="space-y-4">
            <div class="flex items-start gap-4">
              <Icon icon="ph:check" class="mt-0.5 h-5 w-5 shrink-0 text-main" />
              <div>
                <p class="font-ui text-[15px] font-semibold text-black">
                  {{ t("privacy.info_1_title") }}
                </p>
                <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                  {{ t("privacy.info_1_desc") }}
                </p>
              </div>
            </div>
            <div class="h-px bg-grey/10" />
            <div class="flex items-start gap-4">
              <Icon icon="ph:check" class="mt-0.5 h-5 w-5 shrink-0 text-main" />
              <div>
                <p class="font-ui text-[15px] font-semibold text-black">
                  {{ t("privacy.info_2_title") }}
                </p>
                <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                  {{ t("privacy.info_2_desc") }}
                </p>
              </div>
            </div>
            <div class="h-px bg-grey/10" />
            <div class="flex items-start gap-4">
              <Icon icon="ph:check" class="mt-0.5 h-5 w-5 shrink-0 text-main" />
              <div>
                <p class="font-ui text-[15px] font-semibold text-black">
                  {{ t("privacy.info_3_title") }}
                </p>
                <p class="mt-1 font-ui text-[13px] leading-5 text-grey-700">
                  {{ t("privacy.info_3_desc") }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <BottomNav active="profile" />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { Icon } from "@iconify/vue";
import BottomNav from "./BottomNav.vue";

const { t } = useI18n();

const twoFactorEnabled = ref(false);
const profilePublic = ref(true);

const toggleTwoFactor = () => {
  twoFactorEnabled.value = !twoFactorEnabled.value;
};

const downloadData = () => {
  // TODO: Implement data download functionality
  alert(t("privacy.download_coming_soon"));
};

const requestDeleteAccount = () => {
  if (confirm(t("privacy.delete_confirmation"))) {
    // TODO: Implement account deletion functionality
    alert(t("privacy.delete_coming_soon"));
  }
};
</script>

<style scoped>
/* Custom styles can be added here if needed */
</style>
