import Vue from "vue"
import App from "./core/App"
import VueRouter from "vue-router"
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

require('./bootstrap')

Vue.use(VueRouter)

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

import Index from "./core/components/Index"
import Analytics from "./core/components/Analytics";

const routes = [
    {
        path: '/',
        component: Index
    },
    {
        path: '/analytics',
        component: Analytics
    }
]

const router = new VueRouter({ routes })

window.Vue = new Vue({
    router,
    render: h => h(App)
}).$mount('#app')
