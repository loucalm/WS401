<template>
  <div
    class="h-screen overflow-hidden bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] text-black"
  >
    <main
      class="mx-auto flex h-full w-full max-w-105 flex-col border-x border-black/5 bg-white shadow-[0_0_40px_rgba(15,23,42,0.06)]"
    >
      <section
        :class="headerClass"
        class="rounded-b-[30px] px-4 pb-6 pt-4 shadow-[0_8px_20px_rgba(0,0,0,0.06)]"
      >
        <div class="relative flex items-center justify-center">
          <button
            type="button"
            class="absolute left-0 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full text-black transition hover:bg-white/45"
            :aria-label="t('common.close')"
            @click="goBack"
          >
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.8"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="h-7 w-7"
              aria-hidden="true"
            >
              <path d="M5 5L19 19" />
              <path d="M19 5L5 19" />
            </svg>
          </button>

          <h1
            class="px-12 text-center font-ui text-[38px] leading-none text-black"
          >
            {{ t("wizard.add_activity") }}
          </h1>
        </div>
      </section>

      <div class="relative flex-1 overflow-hidden">
        <Transition :name="stepTransitionName">
          <section
            v-if="currentStep === 1"
            key="wizard-step-1"
            class="h-full overflow-y-auto px-4 pt-6"
          >
            <div class="mx-auto w-full px-1">
              <div class="flex w-full" :class="stepIndicatorContainerClass">
                <div
                  class="[&>svg]:h-20 [&>svg]:w-auto [&>svg]:max-w-full"
                  :class="stepIndicatorColorClass"
                  v-html="stepIndicatorSvgMarkup"
                ></div>
              </div>
            </div>

            <h2
              class="mt-6 text-center font-ui text-[36px] leading-[0.98] text-black"
            >
              {{ t("wizard.type_of_activity") }}
            </h2>

            <p class="mt-2 text-center font-ui text-body-16 text-black/55">
              {{ t("wizard.pick_category") }}
            </p>

            <Spinner
              v-if="loading"
              class="mt-6"
              :label="t('wizard.loading_activities')"
              color-class="text-main"
              text-class="text-grey"
            />

            <div v-else class="mt-6 space-y-4">
              <button
                v-for="activity in shownTypes"
                :key="activity.id"
                type="button"
                class="flex w-full items-center gap-3 rounded-[20px] border border-black/7 bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.12)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_22px_rgba(0,0,0,0.16)]"
                @click="selectActivityType(activity.raw)"
              >
                <div
                  class="flex h-18 w-18 shrink-0 items-center justify-center rounded-2xl"
                  :class="activity.iconBoxClass"
                  :style="activity.iconBoxStyle"
                >
                  <Icon
                    :icon="activity.icon"
                    class="h-12 w-12"
                    :class="activity.iconColorClass"
                    :style="activity.iconColorStyle"
                  />
                </div>

                <span class="font-ui text-[28px] leading-none text-black">{{
                  activity.label
                }}</span>
              </button>
            </div>
          </section>

          <WizardStep2Travel
            v-else-if="currentStep === 2 && selectedCategory === 'travel'"
            key="wizard-step-2-travel"
            class="h-full"
            :step-title="stepTitle"
            :step-indicator-svg-markup="stepIndicatorSvgMarkup"
            :step-indicator-color-class="stepIndicatorColorClass"
            :step-indicator-container-class="stepIndicatorContainerClass"
            :transport-mode="transportMode"
            :transport-options="transportOptions"
            :is-transport-menu-open="isTransportMenuOpen"
            :distance="distance"
            @toggle-transport-menu="isTransportMenuOpen = !isTransportMenuOpen"
            @select-transport-mode="selectTransportMode"
            @adjust-distance="adjustDistance"
            @update-distance="distance = $event"
          />

          <WizardStep2Food
            v-else-if="currentStep === 2 && selectedCategory === 'food'"
            key="wizard-step-2-food"
            class="h-full"
            :step-indicator-svg-markup="stepIndicatorSvgMarkup"
            :step-indicator-color-class="stepIndicatorColorClass"
            :step-indicator-container-class="stepIndicatorContainerClass"
            :selected-food-consumption="selectedFoodConsumption"
            :food-consumption-options="foodConsumptionOptions"
            :selected-food-consumption-label="selectedFoodConsumptionLabel"
            :food-sections="foodSections"
            :selected-food-options="selectedFoodOptions"
            @select-food-consumption="selectFoodConsumption"
            @select-food-option="selectFoodOption"
            @reset-food-selection="resetFoodSelection"
          />

          <WizardStep2Consumption
            v-else-if="currentStep === 2 && selectedCategory === 'consumption'"
            key="wizard-step-2-consumption"
            class="h-full"
            :step-indicator-svg-markup="stepIndicatorSvgMarkup"
            :step-indicator-color-class="stepIndicatorColorClass"
            :step-indicator-container-class="stepIndicatorContainerClass"
            :consumption-source="consumptionSource"
            :consumption-sources="consumptionSources"
            :is-consumption-menu-open="isConsumptionMenuOpen"
            :surface="surface"
            :duration="duration"
            :duration-marks="durationMarks"
            @toggle-consumption-menu="
              isConsumptionMenuOpen = !isConsumptionMenuOpen
            "
            @select-consumption-source="selectConsumptionSource"
            @adjust-surface="adjustSurface"
            @update-surface="surface = $event"
            @update-duration="duration = $event"
          />

          <WizardStep2Clothing
            v-else-if="currentStep === 2 && selectedCategory === 'clothing'"
            key="wizard-step-2-clothing"
            class="h-full"
            :step-indicator-svg-markup="stepIndicatorSvgMarkup"
            :step-indicator-color-class="stepIndicatorColorClass"
            :step-indicator-container-class="stepIndicatorContainerClass"
            :selected-clothing-purchase="selectedClothingPurchase"
            :clothing-purchase-options="clothingPurchaseOptions"
            :selected-clothing-purchase-label="selectedClothingPurchaseLabel"
            :clothing-sections="clothingSections"
            :selected-clothing-options="selectedClothingOptions"
            @select-clothing-purchase="selectedClothingPurchase = $event"
            @select-clothing-option="selectClothingOption"
            @reset-clothing-selection="resetClothingSelection"
          />

          <section
            v-else
            key="wizard-step-3"
            class="flex h-full flex-col overflow-y-auto px-4 pb-40 pt-3"
          >
            <div class="mx-auto w-full px-1">
              <div class="flex w-full" :class="stepIndicatorContainerClass">
                <div
                  class="[&>svg]:h-20 [&>svg]:w-auto [&>svg]:max-w-full"
                  :class="stepIndicatorColorClass"
                  v-html="stepIndicatorSvgMarkup"
                ></div>
              </div>
            </div>

            <h2
              class="mt-7 text-center font-ui text-[34px] leading-none text-black"
            >
              {{ t("wizard.recap", { title: stepTitle }) }}
            </h2>

            <p class="mt-2 text-center font-ui text-body-16 text-black/55">
              {{ t("wizard.review") }}
            </p>

            <div v-if="selectedCategory === 'food'" class="mt-10 space-y-6">
              <p class="font-ui text-[24px] leading-none text-black">
                {{ t("wizard.diet") }} : {{ selectedFoodConsumptionLabel }}
              </p>
              <template
                v-for="section in cachedFoodSections[selectedFoodConsumption] ||
                []"
                :key="section.id"
              >
                <p class="font-ui text-[24px] leading-none text-black">
                  {{ section.label }} :
                  {{ getFoodSelectionLabels(section.id) }}
                </p>
              </template>
            </div>

            <div
              v-else-if="selectedCategory === 'consumption'"
              class="mt-12 space-y-10"
            >
              <p class="font-ui text-[32px] leading-none text-black">
                {{ t("wizard.consumption") }} : {{ translateActivityLabel(consumptionSource) }}
              </p>
              <p class="font-ui text-[32px] leading-none text-black">
                {{ t("wizard.surface") }} : {{ surface }} m²
              </p>
              <p class="font-ui text-[32px] leading-none text-black">
                {{ t("wizard.duration") }} : {{ duration }}h
              </p>
            </div>

            <div
              v-else-if="selectedCategory === 'clothing'"
              class="mt-10 space-y-6"
            >
              <p class="font-ui text-[24px] leading-none text-black">
                {{ t("wizard.purchase_type") }} {{ selectedClothingPurchaseLabel }}
              </p>
              <template v-for="section in clothingSections" :key="section.id">
                <p class="font-ui text-[24px] leading-none text-black">
                  {{ section.label }} : {{ getClothingLabel(section.id) }}
                </p>
              </template>
            </div>

            <div v-else class="mt-16 space-y-16">
              <p class="font-ui text-[32px] leading-none text-black">
                {{ t("wizard.transport") }} : {{ translateActivityLabel(transportMode) }}
              </p>
              <p class="font-ui text-[32px] leading-none text-black">
                {{ t("wizard.distance") }} : {{ distance }}km
              </p>
            </div>

            <div class="mt-auto pb-4 text-center">
              <p
                :class="step3TextAccentClass"
                class="font-ui text-[44px] font-bold leading-none"
              >
                {{ formattedCo2 }} KG CO2E
              </p>
            </div>
          </section>
        </Transition>
      </div>

      <div
        v-if="currentStep > 1"
        class="pointer-events-none fixed inset-x-0 bottom-0 z-20 bg-linear-to-t from-white/88 via-white/75 to-transparent px-4 pb-2 backdrop-blur-sm"
      >
        <div
          :class="currentStep === 2 ? '' : 'bg-white'"
          class="mx-auto grid w-full max-w-105 grid-cols-2 gap-4 rounded-3xl px-2 pt-3 pointer-events-auto"
        >
          <button
            type="button"
            :class="actionBackClass"
            @click="goToStep(currentStep === 2 ? 1 : 2, 'backward')"
          >
            {{ t("common.back") }}
          </button>
          <button
            type="button"
            :class="actionNextClass"
            :disabled="
              (currentStep === 2 && !canProceedToRecap) ||
              (currentStep === 3 && isSubmittingConfirmation)
            "
            @click="currentStep === 2 ? goNextStep() : confirmWizard()"
          >
            {{
              currentStep === 2
                ? t("common.next")
                : isSubmittingConfirmation
                  ? t("wizard.confirm_in_progress")
                  : t("common.confirm")
            }}
          </button>
        </div>
      </div>

      <ActivityAddedPopup
        :visible="showSuccessPopup"
        :co2-label="lastAddedCo2Label"
        @add-new="startNewActivity"
        @go-dashboard="goToDashboardFromPopup"
      />
    </main>
  </div>
</template>

<script>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useI18n } from "vue-i18n";
import { Icon } from "@iconify/vue";
import Spinner from "./Spinner.vue";
import WizardStep2Travel from "./wizard/WizardStep2Travel.vue";
import WizardStep2Food from "./wizard/WizardStep2Food.vue";
import WizardStep2Consumption from "./wizard/WizardStep2Consumption.vue";
import WizardStep2Clothing from "./wizard/WizardStep2Clothing.vue";
import ActivityAddedPopup from "./wizard/ActivityAddedPopup.vue";
import wizardStep1Svg from "../assets/img/svg/wizard_etape_1.svg?raw";
import wizardStep2Svg from "../assets/img/svg/wizard_etape_2.svg?raw";
import wizardStep3Svg from "../assets/img/svg/wizard_etape_3.svg?raw";
import ecoBoostImg from "../assets/img/eco-boost.png";
import ecoGreenImg from "../assets/img/eco-green.png";
import ecoMixImg from "../assets/img/eco-mix.png";
import onlinePurchaseSvg from "../assets/img/svg/online.svg?raw";
import secondHandPurchaseSvg from "../assets/img/svg/second-hand.svg?raw";
import shopPurchaseSvg from "../assets/img/svg/shop.svg?raw";

export default {
  components: {
    Icon,
    Spinner,
    WizardStep2Travel,
    WizardStep2Food,
    WizardStep2Consumption,
    WizardStep2Clothing,
    ActivityAddedPopup,
  },
  setup() {
    const router = useRouter();
    const { t, te } = useI18n();
    const loading = ref(true);
    const categories = ref([]);
    const activityTypes = ref([]);
    const currentStep = ref(1);
    const selectedType = ref(null);
    const transportMode = ref("Car");
    const isTransportMenuOpen = ref(false);
    const distance = ref(7);
    const selectedFoodConsumption = ref("eco-mix");
    const selectedFoodOptions = ref({});
    const consumptionSource = ref("Electricity");
    const selectedConsumptionActivityId = ref(null);
    const isConsumptionMenuOpen = ref(false);
    const surface = ref(130);
    const duration = ref(8);
    const durationMarks = [0, 4, 8, 12, 16, 20, 24];
    const selectedClothingPurchase = ref("second-hand");
    const selectedClothingOptions = ref({});
    const showSuccessPopup = ref(false);
    const lastAddedCo2Label = ref("");
    const transitionDirection = ref("forward");
    const isSubmittingConfirmation = ref(false);

    const clothingPurchaseFactors = {
      online: 1,
      "second-hand": 0.35,
      store: 1.15,
    };

    const fallbackTypes = [
      { id: "travel", name: "Travel", icon: "car", color: "#117D6F" },
      { id: "food", name: "Food", icon: "utensils", color: "#C1A573" },
      {
        id: "consumption",
        name: "Consumption",
        icon: "home",
        color: "#4B6481",
      },
      { id: "clothing", name: "Clothing", icon: "tshirt", color: "#831297" },
    ];

    const clothingPurchaseOptions = computed(() => [
      {
        id: "online",
        label: t("wizard.purchase_options.online"),
        svgMarkup: onlinePurchaseSvg,
      },
      {
        id: "second-hand",
        label: t("wizard.purchase_options.second_hand"),
        svgMarkup: secondHandPurchaseSvg,
      },
      {
        id: "store",
        label: t("wizard.purchase_options.shop"),
        svgMarkup: shopPurchaseSvg,
      },
    ]);

    const iconConfig = {
      travel: {
        icon: "mdi:travel",
        iconBoxClass: "bg-main-light",
        iconColorClass: "text-main",
      },
      food: {
        icon: "mdi:food",
        iconBoxClass: "bg-tertiary-light",
        iconColorClass: "text-tertiary",
      },
      consumption: {
        icon: "material-symbols:home-rounded",
        iconBoxClass: "bg-secondary-light",
        iconColorClass: "text-secondary",
      },
      clothing: {
        icon: "tabler:shirt-filled",
        iconBoxClass: "bg-cloth-light",
        iconColorClass: "text-cloth",
      },
    };

    const normalizeToCategory = (name = "") => {
      const value = name.toLowerCase();

      if (value.includes("travel") || value.includes("transport"))
        return "travel";
      if (
        value.includes("food") ||
        value.includes("meal") ||
        value.includes("alimentation")
      )
        return "food";
      if (
        value.includes("consumption") ||
        value.includes("housing") ||
        value.includes("home")
      )
        return "consumption";
      if (
        value.includes("cloth") ||
        value.includes("fashion") ||
        value.includes("vetement")
      )
        return "clothing";

      return "travel";
    };

    const parseHexColor = (color) => {
      if (typeof color !== "string") return null;
      const value = color.trim();
      return /^#([0-9a-fA-F]{6})$/.test(value) ? value : null;
    };

    const colorTokenByCategory = {
      travel: "--color-main",
      food: "--color-tertiary",
      consumption: "--color-secondary",
      clothing: "--color-cloth",
    };

    const resolveStep1IconStyles = (rawColor, category) => {
      const trimmed = typeof rawColor === "string" ? rawColor.trim() : "";

      // Preferred mode: DB stores a CSS variable token (ex: --color-tertiary)
      if (trimmed.startsWith("--color-")) {
        return {
          iconColorStyle: { color: `var(${trimmed})` },
          iconBoxStyle: { backgroundColor: `var(${trimmed}-light)` },
        };
      }

      // Backward compatibility if the DB still sends a hex color.
      const hexColor = parseHexColor(trimmed);
      if (hexColor) {
        return {
          iconColorStyle: { color: hexColor },
          iconBoxStyle: { backgroundColor: `${hexColor}22` },
        };
      }

      const fallbackToken = colorTokenByCategory[category] || "--color-main";
      return {
        iconColorStyle: { color: `var(${fallbackToken})` },
        iconBoxStyle: { backgroundColor: `var(${fallbackToken}-light)` },
      };
    };

    const normalizeKey = (value = "") =>
      value
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");

    const translateCategoryLabel = (name = "") => {
      const normalizedCategory = normalizeToCategory(name);
      const key = `taxonomy.categories.${normalizedCategory}`;
      return te(key) ? t(key) : name;
    };

    const translateSubCategoryLabel = (name = "") => {
      const key = `taxonomy.subcategories.${normalizeKey(name)}`;
      return te(key) ? t(key) : name;
    };

    const translateActivityLabel = (name = "") => {
      const key = `taxonomy.activities.${normalizeKey(name).replace(/-/g, "_")}`;
      return te(key) ? t(key) : name;
    };

    const translateDietLabel = (diet = "") => {
      const key = `taxonomy.diets.${normalizeKey(diet).replace(/-/g, "_")}`;
      return te(key) ? t(key) : diet;
    };

    const resolveCategoryName = (activity) => {
      if (!activity) return "";

      if (typeof activity.category === "object" && activity.category?.name) {
        return activity.category.name;
      }

      if (typeof activity.category === "string") {
        const category = categories.value.find(
          (item) => item["@id"] === activity.category,
        );
        if (category?.name) return category.name;
      }

      return "";
    };

    const activitiesByCategory = computed(() => {
      const grouped = {
        travel: [],
        food: [],
        consumption: [],
        clothing: [],
      };

      for (const activity of activityTypes.value) {
        const categoryKey = normalizeToCategory(resolveCategoryName(activity));
        if (grouped[categoryKey]) grouped[categoryKey].push(activity);
      }

      return grouped;
    });

    const travelActivities = computed(() =>
      activitiesByCategory.value.travel.filter(
        (activity) => activity.subCategory === "Transport",
      ),
    );

    const transportOptionsFromApi = computed(() => {
      const modes = [];
      for (const activity of travelActivities.value) {
        const mode = activity.name;
        if (mode && !modes.includes(mode)) modes.push(mode);
      }
      return modes;
    });

    const selectedTravelActivity = computed(() => {
      return travelActivities.value.find(
        (activity) => activity.name === transportMode.value,
      );
    });

    const foodDietsFromApi = computed(() => {
      const diets = [];
      for (const activity of activitiesByCategory.value.food) {
        if (activity.diet && !diets.includes(activity.diet))
          diets.push(activity.diet);
      }
      return diets;
    });

    const normalizeDietKey = (diet = "") =>
      diet.toString().trim().toLowerCase().replace(/\s+/g, "-");

    const dietImageByKey = {
      "eco-boost": ecoBoostImg,
      "eco-green": ecoGreenImg,
      "eco-mix": ecoMixImg,
    };

    const foodConsumptionOptionsFromApi = computed(() =>
      foodDietsFromApi.value.map((diet) => {
        const dietKey = normalizeDietKey(diet);

        return {
          id: diet,
          label: translateDietLabel(diet),
          image: dietImageByKey[dietKey] || null,
          cardClass: "border-transparent bg-tertiary-light",
        };
      }),
    );

    const buildFoodSectionsForDiet = (diet) => {
      const groups = new Map();
      for (const activity of activitiesByCategory.value.food) {
        if (activity.diet !== diet) continue;
        const subCategory = activity.subCategory || "Other";
        if (!groups.has(subCategory)) groups.set(subCategory, []);
        groups.get(subCategory).push({
          id: activity["@id"],
          label: translateActivityLabel(activity.name),
          icon: activity.icon,
        });
      }

      return Array.from(groups.entries()).map(([subCategory, options]) => ({
        id: normalizeKey(subCategory),
        label: translateSubCategoryLabel(subCategory),
        options,
      }));
    };

    const cachedFoodSections = ref({});

    const foodSectionsFromApi = computed(() => {
      const sections = buildFoodSectionsForDiet(selectedFoodConsumption.value);
      cachedFoodSections.value[selectedFoodConsumption.value] = sections;
      return sections;
    });

    const consumptionSourcesFromApi = computed(() =>
      activitiesByCategory.value.consumption
        .filter((activity) => activity.subCategory === "Energy")
        .map((activity) => activity.name),
    );

    const clothingSectionsFromApi = computed(() => {
      const groups = new Map();
      for (const activity of activitiesByCategory.value.clothing) {
        const subCategory = activity.subCategory || "Other";
        if (!groups.has(subCategory)) groups.set(subCategory, []);
        groups.get(subCategory).push({
          id: activity["@id"],
          label: translateActivityLabel(activity.name),
          icon: activity.icon,
        });
      }

      return Array.from(groups.entries()).map(([subCategory, options]) => ({
        id: normalizeKey(subCategory),
        label: translateSubCategoryLabel(subCategory),
        options,
      }));
    });

    const activityByIri = computed(() => {
      const map = new Map();
      for (const activity of activityTypes.value) {
        if (activity["@id"]) map.set(activity["@id"], activity);
      }
      return map;
    });

    const shownTypes = computed(() => {
      const source =
        categories.value.length > 0 ? categories.value : fallbackTypes;

      return source.map((item, index) => {
        const sourceName = item.name || `Activity ${index + 1}`;
        const label = translateCategoryLabel(sourceName);
        const category = normalizeToCategory(sourceName);
        const config = iconConfig[category] || iconConfig.travel;
        const iconStyles = resolveStep1IconStyles(item.color, category);

        return {
          id: item["@id"] || item.id || `${category}-${index}`,
          label,
          raw: {
            id: item["@id"] || item.id,
            name: sourceName,
          },
          ...config,
          icon:
            typeof item.icon === "string" && item.icon.trim().length > 0
              ? item.icon.trim()
              : config.icon,
          iconBoxStyle: iconStyles.iconBoxStyle,
          iconColorStyle: iconStyles.iconColorStyle,
        };
      });
    });

    const selectedCategory = computed(() =>
      normalizeToCategory(selectedType.value?.name || "travel"),
    );
    const stepTitle = computed(() =>
      translateCategoryLabel(selectedType.value?.name || "travel"),
    );

    const selectedFoodConsumptionLabel = computed(() => {
      return selectedFoodConsumption.value
        ? translateDietLabel(selectedFoodConsumption.value)
        : "-";
    });

    const getFoodSelectionLabels = (sectionId) => {
      const section = foodSectionsFromApi.value.find(
        (item) => item.id === sectionId,
      );
      const labels =
        section?.options
          .filter((option) =>
            (selectedFoodOptions.value[sectionId] || []).includes(option.id),
          )
          .map((option) => option.label) || [];

      return labels.length ? labels.join(", ") : "-";
    };

    const getClothingSelectionLabels = (sectionId) => {
      const section = clothingSectionsFromApi.value.find(
        (item) => item.id === sectionId,
      );
      const labels =
        section?.options
          .filter((option) =>
            (selectedClothingOptions.value[sectionId] || []).includes(
              option.id,
            ),
          )
          .map((option) => option.label) || [];

      return labels.length ? labels.join(", ") : "-";
    };

    const selectedClothingPurchaseLabel = computed(() => {
      const option = clothingPurchaseOptions.value.find(
        (item) => item.id === selectedClothingPurchase.value,
      );
      return option?.label || t("wizard.purchase_options.second_hand");
    });

    const co2Value = computed(() => {
      if (selectedCategory.value === "food") {
        return Object.values(selectedFoodOptions.value)
          .flat()
          .reduce((total, iri) => {
            const factor = activityByIri.value.get(iri)?.co2Factor || 0;
            return total + factor;
          }, 0);
      }

      if (selectedCategory.value === "consumption") {
        const factor =
          activityByIri.value.get(selectedConsumptionActivityId.value)
            ?.co2Factor || 0;
        return surface.value * duration.value * factor;
      }

      if (selectedCategory.value === "clothing") {
        const purchaseFactor =
          clothingPurchaseFactors[selectedClothingPurchase.value] ||
          clothingPurchaseFactors["second-hand"];
        const selectionTotal = Object.values(selectedClothingOptions.value)
          .flat()
          .reduce((total, iri) => {
            const factor = activityByIri.value.get(iri)?.co2Factor || 0;
            return total + factor;
          }, 0);
        return selectionTotal * purchaseFactor;
      }

      const factor = selectedTravelActivity.value?.co2Factor || 0;
      return distance.value * factor;
    });

    const formattedCo2 = computed(() => co2Value.value.toFixed(2));

    const headerClass = computed(() => {
      if (currentStep.value > 1 && selectedCategory.value === "food")
        return "bg-tertiary-light";
      if (currentStep.value > 1 && selectedCategory.value === "consumption")
        return "bg-secondary-light";
      if (currentStep.value > 1 && selectedCategory.value === "clothing")
        return "bg-cloth-light";
      return "bg-main-light";
    });

    const stepIndicatorSvgMarkup = computed(() => {
      if (currentStep.value === 1) return wizardStep1Svg;
      if (currentStep.value === 2) return wizardStep2Svg;
      return wizardStep3Svg;
    });

    const stepIndicatorColorClass = computed(() => {
      if (currentStep.value === 1) return "text-main";
      if (selectedCategory.value === "food") return "text-tertiary";
      if (selectedCategory.value === "consumption") return "text-secondary";
      if (selectedCategory.value === "clothing") return "text-cloth";
      return "text-main";
    });

    const stepIndicatorContainerClass = computed(() => {
      if (currentStep.value === 1) return "justify-end";
      if (currentStep.value === 2) return "justify-center";
      return "justify-start";
    });

    const stepTransitionName = computed(() =>
      transitionDirection.value === "backward"
        ? "wizard-step-backward"
        : "wizard-step-forward",
    );

    const actionBackClass = computed(() =>
      selectedCategory.value === "food"
        ? "flex h-14 items-center justify-center rounded-[18px] border-4 border-tertiary bg-white font-ui text-[24px] leading-none text-tertiary shadow-[0_6px_14px_rgba(0,0,0,0.08)] transition-all duration-200 hover:-translate-y-0.5"
        : selectedCategory.value === "consumption"
          ? "flex h-14 items-center justify-center rounded-[18px] border-4 border-secondary bg-white font-ui text-[24px] leading-none text-secondary shadow-[0_6px_14px_rgba(0,0,0,0.08)] transition-all duration-200 hover:-translate-y-0.5"
          : selectedCategory.value === "clothing"
            ? "flex h-14 items-center justify-center rounded-[18px] border-4 border-cloth bg-white font-ui text-[24px] leading-none text-cloth shadow-[0_6px_14px_rgba(0,0,0,0.08)] transition-all duration-200 hover:-translate-y-0.5"
            : "flex h-14 items-center justify-center rounded-[18px] border-4 border-main bg-white font-ui text-[24px] leading-none text-main shadow-[0_6px_14px_rgba(0,0,0,0.08)] transition-all duration-200 hover:-translate-y-0.5",
    );

    const actionNextClass = computed(() =>
      selectedCategory.value === "food"
        ? "flex h-14 items-center justify-center rounded-[18px] bg-tertiary font-ui text-[24px] leading-none text-white shadow-[0_8px_16px_rgba(0,0,0,0.12)] transition-all duration-200 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0"
        : selectedCategory.value === "consumption"
          ? "flex h-14 items-center justify-center rounded-[18px] bg-secondary font-ui text-[24px] leading-none text-white shadow-[0_8px_16px_rgba(0,0,0,0.12)] transition-all duration-200 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0"
          : selectedCategory.value === "clothing"
            ? "flex h-14 items-center justify-center rounded-[18px] bg-cloth font-ui text-[24px] leading-none text-white shadow-[0_8px_16px_rgba(0,0,0,0.12)] transition-all duration-200 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0"
            : "flex h-14 items-center justify-center rounded-[18px] bg-main font-ui text-[24px] leading-none text-white shadow-[0_8px_16px_rgba(0,0,0,0.12)] transition-all duration-200 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0",
    );

    const hasFoodSelection = computed(() =>
      Object.values(selectedFoodOptions.value).some(
        (selection) => Array.isArray(selection) && selection.length > 0,
      ),
    );

    const hasClothingSelection = computed(() =>
      Object.values(selectedClothingOptions.value).some(
        (selection) => Array.isArray(selection) && selection.length > 0,
      ),
    );

    const canProceedToRecap = computed(() => {
      if (selectedCategory.value === "food") return hasFoodSelection.value;
      if (selectedCategory.value === "clothing")
        return hasClothingSelection.value;
      return true;
    });

    const step3GaugeFillClass = computed(() =>
      selectedCategory.value === "food"
        ? "bg-tertiary"
        : selectedCategory.value === "consumption"
          ? "bg-secondary"
          : selectedCategory.value === "clothing"
            ? "bg-cloth"
            : "bg-main",
    );

    const step3GaugeRingClass = computed(() =>
      selectedCategory.value === "food"
        ? "border-tertiary"
        : selectedCategory.value === "consumption"
          ? "border-secondary"
          : selectedCategory.value === "clothing"
            ? "border-cloth"
            : "border-main",
    );

    const step3TextAccentClass = computed(() =>
      selectedCategory.value === "food"
        ? "text-tertiary"
        : selectedCategory.value === "consumption"
          ? "text-secondary"
          : selectedCategory.value === "clothing"
            ? "text-cloth"
            : "text-main",
    );

    const extractCollection = (responseData) => {
      if (responseData?.member) return responseData.member;
      if (responseData?.["hydra:member"]) return responseData["hydra:member"];
      return Array.isArray(responseData) ? responseData : [];
    };

    const parseJwtPayload = (token) => {
      try {
        const payload = token.split(".")[1];
        if (!payload) return null;
        const normalized = payload.replace(/-/g, "+").replace(/_/g, "/");
        const padded =
          normalized + "=".repeat((4 - (normalized.length % 4)) % 4);
        return JSON.parse(atob(padded));
      } catch {
        return null;
      }
    };

    const normalizeToken = (rawToken) => {
      if (typeof rawToken !== "string") return "";
      return rawToken.trim().replace(/^"+|"+$/g, "");
    };

    const resolveCurrentUserIri = async (headers, token) => {
      const payload = parseJwtPayload(token);
      const currentEmail = payload?.email || payload?.username || "";
      if (!currentEmail) return null;

      const usersResponse = await axios.get("http://localhost:8000/api/users", {
        headers,
      });
      const users = extractCollection(usersResponse.data);
      const currentUser = users.find((user) => user.email === currentEmail);
      return currentUser?.["@id"] || null;
    };

    const fetchAllActivityTypes = async (headers) => {
      const all = [];
      const seen = new Set();
      let page = 1;

      while (true) {
        const response = await axios.get(
          `http://localhost:8000/api/activity_types?page=${page}`,
          { headers },
        );
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

    const fetchTypes = async () => {
      try {
        const token = normalizeToken(localStorage.getItem("jwt_token"));
        if (!token) {
          loading.value = false;
          localStorage.removeItem("jwt_token");
          router.push("/login");
          return;
        }

        const headers = {
          Authorization: `Bearer ${token}`,
          Accept: "application/ld+json",
        };

        const [categoriesResponse, allActivityTypes] = await Promise.all([
          axios.get("http://localhost:8000/api/categories", { headers }),
          fetchAllActivityTypes(headers),
        ]);

        categories.value = extractCollection(categoriesResponse.data);
        activityTypes.value = allActivityTypes;
      } catch (error) {
        if (error?.response?.status === 401) {
          localStorage.removeItem("jwt_token");
          router.push("/login");
          return;
        }

        console.error("Erreur lors de la recuperation des categories :", error);
      } finally {
        loading.value = false;
      }
    };

    const initializeFoodSelection = () => {
      const sections = foodSectionsFromApi.value;
      const next = {};
      for (const section of sections) {
        next[section.id] = [];
      }
      selectedFoodOptions.value = next;
    };

    const initializeClothingSelection = () => {
      const sections = clothingSectionsFromApi.value;
      const next = {};
      for (const section of sections) {
        next[section.id] = [];
      }
      selectedClothingOptions.value = next;
    };

    const selectActivityType = (type) => {
      selectedType.value = type;
      transitionDirection.value = "forward";
      transportMode.value = transportOptionsFromApi.value[0] || "Car";
      distance.value = 7;
      isTransportMenuOpen.value = false;
      selectedFoodConsumption.value = foodDietsFromApi.value[0] || "";
      initializeFoodSelection();
      consumptionSource.value = consumptionSourcesFromApi.value[0] || "";
      selectedConsumptionActivityId.value =
        activitiesByCategory.value.consumption.find(
          (item) => item.name === consumptionSource.value,
        )?.["@id"] || null;
      isConsumptionMenuOpen.value = false;
      surface.value = 130;
      duration.value = 8;
      selectedClothingPurchase.value = "second-hand";
      initializeClothingSelection();
      goToStep(2, "forward");
    };

    const selectTransportMode = (option) => {
      transportMode.value = option;
      isTransportMenuOpen.value = false;
    };

    const selectConsumptionSource = (option) => {
      consumptionSource.value = option;
      selectedConsumptionActivityId.value =
        activitiesByCategory.value.consumption.find(
          (item) => item.name === option,
        )?.["@id"] || null;
      isConsumptionMenuOpen.value = false;
    };

    const adjustDistance = (delta) => {
      distance.value = Math.max(0, distance.value + delta);
    };

    const adjustSurface = (delta) => {
      surface.value = Math.max(0, surface.value + delta);
    };

    const selectClothingOption = (sectionId, optionId) => {
      const currentSelection = selectedClothingOptions.value[sectionId] || [];
      const nextSelection = currentSelection.includes(optionId)
        ? currentSelection.filter((selectedId) => selectedId !== optionId)
        : [...currentSelection, optionId];

      selectedClothingOptions.value = {
        ...selectedClothingOptions.value,
        [sectionId]: nextSelection,
      };
    };

    const getClothingLabel = (sectionId) => {
      return getClothingSelectionLabels(sectionId);
    };

    const selectFoodOption = (sectionId, optionId) => {
      const currentSelection = selectedFoodOptions.value[sectionId] || [];
      const nextSelection = currentSelection.includes(optionId)
        ? currentSelection.filter((selectedId) => selectedId !== optionId)
        : [...currentSelection, optionId];

      selectedFoodOptions.value = {
        ...selectedFoodOptions.value,
        [sectionId]: nextSelection,
      };
    };

    const selectFoodConsumption = (diet) => {
      selectedFoodConsumption.value = diet;
      initializeFoodSelection();
    };

    const resetFoodSelection = () => {
      initializeFoodSelection();
    };

    const resetClothingSelection = () => {
      initializeClothingSelection();
    };

    const goToStep = (step, direction = "forward") => {
      isTransportMenuOpen.value = false;
      isConsumptionMenuOpen.value = false;
      transitionDirection.value = direction;
      currentStep.value = step;
    };

    const goNextStep = () => {
      if (!canProceedToRecap.value) {
        alert(
          selectedCategory.value === "food"
            ? t("wizard.errors.pick_food")
            : t("wizard.errors.pick_clothing"),
        );
        return;
      }

      goToStep(3, "forward");
    };

    const goBack = () => {
      router.push("/dashboard");
    };

    const resetWizardState = () => {
      selectedType.value = null;
      currentStep.value = 1;
      transitionDirection.value = "forward";
      transportMode.value = transportOptionsFromApi.value[0] || "Car";
      isTransportMenuOpen.value = false;
      distance.value = 7;
      selectedFoodConsumption.value = foodDietsFromApi.value[0] || "";
      initializeFoodSelection();
      consumptionSource.value = consumptionSourcesFromApi.value[0] || "";
      selectedConsumptionActivityId.value =
        activitiesByCategory.value.consumption.find(
          (item) => item.name === consumptionSource.value,
        )?.["@id"] || null;
      isConsumptionMenuOpen.value = false;
      surface.value = 130;
      duration.value = 8;
      selectedClothingPurchase.value = "second-hand";
      initializeClothingSelection();
    };

    const startNewActivity = () => {
      showSuccessPopup.value = false;
      resetWizardState();
    };

    const goToDashboardFromPopup = () => {
      showSuccessPopup.value = false;
      router.push("/dashboard");
    };

    // 🚀 LA NOUVELLE FONCTION MAGIQUE DE VALIDATION PANIER
    const confirmWizard = async () => {
      if (isSubmittingConfirmation.value) return;

      const token = normalizeToken(localStorage.getItem("jwt_token"));
      if (!token) return;

      const headers = {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/ld+json",
      };

      const ownerIri = await resolveCurrentUserIri(headers, token);
      if (!ownerIri) {
        alert(t("wizard.errors.user_not_found"));
        return;
      }

      let entryItems = [];
      let details = {};

      // --- 🚗 TRAVEL ---
      if (selectedCategory.value === "travel") {
        const activityId = selectedTravelActivity.value?.["@id"] || null;
        if (activityId) {
          entryItems.push({
            activityType: activityId,
            quantity: distance.value,
          });
        }
        details = {
          transportMode: transportMode.value,
          distance: distance.value,
        };
      }

      // --- 🍔 FOOD ---
      else if (selectedCategory.value === "food") {
        for (const activityId of Object.values(
          selectedFoodOptions.value,
        ).flat()) {
          if (
            typeof activityId === "string" &&
            activityId.startsWith("/api/")
          ) {
            entryItems.push({ activityType: activityId, quantity: 1 });
          }
        }

        details = { diet: selectedFoodConsumption.value };
      }

      // --- ⚡ CONSUMPTION ---
      else if (selectedCategory.value === "consumption") {
        const activityId = selectedConsumptionActivityId.value;
        if (activityId) {
          entryItems.push({
            activityType: activityId,
            quantity: surface.value * duration.value,
          });
        }
        details = {
          surface: surface.value,
          duration: duration.value,
          source: consumptionSource.value,
        };
      }

      // --- 👕 CLOTHING ---
      else if (selectedCategory.value === "clothing") {
        const purchaseFactor =
          clothingPurchaseFactors[selectedClothingPurchase.value] || 1;

        for (const activityId of Object.values(
          selectedClothingOptions.value,
        ).flat()) {
          if (
            typeof activityId === "string" &&
            activityId.startsWith("/api/")
          ) {
            entryItems.push({
              activityType: activityId,
              quantity: 1 * purchaseFactor,
            });
          }
        }

        details = { purchaseType: selectedClothingPurchaseLabel.value };
      }

      // 📦 Préparation du Payload JSON
      const entryPayload = {
        owner: ownerIri,
        details: details,
      };

      isSubmittingConfirmation.value = true;

      try {
        const entryResponse = await axios.post(
          "http://localhost:8000/api/entries",
          entryPayload,
          {
            headers,
          },
        );

        const entryIri = entryResponse.data?.["@id"];
        if (!entryIri) {
          throw new Error("Entry IRI missing after creation");
        }

        await Promise.all(
          entryItems.map((item) =>
            axios.post(
              "http://localhost:8000/api/entry_items",
              {
                entry: entryIri,
                activityType: item.activityType,
                quantity: item.quantity,
              },
              { headers },
            ),
          ),
        );

        lastAddedCo2Label.value = formattedCo2.value;
        showSuccessPopup.value = true;
      } catch (error) {
        console.error("Submission error:", error.response?.data || error);
        alert(t("wizard.errors.save_failed"));
      } finally {
        isSubmittingConfirmation.value = false;
      }
    };

    onMounted(fetchTypes);

    return {
      Icon,
      loading,
      currentStep,
      shownTypes,
      stepTitle,
      transportMode,
      transportOptions: transportOptionsFromApi,
      isTransportMenuOpen,
      distance,
      selectedCategory,
      selectedFoodConsumption,
      selectedFoodConsumptionLabel,
      selectedFoodOptions,
      cachedFoodSections,
      foodConsumptionOptions: foodConsumptionOptionsFromApi,
      foodSections: foodSectionsFromApi,
      clothingPurchaseOptions,
      clothingSections: clothingSectionsFromApi,
      consumptionSource,
      consumptionSources: consumptionSourcesFromApi,
      isConsumptionMenuOpen,
      surface,
      duration,
      durationMarks,
      selectedClothingPurchase,
      selectedClothingPurchaseLabel,
      selectedClothingOptions,
      showSuccessPopup,
      lastAddedCo2Label,
      isSubmittingConfirmation,
      formattedCo2,
      headerClass,
      stepIndicatorSvgMarkup,
      stepIndicatorColorClass,
      stepIndicatorContainerClass,
      stepTransitionName,
      actionBackClass,
      actionNextClass,
      step3GaugeFillClass,
      step3GaugeRingClass,
      step3TextAccentClass,
      selectActivityType,
      selectTransportMode,
      selectConsumptionSource,
      adjustDistance,
      adjustSurface,
      selectFoodOption,
      selectFoodConsumption,
      resetFoodSelection,
      selectClothingOption,
      resetClothingSelection,
      getFoodSelectionLabels,
      getClothingLabel,
      translateActivityLabel,
      t,
      canProceedToRecap,
      goToStep,
      goNextStep,
      confirmWizard,
      startNewActivity,
      goToDashboardFromPopup,
      goBack,
    };
  },
};
</script>

<style scoped>
.wizard-step-forward-enter-active,
.wizard-step-forward-leave-active,
.wizard-step-backward-enter-active,
.wizard-step-backward-leave-active {
  transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1);
  will-change: transform;
  position: absolute;
  inset: 0;
}

.wizard-step-forward-enter-from {
  transform: translateX(100%);
}

.wizard-step-forward-enter-to {
  transform: translateX(0);
}

.wizard-step-forward-leave-from {
  transform: translateX(0);
}

.wizard-step-forward-leave-to {
  transform: translateX(-100%);
}

.wizard-step-backward-enter-from {
  transform: translateX(-100%);
}

.wizard-step-backward-enter-to {
  transform: translateX(0);
}

.wizard-step-backward-leave-from {
  transform: translateX(0);
}

.wizard-step-backward-leave-to {
  transform: translateX(100%);
}
</style>
