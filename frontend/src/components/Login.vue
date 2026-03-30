<template>
  <div class="login-box">
    <h2>Connexion à l'API</h2>
    
    <form @submit.prevent="handleLogin">
      <div>
        <label>Email :</label>
        <input type="email" v-model="email" placeholder="admin@test.com" required />
      </div>
      
      <div>
        <label>Mot de passe :</label>
        <input type="password" v-model="password" placeholder="password123" required />
      </div>
      
      <button type="submit">Se connecter</button>
    </form>

    <p v-if="errorMessage" class="error">❌ {{ errorMessage }}</p>
    <p v-if="successMessage" class="success">✅ {{ successMessage }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

// Nos variables réactives connectées aux inputs
const email = ref('');
const password = ref('');
const errorMessage = ref('');
const successMessage = ref('');

const handleLogin = async () => {
  // On réinitialise les messages
  errorMessage.value = '';
  successMessage.value = '';

  try {
    // 1. On envoie la requête à notre API Symfony
    const response = await axios.post('http://localhost:8000/api/login_check', {
      email: email.value,
      password: password.value
    });

    // 2. On récupère le fameux Token JWT !
    const token = response.data.token;

    // 3. On le sauvegarde dans le coffre-fort du navigateur (localStorage)
    localStorage.setItem('jwt_token', token);
    
    successMessage.value = 'Connexion réussie ! Regardez la console.';
    console.log('Token récupéré avec succès :', token);
    
    // Plus tard : on ajoutera une redirection vers le tableau de bord ici

  } catch (error) {
    // Gestion des erreurs (mauvais mot de passe, etc.)
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Identifiants incorrects.';
    } else {
      errorMessage.value = 'Erreur de connexion au serveur.';
    }
  }
};
</script>

<style scoped>
.login-box {
  max-width: 300px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #f9f9f9;
}
input {
  width: 100%;
  margin-bottom: 10px;
  padding: 8px;
}
button {
  width: 100%;
  padding: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}
.error { color: red; font-weight: bold; }
.success { color: green; font-weight: bold; }
</style>