<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <h1>Dashboard</h1>
      <button class="btn-logout" @click="handleLogout">Logout</button>
    </header>

    <section class="summary-card">
      <!-- Calcul dynamique des points totaux -->
      <div class="points-badge">
        <h3>MY POINTS</h3>
        <p>+ {{ totalPoints }} n2e points</p>
      </div>

      <div class="central-gauge">
        <div class="gauge-container">
          <svg class="progress-ring" width="220" height="220">
            <circle class="progress-ring__circle" stroke-width="8" fill="transparent" r="90" cx="110" cy="110"/>
          </svg>
          <div class="character-mask">
            <!-- <img src="../assets/img/mascotte.png" alt="Eco Character" class="character-img" /> -->
            <div class="stats">
              <span class="value">{{ totalCo2 }} kg</span>
              <span class="label">CO2 / Daily</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Leaderboard vide au chargement -->
      <div class="leaderboard-card">
        <h3>Friends Leaderboard (live)</h3>
        <ul v-if="friends.length > 0" class="leaderboard-list">
          <li v-for="(friend, index) in friends" :key="friend.id" class="leaderboard-item">
            <div class="user-info">
              <img :src="friend.avatar" :alt="friend.name" class="avatar" />
              <span class="name">{{ friend.name }}</span>
            </div>
            <span class="score" :class="'rank-' + (index + 1)">
              {{ index + 1 }}{{ index === 0 ? 'st' : index === 1 ? 'nd' : 'rd' }} : {{ friend.score }}kg
            </span>
          </li>
        </ul>
        <p v-else class="empty-msg">No friends online...</p>
      </div>
    </section>

    <section class="activities-section">
      <div class="section-header">
        <h2>Latest Activities</h2>
        <button class="btn-add" @click="openAddActivityModal">Add Activities</button>
      </div>

      <!-- Liste des activités filtrées (uniquement si non vides) -->
      <div v-if="activities.length > 0" class="activities-grid">
        <div v-for="activity in activities" :key="activity.id" class="activity-card">
          <div class="activity-icon">
            <i :class="activity.icon"></i>
          </div>
          <div class="activity-content">
            <div class="top-row">
              <span class="title">{{ activity.title }}</span>
              <span class="amount">{{ activity.quantity }} {{ activity.unit }}</span>
            </div>
            <div class="bottom-row">
              <span class="points">+{{ activity.points }} n2e points</span>
              <span class="impact" :class="activity.co2 >= 0 ? 'negative' : 'positive'">
                {{ activity.co2 >= 0 ? '+' : '' }}{{ activity.co2 }} kg CO2
              </span>
            </div>
          </div>
        </div>
      </div>
      <p v-else class="empty-msg">No activities yet. Start by adding one!</p>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';

// Données réactives (vides au départ)
const friends = ref([]);
const activities = ref([]);
const router = useRouter();

// Calcul automatique du total des points basé sur les activités
const totalPoints = computed(() => {
  return activities.value.reduce((sum, act) => sum + act.points, 0);
});

// Calcul du CO2 total
const totalCo2 = computed(() => {
  return activities.value.reduce((sum, act) => sum + act.co2, 0).toFixed(1);
});

// Simulation de l'appel API 
onMounted(async () => {
  // friends.value = await api.getFriends()
  // activities.value = await api.getActivities()
});

const openAddActivityModal = () => {
  console.log("Ouvrir le formulaire d'ajout");
  // Logique pour ouvrir ta modale ici
};

const handleLogout = () => {
  localStorage.removeItem('jwt_token');
  router.replace('/login');
};
</script>