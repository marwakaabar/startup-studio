<template>
  <div id="sidebar" class="sidebar">
    <div class="sidebar-header">
      <h3>Startup Studio</h3>
      <button @click="toggleSidebar" class="toggle-btn">☰</button>
    </div>
    <ul class="sidebar-menu">
      
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
  width: 250px;
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  background-color: #2c3e50;
  color: #fff;
  transition: width 0.3s;
}

.sidebar-header {
  padding: 15px;
  text-align: center;
  background-color: #34495e;
}

.sidebar-menu {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.sidebar-menu li {
  padding: 10px 20px;
}

.menu-item {
  color: #ecf0f1;
  text-decoration: none;
  display: block;
  cursor: pointer;
}

.menu-item:hover {
  background-color: #16a085;
}
</style>
