<template>
    <Head>
        <title>System Admin Dashboard</title>
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
                        <Link href="#" class="settings-item" @click="closeSettings">Terms & Conditions</Link>
                        <Link href="#" class="settings-item" @click.prevent="logout">
                            Sign Out
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
                        <div class="profile-name">{{user.name || 'Unknown User'}}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        :href="route('dashboard_admin')" 
                        class="nav-item active"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        Dashboard
                    </Link>
                    <Link 
                        :href="route('history_admin')" 
                        class="nav-item"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                    </Link>
                    <Link 
                        :href="route('register_request_admin')" 
                        class="nav-item"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Register Request
                    </Link>
                    <Link 
                        :href="route('registration_employee')" 
                        class="nav-item"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Register Official
                    </Link>
                    <Link 
                        :href="route('report_admin')" 
                        class="nav-item"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Report
                    </Link>
                </div>

            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Success Message Alert -->
                    <div v-if="page.props.flash?.success" class="success-alert" style="margin-bottom: 20px; padding: 15px 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; color: #155724; display: flex; align-items: center; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px; flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span style="flex: 1; font-weight: 500;">{{ page.props.flash.success }}</span>
                        <button @click="dismissSuccess" style="background: none; border: none; color: #155724; cursor: pointer; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Error Message Alert -->
                    <div v-if="page.props.flash?.error" class="error-alert" style="margin-bottom: 20px; padding: 15px 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; color: #721c24; display: flex; align-items: center; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px; flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span style="flex: 1; font-weight: 500;">{{ page.props.flash.error }}</span>
                        <button @click="dismissError" style="background: none; border: none; color: #721c24; cursor: pointer; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dashboard Header -->
                    <div class="dashboard-header">
                        <div class="dashboard-title">
                            <h2>Dashboard and User Management</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="dashboard-container">
                        <!-- Top Metrics Grid -->
                        <div class="metrics-grid">
                            <!-- Total Registration Requests Card -->
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-info">
                                        <div class="metric-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="metric-title">Registration Requests</div>
                                        <div class="metric-subtitle">As of {{ currentDate }}</div>
                                    </div>
                                    <div class="metric-value green">{{ stats.registration_requests }}</div>
                                </div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-info">
                                        <div class="metric-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="metric-title">Services Approved</div>
                                        <div class="metric-subtitle">As of {{ currentDate }}</div>
                                    </div>
                                    <div class="metric-value green">{{ stats.services_approved }}</div>
                                </div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-info">
                                        <div class="metric-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                            </svg>
                                        </div>
                                        <div class="metric-title">Total Posts</div>
                                        <div class="metric-subtitle">As of {{ currentDate }}</div>
                                    </div>
                                    <div class="metric-value green">{{ stats.total_posts }}</div>
                                </div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-info">
                                        <div class="metric-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div class="metric-title">Total Residents</div>
                                        <div class="metric-subtitle">As of {{ currentDate }}</div>
                                    </div>
                                    <div class="metric-value green">{{ stats.total_residents }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= USER MANAGEMENT SECTION ================= -->
                    <div class="user-management-section">
                        <!-- Filter Header -->
                        <div class="ums-filter-bar">
                            <div class="filter-left">
                                <span class="filter-label">Filter by</span>
                                <div class="filter-dropdown-wrapper">
                                    <button class="filter-dropdown-btn" @click="toggleSortDropdown">
                                        {{ sortBy.toUpperCase() }}
                                        <svg class="filter-arrow" :class="{ rotated: showSortDropdown }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                    <div v-if="showSortDropdown" class="filter-dropdown-menu">
                                        <button @click="selectSort('newest')" :class="{ active: sortBy === 'newest' }">NEWEST</button>
                                        <button @click="selectSort('oldest')" :class="{ active: sortBy === 'oldest' }">OLDEST</button>
                                    </div>
                                </div>
                                <div class="filter-dropdown-wrapper">
                                    <button class="filter-dropdown-btn" @click="toggleRoleDropdown">
                                        {{ roleFilter.toUpperCase() }}
                                        <svg class="filter-arrow" :class="{ rotated: showRoleDropdown }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                    <div v-if="showRoleDropdown" class="filter-dropdown-menu">
                                        <button @click="selectRole('all')" :class="{ active: roleFilter === 'all' }">ALL</button>
                                        <button @click="selectRole('resident')" :class="{ active: roleFilter === 'resident' }">RESIDENT</button>
                                        <button @click="selectRole('official')" :class="{ active: roleFilter === 'official' }">OFFICIAL</button>
                                    </div>
                                </div>
                            </div>
                            <div class="filter-right">
                                <div class="search-container">
                                    <input 
                                        type="text" 
                                        class="search-input" 
                                        placeholder="Search by name, address, contact, role..." 
                                        v-model="searchQuery"
                                    />
                                    <button class="search-btn" @click="performSearch">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="ums-table-wrapper">
                            <table class="ums-table">
                                <thead>
                                    <tr>
                                        <th>PROFILE</th>
                                        <th>FULL NAME</th>
                                        <th>ADDRESS</th>
                                        <th>CONTACT NUMBER</th>
                                        <th>ROLE</th>
                                        <th>POSTS</th>
                                        <th>COMMENTS</th>
                                        <th>DATE JOINED</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in filteredUsers" :key="user.id">
                                        <td>
                                            <img :src="user.profile" class="ums-avatar" alt="Profile" />
                                        </td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.address }}</td>
                                        <td>{{ user.contact }}</td>
                                        <td>
                                            <span :class="['role-badge', user.role === 'Official' ? 'role-official' : 'role-resident']">
                                                {{ user.role.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td>{{ user.posts }}</td>
                                        <td>{{ user.comments }}</td>
                                        <td>{{ user.joined }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-restrict" @click="openRestrictModal(user)" title="Restrict User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </button>
                                                <button class="btn-action btn-password" @click="openPasswordModal(user)" title="Change Password">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                    </svg>
                                                </button>
                                                <button class="btn-action btn-delete" @click="openDeleteModal(user)" title="Delete User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- No users message -->
                            <div v-if="filteredUsers.length === 0" class="no-users-message">
                                <p>No users found matching your criteria.</p>
                            </div>
                        </div>
                    </div>
                    <!-- =========================================================== -->
                    
                </div>
            </div>
            
        </div>

        <!-- Restrict User Modal -->
        <div v-if="showRestrictModal" class="modal-overlay" @click="closeRestrictModal">
            <div class="modal-container" @click.stop>
                <div class="modal-header">
                    <h3 style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Restrict User
                    </h3>
                    <button class="modal-close" @click="closeRestrictModal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="user-info-box">
                        <img :src="selectedUser?.profile" :alt="selectedUser?.name" class="modal-user-avatar" />
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

                <div class="modal-footer">
                    <button class="btn-cancel" @click="closeRestrictModal">
                        Cancel
                    </button>
                    <button class="btn-apply" @click="applyRestrictions">
                        Apply Restrictions
                    </button>
                </div>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div v-if="showPasswordModal" class="modal-overlay" @click="closePasswordModal">
            <div class="modal-container password-modal" @click.stop>
                <div class="modal-header">
                    <h3 style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Change Password
                    </h3>
                    <button class="modal-close" @click="closePasswordModal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="user-info-box">
                        <img :src="selectedUser?.profile" :alt="selectedUser?.name" class="modal-user-avatar" />
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
                                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l13.42 13.42" />
                                </svg>
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
                                <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l13.42 13.42" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div v-if="passwordError" class="error-message">
                        {{ passwordError }}
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-cancel" @click="closePasswordModal">
                        Cancel
                    </button>
                    <button class="btn-apply" @click="changePassword">
                        Change Password
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
            <div class="modal-container delete-modal" @click.stop>
                <div class="modal-header">
                    <h3 style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete User
                    </h3>
                    <button class="modal-close" @click="closeDeleteModal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="delete-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; color: #dc3545; flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h4>Are you sure you want to delete this user?</h4>
                    </div>

                    <div class="user-info-box">
                        <img :src="selectedUser?.profile" :alt="selectedUser?.name" class="modal-user-avatar" />
                        <div class="modal-user-details">
                            <h4>{{ selectedUser?.name }}</h4>
                            <p>{{ selectedUser?.role }}</p>
                            <p class="user-meta">{{ selectedUser?.address }}</p>
                        </div>
                    </div>

                    <div class="delete-notice">
                        <p><strong>This action cannot be undone.</strong></p>
                        <p>All user data, posts, comments, and related information will be permanently deleted.</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-cancel" @click="closeDeleteModal">
                        Cancel
                    </button>
                    <button class="btn-delete-confirm" @click="confirmDeleteUser">
                        Delete User
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

// Props from backend
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            registration_requests: 0,
            services_approved: 0,
            total_posts: 0,
            total_residents: 0,
        })
    },
    users: {
        type: Array,
        default: () => []
    }
})

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
const postRequestYear = ref('2025')
const postRequestRange = ref('jan')
const reportYear = ref('2025')
const reportRange = ref('jan')
const comparisonYear = ref('2025')
const comparisonRange = ref('jan')

// Computed properties
const currentDate = computed(() => {
    const date = new Date()
    return date.toLocaleDateString('en-US', { 
        month: '2-digit', 
        day: '2-digit', 
        year: 'numeric' 
    })
})

// Methods
const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const logout = () => {
  router.get(route('login_admin'))
}

const openHelp = () => {
    console.log('Open help center')
}

// Dismiss flash messages
const dismissSuccess = () => {
    router.reload({ only: [] })
}

const dismissError = () => {
    router.reload({ only: [] })
}

// Dropdown handlers
const toggleSortDropdown = () => {
    showSortDropdown.value = !showSortDropdown.value
    showRoleDropdown.value = false
}

const toggleRoleDropdown = () => {
    showRoleDropdown.value = !showRoleDropdown.value
    showSortDropdown.value = false
}

const selectSort = (value) => {
    sortBy.value = value
    showSortDropdown.value = false
}

const selectRole = (value) => {
    roleFilter.value = value
    showRoleDropdown.value = false
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showRoleDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

// ================= USER MANAGEMENT LOGIC =================

// Users from backend - make it reactive to props changes
const users = ref(Array.isArray(props.users) ? props.users : [])

// Watch for props changes and update users
watch(() => props.users, (newUsers) => {
    if (newUsers && Array.isArray(newUsers)) {
        users.value = newUsers
    } else {
        users.value = []
    }
}, { immediate: true, deep: true })

// Filters
const searchQuery = ref('')
const sortBy = ref('newest')
const roleFilter = ref('all')

// Dropdown states
const showSortDropdown = ref(false)
const showRoleDropdown = ref(false)

// Modal states
const showRestrictModal = ref(false)
const showPasswordModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

// Restrict modal data
const restrictionReason = ref('')
const restrictions = ref({
    posting: false,
    commenting: false,
    messaging: false,
    profileEdit: false,
    fileUpload: false
})

// Password modal data
const newPassword = ref('')
const confirmPassword = ref('')
const passwordError = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Search method
const performSearch = () => {
    // Search is handled automatically by the computed property filteredUsers
    // This method is kept for potential future enhancements
}

// Computed Filtered Users
const filteredUsers = computed(() => {
    // Ensure we have an array to work with
    if (!users.value || !Array.isArray(users.value)) {
        return []
    }
    
    let list = [...users.value]

    // Search - search in name, address, contact, role, and date joined
    if (searchQuery.value && searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim()
        list = list.filter(u => {
            if (!u) return false
            
            const name = String(u.name || '').toLowerCase()
            const address = String(u.address || '').toLowerCase()
            const contact = String(u.contact || '').toLowerCase()
            const role = String(u.role || '').toLowerCase()
            const dateJoined = String(u.joined || '').toLowerCase()
            
            return name.includes(query) || 
                   address.includes(query) || 
                   contact.includes(query) ||
                   role.includes(query) ||
                   dateJoined.includes(query)
        })
    }

    // Role Filter
    if (roleFilter.value !== 'all') {
        list = list.filter(u => u.role.toLowerCase() === roleFilter.value)
    }

    // Sorting by date joined - use timestamp if available, otherwise parse date
    if (sortBy.value === 'newest') {
        list.sort((a, b) => {
            if (a.joined_timestamp && b.joined_timestamp) {
                return b.joined_timestamp - a.joined_timestamp
            }
            // Fallback to parsing date string
            const dateA = new Date(a.joined.split('/').reverse().join('-'))
            const dateB = new Date(b.joined.split('/').reverse().join('-'))
            return dateB - dateA
        })
    } else if (sortBy.value === 'oldest') {
        list.sort((a, b) => {
            if (a.joined_timestamp && b.joined_timestamp) {
                return a.joined_timestamp - b.joined_timestamp
            }
            // Fallback to parsing date string
            const dateA = new Date(a.joined.split('/').reverse().join('-'))
            const dateB = new Date(b.joined.split('/').reverse().join('-'))
            return dateA - dateB
        })
    }

    return list
})

// Modal handlers
const openRestrictModal = (user) => {
    selectedUser.value = user
    showRestrictModal.value = true
    restrictionReason.value = ''
    restrictions.value = {
        posting: false,
        commenting: false,
        messaging: false,
        profileEdit: false,
        fileUpload: false
    }
}

const closeRestrictModal = () => {
    showRestrictModal.value = false
    selectedUser.value = null
    restrictionReason.value = ''
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

const openDeleteModal = (user) => {
    selectedUser.value = user
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedUser.value = null
}

// Actions
const applyRestrictions = async () => {
    const selectedRestrictions = Object.keys(restrictions.value).filter(key => restrictions.value[key])
    
    if (selectedRestrictions.length === 0) {
        alert('Please select at least one restriction.')
        return
    }

    if (!restrictionReason.value.trim()) {
        alert('Please provide a reason for the restriction.')
        return
    }

    try {
        const response = await window.axios.post(route('admin.users.restrict', selectedUser.value.id), {
            reason: restrictionReason.value,
            restrictions: restrictions.value
        })

        if (response.data.success) {
            alert(`User restrictions applied successfully to ${selectedUser.value.name}!`)
            closeRestrictModal()
        }
    } catch (error) {
        console.error('Error restricting user:', error)
        const message = error.response?.data?.message || 'Failed to apply restrictions. Please try again.'
        alert(message)
    }
}

const changePassword = async () => {
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

    try {
        const response = await window.axios.post(route('admin.users.password', selectedUser.value.id), {
            password: newPassword.value,
            password_confirmation: confirmPassword.value
        })

        if (response.data.success) {
            alert('Password changed successfully!')
            closePasswordModal()
        }
    } catch (error) {
        console.error('Error changing password:', error)
        passwordError.value = error.response?.data?.message || 'Failed to change password. Please try again.'
    }
}

const confirmDeleteUser = async () => {
    if (!selectedUser.value) return

    try {
        const response = await window.axios.delete(route('admin.users.delete', selectedUser.value.id))

        if (response.data.success) {
            users.value = users.value.filter(u => u.id !== selectedUser.value.id)
            alert(`${selectedUser.value.name} has been successfully deleted.`)
            closeDeleteModal()
            // Reload the page to refresh data
            router.reload()
        }
    } catch (error) {
        console.error('Error deleting user:', error)
        const message = error.response?.data?.message || 'Failed to delete user. Please try again.'
        alert(message)
    }
}

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

/* Orange Header Bar */
.header-bar {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    color: white;
    padding: 5px 0;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
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
    background: #fff7ef;
    color: #ff8c42;
}

.settings-item:last-child {
    border-bottom: none;
}

/* Main Layout - Full Width */
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
    background: #fff7ef;
    transform: translateX(3px);
}

.nav-item.active {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    color: #239640;
    font-weight: 600;
    border-left: 4px solid #239640;
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

/* Content Area */
.content-area {
    display: flex;
    flex-direction: column;
    width: 100%;
}

/* Main Content */
.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    flex: 1;
    border: 1px solid rgba(0,0,0,0.05);
}

.dashboard-header {
    background: #ff8c42;
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-title h2 {
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

/* Dashboard Container */
.dashboard-container {
    padding: 30px;
}

/* Metrics Grid */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 30px;
}

.metric-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
    position: relative;
}

.metric-card:hover {
    border-color: #239640;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.15);
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #239640, #2bb24a);
    border-radius: 12px 12px 0 0;
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
}

.metric-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
}

.metric-icon {
    width: 56px;
    height: 56px;
    padding: 12px;
    background: linear-gradient(135deg, #e8f8ed, #d4f1dd);
    border-radius: 12px;
    color: #239640;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.1);
}

.metric-icon svg {
    width: 32px;
    height: 32px;
    stroke-width: 2;
}

.metric-title {
    font-weight: 700;
    font-size: 13px;
    color: #111827;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.metric-subtitle {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.metric-value {
    font-size: 48px;
    font-weight: 800;
    color: #111827;
    line-height: 1;
    text-align: right;
    min-width: auto;
    padding: 0;
    background: transparent;
    box-shadow: none;
    border-radius: 0;
    letter-spacing: -1px;
}

.metric-value::after {
    display: none;
}

.metric-value.green {
    background: transparent;
    color: #111827;
}

.metric-value.orange {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}


.metric-controls {
    display: flex;
    gap: 10px;
}



/* ================= USER MANAGEMENT TABLE ================= */

.user-management-section {
    margin-top: 0;
    background: transparent;
    border-radius: 0;
    box-shadow: none;
}

.ums-filter-bar {
    background: #f9fafb;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
}

.filter-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.filter-label {
    color: #374151;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-dropdown-wrapper {
    position: relative;
}

.filter-dropdown-btn {
    padding: 8px 15px;
    border: 1px solid rgba(255,255,255,0.2);
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
    box-shadow: 0 2px 8px rgba(255,140,66,0.2);
}

.filter-arrow {
    transition: transform 0.3s ease;
    flex-shrink: 0;
    color: #333;
}

.filter-arrow.rotated {
    transform: rotate(180deg);
}

.filter-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    min-width: 150px;
    z-index: 1000;
    margin-top: 4px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.filter-dropdown-menu button {
    display: block;
    width: 100%;
    padding: 10px 14px;
    background: none;
    border: none;
    text-align: left;
    color: #374151;
    cursor: pointer;
    transition: background 0.15s;
    font-weight: 500;
    font-size: 12px;
}

.filter-dropdown-menu button:hover {
    background: #f9fafb;
}

.filter-dropdown-menu button.active {
    background: #fff7ef;
    color: #ff8c42;
    font-weight: 600;
}

.filter-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-container {
    display: flex;
    align-items: center;
    gap: 0;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #d1d5db;
    transition: all 0.2s;
}

.search-container:focus-within {
    border-color: #ff8c42;
    box-shadow: 0 0 0 3px rgba(255, 140, 66, 0.1);
}

.search-input {
    padding: 8px 14px;
    border: none;
    width: 250px;
    font-size: 13px;
    background: transparent;
    outline: none;
    color: #111827;
}

.search-input::placeholder {
    color: #9ca3af;
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

.ums-table-wrapper {
    overflow-x: auto;
    background: white;
}

.ums-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1400px;
}

.ums-table thead {
    background: #f9fafb;
}

.ums-table th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 11px;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ums-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #111827;
}

.ums-table tbody tr {
    transition: background-color 0.15s ease;
}

.ums-table tbody tr:hover {
    background: #f9fafb;
}

.ums-avatar {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #f3f4f6;
}

.role-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.role-badge.role-resident {
    background: #10b981;
}

.role-badge.role-official {
    background: #ff8c42;
}

.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-action {
    padding: 8px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    color: #6b7280;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Restrict Button (Gray) */
.btn-restrict {
    background: #f3f4f6;
    color: #6b7280;
}
.btn-restrict:hover {
    background: #e5e7eb;
    color: #374151;
}

/* Change Password Button (Blue) */
.btn-password {
    background: #eff6ff;
    color: #2563eb;
}
.btn-password:hover {
    background: #dbeafe;
    color: #1d4ed8;
}

/* Delete Button */
.btn-delete {
    background: #fef2f2;
    color: #dc2626;
}
.btn-delete:hover {
    background: #fee2e2;
    color: #b91c1c;
}

.no-users-message {
    padding: 60px 40px;
    text-align: center;
    color: #6b7280;
}

.no-users-message p {
    font-size: 14px;
    color: #9ca3af;
}

/* ================= MODAL STYLES ================= */
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

.modal-container {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 550px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #e0e0e0;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #333;
}

.modal-close {
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #e0e0e0;
    color: #333;
}

.modal-body {
    padding: 25px;
}

.user-info-box {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}

.modal-user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
}

.modal-user-details h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.modal-user-details p {
    margin: 0;
    font-size: 14px;
    color: #666;
}

.user-meta {
    margin-top: 5px !important;
    font-size: 12px !important;
    color: #999 !important;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
    color: #333;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #239640;
    box-shadow: 0 0 0 3px rgba(35, 150, 64, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.password-input-wrapper {
    position: relative;
}

.toggle-password-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    padding: 5px;
}

.input-hint {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
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
    cursor: pointer;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    transition: all 0.2s;
}

.checkbox-label:hover {
    background: #f8f9fa;
    border-color: #239640;
}

.checkbox-input {
    margin-top: 2px;
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #239640;
}

.checkbox-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.checkbox-text strong {
    font-size: 14px;
    color: #333;
}

.checkbox-text small {
    font-size: 12px;
    color: #666;
}

.error-message {
    padding: 10px;
    background: #fee;
    border: 1px solid #fcc;
    border-radius: 6px;
    color: #c33;
    font-size: 14px;
    margin-top: 10px;
}

.delete-warning {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: #fff3cd;
    border-radius: 8px;
    margin-bottom: 20px;
}

.delete-warning h4 {
    margin: 0;
    color: #856404;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.4;
}

.delete-notice {
    padding: 15px;
    background: #f8f9fa;
    border-left: 4px solid #dc3545;
    border-radius: 4px;
    margin-top: 15px;
}

.delete-notice p {
    margin: 5px 0;
    font-size: 14px;
    color: #666;
}

.delete-notice strong {
    color: #dc3545;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px 25px;
    border-top: 1px solid #e0e0e0;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

.btn-cancel,
.btn-apply,
.btn-delete-confirm {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #5a6268;
}

.btn-apply {
    background: #239640;
    color: white;
}

.btn-apply:hover {
    background: #1e7e34;
}

.btn-delete-confirm {
    background: #dc3545;
    color: white;
}

.btn-delete-confirm:hover {
    background: #c82333;
}
</style>