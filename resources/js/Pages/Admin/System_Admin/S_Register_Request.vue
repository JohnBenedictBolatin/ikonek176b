<template>
    <Head>
        <title>Register Requests</title>
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
                        @click="navigateToDashboard"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        Dashboard
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
                        href="#" 
                        class="nav-item active"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Register Requests
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
                    <Link 
                        href="#" 
                        class="nav-item"
                        @click="navigateToReport"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Reports & Messages
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
                            <h2>Register Requests</h2>
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
                                <button class="search-btn" @click="performSearch">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Requests Container -->
                    <div class="requests-container">
                        <div 
                            v-for="(request, index) in paginatedRequests" 
                            :key="request.id || index"
                            class="request-card"
                        >
                            <div class="request-content">
                                <div class="request-left">
                                    <img :src="request.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" />
                                    <div class="request-info">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                            <h3 class="request-name">{{ request.name }}</h3>
                                        </div>
                                        <p class="request-role-type"><span class="role-highlight" :class="request.role.toLowerCase()">{{ request.role.toUpperCase() }}</span></p>
                                        <p class="request-date-small">{{ request.date }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <p class="request-date">{{ request.date }}</p>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button 
                                            @click.stop="openModal(request)" 
                                            class="view-btn" 
                                            type="button"
                                        >
                                            VIEW DETAILS
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No requests message -->
                        <div v-if="filteredRequests.length === 0" class="no-requests" style="grid-column: 1 / -1;">
                            <p>No registration requests found matching your criteria.</p>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div v-if="filteredRequests.length > 0" class="pagination-container">
                        <div class="pagination-info">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredRequests.length) }} of {{ filteredRequests.length }} requests
                        </div>
                        <div class="pagination-controls">
                            <button 
                                class="pagination-btn" 
                                :disabled="currentPage === 1"
                                @click="prevPage"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            
                            <div class="pagination-numbers">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    class="pagination-number"
                                    :class="{ active: currentPage === page }"
                                    @click="goToPage(page)"
                                >
                                    {{ page }}
                                </button>
                            </div>
                            
                            <button 
                                class="pagination-btn" 
                                :disabled="currentPage === totalPages"
                                @click="nextPage"
                            >
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Popup -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div v-if="selectedRequest" class="modal-content">
                    <!-- Top Section - Matching Document Request Design -->
                    <div class="modal-top" style="grid-template-columns: 1fr 1fr; gap: 15px; align-items: start;">
                        <div class="modal-user-section" style="display: flex; align-items: center; gap: 12px;">
                            <img :src="selectedRequest?.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" style="width: 60px; height: 60px; flex-shrink: 0;" />
                            <div style="flex: 1; min-width: 0;">
                                <h3 class="modal-name" style="font-size: 18px; margin-bottom: 4px;">{{ selectedRequest?.name || 'Unknown' }}</h3>
                                <p class="modal-label" style="font-size: 12px; margin: 0;">Registration Requests</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: row; gap: 15px; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ff8c42 0%, #ff7a28 100%); color: white; padding: 10px 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(255, 122, 40, 0.3); min-height: fit-content;">
                            <div style="flex: 1;">
                                <p style="font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; opacity: 0.9;">Registration Type</p>
                                <h3 style="font-size: 15px; font-weight: 700; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.2); line-height: 1.2;">{{ selectedRequest.role || 'Unknown' }}</h3>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.3); flex-shrink: 0;"></div>
                            <div style="flex: 0 0 auto; text-align: right;">
                                <p style="font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; opacity: 0.9;">Request Date</p>
                                <p style="font-size: 13px; font-weight: 700; margin: 0; font-family: monospace; letter-spacing: 0.8px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ selectedRequest.date }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="modal-details">
                        <!-- User & Personal Information -->
                        <div class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 20px;">User Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 8px 12px;">
                                <!-- Personal Info -->
                                <div v-if="selectedRequest.first_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">First Name:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.first_name }}</p>
                                </div>
                                <div v-if="selectedRequest.middle_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Middle Name:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.middle_name }}</p>
                                </div>
                                <div v-if="selectedRequest.last_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Last Name:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.last_name }}</p>
                                </div>
                                <div v-if="selectedRequest.suffix" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Suffix:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.suffix }}</p>
                                </div>
                                <div v-if="selectedRequest.birthdate" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Birthdate:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ formatDate(selectedRequest.birthdate) }}</p>
                                </div>
                                <div v-if="selectedRequest.age" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Age:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.age }}</p>
                                </div>
                                <div v-if="selectedRequest.sex" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Sex:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.sex }}</p>
                                </div>
                                <div v-if="selectedRequest.civilStatus" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Civil Status:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.civilStatus }}</p>
                                </div>
                                <div v-if="selectedRequest.place_of_birth" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Place of Birth:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.place_of_birth }}</p>
                                </div>
                                <div v-if="selectedRequest.religion" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Religion:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.religion }}</p>
                                </div>
                                <div v-if="selectedRequest.nationality" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Nationality:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.nationality }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div v-if="selectedRequest.contact || selectedRequest.secondary_contact || selectedRequest.occupation || selectedRequest.address || selectedRequest.phase || selectedRequest.package || selectedRequest.yearsInBarangay" class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 20px;">Additional Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 8px 12px;">
                                <!-- Contact Info -->
                                <div v-if="selectedRequest.contact" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Contact:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.contact }}</p>
                                </div>
                                <div v-if="selectedRequest.secondary_contact" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Secondary Contact:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.secondary_contact }}</p>
                                </div>
                                <div v-if="selectedRequest.occupation" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Occupation:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.occupation }}</p>
                                </div>
                                
                                <!-- Address Info (Compact) -->
                                <div v-if="selectedRequest.address" class="detail-item" style="grid-column: span 2; margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Address:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; line-height: 1.3;">
                                        {{ selectedRequest.address }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.phase || selectedRequest.package" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Phase/Package:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">
                                        {{ [selectedRequest.phase, selectedRequest.package].filter(Boolean).join(' / ') }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.yearsInBarangay" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 2px; color: #666;">Years in Barangay:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0;">{{ selectedRequest.yearsInBarangay }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Proof of Residency Section -->
                        <div class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 20px;">Proof of Residency</h4>
                            
                            <!-- Proof Type -->
                            <div v-if="selectedRequest.proof_of_residency" class="detail-item" style="margin: 0 0 15px 0; padding: 12px 15px; background: #e8f8ed; border-left: 4px solid #239640;">
                                <p class="detail-label" style="font-size: 13px; font-weight: 700; color: #239640; margin: 0 0 5px 0; text-transform: uppercase;">Type of Proof:</p>
                                <p class="detail-value" style="font-size: 15px; font-weight: 600; color: #333; margin: 0;">{{ selectedRequest.proof_of_residency }}</p>
                            </div>
                            
                            <div v-if="selectedRequest && (selectedRequest.proof || (selectedRequest.proof_raw && selectedRequest.proof_raw !== 'none'))" class="proof-viewer" style="margin-top: 10px;">
                                <!-- If the controller resolved a URL (preferred) use it -->
                                <div v-if="selectedRequest.proof" class="attachment-preview-section">
                                    <div class="image-preview-container-inline" style="width: 100%; max-height: 400px; overflow: hidden; border-radius: 8px; border: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: center; background: #f8f9fa; margin-top: 10px;">
                                        <img
                                            :src="selectedRequest.proof"
                                            alt="Proof of Residency"
                                            class="attachment-image-inline"
                                            style="max-width: 100%; max-height: 400px; border-radius: 8px; object-fit: contain; display: block; margin: 0 auto;"
                                            @error="handleImageError"
                                        />
                                    </div>
                                    <div class="attachment-actions-inline" style="margin-top: 10px; text-align: center;">
                                        <a 
                                            :href="selectedRequest.proof" 
                                            target="_blank" 
                                            rel="noopener"
                                            class="attachment-download-btn"
                                            style="display: inline-block; text-align: center; padding: 10px 20px; background: #239640; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;"
                                        >
                                            View/Download Proof
                                        </a>
                                    </div>
                                </div>

                                <!-- If controller couldn't resolve a full URL but raw value exists and is not 'none', show raw path so admin can inspect -->
                                <div v-else style="padding:15px; background:#fff7e6; border-radius:8px; border: 1px solid #ffc107;">
                                    <p style="margin:0 0 8px 0; font-weight:600; color:#856404;"><strong>Proof path:</strong> {{ selectedRequest.proof_raw }}</p>
                                    <p style="margin:0; color:#856404; font-size:13px;">(Image file could not be found at the expected location. Check your storage disk or file path.)</p>
                                </div>
                            </div>

                            <!-- No proof uploaded -->
                            <div v-else style="padding:20px; background:#f8f9fa; border-radius:8px; border: 1px dashed #ddd; text-align: center;">
                                <p style="margin:0; font-weight:600; color:#999; display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    User did not upload proof of residency.
                                </p>
                            </div>
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
                <div v-else class="modal-content" style="padding: 40px; text-align: center;">
                    <p style="font-size: 16px; color: #666;">Loading request details...</p>
                    <p style="font-size: 14px; color: #999; margin-top: 10px;">If this message persists, please refresh the page.</p>
                </div>
            </div>
        </div>


        <!-- Reject Reason Modal -->
        <div v-if="showRejectModal" class="modal-overlay" @click="closeRejectModal">
            <div class="reject-modal-container" @click.stop>
                <button @click="closeRejectModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
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
                            CANCEL
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

        <!-- Approval Success Confirmation Modal -->
        <div v-if="showApprovalSuccessModal" class="modal-overlay success-modal-overlay" @click.stop>
            <div class="success-modal" @click.stop>
                <div class="success-modal-header">
                    <div class="success-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="success-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="success-modal-title">Registration Request Approved Successfully!</h3>
                </div>
                <div class="success-modal-body">
                    <p class="success-message">
                        The registration request has been successfully approved. 
                        The user account has been created and the applicant will be notified via SMS. 
                        The request has been removed from the pending list.
                    </p>
                </div>
                <div class="success-modal-footer">
                    <button @click="closeApprovalSuccessModal" class="success-modal-btn">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Rejection Success Confirmation Modal -->
        <div v-if="showRejectionSuccessModal" class="modal-overlay success-modal-overlay" @click.stop>
            <div class="rejection-success-modal" @click.stop>
                <div class="rejection-success-modal-header">
                    <div class="rejection-success-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="rejection-success-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="rejection-success-modal-title">Registration Request Rejected Successfully!</h3>
                </div>
                <div class="rejection-success-modal-body">
                    <p class="rejection-success-message">
                        The registration request has been successfully rejected. 
                        The applicant will be notified via SMS with the rejection reason. 
                        The request has been removed from the pending list.
                    </p>
                </div>
                <div class="rejection-success-modal-footer">
                    <button @click="closeRejectionSuccessModal" class="rejection-success-modal-btn">
                        OK
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
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'

  // --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// Check authentication on mount and redirect if not authenticated
onMounted(() => {
    if (!user.value) {
        router.visit(route('login_admin'), {
            replace: true,
            preserveState: false
        })
    }
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

// Reactive data
const showSettings = ref(false)
const showTermsModal = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const isModalOpen = ref(false)
const selectedRequest = ref(null)
const showRejectModal = ref(false)
const rejectReason = ref('')
const loadingApprove = ref(false)
const loadingReject = ref(false)
const showApprovalSuccessModal = ref(false)
const showRejectionSuccessModal = ref(false)

// Debug watchers to track modal state
watch(showApprovalSuccessModal, (newVal) => {
    console.log('showApprovalSuccessModal changed to:', newVal)
    if (newVal) {
        console.log('Approval success modal should be visible now')
    }
})

watch(showRejectionSuccessModal, (newVal) => {
    console.log('showRejectionSuccessModal changed to:', newVal)
    if (newVal) {
        console.log('Rejection success modal should be visible now')
    }
})

// Handle image load errors
const handleImageError = (event) => {
  console.error('Failed to load image:', event)
  event.target.style.display = 'none'
  const parent = event.target.parentElement
  if (parent) {
    const errorMsg = document.createElement('p')
    errorMsg.textContent = 'Failed to load image'
    errorMsg.style.color = '#999'
    errorMsg.style.padding = '20px'
    errorMsg.style.textAlign = 'center'
    parent.appendChild(errorMsg)
  }
}

// update openModal to prepare request data
const openModal = (request) => {
  if (request && request.birthdate) {
    request.age = computeAgeFromBirthdate(request.birthdate)
    // ensure formatted value exists on the object
    request.birthdateFormatted = formatDate(request.birthdate)
  }
  // Set default profile image if not present
  if (!request.profileImg) {
    request.profileImg = '/assets/DEFAULT.jpg'
  }
  selectedRequest.value = request
  isModalOpen.value = true
}



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

// helper: robust date formatter -> returns MM/DD/YYYY or '' on invalid
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  // if it's already a Date object
  if (dateStr instanceof Date) {
    const d = dateStr
    if (isNaN(d.getTime())) return ''
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const dd = String(d.getDate()).padStart(2, '0')
    const yyyy = d.getFullYear()
    return `${mm}/${dd}/${yyyy}`
  }

  let s = String(dateStr).trim()
  // Some DBs include microseconds (.000000Z). JS Date may not parse that reliably in all engines.
  // Remove fractional seconds so it becomes valid ISO e.g. "2007-04-07T00:00:00Z"
  s = s.replace(/\.(\d+)Z$/, 'Z') // remove fractional seconds
  // Also handle case without trailing Z but with fractional seconds
  s = s.replace(/\.(\d+)$/, '')

  const d = new Date(s)
  if (isNaN(d.getTime())) {
    // try parse yyyy-mm-dd fallback
    const ymd = s.match(/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/)
    if (ymd) {
      const dd2 = new Date(Number(ymd[1]), Number(ymd[2]) - 1, Number(ymd[3]))
      if (!isNaN(dd2.getTime())) {
        return formatDate(dd2)
      }
    }
    return ''
  }

  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${mm}/${dd}/${yyyy}`
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
      birthdateDisplay: formatDate(bd),
      birthdateFormatted: formatDate(bd),
      // set age computed from birthdate (frontend)
      age: computedAge || r.age || '',
      sex: r.sex ?? '',
      civilStatus: r.civilStatus ?? r.civil_status ?? '',
      contact: r.contact ?? r.contact_number ?? '',
      yearsInBarangay: r.yearsInBarangay ?? r.years_in_barangay ?? '',
      phase: phase ?? '',
      package: packageVal ?? '',
      address: r.address ?? r.home_address ?? '',
      description: r.description ?? '',
      // Set default profile image
      profileImg: r.profileImg || r.profile_pic || '/assets/DEFAULT.jpg',
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

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(8)

// Paginated requests
const paginatedRequests = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredRequests.value.slice(start, end)
})

// Total pages
const totalPages = computed(() => {
  return Math.ceil(filteredRequests.value.length / itemsPerPage.value)
})

// Pagination functions
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    // Scroll to top of requests container
    const requestsContainer = document.querySelector('.requests-container')
    if (requestsContainer) {
      requestsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    goToPage(currentPage.value + 1)
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    goToPage(currentPage.value - 1)
  }
}

// Watch for filter changes to reset to page 1
watch([filterOption, searchQuery, sortOption], () => {
  currentPage.value = 1
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
    // Properly logout by calling the logout endpoint
    router.post(route('logout'), {}, {
        onSuccess: () => {
            // Clear any local storage or session storage if needed
            if (typeof window !== 'undefined') {
                localStorage.clear()
                sessionStorage.clear()
            }
            // Redirect to login page after successful logout
            router.visit(route('login_admin'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onError: () => {
            // Even if logout fails, redirect to login
            router.visit(route('login_admin'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onFinish: () => {
            // Ensure we redirect even if something goes wrong
            router.visit(route('login_admin'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        }
    })
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const closeModal = () => {
    isModalOpen.value = false
    selectedRequest.value = null
}

const approveRequest = () => {
    // Skip background check modal - background checks are automatic
    confirmApproval()
}

const rejectRequest = () => {
    showRejectModal.value = true
}

// Confirm Approval -> call backend to create user from registration request
const confirmApproval = async () => {
  if (!selectedRequest.value) return
  loadingApprove.value = true

  const id = selectedRequest.value.id
  if (!id) {
    alert('Missing request id. Cannot approve.')
    loadingApprove.value = false
    return
  }

  const payload = {
    offenses: [] // Background checks are automatic, no manual offenses needed
  }

  // build URL robustly in case route() helper signature differs
  const url = (typeof route === 'function')
    ? route('admin.register_requests.approve', id)
    : `/admin/register-requests/${encodeURIComponent(id)}/approve`

  console.log('Sending approval request:', { id, url, payload })
  
  router.post(url, payload, {
    preserveState: true,
    preserveScroll: false,
    onSuccess: (page) => {
      console.log('Approval successful, showing success modal')
      closeModal()
      // Small delay to ensure detail modal is fully closed before showing success modal
      setTimeout(() => {
        console.log('Setting showApprovalSuccessModal to true')
        showApprovalSuccessModal.value = true
        console.log('showApprovalSuccessModal value:', showApprovalSuccessModal.value)
      }, 300)
    },
    onError: (errors) => {
      console.error('Approve errors', errors)
      console.error('Error details:', JSON.stringify(errors, null, 2))
      // Check if it's an authentication error
      if (errors && (errors.message?.includes('Unauthorized') || errors.message?.includes('sign in'))) {
        alert('Your session has expired. Please sign in again.')
        router.visit(route('login_admin'), {
          replace: true,
          preserveState: false
        })
      } else {
        const errorMsg = errors?.message || errors?.error || JSON.stringify(errors) || 'Unknown error'
        alert('Error approving request: ' + errorMsg)
      }
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
        reason: rejectReason.value
    }

    router.post(route('admin.register_requests.reject', id), payload, {
        preserveState: true,
        onSuccess: () => {
            const idx = requests.value.findIndex(r => r.id === id)
            if (idx > -1) requests.value.splice(idx, 1)
            console.log('Rejection successful, showing success modal')
            closeRejectModal()
            closeModal()
            // Small delay to ensure modals are fully closed before showing success modal
            setTimeout(() => {
                console.log('Setting showRejectionSuccessModal to true')
                showRejectionSuccessModal.value = true
                console.log('showRejectionSuccessModal value:', showRejectionSuccessModal.value)
            }, 300)
        },
        onError: (errors) => {
            console.error('Reject errors', errors)
            // Check if it's an authentication error
            if (errors && (errors.message?.includes('Unauthorized') || errors.message?.includes('sign in'))) {
                alert('Your session has expired. Please sign in again.')
                router.visit(route('login_admin'), {
                    replace: true,
                    preserveState: false
                })
            } else {
                alert('Error rejecting request.')
            }
        },
        onFinish: () => { loadingReject.value = false }
    })
}



const closeRejectModal = () => {
    showRejectModal.value = false
    rejectReason.value = ''
}

const closeApprovalSuccessModal = () => {
    showApprovalSuccessModal.value = false
    // Force a full page reload to ensure fresh data after modal closes
    setTimeout(() => {
        window.location.reload()
    }, 300)
}

const closeRejectionSuccessModal = () => {
    showRejectionSuccessModal.value = false
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
    // Don't close anything if clicking on the terms modal
    if (event.target.closest('.terms-modal') || event.target.closest('.modal-overlay')) {
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

/* Requests Container */
.requests-container {
    padding: 25px 25px 10px 25px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.request-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
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
    margin: 0;
}

.request-role-type {
    font-size: 14px;
    color: #239640;
    font-weight: 600;
    margin: 3px 0;
}

.role-highlight {
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    color: white;
    display: inline-block;
}

.role-highlight.resident {
    background: #239640;
}

.role-highlight.official {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}

.request-date-small {
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
    padding: 10px 20px;
    background: #239640;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-transform: uppercase;
}

.view-btn:hover {
    background: #1e7e34;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.3);
}


.no-requests {
    padding: 60px 40px;
    text-align: center;
    color: #666;
    grid-column: 1 / -1;
}

.no-requests p {
    font-size: 16px;
    color: #999;
}

/* Pagination Styles */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: -5px;
    padding: 15px 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.pagination-info {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pagination-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
    background: #ff8c42;
    border-color: #ff8c42;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f5f5f5;
}

.pagination-numbers {
    display: flex;
    gap: 6px;
    align-items: center;
}

.pagination-number {
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-number:hover {
    background: #f8f9fa;
    border-color: #ff8c42;
    color: #ff8c42;
    transform: translateY(-1px);
}

.pagination-number.active {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    border-color: #ff8c42;
    color: #fff;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.pagination-number.active:hover {
    background: linear-gradient(135deg, #ff7a28, #ff6a18);
    transform: translateY(-1px);
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
    background: #e0e0e0;
    color: #666;
}

.modal-content {
    margin-top: 10px;
}

.modal-top {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 20px;
    align-items: start;
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

.modal-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.detail-section:first-of-type {
    margin-top: 0;
    padding-top: 0;
    border-top: none;
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #239640;
    margin: 0 0 10px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid #239640;
}

.proof-viewer {
    margin-top: 10px;
}

.attachment-preview-section {
    margin-top: 15px;
}

.image-preview-container-inline {
    width: 100%;
    max-height: 400px;
    overflow: hidden;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    margin-top: 10px;
}

.attachment-image-inline {
    max-width: 100%;
    max-height: 400px;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

.attachment-actions-inline {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    justify-content: center;
}

.attachment-download-btn {
    display: inline-block;
    text-align: center;
    padding: 10px 20px;
    background: #239640;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.attachment-download-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.4);
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px 12px;
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
    background: white;
    color: #4a4a4a;
    border: 1px solid #e0e0e0;
    padding: 12px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
}

.cancel-reject-btn:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
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
    background: #888;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb:hover,
.modal-container::-webkit-scrollbar-thumb:hover {
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

/* Approval Success Confirmation Modal Styles */
.success-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 550px;
    width: 90%;
    overflow: hidden;
    animation: slideUp 0.3s ease;
    position: relative;
    z-index: 10002;
}

/* Success modal overlay needs higher z-index than all other modals */
.success-modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    z-index: 10001 !important;
    background: rgba(0, 0, 0, 0.6) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    backdrop-filter: blur(4px) !important;
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

.success-modal-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 35px 30px 25px 30px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.success-icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.success-icon {
    width: 100px;
    height: 100px;
    color: #239640;
    background: rgba(35, 150, 64, 0.1);
    border-radius: 50%;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.2);
}

.success-modal-title {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    color: #239640;
    line-height: 1.3;
}

.success-modal-body {
    padding: 30px 35px;
    text-align: center;
    background: white;
}

.success-message {
    margin: 0;
    font-size: 16px;
    line-height: 1.7;
    color: #555;
    text-align: justify;
}

.success-modal-footer {
    padding: 25px 30px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.success-modal-btn {
    padding: 14px 50px;
    background: linear-gradient(135deg, #239640, #1e7d35);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.success-modal-btn:hover {
    background: linear-gradient(135deg, #1e7d35, #166529);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(35, 150, 64, 0.4);
}

.success-modal-btn:active {
    transform: translateY(0);
}

/* Rejection Success Confirmation Modal Styles */
.rejection-success-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 550px;
    width: 90%;
    overflow: hidden;
    animation: slideUp 0.3s ease;
    position: relative;
    z-index: 10002;
}

.rejection-success-modal-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 35px 30px 25px 30px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.rejection-success-icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.rejection-success-icon {
    width: 100px;
    height: 100px;
    color: #dc2626;
    background: rgba(220, 38, 38, 0.1);
    border-radius: 50%;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
}

.rejection-success-modal-title {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    color: #dc2626;
    line-height: 1.3;
}

.rejection-success-modal-body {
    padding: 30px 35px;
    text-align: center;
    background: white;
}

.rejection-success-message {
    margin: 0;
    font-size: 16px;
    line-height: 1.7;
    color: #555;
    text-align: justify;
}

.rejection-success-modal-footer {
    padding: 25px 30px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.rejection-success-modal-btn {
    padding: 14px 50px;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.rejection-success-modal-btn:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
}

.rejection-success-modal-btn:active {
    transform: translateY(0);
}
</style>