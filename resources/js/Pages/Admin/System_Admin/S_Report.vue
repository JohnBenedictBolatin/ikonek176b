<template>
    <Head>
        <title>Report</title>
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
                        📊 Dashboard
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToUsers"
                    >
                        👥 Users
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToHistory"
                    >
                        📜 History
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToRegisterRequest"
                    >
                        📝 Register Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                    >
                        🚩 Report
                    </Link>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Report Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Reported Posts</h2>
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
                                    <button @click="selectFilter('resident')" :class="{ active: filterOption === 'resident' }">RESIDENT</button>
                                    <button @click="selectFilter('official')" :class="{ active: filterOption === 'official' }">OFFICIAL</button>
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

                    <!-- Reports Container -->
                    <div class="reports-container">
                        <div 
                            v-for="report in filteredReports" 
                            :key="report.id"
                            class="report-card"
                        >
                            <!-- Reported Post Section -->
                            <div class="reported-post-section">
                                <div class="section-label">
                                    <span class="flag-icon">🚩</span>
                                    <span>Reported Post</span>
                                </div>
                                <div class="post-preview">
                                    <div class="post-author-info">
                                        <img :src="report.postAuthorAvatar" :alt="report.postAuthor" class="author-avatar" />
                                        <div class="author-details">
                                            <h3 class="author-name">{{ report.postAuthor }}</h3>
                                            <span class="role-badge" :class="report.postAuthorRole.toLowerCase()">
                                                {{ report.postAuthorRole.toUpperCase() }}
                                            </span>
                                            <p class="post-date">Posted: {{ report.postDate }}</p>
                                        </div>
                                    </div>
                                    <div class="post-content-preview">
                                        <h4 class="post-title">{{ report.postTitle }}</h4>
                                        <p class="post-text">{{ report.postContent }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Reporter Section -->
                            <div class="reporter-section">
                                <div class="section-label">
                                    <span class="user-icon">👤</span>
                                    <span>Reported By</span>
                                </div>
                                <div class="reporter-info">
                                    <img :src="report.reportedByAvatar" :alt="report.reportedBy" class="reporter-avatar" />
                                    <div class="reporter-details">
                                        <h4 class="reporter-name">{{ report.reportedBy }}</h4>
                                        <p class="report-date">Reported: {{ report.reportDate }}</p>
                                    </div>
                                </div>
                                <div class="report-reason-box">
                                    <div class="reason-label">Reason:</div>
                                    <div class="reason-badge">{{ report.reportReason }}</div>
                                    <div class="reason-details">{{ report.reportDetails }}</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button @click="openModal(report, 'keep')" class="keep-btn">
                                    ✓ Keep Post
                                </button>
                                <button @click="openModal(report, 'delete')" class="delete-btn">
                                    ✕ Remove Post
                                </button>
                            </div>
                        </div>

                        <!-- No reports message -->
                        <div v-if="filteredReports.length === 0" class="no-reports">
                            <div class="no-reports-icon">🔍</div>
                            <p>No flagged posts found matching your criteria.</p>
                            <p class="no-reports-subtext">All posts are following community guidelines.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Popup -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">✕</button>

                <div class="modal-content">
                    <h2 class="modal-title">
                        {{ modalAction === 'keep' ? 'Keep Reported Post?' : 'Remove Reported Post?' }}
                    </h2>
                    <p class="modal-message">
                        Would you like to <span class="modal-action-text">{{ modalAction }}</span> this reported post?
                    </p>
                    <div class="modal-report-info">
                        <p><strong>Post Author:</strong> {{ selectedReport?.postAuthor }}</p>
                        <p><strong>Post Title:</strong> {{ selectedReport?.postTitle }}</p>
                        <p><strong>Reported By:</strong> {{ selectedReport?.reportedBy }}</p>
                        <p><strong>Reason:</strong> {{ selectedReport?.reportReason }}</p>
                    </div>
                    <div class="modal-warning" v-if="modalAction === 'delete'">
                        <span class="warning-icon">⚠️</span>
                        <span>This action will permanently remove the post from the discussion board and notify the author.</span>
                    </div>
                    <div class="modal-info" v-if="modalAction === 'keep'">
                        <span class="info-icon">ℹ️</span>
                        <span>This will dismiss the report and keep the post visible on the discussion board.</span>
                    </div>
                    <button
                        @click="confirmAction"
                        class="confirm-btn"
                        :class="modalAction === 'keep' ? 'keep-confirm' : 'delete-confirm'"
                    >
                        {{ modalAction === 'keep' ? 'Keep Post & Dismiss Report' : 'Remove Post Permanently' }}
                    </button>
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
const isModalOpen = ref(false)
const selectedReport = ref(null)
const modalAction = ref('')

// Sample flagged posts data - posts that have been reported by users
const reports = ref([
    {
        id: 1,
        postAuthor: 'Maria Theresa Santos',
        postAuthorRole: 'Resident',
        postAuthorAvatar: '/assets/PROFILE PIC 3.jpg',
        postTitle: 'Tanong lang po about Barangay ID Renewal',
        postContent: 'Hi guys! May nakakaalam ba dito ng requirements for barangay ID renewal? Nawala kasi yung old ID ko eh, need ko na rin kumuha ng bago. Salamat sa sasagot!',
        postDate: '09/28/2025',
        dateObj: new Date('2025-09-28'),
        reportedBy: 'Juan Dela Cruz',
        reportedByAvatar: '/assets/PROFILE PIC 9.jpg',
        reportReason: 'Spam or misleading',
        reportDetails: 'This post appears multiple times and seems automated. User is posting the same question repeatedly.',
        reportDate: '09/29/2025',
        likes: 12,
        comments: 5,
        shares: 2,
        status: 'pending'
    },
    {
        id: 2,
        postAuthor: 'Josefino Manalantad',
        postAuthorRole: 'Resident',
        postAuthorAvatar: '/assets/PROFILE PIC 4.jpg',
        postTitle: 'May nakaexperience na ba ng problema sa water supply?',
        postContent: 'Since last night wala kaming tubig dito sa Phase 2. Kayo ba? Normal lang ba to or may problema sa main line? Pa-update naman kung may nakakaalam ng reason.',
        postDate: '09/26/2025',
        dateObj: new Date('2025-09-26'),
        reportedBy: 'Anna Mae Buenavente',
        reportedByAvatar: '/assets/PROFILE PIC 5.jpg',
        reportReason: 'False information',
        reportDetails: 'Post contains misleading information about water supply issues. There was no actual water interruption reported by the water company.',
        reportDate: '09/27/2025',
        likes: 15,
        comments: 10,
        shares: 5,
        status: 'pending'
    },
    {
        id: 3,
        postAuthor: 'Manuelo Reyes III',
        postAuthorRole: 'Resident',
        postAuthorAvatar: '/assets/PROFILE PIC 2.jpg',
        postTitle: 'Pa-recommend naman ng Vet Clinic malapit dito',
        postContent: 'Mga ka-barangay, sino may alam na magandang vet clinic malapit lang dito sa area natin? Need ng dog ko ng checkup kasi parang may allergy. Salamat sa tutulong!',
        postDate: '09/27/2025',
        dateObj: new Date('2025-09-27'),
        reportedBy: 'Liza Garcia',
        reportedByAvatar: '/assets/PROFILE PIC 8.jpg',
        reportReason: 'Inappropriate content',
        reportDetails: 'User has been using this post to advertise their own vet clinic business in the comments section repeatedly.',
        reportDate: '09/28/2025',
        likes: 8,
        comments: 4,
        shares: 1,
        status: 'pending'
    }
])

// Computed filtered reports
const filteredReports = computed(() => {
    let filtered = [...reports.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.postAuthor.toLowerCase().includes(query) ||
            item.postTitle.toLowerCase().includes(query) ||
            item.postContent.toLowerCase().includes(query) ||
            item.reportedBy.toLowerCase().includes(query) ||
            item.reportReason.toLowerCase().includes(query)
        )
    }

    // Role filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.postAuthorRole.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'relevant') {
        // Sort by engagement (likes + comments + shares)
        filtered.sort((a, b) => {
            const aEngagement = a.likes + a.comments + a.shares
            const bEngagement = b.likes + b.comments + b.shares
            return bEngagement - aEngagement
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

const openModal = (report, action) => {
    selectedReport.value = report
    modalAction.value = action
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedReport.value = null
    modalAction.value = ''
}

const confirmAction = () => {
    if (modalAction.value === 'keep') {
        console.log('Keeping post and dismissing report:', selectedReport.value.postTitle)
        alert('✅ Post Kept!\n\nThe post will remain on the discussion board and the report has been dismissed.')
    } else if (modalAction.value === 'delete') {
        console.log('Removing post:', selectedReport.value.postTitle)
        // Remove report from list
        const index = reports.value.findIndex(r => r.id === selectedReport.value.id)
        if (index > -1) {
            reports.value.splice(index, 1)
        }
        alert('❌ Post Removed!\n\nThe post has been permanently removed from the discussion board and the author has been notified.')
    }
    closeModal()
}

const navigateToDashboard = () => {
    router.visit(route('dashboard_admin'))
}

const navigateToUsers = () => {
    router.visit(route('users_admin'))
}

const navigateToHistory = () => {
    router.visit(route('history_admin'))
}

const navigateToRegisterRequest = () => {
    router.visit(route('register_request_admin'))
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

/* Reports Container */
.reports-container {
    padding: 25px;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.report-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 0;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    overflow: hidden;
}

.report-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

/* Reported Post Section */
.reported-post-section {
    background: linear-gradient(135deg, #fff5f5, #ffe5e5);
    padding: 20px;
    border-bottom: 2px solid #fecaca;
}

.section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 13px;
    color: #dc2626;
    margin-bottom: 15px;
    text-transform: uppercase;
}

.flag-icon {
    font-size: 16px;
}

.user-icon {
    font-size: 16px;
}

.post-preview {
    background: white;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.post-author-info {
    display: flex;
    align-items: start;
    gap: 12px;
    margin-bottom: 15px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.author-details {
    flex: 1;
}

.author-name {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.role-badge {
    display: inline-block;
    font-size: 10px;
    padding: 3px 10px;
    border-radius: 8px;
    font-weight: 700;
    color: white;
    margin-bottom: 5px;
}

.role-badge.resident {
    background: #239640;
}

.role-badge.official {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}

.post-date {
    font-size: 11px;
    color: #999;
    margin: 0;
}

.post-content-preview {
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 12px;
}

.post-title {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.post-text {
    font-size: 13px;
    line-height: 1.6;
    color: #666;
    margin: 0;
}

.post-engagement {
    display: flex;
    gap: 20px;
}

.engagement-stat {
    font-size: 13px;
    color: #666;
    font-weight: 600;
}

/* Reporter Section */
.reporter-section {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    padding: 20px;
    border-bottom: 2px solid #bae6fd;
}

.reporter-info {
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.reporter-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.reporter-details {
    flex: 1;
}

.reporter-name {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin: 0 0 4px 0;
}

.report-date {
    font-size: 11px;
    color: #999;
    margin: 0;
}

.report-reason-box {
    background: white;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.reason-label {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.reason-badge {
    display: inline-block;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.reason-details {
    font-size: 13px;
    line-height: 1.6;
    color: #555;
    font-style: italic;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 10px;
    padding: 20px;
    background: #f8f9fa;
}

.keep-btn {
    flex: 1;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.keep-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.delete-btn {
    flex: 1;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.delete-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.no-reports {
    padding: 80px 40px;
    text-align: center;
    color: #666;
}

.no-reports-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.no-reports p {
    font-size: 18px;
    font-weight: 600;
    color: #666;
    margin: 0 0 8px 0;
}

.no-reports-subtext {
    font-size: 14px;
    color: #999;
    font-weight: 400;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(4px);
}

.modal-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #f0f0f0;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    color: #666;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: #ff4444;
    color: white;
    transform: rotate(90deg);
}

.modal-content {
    margin-top: 10px;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

.modal-message {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
    text-align: center;
    line-height: 1.6;
}

.modal-action-text {
    font-weight: 700;
    color: #ff8c42;
    text-transform: uppercase;
}

.modal-report-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.modal-report-info p {
    margin: 8px 0;
    font-size: 14px;
    color: #555;
}

.modal-report-info strong {
    color: #333;
    font-weight: 600;
}

.modal-warning {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border-left: 4px solid #ef4444;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: start;
    gap: 10px;
}

.modal-warning .warning-icon {
    font-size: 20px;
    flex-shrink: 0;
}

.modal-warning span:last-child {
    font-size: 13px;
    color: #991b1b;
    line-height: 1.5;
}

.modal-info {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-left: 4px solid #10b981;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: start;
    gap: 10px;
}

.modal-info .info-icon {
    font-size: 20px;
    flex-shrink: 0;
}

.modal-info span:last-child {
    font-size: 13px;
    color: #065f46;
    line-height: 1.5;
}

.confirm-btn {
    width: 100%;
    padding: 14px 30px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
    color: white;
}

.keep-confirm {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.keep-confirm:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.delete-confirm {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.delete-confirm:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

/* Custom Scrollbar */
.reports-container::-webkit-scrollbar {
    width: 6px;
}

.reports-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.reports-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.reports-container::-webkit-scrollbar-thumb:hover {
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
    
    .modal-container {
        width: 95%;
        padding: 30px 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .keep-btn,
    .delete-btn {
        width: 100%;
    }

    .post-author-info {
        flex-direction: column;
        align-items: start;
    }

    .author-avatar {
        width: 60px;
        height: 60px;
    }

    .post-engagement {
        flex-wrap: wrap;
        gap: 12px;
    }

    .modal-container {
        padding: 25px 20px;
    }
}
</style>