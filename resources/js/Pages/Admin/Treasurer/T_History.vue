<template>
    <Head>
        <title>Payment History</title>
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
                        <a href="#" class="settings-item" @click.prevent.stop="openTermsModal">TERMS & CONDITIONS</a>
                        <Link href="#" class="settings-item" @click="logout">SIGN OUT</Link>
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
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToViewPayment"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        Payments
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
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- History Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Payment History</h2>
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
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Profile</th>
                                    <th style="width: 180px; text-align: left; padding-left: 20px;">Full Name</th>
                                    <th style="width: 200px; text-align: center;">Role</th>
                                    <th style="width: 200px; text-align: left; padding-left: 25px;">Document Type</th>
                                    <th style="width: 120px; text-align: center;">Payment No.</th>
                                    <th style="width: 110px; text-align: center;">Amount</th>
                                    <th style="width: 100px; text-align: center;">Method</th>
                                    <th style="width: 110px; text-align: center;">Date</th>
                                    <th style="width: 100px; text-align: center;">Status</th>
                                    <th style="width: 120px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="payment in filteredHistory" 
                                    :key="payment.id"
                                    class="user-row"
                                >
                                    <td style="text-align: center; padding: 15px 8px;">
                                        <img :src="payment.profileImg || '/assets/DEFAULT.jpg'" :alt="payment.name" class="user-avatar" />
                                    </td>
                                    <td style="text-align: left; padding: 15px 12px 15px 20px;" class="user-name">{{ payment.name }}</td>
                                    <td style="text-align: center; padding: 15px 15px;">
                                        <span class="role-badge" :class="getRoleClass(payment.role)">
                                            {{ payment.role.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: left; padding: 15px 12px 15px 25px;">{{ payment.doc_type }}</td>
                                    <td style="text-align: center; padding: 15px 12px;" class="payment-no">{{ payment.payment_no }}</td>
                                    <td style="text-align: center; padding: 15px 12px;" class="amount-cell">₱{{ payment.amount.toFixed(2) }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">
                                        <span class="method-badge" :class="getMethodClass(payment.paymentMethod || payment.method)">
                                            {{ payment.paymentMethod || payment.method?.toUpperCase() || 'ONSITE' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; padding: 15px 12px;">{{ payment.date }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">
                                        <span class="action-badge" :class="getActionClass(payment.status)">
                                            {{ payment.status || 'APPROVED' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; padding: 15px 12px;">
                                        <button
                                            v-if="payment.receiptImage"
                                            class="receipt-btn"
                                            @click="openReceipt(payment)"
                                        >
                                            VIEW RECEIPT
                                        </button>
                                        <button
                                            v-else
                                            class="no-receipt-btn"
                                            disabled
                                        >
                                            NO RECEIPT
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- No history message -->
                        <div v-if="filteredHistory.length === 0" class="no-users">
                            <p>No payment history found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div v-if="showReceipt" class="modal-overlay" @click="closeReceipt">
            <div class="receipt-modal-container" @click.stop>
                <button @click="closeReceipt" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <div class="receipt-modal-content">
                    <h3 class="receipt-title">Digital Receipt</h3>
                    
                    <div class="receipt-details-box">
                        <div class="receipt-row">
                            <span class="receipt-label">Payment No:</span>
                            <span class="receipt-value">{{ selectedReceipt.payment_no }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Document:</span>
                            <span class="receipt-value">{{ selectedReceipt.doc_type }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Date:</span>
                            <span class="receipt-value">{{ selectedReceipt.date }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Payer:</span>
                            <span class="receipt-value">{{ selectedReceipt.name }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Amount Paid:</span>
                            <span class="receipt-value amount">₱{{ selectedReceipt.amount.toFixed(2) }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Payment Method:</span>
                            <span class="receipt-value">{{ selectedReceipt.paymentMethod || selectedReceipt.method?.toUpperCase() || 'N/A' }}</span>
                        </div>
                        <div class="receipt-row" v-if="selectedReceipt.transactionId && selectedReceipt.transactionId !== 'N/A'">
                            <span class="receipt-label">Transaction ID:</span>
                            <span class="receipt-value">{{ selectedReceipt.transactionId }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Status:</span>
                            <span class="receipt-value" :style="{ color: selectedReceipt.status === 'APPROVED' ? '#239640' : '#dc3545' }">
                                {{ selectedReceipt.status || 'APPROVED' }}
                            </span>
                        </div>
                    </div>

                    <div class="receipt-note" v-if="selectedReceipt.roleId === 1">
                        <p>Thank you for your payment. This digital receipt is proof of payment issued by the Barangay.</p>
                    </div>

                    <div class="receipt-actions">
                        <button class="download-receipt-btn" @click="downloadReceipt">
                            DOWNLOAD
                        </button>
                        <button class="print-receipt-btn" @click="printReceipt">
                            PRINT
                        </button>
                        <button class="close-receipt-btn" @click="closeReceipt">CLOSE</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms and Conditions Modal -->
        <div v-if="showTermsModal" class="modal-overlay" @click.self="closeTermsModal">
            <div class="terms-modal" @click.stop>
                <div class="terms-modal-header">
                    <h2 class="terms-modal-title">Terms and Conditions</h2>
                    <button @click="closeTermsModal" class="terms-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="terms-modal-body">
                    <div class="terms-section">
                        <h3 class="terms-section-title">1. Role and Responsibilities</h3>
                        <p class="terms-text">
                            As a Treasurer/Payment Handler, you are responsible for processing payment requests, verifying payment receipts, approving or rejecting payments, and maintaining accurate payment records for the iKonek176B system. You must exercise your payment handling privileges with care and in accordance with barangay financial policies and regulations.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">2. Access and Security</h3>
                        <p class="terms-text">
                            You have been granted access to payment processing functions. You must maintain the confidentiality of your login credentials and immediately report any suspected security breaches. Sharing your account credentials with unauthorized persons is strictly prohibited and may result in immediate account termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">3. Payment Processing</h3>
                        <p class="terms-text">
                            You are responsible for reviewing payment requests in a timely manner. When processing payments, you must:
                            <ul class="terms-list">
                                <li>Verify that payment receipts are valid and match the payment amount</li>
                                <li>Ensure payment methods are correctly identified (Cash, GCash, Maya, Onsite)</li>
                                <li>Approve or reject payments based on valid criteria and documented requirements</li>
                                <li>Maintain accurate records of all payment transactions</li>
                                <li>Process payments in accordance with barangay financial policies</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">4. Financial Data Privacy and Confidentiality</h3>
                        <p class="terms-text">
                            You have access to sensitive financial information of residents and officials. You must handle all payment data in accordance with the Data Privacy Act of 2012. Financial information must only be accessed for legitimate payment processing purposes and must never be disclosed to unauthorized parties or used for personal gain.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">5. Payment Approval and Rejection</h3>
                        <p class="terms-text">
                            When approving or rejecting payments, you must ensure that all decisions are justified, documented, and in compliance with barangay financial policies. Discrimination or bias in processing payments is strictly prohibited. All payment actions are logged and may be subject to audit.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">6. Receipt Verification</h3>
                        <p class="terms-text">
                            You must carefully verify payment receipts to ensure they are legitimate and match the payment request. If a receipt appears fraudulent or does not match the payment details, you must reject the payment and document the reason. Failure to properly verify receipts may result in financial discrepancies.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">7. Limitations and Restrictions</h3>
                        <p class="terms-text">
                            Your payment processing privileges do not grant you the right to:
                            <ul class="terms-list">
                                <li>Access or modify system code or database structure without authorization</li>
                                <li>Bypass system security measures or attempt to exploit system vulnerabilities</li>
                                <li>Use payment processing functions for personal purposes or to gain unfair advantage</li>
                                <li>Delete or modify payment logs or audit trails</li>
                                <li>Grant payment processing privileges to other users without proper authorization</li>
                                <li>Approve payments without proper verification</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">8. Prohibited Activities</h3>
                        <p class="terms-text">
                            The following activities are strictly prohibited:
                            <ul class="terms-list">
                                <li>Unauthorized access to payment records or financial data</li>
                                <li>Tampering with payment records or documentation</li>
                                <li>Approving payments without proper verification</li>
                                <li>Sharing financial information outside of official channels</li>
                                <li>Engaging in any activity that compromises system security or financial integrity</li>
                                <li>Accepting bribes or favors in exchange for payment approval</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">9. Accountability and Auditing</h3>
                        <p class="terms-text">
                            All payment processing actions are logged and monitored. You are accountable for all actions performed using your account. Regular audits may be conducted to ensure compliance with these terms and financial policies. Failure to comply may result in disciplinary action, including but not limited to account suspension or termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">10. Violations and Consequences</h3>
                        <p class="terms-text">
                            Violation of these terms and conditions may result in immediate suspension or termination of your payment processing account, legal action if applicable, and reporting to appropriate barangay authorities. The severity of consequences will depend on the nature and extent of the violation.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">11. Updates to Terms</h3>
                        <p class="terms-text">
                            These terms and conditions may be updated periodically. You will be notified of significant changes. Continued use of payment processing privileges after changes constitutes acceptance of the updated terms.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">12. Contact and Support</h3>
                        <p class="terms-text">
                            For questions, concerns, or to report issues related to your payment processing role, contact the Barangay 176B office at ikonek176b@dev.ph or +639193076338.
                        </p>
                    </div>
                </div>
                <div class="terms-modal-footer">
                    <button @click="closeTermsModal" class="terms-modal-btn">
                        I UNDERSTAND
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
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
const showTermsModal = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const showReceipt = ref(false)
const selectedReceipt = ref(null)

// Get payments from server (Inertia props)
const serverPayments = computed(() => {
  return page?.props?.value?.payments ?? page?.props?.payments ?? []
})

// Convert server payments into the shape the UI expects
const payments = ref([])

const initializePaymentsFromServer = () => {
  payments.value = (serverPayments.value || []).map(p => {
    // Compute dateObj safely
    let dateObj = null
    try {
      dateObj = p.date_iso ? new Date(p.date_iso) : (p.date ? new Date(p.date) : new Date())
    } catch (e) {
      dateObj = new Date()
    }

    return {
      id: p.id,
      payment_no: p.payment_no || p.transactionId || 'N/A',
      name: p.name || 'Unknown',
      doc_type: p.doc_type || 'N/A',
      document: p.document || 'N/A',
      amount: Number(p.amount ?? 0),
      date: p.date || (dateObj.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })),
      dateObj: dateObj,
      method: p.method || 'onsite',
      paymentMethod: p.paymentMethod || 'ONSITE',
      status: p.status || 'APPROVED',
      receiptImage: p.receiptImage || null,
      transactionId: p.transactionId || p.payment_no || 'N/A',
      paymentTime: p.paymentTime || p.date || 'N/A',
      profileImg: p.profileImg || '/assets/DEFAULT.jpg',
      role: p.role || 'Resident',
      roleId: p.roleId || 1,
    }
  })
}

// Initialize on mount
onMounted(() => {
  initializePaymentsFromServer()
})

// Computed filtered history
const filteredHistory = computed(() => {
    let filtered = [...payments.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) ||
            item.payment_no.toLowerCase().includes(query) ||
            item.doc_type.toLowerCase().includes(query) ||
            item.document.toLowerCase().includes(query) ||
            item.role.toLowerCase().includes(query) ||
            (item.transactionId && item.transactionId.toLowerCase().includes(query))
        )
    }

    // Status filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.status.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'relevant') {
        // Sort by status priority: Rejected > Approved
        const priority = { 'rejected': 1, 'approved': 2 }
        filtered.sort((a, b) => {
            const aPriority = priority[a.status.toLowerCase()] || 3
            const bPriority = priority[b.status.toLowerCase()] || 3
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

const openTermsModal = (e) => {
    if (e) {
        e.preventDefault()
        e.stopPropagation()
    }
    showSettings.value = false
    nextTick(() => {
        showTermsModal.value = true
    })
}

const closeTermsModal = () => {
    showTermsModal.value = false
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

const openReceipt = (payment) => {
    if (!payment.receiptImage) return
    selectedReceipt.value = payment
    showReceipt.value = true
}

const closeReceipt = () => {
    showReceipt.value = false
    selectedReceipt.value = null
}

const navigateToViewPayment = () => {
    router.visit(route('view_payment_treasurer'))
}

const handleImageError = (event) => {
    event.target.style.display = 'none'
    const container = event.target.closest('.receipt-image-container')
    if (container) {
        container.innerHTML = '<p class="no-receipt-message">Failed to load image</p>'
    }
}

const printReceipt = () => {
    if (!selectedReceipt.value) return
    
    // Create a new window with the receipt content for printing
    const printWindow = window.open('', '_blank')
    const receipt = selectedReceipt.value
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt - ${receipt.payment_no || 'N/A'}</title>
            <meta charset="UTF-8">
            <style>
                @media print {
                    @page {
                        margin: 20mm;
                        size: A4;
                    }
                }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    padding: 40px;
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                }
                .receipt-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                }
                .receipt-title {
                    font-size: 28px;
                    font-weight: 700;
                    color: #2e2e2e;
                    margin: 0 0 8px 0;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #ff8c42;
                }
                .receipt-details-box {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    padding: 25px;
                    border-radius: 16px;
                    margin: 25px 0;
                    border: 1px solid #e9ecef;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                }
                .receipt-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 0;
                    border-bottom: 1px solid #e9ecef;
                }
                .receipt-row:last-child {
                    border-bottom: none;
                }
                .receipt-label {
                    font-weight: 600;
                    color: #666;
                    font-size: 15px;
                    letter-spacing: 0.3px;
                }
                .receipt-value {
                    font-weight: 600;
                    color: #2e2e2e;
                    font-size: 15px;
                    text-align: right;
                }
                .receipt-value.amount {
                    color: #239640;
                    font-size: 22px;
                    font-family: 'Courier New', monospace;
                    font-weight: 700;
                }
                .receipt-image-container {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    padding: 20px;
                    border-radius: 16px;
                    margin: 20px 0;
                    border: 1px solid #e9ecef;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                    text-align: center;
                }
                .receipt-image-label {
                    font-weight: 700;
                    color: #2e2e2e;
                    font-size: 16px;
                    margin-bottom: 15px;
                    letter-spacing: 0.5px;
                }
                .receipt-image {
                    max-width: 100%;
                    height: auto;
                    border-radius: 12px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
                    border: 2px solid #e9ecef;
                }
                .no-receipt-container {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    padding: 40px;
                    border-radius: 16px;
                    text-align: center;
                    border: 1px solid #e9ecef;
                }
                .no-receipt-message {
                    color: #999;
                    font-size: 14px;
                    font-style: italic;
                    margin: 0;
                }
                .receipt-note {
                    background: linear-gradient(135deg, #e8f8ed 0%, #d4f1dd 100%);
                    border-left: 4px solid #239640;
                    padding: 18px 20px;
                    border-radius: 12px;
                    margin: 20px 0;
                    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.1);
                }
                .receipt-note p {
                    margin: 0;
                    font-size: 14px;
                    color: #1e7e34;
                    line-height: 1.7;
                    font-weight: 500;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="receipt-header">
                <h1 class="receipt-title">Payment Receipt</h1>
                <p style="color: #666; margin: 0;">Barangay</p>
            </div>
            <div class="receipt-details-box">
                <div class="receipt-row">
                    <span class="receipt-label">Payment No:</span>
                    <span class="receipt-value">${receipt.payment_no || 'N/A'}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Document:</span>
                    <span class="receipt-value">${receipt.doc_type || 'N/A'}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Date:</span>
                    <span class="receipt-value">${receipt.date || 'N/A'}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payer:</span>
                    <span class="receipt-value">${receipt.name || 'N/A'}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Amount Paid:</span>
                    <span class="receipt-value amount">₱${receipt.amount?.toFixed(2) || '0.00'}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Method:</span>
                    <span class="receipt-value">${receipt.paymentMethod || receipt.method?.toUpperCase() || 'N/A'}</span>
                </div>
                ${receipt.transactionId && receipt.transactionId !== 'N/A' ? `
                <div class="receipt-row">
                    <span class="receipt-label">Transaction ID:</span>
                    <span class="receipt-value">${receipt.transactionId}</span>
                </div>
                ` : ''}
                <div class="receipt-row">
                    <span class="receipt-label">Status:</span>
                    <span class="receipt-value" style="color: ${receipt.status === 'APPROVED' ? '#239640' : '#dc3545'}">
                        ${receipt.status || 'APPROVED'}
                    </span>
                </div>
            </div>
            ${receipt.roleId === 1 ? `
            <div class="receipt-note">
                <p>Thank you for your payment. This digital receipt is proof of payment issued by Barangay.</p>
            </div>
            ` : ''}
        </body>
        </html>
    `)
    
    printWindow.document.close()
    printWindow.focus()
    
    // Wait for images to load before printing
    setTimeout(() => {
        printWindow.print()
        // Don't close immediately - let user see the print dialog
    }, 500)
}

const downloadReceipt = async () => {
    try {
        if (!selectedReceipt.value) return
        
        // Create a clean HTML receipt for download
        const receipt = selectedReceipt.value
        
        const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Payment Receipt - ${receipt.payment_no || 'N/A'}</title>
                <meta charset="UTF-8">
                <style>
                    @media print {
                        @page {
                            margin: 20mm;
                            size: A4;
                        }
                    }
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        padding: 40px;
                        max-width: 800px;
                        margin: 0 auto;
                        background: white;
                    }
                    .receipt-header {
                        text-align: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                    }
                    .receipt-title {
                        font-size: 28px;
                        font-weight: 700;
                        color: #2e2e2e;
                        margin: 0 0 8px 0;
                        padding-bottom: 20px;
                        border-bottom: 3px solid #ff8c42;
                    }
                    .receipt-details-box {
                        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                        padding: 25px;
                        border-radius: 16px;
                        margin: 25px 0;
                        border: 1px solid #e9ecef;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                    }
                    .receipt-row {
                        display: flex;
                        justify-content: space-between;
                        padding: 12px 0;
                        border-bottom: 1px solid #e9ecef;
                    }
                    .receipt-row:last-child {
                        border-bottom: none;
                    }
                    .receipt-label {
                        font-weight: 600;
                        color: #666;
                        font-size: 15px;
                        letter-spacing: 0.3px;
                    }
                    .receipt-value {
                        font-weight: 600;
                        color: #2e2e2e;
                        font-size: 15px;
                        text-align: right;
                    }
                    .receipt-value.amount {
                        color: #239640;
                        font-size: 22px;
                        font-family: 'Courier New', monospace;
                        font-weight: 700;
                    }
                    .receipt-image-container {
                        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                        padding: 20px;
                        border-radius: 16px;
                        margin: 20px 0;
                        border: 1px solid #e9ecef;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                        text-align: center;
                    }
                    .receipt-image-label {
                        font-weight: 700;
                        color: #2e2e2e;
                        font-size: 16px;
                        margin-bottom: 15px;
                        letter-spacing: 0.5px;
                    }
                    .receipt-image {
                        max-width: 100%;
                        height: auto;
                        border-radius: 12px;
                        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
                        border: 2px solid #e9ecef;
                    }
                    .no-receipt-container {
                        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                        padding: 40px;
                        border-radius: 16px;
                        text-align: center;
                        border: 1px solid #e9ecef;
                    }
                    .no-receipt-message {
                        color: #999;
                        font-size: 14px;
                        font-style: italic;
                        margin: 0;
                    }
                    .receipt-note {
                        background: linear-gradient(135deg, #e8f8ed 0%, #d4f1dd 100%);
                        border-left: 4px solid #239640;
                        padding: 18px 20px;
                        border-radius: 12px;
                        margin: 20px 0;
                        box-shadow: 0 2px 8px rgba(35, 150, 64, 0.1);
                    }
                    .receipt-note p {
                        margin: 0;
                        font-size: 14px;
                        color: #1e7e34;
                        line-height: 1.7;
                        font-weight: 500;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="receipt-header">
                    <h1 class="receipt-title">Payment Receipt</h1>
                    <p style="color: #666; margin: 0;">Barangay</p>
                </div>
                <div class="receipt-details-box">
                    <div class="receipt-row">
                        <span class="receipt-label">Payment No:</span>
                        <span class="receipt-value">${receipt.payment_no || 'N/A'}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Document:</span>
                        <span class="receipt-value">${receipt.doc_type || 'N/A'}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Date:</span>
                        <span class="receipt-value">${receipt.date || 'N/A'}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Payer:</span>
                        <span class="receipt-value">${receipt.name || 'N/A'}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Amount Paid:</span>
                        <span class="receipt-value amount">₱${receipt.amount?.toFixed(2) || '0.00'}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Payment Method:</span>
                        <span class="receipt-value">${receipt.paymentMethod || receipt.method?.toUpperCase() || 'N/A'}</span>
                    </div>
                    ${receipt.transactionId && receipt.transactionId !== 'N/A' ? `
                    <div class="receipt-row">
                        <span class="receipt-label">Transaction ID:</span>
                        <span class="receipt-value">${receipt.transactionId}</span>
                    </div>
                    ` : ''}
                    <div class="receipt-row">
                        <span class="receipt-label">Status:</span>
                        <span class="receipt-value" style="color: ${receipt.status === 'APPROVED' ? '#239640' : '#dc3545'}">
                            ${receipt.status || 'APPROVED'}
                        </span>
                    </div>
                </div>
                ${receipt.roleId === 1 ? `
                <div class="receipt-note">
                    <p>Thank you for your payment. This digital receipt is proof of payment issued by Barangay.</p>
                </div>
                ` : ''}
            </body>
            </html>
        `
        
        const blob = new Blob([printContent], { type: 'text/html' })
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `payment-receipt-${receipt.payment_no || 'receipt'}-${Date.now()}.html`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        URL.revokeObjectURL(url)
    } catch (error) {
        console.error('Error downloading receipt:', error)
        alert('Failed to download receipt. Please try printing instead.')
    }
}

const getActionClass = (status) => {
    const statusLower = status?.toLowerCase() || 'approved'
    if (statusLower === 'approved') return 'approved'
    if (statusLower === 'rejected') return 'rejected'
    return 'default'
}

const getRoleClass = (role) => {
    const roleLower = role?.toLowerCase().replace(/\s+/g, '-') || 'resident'
    // Check if it's a resident (role_id 1)
    if (roleLower === 'resident') return 'resident'
    // All other roles are officials
    return 'official'
}

const getMethodClass = (method) => {
    if (!method) return 'onsite'
    const methodLower = String(method).toLowerCase().trim()
    // Check if it contains the keyword first (more flexible)
    if (methodLower.includes('gcash') || methodLower === 'g-cash') return 'gcash'
    if (methodLower.includes('maya') || methodLower === 'maya wallet') return 'maya'
    if (methodLower === 'onsite' || methodLower === 'cash') return 'onsite'
    if (methodLower === 'online') return 'online'
    return 'onsite' // default
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    // If the terms modal is open and the click is inside it, do nothing
    if (showTermsModal.value && event.target.closest('.terms-modal')) {
        return
    }
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
/* Use the same base styles as the Approver History page */
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

/* Table Container */
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
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle;
}

.users-table th:nth-child(1),
.users-table th:nth-child(3),
.users-table th:nth-child(5),
.users-table th:nth-child(6),
.users-table th:nth-child(7),
.users-table th:nth-child(8),
.users-table th:nth-child(9),
.users-table th:nth-child(10) {
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
    font-size: 15px;
    color: #555;
    vertical-align: middle;
    text-align: left;
}

.users-table td:nth-child(1),
.users-table td:nth-child(3),
.users-table td:nth-child(5),
.users-table td:nth-child(6),
.users-table td:nth-child(7),
.users-table td:nth-child(8),
.users-table td:nth-child(9),
.users-table td:nth-child(10) {
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
    font-size: 15px;
}

.role-badge {
    font-size: 13px;
    padding: 6px 14px;
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

.role-badge:not(.resident):not(.official) {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.payment-no {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #333;
}

.amount-cell {
    font-weight: 700;
    color: #239640;
    font-family: 'Courier New', monospace;
}

.method-badge {
    font-size: 13px;
    padding: 6px 14px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    color: white;
}

.method-badge.onsite {
    background: #ff8c42;
}

.method-badge.gcash {
    background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
    background-color: #3b82f6 !important;
}

.method-badge.maya {
    background: linear-gradient(135deg, #10b981, #059669);
}

.method-badge.online {
    background: linear-gradient(135deg, #10b981, #059669);
}

.action-badge {
    font-size: 13px;
    padding: 6px 14px;
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

.receipt-btn {
    font-size: 13px;
    padding: 6px 14px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    color: #ff8c42;
    background: transparent;
    border: 2px solid #ff8c42;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.receipt-btn:hover {
    background: #ff8c42;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.no-receipt-btn {
    background: #e0e0e0;
    color: #999;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 12px;
    cursor: not-allowed;
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

/* Receipt Modal */
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

.receipt-modal-container {
    background: white;
    border-radius: 20px;
    padding: 0;
    width: 90%;
    max-width: 700px;
    max-height: 90vh;
    overflow: hidden;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
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
    line-height: 35px;
    color: #666;
    transition: all 0.2s;
    text-align: center;
    padding: 0;
    z-index: 10;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #333;
}

.receipt-modal-content {
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 40px;
    overflow-y: auto;
    max-height: calc(90vh - 80px);
}

.receipt-title {
    font-size: 28px;
    font-weight: 700;
    color: #2e2e2e;
    margin: 0 0 8px 0;
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 3px solid #ff8c42;
}

.receipt-details-box {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 25px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin: 25px 0;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s;
}

.receipt-row:hover {
    background: rgba(255, 140, 66, 0.05);
    margin: 0 -10px;
    padding: 12px 10px;
    border-radius: 8px;
}

.receipt-row:last-child {
    border-bottom: none;
}

.receipt-label {
    font-weight: 600;
    color: #666;
    font-size: 15px;
    letter-spacing: 0.3px;
}

.receipt-value {
    font-weight: 600;
    color: #2e2e2e;
    font-size: 15px;
    text-align: right;
}

.receipt-value.amount {
    color: #239640;
    font-size: 22px;
    font-family: 'Courier New', monospace;
    font-weight: 700;
}

.receipt-image-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 20px;
    border-radius: 16px;
    margin: 20px 0;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.receipt-image-label {
    font-weight: 700;
    color: #2e2e2e;
    font-size: 16px;
    margin-bottom: 15px;
    text-align: center;
    letter-spacing: 0.5px;
}

.receipt-image {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    display: block;
    margin: 0 auto;
    border: 2px solid #e9ecef;
}

.no-receipt-container {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
}

.no-receipt-message {
    color: #999;
    font-size: 14px;
    font-style: italic;
    margin: 0;
}

.receipt-note {
    background: linear-gradient(135deg, #e8f8ed 0%, #d4f1dd 100%);
    border-left: 4px solid #239640;
    padding: 18px 20px;
    border-radius: 12px;
    margin: 20px 0;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.1);
}

.receipt-note p {
    margin: 0;
    font-size: 14px;
    color: #1e7e34;
    line-height: 1.7;
    font-weight: 500;
    text-align: center;
}

.receipt-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.download-receipt-btn,
.print-receipt-btn,
.close-receipt-btn {
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.download-receipt-btn {
    background: #239640;
    color: white;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.download-receipt-btn:hover {
    background: #1e7d36;
    transform: translateY(-1px);
}

.print-receipt-btn {
    background: #007DFF;
    color: white;
    box-shadow: 0 2px 8px rgba(0, 125, 255, 0.3);
}

.print-receipt-btn:hover {
    background: #0062CC;
    transform: translateY(-1px);
}

.close-receipt-btn {
    background: white;
    color: #4a4a4a;
    border: 1px solid #e0e0e0;
    box-shadow: none;
}

.close-receipt-btn:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
    transform: translateY(-1px);
}

/* Custom Scrollbar */
.users-container::-webkit-scrollbar,
.receipt-modal-container::-webkit-scrollbar {
    width: 6px;
}

.users-container::-webkit-scrollbar-track,
.receipt-modal-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb,
.receipt-modal-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb:hover,
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

    .receipt-modal-container {
        width: 95%;
        padding: 20px;
    }
}

/* Terms and Conditions Modal Styles */
@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.terms-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s ease;
}

.terms-modal-header {
    background: white;
    padding: 25px 30px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.terms-modal-title {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: #333;
}

.terms-modal-close {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    color: #666;
    transition: all 0.2s ease;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.terms-modal-close:hover {
    background: #f0f0f0;
    color: #333;
}

.terms-modal-body {
    padding: 30px;
    overflow-y: auto;
    flex: 1;
}

.terms-section {
    margin-bottom: 25px;
}

.terms-section:last-child {
    margin-bottom: 0;
}

.terms-section-title {
    margin: 0 0 12px 0;
    font-size: 18px;
    font-weight: 700;
    color: #239640;
}

.terms-text {
    margin: 0;
    font-size: 15px;
    line-height: 1.7;
    color: #555;
    text-align: justify;
}

.terms-list {
    margin: 10px 0 0 20px;
    padding: 0;
}

.terms-list li {
    margin-bottom: 8px;
    font-size: 15px;
    line-height: 1.6;
    color: #555;
}

.terms-modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
    background: #f8f9fa;
    flex-shrink: 0;
}

.terms-modal-btn {
    padding: 12px 50px;
    background: #ff8c42;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.terms-modal-btn:hover {
    background: #ff7a28;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.settings-item {
    text-transform: uppercase;
    white-space: nowrap;
}
</style>