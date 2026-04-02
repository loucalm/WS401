<template>
  <div class="wizard-container">
    <div v-if="loading" class="loading-state">Connexion à l'inventaire carbone...</div>

    <template v-else>
      <h2 class="wizard-title">Ajout d'activité - Étape {{ currentStep }} / 3</h2>

      <div v-if="currentStep === 1" class="step-1">
        <h3>Type d'activité</h3>
        <div class="category-buttons">
          <button 
            v-for="type in activityTypes" 
            :key="type['@id']" 
            @click="selectActivityType(type)"
            class="cat-btn"
          >
            {{ type.name }}
          </button>
        </div>
        <p v-if="activityTypes.length === 0" class="error-msg">Aucune catégorie trouvée.</p>
      </div>

      <div v-if="currentStep === 2 && selectedType" class="step-2">
        <h3>Détails - {{ selectedType.name }}</h3>
        
        <label>Saisir la quantité (en {{ selectedType.unitLabel || 'unités' }}) :</label>
        <div class="input-group">
          <button @click="formData.value -= 1" :disabled="formData.value <= 0" class="step-btn">-</button>
          <input type="number" v-model.number="formData.value" min="0" step="0.1" class="val-input" />
          <button @click="formData.value += 1" class="step-btn">+</button>
        </div>

        <div class="navigation-buttons">
          <button @click="prevStep" class="btn-back">Retour</button>
          <button @click="nextStep" :disabled="formData.value <= 0" class="btn-next">Suivant</button>
        </div>
      </div>

      <div v-if="currentStep === 3 && selectedType" class="step-3">
        <h3>Récapitulatif</h3>
        <div class="recap-card">
          <p><strong>Activité :</strong> {{ selectedType.name }}</p>
          <p><strong>Valeur :</strong> {{ formData.value }} {{ selectedType.unitLabel }}</p>
        </div>

        <div class="co2-result">
          <p class="result-label">Estimation de ton impact :</p>
          <h2 class="result-value">{{ (formData.value * selectedType.co2Factor).toFixed(2) }} KG CO2E</h2>
        </div>

        <div class="navigation-buttons">
          <button @click="prevStep" class="btn-back">Retour</button>
          <button @click="confirmEntry" class="btn-confirm">Confirmer l'impact 🌿</button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

// --- ÉTAT ---
const currentStep = ref(1);
const loading = ref(true);
const activityTypes = ref([]); 
const selectedType = ref(null);

const formData = reactive({
  value: 0
});

// --- ACTIONS API ---

const fetchTypes = async () => {
  try {
    const token = localStorage.getItem('jwt_token');
    
    const response = await axios.get('http://localhost:8000/api/activity_types', {
      headers: { 
        'Authorization': `Bearer ${token}`, 
        'Accept': 'application/ld+json' 
      }
    });

    // On cible 'member' car c'est ce qu'on voit dans ta console
    if (response.data && response.data['member']) {
      activityTypes.value = response.data['member'];
    } else if (response.data && response.data['hydra:member']) {
      activityTypes.value = response.data['hydra:member'];
    } else {
      activityTypes.value = Array.isArray(response.data) ? response.data : [];
    }

  } catch (error) {
    console.error("Erreur lors de la récupération des catégories :", error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchTypes);

const selectActivityType = (type) => {
  selectedType.value = type;
  formData.value = 0;
  currentStep.value = 2;
};

const confirmEntry = async () => {
  const token = localStorage.getItem('jwt_token');

  // On prépare l'objet exactement comme ton PHP l'attend (value et owner)
  const payload = {
    activityType: selectedType.value['@id'], 
    owner: "/api/users/3", 
    value: parseFloat(formData.value)
  };

  try {
    await axios.post('http://localhost:8000/api/entries', payload, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/ld+json'
      }
    });

    alert("Félicitations ! Ton activité a été enregistrée. ✨");
    currentStep.value = 1;
    formData.value = 0;
    selectedType.value = null;
  } catch (error) {
    console.error("Erreur d'envoi :", error.response?.data || error);
    alert("Erreur lors de la sauvegarde.");
  }
};

// --- NAVIGATION ---
const nextStep = () => { if (currentStep.value < 3) currentStep.value++; };
const prevStep = () => { if (currentStep.value > 1) currentStep.value--; };
</script>

<style scoped>
.wizard-container { 
  max-width: 500px; 
  margin: 40px auto; 
  padding: 30px; 
  background: white; 
  border-radius: 16px; 
  box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #1f2937;
}

.wizard-title { text-align: center; color: #10b981; margin-bottom: 25px; }

.category-buttons { 
  display: grid; 
  grid-template-columns: 1fr 1fr; 
  gap: 12px; 
  margin-top: 20px; 
}

.cat-btn { 
  padding: 15px; 
  border: 2px solid #e5e7eb; 
  border-radius: 10px; 
  background: #f9fafb; 
  cursor: pointer; 
  font-weight: 600;
  transition: all 0.3s ease; 
}

.cat-btn:hover { 
  border-color: #10b981; 
  background: #ecfdf5; 
  transform: translateY(-2px);
}

.input-group { 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  gap: 15px; 
  margin: 30px 0; 
}

.val-input { 
  width: 100px; 
  text-align: center; 
  padding: 12px; 
  font-size: 1.5rem; 
  border: 2px solid #10b981;
  border-radius: 8px;
}

.step-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: #10b981;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}

.navigation-buttons { display: flex; justify-content: space-between; margin-top: 30px; }

.btn-back { background: none; border: none; color: #6b7280; cursor: pointer; text-decoration: underline; }
.btn-next, .btn-confirm { 
  background: #10b981; 
  color: white; 
  border: none; 
  padding: 12px 25px; 
  border-radius: 8px; 
  font-weight: bold; 
  cursor: pointer; 
}

.co2-result { 
  background: #f0fdf4; 
  padding: 20px; 
  border-radius: 12px; 
  margin: 20px 0; 
  text-align: center; 
  border: 1px solid #dcfce7;
}

.result-label { color: #065f46; font-size: 0.9rem; margin-bottom: 5px; }
.result-value { color: #059669; margin: 0; }
</style>