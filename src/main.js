import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import App from './App.vue'
import MarkdownIt from 'markdown-it'

export const api_url = "/api/"
let md = new MarkdownIt()
export const Md = md
const app = createApp(App)
app.use(VueAxios, axios )
app.mount('#app')
