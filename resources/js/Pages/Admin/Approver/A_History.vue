<template>
    <Head>
        <title>History</title>
    </Head>

    <!-- Full Screen Layout -->
    <div class="app-container">
        <!-- Green Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/ADMIN LOGO1.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <!-- Settings Dropdown -->
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
                        <Link href="#" class="settings-item" @click="closeSettings">Terms & Conditions</Link>
                        <Link href="#" class="settings-item" @click="logout">Sign Out</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area - Full Width -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img src="/assets/ADMIN.png" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{user.name || 'Unkown User'}}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToDocumentRequest"
                    >
                        📄 Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToRegisterRequest"
                    >
                        📝 Event Assistance Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                    >
                        📜 History
                    </Link>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- History Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>History</h2>
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
                                    <span class="filter-arrow" :class="{ rotated: showSortDropdown }">▼</span>
                                </button>
                                <div v-if="showSortDropdown" class="filter-dropdown-menu">
                                    <button @click="selectSort('newest')" :class="{ active: sortOption === 'newest' }">NEWEST</button>
                                    <button @click="selectSort('oldest')" :class="{ active: sortOption === 'oldest' }">OLDEST</button>
                                    <button @click="selectSort('relevant')" :class="{ active: sortOption === 'relevant' }">RELEVANT</button>
                                </div>
                            </div>
                            <div class="filter-dropdown-wrapper">
                                <button class="filter-dropdown-btn" @click="toggleFilterDropdown">
                                    {{ filterOption.toUpperCase() }}
                                    <span class="filter-arrow" :class="{ rotated: showFilterDropdown }">▼</span>
                                </button>
                                <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                    <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                    <button @click="selectFilter('approved')" :class="{ active: filterOption === 'approved' }">APPROVED</button>
                                    <button @click="selectFilter('rejected')" :class="{ active: filterOption === 'rejected' }">REJECTED</button>
                                </div>
                            </div>
                        </div>
                        <div class="filter-right">  
                            <div class="search-container">
                                <input 
                                    type="text" 
                                    v-model="searchQuery" 
                                    @input="performSearch"
                                    placeholder="SEARCH..." 
                                    class="search-input" 
                                />
                                <button class="search-btn" @click="performSearch">🔍</button>
                            </div>
                        </div>
                    </div>

                    <!-- History Table Container -->
                    <div class="users-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Profile</th>
                                    <th style="width: 180px;">Full Name</th>
                                    <th style="width: 100px;">Role</th>
                                    <th style="width: 200px;">Activity</th>
                                    <th style="width: 140px;">Action</th>
                                    <th style="width: 150px;">Approver</th>
                                    <th style="width: 110px;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="activity in filteredHistory" 
                                    :key="activity.id"
                                    class="user-row"
                                >
                                    <td style="text-align: center;">
                                        <img :src="activity.avatar" :alt="activity.name" class="user-avatar" />
                                    </td>
                                    <td class="user-name">{{ activity.name }}</td>
                                    <td style="text-align: center;">
                                        <span class="role-badge" :class="activity.role.toLowerCase()">
                                            {{ activity.role.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: left;">{{ activity.activity }}</td>
                                    <td style="text-align: center;">
                                        <span class="action-badge" :class="getActionClass(activity.action)">
                                            {{ activity.action.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">{{ activity.approver }}</td>
                                    <td style="text-align: center;">{{ activity.date }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- No history message -->
                        <div v-if="filteredHistory.length === 0" class="no-users">
                            <p>No history records found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

  // --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// map of role_id -> role_name (based on the table you provided)
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
  return id ? (roleMap[id] ?? `Role ${id}`) : 'Resident' // fallback to 'Resident' or whatever you prefer
})

// Reactive data
const showSettings = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')

// Sample history data - 3 document requests + 3 event assistance requests
const history = ref([
    // Document Requests
    {
        id: 1,
        name: 'John Rodriguez',
        role: 'Resident',
        activity: 'Barangay Clearance Request',
        action: 'Approved',
        approver: 'Justin N.',
        date: '09/28/2025',
        avatar: '/assets/PROFILE PIC 4.jpg',
        dateObj: new Date('2025-09-28')
    },
    {
        id: 2,
        name: 'Maria Santos',
        role: 'Resident',
        activity: 'Certificate of Residency Request',
        action: 'Approved',
        approver: 'Justin N.',
        date: '09/27/2025',
        avatar: '/assets/PROFILE PIC 3.jpg',
        dateObj: new Date('2025-09-27')
    },
    {
        id: 3,
        name: 'Roberto Cruz',
        role: 'Resident',
        activity: 'Barangay ID Request',
        action: 'Rejected',
        approver: 'Justin N.',
        date: '09/26/2025',
        avatar: '/assets/PROFILE PIC 7.jpg',
        dateObj: new Date('2025-09-26')
    },
    // Event Assistance Requests
    {
        id: 4,
        name: 'Roberto Cruz',
        role: 'Resident',
        activity: 'Venue Assistance - Wedding Reception',
        action: 'Approved',
        approver: 'Justin N.',
        date: '09/25/2025',
        avatar: '/assets/PROFILE PIC 2.jpg',
        dateObj: new Date('2025-09-25')
    },
    {
        id: 5,
        name: 'Ana Reyes',
        role: 'Resident',
        activity: 'Equipment Assistance - Youth Summit',
        action: 'Approved',
        approver: 'Justin N.',
        date: '09/24/2025',
        avatar: '/assets/PROFILE PIC 3.jpg',
        dateObj: new Date('2025-09-24')
    },
    {
        id: 6,
        name: 'Carlos Mendoza',
        role: 'Resident',
        activity: 'Personnel Assistance - Senior Gathering',
        action: 'Rejected',
        approver: 'Justin N.',
        date: '09/23/2025',
        avatar: '/assets/PROFILE PIC.jpg',
        dateObj: new Date('2025-09-23')
    }
])

// Computed filtered history
const filteredHistory = computed(() => {
    let filtered = [...history.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) ||
            item.activity.toLowerCase().includes(query) ||
            item.action.toLowerCase().includes(query) ||
            item.approver.toLowerCase().includes(query)
        )
    }

    // Action filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.action.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'relevant') {
        // Sort by action priority: Rejected > Approved
        const priority = { 'rejected': 1, 'approved': 2 }
        filtered.sort((a, b) => {
            const aPriority = priority[a.action.toLowerCase()] || 3
            const bPriority = priority[b.action.toLowerCase()] || 3
            return aPriority - bPriority
        })
    }

    return filtered
})

// Methods
const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const toggleSortDropdown = () => {
    showSortDropdown.value = !showSortDropdown.value
    showFilterDropdown.value = false
}

const toggleFilterDropdown = () => {
    showFilterDropdown.value = !showFilterDropdown.value
    showSortDropdown.value = false
}

const selectSort = (option) => {
    sortOption.value = option
    showSortDropdown.value = false
}

const selectFilter = (option) => {
    filterOption.value = option
    showFilterDropdown.value = false
}

const logout = () => {
    showSettings.value = false
    router.visit(route('login_approver'))
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const getActionClass = (action) => {
    const actionLower = action.toLowerCase()
    if (actionLower === 'approved') return 'approved'
    if (actionLower === 'rejected') return 'rejected'
    return 'default'
}

const navigateToDocumentRequest = () => {
    router.visit(route('document_request_approver'))
}

const navigateToRegisterRequest = () => {
    router.visit(route('event_request_approver'))
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showFilterDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Full screen layout reset */
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
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header Bar */
.header-bar {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    color: white;
    padding: 5px 0;
    box-shadow: 0 4px 15px rgba(35, 150, 64, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000;
}

.header-content {
    max-width: none;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 10px;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 12px;
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
    border: 1px solid rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-item {
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
    cursor: pointer;
    font-weight: 500;
}

.settings-item:hover {
    background: #e8f8ed;
    color: #239640;
}

.settings-item:last-child {
    border-bottom: none;
}

/* Main Layout */
.main-layout {
    flex: 1;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 25px;
    width: 100%;
    max-width: none;
    margin: 0;
    margin-top: 70px;
    padding: 25px 30px;
}

/* Sidebar */
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
    box-shadow: 0 8px 25px rgba(35, 150, 64, 0.3);
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
    background: #ff8c42;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    backdrop-filter: blur(10px);
    margin-top: 5px;
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
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    font-weight: 500;
}

.nav-item:last-child {
    border-bottom: none;
}

.nav-item:hover {
    background: #e8f8ed;
    transform: translateX(3px);
}

.nav-item.active {
    background: linear-gradient(135deg, #e8f8ed, #d4f1dd);
    color: #239640;
    font-weight: 600;
    border-left: 4px solid #239640;
}

/* Content Area */
.content-area {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    flex: 1;
    border: 1px solid rgba(0,0,0,0.05);
}

.users-header {
    background: #ff8c42;
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.users-title h2 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

/* Filter Section */
.filter-section {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
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
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    background: white;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 120px;
    justify-content: space-between;
}

.filter-dropdown-btn:hover {
    border-color: #239640;
}

.filter-arrow {
    font-size: 10px;
    transition: transform 0.3s ease;
}

.filter-arrow.rotated {
    transform: rotate(180deg);
}

.filter-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 180px;
    z-index: 1000;
    margin-top: 5px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.1);
}

.filter-dropdown-menu button {
    display: block;
    width: 100%;
    padding: 10px 15px;
    background: none;
    border: none;
    text-align: left;
    color: #333;
    cursor: pointer;
    transition: background 0.2s;
    font-weight: 500;
    font-size: 12px;
}

.filter-dropdown-menu button:hover {
    background: #e8f8ed;
}

.filter-dropdown-menu button.active {
    background: #e8f8ed;
    color: #239640;
    font-weight: 600;
}

.filter-right {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-container {
    display: flex;
    gap: 5px;
    background: white;
    border-radius: 8px;
    padding: 2px;
    border: 1px solid #ddd;
}

.search-input {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    width: 220px;
    font-size: 12px;
    outline: none;
}

.search-btn {
    background: #f8f9fa;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
}

.search-btn:hover {
    background: #e9ecef;
}

/* Users Container */
.users-container {
    padding: 0;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.users-table thead {
    background: #9c9b9b;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.users-table th {
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle;
}

.users-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
}

.users-table tbody tr:hover {
    background: linear-gradient(135deg, #fafbfc, #f8f9fa);
}

.users-table td {
    padding: 12px 8px;
    font-size: 13px;
    color: #555;
    vertical-align: middle;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: block;
    margin: 0 auto;
}

.user-name {
    font-weight: 600;
    color: #333;
    text-align: left;
}

.role-badge {
    font-size: 11px;
    padding: 5px 12px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    color: white;
}

.role-badge.resident {
    background: #239640;
}

.role-badge.official {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}

.action-badge {
    font-size: 11px;
    padding: 5px 12px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    color: white;
}

.action-badge.approved {
    background: linear-gradient(135deg, #10b981, #059669);
}

.action-badge.rejected {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.action-badge.default {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.no-users {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.no-users p {
    font-size: 16px;
    color: #999;
}

/* Custom Scrollbar */
.users-container::-webkit-scrollbar {
    width: 6px;
}

.users-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Responsive */
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
        padding-right: 0;
    }
    
    .filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input {
        width: 100%;
    }
    
    .users-table {
        font-size: 11px;
    }
    
    .users-table th,
    .users-table td {
        padding: 8px 4px;
    }
}
</style>