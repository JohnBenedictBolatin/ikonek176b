<template>
    <Head>
        <title>History</title>
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
                    <div class="notification-header-wrap">
                        <button type="button" class="notification-bell-btn" @click="toggleNotifications" aria-label="Notifications">
                            <svg class="notification-bell-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="unreadNotificationCount > 0" class="notification-badge">{{ unreadNotificationCount > 99 ? '99+' : unreadNotificationCount }}</span>
                        </button>
                        <div v-if="showNotifications" class="notification-dropdown">
                            <div class="notification-dropdown-header">
                                <span class="notification-dropdown-title">Notifications</span>
                                <button v-if="unreadNotificationCount > 0" type="button" class="notification-mark-all" @click="markAllNotificationsRead">Mark all as read</button>
                            </div>
                            <div class="notification-dropdown-list">
                                <div v-if="loadingNotifications" class="notification-loading">Loading...</div>
                                <template v-else-if="notificationsList.length === 0">
                                    <div class="notification-empty">No notifications</div>
                                </template>
                                <template v-else>
                                    <div v-for="n in notificationsList" :key="n.id" class="notification-item" :class="{ unread: !n.is_read }" @click="handleNotificationClick(n)">
                                        <img :src="n.avatar" alt="" class="notification-item-avatar" @error="n.avatar = '/assets/DEFAULT.jpg'" />
                                        <div class="notification-item-body">
                                            <p class="notification-item-text"><strong>{{ n.user }}</strong> {{ n.action }}</p>
                                            <span class="notification-item-time">{{ n.time }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="settings-burger-btn" @click="toggleSettings" aria-label="Settings">
                        <svg class="settings-burger-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <!-- Settings Dropdown -->
                    <div v-if="showSettings" class="settings-dropdown">
                        <a href="#" class="settings-item" @click.prevent.stop="openTermsModal">TERMS & CONDITIONS</a>
                        <Link href="#" class="settings-item" @click.prevent="logout">
                            SIGN OUT
                        </Link>
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
                        <div class="profile-name">{{user.name || 'Unkown User'}}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToDocumentRequest"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToRegisterRequest"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Event Assistance Request
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
                            <h2>History</h2>
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
                        <!-- Loading state -->
                        <div v-if="isLoading" class="loading-state">
                            <p>Loading history...</p>
                        </div>
                        
                        <!-- History table -->
                        <table v-else class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Profile</th>
                                    <th style="width: 180px; text-align: left; padding-left: 20px;">Full Name</th>
                                    <th style="width: 200px; text-align: center;">Role</th>
                                    <th style="width: 250px; text-align: left; padding-left: 25px;">Service Requested</th>
                                    <th style="width: 140px; text-align: center;">Action</th>
                                    <th style="width: 150px; text-align: center;">Approver</th>
                                    <th style="width: 110px; text-align: center;">Date</th>
                                    <th style="width: 120px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="activity in filteredHistory" 
                                    :key="activity.id"
                                    class="user-row"
                                >
                                    <td style="text-align: center; padding: 15px 8px;">
                                        <img :src="activity.avatar" :alt="activity.name" class="user-avatar" />
                                    </td>
                                    <td style="text-align: left; padding: 15px 12px 15px 20px;" class="user-name">{{ activity.name }}</td>
                                    <td style="text-align: center; padding: 15px 15px;">
                                        <span class="role-badge" :class="getRoleClass(activity.role)">
                                            {{ activity.role.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: left; padding: 15px 12px 15px 25px;">{{ activity.activity }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">
                                        <span class="action-badge" :class="getActionClass(activity.action)">
                                            {{ activity.action.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; padding: 15px 12px;">{{ activity.approver }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">{{ activity.date }}</td>
                                    <td style="text-align: center; padding: 15px 12px;">
                                        <button 
                                            v-if="activity.type === 'document' && activity.action === 'Approved' && activity.docRequestId"
                                            @click="downloadDocument(activity.docRequestId)"
                                            class="download-btn"
                                            title="Download Document"
                                        >
                                            DOWNLOAD
                                        </button>
                                        <span v-else style="color: #999;">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- No history message -->
                        <div v-if="!isLoading && filteredHistory.length === 0" class="no-users">
                            <p>No history records found matching your criteria.</p>
                        </div>
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
                            As an Approver, you are responsible for reviewing and processing document requests and event assistance requests for the iKonek176B system. You must exercise your approval privileges with care and in accordance with barangay policies and regulations. Your decisions directly impact residents' access to essential services and assistance.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">2. Access and Security</h3>
                        <p class="terms-text">
                            You have been granted access to approval functions for document and event assistance requests. You must maintain the confidentiality of your login credentials and immediately report any suspected security breaches. Sharing your account credentials with unauthorized persons is strictly prohibited and may result in immediate account termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">3. Request Review and Processing</h3>
                        <p class="terms-text">
                            You are responsible for reviewing requests in a timely and fair manner. When processing requests, you must:
                            <ul class="terms-list">
                                <li>Verify that all required documents and information are provided</li>
                                <li>Ensure requests meet the eligibility criteria and documented requirements</li>
                                <li>Approve or reject requests based on valid criteria and barangay policies</li>
                                <li>Provide clear feedback when rejecting requests, explaining the reason for rejection</li>
                                <li>Set appropriate assistance details when approving event assistance requests</li>
                                <li>Maintain accurate records of all approval decisions</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">4. Data Privacy and Confidentiality</h3>
                        <p class="terms-text">
                            You have access to sensitive personal information of residents and officials through request submissions. You must handle all data in accordance with the Data Privacy Act of 2012. Personal information must only be accessed for legitimate approval purposes and must never be disclosed to unauthorized parties or used for personal gain.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">5. Approval and Rejection Decisions</h3>
                        <p class="terms-text">
                            When approving or rejecting requests, you must ensure that all decisions are justified, documented, and in compliance with barangay policies. Discrimination or bias in processing requests is strictly prohibited. All approval actions are logged and may be subject to audit. You must provide constructive feedback when rejecting requests to help residents understand what is needed.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">6. Document Verification</h3>
                        <p class="terms-text">
                            You must carefully verify all submitted documents and attachments to ensure they are legitimate and meet the requirements. If documents appear fraudulent, incomplete, or do not match the request details, you must reject the request and document the reason clearly. Failure to properly verify documents may result in improper approvals or service delivery issues.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">7. Limitations and Restrictions</h3>
                        <p class="terms-text">
                            Your approval privileges do not grant you the right to:
                            <ul class="terms-list">
                                <li>Access or modify system code or database structure without authorization</li>
                                <li>Bypass system security measures or attempt to exploit system vulnerabilities</li>
                                <li>Use approval functions for personal purposes or to gain unfair advantage</li>
                                <li>Delete or modify approval logs or audit trails</li>
                                <li>Grant approval privileges to other users without proper authorization</li>
                                <li>Approve requests without proper verification</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">8. Prohibited Activities</h3>
                        <p class="terms-text">
                            The following activities are strictly prohibited:
                            <ul class="terms-list">
                                <li>Unauthorized access to request records or user data</li>
                                <li>Tampering with request records or documentation</li>
                                <li>Approving requests without proper verification</li>
                                <li>Sharing confidential information outside of official channels</li>
                                <li>Engaging in any activity that compromises system security or integrity</li>
                                <li>Accepting bribes or favors in exchange for request approval</li>
                                <li>Discriminating against residents based on personal bias or prejudice</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">9. Accountability and Auditing</h3>
                        <p class="terms-text">
                            All approval actions are logged and monitored. You are accountable for all actions performed using your account. Regular audits may be conducted to ensure compliance with these terms and barangay policies. Failure to comply may result in disciplinary action, including but not limited to account suspension or termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">10. Violations and Consequences</h3>
                        <p class="terms-text">
                            Violation of these terms and conditions may result in immediate suspension or termination of your approval account, legal action if applicable, and reporting to appropriate barangay authorities. The severity of consequences will depend on the nature and extent of the violation.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">11. Updates to Terms</h3>
                        <p class="terms-text">
                            These terms and conditions may be updated periodically. You will be notified of significant changes. Continued use of approval privileges after changes constitutes acceptance of the updated terms.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">12. Contact and Support</h3>
                        <p class="terms-text">
                            For questions, concerns, or to report issues related to your approval role, contact the Barangay 176B office at ikonek176b@dev.ph or +639193076338.
                        </p>
                    </div>
                </div>
                <div class="terms-modal-footer">
                    <button @click="closeTermsModal" class="terms-modal-btn">
                        I Understand
                    </button>
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

// map of role_id -> role_name (based on the table you provided)
const roleMap = {
  1: 'Resident',
  2: 'Barangay Captain',
  3: 'Barangay Secretary',
  4: 'Barangay Treasurer',
  5: 'Barangay Kagawad',
  6: 'SK Chairman',
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
const showNotifications = ref(false)
const notificationsList = ref([])
const loadingNotifications = ref(false)
const unreadNotificationCount = computed(() => notificationsList.value.filter(n => !n.is_read).length)
const showTermsModal = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const isLoading = ref(false)

// History data - will be populated from backend
const history = ref([])

// Format date helper - converts UTC to Philippines timezone (Asia/Manila, UTC+8)
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    // Parse the date string (assumes it's in UTC from server)
    const date = new Date(dateStr)
    if (isNaN(date.getTime())) return ''
    
    // Use Intl.DateTimeFormat to format in Philippines timezone
    const formatter = new Intl.DateTimeFormat('en-US', {
      timeZone: 'Asia/Manila',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
    
    const parts = formatter.formatToParts(date)
    const month = parts.find(p => p.type === 'month').value
    const day = parts.find(p => p.type === 'day').value
    const year = parts.find(p => p.type === 'year').value
    
    return `${month}/${day}/${year}`
  } catch (e) {
    return ''
  }
}

// Get approver name helper - get from approver_info in the response
const getApproverName = (req) => {
  // First try to get from approver_info (from backend)
  if (req.approver_info && req.approver_info.name) {
    return req.approver_info.name
  }
  // Fallback: if there's an approver ID and it matches current user, use current user's name
  if (req.fk_approver_id && user.value?.name) {
    const nameParts = user.value.name.split(' ')
    if (nameParts.length > 0) {
      return nameParts[0] + (nameParts.length > 1 ? ' ' + nameParts[nameParts.length - 1].charAt(0) + '.' : '')
    }
    return user.value.name
  }
  return 'N/A'
}

// Fetch history data from backend
const fetchHistory = async () => {
  isLoading.value = true
  try {
    // Build URLs for document requests
    const buildDocUrl = (status) => {
      return `/a_document_request?status=${status}`
    }

    // Build URLs for event assistance requests
    const buildEventUrl = (status) => {
      return `/a_event_request?status=${status}`
    }

    // Fetch approved and rejected document requests and event assistance requests
    const [approvedDocRes, rejectedDocRes, approvedEventRes, rejectedEventRes] = await Promise.all([
      axios.get(buildDocUrl('Approved'), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      }),
      axios.get(buildDocUrl('Rejected'), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      }),
      axios.get(buildEventUrl('Approved'), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      }),
      axios.get(buildEventUrl('Rejected'), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
    ])

    // Extract requests from JSON responses
    let approvedDocRequests = approvedDocRes?.data?.document_requests || []
    let rejectedDocRequests = rejectedDocRes?.data?.document_requests || []
    let approvedEventRequests = approvedEventRes?.data?.event_requests || []
    let rejectedEventRequests = rejectedEventRes?.data?.event_requests || []
    
    // Debug logging
    console.log('Approved document requests found:', approvedDocRequests.length)
    console.log('Rejected document requests found:', rejectedDocRequests.length)
    console.log('Approved event requests found:', approvedEventRequests.length)
    console.log('Rejected event requests found:', rejectedEventRequests.length)

    const historyItems = []

    // Map approved document requests
    approvedDocRequests.forEach((req) => {
      const userInfo = req.user_info || {}
      const approver = getApproverName(req)
      
      let profileImg = '/assets/DEFAULT.jpg'
      if (userInfo.profile_pic) {
        if (String(userInfo.profile_pic).startsWith('http')) {
          profileImg = userInfo.profile_pic
        } else {
          profileImg = `/storage/${userInfo.profile_pic}`.replace('//', '/')
        }
      }

      const roleId = userInfo.fk_role_id || userInfo.role_id || 1
      const role = roleMap[roleId] || 'Resident'

      const docTypeName = req.document_type?.name || req.documentType?.name || req.documentType || 'Document Request'

      // Parse date properly - use reviewed_at if available, otherwise created_at, otherwise use epoch (oldest)
      const dateSource = req.reviewed_at || req.created_at
      let dateObj = new Date(0) // Default to epoch (oldest) if no date
      if (dateSource) {
        const parsed = new Date(dateSource)
        if (!isNaN(parsed.getTime())) {
          dateObj = parsed
        }
      }

      historyItems.push({
        id: req.doc_request_id || req.id,
        name: req.name || `${req.first_name || ''} ${req.last_name || ''}`.trim() || 'Unknown',
        role: role,
        activity: docTypeName,
        action: 'Approved',
        approver: approver,
        date: formatDate(dateSource),
        dateObj: dateObj,
        avatar: profileImg,
        type: 'document',
        docRequestId: req.doc_request_id || req.id,
        documentType: req.document_type?.name || req.documentType?.name || docTypeName,
        status: req.status || 'Approved'
      })
    })

    // Map rejected document requests
    rejectedDocRequests.forEach((req) => {
      const userInfo = req.user_info || {}
      const approver = getApproverName(req)
      
      let profileImg = '/assets/DEFAULT.jpg'
      if (userInfo.profile_pic) {
        if (String(userInfo.profile_pic).startsWith('http')) {
          profileImg = userInfo.profile_pic
        } else {
          profileImg = `/storage/${userInfo.profile_pic}`.replace('//', '/')
        }
      }

      const roleId = userInfo.fk_role_id || userInfo.role_id || 1
      const role = roleMap[roleId] || 'Resident'

      const docTypeName = req.document_type?.name || req.documentType?.name || req.documentType || 'Document Request'

      // Parse date properly - use reviewed_at if available, otherwise created_at, otherwise use epoch (oldest)
      const dateSource = req.reviewed_at || req.created_at
      let dateObj = new Date(0) // Default to epoch (oldest) if no date
      if (dateSource) {
        const parsed = new Date(dateSource)
        if (!isNaN(parsed.getTime())) {
          dateObj = parsed
        }
      }

      historyItems.push({
        id: req.doc_request_id || req.id,
        name: req.name || `${req.first_name || ''} ${req.last_name || ''}`.trim() || 'Unknown',
        role: role,
        activity: docTypeName,
        action: 'Rejected',
        approver: approver,
        date: formatDate(dateSource),
        dateObj: dateObj,
        avatar: profileImg,
        type: 'document',
        docRequestId: req.doc_request_id || req.id,
        documentType: req.document_type?.name || req.documentType?.name || docTypeName,
        status: req.status || 'Rejected'
      })
    })

    // Map approved event assistance requests
    approvedEventRequests.forEach((req) => {
      const userInfo = req.user_info || {}
      const approver = getApproverName(req)
      
      let profileImg = '/assets/DEFAULT.jpg'
      if (userInfo.profile_pic) {
        if (String(userInfo.profile_pic).startsWith('http')) {
          profileImg = userInfo.profile_pic
        } else {
          profileImg = `/storage/${userInfo.profile_pic}`.replace('//', '/')
        }
      }

      const roleId = userInfo.fk_role_id || userInfo.role_id || 1
      const role = roleMap[roleId] || 'Resident'

      const eventTypeName = req.event_type || 'Event Assistance Request'

      // Parse date properly - use reviewed_at if available, otherwise created_at, otherwise use epoch (oldest)
      const dateSource = req.reviewed_at || req.created_at
      let dateObj = new Date(0) // Default to epoch (oldest) if no date
      if (dateSource) {
        const parsed = new Date(dateSource)
        if (!isNaN(parsed.getTime())) {
          dateObj = parsed
        }
      }

      historyItems.push({
        id: req.event_assist_request_id || req.id,
        name: req.name || `${req.first_name || ''} ${req.last_name || ''}`.trim() || 'Unknown',
        role: role,
        activity: eventTypeName,
        action: 'Approved',
        approver: approver,
        date: formatDate(dateSource),
        dateObj: dateObj,
        avatar: profileImg,
        type: 'event',
        status: req.status || 'Approved'
      })
    })

    // Map rejected event assistance requests
    rejectedEventRequests.forEach((req) => {
      const userInfo = req.user_info || {}
      const approver = getApproverName(req)
      
      let profileImg = '/assets/DEFAULT.jpg'
      if (userInfo.profile_pic) {
        if (String(userInfo.profile_pic).startsWith('http')) {
          profileImg = userInfo.profile_pic
        } else {
          profileImg = `/storage/${userInfo.profile_pic}`.replace('//', '/')
        }
      }

      const roleId = userInfo.fk_role_id || userInfo.role_id || 1
      const role = roleMap[roleId] || 'Resident'

      const eventTypeName = req.event_type || 'Event Assistance Request'

      // Parse date properly - use reviewed_at if available, otherwise created_at, otherwise use epoch (oldest)
      const dateSource = req.reviewed_at || req.created_at
      let dateObj = new Date(0) // Default to epoch (oldest) if no date
      if (dateSource) {
        const parsed = new Date(dateSource)
        if (!isNaN(parsed.getTime())) {
          dateObj = parsed
        }
      }

      historyItems.push({
        id: req.event_assist_request_id || req.id,
        name: req.name || `${req.first_name || ''} ${req.last_name || ''}`.trim() || 'Unknown',
        role: role,
        activity: eventTypeName,
        action: 'Rejected',
        approver: approver,
        date: formatDate(dateSource),
        dateObj: dateObj,
        avatar: profileImg,
        type: 'event',
        status: req.status || 'Rejected'
      })
    })

    // Sort by newest first (most recent first) by default
    historyItems.sort((a, b) => {
      // Ensure dateObj is valid
      const aDate = a.dateObj instanceof Date && !isNaN(a.dateObj.getTime()) ? a.dateObj : new Date(0)
      const bDate = b.dateObj instanceof Date && !isNaN(b.dateObj.getTime()) ? b.dateObj : new Date(0)
      return bDate.getTime() - aDate.getTime() // Descending order (newest first)
    })
    
    history.value = historyItems
  } catch (error) {
    console.error('Error fetching history:', error)
    history.value = []
  } finally {
    isLoading.value = false
  }
}

// Computed filtered history
const filteredHistory = computed(() => {
    let filtered = [...history.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) ||
            item.activity.toLowerCase().includes(query) ||
            item.action.toLowerCase().includes(query) ||
            item.approver.toLowerCase().includes(query)
        )
    }

    // Action filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.action.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => {
            // Ensure dateObj is valid Date object
            const aDate = a.dateObj instanceof Date && !isNaN(a.dateObj.getTime()) ? a.dateObj : new Date(0)
            const bDate = b.dateObj instanceof Date && !isNaN(b.dateObj.getTime()) ? b.dateObj : new Date(0)
            return bDate.getTime() - aDate.getTime() // Descending order (newest first)
        })
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => {
            // Ensure dateObj is valid Date object
            const aDate = a.dateObj instanceof Date && !isNaN(a.dateObj.getTime()) ? a.dateObj : new Date(0)
            const bDate = b.dateObj instanceof Date && !isNaN(b.dateObj.getTime()) ? b.dateObj : new Date(0)
            return aDate.getTime() - bDate.getTime() // Ascending order (oldest first)
        })
    } else if (sortOption.value === 'relevant') {
        // Sort by action priority: Rejected > Approved, then by date (newest first)
        const priority = { 'rejected': 1, 'approved': 2 }
        filtered.sort((a, b) => {
            const aPriority = priority[a.action.toLowerCase()] || 3
            const bPriority = priority[b.action.toLowerCase()] || 3
            if (aPriority !== bPriority) {
                return aPriority - bPriority
            }
            // If same priority, sort by date (newest first)
            const aDate = a.dateObj instanceof Date && !isNaN(a.dateObj.getTime()) ? a.dateObj : new Date(0)
            const bDate = b.dateObj instanceof Date && !isNaN(b.dateObj.getTime()) ? b.dateObj : new Date(0)
            return bDate.getTime() - aDate.getTime()
        })
    }

    return filtered
})

// Methods
const toggleSettings = () => {
    showNotifications.value = false
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const fetchNotifications = async () => {
    loadingNotifications.value = true
    try {
        const res = await axios.get('/api/notifications')
        if (res.data?.success && Array.isArray(res.data.notifications)) {
            notificationsList.value = res.data.notifications
        }
    } catch (e) {
        console.error('Failed to fetch notifications', e)
        notificationsList.value = []
    } finally {
        loadingNotifications.value = false
    }
}

const toggleNotifications = () => {
    showSettings.value = false
    showNotifications.value = !showNotifications.value
    if (showNotifications.value) fetchNotifications()
}

const markAllNotificationsRead = async () => {
    try {
        await axios.put('/api/notifications/mark-all-read')
        notificationsList.value = notificationsList.value.map(n => ({ ...n, is_read: true }))
    } catch (e) {
        console.error('Failed to mark all read', e)
    }
}

const markNotificationRead = async (id) => {
    try {
        await axios.put(`/api/notifications/${id}/read`)
        const n = notificationsList.value.find(x => x.id === id)
        if (n) n.is_read = true
    } catch (e) {
        console.error('Failed to mark read', e)
    }
}

const handleNotificationClick = (n) => {
    if (!n.is_read) markNotificationRead(n.id)
    showNotifications.value = false
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

const downloadDocument = async (docRequestId) => {
  try {
    // Try to use route helper first
    let downloadUrl
    try {
      downloadUrl = route('document_requests.download', { 
        id: docRequestId, 
        format: 'pdf' 
      })
    } catch (routeError) {
      // Fallback to direct URL if route helper fails
      downloadUrl = `/document-requests/${docRequestId}/download/pdf`
    }
    
    // Open download URL in new window/tab - browser will handle the download
    window.open(downloadUrl, '_blank')
  } catch (error) {
    console.error('Error downloading document:', error)
    alert('Failed to download document. Please try again.')
  }
}

const selectFilter = (option) => {
    filterOption.value = option
    showFilterDropdown.value = false
}

const logout = () => {
    showSettings.value = false
    router.visit(route('login_approver'))
}

const openTermsModal = (e) => {
    if (e) {
        e.preventDefault()
        e.stopPropagation()
    }
    showSettings.value = false
    showTermsModal.value = true
}

const closeTermsModal = () => {
    showTermsModal.value = false
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const getActionClass = (action) => {
    const actionLower = action.toLowerCase()
    if (actionLower === 'approved') return 'approved'
    if (actionLower === 'rejected') return 'rejected'
    return 'default'
}

const getRoleClass = (role) => {
    const roleLower = role.toLowerCase().replace(/\s+/g, '-')
    // Check if it's a resident (role_id 1)
    if (roleLower === 'resident') return 'resident'
    // All other roles are officials
    return 'official'
}

const navigateToDocumentRequest = () => {
    router.visit(route('document_request_approver'))
}

const navigateToRegisterRequest = () => {
    router.visit(route('event_request_approver'))
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    // Don't close anything if clicking on the terms modal
    if (event.target.closest('.terms-modal') || event.target.closest('.modal-overlay')) {
        return
    }
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
        showNotifications.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showFilterDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    fetchHistory()
    fetchNotifications()
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
    display: flex;
    align-items: center;
    gap: 24px;
}

.notification-header-wrap { position: relative; }
.notification-bell-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; padding: 0; border: none; background: transparent; cursor: pointer; border-radius: 50%; color: white; transition: background 0.2s, transform 0.2s; }
.notification-bell-btn:hover { background: rgba(255,255,255,0.15); transform: scale(1.05); }
.notification-bell-icon { width: 24px; height: 24px; }
.notification-badge { position: absolute; top: 2px; right: 2px; min-width: 18px; height: 18px; padding: 0 5px; font-size: 11px; font-weight: 700; color: white; background: #e41e3a; border-radius: 10px; display: flex; align-items: center; justify-content: center; line-height: 1; }
.notification-dropdown { position: absolute; top: 100%; right: 0; margin-top: 8px; width: 380px; max-width: 90vw; background: white; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.1); z-index: 1001; overflow: hidden; }
.notification-dropdown-header { padding: 14px 16px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; background: #fafafa; }
.notification-dropdown-title { font-weight: 700; font-size: 16px; color: #333; }
.notification-mark-all { font-size: 13px; color: #ff8c42; background: none; border: none; cursor: pointer; font-weight: 500; padding: 0; }
.notification-mark-all:hover { text-decoration: underline; }
.notification-dropdown-list { max-height: 400px; overflow-y: auto; }
.notification-loading, .notification-empty { padding: 24px 16px; text-align: center; color: #666; font-size: 14px; }
.notification-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background 0.15s; }
.notification-item:hover { background: #f5f5f5; }
.notification-item.unread { background: #f0f7ff; }
.notification-item-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.notification-item-body { flex: 1; min-width: 0; }
.notification-item-text { margin: 0 0 4px 0; font-size: 14px; color: #333; line-height: 1.4; }
.notification-item-text strong { font-weight: 600; }
.notification-item-time { font-size: 12px; color: #888; }

.settings-burger-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    margin-right: 30px;
    padding: 0;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 50%;
    color: white;
    transition: background 0.2s, transform 0.2s;
}

.settings-burger-btn:hover {
    background: rgba(255,255,255,0.15);
    transform: scale(1.05);
}

.settings-burger-icon {
    width: 24px;
    height: 24px;
}

.settings-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 240px;
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
    white-space: nowrap;
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

/* Users Container */
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
.users-table th:nth-child(7) {
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
.users-table td:nth-child(7) {
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

/* Default style for any role badge that doesn't match above classes */
.role-badge:not(.resident):not(.official) {
    background: linear-gradient(135deg, #6b7280, #4b5563);
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

.download-btn {
    background: transparent;
    color: #ff8c42;
    border: 2px solid #ff8c42;
    padding: 6px 14px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.download-btn:hover {
    background: #ff8c42;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 140, 66, 0.3);
}

.download-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(255, 140, 66, 0.2);
}

.loading-state {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.loading-state p {
    font-size: 16px;
    color: #999;
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

/* Custom Scrollbar */
.users-container::-webkit-scrollbar {
    width: 6px;
}

.users-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb {
    background: #999;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb:hover {
    background: #777;
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
}

/* Modal Overlay Styles */
.modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.6) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 9999 !important;
    backdrop-filter: blur(4px);
    visibility: visible !important;
    opacity: 1 !important;
}

/* Terms and Conditions Modal Styles */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Ensure terms modal overlay has proper z-index */
.modal-overlay:has(.terms-modal) {
    z-index: 10000 !important;
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
    position: relative;
    z-index: 10001;
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
</style>