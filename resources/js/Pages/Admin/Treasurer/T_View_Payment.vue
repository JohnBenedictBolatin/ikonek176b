<template>
    <Head>
        <title>View Payment</title>
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
                        <div class="profile-name">{{user.name || 'Unknown User'}}</div>
                        <div class="profile-role">{{displayRole}}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item active"
                    >
                        💰 View Payment
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToHistory"
                    >
                        📜 History
                    </Link>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- View Payment Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>View Payment</h2>
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
                                    <button @click="selectSort('amount-high')" :class="{ active: sortOption === 'amount-high' }">AMOUNT (HIGH-LOW)</button>
                                    <button @click="selectSort('amount-low')" :class="{ active: sortOption === 'amount-low' }">AMOUNT (LOW-HIGH)</button>
                                </div>
                            </div>
                            <div class="filter-dropdown-wrapper">
                                <button class="filter-dropdown-btn" @click="toggleFilterDropdown">
                                    {{ filterOption.toUpperCase() }}
                                    <span class="filter-arrow" :class="{ rotated: showFilterDropdown }">▼</span>
                                </button>
                                <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                    <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                    <button @click="selectFilter('cash')" :class="{ active: filterOption === 'cash' }">CASH</button>
                                    <button @click="selectFilter('gcash')" :class="{ active: filterOption === 'gcash' }">GCASH</button>
                                    <button @click="selectFilter('maya')" :class="{ active: filterOption === 'maya' }">MAYA</button>
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

                    <!-- Payments Container -->
                    <div class="requests-container">
                        <div 
                            v-for="payment in filteredPayments" 
                            :key="payment.id"
                            class="request-card"
                        >
                            <div class="request-header">
                                <div class="request-user-info">
                                    <div class="request-details">
                                        <h3 class="request-name">{{ payment.requestor }}</h3>
                                        <p class="request-type">
                                            <span class="payment-method-highlight" :class="payment.paymentMethod.toLowerCase()">
                                                {{ payment.paymentMethod.toUpperCase() }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="request-body">
                                <p class="payment-info"><strong>Document:</strong> {{ payment.document }}</p>
                                <p class="payment-info"><strong>Amount:</strong> ₱{{ payment.amount.toFixed(2) }}</p>
                                <p class="request-date">{{ payment.date }}</p>
                                <button @click="openModal(payment)" class="view-btn">
                                    View Details
                                </button>
                            </div>
                        </div>

                        <!-- No payments message -->
                        <div v-if="filteredPayments.length === 0" class="no-requests">
                            <p>No pending payments found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details Modal -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">✕</button>

                <div class="modal-content">
                    <!-- Top Section -->
                    <div class="modal-top">
                        <div class="modal-user-section">
                            <div>
                                <h3 class="modal-name">{{ selectedPayment.requestor }}</h3>
                                <p class="modal-label">Payment Request</p>
                            </div>
                        </div>
                        <div class="modal-role-section">
                            <h3 class="modal-payment-method" :class="selectedPayment.paymentMethod.toLowerCase()">
                                {{ selectedPayment.paymentMethod.toUpperCase() }}
                            </h3>
                        </div>
                        <div class="modal-proof-section" v-if="selectedPayment.paymentMethod !== 'Cash'">
                            <button class="proof-btn" @click="viewReceipt">
                                View Digital Receipt
                            </button>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="modal-details">
                        <!-- Payment Details Grid -->
                        <div class="details-grid">
                            <div class="detail-item">
                                <p class="detail-label">Document Type:</p>
                                <p class="detail-value">{{ selectedPayment.documentType }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Document Number:</p>
                                <p class="detail-value">{{ selectedPayment.document }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Amount:</p>
                                <p class="detail-value">₱{{ selectedPayment.amount.toFixed(2) }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Date Submitted:</p>
                                <p class="detail-value">{{ selectedPayment.date }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Contact Number:</p>
                                <p class="detail-value">{{ selectedPayment.contact }}</p>
                            </div>
                        </div>

                        <!-- Payment Method Specific Details -->
                        <div class="details-grid-2" v-if="selectedPayment.paymentMethod !== 'Cash'">
                            <div class="detail-item">
                                <p class="detail-label">Transaction ID:</p>
                                <p class="detail-value">{{ selectedPayment.transactionId }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Payment Platform:</p>
                                <p class="detail-value">{{ selectedPayment.paymentMethod }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Payment Time:</p>
                                <p class="detail-value">{{ selectedPayment.paymentTime }}</p>
                            </div>
                        </div>

                        <!-- Full Address -->
                        <div class="detail-item-full">
                            <p class="detail-label">Requestor Address:</p>
                            <p class="detail-value">{{ selectedPayment.address }}</p>
                        </div>

                        <!-- Notes Section -->
                        <div class="comment-section">
                            <label class="comment-label">Notes (Optional):</label>
                            <textarea 
                                v-model="notes"
                                placeholder="Add any notes about this payment..." 
                                class="comment-textarea"
                                rows="4"
                            ></textarea>
                        </div>

                        <!-- Action Button -->
                        <div class="modal-actions">
                            <button @click="confirmPayment" class="approve-btn">
                                Confirm Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmModal" class="modal-overlay" @click="closeConfirmModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content">
                    <div class="confirm-icon">✓</div>
                    <h3 class="confirm-title">Payment Confirmed!</h3>
                    <p class="confirm-message">
                        Payment from <strong>{{ selectedPayment.requestor }}</strong> for 
                        <strong>{{ selectedPayment.document }}</strong> has been successfully confirmed.
                    </p>
                    <button @click="closeConfirmModal" class="confirm-ok-btn">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Receipt Viewer Modal -->
        <div v-if="showReceiptModal" class="modal-overlay" @click="closeReceiptModal">
            <div class="receipt-modal-container" @click.stop>
                <button @click="closeReceiptModal" class="modal-close">✕</button>
                <div class="receipt-modal-content">
                    <h3 class="receipt-title">Digital Receipt</h3>
                    <div class="receipt-image-container">
                        <img :src="selectedPayment.receiptImage" alt="Payment Receipt" class="receipt-image" />
                    </div>
                    <div class="receipt-details">
                        <p><strong>Transaction ID:</strong> {{ selectedPayment.transactionId }}</p>
                        <p><strong>Platform:</strong> {{ selectedPayment.paymentMethod }}</p>
                        <p><strong>Amount:</strong> ₱{{ selectedPayment.amount.toFixed(2) }}</p>
                        <p><strong>Date & Time:</strong> {{ selectedPayment.paymentTime }}</p>
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
const isModalOpen = ref(false)
const selectedPayment = ref(null)
const notes = ref('')
const showConfirmModal = ref(false)
const showReceiptModal = ref(false)

// Sample payments data
const payments = ref([
    {
        id: 1,
        requestor: 'John Rodriguez',
        document: 'Invoice #001',
        documentType: 'Barangay Clearance',
        amount: 150.00,
        date: '09/28/2025',
        dateObj: new Date('2025-09-28'),
        paymentMethod: 'GCash',
        contact: '09789932832',
        address: 'Phase 2 Package 3, Barangay Commonwealth',
        transactionId: 'GC-2025092801234',
        paymentTime: '09/28/2025 10:30 AM',
        receiptImage: '/assets/receipt-sample.jpg'
    },
    {
        id: 2,
        requestor: 'Maria Santos',
        document: 'Invoice #002',
        documentType: 'Certificate of Indigency',
        amount: 50.00,
        date: '09/27/2025',
        dateObj: new Date('2025-09-27'),
        paymentMethod: 'Cash',
        contact: '09178234567',
        address: 'Phase 1 Package 5, Barangay Commonwealth',
        transactionId: 'N/A',
        paymentTime: 'N/A',
        receiptImage: null
    },
    {
        id: 3,
        requestor: 'Roberto Cruz',
        document: 'Invoice #003',
        documentType: 'Business Permit',
        amount: 500.00,
        date: '09/26/2025',
        dateObj: new Date('2025-09-26'),
        paymentMethod: 'maya',
        contact: '09267891234',
        address: 'Phase 3 Package 1, Barangay Commonwealth',
        transactionId: 'PM-2025092601567',
        paymentTime: '09/26/2025 2:15 PM',
        receiptImage: '/assets/receipt-sample.jpg'
    },
    {
        id: 4,
        requestor: 'Ana Reyes',
        document: 'Invoice #004',
        documentType: 'Barangay ID',
        amount: 100.00,
        date: '09/25/2025',
        dateObj: new Date('2025-09-25'),
        paymentMethod: 'GCash',
        contact: '09456123789',
        address: 'Phase 2 Package 7, Barangay Commonwealth',
        transactionId: 'GC-2025092502345',
        paymentTime: '09/25/2025 3:45 PM',
        receiptImage: '/assets/receipt-sample.jpg'
    },
    {
        id: 5,
        requestor: 'Carlos Mendoza',
        document: 'Invoice #005',
        documentType: 'Certificate of Residency',
        amount: 75.00,
        date: '09/24/2025',
        dateObj: new Date('2025-09-24'),
        paymentMethod: 'Cash',
        contact: '09123456789',
        address: 'Phase 1 Package 2, Barangay Commonwealth',
        transactionId: 'N/A',
        paymentTime: 'N/A',
        receiptImage: null
    }
])

// Computed filtered payments
const filteredPayments = computed(() => {
    let filtered = [...payments.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.requestor.toLowerCase().includes(query) ||
            item.document.toLowerCase().includes(query) ||
            item.documentType.toLowerCase().includes(query) ||
            item.paymentMethod.toLowerCase().includes(query)
        )
    }

    // Payment method filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.paymentMethod.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'amount-high') {
        filtered.sort((a, b) => b.amount - a.amount)
    } else if (sortOption.value === 'amount-low') {
        filtered.sort((a, b) => a.amount - b.amount)
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
    router.visit(route('login_treasurer'))
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const openModal = (payment) => {
    selectedPayment.value = payment
    notes.value = ''
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedPayment.value = null
    notes.value = ''
}

const confirmPayment = () => {
    console.log('Confirming payment:', selectedPayment.value.requestor)
    console.log('Notes:', notes.value)
    
    // Remove payment from list
    const index = payments.value.findIndex(p => p.id === selectedPayment.value.id)
    if (index > -1) {
        payments.value.splice(index, 1)
    }
    
    closeModal()
    showConfirmModal.value = true
}

const closeConfirmModal = () => {
    showConfirmModal.value = false
    selectedPayment.value = null
}

const viewReceipt = () => {
    showReceiptModal.value = true
}

const closeReceiptModal = () => {
    showReceiptModal.value = false
}

const navigateToHistory = () => {
    router.visit(route('history_treasurer'))
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
    min-width: 200px;
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

/* Requests Container */
.requests-container {
    padding: 25px;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.request-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.request-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.request-header {
    margin-bottom: 15px;
}

.request-user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.request-details {
    flex: 1;
}

.request-name {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.request-type {
    font-size: 13px;
    color: #666;
    margin: 5px 0;
}

.payment-method-highlight {
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    color: white;
}

.payment-method-highlight.cash {
    background: #239640;
}

.payment-method-highlight.gcash {
    background: linear-gradient(135deg, #007DFF, #0062CC);
}

.payment-method-highlight.maya {
    background: linear-gradient(135deg, #00D632, #00A827);
}

.payment-info {
    font-size: 14px;
    color: #555;
    margin: 5px 0;
}

.request-date {
    font-size: 13px;
    color: #999;
    margin: 10px 0 0 0;
}

.request-body {
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.view-btn {
    background: #ff7a28;
    color: white;
    border: none;
    margin-left: 1250px;
    padding: 10px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 122, 40, 0.3);
}

.view-btn:hover {
    background: #e66a1f;
    transform: translateY(-1px);
}

.no-requests {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.no-requests p {
    font-size: 16px;
    color: #999;
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
    padding: 30px;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
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

.modal-top {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 20px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 25px;
}

.modal-user-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.modal-name {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.modal-label {
    font-size: 13px;
    color: #666;
    margin: 0;
}

.modal-role-section {
    display: flex;
    margin-bottom: 20px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.modal-payment-method {
    font-size: 19px;
    font-weight: 500;
    margin: 20px 0 0 0;
    margin-left: 100px;
    padding: 10px 20px;
    border-radius: 10px;
    color: white;
}

.modal-payment-method.cash {
    background: #239640;
}

.modal-payment-method.gcash {
    background: linear-gradient(135deg, #007DFF, #0062CC);
}

.modal-payment-method.paymaya {
    background: linear-gradient(135deg, #00D632, #00A827);
}

.modal-proof-section {
    display: flex;
    align-items: center;
    justify-content: center;
}

.proof-btn {
    background: #ff7a28;
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 122, 40, 0.3);
    text-align: center;
}

.proof-btn:hover {
    background: #e66a1f;
}

.modal-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
}

.details-grid-2 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.detail-item {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 10px;
}

.detail-item-full {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 10px;
}

.detail-label {
    font-size: 12px;
    font-weight: 700;
    color: #666;
    margin: 0 0 5px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 14px;
    color: #333;
    font-weight: 600;
    margin: 0;
}

.comment-section {
    margin-top: 5px;
}

.comment-label {
    font-size: 13px;
    font-weight: 600;
    color: #666;
    margin-bottom: 8px;
    display: block;
}

.comment-textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    transition: border-color 0.3s;
}

.comment-textarea:focus {
    outline: none;
    border-color: #239640;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 10px;
}

.approve-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.approve-btn:hover {
    background: #1e7d36;
}

/* Confirmation Modal */
.confirm-modal-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    text-align: center;
}

.confirm-modal-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.confirm-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #239640;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(35, 150, 64, 0.3);
}

.confirm-title {
    font-size: 26px;
    font-weight: 700;
    color: #239640;
    margin: 0;
}

.confirm-message {
    font-size: 15px;
    color: #666;
    line-height: 1.6;
    margin: 0;
}

.confirm-ok-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 12px 40px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
    margin-top: 10px;
}

.confirm-ok-btn:hover {
    background: #1e7d36;
}

/* Receipt Modal */
.receipt-modal-container {
    background: white;
    border-radius: 20px;
    padding: 35px;
    width: 90%;
    max-width: 600px;
    max-height: 85vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.receipt-modal-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.receipt-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0;
    text-align: center;
}

.receipt-image-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
}

.receipt-image {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.receipt-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.receipt-details p {
    margin: 0;
    font-size: 14px;
    color: #333;
}

.receipt-details strong {
    color: #666;
    font-weight: 600;
}

/* Custom Scrollbar */
.requests-container::-webkit-scrollbar,
.modal-container::-webkit-scrollbar,
.receipt-modal-container::-webkit-scrollbar {
    width: 6px;
}

.requests-container::-webkit-scrollbar-track,
.modal-container::-webkit-scrollbar-track,
.receipt-modal-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb,
.modal-container::-webkit-scrollbar-thumb,
.receipt-modal-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb:hover,
.modal-container::-webkit-scrollbar-thumb:hover,
.receipt-modal-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Responsive */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }
    
    .modal-top {
        grid-template-columns: 1fr;
    }
    
    .details-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .view-btn {
        margin-left: auto;
        display: block;
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
    
    .modal-container,
    .receipt-modal-container,
    .confirm-modal-container {
        width: 95%;
        padding: 20px;
    }
    
    .details-grid,
    .details-grid-2 {
        grid-template-columns: 1fr;
    }
    
    .modal-top {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .view-btn {
        margin-left: 0;
        width: 100%;
    }
}</style>