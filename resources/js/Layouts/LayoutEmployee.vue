<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref } from 'vue';
  // import { route } from 'ZiggyVue'
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
                <br>
                <a href="/resident_term_condition">Terms & Conditions</a><br>

                <Link class="register-btn" :href="route('login')">LOG OUT</Link> <br>
            </div>
        </div>

        <!-- Navigation Bar -->
        <div class="navbar">
            <Link href="/employee/announcement">📱Post</Link>
            <Link :href="typeof route !== 'undefined' ? route('document_request_select_employee') : '/e_document_request_select'">Document</Link>
            <Link :href="typeof route !== 'undefined' ? route('notification_activities_employee') : '/e_notification_activities'">Notification</Link>
            <Link :href="typeof route !== 'undefined' ? route('profile_employee') : '/e_profile'">Profile</Link>
        </div>

        <!-- Slot (for dynamic content) -->
        <main class="container">
            <slot></slot>
        </main>
    </div>
</template>

<script>
    const menuVisible = ref(false);

    function toggleMenu() {
    menuVisible.value = !menuVisible.value;
    }

    function logout() {
    router.visit(route('login'));
    }

    function goTo(name) {
    router.visit(route(name));
    }

</script>

<style>

</style>