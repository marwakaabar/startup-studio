import './bootstrap';
import { createApp, defineAsyncComponent } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import 'quill/dist/quill.snow.css';

// App principale
const app = createApp({});

// Enregistrement des composants avec lazy loading
const components = {
  
  // Notifications
  'liste-notif': () => import('./components/notification/liste.vue'),
  // Dashboard coach
  'top-cards-dashbord': () => import('./components/dashbord/top-cards.vue'),
  'avancements-dashbord': () => import('./components/dashbord/avancements.vue'),
  // Formations
  'liste-formations': () => import('./components/formation/liste.vue'),
  'details-formation': () => import('./components/formation/details.vue'),
  'add-formation': () => import('./components/formation/add.vue'),
  // Agents IA
  'liste-agents-ia': () => import('./components/agentsIA/liste.vue'),
  'add-agent-ia': () => import('./components/agentsIA/add.vue'),
  'details-agent-ia': () => import('./components/agentsIA/details.vue'),
  // Forum
  'liste-forum': () => import('./components/forum/liste.vue'),
  'forum-card': () => import('./components/forum/ForumCard.vue'),
  'add-forum': () => import('./components/forum/add.vue'),
  'show-forum': () => import('./components/forum/show.vue'),
  'add-topic': () => import('./components/forum/addtopic.vue'),
  'show-topic': () => import('./components/forum/showTopic.vue'),
  //reports
  'liste-reports': () => import('./components/Admin/Reports/Index.vue'),
  // Admin
  'list-startups': () => import('./components/Admin/Users/Startups.vue'),
  'list-coachs': () => import('./components/Admin/Users/Coachs.vue'),
  'list-investors': () => import('./components/Admin/Users/Investors.vue'),
  // Auth
  'login-form': () => import('./components/auth/Login.vue'),
  'register-form': () => import('./components/auth/Register.vue'),
  'forgot-password-form': () => import('./components/auth/ForgotPassword.vue'),
  'reset-password-form': () => import('./components/auth/ResetPassword.vue'),
  'confirm-password-form': () => import('./components/auth/ConfirmPassword.vue'),
  'verify-email': () => import('./components/auth/Verify.vue'),
  'verify-email-link': () => import('./components/auth/VerifyEmail.vue'),
};

// Enregistrement des composants avec defineAsyncComponent
Object.entries(components).forEach(([name, component]) => {
  app.component(name, defineAsyncComponent(component));
});

// Importation et enregistrement du composant show-topic
import ShowTopic from './components/forum/showTopic.vue';
app.component('show-topic', ShowTopic);

// Importation et enregistrement des nouveaux composants
import ReactionPicker from './components/forum/ReactionPicker.vue';
import ReactionSummary from './components/forum/ReactionSummary.vue';


app.component('reaction-picker', ReactionPicker);
app.component('reaction-summary', ReactionSummary);

// Configuration des toasts
app.config.globalProperties.$toast = Object.assign(toast, {
  success: (msg, opts) => toast(msg, { type: "success", ...opts }),
  error: (msg, opts) => toast(msg, { type: "error", ...opts }),
  info: (msg, opts) => toast(msg, { type: "info", ...opts }),
  warn: (msg, opts) => toast(msg, { type: "warn", ...opts }),
});

// Montage de l'application principale
app.mount('#app');

// Application navbar
const navbar = createApp({});
navbar.component('notifications', defineAsyncComponent(() => 
  import('./components/notification/notifications.vue')
));
navbar.mount('#navbar-nav');

