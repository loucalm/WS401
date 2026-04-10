import { createApp } from "vue";
import { createPinia } from "pinia";
import axios from "axios";
import i18n from "./i18n";

import "./assets/app.css";

import App from "./App.vue";
import router from "./router";

const DEV_API_ORIGIN = "http://localhost:8000";
const API_ORIGIN =
  (import.meta.env.VITE_API_ORIGIN || "").trim().replace(/\/$/, "") ||
  DEV_API_ORIGIN;

axios.interceptors.request.use((config) => {
  if (typeof config?.url !== "string") {
    return config;
  }

  if (config.url.startsWith(DEV_API_ORIGIN) && API_ORIGIN !== DEV_API_ORIGIN) {
    config.url = `${API_ORIGIN}${config.url.slice(DEV_API_ORIGIN.length)}`;
  }

  return config;
});

const app = createApp(App);

app.use(createPinia());
app.use(i18n);
app.use(router);

app.mount("#app");
