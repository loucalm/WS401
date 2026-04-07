<template>
  <div class="h-screen overflow-hidden bg-white text-black">
    <main class="mx-auto flex h-full w-full max-w-105 flex-col bg-white">
      <section :class="headerClass" class="rounded-b-3xl px-4 pb-6 pt-4">
        <div class="relative flex items-center justify-center">
          <button
            type="button"
            class="absolute left-0 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full text-black transition hover:bg-white/45"
            aria-label="Fermer"
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
            Add activity
          </h1>
        </div>
      </section>

      <section
        v-if="currentStep === 1"
        class="flex-1 overflow-y-auto px-4 pt-6"
      >
        <div class="mx-auto w-full max-w-[700px] px-1">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-6 text-center font-ui text-[36px] leading-[0.98] text-black"
        >
          Type of activity
        </h2>

        <p v-if="loading" class="mt-6 text-center text-body-16 text-grey">
          Chargement des activites...
        </p>

        <div v-else class="mt-6 space-y-4">
          <button
            v-for="activity in shownTypes"
            :key="activity.id"
            type="button"
            class="flex w-full items-center gap-3 rounded-[20px] border border-black/7 bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
            @click="selectActivityType(activity.raw)"
          >
            <div
              class="flex h-18 w-18 shrink-0 items-center justify-center rounded-2xl"
              :class="activity.iconBoxClass"
              :style="activity.iconBoxStyle"
            >
              <Icon
                :icon="activity.icon"
                class="h-10 w-10"
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
      />

      <WizardStep2Consumption
        v-else-if="currentStep === 2 && selectedCategory === 'consumption'"
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
      />

      <section
        v-else
        class="flex flex-1 flex-col overflow-y-auto px-4 pb-40 pt-3"
      >
        <div class="mx-auto w-full max-w-[700px] px-1">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-7 text-center font-ui text-[34px] leading-none text-black"
        >
          Recap : {{ stepTitle }}
        </h2>

        <div v-if="selectedCategory === 'food'" class="mt-12 space-y-8">
          <p class="font-ui text-[25px] leading-none text-black">
            Type : {{ selectedFoodConsumptionLabel }}
          </p>
          <template
            v-for="section in cachedFoodSections[selectedFoodConsumption] || []"
            :key="section.id"
          >
            <p class="font-ui text-[25px] leading-none text-black">
              {{ section.label }} :
              {{
                selectedFoodOptions[section.id]
                  ? section.options.find(
                      (opt) => opt.id === selectedFoodOptions[section.id],
                    )?.label || "-"
                  : "-"
              }}
            </p>
          </template>
        </div>

        <div
          v-else-if="selectedCategory === 'consumption'"
          class="mt-16 space-y-14"
        >
          <p class="font-ui text-[32px] leading-none text-black">
            Type : {{ consumptionSource }}
          </p>
          <p class="font-ui text-[32px] leading-none text-black">
            Surface : {{ surface }} m²
          </p>
          <p class="font-ui text-[32px] leading-none text-black">
            Duration : {{ duration }}h
          </p>
        </div>

        <div
          v-else-if="selectedCategory === 'clothing'"
          class="mt-12 space-y-8"
        >
          <p class="font-ui text-[25px] leading-none text-black">
            Purchase : {{ selectedClothingPurchaseLabel }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black">
            Top outfits : {{ getClothingLabel("top") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black">
            Stockings : {{ getClothingLabel("stocking") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black">
            Shoes : {{ getClothingLabel("shoes") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black">
            Underwear : {{ getClothingLabel("underwear") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black">
            Accessory : {{ getClothingLabel("accessory") }}
          </p>
        </div>

        <div v-else class="mt-16 space-y-16">
          <p class="font-ui text-[32px] leading-none text-black">
            Transport : {{ transportMode }}
          </p>
          <p class="font-ui text-[32px] leading-none text-black">
            Distance : {{ distance }}km
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

      <div
        v-if="currentStep > 1"
        class="pointer-events-none fixed inset-x-0 bottom-0 z-20 px-4 pb-4"
      >
        <div
          class="mx-auto grid w-full max-w-105 grid-cols-2 gap-4 bg-white pt-3 pointer-events-auto"
        >
          <button
            type="button"
            :class="actionBackClass"
            @click="goToStep(currentStep === 2 ? 1 : 2)"
          >
            Back
          </button>
          <button
            type="button"
            :class="actionNextClass"
            @click="currentStep === 2 ? goToStep(3) : confirmWizard()"
          >
            {{ currentStep === 2 ? "Next" : "Confirm" }}
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { Icon } from "@iconify/vue";
import WizardStep2Travel from "./wizard/WizardStep2Travel.vue";
import WizardStep2Food from "./wizard/WizardStep2Food.vue";
import WizardStep2Consumption from "./wizard/WizardStep2Consumption.vue";
import WizardStep2Clothing from "./wizard/WizardStep2Clothing.vue";
import wizardStep1Svg from "../assets/img/svg/wizard_etape_1.svg?raw";
import wizardStep2Svg from "../assets/img/svg/wizard_etape_2.svg?raw";
import wizardStep3Svg from "../assets/img/svg/wizard_etape_3.svg?raw";

export default {
  components: {
    Icon,
    WizardStep2Travel,
    WizardStep2Food,
    WizardStep2Consumption,
    WizardStep2Clothing,
  },
  setup() {
    const router = useRouter();
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
    const selectedClothingOptions = ref({
      top: "tshirt",
      stocking: "pants",
      shoes: "shoes",
      underwear: "underwear",
      accessory: "cap-hat",
    });

    const clothingPurchaseFactors = {
      online: 1,
      "second-hand": 0.35,
      premium: 1.15,
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

    const clothingPurchaseOptions = [
      { id: "online", label: "Online" },
      { id: "second-hand", label: "Second hand" },
      { id: "premium", label: "Premium" },
    ];

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

    const foodConsumptionOptionsFromApi = computed(() =>
      foodDietsFromApi.value.map((diet) => ({
        id: diet,
        label: diet,
        cardClass: "border-transparent bg-tertiary-light",
      })),
    );

    const buildFoodSectionsForDiet = (diet) => {
      const groups = new Map();
      for (const activity of activitiesByCategory.value.food) {
        if (activity.diet !== diet) continue;
        const subCategory = activity.subCategory || "Other";
        if (!groups.has(subCategory)) groups.set(subCategory, []);
        groups.get(subCategory).push({
          id: activity["@id"],
          label: activity.name,
        });
      }

      return Array.from(groups.entries()).map(([subCategory, options]) => ({
        id: normalizeKey(subCategory),
        label: subCategory,
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
          label: activity.name,
        });
      }

      return Array.from(groups.entries()).map(([subCategory, options]) => ({
        id: normalizeKey(subCategory),
        label: subCategory,
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
        const label = item.name || `Activity ${index + 1}`;
        const category = normalizeToCategory(label);
        const config = iconConfig[category] || iconConfig.travel;
        const iconStyles = resolveStep1IconStyles(item.color, category);

        return {
          id: item["@id"] || item.id || `${category}-${index}`,
          label,
          raw: {
            id: item["@id"] || item.id,
            name: label,
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
    const stepTitle = computed(() => selectedType.value?.name || "Travel");

    const selectedFoodConsumptionLabel = computed(() => {
      return selectedFoodConsumption.value || "-";
    });

    const selectedClothingPurchaseLabel = computed(() => {
      const option = clothingPurchaseOptions.find(
        (item) => item.id === selectedClothingPurchase.value,
      );
      return option?.label || "Second hand";
    });

    const co2Value = computed(() => {
      if (selectedCategory.value === "food") {
        return Object.values(selectedFoodOptions.value).reduce((total, iri) => {
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
        const selectionTotal = Object.values(
          selectedClothingOptions.value,
        ).reduce((total, iri) => {
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
      if (currentStep.value === 1) return "w-full max-w-[216px] !ml-auto !mr-0";
      if (currentStep.value === 2) return "w-full max-w-[355px]";
      return "w-full max-w-[216px] !ml-0 !mr-auto";
    });

    const actionBackClass = computed(() =>
      selectedCategory.value === "food"
        ? "flex h-14 items-center justify-center rounded-[18px] border-2 border-tertiary bg-white font-ui text-[24px] leading-none text-tertiary"
        : selectedCategory.value === "consumption"
          ? "flex h-14 items-center justify-center rounded-[18px] border-2 border-secondary bg-white font-ui text-[24px] leading-none text-secondary"
          : selectedCategory.value === "clothing"
            ? "flex h-14 items-center justify-center rounded-[18px] border-2 border-cloth bg-white font-ui text-[24px] leading-none text-cloth"
            : "flex h-14 items-center justify-center rounded-[18px] border-2 border-main bg-white font-ui text-[24px] leading-none text-main",
    );

    const actionNextClass = computed(() =>
      selectedCategory.value === "food"
        ? "flex h-14 items-center justify-center rounded-[18px] bg-tertiary font-ui text-[24px] leading-none text-white"
        : selectedCategory.value === "consumption"
          ? "flex h-14 items-center justify-center rounded-[18px] bg-secondary font-ui text-[24px] leading-none text-white"
          : selectedCategory.value === "clothing"
            ? "flex h-14 items-center justify-center rounded-[18px] bg-cloth font-ui text-[24px] leading-none text-white"
            : "flex h-14 items-center justify-center rounded-[18px] bg-main font-ui text-[24px] leading-none text-white",
    );

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
        const token = localStorage.getItem("jwt_token");
        if (!token) {
          loading.value = false;
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
        next[section.id] = section.options[0]?.id || null;
      }
      selectedFoodOptions.value = next;
    };

    const initializeClothingSelection = () => {
      const sections = clothingSectionsFromApi.value;
      const next = {};
      for (const section of sections) {
        next[section.id] = section.options[0]?.id || null;
      }
      selectedClothingOptions.value = next;
    };

    const selectActivityType = (type) => {
      selectedType.value = type;
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
      currentStep.value = 2;
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
      selectedClothingOptions.value = {
        ...selectedClothingOptions.value,
        [sectionId]: optionId,
      };
    };

    const getClothingLabel = (sectionId) => {
      const section = clothingSectionsFromApi.value.find(
        (item) => item.id === sectionId,
      );
      const option = section?.options.find(
        (item) => item.id === selectedClothingOptions.value[sectionId],
      );
      return option?.label || "-";
    };

    const selectFoodOption = (sectionId, optionId) => {
      selectedFoodOptions.value = {
        ...selectedFoodOptions.value,
        [sectionId]: optionId,
      };
    };

    const selectFoodConsumption = (diet) => {
      selectedFoodConsumption.value = diet;
      initializeFoodSelection();
    };

    const goToStep = (step) => {
      isTransportMenuOpen.value = false;
      isConsumptionMenuOpen.value = false;
      currentStep.value = step;
    };

    const goBack = () => {
      router.push("/dashboard");
    };

    // 🚀 LA NOUVELLE FONCTION MAGIQUE DE VALIDATION PANIER
    const confirmWizard = async () => {
      const token = localStorage.getItem("jwt_token");
      if (!token) return;

      let entryItems = [];
      let details = {};

      // Outil pour trouver l'ID exact dans le catalogue depuis tes libellés
      const findActivityId = (
        nameMatch,
        subCategoryMatch = null,
        dietMatch = null,
      ) => {
        const found = activityTypes.value.find((a) => {
          // On retire les tirets et espaces pour comparer proprement (ex: "Tshirt" = "T-shirt")
          const cleanString = (str) =>
            str ? str.toLowerCase().replace(/[-\s]/g, "") : "";

          const matchName = nameMatch
            ? cleanString(a.name).includes(cleanString(nameMatch))
            : true;
          const matchSub = subCategoryMatch
            ? a.subCategory === subCategoryMatch
            : true;
          const matchDiet = dietMatch ? a.diet === dietMatch : true;

          return matchName && matchSub && matchDiet;
        });
        return found ? found["@id"] : null;
      };

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
        for (const activityId of Object.values(selectedFoodOptions.value)) {
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

        for (const activityId of Object.values(selectedClothingOptions.value)) {
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
      const payload = {
        owner: "/api/users/3", // A dynamiser plus tard avec le vrai utilisateur
        details: details,
        entryItems: entryItems,
      };

      try {
        await axios.post("http://localhost:8000/api/entries", payload, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/ld+json",
          },
        });

        alert("Félicitations ! Activité enregistrée. 🌱");
        router.push("/dashboard");
      } catch (error) {
        console.error(
          "Erreur lors de l'envoi :",
          error.response?.data || error,
        );
        alert("Une erreur est survenue lors de la sauvegarde de l'empreinte.");
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
      formattedCo2,
      headerClass,
      stepIndicatorSvgMarkup,
      stepIndicatorColorClass,
      stepIndicatorContainerClass,
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
      selectClothingOption,
      getClothingLabel,
      goToStep,
      confirmWizard,
      goBack,
    };
  },
};
</script>
