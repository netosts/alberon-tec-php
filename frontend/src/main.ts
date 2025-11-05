import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import Icon from './components/Icon.vue'

import './assets/styles/index.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Register global components
app.component('Icon', Icon)

app.mount('#app')
