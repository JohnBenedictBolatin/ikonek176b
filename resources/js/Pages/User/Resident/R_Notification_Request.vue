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
                    <img src="/assets/DEFAULT.jpg" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
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
                        <span v-if="selectedRequest?.status === 'APPROVED'">✓</span>
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
                    <div v-if="selectedRequest?.status === 'APPROVED'" class="accepted-section">
                        <p class="details-message">
                            You may now proceed to claim your certificate by following the instructions provided.
                        </p>

                        <div class="pickup-info">
                            <div class="info-item">
                                <span class="info-label">WHAT:</span>
                                <!-- prefer pickup_item (from document_requests), otherwise title -->
                                <span class="info-value">
                                    PICKUP OF DOCUMENT ({{ selectedRequest?.pickup_item ?? selectedRequest?.title }})
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">WHERE:</span>
                                <!-- prefer pickup_location from request, fallback to raw, then default hall -->
                                <span class="info-value">
                                    {{ selectedRequest?.pickup_location ?? selectedRequest?.raw?.pickup_location ?? 'BARANGAY 176B BARANGAY HALL' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">WHEN:</span>
                                <!-- pass the whole request so the function can use pickup_start / pickup_end -->
                                <span class="info-value">{{ getPickupSchedule(selectedRequest) }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">WHO:</span>
                                <!-- person_to_look on request, else fallback to raw, then logged-in user's name, then 'N/A' -->
                                <span class="info-value">
                                    {{ selectedRequest?.person_to_look ?? selectedRequest?.raw?.person_to_look ?? user?.name ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="info-item" v-if="selectedRequest?.type === 'document'">
                                <span class="info-label">AMOUNT:</span>
                                <span class="info-value amount">₱{{ (selectedRequest?.amount ?? selectedRequest?.processing_fee) || '0.00' }}</span>
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
                                <span class="info-value">₱{{ (selectedRequest?.amount ?? selectedRequest?.processing_fee) || '0.00' }}</span>
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
                    
                </div>
            </div>
        </div>

        <!-- Payment Gateway Modal -->
        <div v-if="showPaymentModal" class="modal-overlay" @click="closePaymentModal">
            <div class="modal-content payment-modal" @click.stop>
            <button class="close-modal-btn" @click="closePaymentModal">✕</button>
            <h3 class="modal-title">Choose Payment Gateway</h3>
            <p class="modal-subtitle">Select your preferred payment method</p>
            
            <div class="payment-options">
                <button class="payment-option-btn gcash" @click="openQR('GCash')">
                <div class="payment-logo-placeholder">
                    <img src="/assets/GCASH.png" alt="GCash" class="payment-logo" />
                </div>
                <span>GCash</span>
                </button>
                
                <button class="payment-option-btn maya" @click="openQR('Maya')">
                <div class="payment-logo-placeholder">
                    <img src="/assets/MAYA.png" alt="Maya" class="payment-logo" />
                </div>
                <span>Maya</span>
                </button>
            </div>

            <div class="payment-details">
                <p><strong>Request:</strong> {{ selectedRequest?.title }}</p>
                <p><strong>Amount:</strong> ₱{{ (selectedRequest?.amount ?? selectedRequest?.processing_fee) || '0.00' }}</p>
            </div>
            </div>
        </div>

        <!-- QR Modal (fake QR + simulate scan) -->
        <div v-if="showQRModal" class="modal-overlay" @click="closeQRModal">
            <div class="modal-content qr-modal" @click.stop>
            <button class="close-modal-btn" @click="closeQRModal">✕</button>

            <h3 class="modal-title">Pay via QR - {{ selectedGateway }}</h3>
            <p class="modal-subtitle">Scan the QR code below to pay online</p>
             <p class="modal-subtitle">Click <strong>Upload Payment Screenshot</strong> to complete your payment.</p>

            <div class="qr-preview">
                <!-- Use your fake QR assets or generate dataURIs; here assume assets exist -->
                <img v-if="selectedGateway === 'GCash'" src="/assets/GQR.png" alt="GCash QR" class="qr-image" />
                <img v-else-if="selectedGateway === 'Maya'" src="/assets/MQR.png" alt="Maya QR" class="qr-image" />
                <div v-else class="qr-placeholder">NO QR</div>
            </div>

            <!-- NEW: Upload section below QR picture -->
            <div class="upload-section">
            <label class="upload-btn">
                Upload Payment Screenshot
                <input type="file" accept="image/*" @change="onFileChange" />
            </label>

            <!-- show thumbnail / filename when uploaded -->
            <div v-if="uploadedPreview" class="upload-preview">
                <img :src="uploadedPreview" alt="Uploaded preview" class="uploaded-thumb" />
                <div class="upload-filename">{{ uploadedFile?.name }}</div>
            </div>

            <!-- Once a file is uploaded, show Reference ID field + Submit button -->
            <div v-if="uploadedFile" class="evidence-form">
    <div class="input-and-actions">
        <input
            type="text"
            v-model="referenceId"
            placeholder="Enter Transaction Reference ID"
            class="reference-input"
        />

        <div class="evidence-actions">
            <button
                class="submit-evidence-btn"
                :disabled="uploading"
                @click="submitEvidence"
            >
                {{ uploading ? 'Uploading...' : 'Submit' }}
            </button>

            <button 
                class="clear-evidence-btn" 
                @click="clearEvidence" 
                :disabled="uploading"
            >
                Clear
            </button>
        </div>
    </div>
</div>
</div>

            <!-- END Upload section -->

            <!-- <div class="qr-actions">
                <button
                class="simulate-scan-btn"
                :disabled="scanning"
                @click="simulateScan(false)"
            >
                {{ scanning ? 'Processing...' : 'Simulate Scan' }}
            </button>

            <button
                class="simulate-scan-btn"
                :disabled="scanning"
                @click="simulateScan(true)"
                title="Also generate a receipt record"
            >
                {{ scanning ? 'Processing...' : 'Simulate + Create Receipt' }}
            </button>

            <button class="cancel-btn" @click="closeQRModal">Cancel</button>
            </div> -->

            <p class="note-message small">After submission, your payment will be reviewed by the Barangay Treasurer.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3' 
import { Head, usePage } from '@inertiajs/vue3' 
import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue' 
import { router } from '@inertiajs/vue3'

  // --- Inertia-shared auth user ---
const page = usePage()

// helper to read props in both shapes: page.props.value?.X or page.props.X
const getPageProp = (key) => {
  // page.props may be a reactive proxy or { value: {...} } depending on setup
  try {
    if (page?.props?.value && Object.prototype.hasOwnProperty.call(page.props.value, key)) {
      return page.props.value[key]
    }
    if (page?.props && Object.prototype.hasOwnProperty.call(page.props, key)) {
      return page.props[key]
    }
  } catch (e) {
    // fallback silent
  }
  return undefined
}

const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// raw documentRequests from the server (supports both keys)
const documentRequestsRaw = computed(() => getPageProp('documentRequests') ?? getPageProp('document_requests') ?? [])

const eventRequestsRaw = computed(() => getPageProp('eventAssistanceRequests') ?? getPageProp('event_assistance_requests') ?? [])

const mappedDocumentRequests = computed(() => {
  const server = documentRequestsRaw.value || []
  if (server.length === 0) return []

  return (Array.isArray(server) ? server : []).map((r) => {
    const raw = r.raw ?? r
    const createdAt = raw?.created_at ?? r.created_at ?? r.date
    let timestamp = null
    if (raw?.created_at) timestamp = new Date(raw.created_at)
    else if (r.timestamp) timestamp = typeof r.timestamp === 'string' ? new Date(r.timestamp) : (r.timestamp instanceof Date ? r.timestamp : null)
    else {
      try { timestamp = createdAt ? new Date(createdAt) : null } catch (e) { timestamp = null }
    }

    const dateStr = timestamp ? timestamp.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : (r.date ?? '')
    const timeStr = timestamp ? timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : (r.time ?? '')

    const requestNumber = r.requestNumber ?? r.doc_request_ticket ?? raw?.doc_request_ticket ?? String(r.id ?? raw?.doc_request_id ?? '')
    const title = r.title ?? raw?.document_name ?? raw?.purpose ?? 'Request'
    const status = (r.status ?? raw?.status ?? 'PENDING').toString().toUpperCase()
    const type = r.type ?? (raw?.fk_document_type_id ? 'document' : (raw?.pickup_item ? 'document' : 'event'))
    // Prefer server-provided formatted amount, then numeric processing_fee, then applied_processing_fee, then any raw.documentType.processing_fee
    const amountVal = (
    // formatted string sent by server (string or numeric) -> we want the numeric value if possible
    (r.amount !== undefined && r.amount !== null) ? Number(r.amount) :
    (r.processing_fee !== undefined && r.processing_fee !== null) ? Number(r.processing_fee) :
    (r.applied_processing_fee !== undefined && r.applied_processing_fee !== null) ? Number(r.applied_processing_fee) :
    (raw?.processing_fee !== undefined && raw?.processing_fee !== null) ? Number(raw.processing_fee) :
    (raw?.documentType?.processing_fee !== undefined && raw?.documentType?.processing_fee !== null) ? Number(raw.documentType.processing_fee) :
    null
    )
    const amount = amountVal !== null ? Number(amountVal).toFixed(2) : null

    // const amount = amountVal !== null && amountVal !== undefined ? Number(amountVal).toFixed(2) : null

    return {
      id: r.id ?? raw?.doc_request_id ?? requestNumber,
      requestNumber,
      title,
      date: dateStr,
      time: timeStr,
      status,
      type,
      amount,
      timestamp: timestamp || new Date(0),
      pickup_location: r.pickup_location ?? raw?.pickup_location ?? null,
      pickup_start: r.pickup_start ?? raw?.pickup_start ?? null,
      pickup_end: r.pickup_end ?? raw?.pickup_end ?? null,
      raw,
    }
  })
})

// Map event assistance requests into the same normalized shape
const mappedEventRequests = computed(() => {
  const server = eventRequestsRaw.value || []
  if (!Array.isArray(server) || server.length === 0) return []

  return server.map((r) => {
    const raw = r.raw ?? r
    // prefer start_datetime for timestamp, fallback to created_at
    const dt = raw?.start_datetime ?? raw?.created_at ?? raw?.createdAt ?? raw?.created_at
    let timestamp = null
    if (dt) {
      try { timestamp = new Date(dt) } catch (e) { timestamp = null }
    } else if (raw?.created_at) {
      try { timestamp = new Date(raw.created_at) } catch (e) { timestamp = null }
    }

    // Date and time strings
    const dateStr = timestamp ? timestamp.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : (raw?.created_at ? new Date(raw.created_at).toLocaleDateString('en-US') : '')
    const timeStr = timestamp ? timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : ''

    // request number & title
    const requestNumber = raw?.eventassist_request_ticket ?? raw?.ticket ?? String(raw?.eventassist_request_id ?? raw?.id ?? '')
    // title: show purpose, with fallback to location or a default
    const title = (raw?.purpose ? String(raw.purpose) : (raw?.location ? String(raw.location) : 'Event Assistance'))

    const status = (raw?.status ?? 'PENDING').toString().toUpperCase()
    const type = 'event' // mark it as event

    return {
      id: raw?.eventassist_request_id ?? requestNumber,
      requestNumber,
      title,
      date: dateStr,
      time: timeStr,
      status,
      type,
      amount: null,
      timestamp: timestamp || new Date(0),
      // event-specific fields (kept in raw)
      start_datetime: raw?.start_datetime ?? null,
      end_datetime: raw?.end_datetime ?? null,
      location: raw?.location ?? null,
      purpose: raw?.purpose ?? null,
      raw,
    }
  })
})

watchEffect(() => {
  try {
    // stringify to avoid Proxy circular errors in console
    const snap = JSON.parse(JSON.stringify(page.props?.value ?? page.props ?? {}))
    console.info('[DEBUG] Inertia page.props snapshot:', snap)
  } catch (e) {
    console.info('[DEBUG] Inertia page.props (raw):', page.props)
  }
  console.info('[DEBUG] getPageProp(documentRequests):', getPageProp('documentRequests'))
  console.info('[DEBUG] getPageProp(document_requests):', getPageProp('document_requests'))
  console.info('[DEBUG] computed documentRequestsRaw:', JSON.parse(JSON.stringify(documentRequestsRaw.value ?? [])))
  console.info('[DEBUG] auth user (computed):', JSON.parse(JSON.stringify(user.value ?? null)))
})

// Combine documents + events into a single requests array for the UI
const combinedRequests = computed(() => {
  // merge arrays and sort by timestamp desc by default
  const combined = [
    ...(mappedDocumentRequests.value || []),
    ...(mappedEventRequests.value || [])
  ]

  // ensure timestamp is a Date for sorting
  combined.forEach(c => {
    if (!(c.timestamp instanceof Date)) {
      try { c.timestamp = new Date(c.timestamp) } catch (e) { c.timestamp = new Date(0) }
    }
  })

  // default sorting (newest first)
  combined.sort((a,b) => b.timestamp - a.timestamp)
  return combined
})

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
// QR modal state
const showQRModal = ref(false)
const selectedGateway = ref(null) // 'GCash' or 'Maya'
const scanning = ref(false)

/* NEW reactive refs for upload feature */
const uploadedFile = ref(null)         // File object
const uploadedPreview = ref(null)      // data URL for preview
const referenceId = ref('')            // user-entered reference id
const uploading = ref(false)           // upload in progress flag

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
 
const filteredActivities = computed(() => {
  let filtered = [...activities.value]
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    filtered = filtered.filter(a => (a.user || '').toLowerCase().includes(q) || (a.action || '').toLowerCase().includes(q))
  }
  if (sortOption.value === 'newest') filtered.sort((a,b) => b.timestamp - a.timestamp)
  else if (sortOption.value === 'oldest') filtered.sort((a,b) => a.timestamp - b.timestamp)
  return filtered
})

const filteredRequests = computed(() => {
  let filtered = [...combinedRequests.value]

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    filtered = filtered.filter(req =>
      (req.title || '').toLowerCase().includes(q) ||
      (String(req.requestNumber || '')).toLowerCase().includes(q) ||
      (req.status || '').toLowerCase().includes(q)
    )
  }

  if (sortOption.value === 'newest') {
    filtered.sort((a,b) => b.timestamp - a.timestamp)
  } else if (sortOption.value === 'oldest') {
    filtered.sort((a,b) => a.timestamp - b.timestamp)
  } else if (sortOption.value === 'relevant') {
    const statusPriority = { 'PENDING': 1, 'APPROVED': 2, 'REJECTED': 3 }
    filtered.sort((a,b) => (statusPriority[a.status] || 99) - (statusPriority[b.status] || 99))
  }

  return filtered
})

// // Filtered Activities
// const filteredActivities = computed(() => {
//     let filtered = [...activities.value]

//     if (searchQuery.value.trim()) {
//         const query = searchQuery.value.toLowerCase()
//         filtered = filtered.filter(activity => 
//             activity.user.toLowerCase().includes(query) ||
//             activity.action.toLowerCase().includes(query)
//         )
//     }

//     if (sortOption.value === 'newest') {
//         filtered.sort((a, b) => b.timestamp - a.timestamp)
//     } else if (sortOption.value === 'oldest') {
//         filtered.sort((a, b) => a.timestamp - b.timestamp)
//     }

//     return filtered
// })

// // Filtered Requests
// const filteredRequests = computed(() => {
//     let filtered = [...requests.value]

//     if (searchQuery.value.trim()) {
//         const query = searchQuery.value.toLowerCase()
//         filtered = filtered.filter(request => 
//             request.title.toLowerCase().includes(query) ||
//             request.requestNumber.includes(query) ||
//             request.status.toLowerCase().includes(query)
//         )
//     }

//     if (sortOption.value === 'newest') {
//         filtered.sort((a, b) => b.timestamp - a.timestamp)
//     } else if (sortOption.value === 'oldest') {
//         filtered.sort((a, b) => a.timestamp - b.timestamp)
//     } else if (sortOption.value === 'relevant') {
//         // Sort by status priority: PENDING > ACCEPTED > REJECTED
//         const statusPriority = { 'PENDING': 1, 'ACCEPTED': 2, 'REJECTED': 3 }
//         filtered.sort((a, b) => statusPriority[a.status] - statusPriority[b.status])
//     }

//     return filtered
// })

/**
 * onFileChange(event)
 * - Accepts the selected file and generates a preview URL.
 */
const onFileChange = (event) => {
  const file = (event.target && event.target.files && event.target.files[0]) || null
  if (!file) {
    clearEvidence()
    return
  }

  // Basic validation: only images and size limit (optional)
  const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp']
  if (!allowedTypes.includes(file.type)) {
    alert('Please upload a PNG/JPEG/WebP image.')
    event.target.value = ''
    return
  }

  // Optional size check: 5 MB
  const maxMB = 5
  if (file.size / 1024 / 1024 > maxMB) {
    alert(`File too large. Max ${maxMB} MB allowed.`)
    event.target.value = ''
    return
  }

  uploadedFile.value = file

  // create preview URL for UI (revoke old object url if present)
  if (uploadedPreview.value) {
    URL.revokeObjectURL(uploadedPreview.value)
    uploadedPreview.value = null
  }
  uploadedPreview.value = URL.createObjectURL(file)
}

/**
 * clearEvidence()
 * - Resets uploaded file + preview + reference input
 */
const clearEvidence = () => {
  if (uploadedPreview.value) {
    try { URL.revokeObjectURL(uploadedPreview.value) } catch (e) {}
  }
  uploadedFile.value = null
  uploadedPreview.value = null
  referenceId.value = ''
  uploading.value = false
  // also clear the input element (if user reopens)
  // Note: we do not manipulate DOM here; the input clearing is handled by user or re-mount.
}

/**
 * submitEvidence()
 * - Validates fields and sends FormData to the server.
 * - Uses Inertia router.post with FormData; Inertia will use multipart/form-data.
 *
 * IMPORTANT: adjust the route name 'payments.upload_evidence' to match your backend route.
 */
const submitEvidence = async () => {
  if (!selectedRequest.value) {
    alert('No selected request to attach evidence to.')
    return
  }
  if (!uploadedFile.value) {
    alert('Please upload an image first.')
    return
  }
  if (!referenceId.value || !referenceId.value.trim()) {
    if (!confirm('Reference ID is empty. Do you want to submit without a reference ID?')) {
      return
    }
  }

  uploading.value = true

  try {
    const formData = new FormData()
    // file field name: 'evidence' (change if backend expects different key)
    formData.append('evidence', uploadedFile.value)
    formData.append('reference_id', referenceId.value ?? '')
    formData.append('fk_doc_request_id', selectedRequest.value.id ?? '')
    formData.append('fk_user_id', user.value?.id ?? '')
    formData.append('gateway', selectedGateway.value ?? '')
    formData.append('amount', String(selectedRequest.value?.amount ?? '0'))
    // Any other metadata you need:
    // formData.append('create_receipt', 'false')

    // Use router.post to send FormData to an upload endpoint.
    // Change the route name to what your server expects.
    router.post(route('payments.upload_evidence'), formData, {
      preserveScroll: true,
      onStart: () => {
        uploading.value = true
      },
      onSuccess: (page) => {
        uploading.value = false
        alert('Evidence uploaded successfully.')
        clearEvidence()
        // Close modal and optionally clear selectedRequest/gateway
        showQRModal.value = false
        selectedGateway.value = null
        selectedRequest.value = null
      },
      onError: (errors) => {
        uploading.value = false
        console.error('Upload errors:', errors)
        alert('Failed to upload evidence: ' + JSON.stringify(errors))
      },
      onFinish: () => {
        uploading.value = false
      },
    })
  } catch (err) {
    uploading.value = false
    console.error(err)
    alert('An unexpected error occurred during upload.')
  }
}

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
    router.visit(route('announcement_resident'))
}
const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_resident'))
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
    // Already on notifications page, no need to navigate
}

const switchTab = (tab) => {
    currentTab.value = tab
    showModeDropdown.value = false
}

const openFAQ = () => {
    router.visit(route('help_center_resident'))
}

const viewRequestDetails = (request) => {
    selectedRequest.value = request
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedRequest.value = null
}

// NEW: payment methods from server props (looks for paymentMethods or payment_methods)
const paymentMethodsRaw = computed(() => getPageProp('paymentMethods') ?? getPageProp('payment_methods') ?? [])
const paymentMethodsMap = computed(() => {
  // returns { 'GCash': id, 'Maya': id, ... }
  const map = {}
  for (const m of (Array.isArray(paymentMethodsRaw.value) ? paymentMethodsRaw.value : [])) {
    // server might give different keys; look for name or pay_method_name
    const name = m.pay_method_name ?? m.name ?? m.payMethodName ?? m.payment_method_name ?? m.pay_method ?? null
    const id = m.pay_method_id ?? m.id ?? m.payMethodId ?? null
    if (name && id) map[String(name).trim()] = id
  }
  return map
})

const showPaymentGateway = (request) => {
    selectedRequest.value = request
    showDetailsModal.value = false
    showPaymentModal.value = true
}

// NEW: open QR modal for chosen gateway
const openQR = (gateway) => {
  selectedGateway.value = gateway
  showQRModal.value = true
  showPaymentModal.value = false
}

// NEW: close QR modal
const closeQRModal = () => {
  showQRModal.value = false
  selectedGateway.value = null
  scanning.value = false
  // keep selectedRequest (so user can re-open) or clear if desired
}

// NEW: simulate scan -> create payment record
// simulateScan(createReceipt: boolean)
const simulateScan = async (createReceipt = false) => {
  if (!selectedRequest.value) {
    alert('No selected request.');
    return;
  }
  if (!selectedGateway.value) {
    alert('No gateway selected.');
    return;
  }

  scanning.value = true;

  const methodId = paymentMethodsMap.value[selectedGateway.value] ?? null;

  const payload = {
    fk_doc_request_id: selectedRequest.value.id,
    fk_user_id: user.value?.id ?? null,
    reference_ticket: selectedRequest.value.requestNumber ?? null,
    amount: Number(selectedRequest.value.amount ?? selectedRequest.value.processing_fee ?? 0),
    fk_pay_method_id: methodId, // may be null
    gateway: selectedGateway.value, // <- important
    client_note: `Simulated scan from ${selectedGateway.value}`,
    create_receipt: createReceipt ? true : false,
    };

  try {
    router.post(route('payments.store'), payload, {
      preserveScroll: true,
      onSuccess: (page) => {
        scanning.value = false;
        showQRModal.value = false;

        // `page.props` will be an Inertia response — but our controller returns JSON,
        // so many apps prefer to handle the success via onSuccess or onFinish. For safety,
        // we'll rely on the onSuccess callback. If your backend returns JSON, Inertia will
        // treat it as success and the page object here may not contain payload. To keep
        // this simple we show a generic success message:
        alert('Payment record created (simulated). If you selected receipt, a receipt was also created.');

        // optionally clear
        selectedGateway.value = null;
        selectedRequest.value = null;
      },
      onError: (errors) => {
        scanning.value = false;
        console.error('Payment create errors:', errors);
        alert('Failed to create payment: ' + JSON.stringify(errors));
      },
    });
  } catch (err) {
    scanning.value = false;
    console.error(err);
    alert('An unexpected error occurred while simulating the scan.');
  }
};

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
    // return reasons[request.requestNumber] || reasons.default
    const rn = request?.requestNumber ?? request?.request_number ?? null
    return rn && reasons[rn] ? reasons[rn] : reasons.default
}

/**
 * getPickupSchedule(request)
 * - If request.pickup_start (and optionally pickup_end) exist, format them nicely.
 * - Else fallback to the previous default (3 days from today, 9:00 AM - 3:00 PM).
 *
 * Accepts either selectedRequest (mapped object) or raw object shapes.
 */
const getPickupSchedule = (request = null) => {
  // Helper to parse a date-like value safely
  const parseDate = (val) => {
    if (!val) return null;
    // If it's already a Date
    if (val instanceof Date && !isNaN(val)) return val;
    // Try common string parse
    try {
      const d = new Date(val);
      if (!isNaN(d)) return d;
    } catch (e) {
      // ignore
    }
    return null;
  };

  // If request provided, prefer pickup_start/pickup_end from request or request.raw
  const startRaw = request?.pickup_start ?? request?.raw?.pickup_start ?? null;
  const endRaw = request?.pickup_end ?? request?.raw?.pickup_end ?? null;

  const startDate = parseDate(startRaw);
  const endDate = parseDate(endRaw);

  const dateOptions = { month: 'short', day: '2-digit', year: 'numeric' };
  const timeOptions = { hour: 'numeric', minute: '2-digit' };

  if (startDate) {
    const dateStr = startDate.toLocaleDateString('en-US', dateOptions).toUpperCase();
    const startTimeStr = startDate.toLocaleTimeString('en-US', timeOptions);

    if (endDate) {
      // If same day, show date once and a time range; otherwise show two full datetimes
      const sameDay = startDate.toDateString() === endDate.toDateString();
      const endTimeStr = endDate.toLocaleTimeString('en-US', timeOptions);
      if (sameDay) {
        return `${dateStr}, ${startTimeStr} - ${endTimeStr}`;
      } else {
        const endDateStr = endDate.toLocaleDateString('en-US', dateOptions).toUpperCase();
        return `${dateStr}, ${startTimeStr} - ${endDateStr}, ${endTimeStr}`;
      }
    } else {
      // Only start given -> show date and start time, keep previous default window if you prefer
      // Here we show a single start datetime and a default end window
      // If start has no time (midnight), we still print the date and keep default times
      const hasTime = !(startTimeStr === '12:00 AM' && startDate.getHours() === 0 && startDate.getMinutes() === 0);
      if (hasTime) {
        return `${dateStr}, ${startTimeStr}`;
      }
      // No time component — show default 9:00 AM - 3:00 PM on that date
      return `${dateStr}, 9:00 AM - 3:00 PM`;
    }
  }

  // Fallback: previous behaviour — 3 days from today, 9:00 AM - 3:00 PM
  const today = new Date();
  const pickupDate = new Date(today);
  pickupDate.setDate(today.getDate() + 3);

  const defaultDateStr = pickupDate.toLocaleDateString('en-US', dateOptions).toUpperCase();
  return `${defaultDateStr}, 9:00 AM - 3:00 PM`;
};

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

.upload-section {
  margin: 16px 0;
  text-align: center;
}
.upload-btn {
  display: inline-block;
  padding: 10px 20px;
  font-weight: bold;
  color: white;
  font-size: 15px;
  background: #ff8c42;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.upload-btn input[type="file"] {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.upload-preview {
  margin-top: 12px;
  display: flex;
  gap: 12px;
  align-items: center;
  justify-content: center; /* <-- center everything horizontally */
  text-align: center;      /* <-- center text */
}
.uploaded-thumb {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #ddd;
}
.upload-filename {
  font-size: 0.9rem;
  color: #333;
}
.evidence-form {
  margin-top: 12px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;

  max-width: 350px;   /* <-- adjust here */
  width: 100%;        /* stays responsive */
  margin-left: auto;  
  margin-right: auto; /* centers the form */
}
.label {
  font-size: 0.85rem;
  display: block;
  margin-bottom: 6px;
}
.reference-input {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-bottom: 8px;
}
.evidence-actions {
  display: flex;
  gap: 8px;
}
.submit-evidence-btn, .clear-evidence-btn {
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.submit-evidence-btn {
  background: #28a745;
  color: white;
  font-weight: bold;
  border: none;
}
.clear-evidence-btn {
  background: #e0e0e0;
  border: none;
  font-weight: bold;
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
    background: #ff8c42;
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
.request-card.approved {
border-left-color: #2bb24a;
}
.request-card.pending {
border-left-color: #ff9800;
}
.request-card.rejected {
border-left-color: #dc3545;
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
.request-status.approved {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
}
.request-status.pending {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
}
.request-status.rejected {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
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
.approved-icon .status-badge {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
}
.pending-icon .status-badge {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
}
.rejected-icon .status-badge {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
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
  width: 450px; /* adjust width as needed */
  padding: 12px 15px;
  background: #fffbf0;
  border-left: 4px solid #ff9800;
  border-radius: 5px;
  margin: 20px auto; /* centers the box horizontally and keeps bottom margin */
  text-align: left; /* keeps text aligned to the left inside */
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
padding: 12px 10px;
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
}
.pay-online-btn-modal:hover {
transform: translateY(-2px);
}
.pay-onsite-btn-modal {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
}
.pay-onsite-btn-modal:hover {
transform: translateY(-2px);
}
.close-btn {
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
margin-bottom: 1%;
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
.input-and-actions {
  display: flex;
  align-items: center; /* vertically center items */
  gap: 8px;
}

/* Make input and buttons the same height */
.reference-input {
  height: 45px;  
  width: 300px;              /* keep height fixed */
  padding: 0 12px;             /* remove vertical padding */
  border-radius: 6px;
  border: 1px solid #ccc;
  box-sizing: border-box;      /* ensures height is exact */
  font-size: 14px;
}

.evidence-actions {
  display: flex;
  gap: 5px;
}

.submit-evidence-btn,
.clear-evidence-btn {
  height: 45px;                /* same as input */
  padding: 0 16px;             /* only horizontal padding */
  display: flex;
  align-items: center;         /* vertical centering */
  justify-content: center;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  box-sizing: border-box;
}

.submit-evidence-btn {
  background: #28a745;
  color: white;
}

.clear-evidence-btn {
  background: #e0e0e0;
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