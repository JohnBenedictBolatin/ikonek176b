<template>
    <Head>
        <title>Users Management</title>
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
                        <div class="profile-role">{{displayRole}}</div>
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
                        :href="route('registration_employee')" 
                        class="nav-item"
                    >
                        👔 Register Official
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToRegisterRequest"
                    >
                        📝 Register Requests
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
                        class="nav-item"
                        @click="navigateToReport"
                    >
                        🚩 Reports & Messages
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                    >
                        👥 Users
                    </Link>
                </div>

            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Users Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Users Management</h2>
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
                                    <button @click="selectSort('name')" :class="{ active: sortOption === 'name' }">NAME</button>
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

                    <!-- Users Table Container -->
                    <div class="users-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Profile</th>
                                    <th style="width: 180px;">Full Name</th>
                                    <th style="width: 250px;">Address</th>
                                    <th style="width: 140px;">Contact Number</th>
                                    <th style="width: 100px;">Role</th>
                                    <th style="width: 70px;">Posts</th>
                                    <th style="width: 100px;">Comments</th>
                                    <th style="width: 110px;">Date Joined</th>
                                    <th style="width: 280px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="user in filteredUsers" 
                                    :key="user.id"
                                    class="user-row"
                                >
                                    <td style="text-align: center;">
                                        <img :src="user.avatar" :alt="user.name" class="user-avatar" />
                                    </td>
                                    <td class="user-name">{{ user.name }}</td>
                                    <td class="user-address">{{ user.address }}</td>
                                    <td style="text-align: center;">{{ user.contact }}</td>
                                    <td style="text-align: center;">
                                        <span class="role-badge" :class="user.role.toLowerCase()">
                                            {{ user.role.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ user.posts }}</td>
                                    <td class="text-center">{{ user.comments }}</td>
                                    <td style="text-align: center;">{{ user.dateJoined }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button 
                                                class="action-button restrict-btn"
                                                @click="openRestrictModal(user)"
                                                title="Restrict User"
                                            >
                                                🚫 Restrict
                                            </button>
                                            <button 
                                                class="action-button password-btn"
                                                @click="openPasswordModal(user)"
                                                title="Change Password"
                                            >
                                                🔑 Password
                                            </button>
                                            <button 
                                                class="action-button delete-btn"
                                                @click="removeUser(user)"
                                                title="Delete User"
                                            >
                                                🗑️ Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- No users message -->
                        <div v-if="filteredUsers.length === 0" class="no-users">
                            <p>No users found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restriction Modal -->
        <div v-if="showRestrictionModal" class="modal-overlay" @click="closeRestrictionModal">
            <div class="modal-container" @click.stop>
                <div class="modal-header">
                    <h3 style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Restrict User
                    </h3>
                    <button class="modal-close" @click="closeRestrictionModal">×</button>
                </div>
                
                <div class="modal-body">
                    <div class="user-info-box">
                        <img :src="selectedUser?.avatar" :alt="selectedUser?.name" class="modal-user-avatar" />
                        <div class="modal-user-details">
                            <h4>{{ selectedUser?.name }}</h4>
                            <p>{{ selectedUser?.role }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Reason for Restriction *</label>
                        <textarea
                            v-model="restrictionReason"
                            placeholder="Please provide a detailed reason for restricting this user..."
                            class="form-textarea"
                            rows="4"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Select Restrictions *</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    v-model="restrictions.posting"
                                    class="checkbox-input"
                                />
                                <span class="checkbox-text">
                                    <strong>Restrict Posting</strong>
                                    <small>User cannot create new posts</small>
                                </span>
                            </label>
                            
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    v-model="restrictions.commenting"
                                    class="checkbox-input"
                                />
                                <span class="checkbox-text">
                                    <strong>Restrict Commenting</strong>
                                    <small>User cannot comment on posts</small>
                                </span>
                            </label>
                            
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    v-model="restrictions.messaging"
                                    class="checkbox-input"
                                />
                                <span class="checkbox-text">
                                    <strong>Restrict Messaging</strong>
                                    <small>User cannot send direct messages</small>
                                </span>
                            </label>
                            
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    v-model="restrictions.profileEdit"
                                    class="checkbox-input"
                                />
                                <span class="checkbox-text">
                                    <strong>Restrict Profile Editing</strong>
                                    <small>User cannot edit their profile</small>
                                </span>
                            </label>
                            
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    v-model="restrictions.fileUpload"
                                    class="checkbox-input"
                                />
                                <span class="checkbox-text">
                                    <strong>Restrict File Upload</strong>
                                    <small>User cannot upload files or images</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="justify-content: center;">
                    <button class="btn-restrict-user" @click="applyRestrictions">
                        RESTRICT USER
                    </button>
                </div>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div v-if="showPasswordModal" class="modal-overlay" @click="closePasswordModal">
            <div class="modal-container password-modal" @click.stop>
                <div class="modal-header">
                    <h3>🔑 Change Password</h3>
                    <button class="modal-close" @click="closePasswordModal">×</button>
                </div>
                
                <div class="modal-body">
                    <div class="user-info-box">
                        <img :src="selectedUser?.avatar" :alt="selectedUser?.name" class="modal-user-avatar" />
                        <div class="modal-user-details">
                            <h4>{{ selectedUser?.name }}</h4>
                            <p>{{ selectedUser?.role }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password *</label>
                        <div class="password-input-wrapper">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="newPassword"
                                placeholder="Enter new password"
                                class="form-input"
                            />
                            <button 
                                type="button" 
                                class="toggle-password-btn"
                                @click="showPassword = !showPassword"
                            >
                                {{ showPassword ? '👁️‍🗨️' : '👁️‍🗨️' }}
                            </button>
                        </div>
                        <small class="input-hint">Password must be at least 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password *</label>
                        <div class="password-input-wrapper">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                v-model="confirmPassword"
                                placeholder="Confirm new password"
                                class="form-input"
                            />
                            <button 
                                type="button" 
                                class="toggle-password-btn"
                                @click="showConfirmPassword = !showConfirmPassword"
                            >
                                {{ showConfirmPassword ? '👁️‍🗨️' : '👁️‍🗨️' }}
                            </button>
                        </div>
                    </div>

                    <div v-if="passwordError" class="error-message">
                        {{ passwordError }}
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-cancel" @click="closePasswordModal">
                        CANCEL
                    </button>
                    <button class="btn-apply" @click="changePassword">
                        Change Password
                    </button>
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
                            As a System Administrator, you are responsible for managing user accounts, processing registration requests, reviewing reports and messages, and maintaining the integrity of the iKonek176B system. You must exercise your administrative privileges with care and in accordance with barangay policies and regulations.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">2. Access and Security</h3>
                        <p class="terms-text">
                            You have been granted elevated access to the system. You must maintain the confidentiality of your login credentials and immediately report any suspected security breaches. Sharing your account credentials with unauthorized persons is strictly prohibited and may result in immediate account termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">3. Data Privacy and Confidentiality</h3>
                        <p class="terms-text">
                            You have access to sensitive personal information of residents and officials. You must handle all data in accordance with the Data Privacy Act of 2012. Personal information must only be accessed for legitimate administrative purposes and must never be disclosed to unauthorized parties or used for personal gain.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">4. User Account Management</h3>
                        <p class="terms-text">
                            When creating, modifying, or restricting user accounts, you must ensure that all actions are justified, documented, and in compliance with barangay policies. Unauthorized account creation, modification, or deletion is prohibited. All administrative actions are logged and may be subject to audit.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">5. Registration Requests</h3>
                        <p class="terms-text">
                            You are responsible for reviewing and processing registration requests in a timely and fair manner. Approval or rejection decisions must be based on valid criteria and documented requirements. Discrimination or bias in processing requests is strictly prohibited.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">6. Reports and Messages</h3>
                        <p class="terms-text">
                            You must review reports and contact messages promptly and take appropriate action when necessary. Actions taken on reported content must be justified and in accordance with community guidelines. Abuse of moderation powers is prohibited.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">7. Limitations and Restrictions</h3>
                        <p class="terms-text">
                            Your administrative privileges do not grant you the right to:
                            <ul class="terms-list">
                                <li>Access or modify system code or database structure without authorization</li>
                                <li>Bypass system security measures or attempt to exploit system vulnerabilities</li>
                                <li>Use administrative functions for personal purposes or to gain unfair advantage</li>
                                <li>Delete or modify system logs or audit trails</li>
                                <li>Grant administrative privileges to other users without proper authorization</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">8. Prohibited Activities</h3>
                        <p class="terms-text">
                            The following activities are strictly prohibited:
                            <ul class="terms-list">
                                <li>Unauthorized access to user accounts or data</li>
                                <li>Tampering with system records or documentation</li>
                                <li>Using administrative access to harass, intimidate, or discriminate against users</li>
                                <li>Sharing confidential information outside of official channels</li>
                                <li>Engaging in any activity that compromises system security or integrity</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">9. Accountability and Auditing</h3>
                        <p class="terms-text">
                            All administrative actions are logged and monitored. You are accountable for all actions performed using your account. Regular audits may be conducted to ensure compliance with these terms. Failure to comply may result in disciplinary action, including but not limited to account suspension or termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">10. Violations and Consequences</h3>
                        <p class="terms-text">
                            Violation of these terms and conditions may result in immediate suspension or termination of your administrative account, legal action if applicable, and reporting to appropriate barangay authorities. The severity of consequences will depend on the nature and extent of the violation.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">11. Updates to Terms</h3>
                        <p class="terms-text">
                            These terms and conditions may be updated periodically. You will be notified of significant changes. Continued use of administrative privileges after changes constitutes acceptance of the updated terms.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">12. Contact and Support</h3>
                        <p class="terms-text">
                            For questions, concerns, or to report issues related to your administrative role, contact the Barangay 176B office at ikonek176b@dev.ph or +639193076338.
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
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
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
const showRestrictionModal = ref(false)
const showPasswordModal = ref(false)
const selectedUser = ref(null)
const restrictionReason = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const passwordError = ref('')
const restrictions = ref({
    posting: false,
    commenting: false,
    messaging: false,
    profileEdit: false,
    fileUpload: false
})

// Sample users data
const users = ref([
    {
        id: 1,
        name: 'John Rey Santiago',
        address: 'Phase 2 Package 3 Block 27 Lot 23 Kanlaon St.',
        contact: '092278931321',
        role: 'Resident',
        posts: 5,
        comments: 15,
        dateJoined: '05/31/2025',
        avatar: '/assets/PROFILE PIC.jpg',
        dateJoinedObj: new Date('2025-05-31')
    },
    {
        id: 2,
        name: 'Joshua Rodriguez',
        address: 'Phase 2 Package 2 Block 2 Lot 1 Ayas St.',
        contact: '09102345125',
        role: 'Resident',
        posts: 1,
        comments: 5,
        dateJoined: '04/01/2025',
        avatar: '/assets/PROFILE PIC 2.jpg',
        dateJoinedObj: new Date('2025-04-01')
    },
    {
        id: 3,
        name: 'Kap. Sammy DG Reyes',
        address: 'Phase 1 Package 1 Block 5 Lot 12',
        contact: '09123456789',
        role: 'Official',
        posts: 12,
        comments: 45,
        dateJoined: '01/15/2025',
        avatar: '/assets/KAPITAN.jpg',
        dateJoinedObj: new Date('2025-01-15')
    },
    {
        id: 4,
        name: 'Maria Santos',
        address: 'Phase 3 Package 4 Block 10 Lot 5',
        contact: '09187654321',
        role: 'Resident',
        posts: 8,
        comments: 23,
        dateJoined: '03/20/2025',
        avatar: '/assets/PROFILE PIC 6.jpg',
        dateJoinedObj: new Date('2025-03-20')
    }
])

// Computed filtered users
const filteredUsers = computed(() => {
    let filtered = [...users.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(user => 
            user.name.toLowerCase().includes(query) ||
            user.address.toLowerCase().includes(query) ||
            user.contact.includes(query)
        )
    }

    // Role filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(user => 
            user.role.toLowerCase() === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateJoinedObj - a.dateJoinedObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateJoinedObj - b.dateJoinedObj)
    } else if (sortOption.value === 'name') {
        filtered.sort((a, b) => a.name.localeCompare(b.name))
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
    router.visit(route('login_admin'))
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const openRestrictModal = (user) => {
    selectedUser.value = user
    showRestrictionModal.value = true
    restrictionReason.value = ''
    restrictions.value = {
        posting: false,
        commenting: false,
        messaging: false,
        profileEdit: false,
        fileUpload: false
    }
}

const closeRestrictionModal = () => {
    showRestrictionModal.value = false
    selectedUser.value = null
}

const applyRestrictions = () => {
    const selectedRestrictions = Object.keys(restrictions.value).filter(key => restrictions.value[key])
    
    if (selectedRestrictions.length === 0) {
        alert('Please select at least one restriction.')
        return
    }

    if (!restrictionReason.value.trim()) {
        alert('Please provide a reason for the restriction.')
        return
    }

    // Here you would send to backend
    console.log('Applying restrictions:', {
        userId: selectedUser.value.id,
        reason: restrictionReason.value,
        restrictions: selectedRestrictions
    })

    alert(`Restrictions successfully applied to ${selectedUser.value.name}!\n\nRestricted: ${selectedRestrictions.join(', ')}`)
    closeRestrictionModal()
}

const openPasswordModal = (user) => {
    selectedUser.value = user
    showPasswordModal.value = true
    newPassword.value = ''
    confirmPassword.value = ''
    passwordError.value = ''
    showPassword.value = false
    showConfirmPassword.value = false
}

const closePasswordModal = () => {
    showPasswordModal.value = false
    selectedUser.value = null
    newPassword.value = ''
    confirmPassword.value = ''
    passwordError.value = ''
}

const changePassword = () => {
    passwordError.value = ''

    if (!newPassword.value.trim()) {
        passwordError.value = 'Please enter a new password.'
        return
    }

    if (newPassword.value.length < 8) {
        passwordError.value = 'Password must be at least 8 characters long.'
        return
    }

    if (newPassword.value !== confirmPassword.value) {
        passwordError.value = 'Passwords do not match.'
        return
    }

    // Here you would send to backend
    console.log('Changing password:', {
        userId: selectedUser.value.id,
        newPassword: newPassword.value
    })

    alert(`Password successfully changed for ${selectedUser.value.name}!`)
    closePasswordModal()
}

const removeUser = (user) => {
    if (confirm(`Are you sure you want to permanently remove ${user.name}?\n\nThis action cannot be undone.`)) {
        const index = users.value.findIndex(u => u.id === user.id)
        if (index !== -1) {
            users.value.splice(index, 1)
            alert(`${user.name} has been successfully removed.`)
        }
    }
}

const navigateToDashboard = () => {
    router.visit(route('dashboard_admin'))
}

const navigateToHistory = () => {
    router.visit(route('history_admin'))
}

const navigateToRegisterRequest = () => {
    router.visit(route('register_request_admin'))
}

const navigateToReport = () => {
    router.visit(route('report_admin'))
}

const openFAQ = () => {
    router.visit(route('help_center_admin'))
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
    border-radius: 20px;
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
    color: #333;
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
    align-items: center;
}

.search-container {
    display: flex;
    align-items: center;
    gap: 0;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #ddd;
    transition: all 0.2s;
}

.search-container:focus-within {
    border-color: #ff8c42;
    box-shadow: 0 0 0 3px rgba(255,140,66,0.1);
}

.search-input {
    padding: 8px 15px;
    border: none;
    width: 220px;
    font-size: 13px;
    outline: none;
    background: transparent;
    color: #333;
}

.search-input::placeholder {
    color: #999;
}

.search-btn {
    background: transparent;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.2s;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-btn:hover {
    color: #ff8c42;
}

.search-btn:hover {
    background: #e9ecef;
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
    background: #9c9b9b;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.users-table th {
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    vertical-align: middle;
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
    font-size: 13px;
    color: #555;
    vertical-align: middle;
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
}

.user-address {
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.role-badge {
    font-size: 11px;
    padding: 5px 12px;
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

.text-center {
    text-align: center;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.action-button {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.restrict-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.restrict-btn:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
}

.password-btn {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.password-btn:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}

.delete-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.delete-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
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

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.modal-container {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: slideUp 0.3s ease;
    display: flex;
    flex-direction: column;
}

.modal-container.password-modal {
    max-width: 500px;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    background: linear-gradient(135deg, #239640, #1a7230);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 28px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    line-height: 1;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #333;
}

.modal-body {
    padding: 25px;
    overflow-y: auto;
    flex: 1;
}

.user-info-box {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 12px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    border: 2px solid #239640;
}

.modal-user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.modal-user-details h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
    color: #1a7230;
    font-weight: 700;
}

.modal-user-details p {
    margin: 0;
    font-size: 13px;
    color: #666;
    font-weight: 600;
    background: #239640;
    color: white;
    padding: 3px 10px;
    border-radius: 10px;
    display: inline-block;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    transition: all 0.2s;
    outline: none;
}

.form-textarea:focus {
    border-color: #239640;
    box-shadow: 0 0 0 3px rgba(35, 150, 64, 0.1);
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
    outline: none;
}

.form-input:focus {
    border-color: #239640;
    box-shadow: 0 0 0 3px rgba(35, 150, 64, 0.1);
}

.password-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-wrapper input {
    padding-right: 50px;
}

.toggle-password-btn {
    position: absolute;
    right: 10px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    padding: 5px 10px;
    transition: transform 0.2s;
}

.toggle-password-btn:hover {
    transform: scale(1.1);
}

.input-hint {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #6b7280;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fafafa;
}

.checkbox-label:hover {
    background: #f0fdf4;
    border-color: #239640;
}

.checkbox-input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    margin-top: 2px;
    flex-shrink: 0;
    accent-color: #239640;
}

.checkbox-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.checkbox-text strong {
    font-size: 14px;
    color: #333;
    display: block;
}

.checkbox-text small {
    font-size: 12px;
    color: #6b7280;
    display: block;
}

.error-message {
    background: #fee2e2;
    border: 2px solid #ef4444;
    color: #991b1b;
    padding: 12px 15px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    margin-top: 15px;
}

.modal-footer {
    padding: 20px 25px;
    background: #f9fafb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid #e5e7eb;
}

.btn-cancel {
    padding: 12px 24px;
    background: white;
    border: 1px solid #e0e0e0;
    color: #4a4a4a;
    border-radius: 10px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.btn-cancel:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
}

.btn-apply {
    padding: 12px 24px;
    background: linear-gradient(135deg, #239640, #1a7230);
    border: none;
    color: white;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.3);
    font-size: 14px;
}

.btn-apply:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(35, 150, 64, 0.4);
}

.btn-restrict-user {
    padding: 12px 24px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
    color: white;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    white-space: nowrap;
}

.btn-restrict-user:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

/* Custom Scrollbar */
.users-container::-webkit-scrollbar,
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.users-container::-webkit-scrollbar-track,
.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb,
.modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.users-container::-webkit-scrollbar-thumb:hover,
.modal-body::-webkit-scrollbar-thumb:hover {
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
    
    .action-buttons {
        flex-direction: column;
        gap: 4px;
    }
    
    .action-button {
        font-size: 10px;
        padding: 5px 8px;
    }
    
    .modal-container {
        width: 95%;
        max-height: 95vh;
    }
}

/* Terms and Conditions Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
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