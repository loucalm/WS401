import { createRouter, createWebHistory } from "vue-router";
import Login from "./components/Login.vue";
import Dashboard from "./components/Dashboard.vue";
import NotFound from "./components/NotFound.vue";
import Register from "./components/Register.vue";
import Wizard from "./components/Wizard.vue";
import Map from "./components/Map.vue";
import Profile from "./components/Profile.vue";
import ProfileEdit from "./components/ProfileEdit.vue";
import ProfileLocation from "./components/ProfileLocation.vue";
import Data from "./components/Data.vue";
import SecurityPrivacy from "./components/SecurityPrivacy.vue";
import Friends from "./components/Friends.vue";

/**
 * @param {string} token
 */
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

/**
 * @param {unknown} rawToken
 */
const normalizeToken = (rawToken) => {
  if (typeof rawToken !== "string") return "";
  return rawToken.trim().replace(/^"+|"+$/g, "");
};

/**
 * @param {string} token
 */
const isJwtValid = (token) => {
  if (!token) return false;
  const payload = parseJwtPayload(token);
  if (!payload?.exp) return false;
  const nowInSeconds = Math.floor(Date.now() / 1000);
  return payload.exp > nowInSeconds;
};

const routes = [
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: { requiresAuth: false },
  },
  {
    path: "/register",
    name: "register",
    component: Register,
    meta: { requiresAuth: false },
  },
  {
    path: "/dashboard",
    name: "dashboard",
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: "/activities",
    name: "activities",
    component: Wizard,
    meta: { requiresAuth: true },
  },
  {
    path: "/map",
    name: "map",
    component: Map,
    meta: { requiresAuth: true },
  },
  {
    path: "/profile",
    name: "profile",
    component: Profile,
    meta: { requiresAuth: true },
  },
  {
    path: "/profile/location",
    name: "profile-location",
    component: ProfileLocation,
    meta: { requiresAuth: true },
  },
  {
    path: "/profile/edit",
    name: "profile-edit",
    component: ProfileEdit,
    meta: { requiresAuth: true },
  },
  {
    path: "/data",
    name: "data",
    component: Data,
    meta: { requiresAuth: true },
  },
  {
    path: "/profile/security",
    name: "security-privacy",
    component: SecurityPrivacy,
    meta: { requiresAuth: true },
  },
  {
    path: "/friends",
    name: "friends",
    component: Friends,
    meta: { requiresAuth: true },
  },
  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const storedToken = localStorage.getItem("jwt_token");
  const token = normalizeToken(storedToken);

  if (storedToken && token && storedToken !== token) {
    localStorage.setItem("jwt_token", token);
  }

  const isAuthenticated = isJwtValid(token);

  if (token && !isAuthenticated) {
    localStorage.removeItem("jwt_token");
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return { name: "login" };
  }

  if (to.name === "login" && isAuthenticated) {
    return { name: "dashboard" };
  }

  return true;
});

export default router;
