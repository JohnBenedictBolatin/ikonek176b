<template>
    <Head>
        <title>Document Request</title>
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
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item active"
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
                    <!-- Document Request Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Document Request</h2>
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
                                    <button @click="selectFilter('clearance')" :class="{ active: filterOption === 'clearance' }">CLEARANCE</button>
                                    <button @click="selectFilter('certificate')" :class="{ active: filterOption === 'certificate' }">CERTIFICATE</button>
                                    <button @click="selectFilter('cedula')" :class="{ active: filterOption === 'cedula' }">CEDULA</button>
                                    <button @click="selectFilter('id')" :class="{ active: filterOption === 'id' }">ID</button>
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

                    <!-- Requests Container -->
                    <div class="requests-container">
                        <div 
                            v-for="request in filteredRequests" 
                            :key="request.id"
                            class="request-card"
                        >
                            <div class="request-content">
                                <div class="request-left">
                                    <img src="/assets/DEFAULT.jpg" alt="Profile" class="modal-avatar" />
                                    <div class="request-info">
                                        <h3 class="request-name">{{ request.name }}</h3>
                                        <p class="request-doc-type">{{ request.documentType }}</p>
                                        <p class="request-ref-code">{{ request.referenceCode }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <p class="request-date">{{ request.date }}</p>
                                    <button @click="openModal(request)" class="view-btn">
                                        View Request
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- No requests message -->
                        <div v-if="filteredRequests.length === 0" class="no-requests">
                            <p>No document requests found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Modal Popup -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">✕</button>

                <div class="modal-content">
                    <!-- Top Section -->
                    <div class="modal-top">
                        <div class="modal-user-section">
                            <img src="/assets/DEFAULT.jpg" alt="Profile" class="modal-avatar" />
                            <div>
                                <h3 class="modal-name">{{ selectedRequest.name }}</h3>
                                <p class="modal-label">Document Request</p>
                                <p class="modal-ref">{{ selectedRequest.referenceCode }}</p>
                            </div>
                        </div>
                        <div class="modal-doc-section">
                            <h3 class="modal-doc-type">{{ selectedRequest.documentType }}</h3>
                            <p class="modal-purpose">Purpose: {{ selectedRequest.purpose }}</p>
                        </div>
                        <div class="modal-upload-section">
                            <button class="upload-btn">
                                View Upload
                            </button>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="modal-details">
                        <!-- Request Description -->
                        <div class="detail-description">
                            <p class="detail-label">Request Details:</p>
                            <p class="detail-value">{{ selectedRequest.description }}</p>
                        </div>

                        <!-- Personal Information Grid -->
                        <div class="details-grid">
                            <div class="detail-item">
                                <p class="detail-label">Birthdate:</p>
                                <p class="detail-value">{{ selectedRequest.birthdate }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Age:</p>
                                <p class="detail-value">{{ selectedRequest.age }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Sex:</p>
                                <p class="detail-value">{{ selectedRequest.sex }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Civil Status:</p>
                                <p class="detail-value">{{ selectedRequest.civilStatus }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Contact Number:</p>
                                <p class="detail-value">{{ selectedRequest.contact }}</p>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="detail-item-full">
                            <p class="detail-label">Home Address:</p>
                            <p class="detail-value">{{ selectedRequest.address }}</p>
                        </div>

                        <!-- Comment Section -->
                        <div class="comment-section">
                            <textarea 
                                v-model="comment"
                                placeholder="Leave a comment or note..." 
                                class="comment-textarea"
                                rows="4"
                            ></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="modal-actions">
                            <button @click="openApprovalModal" class="approve-btn">
                                Approve
                            </button>
                            <button @click="openRejectionModal" class="reject-btn">
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Modal (Pickup Details) -->
        <div v-if="isApprovalModalOpen" class="modal-overlay" @click="closeApprovalModal">
            <div class="modal-container approval-modal" @click.stop>
                <button @click="closeApprovalModal" class="modal-close">✕</button>

                <div class="modal-content">
                <h2 class="approval-title">Set Pickup Details</h2>
                <p class="approval-subtitle">Document will be approved and ready for pickup</p>

                <div class="approval-form">
                    <div class="form-group">
                    <label class="form-label">Pickup Item</label>
                    <input
                        type="text"
                        v-model="pickupItem"
                        class="form-input"
                        placeholder="e.g. Barangay Clearance (certified)"
                        required
                    />
                    </div>

                    <div class="form-group">
                    <label class="form-label">Pickup Location</label>
                    <input
                        type="text"
                        v-model="pickupLocation"
                        class="form-input"
                        placeholder="e.g. Barangay Hall - Records Office"
                        required
                    />
                    </div>

                    <div class="form-group">
                    <label class="form-label">Pickup Date</label>
                    <input
                        type="date"
                        v-model="pickupDate"
                        class="form-input"
                        required
                    />
                    </div>

                    <div class="form-group">
                    <label class="form-label">Pickup Time</label>
                    <input
                        type="time"
                        v-model="pickupTime"
                        class="form-input"
                        required
                    />
                    </div>

                    <div class="form-group">
                    <label class="form-label">Pickup End (optional)</label>
                    <div style="display:flex;gap:8px;">
                        <input type="date" v-model="pickupEndDate" class="form-input" />
                        <input type="time" v-model="pickupEndTime" class="form-input" />
                    </div>
                    </div>

                    <div class="form-group">
                    <label class="form-label">Person to Look For (optional)</label>
                    <input
                        type="text"
                        v-model="personToLook"
                        class="form-input"
                        placeholder="e.g. Records Officer - Maria D."
                    />
                    </div>

                    <div class="form-group">
                    <label class="form-label">Additional Instructions (Optional)</label>
                    <textarea
                        v-model="comment"
                        class="form-textarea"
                        rows="3"
                        placeholder="Any special instructions for the resident..."
                    ></textarea>
                    </div>

                    <div class="approval-actions">
                    <button
                    @click="confirmApproval"
                    class="confirm-btn"
                    :disabled="isApproving"
                    :aria-busy="isApproving ? 'true' : 'false'"
                    >
                    <span v-if="isApproving">Processing…</span>
                    <span v-else>Confirm Approval</span>
                    </button>
                    <button @click="closeApprovalModal" class="cancel-btn">
                        Cancel
                    </button>
                    </div>
                </div>
                </div>
            </div>
        </div>


        <!-- Rejection Modal -->
        <div v-if="isRejectionModalOpen" class="modal-overlay" @click="closeRejectionModal">
            <div class="modal-container rejection-modal" @click.stop>
                <button @click="closeRejectionModal" class="modal-close">✕</button>
                
                <div class="modal-content">
                    <h2 class="rejection-title">Reason for Rejection</h2>
                    <p class="rejection-subtitle">Please provide a clear reason for rejecting this request</p>

                    <div class="rejection-form">
                        <div class="form-group">
                            <label class="form-label">Rejection Reason</label>
                            <textarea 
                                v-model="rejectionReason"
                                class="form-textarea"
                                rows="6"
                                placeholder="Explain why this request cannot be approved..."
                                required
                            ></textarea>
                        </div>

                        <div class="rejection-actions">
                            <button
                            @click="confirmRejection"
                            class="confirm-reject-btn"
                            :disabled="isRejecting"
                            :aria-busy="isRejecting ? 'true' : 'false'"
                            >
                            <span v-if="isRejecting">Processing…</span>
                            <span v-else>Confirm Rejection</span>
                            </button>
                            <button @click="closeRejectionModal" class="cancel-btn">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Frontend-generated Document Modal -->
        <div v-if="isDocumentModalOpen" class="modal-overlay" @click="closeFrontendDocumentModal">
            <div class="modal-container document-modal" @click.stop>
                <button @click="closeFrontendDocumentModal" class="modal-close">✕</button>
                <div class="modal-content">
                <h2 class="document-title">Generated Document</h2>
                <p v-if="generatedDocumentFilename" class="document-filename">{{ generatedDocumentFilename }}</p>

                <div class="document-preview" style="min-height:60vh;">
                    <iframe
                    id="doc-preview-iframe"
                    v-if="generatedDocumentObjectUrl"
                    :src="generatedDocumentObjectUrl"
                    style="width:100%; height:60vh; border:0;"
                    ></iframe>

                    <div v-else class="no-document">
                    <p>No generated document available yet.</p>
                    </div>
                </div>

                <div class="document-actions" style="margin-top:12px; display:flex; gap:8px;">
                    <a
                    v-if="generatedDocumentObjectUrl"
                    :href="generatedDocumentObjectUrl"
                    :download="generatedDocumentFilename || ''"
                    class="download-btn"
                    >
                    Download
                    </a>

                    <button
                    v-if="generatedDocumentObjectUrl"
                    @click="printGeneratedDocument"
                    class="print-btn"
                    >
                    Print / Save as PDF
                    </button>

                </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { Link } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

// --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)
const closeFrontendDocumentModal = () => {
    isDocumentModalOpen.value = false
}
// map of role_id -> role_name
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

const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  return id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
})

// Setup CSRF + X-Requested-With for Laravel
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
if (csrfToken) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken
}
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// helper to build endpoint (uses Ziggy route() if available, otherwise fallback)
const buildUrl = (routeName, id) => {
  try {
    if (typeof route === 'function') return route(routeName, { id })
  } catch (e) { /* ignore */ }
  // fallback
  if (routeName === 'document_requests.approve') return `/document-requests/${id}/approve`
  if (routeName === 'document_requests.reject')  return `/document-requests/${id}/reject`
  return '/'
}

// Reactive UI state
const showSettings = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const isModalOpen = ref(false)
const isApprovalModalOpen = ref(false)
const isRejectionModalOpen = ref(false)
const isDocumentModalOpen = ref(false) // NEW
const selectedRequest = ref(null)
const comment = ref('')
const pickupDate = ref('')
const pickupTime = ref('')
const rejectionReason = ref('')

const pickupItem = ref('')
const pickupLocation = ref('')
const pickupEndDate = ref('')
const pickupEndTime = ref('')
const personToLook = ref('')
const isApproving = ref(false)
const isRejecting = ref(false)

// Document preview-related refs
const generatedDocumentUrl = ref('')         // direct URL from backend (if provided)
const generatedDocumentObjectUrl = ref('')   // object URL created from base64 blob or HTML blob
const generatedDocumentFilename = ref('')    // optional filename from backend or front-end generated
const generatedDocumentMime = ref('application/pdf') // default
const generatedDocumentBase64 = ref('')      // store base64 optionally

const pageProps = page?.props ?? {}

// Grab document requests from Inertia props
const serverRequests = computed(() => {
  return page?.props?.value?.document_requests ?? page?.props?.document_requests ?? []
})

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  if (Number.isNaN(d.getTime())) return dateStr
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${mm}/${dd}/${yyyy}`
}

const computeAge = (birthStr) => {
  if (!birthStr) return ''
  const b = new Date(birthStr)
  if (Number.isNaN(b.getTime())) return ''
  const today = new Date()
  let age = today.getFullYear() - b.getFullYear()
  const m = today.getMonth() - b.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < b.getDate())) {
    age--
  }
  return String(age)
}

const titleCase = (s = '') =>
  s
    .toString()
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .map(w => w[0]?.toUpperCase() + w.slice(1).toLowerCase())
    .join(' ')

const buildFullName = (r) => {
  if (!r) return ''
  if (r.name && String(r.name).trim()) {
    return String(r.name).trim()
  }
  const parts = []
  if (r.first_name) parts.push(titleCase(r.first_name))
  if (r.middle_name) parts.push(titleCase(r.middle_name))
  if (r.last_name) parts.push(titleCase(r.last_name))
  if (r.suffix) parts.push(String(r.suffix).trim())
  return parts.join(' ').trim()
}

const mapServerRow = (r) => {
  const fullName = buildFullName(r) || 'Unknown'

  let docType = ''
  if (r.document_type && (r.document_type.name || r.document_type.type_name)) {
    docType = r.document_type.name ?? r.document_type.type_name
  } else if (page?.props?.value?.document_types_map && r.fk_document_type_id) {
    docType = page.props.value.document_types_map[r.fk_document_type_id] ?? `Type ${r.fk_document_type_id}`
  } else if (r.document_type_name) {
    docType = r.document_type_name
  } else {
    docType = r.fk_document_type_id ? `Type ${r.fk_document_type_id}` : 'Unknown'
  }

  let uploadedFileUrl = ''
  if (r.valid_id_path) {
    if (String(r.valid_id_path).startsWith('http')) {
      uploadedFileUrl = r.valid_id_path
    } else {
      uploadedFileUrl = `/storage/${r.valid_id_path}`.replace('//', '/')
    }
  }

  const createdAt = r.created_at ?? r.createdAt ?? r.created_date ?? null

  return {
    doc_request_id: r.doc_request_id ?? r.id ?? null,
    referenceCode: r.doc_request_ticket ?? r.doc_request_code ?? r.reference_code ?? '',
    name: fullName,
    profileImg: uploadedFileUrl || '/assets/default_profile.png',
    documentType: docType,
    date: formatDate(createdAt),
    dateObj: createdAt ? new Date(createdAt) : new Date(),
    purpose: r.purpose ?? '',
    description: r.purpose ?? r.description ?? '',
    birthdate: formatDate(r.birthdate),
    age: computeAge(r.birthdate),
    sex: r.sex ?? '',
    civilStatus: r.civil_status ?? '',
    contact: r.contact_number ?? r.contact ?? '',
    address: r.address ?? '',
    uploadedFile: r.valid_id_path ?? '',
    uploadedFileUrl,
    original: r,
    status: r.status ?? '',
    fk_approver_id: r.fk_approver_id ?? null,
    reviewed_at: r.reviewed_at ?? null,
    applied_processing_fee: r.applied_processing_fee ?? null,
    pickup_item: r.pickup_item ?? null,
    pickup_location: r.pickup_location ?? null,
    pickup_start: r.pickup_start ?? null,
    pickup_end: r.pickup_end ?? null,
    person_to_look: r.person_to_look ?? null,
    created_at: createdAt,
  }
}

const localRequests = ref([])

const syncRequests = () => {
  try {
    localRequests.value = (serverRequests.value || []).map(mapServerRow)
  } catch (e) {
    localRequests.value = []
    console.error('Error mapping document requests:', e)
  }
}

watch(serverRequests, syncRequests, { immediate: true })

const filteredRequests = computed(() => {
  let filtered = [...localRequests.value]

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => 
        (item.name || '').toLowerCase().includes(query) ||
        (item.documentType || '').toLowerCase().includes(query) ||
        (item.referenceCode || '').toLowerCase().includes(query)
    )
  }

  if (filterOption.value !== 'all') {
    filtered = filtered.filter(item => 
      (item.documentType || '').toLowerCase().includes(filterOption.value.toLowerCase())
    )
  }

  if (sortOption.value === 'newest') {
    filtered.sort((a, b) => b.dateObj - a.dateObj)
  } else if (sortOption.value === 'oldest') {
    filtered.sort((a, b) => a.dateObj - b.dateObj)
  } else if (sortOption.value === 'relevant') {
    const urgentKeywords = ['urgent', 'asap', 'soonest', 'deadline']
    filtered.sort((a, b) => {
      const aUrgent = urgentKeywords.some(keyword => (a.description || '').toLowerCase().includes(keyword))
      const bUrgent = urgentKeywords.some(keyword => (b.description || '').toLowerCase().includes(keyword))
      if (aUrgent && !bUrgent) return -1
      if (!aUrgent && bUrgent) return 1
      return b.dateObj - a.dateObj
    })
  }

  return filtered
})

// --- UI methods ---
const toggleSettings = () => { showSettings.value = !showSettings.value }
const closeSettings = () => { showSettings.value = false }
const toggleSortDropdown = () => { showSortDropdown.value = !showSortDropdown.value; showFilterDropdown.value = false }
const toggleFilterDropdown = () => { showFilterDropdown.value = !showFilterDropdown.value; showSortDropdown.value = false }
const selectSort = (option) => { sortOption.value = option; showSortDropdown.value = false }
const selectFilter = (option) => { filterOption.value = option; showFilterDropdown.value = false }
const logout = () => { showSettings.value = false; router.visit(route('login')) }
const performSearch = () => { console.log('Performing search:', searchQuery.value) }

const openModal = (request) => {
  selectedRequest.value = request
  comment.value = ''
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedRequest.value = null
  comment.value = ''
}

/**
 * Optional parameter `reopenMain` controls whether closing the smaller modal re-opens the main modal view.
 * Default `true` keeps previous behaviour for Cancel/overlay clicks. Pass `false` when you want everything closed.
 */
const openApprovalModal = () => {
  isModalOpen.value = false
  isApprovalModalOpen.value = true
  // defaults
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  pickupDate.value = tomorrow.toISOString().split('T')[0]
  pickupTime.value = '09:00'
  pickupEndDate.value = ''
  pickupEndTime.value = ''
  pickupItem.value = selectedRequest.value?.documentType ?? ''
  pickupLocation.value = ''
  personToLook.value = ''
  comment.value = ''
}

const closeApprovalModal = (reopenMain = true) => {
  isApprovalModalOpen.value = false
  pickupDate.value = ''
  pickupTime.value = ''
  pickupEndDate.value = ''
  pickupEndTime.value = ''
  pickupItem.value = ''
  pickupLocation.value = ''
  personToLook.value = ''
  comment.value = ''
  if (reopenMain) {
    // only reopen the main modal when user cancelled or clicked overlay
    isModalOpen.value = true
  } else {
    isModalOpen.value = false
    selectedRequest.value = null
  }
}

const buildDateTime = (dateStr, timeStr) => {
  if (!dateStr || !timeStr) return null
  return `${dateStr} ${timeStr}:00`
}

// helpers for base64 -> blob -> objectURL
const base64ToBlob = (base64, mime = 'application/pdf') => {
  const byteChars = atob(base64)
  const byteNumbers = new Array(byteChars.length)
  for (let i = 0; i < byteChars.length; i++) {
    byteNumbers[i] = byteChars.charCodeAt(i)
  }
  const byteArray = new Uint8Array(byteNumbers)
  return new Blob([byteArray], { type: mime })
}

const cleanupGeneratedDocument = () => {
  if (generatedDocumentObjectUrl.value) {
    try { URL.revokeObjectURL(generatedDocumentObjectUrl.value) } catch(e){}
    generatedDocumentObjectUrl.value = ''
  }
  generatedDocumentBase64.value = ''
  generatedDocumentUrl.value = ''
  generatedDocumentFilename.value = ''
  generatedDocumentMime.value = 'application/pdf'
}

/**
 * NEW: generate HTML document on the front-end (used as fallback when server does not return a document)
 * You can customize this HTML template as needed.
 */
const generateDocumentHtml = (request, pickupPayload) => {
  const now = new Date().toLocaleString()
  const refCode = request?.referenceCode ?? ''
  const name = request?.name ?? ''
  const docType = request?.documentType ?? ''
  const purpose = request?.purpose ?? ''
  const address = request?.address ?? ''
  const approver = user.value?.name ?? (user.value?.email ?? 'Approver')

  return `
  <!doctype html>
  <html>
  <head>
    <meta charset="utf-8" />
    <title>${docType} - ${refCode}</title>
    <style>
      body { font-family: Arial, Helvetica, sans-serif; margin: 36px; color:#111; }
      header { text-align:center; margin-bottom:20px; }
      h1 { margin:0; font-size:20px; }
      .meta { margin-bottom: 18px; }
      .row { display:flex; gap:10px; margin-bottom:8px; }
      .label { width:160px; font-weight:600; color:#333; }
      .value { flex:1; }
      .footer { margin-top:40px; font-size:13px; color:#555; }
      .box { border:1px solid #ccc; padding:12px; border-radius:4px }
    </style>
  </head>
  <body>
    <header>
      <h1>${docType}</h1>
      <div>${refCode}</div>
    </header>

    <section class="box">
      <div class="meta">
        <div class="row"><div class="label">Name</div><div class="value">${name}</div></div>
        <div class="row"><div class="label">Purpose</div><div class="value">${purpose}</div></div>
        <div class="row"><div class="label">Address</div><div class="value">${address}</div></div>
      </div>

      <hr />

      <div class="meta">
        <div class="row"><div class="label">Pickup Item</div><div class="value">${pickupPayload.pickup_item ?? ''}</div></div>
        <div class="row"><div class="label">Pickup Location</div><div class="value">${pickupPayload.pickup_location ?? ''}</div></div>
        <div class="row"><div class="label">Pickup Start</div><div class="value">${pickupPayload.pickup_start ?? ''}</div></div>
        <div class="row"><div class="label">Pickup End</div><div class="value">${pickupPayload.pickup_end ?? ''}</div></div>
        <div class="row"><div class="label">Person to Look For</div><div class="value">${pickupPayload.person_to_look ?? ''}</div></div>
      </div>
    </section>

    <div class="footer">
      <div>Approved by: ${approver}</div>
      <div>Reviewed at: ${pickupPayload.reviewed_at ?? now}</div>
    </div>
  </body>
  </html>
  `
}

/**
 * Show generated HTML in modal (creates object URL, sets filename)
 */
const showGeneratedDocumentFromHtml = (htmlString, filename = 'document.html') => {
  cleanupGeneratedDocument()
  const blob = new Blob([htmlString], { type: 'text/html' })
  const url = URL.createObjectURL(blob)
  generatedDocumentObjectUrl.value = url
  generatedDocumentFilename.value = filename
  isDocumentModalOpen.value = true
}

/**
 * existing helpers for server-returned documents
 */
const showGeneratedDocumentFromBase64 = (base64, filename = 'document.pdf', mime = 'application/pdf') => {
  cleanupGeneratedDocument()
  generatedDocumentBase64.value = base64
  generatedDocumentMime.value = mime || 'application/pdf'
  generatedDocumentFilename.value = filename || 'document.pdf'
  try {
    const blob = base64ToBlob(base64, generatedDocumentMime.value)
    const objUrl = URL.createObjectURL(blob)
    generatedDocumentObjectUrl.value = objUrl
    // now open modal
    isDocumentModalOpen.value = true
  } catch (e) {
    console.error('Failed to build blob from base64', e)
    // still open modal so user sees message
    isDocumentModalOpen.value = true
  }
}

const showGeneratedDocumentFromUrl = (url, filename = '') => {
  cleanupGeneratedDocument()
  generatedDocumentUrl.value = url
  generatedDocumentFilename.value = filename || ''
  isDocumentModalOpen.value = true
}

const closeDocumentModal = () => {
  isDocumentModalOpen.value = false
  // cleanup object url if any
  cleanupGeneratedDocument()
}

const printGeneratedDocument = () => {
  // iframe id used in template below: 'doc-preview-iframe'
  const iframe = document.getElementById('doc-preview-iframe')
  if (!iframe) {
    alert('Preview not available for printing yet.')
    return
  }
  try {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
  } catch (e) {
    console.error('Print failed', e)
    alert('Could not open print dialog. You can download the file instead.')
  }
}

// computed for preview src / download href
const previewSrc = computed(() => {
  // prefer object url from base64 or html, else generatedDocumentUrl
  return generatedDocumentObjectUrl.value || generatedDocumentUrl.value || ''
})
const downloadHref = computed(() => {
  return generatedDocumentObjectUrl.value || generatedDocumentUrl.value || ''
})

// CONFIRM APPROVAL (modified only for showing generated document - all other logic kept)
const confirmApproval = async () => {
  if (!pickupDate.value || !pickupTime.value) {
    alert('Please set both pickup date and time')
    return
  }
  if (!selectedRequest.value || !selectedRequest.value.doc_request_id) {
    alert('No request selected')
    return
  }

  const payload = {
    pickup_item: pickupItem.value || null,
    pickup_location: pickupLocation.value || null,
    pickup_start: buildDateTime(pickupDate.value, pickupTime.value),
    pickup_end: (pickupEndDate.value && pickupEndTime.value) ? buildDateTime(pickupEndDate.value, pickupEndTime.value) : null,
    person_to_look: personToLook.value || null,
    status: 'Approved',
    fk_approver_id: user.value?.id ?? user.value?.user_id ?? null,
    reviewed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
  }

  isApproving.value = true
  const url = buildUrl('document_requests.approve', selectedRequest.value.doc_request_id)

  try {
    const res = await axios.post(url, payload)
    // assume backend returns JSON { success: true, data: { ... }, message: '...' }
    if (res?.data?.success) {
      const idx = localRequests.value.findIndex(r => r.doc_request_id === selectedRequest.value.doc_request_id)
      if (idx > -1) {
        localRequests.value[idx] = {
          ...localRequests.value[idx],
          status: payload.status,
          fk_approver_id: payload.fk_approver_id,
          reviewed_at: payload.reviewed_at,
          pickup_item: payload.pickup_item,
          pickup_location: payload.pickup_location,
          pickup_start: payload.pickup_start,
          pickup_end: payload.pickup_end,
          person_to_look: payload.person_to_look,
        }
      }
      // show alert first (per your UX request)
      alert(res.data.message ?? 'Request approved successfully!')

      // Try to extract the returned document; support multiple server shapes:
      const maybeDoc = res.data.document_base64 || (res.data.data && res.data.data.document_base64) || null
      const maybeDocUrl = res.data.document_url || (res.data.data && res.data.data.document_url) || null
      const maybeFilename = res.data.filename || (res.data.data && res.data.data.filename) || `${selectedRequest.value.referenceCode || 'document'}.pdf`
      const maybeMime = res.data.mime || (res.data.data && res.data.data.mime) || 'application/pdf'

      if (maybeDoc) {
        // server returned base64 PDF/data -> show via object URL
        // if server included data URL prefix (data:...;base64,), strip it
        let base64 = maybeDoc
        const dataPrefixMatch = base64.match(/^data:(.*);base64,(.*)$/)
        if (dataPrefixMatch) {
          base64 = dataPrefixMatch[2]
        }
        showGeneratedDocumentFromBase64(base64, maybeFilename, maybeMime)
      } else if (maybeDocUrl) {
        // server returned a direct URL -> open preview using that URL
        // ensure URL is absolute
        let finalUrl = maybeDocUrl
        if (!/^https?:\/\//i.test(finalUrl) && finalUrl.startsWith('/')) {
          finalUrl = window.location.origin + finalUrl
        }
        showGeneratedDocumentFromUrl(finalUrl, maybeFilename)
      } else {
        // SERVER DID NOT RETURN A DOCUMENT -> FALLBACK: generate on FRONTEND and show it
        const html = generateDocumentHtml(selectedRequest.value, payload)
        const filename = `${selectedRequest.value.referenceCode || 'document'}.html`
        showGeneratedDocumentFromHtml(html, filename)
        // keep selectedRequest for context while showing generated doc
      }

      // close the approval modal (we already opened the doc modal if applicable)
      // but we used closeApprovalModal(false) only if no doc; otherwise approval modal should be closed now
      if (!isDocumentModalOpen.value) {
        closeApprovalModal(false)
      } else {
        // approval modal already closed by UI flow; ensure selectedRequest is kept so user can reference it with doc
        // we intentionally do NOT clear selectedRequest here to preserve context while user views document
      }
    } else {
      // handle unexpected responses gracefully
      alert(res?.data?.message ?? 'Approval completed, but server returned unexpected response.')
    }
  } catch (err) {
    console.error('Approval error:', err)
    // Laravel validation errors are usually in err.response.data.errors
    const errors = err?.response?.data?.errors
    if (errors) {
      const flat = Object.values(errors).flat().join('\n')
      alert('Approval failed:\n' + flat)
    } else {
      const msg = err?.response?.data?.message ?? err.message ?? 'Network or server error'
      alert('Approval failed: ' + msg)
    }
  } finally {
    isApproving.value = false
  }
}

const openRejectionModal = () => {
  isModalOpen.value = false
  isRejectionModalOpen.value = true
}

const closeRejectionModal = (reopenMain = true) => {
  isRejectionModalOpen.value = false
  rejectionReason.value = ''
  if (reopenMain) {
    isModalOpen.value = true
  } else {
    isModalOpen.value = false
    selectedRequest.value = null
  }
}

const confirmRejection = async () => {
  if (!rejectionReason.value.trim()) {
    alert('Please provide a reason for rejection')
    return
  }
  if (!selectedRequest.value || !selectedRequest.value.doc_request_id) {
    alert('No request selected')
    return
  }

  const payload = {
    status: 'Rejected',
    fk_approver_id: user.value?.id ?? user.value?.user_id ?? null,
    reviewed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
    rejection_reason: rejectionReason.value.trim(),
  }

  isRejecting.value = true
  const url = buildUrl('document_requests.reject', selectedRequest.value.doc_request_id)

  try {
    const res = await axios.post(url, payload)
    if (res?.data?.success) {
      const idx = localRequests.value.findIndex(r => r.doc_request_id === selectedRequest.value.doc_request_id)
      if (idx > -1) {
        localRequests.value[idx] = {
          ...localRequests.value[idx],
          status: payload.status,
          fk_approver_id: payload.fk_approver_id,
          reviewed_at: payload.reviewed_at,
        }
      }
      alert(res.data.message ?? 'Request rejected successfully!')
      closeRejectionModal(false)
    } else {
      alert(res?.data?.message ?? 'Rejection completed, but server returned unexpected response.')
    }
  } catch (err) {
    console.error('Rejection error:', err)
    const errors = err?.response?.data?.errors
    if (errors) {
      const flat = Object.values(errors).flat().join('\n')
      alert('Rejection failed:\n' + flat)
    } else {
      const msg = err?.response?.data?.message ?? err.message ?? 'Network or server error'
      alert('Rejection failed: ' + msg)
    }
  } finally {
    isRejecting.value = false
  }
}


const navigateToDashboard = () => {
  router.visit(route('dashboard_approver'))
}

const navigateToRegisterRequest = () => {
  router.visit(route('event_request_approver'))
}

const navigateToHistory = () => {
  router.visit(route('history_approver'))
}

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
  syncRequests()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  // cleanup any object URL
  cleanupGeneratedDocument()
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

.request-avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

.request-date {
    font-size: 13px;
    color: #999;
    margin: 0;
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
}

.view-btn:hover {
    background: #239640;
    transform: translateY(-2px);
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

.approval-modal,
.rejection-modal {
    max-width: 600px;
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
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
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

.modal-content {
    margin-top: 10px;
}

.modal-top {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr;
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

.modal-avatar {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    margin: 0 0 3px 0;
}

.modal-ref {
    font-size: 12px;
    color: #999;
    font-family: monospace;
    margin: 0;
}

.modal-doc-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.document-actions {
    margin-top: 12px;
    display: flex;
    gap: 12px;
}

.action-btn {
    padding: 12px 20px;                 /* more padding = cleaner */
    border-radius: 4px;                 /* subtle corner */
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: none;
    display: inline-flex;               /* fixes text alignment */
    align-items: center;                /* centers text vertically */
    justify-content: center;            /* centers text horizontally */
    min-width: 140px;                   /* same width = balanced */
    height: 44px;                       /* uniform height */
    transition: 0.15s ease;
}

/* Download button (blue) */
.download-btn {
    background: #ff8c42;
    padding: 15px 12px;
    border-radius: 4px;  
    font-weight: bold;
    color: white;
}

.download-btn:hover {
    background: #ff7a28;
}

/* Print button (green) */
.print-btn {
    border-radius: 4px;  
    background: #43a047;
    padding: 15px 12px;
    font-weight: bold;
    color: white;
}

.print-btn:hover {
    background: #2e7d32;
}

.modal-doc-type {
    font-size: 18px;
    font-weight: 700;
    color: #239640;
    margin: 0 0 8px 0;
}

.modal-purpose {
    font-size: 13px;
    color: #666;
    margin: 0;
}

.modal-upload-section {
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-btn {
    background: #ff7a28;;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}


.modal-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-description {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #239640;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
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
}

.approve-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
}

.reject-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
}

.reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
}

/* Approval Modal Styles */
.approval-title {
    font-size: 24px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 10px 0;
    text-align: center;
}

.approval-subtitle {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin: 0 0 30px 0;
}

.approval-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: #239640;
}

.form-textarea {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    transition: border-color 0.3s;
}

.form-textarea:focus {
    outline: none;
    border-color: #239640;
}

.approval-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.confirm-btn {
    flex: 1;
    background: #239640;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
}

.confirm-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
}

.cancel-btn {
    flex: 1;
    background: #6b7280;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
}

.cancel-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

/* Rejection Modal Styles */
.rejection-title {
    font-size: 24px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 10px 0;
    text-align: center;
}

.rejection-subtitle {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin: 0 0 30px 0;
}

.rejection-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.rejection-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.confirm-reject-btn {
    flex: 1;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
}

.confirm-reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
}

/* Custom Scrollbar */
.requests-container::-webkit-scrollbar,
.modal-container::-webkit-scrollbar {
    width: 6px;
}

.requests-container::-webkit-scrollbar-track,
.modal-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb,
.modal-container::-webkit-scrollbar-thumb {
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb:hover,
.modal-container::-webkit-scrollbar-thumb:hover {
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
        padding: 20px;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-top {
        grid-template-columns: 1fr;
        gap: 15px;
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
</style>