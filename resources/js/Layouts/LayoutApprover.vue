<script setup>
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

</script>

<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- Profile Section -->
      <div class="profile-section px-4 py-6 bg-orange-500 text-white">
        <div class="flex items-center space-x-3">
          <img src="/path-to-avatar.jpg" class="w-12 h-12 rounded-full" alt="User Avatar" />
          <div>
            <p class="text-sm">You're working as</p>
            <p class="font-bold">Rave C.</p>
            <p class="text-xs">Moderator</p>
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <slot name="navigation">
        <!-- fallback content: your default nav -->
      </slot>

      <!-- Logout Button -->
      <div class="logout-section px-6 pb-6">
        <Link :href="route('login_approver')" class="block w-full bg-red-500 text-center text-white py-2 rounded hover:bg-red-600">
          LOG OUT
        </Link>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <slot />
    </main>
  </div>
</template>


<style scoped>
.app-layout {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.sidebar {
  width: 250px;
  background-color: #ffffff;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-right: 1px solid #ddd;
}

/* .nav-button {
  width: 100%;
  text-align: left;
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  background-color: #f9f9f9;
  transition: background-color 0.2s;
}

.nav-button:hover {
  background-color: #ececec;
} */

.main-content {
  flex: 1;
  background-color: #f5f5f5;
  padding: 2rem;
  overflow-y: auto;
}
</style>