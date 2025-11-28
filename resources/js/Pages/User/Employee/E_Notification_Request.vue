<template>
    <Head>
        <title>Notifications</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/WHITE LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
                        <Link href="#" class="settings-item" @click="closeSettings">Terms & Conditions</Link>
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
                    <img src="/assets/KAPITAN.jpg" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">Kap. Sammy Reyes</div>
                        <div class="profile-role">BARANGAY CAPTAIN</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'posts' }"
                        @click="navigateToPosts"
                    >
                        📋 Posts
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'documents' }"
                        @click="navigateToDocuments"
                    >
                        📄 Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'events' }"
                        @click="navigateToEvents"
                    >
                        🤝 Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                        :class="{ active: activeTab === 'notifications' }"
                        @click="navigateToNotifications"
                    >
                        🔔 Notifications
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'profile' }"
                        @click="navigateToProfile"
                    >
                        👤 Profile
                    </Link>
                </div>

                <button class="faq-btn" @click="openFAQ">
                    ❓ FAQs & Help Center
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="main-content">
                    <!-- Header with Dropdown Toggle -->
                    <div class="notification-header">
                        <div class="notification-title">
                            <button class="title-dropdown" @click="toggleModeDropdown">
                                <h2>{{ currentTab === 'activities' ? 'Activities' : 'Requests' }}</h2>
                                <span class="dropdown-icon" :class="{ rotated: showModeDropdown }">▼</span>
                            </button>
                            <!-- Dropdown Menu -->
                            <div v-if="showModeDropdown" class="mode-dropdown">
                                <button 
                                    class="mode-option"
                                    :class="{ active: currentTab === 'activities' }"
                                    @click="switchTab('activities')"
                                >
                                    Activities
                                </button>
                                <button 
                                    class="mode-option"
                                    :class="{ active: currentTab === 'requests' }"
                                    @click="switchTab('requests')"
                                >
                                    Requests
                                </button>
                            </div>
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
                                    <button @click="selectSort('relevant')" :class="{ active: sortOption === 'relevant' }">RELEVANT</button>
                                    <button @click="selectSort('oldest')" :class="{ active: sortOption === 'oldest' }">OLDEST</button>
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

                    <!-- Notifications Content -->
                    <div class="notifications-container">
                        <!-- Activities Tab -->
                        <div v-if="currentTab === 'activities'" class="activities-list">
                            <div 
                                v-for="activity in filteredActivities" 
                                :key="activity.id"
                                class="notification-card activity-card"
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
                            </div>

                            <div v-if="filteredActivities.length === 0" class="no-notifications">
                                <p>No activities found</p>
                            </div>
                        </div>

                        <!-- Requests Tab -->
                        <div v-if="currentTab === 'requests'" class="requests-list">
                            <div 
                                v-for="request in filteredRequests" 
                                :key="request.id"
                                class="notification-card request-card"
                                :class="request.status.toLowerCase()"
                                @click="viewRequestDetails(request)"
                                style="cursor: pointer;"
                            >
                                <div class="request-header">
                                    <div class="request-info">
                                        <span class="request-number">REQUEST NUMBER: #{{ request.requestNumber }}</span>
                                        <h3 class="request-title">{{ request.title }}</h3>
                                        <span class="request-time">{{ request.date }} | {{ request.time }}</span>
                                    </div>
                                    <div class="request-status" :class="request.status.toLowerCase()">
                                        {{ request.status }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="filteredRequests.length === 0" class="no-notifications">
                                <p>No requests found</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details Modal -->
        <div v-if="showDetailsModal" class="modal-overlay" @click="closeDetailsModal">
            <div class="modal-content details-modal" @click.stop>
                <button class="close-modal-btn" @click="closeDetailsModal">✕</button>
                
                <!-- Success/Status Header -->
                <div class="modal-icon" :class="selectedRequest?.status.toLowerCase() + '-icon'">
                    <div class="status-badge">
                        <span v-if="selectedRequest?.status === 'ACCEPTED'">✓</span>
                        <span v-if="selectedRequest?.status === 'PENDING'">⏱</span>
                        <span v-if="selectedRequest?.status === 'REJECTED'">✕</span>
                    </div>
                </div>
                
                <h3 class="modal-title">REQUEST {{ selectedRequest?.status }}</h3>
                <p class="request-number-display">REQUEST NO. #{{ selectedRequest?.requestNumber }}</p>
                
                <div class="details-content">
                    <!-- Request Message -->
                    <p class="details-message">
                        Your request to acquire a <strong>{{ selectedRequest?.title }}</strong> has been 
                        <strong>{{ selectedRequest?.status.toLowerCase() }}</strong>.
                    </p>
                    
                    <!-- ACCEPTED Status - Show pickup instructions -->
                    <div v-if="selectedRequest?.status === 'ACCEPTED'" class="accepted-section">
                        <p class="details-message">
                            You may now proceed to claim your certificate by following the instructions provided.
                        </p>
                        
                        <div class="pickup-info">
                            <div class="info-item">
                                <span class="info-label">WHAT:</span>
                                <span class="info-value">PICKUP OF DOCUMENT ({{ selectedRequest?.title }})</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">WHERE:</span>
                                <span class="info-value">BARANGAY 176B BARANGAY HALL</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">WHEN:</span>
                                <span class="info-value">{{ getPickupSchedule() }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">WHO:</span>
                                <span class="info-value">FIND MS. MERCY ALPAÑO</span>
                            </div>
                            <div class="info-item" v-if="selectedRequest?.type === 'document'">
                                <span class="info-label">AMOUNT:</span>
                                <span class="info-value amount">₱{{ selectedRequest?.amount || '150.00' }}</span>
                            </div>
                        </div>

                        <p class="present-message">
                            <strong>Present this request number upon pickup:</strong><br/>
                            <span class="highlight-number">#{{ selectedRequest?.requestNumber }}</span>
                        </p>

                        <!-- Payment Buttons for Document Requests -->
                        <div v-if="selectedRequest?.type === 'document'" class="payment-buttons-modal">
                            <button class="pay-online-btn-modal" @click="showPaymentGateway(selectedRequest)">
                                Pay Online
                            </button>
                            <button class="pay-onsite-btn-modal" @click="acknowledgeOnsite">
                                Pay Onsite
                            </button>
                        </div>
                    </div>

                    <!-- PENDING Status -->
                    <div v-if="selectedRequest?.status === 'PENDING'" class="pending-section">
                        <p class="details-message">
                            Your request is currently being reviewed by the barangay officials. You will be notified once a decision has been made.
                        </p>
                        
                        <div class="request-info-box">
                            <div class="info-item">
                                <span class="info-label">SUBMITTED ON:</span>
                                <span class="info-value">{{ selectedRequest?.date }}, {{ selectedRequest?.time }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">REQUEST TYPE:</span>
                                <span class="info-value">{{ selectedRequest?.title }}</span>
                            </div>
                            <div class="info-item" v-if="selectedRequest?.type === 'document'">
                                <span class="info-label">ESTIMATED AMOUNT:</span>
                                <span class="info-value">₱{{ selectedRequest?.amount || '150.00' }}</span>
                            </div>
                        </div>
                        
                        <p class="note-message">
                            <strong>Note:</strong> Processing time typically takes 3-5 business days. Thank you for your patience.
                        </p>
                    </div>

                    <!-- REJECTED Status -->
                    <div v-if="selectedRequest?.status === 'REJECTED'" class="rejected-section">
                        <p class="details-message">
                            Unfortunately, your request has been rejected. Please review the reason below and contact the barangay office if you have questions.
                        </p>
                        
                        <div class="rejection-box">
                            <h4>Reason for Rejection:</h4>
                            <p>{{ getRejectionReason(selectedRequest) }}</p>
                        </div>
                        
                        <div class="request-info-box">
                            <div class="info-item">
                                <span class="info-label">SUBMITTED ON:</span>
                                <span class="info-value">{{ selectedRequest?.date }}, {{ selectedRequest?.time }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">REQUEST TYPE:</span>
                                <span class="info-value">{{ selectedRequest?.title }}</span>
                            </div>
                        </div>
                        
                        <p class="note-message">
                            You may submit a new request with the correct information or contact Ms. Mercy Alpaño at the barangay hall for assistance.
                        </p>
                    </div>
                    
                    <p class="thank-you">Thank you!</p>
                </div>

                <button 
                    v-if="!(selectedRequest?.status === 'ACCEPTED' && selectedRequest?.type === 'document')" 
                    class="close-btn" 
                    @click="closeDetailsModal"
                >
                    Close
                </button>
            </div>
        </div>

        <!-- Payment Gateway Modal -->
        <div v-if="showPaymentModal" class="modal-overlay" @click="closePaymentModal">
            <div class="modal-content payment-modal" @click.stop>
                <button class="close-modal-btn" @click="closePaymentModal">✕</button>
                <h3 class="modal-title">Choose Payment Gateway</h3>
                <p class="modal-subtitle">Select your preferred payment method</p>
                
                <div class="payment-options">
                    <button class="payment-option-btn gcash" @click="processPayment('GCash')">
                        <div class="payment-logo-placeholder">
                            <img src="/assets/GCASH.png" alt="GCash" class="payment-logo" />
                        </div>
                        <span>GCash</span>
                    </button>
                    
                    <button class="payment-option-btn maya" @click="processPayment('Maya')">
                        <div class="payment-logo-placeholder">
                            <img src="/assets/MAYA.png" alt="Maya" class="payment-logo" />
                        </div>
                        <span>Maya</span>
                    </button>
                </div>

                <div class="payment-details">
                    <p><strong>Request:</strong> {{ selectedRequest?.title }}</p>
                    <p><strong>Amount:</strong> ₱{{ selectedRequest?.amount || '150.00' }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const showSettings = ref(false)
const showModeDropdown = ref(false)
const showSortDropdown = ref(false)
const activeTab = ref('notifications')
const currentTab = ref('requests')
const sortOption = ref('newest')
const searchQuery = ref('')
const showPaymentModal = ref(false)
const showDetailsModal = ref(false)
const selectedRequest = ref(null)

// Sample Activities Data
const activities = ref([
    {
        id: 1,
        user: 'John Rey Santiago',
        others: 88,
        action: 'liked your post.',
        avatar: '/assets/PROFILE PIC 9.jpg',
        time: 'Jun 01, 2025 10:08 AM',
        timestamp: new Date('2025-06-01T10:08:00')
    },
    {
        id: 2,
        user: 'Kap. Sammy Reyes',
        others: null,
        action: 'commented on your post.',
        avatar: '/assets/PROFILE PIC 4.jpg',
        time: 'Jun 01, 2025 10:09 AM',
        timestamp: new Date('2025-06-01T10:09:00')
    },
    {
        id: 3,
        user: 'Maria Theresa Santos',
        others: 15,
        action: 'and others reacted to your comment.',
        avatar: '/assets/PROFILE PIC 3.jpg',
        time: 'May 30, 2025 3:45 PM',
        timestamp: new Date('2025-05-30T15:45:00')
    },
    {
        id: 4,
        user: 'Anna Mae Buenavente',
        others: null,
        action: 'shared your post.',
        avatar: '/assets/PROFILE PIC 5.jpg',
        time: 'May 29, 2025 2:20 PM',
        timestamp: new Date('2025-05-29T14:20:00')
    }
])

// Sample Requests Data
const requests = ref([
    {
        id: 1,
        requestNumber: '002212',
        title: 'Request for Barangay Certificate',
        date: 'Jun 01, 2025',
        time: '10:09 AM',
        status: 'ACCEPTED',
        type: 'document',
        amount: '150.00',
        timestamp: new Date('2025-06-01T10:09:00')
    },
    {
        id: 2,
        requestNumber: '002198',
        title: 'Request for Barangay Clearance',
        date: 'May 28, 2025',
        time: '2:30 PM',
        status: 'PENDING',
        type: 'document',
        amount: '200.00',
        timestamp: new Date('2025-05-28T14:30:00')
    },
    {
        id: 3,
        requestNumber: 'EA-0045',
        title: 'Court Reservation Request',
        date: 'May 27, 2025',
        time: '9:15 AM',
        status: 'ACCEPTED',
        type: 'event',
        timestamp: new Date('2025-05-27T09:15:00')
    },
    {
        id: 4,
        requestNumber: '002165',
        title: 'Request for Certificate of Indigency',
        date: 'May 25, 2025',
        time: '11:00 AM',
        status: 'REJECTED',
        type: 'document',
        amount: '100.00',
        timestamp: new Date('2025-05-25T11:00:00')
    },
    {
        id: 5,
        requestNumber: 'EA-0032',
        title: 'Funeral Assistance Request',
        date: 'May 24, 2025',
        time: '8:30 AM',
        status: 'PENDING',
        type: 'event',
        timestamp: new Date('2025-05-24T08:30:00')
    }
])

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

// Filtered Requests
const filteredRequests = computed(() => {
    let filtered = [...requests.value]

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(request => 
            request.title.toLowerCase().includes(query) ||
            request.requestNumber.includes(query) ||
            request.status.toLowerCase().includes(query)
        )
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.timestamp - a.timestamp)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.timestamp - b.timestamp)
    } else if (sortOption.value === 'relevant') {
        // Sort by status priority: PENDING > ACCEPTED > REJECTED
        const statusPriority = { 'PENDING': 1, 'ACCEPTED': 2, 'REJECTED': 3 }
        filtered.sort((a, b) => statusPriority[a.status] - statusPriority[b.status])
    }

    return filtered
})

const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const toggleModeDropdown = () => {
    showModeDropdown.value = !showModeDropdown.value
}

const toggleSortDropdown = () => {
    showSortDropdown.value = !showSortDropdown.value
}

const selectSort = (option) => {
    sortOption.value = option
    showSortDropdown.value = false
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const logout = () => {
    showSettings.value = false
    router.visit(route('login'))
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_employee'))
}
const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_employee'))
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_employee'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_employee'))
}

const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    // Already on notifications page, no need to navigate
}

const switchTab = (tab) => {
    currentTab.value = tab
    showModeDropdown.value = false
}

const openFAQ = () => {
    router.visit(route('help_center_employee'))
}

const viewRequestDetails = (request) => {
    selectedRequest.value = request
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedRequest.value = null
}

const showPaymentGateway = (request) => {
    selectedRequest.value = request
    showDetailsModal.value = false
    showPaymentModal.value = true
}

const closePaymentModal = () => {
    showPaymentModal.value = false
}

const processPayment = (gateway) => {
    alert(`Processing payment via ${gateway}...\nRequest: ${selectedRequest.value.title}\nAmount: ₱${selectedRequest.value.amount}`)
    closePaymentModal()
    selectedRequest.value = null
}

const acknowledgeOnsite = () => {
    showDetailsModal.value = false
    alert('You have chosen to pay onsite. Please bring the exact amount when you pick up your document.')
}

const getRejectionReason = (request) => {
    const reasons = {
        '002165': 'Incomplete documentation. Please submit a valid ID and proof of residency.',
        'default': 'The submitted information does not meet the requirements. Please contact the barangay office for more details.'
    }
    return reasons[request.requestNumber] || reasons.default
}

const getPickupSchedule = () => {
    const today = new Date()
    const pickupDate = new Date(today)
    pickupDate.setDate(today.getDate() + 3)
    
    const options = { month: 'short', day: '2-digit', year: 'numeric' }
    const dateStr = pickupDate.toLocaleDateString('en-US', options).toUpperCase()
    
    return `${dateStr}, 9:00 AM - 3:00 PM`
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.notification-title')) {
        showModeDropdown.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'notifications'
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    padding: 5px 0;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
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
}

.header-actions {
    position: relative;
}

.settings-btn-img {
    margin-right: 30px;
    width: 30px;
    cursor: pointer;
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
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
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 12px;
    font-weight: 600;
cursor: pointer;
box-shadow: 0 6px 20px rgba(43, 178, 74, 0.3);
transition: all 0.3s ease;
font-size: 14px;
}
.faq-btn:hover {
transform: translateY(-2px);
box-shadow: 0 8px 25px rgba(43, 178, 74, 0.4);
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
padding: 8px 25px;
display: flex;
justify-content: space-between;
align-items: center;
position: relative;
}
.notification-title {
position: relative;
}
.title-dropdown {
background: none;
border: none;
color: white;
cursor: pointer;
display: flex;
align-items: center;
gap: 12px;
padding: 8px 12px;
border-radius: 8px;
transition: background 0.2s;
}
.title-dropdown:hover {
background: rgba(255,255,255,0.1);
}
.title-dropdown h2 {
font-size: 22px;
font-weight: 700;
margin: 0;
text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.dropdown-icon {
font-size: 14px;
transition: transform 0.3s ease;
}
.dropdown-icon.rotated {
transform: rotate(180deg);
}
.mode-dropdown {
position: absolute;
top: 100%;
left: 0;
background: white;
border-radius: 10px;
box-shadow: 0 8px 25px rgba(0,0,0,0.15);
min-width: 180px;
z-index: 1000;
margin-top: 8px;
overflow: hidden;
border: 1px solid rgba(0,0,0,0.1);
}
.mode-option {
display: block;
width: 100%;
padding: 12px 16px;
background: none;
border: none;
text-align: left;
color: #333;
cursor: pointer;
transition: background 0.2s;
font-weight: 500;
}
.mode-option:hover {
background: #fff7ef;
}
.mode-option.active {
background: #fff7ef;
color: #ff8c42;
font-weight: 600;
}
.small-logo {
width: 28px;
height: 28px;
border-radius: 6px;
padding: 3px;
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
border-color: #ff8c42;
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
min-width: 150px;
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
background: #fff7ef;
}
.filter-dropdown-menu button.active {
background: #fff7ef;
color: #ff8c42;
font-weight: 600;
}
.filter-right {
display: flex;
gap: 15px;
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
width: 250px;
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
.notifications-container {
padding: 0;
max-height: calc(100vh - 300px);
overflow-y: auto;
}
.activities-list,
.requests-list {
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
.request-card {
border-left: 4px solid transparent;
}
.request-card.accepted {
border-left-color: #2bb24a;
background: linear-gradient(90deg, rgba(43, 178, 74, 0.05), transparent);
}
.request-card.pending {
border-left-color: #ff9800;
background: linear-gradient(90deg, rgba(255, 152, 0, 0.05), transparent);
}
.request-card.rejected {
border-left-color: #dc3545;
background: linear-gradient(90deg, rgba(220, 53, 69, 0.05), transparent);
}
.request-header {
display: flex;
justify-content: space-between;
align-items: start;
}
.request-info {
flex: 1;
}
.request-number {
font-size: 11px;
font-weight: 700;
color: #ff8c42;
display: block;
margin-bottom: 5px;
}
.request-title {
font-size: 16px;
font-weight: 700;
color: #333;
margin-bottom: 5px;
}
.request-time {
font-size: 12px;
color: #999;
}
.request-status {
padding: 8px 16px;
border-radius: 20px;
font-size: 11px;
font-weight: 700;
text-transform: uppercase;
white-space: nowrap;
}
.request-status.accepted {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}
.request-status.pending {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}
.request-status.rejected {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}
.no-notifications {
padding: 60px 40px;
text-align: center;
color: #999;
}
.no-notifications p {
font-size: 16px;
}
/* Modal Styles */
.modal-overlay {
position: fixed;
top: 0;
left: 0;
right: 0;
bottom: 0;
background: rgba(0, 0, 0, 0.6);
display: flex;
align-items: center;
justify-content: center;
z-index: 2000;
backdrop-filter: blur(4px);
}
.modal-content {
background: white;
border-radius: 15px;
max-width: 500px;
width: 90%;
max-height: 80vh;
overflow-y: auto;
box-shadow: 0 10px 40px rgba(0,0,0,0.3);
position: relative;
}
.close-modal-btn {
position: absolute;
top: 15px;
right: 15px;
background: none;
border: none;
font-size: 24px;
color: #999;
cursor: pointer;
width: 30px;
height: 30px;
display: flex;
align-items: center;
justify-content: center;
border-radius: 50%;
transition: all 0.2s;
}
.close-modal-btn:hover {
background: #f8f9fa;
color: #333;
}
/* Request Details Modal */
.details-modal {
padding: 40px 30px;
text-align: center;
max-width: 600px;
}
.status-badge {
width: 80px;
height: 80px;
margin-top: 0px;
border-radius: 50%;
display: flex;
align-items: center;
justify-content: center;
font-size: 40px;
font-weight: 700;
margin: 0 auto;
}
.accepted-icon .status-badge {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
box-shadow: 0 6px 20px rgba(43, 178, 74, 0.4);
}
.pending-icon .status-badge {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
}
.rejected-icon .status-badge {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}
.modal-title {
font-size: 24px;
font-weight: 700;
color: #000000;
margin-top: 15px;
margin-bottom: 10px;
text-align: center;
}
.request-number-display {
font-size: 13px;
color: #ff8c42;
font-weight: 700;
margin-bottom: 25px;
}
.details-content {
text-align: left;
}
.details-message {
font-size: 14px;
color: #555;
line-height: 1.6;
margin-bottom: 20px;
}
.pickup-info {
background: #f8f9fa;
border-radius: 10px;
padding: 20px;
margin-bottom: 20px;
}
.info-item {
display: flex;
gap: 10px;
margin-bottom: 12px;
font-size: 13px;
}
.info-item:last-child {
margin-bottom: 0;
}
.info-label {
font-weight: 700;
color: #333;
min-width: 80px;
}
.info-value {
color: #555;
flex: 1;
}
.info-value.amount {
color: #2bb24a;
font-weight: 700;
font-size: 16px;
}
.present-message {
font-size: 13px;
color: #555;
text-align: center;
margin-bottom: 20px;
line-height: 1.6;
}
.highlight-number {
font-size: 20px;
font-weight: 700;
color: #ff8c42;
display: block;
margin-top: 10px;
}
.request-info-box {
background: #f8f9fa;
border-radius: 10px;
padding: 20px;
margin-bottom: 20px;
}
.rejection-box {
background: #fff5f5;
border: 2px solid #dc3545;
border-radius: 10px;
padding: 20px;
margin-bottom: 20px;
}
.rejection-box h4 {
color: #dc3545;
font-size: 14px;
font-weight: 700;
margin-bottom: 10px;
}
.rejection-box p {
color: #666;
font-size: 13px;
line-height: 1.5;
}
.note-message {
font-size: 12px;
color: #666;
line-height: 1.5;
padding: 15px;
background: #fffbf0;
border-left: 4px solid #ff9800;
border-radius: 5px;
margin-bottom: 20px;
}
.thank-you {
font-size: 14px;
font-weight: 700;
color: #333;
text-align: center;
}
.payment-buttons-modal {
display: flex;
gap: 15px;
margin: 25px 0;
}
.pay-online-btn-modal,
.pay-onsite-btn-modal {
flex: 1;
padding: 12px 20px;
border: none;
border-radius: 8px;
font-size: 14px;
font-weight: 700;
cursor: pointer;
transition: all 0.3s;
}
.pay-online-btn-modal {
background: linear-gradient(135deg, #ff8c42, #ff7a28);
color: white;
box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}
.pay-online-btn-modal:hover {
transform: translateY(-2px);
box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}
.pay-onsite-btn-modal {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}
.pay-onsite-btn-modal:hover {
transform: translateY(-2px);
box-shadow: 0 6px 18px rgba(43, 178, 74, 0.4);
}
.close-btn {
width: 100%;
background: linear-gradient(135deg, #ff8c42, #ff7a28);
color: white;
border: none;
padding: 12px 30px;
border-radius: 8px;
font-size: 14px;
font-weight: 700;
cursor: pointer;
margin-top: 25px;
transition: all 0.3s;
}
.close-btn:hover {
transform: translateY(-2px);
box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
}
/* Payment Gateway Modal */
.payment-modal {
padding: 40px 30px;
}
.modal-subtitle {
font-size: 14px;
color: #666;
margin-bottom: 30px;
text-align: center;
}
.payment-options {
display: grid;
grid-template-columns: 1fr 1fr;
gap: 20px;
margin-bottom: 30px;
}
.payment-option-btn {
display: flex;
flex-direction: column;
align-items: center;
gap: 15px;
padding: 30px 20px;
border: 2px solid #e0e0e0;
border-radius: 12px;
background: white;
cursor: pointer;
transition: all 0.3s;
}
.payment-option-btn:hover {
border-color: #ff8c42;
transform: translateY(-3px);
box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.payment-option-btn.gcash:hover {
border-color: #007dfe;
}
.payment-option-btn.maya:hover {
border-color: #00d632;
}
.payment-logo-placeholder {
width: 80px;
height: 80px;
background: linear-gradient(135deg, #f8f9fa, #e9ecef);
border-radius: 12px;
display: flex;
align-items: center;
justify-content: center;
overflow: hidden;
}
.payment-logo {
width: 100%;
height: 100%;
object-fit: contain;
}
.payment-option-btn span {
font-size: 16px;
font-weight: 700;
color: #333;
}
.payment-details {
background: #f8f9fa;
padding: 20px;
border-radius: 10px;
}
.payment-details p {
font-size: 14px;
color: #555;
margin-bottom: 8px;
}
.payment-details p:last-child {
margin-bottom: 0;
}
.payment-details strong {
font-weight: 700;
color: #333;
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
background: #ff8c42;
border-radius: 3px;
}
.notifications-container::-webkit-scrollbar-thumb:hover {
background: #e6763a;
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
}.sidebar {
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

.payment-options {
    grid-template-columns: 1fr;
}}
@media (max-width: 480px) {
.main-layout {
padding: 10px;
}.profile-card {
    padding: 15px;
}

.notification-card {
    padding: 15px 20px;
}

.request-header {
    flex-direction: column;
    gap: 10px;
}

.request-status {
    align-self: flex-start;
}

.modal-content {
    width: 95%;
    padding: 30px 20px;
}

.details-modal,
.payment-modal {
    padding: 30px 20px;
}}
</style>