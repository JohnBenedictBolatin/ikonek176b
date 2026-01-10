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
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        View Payment
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
                                    <svg class="filter-arrow" :class="{ rotated: showSortDropdown }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
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
                                    <svg class="filter-arrow" :class="{ rotated: showFilterDropdown }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                    <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                    <button @click="selectFilter('cash')" :class="{ active: filterOption === 'cash' }">CASH</button>
                                    <button @click="selectFilter('gcash')" :class="{ active: filterOption === 'gcash' }">GCASH</button>
                                    <button @click="selectFilter('maya')" :class="{ active: filterOption === 'maya' }">MAYA</button>
                                    <button @click="selectFilter('onsite')" :class="{ active: filterOption === 'onsite' }">ONSITE</button>
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

                    <!-- Payments Container -->
                    <div class="requests-container">
                        <div 
                            v-for="payment in filteredPayments" 
                            :key="payment.id"
                            class="request-card"
                        >
                            <div class="request-content">
                                <div class="request-left">
                                    <img :src="payment.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" />
                                    <div class="request-info">
                                        <h3 class="request-name">{{ payment.requestor }}</h3>
                                        <p class="request-doc-type">{{ payment.documentType }}</p>
                                        <p class="request-ref-code">{{ payment.document }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <div class="request-status-badge-wrapper">
                                        <span class="payment-status-badge" :class="payment.status?.toLowerCase() || 'pending'">
                                            {{ payment.status || 'PENDING' }}
                                        </span>
                                    </div>
                                    <p class="request-date">{{ payment.date }}</p>
                                    <button 
                                        @click.stop="openModal(payment)" 
                                        class="view-btn" 
                                        type="button"
                                    >
                                        View Payment Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- No payments message -->
                        <div v-if="filteredPayments.length === 0" class="no-requests">
                            <p>No payments found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details Modal -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="modal-content">
                    <!-- Top Section -->
                    <div class="modal-top" style="grid-template-columns: 1fr 1fr; gap: 15px; align-items: start;">
                        <div class="modal-user-section" style="display: flex; align-items: center; gap: 12px;">
                            <img :src="selectedPayment.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" style="width: 60px; height: 60px; flex-shrink: 0;" />
                            <div style="flex: 1; min-width: 0;">
                                <h3 class="modal-name" style="font-size: 18px; margin-bottom: 4px;">{{ selectedPayment.requestor || 'Unknown' }}</h3>
                                <p class="modal-label" style="font-size: 12px; margin: 0;">Payment Request</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: row; gap: 15px; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ff8c42 0%, #ff7a28 100%); color: white; padding: 10px 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(255, 122, 40, 0.3); min-height: fit-content;">
                            <div style="flex: 1;">
                                <p style="font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; opacity: 0.9;">Payment Method</p>
                                <h3 style="font-size: 15px; font-weight: 700; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.2); line-height: 1.2;">{{ (selectedPayment.paymentMethod || 'UNKNOWN METHOD').toUpperCase() }}</h3>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.3); flex-shrink: 0;"></div>
                            <div style="flex: 0 0 auto; text-align: right;">
                                <p style="font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; opacity: 0.9;">Document Number</p>
                                <p style="font-size: 13px; font-weight: 700; margin: 0; font-family: monospace; letter-spacing: 0.8px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ selectedPayment.document }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Proof of Payment Section -->
                    <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <p style="font-size: 12px; font-weight: 600; color: #666; margin: 0 0 5px 0;">Proof of Payment</p>
                                <p style="font-size: 11px; color: #999; margin: 0;">Transaction ID: {{ selectedPayment.transactionId || 'N/A' }}</p>
                            </div>
                            <button 
                                v-if="selectedPayment.receiptImage" 
                                class="proof-btn" 
                                @click="viewReceipt"
                                style="background: #ff7a28; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 122, 40, 0.3);"
                            >
                                View Proof
                            </button>
                            <p v-else class="no-receipt-text" style="color: #999; font-size: 12px; font-style: italic; margin: 0;">No proof of payment uploaded</p>
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

                        <!-- Payment Method Specific Details - Always show transaction ID -->
                        <div class="details-grid-2" v-if="selectedPayment.paymentMethodKey !== 'cash'">
                            <div class="detail-item">
                                <p class="detail-label">Transaction ID:</p>
                                <p class="detail-value">{{ selectedPayment.transactionId || 'N/A' }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Payment Method:</p>
                                <p class="detail-value">{{ selectedPayment.paymentMethod || 'N/A' }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Payment Time:</p>
                                <p class="detail-value">{{ selectedPayment.paymentTime || 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <!-- Transaction ID for Cash payments too -->
                        <div class="details-grid-2" v-if="selectedPayment.paymentMethodKey === 'cash' && selectedPayment.transactionId && selectedPayment.transactionId !== 'N/A'">
                            <div class="detail-item">
                                <p class="detail-label">Transaction ID:</p>
                                <p class="detail-value">{{ selectedPayment.transactionId }}</p>
                            </div>
                        </div>

                        <!-- Action Button - Only show for PENDING payments -->
                        <div class="modal-actions" v-if="selectedPayment.status === 'PENDING'">
                            <button @click="openApproveModal" class="approve-btn">
                                Confirm Payment
                            </button>

                            <button @click="openRejectModal" class="reject-btn">
                                Reject Payment
                            </button>
                        </div>
                        
                        <!-- Status display for non-pending payments -->
                        <div class="modal-status-display" v-else>
                            <div class="status-info">
                                <p class="status-label">Payment Status:</p>
                                <span class="payment-status-badge-large" :class="selectedPayment.status?.toLowerCase() || 'pending'">
                                    {{ selectedPayment.status || 'PENDING' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmModal" class="modal-overlay" @click="closeConfirmModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content">
                    <div class="confirm-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 32px; height: 32px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="confirm-title">Payment Confirmed!</h3>
                    <p class="confirm-message">
                        Payment from <strong>{{ confirmedPaymentSnapshot?.requestor || 'Unknown' }}</strong> for 
                        <strong>{{ confirmedPaymentSnapshot?.document || 'N/A' }}</strong> has been successfully confirmed.
                    </p>
                    <button @click="closeConfirmModal" class="confirm-ok-btn">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Approve Confirmation Modal (Are you sure?) -->
        <div v-if="showApproveModal" class="modal-overlay" @click="closeApproveModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content">
                    <div class="confirm-icon" style="background: #239640;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 32px; height: 32px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="confirm-title">Confirm Payment</h3>
                    <p class="confirm-message">
                        Are you sure you want to confirm the payment from 
                        <strong>{{ selectedPayment?.requestor }}</strong> for 
                        <strong>{{ selectedPayment?.document }}</strong>?
                        This action cannot be undone.
                    </p>
                    <div class="confirm-actions">
                        <button @click="closeApproveModal" class="confirm-cancel-btn">Cancel</button>
                        <button @click="confirmPayment" class="confirm-approve-btn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Confirmation Modal (Are you sure?) -->
        <div v-if="showRejectModal" class="modal-overlay" @click="closeRejectModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content">
                    <div class="confirm-icon">!</div>
                    <h3 class="confirm-title">Reject Payment</h3>
                    <p class="confirm-message">
                        Are you sure you want to reject the payment from 
                        <strong>{{ selectedPayment?.requestor }}</strong> for 
                        <strong>{{ selectedPayment?.document }}</strong>?
                        This action cannot be undone.
                    </p>
                    <div class="confirm-actions">
                        <button @click="closeRejectModal" class="confirm-cancel-btn">Cancel</button>
                        <button @click="rejectPaymentConfirmed" class="confirm-reject-btn">Reject</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Success Modal -->
        <div v-if="showRejectConfirmModal" class="modal-overlay" @click="closeRejectConfirmModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content reject-modal-content">
                    <div class="confirm-icon reject-icon">✖</div>
                    <h3 class="confirm-title reject-title">Payment Rejected</h3>
                    <p class="confirm-message">
                        Payment from <strong>{{ rejectedPaymentSnapshot?.requestor }}</strong> for 
                        <strong>{{ rejectedPaymentSnapshot?.document }}</strong> has been rejected.
                    </p>
                    <button @click="closeRejectConfirmModal" class="confirm-ok-btn reject-ok-btn">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Receipt Viewer Modal -->
        <div v-if="showReceiptModal && selectedPayment" class="modal-overlay" @click="closeReceiptModal">
            <div class="receipt-modal-container" @click.stop>
                <button @click="closeReceiptModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="receipt-modal-content">
                    <h3 class="receipt-title">Proof of Payment</h3>
                    <div class="receipt-image-container" v-if="selectedPayment.receiptImage">
                        <img :src="selectedPayment.receiptImage" alt="Payment Receipt" class="receipt-image" @error="handleImageError" />
                    </div>
                    <div v-else class="no-receipt-container">
                        <p class="no-receipt-message">No proof of payment image available</p>
                    </div>
                    <div class="receipt-details">
                        <p><strong>Transaction ID:</strong> {{ selectedPayment.transactionId || 'N/A' }}</p>
                        <p><strong>Payment Method:</strong> {{ (selectedPayment.paymentMethod || 'N/A').toUpperCase() }}</p>
                        <p><strong>Amount:</strong> ₱{{ selectedPayment.amount?.toFixed(2) || '0.00' }}</p>
                        <p><strong>Date & Time:</strong> {{ selectedPayment.paymentTime || 'N/A' }}</p>
                        <p v-if="selectedPayment.status"><strong>Status:</strong> {{ selectedPayment.status }}</p>
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
const showApproveModal = ref(false)
const showConfirmModal = ref(false)
const showReceiptModal = ref(false)
const showRejectModal = ref(false)
const showRejectConfirmModal = ref(false)
const rejectedPaymentSnapshot = ref(null)
const confirmedPaymentSnapshot = ref(null)

// --- payments from server (Inertia props) ---
// page.props can vary: check both shapes
const serverPayments = computed(() => {
  // try the common shapes Inertia exposes
  return page?.props?.value?.payments ?? page?.props?.payments ?? []
})

// Convert server payments into the same shape the UI expects, and ensure dateObj is a JS Date
const payments = ref([])

const initializePaymentsFromServer = () => {
  payments.value = (serverPayments.value || []).map(p => {
    // compute dateObj safely
    let dateObj = null
    try {
      dateObj = p.date_iso ? new Date(p.date_iso) : (p.date ? new Date(p.date) : new Date())
    } catch (e) {
      dateObj = new Date()
    }

    // Normalize incoming payment method into stable keys:
    // resultMethod will be one of: 'gcash', 'maya', 'cash', 'onsite'
    const raw = (p.paymentMethod ?? '').toString().trim().toLowerCase()

    let resultMethod = 'onsite' // safe default
    if (raw.includes('gcash')) {
    resultMethod = 'gcash'
    } else if (raw.includes('maya')) {
    resultMethod = 'maya'
    } else if (raw.includes('cash')) {
    resultMethod = 'cash'
    } else if (raw.includes('onsite') || raw.includes('on-site') || raw.includes('on site')) {
    resultMethod = 'onsite'
    } else if (raw === '') {
    // if backend gave empty, try to detect from paymentMethodRaw (if available)
    const raw2 = (p.paymentMethodRaw ?? '').toString().trim().toLowerCase();
    if (raw2.includes('onsite') || raw2.includes('on-site') || raw2.includes('on site')) {
        resultMethod = 'onsite'
    }
    // else leave default onsite or set to 'manual'
    }


    return {
      id: p.id,
      requestor: p.requestor ?? 'Unknown',
      document: p.document ?? 'N/A',
      documentType: p.documentType ?? 'N/A',
      amount: Number(p.amount ?? 0),
      date: p.date ?? (dateObj.toLocaleDateString()),
      dateObj: dateObj,
      // keep a lowercase stable key for logic / classes:
      paymentMethod: (p.paymentMethod ?? resultMethod).toUpperCase(),
      // keep a lowercase stable key for logic / classes:
      paymentMethodKey: resultMethod,
      // keep original casing string available if you want the original label:
      paymentMethodRaw: p.paymentMethod ?? null,
      contact: p.contact ?? 'N/A',
      address: p.address ?? 'N/A',
      transactionId: p.transactionId ?? 'N/A',
      paymentTime: p.paymentTime ?? 'N/A',
      receiptImage: p.receiptImage ?? null,
      status: (p.status ?? 'PENDING').toUpperCase(),
      profileImg: p.profileImg ?? '/assets/DEFAULT.jpg',
    }
  })
}

// initialize once on mount
onMounted(() => {
  initializePaymentsFromServer()
})

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
            item.paymentMethod.toLowerCase().includes(query) ||
            item.transactionId.toLowerCase().includes(query) ||
            (item.paymentMethodKey && item.paymentMethodKey.toLowerCase().includes(query))
        )
    }

    // Payment method filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.paymentMethodKey === filterOption.value.toLowerCase()
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

// -- helper to call backend and handle success/failure
const sendStatusUpdate = (paymentId, status, notes = '') => {
  // Use a direct URL so Ziggy missing-route errors won't break the call.
  const url = `/payments/${paymentId}/status`

  return new Promise((resolve, reject) => {
    router.post(url, { status, notes }, {
      preserveState: true,
      preserveScroll: true,
      only: [],
      onSuccess: (page) => {
        resolve(page)
      },
      onError: (errors) => {
        // Handle Inertia validation errors gracefully
        const errorMessage = errors?.payment_status || errors?.payment || errors?.message || 'An error occurred. Please try again.'
        reject({ message: errorMessage, response: { data: { message: errorMessage } } })
      },
      onFinish: () => {
        // optional cleanup hook
      }
    })
  })
}

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
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedPayment.value = null
}

const confirmPayment = async () => {
  if (!selectedPayment.value) return

  const paymentId = selectedPayment.value.id

  // Store snapshot for success confirmation message
  confirmedPaymentSnapshot.value = {
    requestor: selectedPayment.value.requestor,
    document: selectedPayment.value.document,
  }

  // Close the approval confirmation modal
  closeApproveModal()

  try {
    await sendStatusUpdate(paymentId, 'APPROVED', '')

    // Remove payment from list since it's no longer PENDING
    const index = payments.value.findIndex(p => p.id === paymentId)
    if (index > -1) payments.value.splice(index, 1)

    // Close main modal and show success confirmation
    closeModal()
    showConfirmModal.value = true
  } catch (err) {
    console.error('Failed to confirm payment:', err)
    // Handle error gracefully - show user-friendly message
    const errorMessage = err?.response?.data?.message || err?.message || 'Failed to confirm payment. Please try again.'
    alert(errorMessage)
    confirmedPaymentSnapshot.value = null
  }
}

const closeConfirmModal = () => {
    showConfirmModal.value = false
    confirmedPaymentSnapshot.value = null
}

const viewReceipt = () => {
    if (selectedPayment.value && selectedPayment.value.receiptImage) {
        showReceiptModal.value = true
    } else {
        alert('No proof of payment available for this payment.')
    }
}

const handleImageError = (event) => {
    event.target.style.display = 'none'
    const container = event.target.closest('.receipt-image-container')
    if (container) {
        container.innerHTML = '<p class="no-receipt-message">Failed to load image</p>'
    }
}

const closeReceiptModal = () => {
    showReceiptModal.value = false
}

const openApproveModal = () => {
  if (!selectedPayment.value) return
  showApproveModal.value = true
}

const closeApproveModal = () => {
    showApproveModal.value = false
}

const openRejectModal = () => {
  if (!selectedPayment.value) return
  showRejectModal.value = true
}

// close without rejecting
const closeRejectModal = () => {
    showRejectModal.value = false
}

const rejectPaymentConfirmed = async () => {
  if (!selectedPayment.value) {
    showRejectModal.value = false
    return
  }

  const paymentId = selectedPayment.value.id

  // snapshot for UI feedback
  rejectedPaymentSnapshot.value = {
    id: selectedPayment.value.id,
    requestor: selectedPayment.value.requestor,
    document: selectedPayment.value.document,
  }

  try {
    await sendStatusUpdate(paymentId, 'REJECTED', '')

    // Remove payment from list since it's no longer PENDING
    const index = payments.value.findIndex(p => p.id === paymentId)
    if (index > -1) payments.value.splice(index, 1)

    // Close modals and show confirmation
    showRejectModal.value = false
    closeModal()
    showRejectConfirmModal.value = true
  } catch (err) {
    console.error('Failed to reject payment:', err)
    alert('Failed to reject payment. Check console for details.')
  }
}

// close the reject-success modal
const closeRejectConfirmModal = () => {
    showRejectConfirmModal.value = false
    rejectedPaymentSnapshot.value = null
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

.modal-actions {
  display: flex;
  gap: 12px;
  margin-top: 12px;
}
.approve-btn {
  padding: 8px 14px;
  border-radius: 6px;
  border: none;
  background-color: #28a745;
  color: white;
  cursor: pointer;
}
.reject-btn {
  padding: 8px 14px;
  border-radius: 6px;
  border: none;
  background-color: #dc3545;
  color: white;
  cursor: pointer;
}

/* Confirm modal action buttons */
.confirm-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 12px;
}
.confirm-cancel-btn {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: white;
  cursor: pointer;
}
.confirm-approve-btn {
  padding: 8px 12px;
  border-radius: 6px;
  border: none;
  background-color: #239640;
  color: white;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.confirm-approve-btn:hover {
  background-color: #1e7d36;
  transform: translateY(-1px);
}

.confirm-reject-btn {
  padding: 8px 12px;
  border-radius: 6px;
  border: none;
  background-color: #dc3545;
  color: white;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.confirm-reject-btn:hover {
  background-color: #c82333;
  transform: translateY(-1px);
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
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
}

.search-btn:hover {
    background: #e9ecef;
}

.search-btn svg {
    width: 18px;
    height: 18px;
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

.request-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.request-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.modal-avatar {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    flex-shrink: 0;
}

.request-info {
    flex: 1;
}

.request-name {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.request-doc-type {
    font-size: 14px;
    color: #239640;
    font-weight: 600;
    margin: 3px 0;
}

.request-ref-code {
    font-size: 12px;
    color: #999;
    margin: 3px 0;
    font-family: monospace;
}

.request-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.request-status-badge-wrapper {
    display: flex;
    align-items: center;
}

.request-date {
    font-size: 13px;
    color: #999;
    margin: 0;
}

.payment-status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-status-badge.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.payment-status-badge.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.payment-status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
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

.view-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
    pointer-events: auto;
    position: relative;
    z-index: 10;
}

.view-btn:hover {
    background: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.4);
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


.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 10px;
}

.modal-status-display {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    text-align: center;
}

.status-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.status-label {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    margin: 0;
}

.payment-status-badge-large {
    padding: 8px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-status-badge-large.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.payment-status-badge-large.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.payment-status-badge-large.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.no-receipt-text {
    color: #999;
    font-size: 13px;
    font-style: italic;
    margin: 0;
    padding: 10px;
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
    margin: 0 auto 20px;
    box-shadow: 0 4px 15px rgba(35, 150, 64, 0.3);
}

.confirm-icon svg {
    width: 32px;
    height: 32px;
    stroke-width: 3;
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

/* Reject Confirmation Modal - Red Theme */
.reject-icon {
    background: #dc3545 !important;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
}

.reject-title {
    color: #dc3545 !important;
}

.reject-ok-btn {
    background: #dc3545 !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
}

.reject-ok-btn:hover {
    background: #c82333 !important;
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
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.no-receipt-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
}

.no-receipt-message {
    color: #999;
    font-size: 14px;
    font-style: italic;
    margin: 0;
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
    background: #888;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb:hover,
.modal-container::-webkit-scrollbar-thumb:hover,
.receipt-modal-container::-webkit-scrollbar-thumb:hover {
    background: #666;
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

    .request-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .request-right {
        width: 100%;
        align-items: flex-start;
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
        width: 100%;
    }
}</style>