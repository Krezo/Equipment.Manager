import { createApp } from "vue";
import * as VueRouter from "vue-router";
import store from "./store";

import axios from "axios";

import { CIcon } from "@coreui/icons-vue"
import {
  cilColorBorder,
  cilPlus,
  cilXCircle,
  cilCheckAlt
} from '@coreui/icons';

const icons = {
  cilColorBorder,
  cilPlus,
  cilXCircle,
  cilCheckAlt
}


import CoreUI from "@coreui/vue";

import App from "@/js/App.vue";
import AddEquipmentPage from "@/pages/AddEquipment.vue";
import EditEquipmentPage from "@/pages/EditEquipment.vue"
import Main from "@/pages/Main.vue"

const app = createApp(App);


const routes = [
  { path: '/', name: 'home', component: Main },
  { path: '/add-equipment', name: "add-equipment", component: AddEquipmentPage },
  { path: '/edit-equipment', name: "edit-equipment", component: EditEquipmentPage },
]

const router = VueRouter.createRouter({
  routes,
  history: VueRouter.createWebHistory()
})

app.use(store)
app.provide('icons', icons);
app.use(CoreUI);
app.component('CIcon', CIcon);
app.use(router);

app.config.globalProperties.$axios = axios;
app.mount('#app')