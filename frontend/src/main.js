import { createApp } from 'vue'
import './style.css'
import store from './store'
import router from './router'
import App from './App.vue'
import axios from 'axios'


const app =createApp(App)
app.config.globalProperties.$axios = axios

app.use(store)
app.use(router)
app.mount('#app')
