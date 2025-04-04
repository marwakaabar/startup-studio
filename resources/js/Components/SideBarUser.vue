<template>
  <div id="sidebar" class="sidebar" :class="{ 'collapsed': !isSidebarOpen }">
    <ul class="sidebar-menu">
      <li>
        <Link href="/forums" class="menu-item">
          <i class="fas fa-th-large"></i> Forums & groupes
        </Link>
      </li>
      <li>
       <!--<Link href="/forums/create" class="menu-item">
          <i class="fas fa-plus"></i> Créer un Forum
        </Link>--> 
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
  width: 80px; /* Largeur de la sidebar lorsqu'elle est réduite */
}

.sidebar-header {
  padding: 15px;
  text-align: center;
  background-color: #f7f7f7;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.sidebar-menu {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.sidebar-menu li {
  padding: 10px 0; /* Espacement vertical entre les éléments */
}

.menu-item {
  color: #34495e;
  text-decoration: none;
  display: block;
  padding: 10px 20px; /* Espacement interne */
  transition: background-color 0.3s;
}

.menu-item:hover {
  background-color: #f0f7ff; /* Couleur de fond au survol */
}

.sidebar-section {
  margin: 15px 0;
}

.sidebar-title {
  font-size: 18px;
  color: #34495e;
  margin-bottom: 10px;
  padding-bottom: 5px;
  border-bottom: 1px solid #eee;
}

.sidebar-group {
  display: flex;
  align-items: center;
  padding: 10px;
  background-color: #f9f9f9;
  border-radius: 5px;
  margin-bottom: 10px;
}

.group-image {
  width: 40px;
  height: 40px;
  border-radius: 5px;
  object-fit: cover;
  margin-right: 10px;
}

.group-info {
  flex: 1;
}

.group-name {
  font-weight: 500;
  color: #34495e;
}

.group-members {
  font-size: 12px;
  color: #95a5a6;
}

.sidebar-tag {
  display: inline-block;
  padding: 5px 10px;
  background-color: #f7f7f7;
  border-radius: 15px;
  color: #95a5a6;
  font-size: 13px;
  margin-right: 5px;
  margin-bottom: 5px;
  transition: all 0.3s;
}

.sidebar-tag:hover {
  background-color: #f0f7ff;
  color: #3498db;
}
</style>

