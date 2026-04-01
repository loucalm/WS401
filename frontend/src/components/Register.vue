<template>
  <div class="login-box">
    <header>
      <img src="../assets/img/logo.png" alt="Carbon Logo">
      <h1>COMMENCEZ VOTRE<br>JOURNEY TO ZERO EMISSIONS</h1>
    </header>

    <div class="content-grid">
      
      <div class="existing-users">
        <div class="user-card">
          <div class="user-info">
            <div style="width: 45px; height: 45px; border-radius: 50%; background: #10B981; display: flex; align-items: center; justify-content: center; font-size: 24px;">🌍</div>
            <div>
              <span>Suivez votre impact</span>
              <span>Calcul en temps réel</span>
            </div>
          </div>
        </div>

        <div class="user-card">
          <div class="user-info">
            <div style="width: 45px; height: 45px; border-radius: 50%; background: #3B82F6; display: flex; align-items: center; justify-content: center; font-size: 24px;">🏆</div>
            <div>
              <span>Relevez des défis</span>
              <span>Comparez avec vos amis</span>
            </div>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2>CRÉER UN NOUVEAU COMPTE</h2>
        
        <form @submit.prevent="handleRegister">
          <input type="text" v-model="username" placeholder="Pseudo" required />
          <input type="email" v-model="email" placeholder="Email" required />
          <input type="password" v-model="password" placeholder="Mot de passe" required />
          <input type="password" v-model="confirmPassword" placeholder="Confirmer le mot de passe" required />
          
          <button type="submit">S'INSCRIRE</button>
        </form>

        <p v-if="errorMessage" class="error">❌ {{ errorMessage }}</p>
        <p v-if="successMessage" class="success">✅ {{ successMessage }}</p>

        <a href="#" @click.prevent="retourAulogin">DÉJÀ UN COMPTE ? SE CONNECTER</a>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

// 1. Les variables
const username = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const errorMessage = ref('');
const successMessage = ref('');

// 2. LA FONCTION DE RETOUR (Bien séparée du reste !)
const retourAulogin = () => {
  localStorage.removeItem('jwt_token');
  window.location.href = '/login'; 
};

// 3. LA FONCTION D'INSCRIPTION
const handleRegister = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  if (password.value !== confirmPassword.value) {
    errorMessage.value = 'Les mots de passe ne correspondent pas.';
    return;
  }

  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      username: username.value,
      email: email.value,
      password: password.value
    });
    
    successMessage.value = 'Compte créé avec succès ! Redirection...';
    
    setTimeout(() => {
      window.location.href = '/login';
    }, 2000);

  } catch (error) {
    if (error.response && error.response.status === 409) {
      errorMessage.value = 'Cet email est déjà utilisé.';
    } else {
      errorMessage.value = 'Erreur lors de la création du compte.';
    }
  }
};
</script>