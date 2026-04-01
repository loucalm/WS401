<template>
  <div class="login-box">
    <header>
      <img src="../assets/img/logo.png" alt="Carbon Logo">
      <h1>CONNECT TO YOUR<br>JOURNEY TO ZERO EMISSIONS</h1>
    </header>

    <div class="content-grid">
      
      <!-- colonne gauche -->
      <div class="existing-users">
        <div class="user-card">
          <div class="user-info">
            <img src="../assets/img/fabien.png" alt="Fabien">
            <div>
              <span>Fabien</span>
              <span>pré-existant</span>
            </div>
          </div>
          <button type="button">SE CONNECTER COMME FABIEN</button>
        </div>

        <div class="user-card">
          <div class="user-info">
            <img src="../assets/img/brice.png" alt="Brice">
            <div>
              <span>Brice</span>
              <span>pré-existant</span>
            </div>
          </div>
          <button type="button">SE CONNECTER COMME BRICE</button>
        </div>
      </div>

      <!-- colonne formulaire -->
      <div class="form-section">
        <h2>NOUVEL UTILISATEUR OU AUTRE COMPTE</h2>
        
        <form @submit.prevent="handleLogin">
          <input type="email" v-model="email" placeholder="Email" required />
          <input type="password" v-model="password" placeholder="Mot de passe" required />
          <button type="submit">SE CONNECTER</button>
        </form>

        <p v-if="errorMessage" class="error">❌ {{ errorMessage }}</p>
        <p v-if="successMessage" class="success">✅ {{ successMessage }}</p>

        <a href="#">S'INSCRIRE</a>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

// variables
const email = ref('');
const password = ref('');
const errorMessage = ref('');
const successMessage = ref('');
const router = useRouter();

const handleLogin = async () => {
  // réinitialisation messages
  errorMessage.value = '';
  successMessage.value = '';

  try {
    // envoie de requetes
    const response = await axios.post('http://localhost:8000/api/login_check', {
      email: email.value,
      password: password.value
    });

    // récupération token
    const token = response.data.token;

    // sauvegarde en local
    localStorage.setItem('jwt_token', token);
    
    successMessage.value = 'Connexion reussie !';
    console.log('Token récupéré avec succès :', token);
    
    // redirection apres connexion
    router.push('/dashboard');

  } catch (error) {
    // debug infos erronées
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Identifiants incorrects.';
    } else {
      errorMessage.value = 'Erreur de connexion au serveur.';
    }
  }
};
</script>