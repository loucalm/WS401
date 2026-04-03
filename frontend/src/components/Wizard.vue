<template>
  <div class="h-screen overflow-hidden bg-white text-black">
    <main class="mx-auto flex h-full w-full max-w-105 flex-col bg-white">
      <section
        :class="headerClass"
        class="rounded-b-3xl px-4 pb-6 pt-4 sm:px-5 sm:pb-7 sm:pt-5"
      >
        <div class="relative flex items-center justify-center">
          <button
            type="button"
            class="absolute left-0 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full text-black transition hover:bg-white/45 sm:h-11 sm:w-11"
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
              class="h-7 w-7 sm:h-8 sm:w-8"
              aria-hidden="true"
            >
              <path d="M5 5L19 19" />
              <path d="M19 5L5 19" />
            </svg>
          </button>

          <h1
            class="px-12 text-center font-ui text-[38px] leading-none text-black sm:text-[46px] md:text-[58px]"
          >
            Add activity
          </h1>
        </div>
      </section>

      <section
        v-if="currentStep === 1"
        class="flex-1 overflow-y-auto px-4 pt-6 sm:px-5 sm:pt-7"
      >
        <div class="mx-auto w-full max-w-[700px] px-1 sm:px-2">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-6 text-center font-ui text-[36px] leading-[0.98] text-black sm:mt-7 sm:text-[46px] md:text-[58px]"
        >
          Type of activity
        </h2>

        <p
          v-if="loading"
          class="mt-6 text-center text-body-16 text-grey sm:mt-7"
        >
          Chargement des activites...
        </p>

        <div v-else class="mt-6 space-y-4 sm:mt-8 sm:space-y-5">
          <button
            v-for="activity in shownTypes"
            :key="activity.id"
            type="button"
            class="flex w-full items-center gap-3 rounded-[20px] border border-black/7 bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.14)] sm:gap-4 sm:px-5 sm:py-5"
            @click="selectActivityType(activity.raw)"
          >
            <div
              class="flex h-18 w-18 shrink-0 items-center justify-center rounded-2xl sm:h-22 sm:w-22"
              :class="activity.iconBoxClass"
            >
              <Icon
                :icon="activity.icon"
                class="h-10 w-10 sm:h-13 sm:w-13"
                :class="activity.iconColorClass"
              />
            </div>

            <span
              class="font-ui text-[28px] leading-none text-black sm:text-[42px] md:text-[58px]"
              >{{ activity.label }}</span
            >
          </button>
        </div>
      </section>

      <section
        v-else-if="currentStep === 2 && selectedCategory === 'travel'"
        class="flex-1 px-4 pb-30 pt-3 sm:px-5 sm:pt-5"
      >
        <div class="mx-auto w-full max-w-[700px] px-1 sm:px-2">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-5 text-center font-ui text-[34px] leading-none text-black sm:text-[44px]"
        >
          {{ stepTitle }}
        </h2>

        <div class="relative mt-7">
          <button
            type="button"
            class="flex w-full items-center justify-between rounded-[18px] border border-main bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.14)] sm:px-5 sm:py-5"
            @click="isTransportMenuOpen = !isTransportMenuOpen"
          >
            <span
              class="font-ui text-[26px] leading-none text-black sm:text-[34px]"
              >{{ transportMode }}</span
            >
            <ChevronDownIcon
              class="h-7 w-7 text-black transition-transform sm:h-9 sm:w-9"
              :class="isTransportMenuOpen ? 'rotate-180' : ''"
            />
          </button>

          <div
            v-if="isTransportMenuOpen"
            class="absolute left-0 right-0 top-[calc(100%+10px)] z-10 overflow-hidden rounded-[18px] border border-main bg-white shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
          >
            <button
              v-for="option in transportOptions"
              :key="option"
              type="button"
              class="flex w-full items-center justify-between border-b border-main-light px-4 py-3 text-left last:border-b-0 sm:px-5"
              @click="selectTransportMode(option)"
            >
              <span
                class="font-ui text-[22px] leading-none text-black sm:text-[28px]"
                >{{ option }}</span
              >
              <span
                v-if="transportMode === option"
                class="font-ui text-[18px] leading-none text-main"
                >OK</span
              >
            </button>
          </div>
        </div>

        <div class="mt-8 flex gap-3 overflow-x-auto pb-2">
          <button
            v-for="fuel in fuelOptions"
            :key="fuel"
            type="button"
            class="min-w-fit rounded-full px-6 py-2 font-ui text-[24px] leading-none sm:px-8 sm:py-2.5 sm:text-[30px]"
            :class="
              selectedFuel === fuel
                ? 'border-2 border-main bg-white text-main'
                : 'bg-main-light text-main'
            "
            @click="selectedFuel = fuel"
          >
            {{ fuel }}
          </button>
        </div>

        <h3
          class="mt-8 font-ui text-[28px] leading-none text-black sm:text-[34px]"
        >
          Distance
        </h3>

        <div class="mt-8 flex justify-center">
          <div
            class="inline-flex min-w-[148px] items-center justify-center rounded-[999px] bg-white px-8 py-9 shadow-[0_10px_18px_rgba(0,0,0,0.18)] sm:min-w-[180px] sm:px-10 sm:py-11"
          >
            <span
              class="font-ui text-[36px] leading-none text-black sm:text-[46px]"
              >{{ distance }} KM</span
            >
          </div>
        </div>

        <div
          class="mt-10 flex items-center justify-between gap-3 px-1 sm:mt-14 sm:gap-4 sm:px-5"
        >
          <button
            type="button"
            class="flex h-14 min-w-[74px] items-center justify-center rounded-3xl bg-main-light px-4 font-ui text-[30px] leading-none text-main sm:h-18 sm:min-w-[98px] sm:px-6 sm:text-[42px]"
            @click="adjustDistance(-5)"
          >
            -5
          </button>
          <button
            type="button"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-main-light font-ui text-[30px] leading-none text-main sm:h-18 sm:w-18 sm:text-[42px]"
            @click="adjustDistance(-1)"
          >
            -
          </button>
          <button
            type="button"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-main-light font-ui text-[30px] leading-none text-main sm:h-18 sm:w-18 sm:text-[42px]"
            @click="adjustDistance(1)"
          >
            +
          </button>
          <button
            type="button"
            class="flex h-14 min-w-[74px] items-center justify-center rounded-3xl bg-main-light px-4 font-ui text-[30px] leading-none text-main sm:h-18 sm:min-w-28 sm:px-6 sm:text-[42px]"
            @click="adjustDistance(5)"
          >
            +5
          </button>
        </div>
      </section>

      <section
        v-else-if="currentStep === 2 && selectedCategory === 'food'"
        class="flex-1 overflow-y-auto px-4 pb-30 pt-3 sm:px-5 sm:pt-5"
      >
        <div class="mx-auto w-full max-w-[700px] px-1 sm:px-2">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-5 text-center font-ui text-[34px] leading-none text-black sm:text-[44px]"
        >
          Food
        </h2>

        <h3
          class="mt-8 font-ui text-[28px] leading-none text-black sm:text-[34px]"
        >
          Type of consumption
        </h3>

        <div class="mt-5 grid grid-cols-3 gap-4">
          <button
            v-for="option in foodConsumptionOptions"
            :key="option.id"
            type="button"
            class="text-left"
            @click="selectedFoodConsumption = option.id"
          >
            <div
              class="flex aspect-square items-center justify-center rounded-[20px] border-2 bg-white"
              :class="
                selectedFoodConsumption === option.id
                  ? 'border-secondary bg-white'
                  : option.cardClass
              "
            >
              <component :is="option.icon" class="h-20 w-20" />
            </div>
          </button>
        </div>

        <p
          class="mt-5 font-ui text-[30px] leading-none text-black sm:text-[38px]"
        >
          {{ selectedFoodConsumptionLabel }}
        </p>

        <div class="mt-8 space-y-7 pb-4">
          <div v-for="section in foodSections" :key="section.id">
            <p
              class="font-ui text-[24px] leading-none text-black sm:text-[28px]"
            >
              {{ section.label }} :
            </p>
            <div class="mt-4 grid grid-cols-3 gap-4">
              <button
                v-for="option in section.options"
                :key="option.id"
                type="button"
                class="text-center"
                @click="selectFoodOption(section.id, option.id)"
              >
                <div
                  class="flex h-26 items-center justify-center rounded-[18px] border-2 bg-secondary-light"
                  :class="
                    selectedFoodOptions[section.id] === option.id
                      ? 'border-secondary'
                      : 'border-transparent'
                  "
                >
                  <component
                    :is="option.icon"
                    class="h-13 w-13 text-secondary"
                  />
                </div>
                <p class="mt-2 font-ui text-body-16 leading-[1.05] text-black">
                  {{ option.label }}
                </p>
              </button>
            </div>
          </div>
        </div>
      </section>

      <section
        v-else-if="currentStep === 2 && selectedCategory === 'consumption'"
        class="flex-1 overflow-y-auto px-4 pb-30 pt-3 sm:px-5 sm:pt-5"
      >
        <div class="mx-auto w-full max-w-[700px] px-1 sm:px-2">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-5 text-center font-ui text-[34px] leading-none text-black sm:text-[44px]"
        >
          Consumption
        </h2>

        <div class="relative mt-7">
          <button
            type="button"
            class="flex w-full items-center justify-between rounded-[18px] border border-secondary bg-white px-4 py-4 text-left shadow-[0_10px_16px_rgba(0,0,0,0.14)] sm:px-5 sm:py-5"
            @click="isConsumptionMenuOpen = !isConsumptionMenuOpen"
          >
            <span
              class="font-ui text-[26px] leading-none text-black sm:text-[34px]"
              >{{ consumptionSource }}</span
            >
            <ChevronDownIcon
              class="h-7 w-7 text-black transition-transform sm:h-9 sm:w-9"
              :class="isConsumptionMenuOpen ? 'rotate-180' : ''"
            />
          </button>

          <div
            v-if="isConsumptionMenuOpen"
            class="absolute left-0 right-0 top-[calc(100%+10px)] z-10 overflow-hidden rounded-[18px] border border-secondary bg-white shadow-[0_10px_16px_rgba(0,0,0,0.14)]"
          >
            <button
              v-for="option in consumptionSources"
              :key="option"
              type="button"
              class="flex w-full items-center justify-between border-b border-secondary-light px-4 py-3 text-left last:border-b-0 sm:px-5"
              @click="selectConsumptionSource(option)"
            >
              <span
                class="font-ui text-[22px] leading-none text-black sm:text-[28px]"
                >{{ option }}</span
              >
              <span
                v-if="consumptionSource === option"
                class="font-ui text-[18px] leading-none text-secondary"
                >OK</span
              >
            </button>
          </div>
        </div>

        <h3
          class="mt-8 font-ui text-[28px] leading-none text-black sm:text-[34px]"
        >
          Surface
        </h3>

        <div class="mt-8 flex justify-center">
          <div
            class="inline-flex min-w-[210px] items-center justify-center rounded-[999px] bg-white px-10 py-9 shadow-[0_10px_18px_rgba(0,0,0,0.18)] sm:min-w-[250px] sm:px-12 sm:py-10"
          >
            <span
              class="font-ui text-[36px] leading-none text-black sm:text-[46px]"
              >{{ surface }} M²</span
            >
          </div>
        </div>

        <div
          class="mt-10 flex items-center justify-between gap-3 px-1 sm:mt-12 sm:gap-4 sm:px-5"
        >
          <button
            type="button"
            class="flex h-14 min-w-[74px] items-center justify-center rounded-3xl bg-secondary-light px-4 font-ui text-[30px] leading-none text-secondary sm:h-18 sm:min-w-[98px] sm:px-6 sm:text-[42px]"
            @click="adjustSurface(-10)"
          >
            -10
          </button>
          <button
            type="button"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-secondary-light font-ui text-[30px] leading-none text-secondary sm:h-18 sm:w-18 sm:text-[42px]"
            @click="adjustSurface(-1)"
          >
            -
          </button>
          <button
            type="button"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-secondary-light font-ui text-[30px] leading-none text-secondary sm:h-18 sm:w-18 sm:text-[42px]"
            @click="adjustSurface(1)"
          >
            +
          </button>
          <button
            type="button"
            class="flex h-14 min-w-[74px] items-center justify-center rounded-3xl bg-secondary-light px-4 font-ui text-[30px] leading-none text-secondary sm:h-18 sm:min-w-28 sm:px-6 sm:text-[42px]"
            @click="adjustSurface(10)"
          >
            +10
          </button>
        </div>

        <h3
          class="mt-9 font-ui text-[28px] leading-none text-black sm:mt-12 sm:text-[34px]"
        >
          Duration
        </h3>

        <div class="mt-6">
          <input
            v-model.number="duration"
            type="range"
            min="0"
            max="24"
            step="1"
            class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-black/30 accent-secondary"
          />
          <div class="mt-2 flex items-center justify-between">
            <span
              v-for="mark in durationMarks"
              :key="mark"
              class="font-ui text-[18px] leading-none text-black sm:text-[22px]"
              >{{ mark }}h</span
            >
          </div>
        </div>
      </section>

      <section
        v-else-if="currentStep === 2 && selectedCategory === 'clothing'"
        class="flex-1 overflow-y-auto px-4 pb-30 pt-3 sm:px-5 sm:pt-5"
      >
        <div class="mx-auto w-full max-w-[700px] px-1 sm:px-2">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-5 text-center font-ui text-[34px] leading-none text-black sm:text-[44px]"
        >
          Clothing
        </h2>

        <h3
          class="mt-8 font-ui text-[28px] leading-none text-black sm:text-[34px]"
        >
          Purchasing type :
        </h3>

        <div class="mt-5 grid grid-cols-3 gap-4">
          <button
            v-for="option in clothingPurchaseOptions"
            :key="option.id"
            type="button"
            class="text-center"
            @click="selectedClothingPurchase = option.id"
          >
            <div
              class="flex aspect-square items-center justify-center rounded-[20px] border-2 bg-cloth-light"
              :class="
                selectedClothingPurchase === option.id
                  ? 'border-cloth bg-white'
                  : 'border-transparent'
              "
            >
              <component
                :is="option.icon"
                class="h-18 w-18 text-cloth sm:h-20 sm:w-20"
              />
            </div>
            <p class="mt-2 font-ui text-body-16 leading-[1.05] text-black">
              {{ option.label }}
            </p>
          </button>
        </div>

        <p
          class="mt-5 font-ui text-[30px] leading-none text-black sm:text-[38px]"
        >
          {{ selectedClothingPurchaseLabel }}
        </p>

        <div class="mt-8 space-y-7 pb-4">
          <div v-for="section in clothingSections" :key="section.id">
            <p
              class="font-ui text-[24px] leading-none text-black sm:text-[28px]"
            >
              {{ section.label }} :
            </p>
            <div class="mt-4 grid grid-cols-3 gap-4">
              <button
                v-for="option in section.options"
                :key="option.id"
                type="button"
                class="text-center"
                @click="selectClothingOption(section.id, option.id)"
              >
                <div
                  class="flex h-26 items-center justify-center rounded-[18px] border-2 bg-cloth-light"
                  :class="
                    selectedClothingOptions[section.id] === option.id
                      ? 'border-cloth bg-white'
                      : 'border-transparent'
                  "
                >
                  <component :is="option.icon" class="h-13 w-13 text-cloth" />
                </div>
                <p class="mt-2 font-ui text-body-16 leading-[1.05] text-black">
                  {{ option.label }}
                </p>
              </button>
            </div>
          </div>
        </div>
      </section>

      <section
        v-else
        class="flex flex-1 flex-col px-4 pb-30 pt-3 sm:px-5 sm:pt-5"
      >
        <div class="mx-auto w-full max-w-[700px] px-1 sm:px-2">
          <div
            class="mx-auto [&>svg]:h-auto [&>svg]:w-full"
            :class="[stepIndicatorColorClass, stepIndicatorContainerClass]"
            v-html="stepIndicatorSvgMarkup"
          ></div>
        </div>

        <h2
          class="mt-7 text-center font-ui text-[34px] leading-none text-black sm:text-[44px]"
        >
          Recap : {{ stepTitle }}
        </h2>

        <div
          v-if="selectedCategory === 'food'"
          class="mt-12 space-y-8 sm:space-y-10"
        >
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Type : {{ selectedFoodConsumptionLabel }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Base : {{ getFoodLabel("base") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Protein : {{ getFoodLabel("protein") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Vitamines : {{ getFoodLabel("vitamines") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Bonus : {{ getFoodLabel("bonus") }}
          </p>
        </div>

        <div
          v-else-if="selectedCategory === 'consumption'"
          class="mt-16 space-y-14 sm:space-y-18"
        >
          <p class="font-ui text-[32px] leading-none text-black sm:text-[44px]">
            Type : {{ consumptionSource }}
          </p>
          <p class="font-ui text-[32px] leading-none text-black sm:text-[44px]">
            Surface : {{ surface }} m²
          </p>
          <p class="font-ui text-[32px] leading-none text-black sm:text-[44px]">
            Duration : {{ duration }}h
          </p>
        </div>

        <div
          v-else-if="selectedCategory === 'clothing'"
          class="mt-12 space-y-8 sm:space-y-10"
        >
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Purchase : {{ selectedClothingPurchaseLabel }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Top outfits : {{ getClothingLabel("top") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Stockings : {{ getClothingLabel("bottom") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Underwear : {{ getClothingLabel("underwear") }}
          </p>
          <p class="font-ui text-[25px] leading-none text-black sm:text-[34px]">
            Accessory : {{ getClothingLabel("accessory") }}
          </p>
        </div>

        <div v-else class="mt-16 space-y-16 sm:space-y-20">
          <p class="font-ui text-[32px] leading-none text-black sm:text-[44px]">
            Transport : {{ transportMode }}
          </p>
          <p class="font-ui text-[32px] leading-none text-black sm:text-[44px]">
            Fuel : {{ selectedFuel }}
          </p>
          <p class="font-ui text-[32px] leading-none text-black sm:text-[44px]">
            Distance : {{ distance }}km
          </p>
        </div>

        <div class="mt-auto pb-4 text-center">
          <p
            :class="step3TextAccentClass"
            class="font-ui text-[44px] font-bold leading-none sm:text-[64px]"
          >
            {{ formattedCo2 }} KG CO2E
          </p>
        </div>
      </section>

      <div
        v-if="currentStep > 1"
        class="pointer-events-none fixed inset-x-0 bottom-0 z-20 px-4 pb-4 sm:px-5 sm:pb-5"
      >
        <div
          class="mx-auto grid w-full max-w-105 grid-cols-2 gap-4 bg-white pt-3 pointer-events-auto sm:gap-5"
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
import wizardStep1Svg from "../assets/img/svg/wizard_etape_1.svg?raw";
import wizardStep2Svg from "../assets/img/svg/wizard_etape_2.svg?raw";
import wizardStep3Svg from "../assets/img/svg/wizard_etape_3.svg?raw";

function ChevronDownIcon() {
  return null;
}

ChevronDownIcon.template = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m5 9 7 7 7-7" />
            </svg>
          `;

function FoodMealIcon() {
  return null;
}

FoodMealIcon.template = `
            <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
              <circle cx="32" cy="32" r="28" fill="#26c6da" />
              <circle cx="28" cy="28" r="20" fill="#7b4f34" />
              <circle cx="28" cy="28" r="16" fill="#b57a59" />
              <path d="M18 30c2-7 10-12 18-12 4 0 8 1 11 3-2 10-12 18-21 18-4 0-7-1-10-3 0-2 1-4 2-6Z" fill="#fff0d4" />
              <path d="M23 22c4 0 9 2 12 5-5 4-10 6-15 6-2 0-4 0-6-1 1-6 4-10 9-10Z" fill="#ffb703" />
              <circle cx="20" cy="44" r="8" fill="#19c200" />
              <path d="M20 39v10M15 44h10" stroke="#9e7d12" stroke-width="2.5" stroke-linecap="round" />
              <path d="M26 18v4M20 22l2 3M33 20l-1 4" stroke="#14ae2b" stroke-width="3" stroke-linecap="round" />
            </svg>
          `;

function FoodFreshPlateIcon() {
  return null;
}

FoodFreshPlateIcon.template = `
            <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
              <circle cx="34" cy="33" r="24" fill="#0f52ba" />
              <path d="M10 24c8 10 19 20 32 29l9-12c-11-4-23-12-34-24l-7 7Z" fill="#4fc3f7" opacity="0.9" />
              <circle cx="31" cy="31" r="18" fill="#e8f0ff" />
              <path d="M24 20c5 2 10 5 14 9-2 6-7 12-12 15-4-3-7-8-8-13 1-4 3-8 6-11Z" fill="#ff6b6b" />
              <path d="M34 20c6 1 11 4 15 9-4 3-10 6-16 7-1-5-1-11 1-16Z" fill="#ff7f50" />
              <circle cx="20" cy="26" r="5" fill="#ff6b6b" />
              <circle cx="42" cy="18" r="4" fill="#ffd166" />
              <circle cx="20" cy="43" r="5" fill="#06d6a0" />
              <circle cx="25" cy="48" r="4" fill="#2ebd59" />
              <circle cx="34" cy="36" r="3" fill="#ffa94d" />
            </svg>
          `;

function FoodDishIcon() {
  return null;
}

FoodDishIcon.template = `
            <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
              <path d="M10 15h44v34H10z" fill="#f2ebdc" rx="10" />
              <path d="M14 48 36 16l14 20-18 12H14Z" fill="#0f52ba" />
              <circle cx="34" cy="32" r="18" fill="#6ec6ff" />
              <path d="M24 36c3-7 10-11 17-11 4 0 8 1 11 3-1 8-7 15-14 18-6-1-11-4-14-10Z" fill="#7b4f34" />
              <circle cx="40" cy="20" r="5" fill="#ff595e" />
              <circle cx="40" cy="20" r="2" fill="#2ebd59" />
              <path d="M18 30c4 6 10 10 18 10" stroke="#ffb703" stroke-width="4" stroke-linecap="round" />
              <path d="M53 19v24M58 19v24M48 20h12" stroke="#ffffff" stroke-width="3" stroke-linecap="round" />
            </svg>
          `;

function RiceIcon() {
  return null;
}
RiceIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 14c0-4 3-7 7-7s7 3 7 7H5Z" /><path d="M6 16h12c-.7 2.3-3 4-6 4s-5.3-1.7-6-4Z" /><path d="M9 8c0-2 1-3 1-3M12 8c0-2 1-3 1-3M15 8c0-2 1-3 1-3M7 9c0-1.8 1-2.7 1-2.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none" /></svg>`;

function PastaIcon() {
  return null;
}
PastaIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 4h2v16H5zM9 4h2v16H9zM13 4h2v16h-2z" /><path d="M18 7c1.7 0 3 1.3 3 3v7h-2v-7c0-.6-.4-1-1-1V7Z" /><path d="M18 12c1.7 0 3 1.3 3 3v4h-2v-4c0-.6-.4-1-1-1v-2Z" /></svg>`;

function VegetableIcon() {
  return null;
}
VegetableIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 10h16v8H4z" /><path d="M6 8c2-4 5-5 6-5 0 2-1 4-3 5H6Z" /><path d="M12 7c2-3 4-4 6-4 0 2-1 4-3 5h-3Z" /><path d="M8 14h8" stroke="#dfe7f2" stroke-width="2" stroke-linecap="round" /></svg>`;

function WhiteMeatIcon() {
  return null;
}
WhiteMeatIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 8c1-2 3-3 4-3s3 1 4 3c1 2 1 4 0 6-1 3-4 5-4 5s-3-2-4-5c-1-2-1-4 0-6Z" /><path d="M6 9c-2 0-3 1-3 3 0 2 1 3 3 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /><path d="M18 9c2 0 3 1 3 3 0 2-1 3-3 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>`;

function FishIcon() {
  return null;
}
FishIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 12c3-4 7-6 11-6 3 0 5 1 7 3-2 2-3 4-3 6 0 1 0 2 1 4-2 0-4-1-5-3-1 .5-2 .8-3 .8-3 0-6-2-8-5Z" /><circle cx="9" cy="11" r="1" fill="#dfe7f2" /></svg>`;

function EggsIcon() {
  return null;
}
EggsIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 6c2 0 4 3 4 6 0 4-2 6-4 6s-4-2-4-6c0-3 2-6 4-6Z" /><path d="M16 5c2 0 4 3 4 6 0 4-2 6-4 6s-4-2-4-6c0-3 2-6 4-6Z" /></svg>`;

function CheeseIcon() {
  return null;
}
CheeseIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 10c4-4 9-6 16-5v9H8c-2 0-4-2-4-4Z" /><circle cx="14" cy="9" r="1.2" fill="#dfe7f2" /><circle cx="17" cy="12" r="1.2" fill="#dfe7f2" /></svg>`;

function YogurtIcon() {
  return null;
}
YogurtIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 6h8l-1 13a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2L8 6Z" /><path d="M14 4c0 2 1 3 2 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>`;

function SaladIcon() {
  return null;
}
SaladIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 14c0-4 3-7 7-7s7 3 7 7H5Z" /><path d="M6 16h12c-.7 2.3-3 4-6 4s-5.3-1.7-6-4Z" /><path d="M8 10c1-2 2-3 4-3M12 10c1-2 2-3 4-3M10 12c0-1.5-1-2.8-2-3.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>`;

function CookedVegetablesIcon() {
  return null;
}
CookedVegetablesIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 14c0-4 3-7 7-7s7 3 7 7H5Z" /><path d="M6 16h12c-.7 2.3-3 4-6 4s-5.3-1.7-6-4Z" /><circle cx="9" cy="9" r="1.3" fill="#dfe7f2" /><circle cx="12" cy="8" r="1.3" fill="#dfe7f2" /><circle cx="15" cy="9.5" r="1.3" fill="#dfe7f2" /></svg>`;

function ShoppingCartIcon() {
  return null;
}
ShoppingCartIcon.template = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.5" fill="currentColor" stroke="none" /><circle cx="18" cy="20" r="1.5" fill="currentColor" stroke="none" /><path d="M3 4h2l2.3 10.2a1 1 0 0 0 1 .8h9.9a1 1 0 0 0 1-.8L21 7H7.4" /><path d="m14.5 15.5 4.5 4.5" /><path d="m19 15.5-4.5 4.5" /></svg>`;

function EcoShirtIcon() {
  return null;
}
EcoShirtIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.8 4.5 12 6.8l4.2-2.3 3.8 3.1-1.8 3.8-1.7-.7V20H7.5v-9.3l-1.7.7L4 7.6l3.8-3.1Z" /><path d="M9 14c1.2-2 3.8-3.4 6.5-2.8-1.1 3-3.4 5.3-6.3 5.8-.7-1-.8-2-.2-3Z" fill="#f3d4f8" /></svg>`;

function PoloIcon() {
  return null;
}
PoloIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.5 4h9l3.2 3.1-1.5 3.1-1.7-.6V20H7.5V9.6l-1.7.6-1.5-3.1L7.5 4Z" /><path d="M10 4h4l-1.4 2.5H11.4L10 4Z" fill="#f3d4f8" /><path d="M12 9v6" stroke="#f3d4f8" stroke-width="1.7" stroke-linecap="round" /><circle cx="12" cy="8" r="0.8" fill="#f3d4f8" /><circle cx="12" cy="11" r="0.8" fill="#f3d4f8" /></svg>`;

function TshirtIcon() {
  return null;
}
TshirtIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.2 4.3 12 6.3l3.8-2 3.2 2.8-1.6 3-1.6-.6V20H8.2V9.5l-1.6.6-1.6-3 3.2-2.8Z" /></svg>`;

function JacketIcon() {
  return null;
}
JacketIcon.template = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 4.5h8l3 3.2-1.4 3.3-2-.8V20H8.4v-9.8l-2 .8L5 7.7 8 4.5Z" /><path d="M12 4.8V20" /><path d="M10.2 9.5h3.6" /></svg>`;

function CoatIcon() {
  return null;
}
CoatIcon.template = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.5 3.8h7l2.6 3.1-1 3-1.8-.8L17.6 20H6.4L8.7 9.1l-1.8.8-1-3 2.6-3.1Z" /><path d="M12 4.2V20" /><path d="M10.5 8.8h3" /><path d="M10.5 12.5h3" /></svg>`;

function ShortsIcon() {
  return null;
}
ShortsIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 5h12l-1 14h-4l-1-7-1 7H7L6 5Z" /><path d="M6.5 8h11" stroke="#f3d4f8" stroke-width="1.6" stroke-linecap="round" /></svg>`;

function PantsIcon() {
  return null;
}
PantsIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 4h8l-1 16h-3l-1-8-1 8H7L8 4Z" /><path d="M8.5 7h7" stroke="#f3d4f8" stroke-width="1.4" stroke-linecap="round" /></svg>`;

function SweatpantsIcon() {
  return null;
}
SweatpantsIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 4.5h8l-.8 15h-3.2l-.8-7-.8 7H7.2L8 4.5Z" /><path d="M8.6 7.5c2.3 1 4.5 1 6.8 0" stroke="#f3d4f8" stroke-width="1.4" stroke-linecap="round" /></svg>`;

function BraIcon() {
  return null;
}
BraIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.5 7.5c1.3 0 2.5.7 3.2 1.9L12 10l.3-.6c.7-1.2 1.9-1.9 3.2-1.9 1.8 0 3.2 1.4 3.2 3.2 0 3.5-1.9 6.4-5.1 7.7l-1.6-2.6-1.6 2.6c-3.2-1.3-5.1-4.2-5.1-7.7 0-1.8 1.4-3.2 3.2-3.2Z" /><path d="M6 17c1.5-1 2.7-2.2 3.7-3.7M18 17c-1.5-1-2.7-2.2-3.7-3.7" stroke="#f3d4f8" stroke-width="1.3" stroke-linecap="round" /></svg>`;

function BriefsIcon() {
  return null;
}
BriefsIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 5h12l1 5c0 5-2.5 8-7 8s-7-3-7-8l1-5Z" /><path d="M7 8h10" stroke="#f3d4f8" stroke-width="1.5" stroke-linecap="round" /><path d="M12 11v5" stroke="#f3d4f8" stroke-width="1.5" stroke-linecap="round" /></svg>`;

function CapIcon() {
  return null;
}
CapIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 6c3.8 0 7 2.6 7 5.8V14H5v-2.2C5 8.6 8.2 6 12 6Z" /><path d="M12 14c3.6 0 6.8.9 8 2.4-1.5 1.1-4.2 1.8-8 1.8-2.6 0-5.2-.7-7-1.8.6-1.2 2.4-2.1 5-2.4h2Z" /><path d="M18 12c1.8.2 3 .9 3 2.1 0 1.1-.9 1.8-2.7 2" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /></svg>`;

function ScarfIcon() {
  return null;
}
ScarfIcon.template = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 3c3.3 0 6 2.3 6 5.2 0 1.9-1.1 3.6-2.8 4.6L13 20h-3l2.8-5.5C10.1 13.5 8 11.1 8 8.2 8 5.3 10.7 3 14 3Z" /><path d="M12 11.5c1.6 0 3.4.6 5.1 1.7" fill="none" stroke="#f3d4f8" stroke-width="1.5" stroke-linecap="round" /><path d="M12.2 15.5 10 20" fill="none" stroke="#f3d4f8" stroke-width="1.5" stroke-linecap="round" /></svg>`;

export default {
  components: {
    Icon,
    ChevronDownIcon,
  },
  setup() {
    const router = useRouter();
    const loading = ref(true);
    const activityTypes = ref([]);
    const currentStep = ref(1);
    const selectedType = ref(null);
    const transportMode = ref("Car");
    const transportOptions = ["Car", "Bus", "Train", "Plane"];
    const isTransportMenuOpen = ref(false);
    const fuelOptions = ["Diesel", "Petrol", "GPL"];
    const selectedFuel = ref("Diesel");
    const distance = ref(7);
    const selectedFoodConsumption = ref("meal");
    const selectedFoodOptions = ref({
      base: "rice",
      protein: "white-meat",
      vitamines: "yogurt",
      bonus: "salad",
    });
    const consumptionSource = ref("Electricity");
    const consumptionSources = ["Electricity", "Fuel", "Wood", "Gas", "Solar"];
    const isConsumptionMenuOpen = ref(false);
    const surface = ref(130);
    const duration = ref(8);
    const durationMarks = [0, 4, 8, 12, 16, 20, 24];
    const selectedClothingPurchase = ref("second-hand");
    const selectedClothingOptions = ref({
      top: "tshirt",
      bottom: "pants",
      underwear: "men",
      accessory: "cap",
    });

    const co2Factors = {
      Car: { Diesel: 0.193, Petrol: 0.171, GPL: 0.16 },
      Bus: { Diesel: 0.105, Petrol: 0.105, GPL: 0.1 },
      Train: { Diesel: 0.029, Petrol: 0.029, GPL: 0.029 },
      Plane: { Diesel: 0.255, Petrol: 0.255, GPL: 0.255 },
    };
    const consumptionFactors = {
      Electricity: 0.0021,
      Fuel: 0.0049,
      Wood: 0.0014,
      Gas: 0.0032,
      Solar: 0.0002,
    };
    const clothingPurchaseFactors = {
      online: 1,
      "second-hand": 0.35,
      premium: 1.15,
    };
    const clothingItemFactors = {
      tshirt: 6.5,
      jacket: 14,
      coat: 18,
      short: 5,
      pants: 10,
      sweatpants: 8,
      women: 4,
      men: 3,
      cap: 2,
      scarf: 2.5,
    };

    const fallbackTypes = [
      { id: "travel", name: "Travel" },
      { id: "food", name: "Food" },
      { id: "consumption", name: "Consumption" },
      { id: "clothing", name: "Clothing" },
    ];

    const foodConsumptionOptions = [
      {
        id: "meal",
        icon: FoodMealIcon,
        cardClass: "border-transparent bg-main-light",
      },
      {
        id: "fresh",
        icon: FoodFreshPlateIcon,
        cardClass: "border-transparent bg-secondary-light",
      },
      {
        id: "dish",
        icon: FoodDishIcon,
        cardClass: "border-transparent bg-tertiary-light",
      },
    ];

    const clothingPurchaseOptions = [
      { id: "online", label: "Online", icon: ShoppingCartIcon },
      { id: "second-hand", label: "Second hand", icon: EcoShirtIcon },
      { id: "premium", label: "Premium", icon: PoloIcon },
    ];

    const foodSections = [
      {
        id: "base",
        label: "Base",
        options: [
          { id: "rice", label: "Rice", icon: RiceIcon },
          { id: "pasta", label: "Pasta", icon: PastaIcon },
          { id: "vegetable", label: "Vegetable", icon: VegetableIcon },
        ],
      },
      {
        id: "protein",
        label: "Protein",
        options: [
          { id: "white-meat", label: "White meat", icon: WhiteMeatIcon },
          { id: "fish", label: "Fish", icon: FishIcon },
          { id: "eggs", label: "Eggs", icon: EggsIcon },
        ],
      },
      {
        id: "vitamines",
        label: "Vitamines",
        options: [
          { id: "cheese", label: "Cheese", icon: CheeseIcon },
          { id: "yogurt", label: "Yogurt", icon: YogurtIcon },
        ],
      },
      {
        id: "bonus",
        label: "Bonus",
        options: [
          { id: "salad", label: "Salad", icon: SaladIcon },
          {
            id: "cooked-vegetables",
            label: "Cooked vegetables",
            icon: CookedVegetablesIcon,
          },
        ],
      },
    ];

    const clothingSections = [
      {
        id: "top",
        label: "Top outfits",
        options: [
          { id: "tshirt", label: "Tshirt", icon: TshirtIcon },
          { id: "jacket", label: "Jacket", icon: JacketIcon },
          { id: "coat", label: "Coat", icon: CoatIcon },
        ],
      },
      {
        id: "bottom",
        label: "Stockings",
        options: [
          { id: "short", label: "Short", icon: ShortsIcon },
          { id: "pants", label: "Pants", icon: PantsIcon },
          { id: "sweatpants", label: "Sweatpants", icon: SweatpantsIcon },
        ],
      },
      {
        id: "underwear",
        label: "Underwear",
        options: [
          { id: "women", label: "Women", icon: BraIcon },
          { id: "men", label: "Men", icon: BriefsIcon },
        ],
      },
      {
        id: "accessory",
        label: "Accessory",
        options: [
          { id: "cap", label: "Cap", icon: CapIcon },
          { id: "scarf", label: "Scarf", icon: ScarfIcon },
        ],
      },
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

    const shownTypes = computed(() => {
      const source =
        activityTypes.value.length > 0 ? activityTypes.value : fallbackTypes;

      return source.map((item, index) => {
        const label = item.name || `Activity ${index + 1}`;
        const category = normalizeToCategory(label);
        const config = iconConfig[category] || iconConfig.travel;

        return {
          id: item["@id"] || item.id || `${category}-${index}`,
          label,
          raw: item,
          ...config,
        };
      });
    });

    const selectedCategory = computed(() =>
      normalizeToCategory(selectedType.value?.name || "travel"),
    );
    const stepTitle = computed(() => selectedType.value?.name || "Travel");
    const selectedFoodConsumptionLabel = computed(() => {
      const labels = { meal: "Meal", fresh: "Fresh food", dish: "Dish" };
      return labels[selectedFoodConsumption.value] || "Meal";
    });
    const selectedClothingPurchaseLabel = computed(() => {
      const option = clothingPurchaseOptions.find(
        (item) => item.id === selectedClothingPurchase.value,
      );
      return option?.label || "Second hand";
    });
    const co2Value = computed(() => {
      if (selectedCategory.value === "food") {
        return 1.35;
      }

      if (selectedCategory.value === "consumption") {
        const factor =
          consumptionFactors[consumptionSource.value] ||
          consumptionFactors.Electricity;
        return surface.value * duration.value * factor;
      }

      if (selectedCategory.value === "clothing") {
        const purchaseFactor =
          clothingPurchaseFactors[selectedClothingPurchase.value] ||
          clothingPurchaseFactors["second-hand"];
        const selectionTotal = Object.values(
          selectedClothingOptions.value,
        ).reduce(
          (total, itemId) => total + (clothingItemFactors[itemId] || 0),
          0,
        );
        return selectionTotal * purchaseFactor;
      }

      const transportFactors =
        co2Factors[transportMode.value] || co2Factors.Car;
      const factor =
        transportFactors[selectedFuel.value] || transportFactors.Diesel;
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
        ? "flex h-14 items-center justify-center rounded-[18px] border-2 border-tertiary bg-white font-ui text-[24px] leading-none text-tertiary sm:h-16 sm:text-[30px]"
        : selectedCategory.value === "consumption"
          ? "flex h-14 items-center justify-center rounded-[18px] border-2 border-secondary bg-white font-ui text-[24px] leading-none text-secondary sm:h-16 sm:text-[30px]"
          : selectedCategory.value === "clothing"
            ? "flex h-14 items-center justify-center rounded-[18px] border-2 border-cloth bg-white font-ui text-[24px] leading-none text-cloth sm:h-16 sm:text-[30px]"
            : "flex h-14 items-center justify-center rounded-[18px] border-2 border-main bg-white font-ui text-[24px] leading-none text-main sm:h-16 sm:text-[30px]",
    );
    const actionNextClass = computed(() =>
      selectedCategory.value === "food"
        ? "flex h-14 items-center justify-center rounded-[18px] bg-tertiary font-ui text-[24px] leading-none text-white sm:h-16 sm:text-[30px]"
        : selectedCategory.value === "consumption"
          ? "flex h-14 items-center justify-center rounded-[18px] bg-secondary font-ui text-[24px] leading-none text-white sm:h-16 sm:text-[30px]"
          : selectedCategory.value === "clothing"
            ? "flex h-14 items-center justify-center rounded-[18px] bg-cloth font-ui text-[24px] leading-none text-white sm:h-16 sm:text-[30px]"
            : "flex h-14 items-center justify-center rounded-[18px] bg-main font-ui text-[24px] leading-none text-white sm:h-16 sm:text-[30px]",
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

    const fetchTypes = async () => {
      try {
        const token = localStorage.getItem("jwt_token");
        const response = await axios.get(
          "http://localhost:8000/api/activity_types",
          {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/ld+json",
            },
          },
        );

        if (response.data?.member) {
          activityTypes.value = response.data.member;
          return;
        }

        if (response.data?.["hydra:member"]) {
          activityTypes.value = response.data["hydra:member"];
          return;
        }

        activityTypes.value = Array.isArray(response.data) ? response.data : [];
      } catch (error) {
        console.error("Erreur lors de la recuperation des categories :", error);
      } finally {
        loading.value = false;
      }
    };

    const selectActivityType = (type) => {
      selectedType.value = type;
      transportMode.value = "Car";
      selectedFuel.value = "Diesel";
      distance.value = 7;
      isTransportMenuOpen.value = false;
      selectedFoodConsumption.value = "meal";
      selectedFoodOptions.value = {
        base: "rice",
        protein: "white-meat",
        vitamines: "yogurt",
        bonus: "salad",
      };
      consumptionSource.value = "Electricity";
      isConsumptionMenuOpen.value = false;
      surface.value = 130;
      duration.value = 8;
      selectedClothingPurchase.value = "second-hand";
      selectedClothingOptions.value = {
        top: "tshirt",
        bottom: "pants",
        underwear: "men",
        accessory: "cap",
      };
      currentStep.value = 2;
    };

    const selectTransportMode = (option) => {
      transportMode.value = option;
      isTransportMenuOpen.value = false;
    };

    const selectConsumptionSource = (option) => {
      consumptionSource.value = option;
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
      const section = clothingSections.find((item) => item.id === sectionId);
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

    const getFoodLabel = (sectionId) => {
      const section = foodSections.find((item) => item.id === sectionId);
      const option = section?.options.find(
        (item) => item.id === selectedFoodOptions.value[sectionId],
      );
      return option?.label || "-";
    };

    const goToStep = (step) => {
      isTransportMenuOpen.value = false;
      isConsumptionMenuOpen.value = false;
      currentStep.value = step;
    };

    const confirmWizard = () => {
      router.push("/dashboard");
    };

    const goBack = () => {
      router.push("/dashboard");
    };

    onMounted(fetchTypes);

    return {
      Icon,
      loading,
      currentStep,
      shownTypes,
      stepTitle,
      transportMode,
      transportOptions,
      isTransportMenuOpen,
      fuelOptions,
      selectedFuel,
      distance,
      selectedCategory,
      selectedFoodConsumption,
      selectedFoodConsumptionLabel,
      selectedFoodOptions,
      foodConsumptionOptions,
      foodSections,
      clothingPurchaseOptions,
      clothingSections,
      consumptionSource,
      consumptionSources,
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
      selectClothingOption,
      getFoodLabel,
      getClothingLabel,
      goToStep,
      confirmWizard,
      goBack,
    };
  },
};
</script>
