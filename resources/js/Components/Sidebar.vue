<template>
  <div id="sidebar" class="sidebar" :class="{ 'collapsed': !isSidebarOpen }">
    <ul class="sidebar-menu">
      <li>
        <a href="/admin/dashboard" @click.prevent="$emit('change-page', 'dashboard')" class="menu-item">
          <i class="fas fa-home"></i> <span v-if="isSidebarOpen">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="admin/users/coachs" @click.prevent="$emit('change-page', 'coachs')" class="menu-item">
          <i class="fas fa-chalkboard-teacher"></i> <span v-if="isSidebarOpen">Coachs</span>
        </a>
      </li>
      <li>
        <a href="admin/users/investors" @click.prevent="$emit('change-page', 'investors')" class="menu-item">
          <i class="fas fa-hand-holding-usd"></i> <span v-if="isSidebarOpen">Investisseurs</span>
        </a>
      </li>
      <li>
        <a href="admin/users/startups" @click.prevent="$emit('change-page', 'startups')" class="menu-item">
          <i class="fas fa-rocket"></i> <span v-if="isSidebarOpen">Startups</span>
        </a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isSidebarOpen = ref(true);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<style scoped>
.sidebar {
  width: 280px;
  position: fixed;
  top: 72px; /* Ajusté pour correspondre à la hauteur exacte de la navbar (py-3 = 0.75rem * 2 = 24px + contenu) */
  left: 0;
  height: calc(100vh - 72px); /* Utilisation de vh pour la hauteur totale de la fenêtre moins la navbar */
  background-color: white;
  color: #34495e;
  transition: width 0.3s;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
  z-index: 40; /* Ajusté pour être en dessous de la navbar qui est à z-50 */
  overflow-y: auto; /* Permet le défilement si le contenu est trop long */
  overflow-x: hidden;
}
.sidebar.collapsed {
  width: 80px;
}

.sidebar-header {
  padding: 15px;
  text-align: center;
  background-color: #f7f7f7;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.toggle-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.sidebar-menu {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.sidebar-menu li {
  padding: 10px 0;
}

.menu-item {
  color: #34495e;
  text-decoration: none;
  display: flex;
  align-items: center;
  padding: 10px 20px;
  transition: background-color 0.3s;
}

.menu-item i {
  margin-right: 10px;
  font-size: 18px;
}

.menu-item:hover {
  background-color: #f0f7ff;
}
</style>
