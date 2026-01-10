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
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        Dashboard
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToHistory"
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
                        class="nav-item active"
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
                    <!-- Report Header -->
                    <div class="users-header">
                    <div class="users-title">
                        <h2>Reports & Contact Messages</h2>
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
                                <button class="search-btn" @click="performSearch">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Container -->
                    <div class="reports-container">
                        <!-- Post Reports -->
                        <div 
                            v-for="report in filteredReports" 
                            :key="report.id"
                            class="report-card"
                        >
                            <!-- Contact Message Section -->
                            <div v-if="report.type === 'contact'" class="contact-message-section">
                                <div class="section-label">
                                    <svg class="message-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>Contact Message</span>
                                </div>
                                <div class="contact-message-preview">
                                    <div class="contact-sender-info">
                                        <div class="sender-details">
                                            <h3 class="sender-name">{{ report.user_name || 'Guest' }}</h3>
                                            <span class="role-badge" :class="getRoleClass(report.user_type)">
                                                {{ (report.user_type || 'GUEST').toUpperCase() }}
                                            </span>
                                            <p class="contact-date">Received: {{ report.reportDate }}</p>
                                        </div>
                                    </div>
                                    <div class="contact-info-box">
                                        <div v-if="report.first_name" class="contact-info-item">
                                            <strong>First Name:</strong> {{ report.first_name }}
                                        </div>
                                        <div v-if="report.last_name" class="contact-info-item">
                                            <strong>Last Name:</strong> {{ report.last_name }}
                                        </div>
                                        <div v-if="report.user_email" class="contact-info-item">
                                            <strong>Email:</strong> {{ report.user_email }}
                                        </div>
                                        <div v-if="report.user_contact_number" class="contact-info-item">
                                            <strong>Contact Number:</strong> {{ report.user_contact_number }}
                                        </div>
                                    </div>
                                    <div class="message-content-box">
                                        <div class="message-label">Message:</div>
                                        <p class="message-text">{{ report.message }}</p>
                                    </div>
                                </div>
                                <!-- Action Buttons for Contact Messages -->
                                <div class="action-buttons">
                                    <button @click="markContactAsRead(report)" class="read-btn">
                                        Mark as Read
                                    </button>
                                    <button @click="markContactAsReplied(report)" class="replied-btn">
                                        Mark as Replied
                                    </button>
                                </div>
                            </div>

                            <!-- Post Report Section (existing) -->
                            <template v-else>
                                <!-- Reported Post Section -->
                                <div class="reported-post-section">
                                    <div class="section-label">
                                        <svg class="flag-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Reported Post</span>
                                    </div>
                                    <div class="post-preview">
                                        <div class="post-author-info">
                                            <img :src="report.postAuthorAvatar" :alt="report.postAuthor" class="author-avatar" @error="$event.target.src='/assets/DEFAULT.jpg'" />
                                            <div class="author-details">
                                                <h3 class="author-name">{{ report.postAuthor }}</h3>
                                                <span class="role-badge" :class="getRoleClass(report.postAuthorRole)">
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
                                        <svg class="user-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Reported By</span>
                                    </div>
                                    <div class="reporter-info">
                                        <img :src="report.reportedByAvatar" :alt="report.reportedBy" class="reporter-avatar" @error="$event.target.src='/assets/DEFAULT.jpg'" />
                                        <div class="reporter-details">
                                            <h4 class="reporter-name">{{ report.reportedBy }}</h4>
                                            <span class="role-badge" :class="getRoleClass(report.reporterRole)">
                                                {{ report.reporterRole.toUpperCase() }}
                                            </span>
                                            <p class="report-date">Reported: {{ report.reportDate }}</p>
                                        </div>
                                    </div>
                                    <div class="report-reason-box">
                                        <div class="reason-label">Reason{{ report.reportReasons && report.reportReasons.length > 1 ? 's' : '' }}:</div>
                                        <div class="reasons-list">
                                            <span 
                                                v-for="(reason, index) in (report.reportReasons || [report.reportReason])" 
                                                :key="index"
                                                class="reason-badge"
                                            >
                                                {{ reason }}
                                            </span>
                                        </div>
                                        <div v-if="report.reportDetails" class="reason-details">{{ report.reportDetails }}</div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <button @click="openModal(report, 'keep')" class="keep-btn">
                                        Keep Post
                                    </button>
                                    <button @click="openModal(report, 'delete')" class="delete-btn">
                                        Remove Post
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- No reports message -->
                        <div v-if="filteredReports.length === 0" class="no-reports">
                            <svg class="no-reports-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p>No reports or contact messages found matching your criteria.</p>
                            <p class="no-reports-subtext">All posts are following community guidelines and there are no pending contact messages.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Popup -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

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
                        <p><strong>Reason{{ selectedReport?.reportReasons && selectedReport.reportReasons.length > 1 ? 's' : '' }}:</strong> 
                            <span v-for="(reason, index) in (selectedReport?.reportReasons || [selectedReport?.reportReason])" :key="index">
                                {{ reason }}{{ index < (selectedReport?.reportReasons?.length - 1 || 0) ? ', ' : '' }}
                            </span>
                        </p>
                    </div>
                    <div class="modal-warning" v-if="modalAction === 'delete'">
                        <svg class="warning-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
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

// Define props - receive reports from backend
const props = defineProps({
    reports: {
        type: Array,
        default: () => []
    }
})

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
const selectedReportId = ref(null)
const selectedPostId = ref(null)
const modalAction = ref('')
const isLoading = ref(false)

// Convert props.reports to reactive ref and process dates
const reportsData = (props.reports || []).map((report, index) => {
    // Ensure all properties are preserved
    const mappedReport = {
        ...report,
        dateObj: report.reportDateTime ? new Date(report.reportDateTime) : new Date(report.reportDate),
    }
    // Debug: log first report to verify structure
    if (index === 0) {
        console.log('First report structure:', mappedReport)
        console.log('Report ID:', mappedReport.id, 'Post ID:', mappedReport.post_id)
    }
    return mappedReport
})
const reports = ref(reportsData)

// Computed filtered reports
const filteredReports = computed(() => {
    let filtered = [...reports.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => {
            if (item.type === 'contact') {
                return (item.user_name && item.user_name.toLowerCase().includes(query)) ||
                    (item.first_name && item.first_name.toLowerCase().includes(query)) ||
                    (item.last_name && item.last_name.toLowerCase().includes(query)) ||
                    (item.user_email && item.user_email.toLowerCase().includes(query)) ||
                    (item.user_contact_number && item.user_contact_number.toLowerCase().includes(query)) ||
                    item.message.toLowerCase().includes(query)
            } else {
                const reasons = item.reportReasons || [item.reportReason]
                const reasonsText = reasons.join(' ').toLowerCase()
                return item.postAuthor.toLowerCase().includes(query) ||
                    item.postTitle.toLowerCase().includes(query) ||
                    item.postContent.toLowerCase().includes(query) ||
                    item.reportedBy.toLowerCase().includes(query) ||
                    reasonsText.includes(query)
            }
        })
    }

    // Role filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => {
            if (item.type === 'contact') {
                const userType = (item.user_type || 'Guest').toLowerCase()
                if (filterOption.value === 'resident') {
                    return userType === 'resident'
                } else if (filterOption.value === 'official') {
                    return userType === 'employee' || userType.includes('official')
                }
                return true
            } else {
                return item.postAuthorRole.toLowerCase() === filterOption.value.toLowerCase()
            }
        })
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => {
            const dateA = a.dateObj || new Date(a.reportDate)
            const dateB = b.dateObj || new Date(b.reportDate)
            return dateB - dateA
        })
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => {
            const dateA = a.dateObj || new Date(a.reportDate)
            const dateB = b.dateObj || new Date(b.reportDate)
            return dateA - dateB
        })
    } else if (sortOption.value === 'relevant') {
        // Sort by report date (most recent reports first)
        filtered.sort((a, b) => {
            const dateA = a.dateObj || new Date(a.reportDate)
            const dateB = b.dateObj || new Date(b.reportDate)
            return dateB - dateA
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
    console.log('Opening modal with report:', report)
    console.log('Report ID:', report.id, 'Post ID:', report.post_id)
    console.log('All report keys:', Object.keys(report))
    console.log('Report values:', {
        id: report.id,
        report_id: report.report_id,
        post_report_id: report.post_report_id,
        post_id: report.post_id
    })
    
    // Find the original report from reports.value to ensure we have all properties
    // Try to match by id first, then by other possible ID fields
    const originalReport = reports.value.find(r => {
        return r.id === report.id || 
               r.report_id === report.report_id || 
               r.post_report_id === report.post_report_id ||
               (r.id && r.id === report.id)
    }) || report
    
    console.log('Original report:', originalReport)
    console.log('Original report ID:', originalReport.id, 'Post ID:', originalReport.post_id)
    
    // Store both the full report object and the IDs separately
    // Try multiple possible field names for report ID
    selectedReport.value = originalReport
    selectedReportId.value = originalReport.id || 
                              originalReport.report_id || 
                              originalReport.post_report_id || 
                              originalReport.reportId
    selectedPostId.value = originalReport.post_id || 
                            originalReport.postId || 
                            originalReport.post?.post_id
    modalAction.value = action
    isModalOpen.value = true
    
    console.log('Stored IDs - Report ID:', selectedReportId.value, 'Post ID:', selectedPostId.value)
    
    // If still no ID, log detailed info
    if (!selectedReportId.value) {
        console.error('CRITICAL: No report ID found!')
        console.error('Report object:', JSON.stringify(report, null, 2))
        console.error('Original report object:', JSON.stringify(originalReport, null, 2))
    }
}

const closeModal = () => {
    isModalOpen.value = false
    selectedReport.value = null
    selectedReportId.value = null
    selectedPostId.value = null
    modalAction.value = ''
}

const confirmAction = async () => {
    if (isLoading.value) return

    if (!selectedReport.value) {
        alert('Error: No report selected.')
        return
    }

    // Store the report data in local variables to avoid reactivity issues
    let report = selectedReport.value
    
    // Try to find the original report from reports.value to ensure we have all properties
    if (report.id) {
        const originalReport = reports.value.find(r => r.id === report.id)
        if (originalReport) {
            report = originalReport
        }
    }
    
    const action = modalAction.value

    console.log('Confirm action - Report:', report)
    console.log('Confirm action - Action:', action)
    console.log('Report ID:', report.id, 'Post ID:', report.post_id)
    console.log('All report keys:', Object.keys(report))

    isLoading.value = true

    try {
        if (action === 'keep') {
            // Dismiss the report - use stored ID first, then try from report object
            // Try all possible field names
            const reportId = selectedReportId.value || 
                             report.id || 
                             report.report_id || 
                             report.post_report_id || 
                             report.reportId
            
            console.log('Attempting to dismiss report with ID:', reportId)
            console.log('Stored report ID:', selectedReportId.value)
            console.log('Report object ID fields:', {
                id: report.id,
                report_id: report.report_id,
                post_report_id: report.post_report_id,
                reportId: report.reportId
            })
            
            if (!reportId) {
                console.error('Report object structure:', JSON.stringify(report, null, 2))
                console.error('Available keys:', Object.keys(report))
                console.error('Selected report ID:', selectedReportId.value)
                console.error('Selected report object:', selectedReport.value)
                alert('Error: Report ID is missing. Please refresh the page and try again.\n\nCheck console for details.')
                isLoading.value = false
                return
            }

            // Build URL - use route helper if available, otherwise construct manually
            let url
            if (typeof route === 'function') {
                try {
                    url = route('reports.dismiss', reportId)
                } catch (e) {
                    console.warn('Route helper error, using fallback URL:', e)
                    url = `/reports/${reportId}/dismiss`
                }
            } else {
                url = `/reports/${reportId}/dismiss`
            }

            console.log('Dismissing report:', { reportId, url })
            const response = await window.axios.post(url)
            console.log('Dismiss response:', response.data)
            
            if (response.data && response.data.success) {
                // Remove only the dismissed report from the list
                reports.value = reports.value.filter(r => r.id !== reportId)
                alert('✅ Post Kept!\n\nThe post will remain on the discussion board and the report has been dismissed.')
            } else {
                const errorMsg = response.data?.message || 'Error dismissing report. Please try again.'
                alert(errorMsg)
            }
        } else if (action === 'delete') {
            // Delete the post - use stored ID first, then try from report object
            const postId = selectedPostId.value || report.post_id || report.postId || report.post?.post_id
            
            console.log('Attempting to delete post with ID:', postId)
            console.log('Stored post ID:', selectedPostId.value)
            console.log('Report object post_id:', report.post_id)
            
            if (!postId) {
                console.error('Report object structure:', JSON.stringify(report, null, 2))
                console.error('Available keys:', Object.keys(report))
                console.error('Selected post ID:', selectedPostId.value)
                alert('Error: Post ID is missing. Please refresh the page and try again.\n\nCheck console for details.')
                isLoading.value = false
                return
            }

            // Build URL - use route helper if available, otherwise construct manually
            let url
            if (typeof route === 'function') {
                try {
                    url = route('admin.posts.delete', { postId: postId })
                } catch (e) {
                    console.warn('Route helper error, using fallback URL:', e)
                    url = `/admin/posts/${postId}`
                }
            } else {
                url = `/admin/posts/${postId}`
            }

            console.log('Deleting post:', { postId, url })
            const response = await window.axios.delete(url)
            console.log('Delete response:', response.data)
            
            if (response.data && response.data.success) {
                // Remove all reports for this post from the list (since post is deleted)
                reports.value = reports.value.filter(r => r.post_id !== postId)
                alert('❌ Post Removed!\n\nThe post has been permanently removed from the discussion board.')
            } else {
                const errorMsg = response.data?.message || 'Error deleting post. Please try again.'
                alert(errorMsg)
            }
        }
    } catch (error) {
        console.error('Error performing action:', error)
        console.error('Error response:', error.response)
        console.error('Error response data:', error.response?.data)
        
        let errorMessage = 'An error occurred. Please try again.'
        if (error.response) {
            if (error.response.data) {
                errorMessage = error.response.data.message || error.response.data.error || errorMessage
            } else if (error.response.status === 404) {
                errorMessage = 'The requested resource was not found. Please refresh the page.'
            } else if (error.response.status === 500) {
                errorMessage = 'Server error. Please try again later.'
            }
        } else if (error.message) {
            errorMessage = error.message
        }
        
        alert(`Error: ${errorMessage}`)
    } finally {
        isLoading.value = false
        closeModal()
    }
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

const markContactAsRead = async (contact) => {
    if (!contact.contact_id) return
    
    try {
        const response = await window.axios.post(route('admin.contact_messages.update', contact.contact_id), {
            status: 'Read'
        })
        
        if (response.data && response.data.success) {
            reports.value = reports.value.filter(r => r.id !== contact.id)
            alert('✅ Contact message marked as read!')
        } else {
            alert('Error updating contact message.')
        }
    } catch (error) {
        console.error('Error marking contact as read:', error)
        alert('Error updating contact message. Please try again.')
    }
}

const markContactAsReplied = async (contact) => {
    if (!contact.contact_id) return
    
    try {
        const response = await window.axios.post(route('admin.contact_messages.update', contact.contact_id), {
            status: 'Replied'
        })
        
        if (response.data && response.data.success) {
            reports.value = reports.value.filter(r => r.id !== contact.id)
            alert('✅ Contact message marked as replied!')
        } else {
            alert('Error updating contact message.')
        }
    } catch (error) {
        console.error('Error marking contact as replied:', error)
        alert('Error updating contact message. Please try again.')
    }
}

// Get role class for styling
const getRoleClass = (role) => {
    if (!role) return 'resident'
    const roleLower = role.toLowerCase()
    if (roleLower === 'resident') {
        return 'resident'
    } else if (roleLower.includes('barangay') || roleLower.includes('sangguniang') || roleLower.includes('system admin')) {
        return 'official'
    }
    return 'resident' // default
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
    
    // Debug: Check reports structure on mount
    console.log('Reports on mount:', reports.value)
    if (reports.value.length > 0) {
        console.log('First report on mount:', reports.value[0])
        console.log('First report ID:', reports.value[0].id)
        console.log('First report post_id:', reports.value[0].post_id)
        console.log('First report keys:', Object.keys(reports.value[0]))
    }
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
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.user-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
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
    border-radius: 10px;
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

.reporter-details .role-badge {
    display: inline-block;
    font-size: 10px;
    padding: 3px 10px;
    border-radius: 8px;
    font-weight: 700;
    color: white;
    margin-bottom: 5px;
    margin-right: 5px;
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

.reasons-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
}

.reason-badge {
    display: inline-block;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
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
    display: flex;
    flex-direction: column;
    align-items: center;
}

.no-reports-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 20px;
    opacity: 0.3;
    color: #9ca3af;
    margin-left: auto;
    margin-right: auto;
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
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: all 0.2s;
    color: #666;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #333;
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
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    color: #dc3545;
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

/* Contact Message Section */
.contact-message-section {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    padding: 20px;
    border-bottom: 2px solid #bae6fd;
}

.message-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.contact-message-preview {
    background: white;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.contact-sender-info {
    display: flex;
    align-items: start;
    gap: 12px;
    margin-bottom: 15px;
}

.sender-details {
    flex: 1;
}

.sender-name {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.contact-date {
    font-size: 11px;
    color: #999;
    margin: 0;
}

.contact-info-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
}

.contact-info-item {
    font-size: 13px;
    color: #555;
    margin-bottom: 5px;
}

.contact-info-item:last-child {
    margin-bottom: 0;
}

.message-content-box {
    padding: 15px 0;
    border-top: 1px solid #f0f0f0;
}

.message-label {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.message-text {
    font-size: 13px;
    line-height: 1.6;
    color: #555;
    margin: 0;
    white-space: pre-wrap;
}

.read-btn {
    flex: 1;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.read-btn:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.replied-btn {
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

.replied-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
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
    background: #888;
    border-radius: 3px;
}

.reports-container::-webkit-scrollbar-thumb:hover {
    background: #666;
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