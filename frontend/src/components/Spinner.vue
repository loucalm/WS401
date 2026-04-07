<template>
  <div
    class="flex flex-col items-center justify-center"
    role="status"
    :aria-label="label"
  >
    <div
      class="animate-spin rounded-full border-4 border-current border-t-transparent"
      :class="[sizeClass, colorClass]"
    ></div>
    <p
      v-if="label"
      class="mt-3 text-center font-ui text-body-16"
      :class="textClass"
    >
      {{ label }}
    </p>
  </div>
</template>

<script>
export default {
  name: "Spinner",
  props: {
    label: {
      type: String,
      default: "",
    },
    sizeClass: {
      type: String,
      default: "h-10 w-10",
    },
    colorClass: {
      type: String,
      default: "text-main",
    },
    textClass: {
      type: String,
      default: "text-grey",
    },
  },
};
</script>
