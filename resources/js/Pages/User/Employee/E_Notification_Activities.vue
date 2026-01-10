<template>
  <LayoutEmployee>
    <Head>
      <title>Discussion</title>
    </Head>

    <div class="main-content">
      <div class="notification-header">
        <div class="tabs" role="tablist">
          <button
            type="button"
            class="tab-btn"
            :class="{ active: activeTab === 'notifications' }"
            @click="setActiveTab('notifications')"
          >
            ACTIVITIES
          </button>

          <Link
            :href="route('notification_request_employee')"
            class="tab-btn"
            @click="setActiveTab('requests')"
          >
            REQUEST
          </Link>
        </div>
        <div class="header-icon">
          <div class="small-logo" />
        </div>
      </div>

      <section class="filter-section">
        <div class="filter-left">
          <label class="filter-label">Sort</label>
          <select class="filter-select" v-model="sortOption">
            <option value="newest">NEWEST</option>
            <option value="relevant">RELEVANT</option>
            <option value="oldest">OLDEST</option>
          </select>
        </div>

        <div class="filter-right">
          <input
            class="search-input"
            type="text"
            v-model="searchQuery"
            placeholder="🔍 Search..."
          />
        </div>
      </section>

      <div class="notifications-container">
        <!-- Activities list (left) -->
        <div v-if="currentTab === 'activities' || activeTab === 'notifications'" class="activities-list">
          <div
            v-for="activity in filteredActivities"
            :key="activity.id"
            class="notification-card activity-card"
          >
            <img :src="activity.avatar" alt="avatar" class="notif-avatar" />
            <div class="notif-content">
              <div class="notif-text">
                <strong>{{ activity.user }}</strong>
                <span v-if="activity.others"> and {{ activity.others }} others</span>
                {{ activity.action }}
              </div>
              <div class="notif-time">{{ activity.time }}</div>
            </div>
          </div>
        </div>

        <!-- Requests list (right) -->
        <div v-if="currentTab === 'requests' || activeTab === 'requests'" class="requests-list">
          <div
            v-for="req in filteredRequests"
            :key="req.id"
            :class="['notification-card', 'request-card', req.status.toLowerCase()]"
          >
            <div class="request-header">
              <div class="request-info">
                <span class="request-number">{{ req.requestNumber }}</span>
                <div class="request-title">{{ req.title }}</div>
                <div class="request-time">{{ req.date }} · {{ req.time }}</div>
              </div>

              <div>
                <span :class="['request-status', req.status.toLowerCase()]">
                  {{ req.status }}
                </span>
              </div>
            </div>

            <div class="payment-buttons" v-if="req.type === 'document' && req.amount">
              <button class="pay-online-btn" @click="showPaymentGateway(req)">
                Pay Online • ₱{{ req.amount }}
              </button>
              <button class="pay-onsite-btn" @click="showOnsitePayment(req)">
                Pay Onsite
              </button>
            </div>
          </div>

          <div v-if="filteredRequests.length === 0" class="no-notifications">
            <p>No requests found.</p>
          </div>
        </div>
      </div>
    </div>
  </LayoutEmployee>
</template>


<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import LayoutEmployee from '@/Layouts/LayoutEmployee.vue'

const showSettings = ref(false)
const activeTab = ref('notifications')
const currentTab = ref('requests')
const sortOption = ref('newest')
const searchQuery = ref('')
const showPaymentModal = ref(false)
const showOnsiteModal = ref(false)
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

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_employee'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_employee'))
}

const switchTab = (tab) => {
    currentTab.value = tab
}

const openFAQ = () => {
    console.log('Opening FAQ')
}

const showPaymentGateway = (request) => {
    selectedRequest.value = request
    showPaymentModal.value = true
}

const closePaymentModal = () => {
    showPaymentModal.value = false
    selectedRequest.value = null
}

const processPayment = (gateway) => {
    alert(`Processing payment via ${gateway}...\nRequest: ${selectedRequest.value.title}\nAmount: ₱${selectedRequest.value.amount}`)
    closePaymentModal()
}

const showOnsitePayment = (request) => {
    selectedRequest.value = request
    showOnsiteModal.value = true
}

const closeOnsiteModal = () => {
    showOnsiteModal.value = false
    selectedRequest.value = null
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tabs {
    display: flex;
    flex: 1;
}

.tab-btn {
    flex: 1;
    background: none;
    border: none;
    color: white;
    padding: 16px 25px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    border-bottom: 3px solid transparent;
}

.tab-btn:hover {
    background: rgba(255,255,255,0.1);
}

.tab-btn.active {
    background: rgba(255,255,255,0.2);
    border-bottom-color: white;
}

.header-icon {
    padding: 16px 25px;
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

.filter-select {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    background: white;
    cursor: pointer;
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
    margin-bottom: 15px;
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

.payment-buttons {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.pay-online-btn,
.pay-onsite-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}

.pay-online-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.pay-online-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.pay-onsite-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}

.pay-onsite-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(43, 178, 74, 0.4);
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

/* Payment Gateway Modal */
.payment-modal {
    padding: 40px 30px;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    text-align: center;
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

/* Onsite Payment Modal */
.onsite-modal {
    padding: 40px 30px;
    text-align: center;
}

.modal-icon {
    margin-bottom: 20px;
}

.success-icon .checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: 700;
    box-shadow: 0 6px 20px rgba(43, 178, 74, 0.4);
    margin: 0 auto;
}

.request-number-display {
    font-size: 13px;
    color: #ff8c42;
    font-weight: 700;
    margin-bottom: 25px;
}

.onsite-details {
    text-align: left;
}

.onsite-message {
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

.thank-you {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    text-align: center;
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

    .payment-buttons {
        flex-direction: column;
    }

    .payment-options {
        grid-template-columns: 1fr;
    }

    .tab-btn {
        font-size: 16px;
        padding: 14px 20px;
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

    .onsite-modal,
    .payment-modal {
        padding: 30px 20px;
    }
}
</style>