<script setup>
import { Link, router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
// import { route } from 'ZiggyVue'

const menuVisible = ref(false)

function toggleMenu() {
  menuVisible.value = !menuVisible.value
}

function logout() {
  // Properly logout by calling the logout endpoint (no need for Ziggy route here)
  router.post('/logout', {}, {
    onSuccess: () => {
      if (typeof window !== 'undefined') {
        localStorage.clear()
        sessionStorage.clear()
      }
      router.visit(route('login'), {
        replace: true,
        preserveState: false,
        preserveScroll: false,
      })
    },
    onError: () => {
      router.visit(route('login'), {
        replace: true,
        preserveState: false,
        preserveScroll: false,
      })
    },
    onFinish: () => {
      router.visit(route('login'), {
        replace: true,
        preserveState: false,
        preserveScroll: false,
      })
    },
  })
}

function goTo(name) {
  router.visit(route(name))
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="header">
      <span>📱</span> iKONEK176B
    </div>

    <!-- Menu -->
    <div class="menu-container">
      <div class="dots" @click="toggleMenu">⋮</div>
      <div class="dropdown" v-show="menuVisible">
        <Link :href="route('help_center_resident')">Help Center</Link>
        <br />
        <a href="/resident_term_condition">Terms & Conditions</a><br />

        <a href="#" class="register-btn" @click.prevent="logout">LOG OUT</a> <br />
      </div>
    </div>

    <!-- Navigation Bar -->
    <div class="navbar">
      <Link :href="route('announcement_resident')">📱Post</Link>
      <Link :href="route('document_request_select_resident')">Document</Link>
      <Link :href="route('notification_activities_resident')">Notification</Link>
      <Link :href="route('profile_resident')">Profile</Link>
    </div>

    <!-- Slot (for dynamic content) -->
    <main class="container">
      <slot></slot>
    </main>
  </div>
</template>

<style>

</style>