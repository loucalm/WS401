<template>
  <Transition name="popup-fade">
    <div
      v-if="visible"
      class="popup-overlay"
      role="dialog"
      aria-modal="true"
      aria-labelledby="activity-added-title"
    >
      <div class="popup-card">
        <div class="popup-icon-wrap" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" class="popup-icon">
            <path
              d="M20 7L10 17L5 12"
              stroke="currentColor"
              stroke-width="2.4"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </div>

        <h3 id="activity-added-title" class="font-title popup-title">
          Activity added
        </h3>

        <p class="font-ui popup-subtitle">
          Your carbon footprint has been saved.
        </p>

        <p v-if="co2Label" class="font-ui popup-co2">
          {{ co2Label }} KG CO2E
        </p>

        <div class="popup-actions">
          <button
            type="button"
            class="font-ui popup-button-secondary"
            @click="$emit('go-dashboard')"
          >
            OK
          </button>

          <button
            type="button"
            class="font-ui popup-button"
            @click="$emit('add-new')"
          >
            Add another activity
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
  co2Label: {
    type: String,
    default: "",
  },
});

defineEmits(["add-new", "go-dashboard"]);
</script>

<style scoped>
.popup-overlay {
  position: fixed;
  inset: 0;
  z-index: 60;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: color-mix(in srgb, var(--color-black) 48%, transparent);
  backdrop-filter: blur(6px);
}

.popup-card {
  width: min(420px, 100%);
  border-radius: 24px;
  border: 1px solid color-mix(in srgb, var(--color-main) 20%, var(--color-white));
  background:
    radial-gradient(circle at 18% 12%, color-mix(in srgb, var(--color-main) 18%, transparent), transparent 42%),
    var(--color-white);
  padding: 1.25rem 1.1rem 1.15rem;
  text-align: center;
  box-shadow: 0 24px 40px rgba(0, 0, 0, 0.22);
}

.popup-icon-wrap {
  margin: 0 auto;
  display: flex;
  height: 68px;
  width: 68px;
  align-items: center;
  justify-content: center;
  border-radius: 9999px;
  color: var(--color-main);
  background: var(--color-main-light);
  box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--color-main) 28%, transparent);
}

.popup-icon {
  width: 38px;
  height: 38px;
}

.popup-title {
  margin: 0.85rem 0 0;
  color: var(--color-black);
  font-size: 1.7rem;
  line-height: 1;
}

.popup-subtitle {
  margin: 0.6rem 0 0;
  color: color-mix(in srgb, var(--color-black) 66%, var(--color-white));
  font-size: var(--text-body-16);
}

.popup-co2 {
  margin: 0.65rem 0 0;
  color: var(--color-main);
  font-size: var(--text-body-24);
  font-weight: 700;
}

.popup-actions {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: 112px 1fr;
  gap: 0.65rem;
}

.popup-button,
.popup-button-secondary {
  width: 100%;
  border-radius: 16px;
  font-size: var(--text-body-16);
  font-weight: 700;
  padding: 0.9rem 1rem;
  transition: transform 180ms ease, filter 180ms ease;
}

.popup-button {
  border: none;
  background: var(--color-main);
  color: var(--color-white);
}

.popup-button-secondary {
  border: 1px solid color-mix(in srgb, var(--color-main) 28%, transparent);
  background: var(--color-white);
  color: var(--color-main);
}

.popup-button:hover,
.popup-button-secondary:hover {
  transform: translateY(-1px);
  filter: brightness(1.04);
}

.popup-button:active,
.popup-button-secondary:active {
  transform: translateY(0);
}

.popup-fade-enter-active,
.popup-fade-leave-active {
  transition: opacity 220ms ease;
}

.popup-fade-enter-from,
.popup-fade-leave-to {
  opacity: 0;
}
</style>