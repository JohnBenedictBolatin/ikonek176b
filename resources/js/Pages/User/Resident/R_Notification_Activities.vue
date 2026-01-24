<template>
    <Head>
        <title>Notifications</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
                        <button type="button" class="settings-item" @click="openTerms">Terms & Conditions</button>
                        <Link href="#" class="settings-item" @click="logout">Sign Out</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img :src="profilePictureUrl" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user?.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'posts' }"
                        @click="navigateToPosts"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        Posts
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'documents' }"
                        @click="navigateToDocuments"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'events' }"
                        @click="navigateToEvents"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                        :class="{ active: activeTab === 'notifications' }"
                        @click.prevent="navigateToNotifications"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        Notifications
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'profile' }"
                        @click="navigateToProfile"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Profile
                    </Link>
                </div>

                <button class="faq-btn" @click="openFAQ">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                    FAQs & Help Center
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="main-content">
                    <!-- Header -->
                    <div class="notification-header">
                        <div class="header-title">
                            <h2>Notifications</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Filter Bar -->
                    <div class="filter-section">
                        <div class="filter-left">
                            <span class="filter-label">Filter by</span>
                            <div class="filter-dropdown-wrapper">
                                <button class="filter-dropdown-btn" @click="toggleSortDropdown">
                                    {{ sortOption.toUpperCase() }}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="filter-arrow" :class="{ rotated: showSortDropdown }">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div v-if="showSortDropdown" class="filter-dropdown-menu">
                                    <button @click="selectSort('newest')" :class="{ active: sortOption === 'newest' }">NEWEST</button>
                                    <button @click="selectSort('oldest')" :class="{ active: sortOption === 'oldest' }">OLDEST</button>
                                </div>
                            </div>
                        </div>
                        <div class="filter-right">
                            <div class="search-container">
                                <input 
                                    type="text" 
                                    v-model="searchQuery" 
                                    placeholder="SEARCH..." 
                                    class="search-input"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Content -->
                    <div class="notifications-container">
                        <div class="activities-list">
                            <!-- Loading State -->
                            <div v-if="isLoading" class="loading-state">
                                <p>Loading notifications...</p>
                            </div>

                            <!-- Notifications List -->
                            <div v-else>
                                <div
                                    v-for="activity in filteredActivities"
                                    :key="activity.id"
                                    class="notification-card activity-card"
                                    :class="{ 'unread': !activity.is_read }"
                                    @click="handleNotificationClick(activity)"
                                >
                                    <img :src="activity.avatar" alt="User" class="notif-avatar" />
                                    <div class="notif-content">
                                        <p class="notif-text">
                                            <strong>{{ activity.user }}</strong>
                                            <span v-if="activity.others"> and {{ activity.others }} others</span>
                                            {{ activity.action }}
                                        </p>
                                        <span class="notif-time">{{ activity.time }}</span>
                                    </div>
                                    <div v-if="!activity.is_read" class="unread-indicator"></div>
                                </div>

                                <!-- Mark All as Read Button -->
                                <div v-if="activities.length > 0 && activities.some(a => !a.is_read)" class="mark-all-read">
                                    <button class="mark-read-btn" @click="markAllAsRead">
                                        Mark all as read
                                    </button>
                                </div>

                                <div v-if="!isLoading && filteredActivities.length === 0" class="no-notifications">
                                    <p>No notifications found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Terms & Conditions Modal -->
    <TermsModal :open="showTerms" @close="closeTerms" />
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import TermsModal from '@/Components/TermsModal.vue'

// --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// map of role_id -> role_name
const roleMap = {
  1: 'Resident',
  2: 'Barangay Captain',
  3: 'Barangay Secretary',
  4: 'Barangay Treasurer',
  5: 'Barangay Kagawad',
  6: 'Sangguniang Kabataan Chairman',
  7: 'Sangguniang Kabataan Kagawad',
  9: 'System Admin',
}

// computed display role (safe if user is null)
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  const role = id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
  return role.toUpperCase()
})

// Profile picture URL
const profilePictureUrl = computed(() => {
  if (user.value?.profile_pic) {
    const pic = user.value.profile_pic
    if (pic.startsWith('http://') || pic.startsWith('https://') || pic.startsWith('/')) {
      return pic
    }
    return '/storage/' + pic.replace(/^\/+/, '')
  }
  return '/assets/PROFILE PIC.jpg'
})

const showSettings = ref(false)
const activeTab = ref('notifications')
const sortOption = ref('newest')
const searchQuery = ref('')
const activities = ref([])
const isLoading = ref(false)
const showSortDropdown = ref(false)

// Fetch notifications from API
const fetchNotifications = async () => {
    try {
        isLoading.value = true
        const response = await axios.get('/api/notifications')
        if (response.data.success) {
            activities.value = response.data.notifications.map(notification => ({
                id: notification.id,
                user: notification.user,
                others: notification.others || null,
                action: notification.action,
                avatar: notification.avatar,
                time: notification.time,
                timestamp: new Date(notification.timestamp),
                is_read: notification.is_read,
                type: notification.type,
                post_id: notification.post_id || null,
                post_category: notification.post_category || null // 'Announcement' or 'Discussion'
            }))
        } else {
            activities.value = []
        }
    } catch (error) {
        console.error('Error fetching notifications:', error)
        activities.value = []
    } finally {
        isLoading.value = false
    }
}

// Handle notification click - mark as read and navigate to post if available
const handleNotificationClick = async (activity) => {
    if (!activity.is_read) {
        await markAsRead(activity.id)
    }
    
    // Navigate to post if post_id is available
    if (activity.post_id) {
        // Determine which route to use based on post category
        const category = activity.post_category || 'Discussion' // Default to Discussion if not specified
        const routeName = category === 'Announcement' 
            ? 'announcement_resident' 
            : 'discussion_resident'
        
        // Navigate to the appropriate page with post_id as query parameter
        const url = route(routeName) + `?post=${activity.post_id}`
        router.visit(url, {
            preserveScroll: false
        })
    }
}

// Mark notification as read
const markAsRead = async (notificationId) => {
    try {
        await axios.put(`/api/notifications/${notificationId}/read`)
        // Update the local state
        const notification = activities.value.find(n => n.id === notificationId)
        if (notification) {
            notification.is_read = true
        }
    } catch (error) {
        console.error('Error marking notification as read:', error)
    }
}

// Mark all notifications as read
const markAllAsRead = async () => {
    try {
        await axios.put('/api/notifications/mark-all-read')
        // Update all notifications in local state
        activities.value.forEach(notification => {
            notification.is_read = true
        })
    } catch (error) {
        console.error('Error marking all notifications as read:', error)
    }
}

// Filtered Activities
const filteredActivities = computed(() => {
    let filtered = [...activities.value]

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(activity => 
            activity.user.toLowerCase().includes(query) ||
            activity.action.toLowerCase().includes(query)
        )
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.timestamp - a.timestamp)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.timestamp - b.timestamp)
    }

    return filtered
})


const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

// Terms & Conditions modal
const showTerms = ref(false)
const openTerms = () => {
    showSettings.value = false
    showTerms.value = true
}
const closeTerms = () => {
    showTerms.value = false
}

const logout = () => {
    showSettings.value = false
    // Properly logout by calling the logout endpoint
    router.post('/logout', {}, {
        onSuccess: () => {
            // Clear any local storage or session storage if needed
            if (typeof window !== 'undefined') {
                localStorage.clear()
                sessionStorage.clear()
            }
            // Redirect to login page after successful logout
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onError: () => {
            // Even if logout fails, redirect to login
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onFinish: () => {
            // Ensure we redirect even if something goes wrong
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        }
    })
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_resident'))
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_resident'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_resident'))
}

const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_activities_resident'))
}

const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_resident'))
}

const toggleSortDropdown = () => {
    showSortDropdown.value = !showSortDropdown.value
}

const selectSort = (option) => {
    sortOption.value = option
    showSortDropdown.value = false
}

const openFAQ = () => {
    router.visit(route('help_center_resident'))
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'notifications'
    fetchNotifications()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.app-container {
    min-height: 100vh;
    width: 100vw;
    background: url('/assets/BG MAIN.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    flex-direction: column;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header-bar {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    color: white;
    padding: 5px 0;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000;
}

.header-content {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 10px;
}

.header-logo {
    width: 180px;
    height: 60px;
    padding: 1px;
}

.header-actions {
    position: relative;
}

.settings-btn-img {
    margin-right: 30px;
    width: 30px;
    cursor: pointer;
    transition: transform 0.2s;
}

.settings-btn-img:hover {
    transform: scale(1.1);
}

.settings-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 200px;
    z-index: 1000;
    margin-top: 10px;
    overflow: hidden;
}

.settings-item {
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    font-weight: 500;
}

.settings-item:hover {
    background: #fff7ef;
    color: #ff8c42;
}

.main-layout {
    flex: 1;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 25px;
    margin-top: 70px;
    padding: 25px 30px;
}

.sidebar {
    background: transparent;
    padding-right: 20px;
}

.profile-card {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    border-radius: 15px;
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.3);
    border: 1px solid rgba(255,255,255,0.2);
}

.profile-avatar {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.profile-name {
    font-weight: 700;
    font-size: 17px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.profile-role {
    font-size: 12px;
    background: #239640;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    backdrop-filter: blur(10px);
    text-transform: uppercase;
}

.nav-menu {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.05);
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    font-weight: 500;
}

.nav-icon {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    flex-shrink: 0;
}

.nav-item:last-child {
    border-bottom: none;
}

.nav-item:hover {
    background: #fff7ef;
    transform: translateX(3px);
}

.nav-item.active {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    color: #ff8c42;
    font-weight: 600;
    border-left: 4px solid #ff8c42;
}

.faq-btn {
    width: 100%;
    background: #ff8c42;
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.faq-btn:hover {
    background: #ff7a28;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.content-area {
    display: flex;
    flex-direction: column;
}

.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.notification-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.header-title {
    flex: 1;
}

.header-title h2 {
    color: white;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
}

.header-icon {
    display: flex;
    align-items: center;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
}

.filter-section {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.filter-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.filter-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.filter-dropdown-wrapper {
    position: relative;
}

.filter-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-dropdown-btn:hover {
    border-color: #ff8c42;
    background: #fff7ef;
}

.filter-arrow {
    width: 14px;
    height: 14px;
    transition: transform 0.2s;
}

.filter-arrow.rotated {
    transform: rotate(180deg);
}

.filter-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 4px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 100;
    min-width: 120px;
    overflow: hidden;
}

.filter-dropdown-menu button {
    display: block;
    width: 100%;
    padding: 10px 15px;
    text-align: left;
    border: none;
    background: white;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s;
    color: #333;
}

.filter-dropdown-menu button:hover {
    background: #fff7ef;
    color: #ff8c42;
}

.filter-dropdown-menu button.active {
    background: #fff7ef;
    color: #ff8c42;
}

.search-container {
    position: relative;
}

.filter-right {
    display: flex;
    gap: 15px;
}

.search-input {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 250px;
    font-size: 12px;
}

.notifications-container {
    padding: 0;
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.activities-list {
    display: flex;
    flex-direction: column;
}

.notification-card {
    padding: 20px 25px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
}

.notification-card:hover {
    background: #fafbfc;
}

.activity-card {
    display: flex;
    gap: 15px;
    align-items: start;
    cursor: pointer;
    position: relative;
}

.notif-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.notif-content {
    flex: 1;
}

.notif-text {
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
    line-height: 1.5;
}

.notif-text strong {
    font-weight: 700;
}

.notif-time {
    font-size: 12px;
    color: #999;
}


.no-notifications {
    padding: 60px 40px;
    text-align: center;
    color: #999;
}

.no-notifications p {
    font-size: 16px;
}


    /* Notification Styles */
    .loading-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 16px;
    }

    .activity-card.unread {
        background: linear-gradient(135deg, rgba(255, 140, 66, 0.05), rgba(255, 140, 66, 0.02));
        border-left: 3px solid #ff8c42;
    }

    .activity-card.unread:hover {
        background: linear-gradient(135deg, rgba(255, 140, 66, 0.08), rgba(255, 140, 66, 0.04));
    }

    .unread-indicator {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 8px;
        height: 8px;
        background: #ff8c42;
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
    }

    .mark-all-read {
        text-align: center;
        padding: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .mark-read-btn {
        background: linear-gradient(135deg, #ff8c42, #ff7a28);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
    }

    .mark-read-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
    }

/* Scrollbar Styles */
.notifications-container::-webkit-scrollbar {
    width: 6px;
}

.notifications-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.notifications-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.notifications-container::-webkit-scrollbar-thumb:hover {
    background: #666;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .main-layout {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
    }

    .sidebar {
        order: 2;
    }

    .filter-section {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }

    .search-input {
        width: 100%;
    }

}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .profile-card {
        padding: 15px;
    }

    .notification-card {
        padding: 15px 20px;
    }
}
</style>