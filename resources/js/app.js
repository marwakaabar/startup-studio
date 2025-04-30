import './bootstrap'; 
import { createApp } from 'vue'; 
import { toast } from 'vue3-toastify'; 
import 'vue3-toastify/dist/index.css';  

import notifications from './components/notification/liste.vue';
import notificationsNavbar from './components/notification/notifications.vue';
import topsCards from './components/dashbord/top-cards.vue'; 
import avancementsDash from './components/dashbord/avancements.vue'; 
import listeFormations from './components/formation/liste.vue'; 
import detailsFormation from './components/formation/details.vue'; 
import addFormation from './components/formation/add.vue'; 
import ListeAgentsIA from './components/agentsIA/liste.vue';  
import addAgentsIA from './components/agentsIA/add.vue';
import detailsAgentsIA from './components/agentsIA/details.vue';

// App principale
const app = createApp({});  
app.component('liste-notif', notifications); 
app.component('top-cards-dashbord', topsCards); 
app.component('avancements-dashbord', avancementsDash); 
app.component('liste-formations', listeFormations); 
app.component('details-formation', detailsFormation); 
app.component('add-formation', addFormation);
app.component('liste-agents-ia', ListeAgentsIA);  
app.component('add-agent-ia', addAgentsIA); 
app.component('details-agent-ia', detailsAgentsIA); 

// Ajout de toast global 
app.config.globalProperties.$toast = Object.assign(toast, {   
  success: (msg, opts) => toast(msg, { type: "success", ...opts }),   
  error: (msg, opts) => toast(msg, { type: "error", ...opts }),   
  info: (msg, opts) => toast(msg, { type: "info", ...opts }),   
  warn: (msg, opts) => toast(msg, { type: "warn", ...opts }), 
});  
// Montages
app.mount('#app');

// Navbar à part
const navbar = createApp({});
navbar.component('notifications', notificationsNavbar);


navbar.mount('#navbar-nav');
