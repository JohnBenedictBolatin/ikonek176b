<template>
    <Head>
        <title>Register Request</title>
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
                        class="nav-item active"
                    >
                        📝 Register Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToReport"
                    >
                        🚩 Report
                    </Link>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Register Request Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Register Request</h2>
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

                    <!-- Requests Container -->
                    <div class="requests-container">
                        <div 
                            v-for="request in filteredRequests" 
                            :key="request.id"
                            class="request-card"
                        >
                            <div class="request-header">
                                <div class="request-user-info">
                                    <div class="request-details">
                                        <h3 class="request-name">{{ request.name }}</h3>
                                        <p class="request-type"><span class="role-highlight" :class="request.role.toLowerCase()">{{ request.role.toUpperCase() }}</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="request-body">
                                <p class="request-date">{{ request.date }}</p>
                                <button @click="openModal(request)" class="view-btn">
                                    View Request
                                </button>
                            </div>
                        </div>

                        <!-- No requests message -->
                        <div v-if="filteredRequests.length === 0" class="no-requests">
                            <p>No registration requests found matching your criteria.</p>
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
                    <!-- Top Section -->
                    <div class="modal-top">
                        <div class="modal-user-section">
                            <div>
                                <h3 class="modal-name">{{ selectedRequest.name }}</h3>
                                <p class="modal-label">Registration Request</p>
                            </div>
                        </div>
                        <div class="modal-role-section">
                            <h3 class="modal-role" :class="selectedRequest.role.toLowerCase()">{{ selectedRequest.role.toUpperCase() }}</h3>
                        </div>
                        <div class="modal-proof-section">
                            <button class="proof-btn">
                            Proof of Residency
                            </button>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="modal-details">
                        <!-- First Row -->
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

                        <!-- Second Row -->
                        <div class="details-grid-2">
                            <div class="detail-item">
                                <p class="detail-label">Phase:</p>
                                <p class="detail-value">{{ selectedRequest.phase }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Year in Barangay:</p>
                                <p class="detail-value">{{ selectedRequest.yearsInBarangay }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Package:</p>
                                <p class="detail-value">{{ selectedRequest.package }}</p>
                            </div>
                        </div>

                        <!-- Third Row -->
                        <div class="detail-item-full">
                            <p class="detail-label">Home Address:</p>
                            <p class="detail-value">{{ selectedRequest.address }}</p>
                        </div>

                        <!-- Comment Section -->
                        <div class="comment-section">
                            <textarea 
                                v-model="comment"
                                placeholder="Leave a comment..." 
                                class="comment-textarea"
                                rows="4"
                            ></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="modal-actions">
                            <button
                                @click="approveRequest"
                                class="approve-btn"
                                :disabled="loadingApprove || loadingReject"
                                :aria-disabled="loadingApprove || loadingReject"
                            >
                                <span v-if="loadingApprove">Processing…</span>
                                <span v-else>Approve</span>
                            </button>
                            <button
                                @click="rejectRequest"
                                class="reject-btn"
                                :disabled="loadingApprove || loadingReject"
                                :aria-disabled="loadingApprove || loadingReject"
                            >
                                <span v-if="loadingReject">Processing…</span>
                                <span v-else>Reject</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offenses Check Modal -->
        <div v-if="showOffensesModal" class="modal-overlay" @click="closeOffensesModal">
            <div class="offenses-modal-container" @click.stop>
                <button @click="closeOffensesModal" class="modal-close">✕</button>
                
                <div class="offenses-modal-content">
                    <h3 class="offenses-title">Record Check - Physical Records</h3>
                    <p class="offenses-subtitle">Check any offenses committed by {{ selectedRequest.name }} found in physical records</p>
                    
                    <div class="offenses-list">
                        <div 
                            v-for="offense in offensesList" 
                            :key="offense"
                            class="offense-item"
                        >
                            <label class="offense-checkbox-label">
                                <input 
                                    type="checkbox" 
                                    :value="offense"
                                    v-model="selectedOffenses"
                                    class="offense-checkbox"
                                />
                                <span class="offense-text">{{ offense }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="offenses-note">
                        <p><strong>Note:</strong> Leave all unchecked if no offenses found in records. Selected offenses will be recorded in the user's profile.</p>
                    </div>

                    <div class="offenses-actions">
                        <button @click="closeOffensesModal" class="cancel-offense-btn" :disabled="loadingApprove">
                            Cancel
                        </button>
                        <button
                            @click="confirmApproval"
                            class="confirm-offense-btn"
                            :disabled="loadingApprove"
                            :aria-disabled="loadingApprove"
                        >
                            <span v-if="loadingApprove">Approving…</span>
                            <span v-else>Confirm &amp; Approve</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Reason Modal -->
        <div v-if="showRejectModal" class="modal-overlay" @click="closeRejectModal">
            <div class="reject-modal-container" @click.stop>
                <button @click="closeRejectModal" class="modal-close">✕</button>
                
                <div class="reject-modal-content">
                    <h3 class="reject-title">Rejection Reason</h3>
                    <p class="reject-subtitle">Please provide a reason for rejecting {{ selectedRequest.name }}'s registration request</p>
                    
                    <div class="reject-reason-section">
                        <label class="reject-label">Reason for Rejection:</label>
                        <textarea 
                            v-model="rejectReason"
                            placeholder="Enter detailed reason for rejection..." 
                            class="reject-textarea"
                            rows="6"
                        ></textarea>
                    </div>

                    <div class="reject-note">
                        <p><strong>Note:</strong> This reason will be sent to the applicant and recorded in the system.</p>
                    </div>

                    <div class="reject-actions">
                        <button @click="closeRejectModal" class="cancel-reject-btn" :disabled="loadingReject">
                            Cancel
                        </button>
                        <button
                            @click="confirmRejection"
                            class="confirm-reject-btn"
                            :disabled="loadingReject"
                            :aria-disabled="loadingReject"
                        >
                            <span v-if="loadingReject">Rejecting…</span>
                            <span v-else>Confirm Rejection</span>
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
const selectedRequest = ref(null)
const comment = ref('')
const showOffensesModal = ref(false)
const showRejectModal = ref(false)
const selectedOffenses = ref([])
const rejectReason = ref('')
const loadingApprove = ref(false)
const loadingReject = ref(false)

const offensesList = [
    'Violation of Community Rules',
    'Noise Disturbance',
    'Improper Waste Disposal',
    'Boundary Disputes',
    'Illegal Construction',
    'Public Nuisance',
    'Failure to Attend Mandatory Meetings',
    'Non-payment of Community Dues',
    'Property Damage',
    'Other Violations'
]

// Server-sent requests (from Inertia props)
const serverRequests = page?.props?.registerRequests ?? []
console.log('Server-sent requests:', serverRequests)
console.log('Page props:', page.props)

// helper: compute age in years from birthdate string
const computeAgeFromBirthdate = (birthdate) => {
  if (!birthdate) return ''
  // Accept many formats: try native Date first
  let d = new Date(birthdate)
  if (isNaN(d.getTime())) {
    // Normalize common formats:
    // 1) YYYY-MM-DD or YYYY/MM/DD
    // 2) MM/DD/YYYY or MM-DD-YYYY
    // 3) "Month DD, YYYY" handled by Date usually, but fallback below
    const trimmed = ('' + birthdate).trim()
    // try YYYY-MM-DD
    const ymd = trimmed.match(/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/)
    if (ymd) {
      d = new Date(Number(ymd[1]), Number(ymd[2]) - 1, Number(ymd[3]))
    } else {
      // try MM/DD/YYYY
      const mdy = trimmed.match(/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/)
      if (mdy) {
        d = new Date(Number(mdy[3]), Number(mdy[1]) - 1, Number(mdy[2]))
      } else {
        // try to extract last 4-digit year and use heuristics
        const yearMatch = trimmed.match(/(\d{4})$/)
        if (yearMatch) {
          const year = Number(yearMatch[1])
          // try to find a month/day earlier in the string
          const md = trimmed.match(/(\d{1,2})[^\d]+(\d{1,2})[^\d]*\d{4}$/)
          if (md) {
            const month = Number(md[1])
            const day = Number(md[2])
            if (!isNaN(month) && !isNaN(day)) {
              d = new Date(year, month - 1, day)
            }
          }
        }
      }
    }
    if (isNaN(d.getTime())) return ''
  }

  const today = new Date()
  let age = today.getFullYear() - d.getFullYear()
  const m = today.getMonth() - d.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--
  return age >= 0 ? String(age) : ''
}


// hydrate requests from server and normalize fields expected by the front-end
const requests = ref(
  serverRequests.map(r => {
    // If backend still sends combined phase_package, attempt to extract phase+package
    let phase = r.phase ?? ''
    let packageVal = r.package ?? ''

    if ((!phase || !packageVal) && (r.phasePackage || r.phase_package)) {
      const text = r.phasePackage ?? r.phase_package ?? ''
      const phaseMatch = text.match(/(Phase\s*\d+)/i)
      const packageMatch = text.match(/(Package\s*\d+)/i)
      if (phaseMatch) phase = phaseMatch[1]
      if (packageMatch) packageVal = packageMatch[1]
      if (!phase || !packageVal) {
        const parts = text.split(/[,|\-–—|\/]/).map(p => p.trim())
        if (!phase && parts[0]) phase = parts[0]
        if (!packageVal && parts[1]) packageVal = parts[1]
      }
    }

    // --- ALWAYS compute age from birthdate on the client ---
    const bd = r.birthdate ?? r.bday ?? ''
    const computedAge = bd ? computeAgeFromBirthdate(bd) : ''

    return {
      ...r,
      // Ensure an `id` exists (front-end key)
      id: r.id ?? r.user_cred_id ?? r.registration_req_id ?? null,
      // date string and Date object for sorting
      date: r.date ?? (r.date_raw ? new Date(r.date_raw).toLocaleDateString() : (r.date_raw ?? '')),
      dateObj: r.date_raw ? new Date(r.date_raw) : (r.created_at ? new Date(r.created_at) : new Date()),
      // keep consistent keys used across the component
      name: (r.name ?? r.full_name ?? `${r.first_name ?? ''} ${r.last_name ?? ''}`.trim()) || 'Unknown',
      role: r.role ?? (r.is_official ? 'Official' : (r.role_name ?? (r.fk_role_id && r.fk_role_id !== 1 ? 'Official' : 'Resident'))),
      birthdate: bd,
      // set age computed from birthdate (frontend)
      age: computedAge,
      sex: r.sex ?? '',
      civilStatus: r.civilStatus ?? r.civil_status ?? '',
      contact: r.contact ?? r.contact_number ?? '',
      yearsInBarangay: r.yearsInBarangay ?? r.years_in_barangay ?? '',
      phase: phase ?? '',
      package: packageVal ?? '',
      address: r.address ?? r.home_address ?? '',
      description: r.description ?? '',
    }
  })
)

// --- Debugging server data ---
console.log('User object from page props:', user.value)
console.log('Server-sent requests:', serverRequests)

// --- Debugging hydrated requests ---
console.log('Hydrated requests after mapping:', requests.value)

// Computed filtered requests
const filteredRequests = computed(() => {
    let filtered = [...requests.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            (item.name && item.name.toLowerCase().includes(query)) ||
            (item.description && item.description.toLowerCase().includes(query)) ||
            (item.role && item.role.toLowerCase().includes(query))
        )
    }

    // Role filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.role.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'relevant') {
        // Sort by role priority: Official first, then Resident
        filtered.sort((a, b) => {
            if (a.role === 'Official' && b.role === 'Resident') return -1
            if (a.role === 'Resident' && b.role === 'Official') return 1
            return b.dateObj - a.dateObj
        })
    }

    return filtered
})

watch(filteredRequests, (newVal) => {
    console.log('Filtered requests updated:', newVal)
}, { immediate: true })

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

const openModal = (request) => {
    // ensure selectedRequest has age computed (in case request came from older code)
    if (request && request.birthdate) {
      request.age = computeAgeFromBirthdate(request.birthdate)
    }
    selectedRequest.value = request
    comment.value = ''
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedRequest.value = null
    comment.value = ''
}

const approveRequest = () => {
    showOffensesModal.value = true
}

const rejectRequest = () => {
    showRejectModal.value = true
}

// Confirm Approval -> call backend to create user from user_credentials and delete credential
const confirmApproval = async () => {
    if (!selectedRequest.value) return
    loadingApprove.value = true

    const id = selectedRequest.value.id
    const payload = {
        comment: comment.value || '',
        offenses: selectedOffenses.value
    }

    // Use named route with path param
    router.post(route('admin.register_requests.approve', id), payload, {
        preserveState: true,
        onSuccess: () => {
            // remove locally
            const idx = requests.value.findIndex(r => r.id === id)
            if (idx > -1) requests.value.splice(idx, 1)
            closeOffensesModal()
            closeModal()
            alert('Request approved.')
        },
        onError: (errors) => {
            console.error('Approve errors', errors)
            alert('Error approving request.')
        },
        onFinish: () => { loadingApprove.value = false }
    })
}

// Confirm Rejection -> call backend to delete user_credentials record and optionally record reason
const confirmRejection = async () => {
    if (!selectedRequest.value) return
    if (!rejectReason.value.trim()) {
        alert('Please provide a reason for rejection')
        return
    }
    loadingReject.value = true

    const id = selectedRequest.value.id
    const payload = {
        reason: rejectReason.value,
        comment: comment.value || ''
    }

    router.post(route('admin.register_requests.reject', id), payload, {
        preserveState: true,
        onSuccess: () => {
            const idx = requests.value.findIndex(r => r.id === id)
            if (idx > -1) requests.value.splice(idx, 1)
            closeRejectModal()
            closeModal()
            alert('Request rejected and removed.')
        },
        onError: (errors) => {
            console.error('Reject errors', errors)
            alert('Error rejecting request.')
        },
        onFinish: () => { loadingReject.value = false }
    })
}


const closeOffensesModal = () => {
    showOffensesModal.value = false
    selectedOffenses.value = []
}

const closeRejectModal = () => {
    showRejectModal.value = false
    rejectReason.value = ''
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
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>

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

.role-highlight {
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    color: white;
}

.role-highlight.resident {
    background: #239640;
}

.role-highlight.official {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}

.request-date {
    font-size: 15px;
    color: #999;
    margin: 5px 0 0 0;
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
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
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
    margin: 0;
}

.modal-role-section {
    display: flex;
    margin-bottom: 20px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.modal-role {
    font-size: 19px;
    font-weight: 500;
    margin: 20px 0 0 0;
    margin-left: 100px;
    padding: 10px 20px;
    border-radius: 10px;
    color: white;
}

.modal-role.resident {
    background: #239640;
}

.modal-role.official {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
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
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
    text-align: center;
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
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
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
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

/* Offenses Modal Styles */
.offenses-modal-container {
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

.offenses-modal-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.offenses-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0;
    padding-bottom: 5px;
}

.offenses-subtitle {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.offenses-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 400px;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
}

.offense-item {
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    transition: all 0.2s;
}

.offense-item:hover {
    border-color: #239640;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.1);
}

.offense-checkbox-label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
}

.offense-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #239640;
}

.offense-text {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.offenses-note {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 12px 15px;
    border-radius: 8px;
}

.offenses-note p {
    margin: 0;
    font-size: 13px;
    color: #856404;
}

.offenses-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 10px;
}

.cancel-offense-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.cancel-offense-btn:hover {
    background: #5a6268;
}

.confirm-offense-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.confirm-offense-btn:hover {
    background: #1e7d36;
}

/* Reject Modal Styles */
.reject-modal-container {
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

.reject-modal-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.reject-title {
    font-size: 24px;
    font-weight: 700;
    color: #dc2626;
    margin: 0;
    padding-bottom: 5px;
}

.reject-subtitle {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.reject-reason-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.reject-label {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.reject-textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    transition: border-color 0.3s;
}

.reject-textarea:focus {
    outline: none;
    border-color: #dc2626;
}

.reject-note {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
    padding: 12px 15px;
    border-radius: 8px;
}

.reject-note p {
    margin: 0;
    font-size: 13px;
    color: #991b1b;
}

.reject-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 10px;
}

.cancel-reject-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.cancel-reject-btn:hover {
    background: #5a6268;
}

.confirm-reject-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.confirm-reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
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
    background: #ff8c42;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb:hover,
.modal-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

.offenses-list::-webkit-scrollbar {
    width: 6px;
}

.offenses-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.offenses-list::-webkit-scrollbar-thumb {
    background: #239640;
    border-radius: 3px;
}

.offenses-list::-webkit-scrollbar-thumb:hover {
    background: #1e7d36;
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
    
    .details-grid,
    .details-grid-2 {
        grid-template-columns: 1fr;
    }
    
    .modal-top {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}
</style>