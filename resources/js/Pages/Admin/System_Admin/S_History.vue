<template>
    <Head>
        <title>History</title>
    </Head>

    <!-- Full Screen Layout -->
    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
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
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{displayRole}}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToDashboard"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        Dashboard
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToRegisterRequest"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Register Request
                    </Link>
                    <Link 
                        :href="route('registration_employee')" 
                        class="nav-item"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Register Official
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToReport"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Report
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
                                    <svg class="filter-arrow" :class="{ rotated: showSortDropdown }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
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
                                    <svg class="filter-arrow" :class="{ rotated: showFilterDropdown }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                    <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                    <button @click="selectFilter('approved')" :class="{ active: filterOption === 'approved' }">APPROVED</button>
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
                                <button class="search-btn" @click="performSearch">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- History Table Container -->
                    <div class="users-container">
                        <!-- Loading state -->
                        <div v-if="isLoading" class="loading-state">
                            <p>Loading history...</p>
                        </div>
                        
                        <!-- History table -->
                        <table v-else class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Profile</th>
                                    <th style="width: 180px; text-align: left; padding-left: 20px;">Full Name</th>
                                    <th style="width: 200px; text-align: center;">Role</th>
                                    <th style="width: 250px; text-align: left; padding-left: 25px;">Activity</th>
                                    <th style="width: 140px; text-align: center;">Action</th>
                                    <th style="width: 150px; text-align: center;">Admin</th>
                                    <th style="width: 110px; text-align: center;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="activity in filteredHistory" 
                                    :key="activity.id"
                                    class="user-row"
                                >
                                    <td style="text-align: center; padding: 15px 8px;">
                                        <img :src="activity.avatar" :alt="activity.name" class="user-avatar" />
                                    </td>
                                    <td style="text-align: left; padding: 15px 12px 15px 20px;" class="user-name">{{ activity.name }}</td>
                                    <td style="text-align: center; padding: 15px 15px;">
                                        <span class="role-badge" :class="getRoleClass(activity.role)">
                                            {{ activity.role.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: left; padding: 15px 12px 15px 25px;">{{ activity.activity }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">
                                        <span class="action-badge" :class="getActionClass(activity.action)">
                                            {{ activity.action.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; padding: 15px 12px;">{{ activity.moderator }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">{{ activity.date }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- No history message -->
                        <div v-if="!isLoading && filteredHistory.length === 0" class="no-users">
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
import axios from 'axios'

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
const isLoading = ref(false)

// History data - will be populated from backend
const history = ref([])

// Format date helper - converts UTC to Philippines timezone (Asia/Manila, UTC+8)
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    // Parse the date string (assumes it's in UTC from server)
    const date = new Date(dateStr)
    if (isNaN(date.getTime())) return ''
    
    // Use Intl.DateTimeFormat to format in Philippines timezone
    const formatter = new Intl.DateTimeFormat('en-US', {
      timeZone: 'Asia/Manila',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
    
    const parts = formatter.formatToParts(date)
    const month = parts.find(p => p.type === 'month').value
    const day = parts.find(p => p.type === 'day').value
    const year = parts.find(p => p.type === 'year').value
    
    return `${month}/${day}/${year}`
  } catch (e) {
    return ''
  }
}

// Get admin/moderator name helper
const getAdminName = (req) => {
  // First try to get from approver_info (from backend)
  if (req.approver_info && req.approver_info.name) {
    return req.approver_info.name
  }
  // Fallback: if there's an approver ID and it matches current user, use current user's name
  if (req.fk_approver_id && user.value?.name) {
    const nameParts = user.value.name.split(' ')
    if (nameParts.length > 0) {
      return nameParts[0] + (nameParts.length > 1 ? ' ' + nameParts[nameParts.length - 1].charAt(0) + '.' : '')
    }
    return user.value.name
  }
  // For system admin actions, use current user's name
  if (user.value?.name) {
    const nameParts = user.value.name.split(' ')
    if (nameParts.length > 0) {
      return nameParts[0] + (nameParts.length > 1 ? ' ' + nameParts[nameParts.length - 1].charAt(0) + '.' : '')
    }
    return user.value.name
  }
  return 'N/A'
}

// Fetch history data from backend
const fetchHistory = async () => {
  isLoading.value = true
  try {
    // Build URL for registration requests
    const buildRegUrl = (status) => {
      return `/s_register_request?status=${status}`
    }

    // Fetch approved registration requests (rejected ones are deleted, so we only fetch approved)
    const approvedRegRes = await axios.get(buildRegUrl('Approved'), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    // Extract requests from JSON response
    let approvedRegRequests = approvedRegRes?.data?.registration_requests || []
    
    // Debug logging
    console.log('Approved registration requests found:', approvedRegRequests.length)

    const historyItems = []

    // Map approved registration requests
    approvedRegRequests.forEach((req) => {
      const admin = getAdminName(req)
      
      let profileImg = req.profile_pic || '/assets/DEFAULT.jpg'
      if (profileImg && !profileImg.startsWith('http') && !profileImg.startsWith('/')) {
        profileImg = `/storage/${profileImg}`.replace('//', '/')
      }

      const roleId = req.role_id || 1
      const role = roleMap[roleId] || 'Resident'

      historyItems.push({
        id: req.registration_request_id || req.id,
        name: req.name || `${req.first_name || ''} ${req.last_name || ''}`.trim() || 'Unknown',
        role: role,
        activity: 'Registration Request',
        action: 'Approved',
        moderator: admin,
        date: formatDate(req.updated_at || req.created_at),
        dateObj: new Date(req.updated_at || req.created_at || new Date()),
        avatar: profileImg
      })
    })

    history.value = historyItems
  } catch (error) {
    console.error('Error fetching history:', error)
    history.value = []
  } finally {
    isLoading.value = false
  }
}

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
            item.moderator.toLowerCase().includes(query)
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
    router.visit(route('login_admin'))
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const getActionClass = (action) => {
    const actionLower = action.toLowerCase()
    if (actionLower === 'approved') return 'approved'
    if (actionLower === 'rejected') return 'rejected'
    if (actionLower === 'deleted') return 'deleted'
    if (actionLower === 'restricted') return 'restricted'
    if (actionLower === 'password changed') return 'password-changed'
    return 'default'
}

const getRoleClass = (role) => {
    const roleLower = role.toLowerCase().replace(/\s+/g, '-')
    // Check if it's a resident (role_id 1)
    if (roleLower === 'resident') return 'resident'
    // All other roles are officials
    return 'official'
}

const navigateToDashboard = () => {
    router.visit(route('dashboard_admin'))
}

const navigateToUsers = () => {
    router.visit(route('users_admin'))
}

const navigateToRegisterRequest = () => {
    router.visit(route('register_request_admin'))
}

const navigateToReport = () => {
    router.visit(route('report_admin'))
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
    fetchHistory()
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
    flex-shrink: 0;
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
    transition: transform 0.3s ease;
    flex-shrink: 0;
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
    background: transparent;
    border: none;
    cursor: pointer;
    color: #6b7280;
    padding: 8px 12px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-btn:hover {
    color: #ff8c42;
}

.search-btn svg {
    width: 18px;
    height: 18px;
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
    background: #2e2e2e;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.users-table th {
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle;
}

.users-table th:nth-child(1),
.users-table th:nth-child(3),
.users-table th:nth-child(5),
.users-table th:nth-child(6),
.users-table th:nth-child(7) {
    text-align: center;
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
    text-align: left;
}

.users-table td:nth-child(1),
.users-table td:nth-child(3),
.users-table td:nth-child(5),
.users-table td:nth-child(6),
.users-table td:nth-child(7) {
    text-align: center;
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

/* Default style for any role badge that doesn't match above classes */
.role-badge:not(.resident):not(.official) {
    background: linear-gradient(135deg, #6b7280, #4b5563);
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

.action-badge.deleted {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.action-badge.restricted {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.action-badge.password-changed {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.action-badge.default {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.loading-state {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.loading-state p {
    font-size: 16px;
    color: #999;
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
    background: #999;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb:hover {
    background: #777;
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