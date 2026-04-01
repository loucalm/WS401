import { createApp } from 'vue'

import './assets/styles/style.css' 

import App from './App.vue'
import router from './router'

const app = createApp(App)

// import { createPinia } from 'pinia'
import i18n from './i18n'
// app.use(createPinia())
app.use(i18n)
app.use(router)

app.mount('#app')