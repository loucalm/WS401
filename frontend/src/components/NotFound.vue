<template>
  <div class="not-found-container">
    <div class="error-card">
      <div class="error-code">404</div>
      <div class="character-illustration">
        <img src="../assets/img/404.png" alt="Eco Character" class="character-img" />
      </div>
      <h1>Oups ! Page introuvable</h1>
      <p>Il semble que vous ayez pris un chemin qui n'existe pas encore sur notre carte écologique.</p>
      
      <router-link to="/dashboard" class="btn-back">
        Retour au Dashboard
      </router-link>
    </div>
  </div>
</template>

<script setup>
</script>
