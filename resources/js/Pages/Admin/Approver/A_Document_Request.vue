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
                            v-for="(request, index) in filteredRequests" 
                            :key="request.doc_request_id || request.referenceCode || index"
                            class="request-card"
                        >
                            <div class="request-content">
                                <div class="request-left">
                                    <img :src="request.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" />
                                    <div class="request-info">
                                        <h3 class="request-name">{{ request.name }}</h3>
                                        <p class="request-doc-type">{{ request.documentType }}</p>
                                        <p class="request-ref-code">{{ request.referenceCode }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <p class="request-date">{{ request.date }}</p>
                                    <button 
                                        @click.stop="viewRequestDetails(request)" 
                                        class="view-btn" 
                                        type="button"
                                    >
                                        View Request Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- No requests message -->
                        <div v-if="filteredRequests.length === 0" class="no-requests" style="grid-column: 1 / -1;">
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
                <button @click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div v-if="selectedRequest" class="modal-content">
                        <!-- Top Section -->
                        <div class="modal-top" style="grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
                            <div class="modal-user-section" style="display: flex; align-items: center; gap: 20px;">
                                <img :src="selectedRequest?.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" style="width: 80px; height: 80px; flex-shrink: 0;" />
                                <div style="flex: 1; min-width: 0;">
                                    <h3 class="modal-name" style="font-size: 24px; margin-bottom: 6px; font-weight: 700; color: #1a1a1a;">{{ selectedRequest?.name || 'Unknown' }}</h3>
                                    <p class="modal-label" style="font-size: 15px; margin: 0; color: #6b7280; font-weight: 500;">Document Request</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: row; gap: 20px; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ff8c42 0%, #ff7a28 100%); color: white; padding: 20px 24px; border-radius: 16px; box-shadow: 0 4px 16px rgba(255, 122, 40, 0.25); min-height: fit-content;">
                                <div style="flex: 1;">
                                    <p style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0; opacity: 0.95;">Document Type</p>
                                    <h3 style="font-size: 20px; font-weight: 700; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.2); line-height: 1.3;">{{ selectedRequest.documentType || 'Unknown Document Type' }}</h3>
                                </div>
                                <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.3); flex-shrink: 0;"></div>
                                <div style="flex: 0 0 auto; text-align: right;">
                                    <p style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0; opacity: 0.95;">Request Number</p>
                                    <p style="font-size: 18px; font-weight: 700; margin: 0; font-family: 'SF Mono', 'Monaco', 'Cascadia Code', monospace; letter-spacing: 0.5px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ selectedRequest.referenceCode }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="modal-details">
                        <!-- Request Information - Compact -->
                        <div class="detail-section" style="margin-bottom: 24px;">
                            <h4 class="section-title" style="margin-bottom: 16px; font-size: 20px;">Request Information</h4>
                            <div class="details-grid" style="grid-template-columns: auto 1fr; gap: 16px 20px; align-items: start;">
                                <div v-if="getRequestCategory(selectedRequest)" class="detail-item" style="margin: 0; min-width: 140px;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px;">Category:</p>
                                    <p class="detail-value" style="font-size: 16px; font-weight: 600; color: #239640; margin: 0;">
                                        {{ getRequestCategory(selectedRequest) }}
                                    </p>
                                </div>
                                <div v-if="getRequestPurpose(selectedRequest)" class="detail-item" style="margin: 0; flex: 1;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px;">Purpose:</p>
                                    <p class="detail-value" style="font-size: 16px; margin: 0; line-height: 1.6; text-align: left; word-wrap: break-word; color: #1a1a1a;">{{ getRequestPurpose(selectedRequest) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- User & Personal Information - Merged -->
                        <div class="detail-section" style="margin-bottom: 24px;">
                            <h4 class="section-title" style="margin-bottom: 16px; font-size: 20px;">User Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 12px 16px;">
                                <!-- Personal Info -->
                                <div v-if="selectedRequest.first_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">First Name:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.first_name }}</p>
                                </div>
                                <div v-if="selectedRequest.middle_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Middle Name:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.middle_name }}</p>
                                </div>
                                <div v-if="selectedRequest.last_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Last Name:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.last_name }}</p>
                                </div>
                                <div v-if="selectedRequest.suffix" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Suffix:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.suffix }}</p>
                                </div>
                                <div v-if="selectedRequest.birthdate" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Birthdate:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.birthdate }}</p>
                                </div>
                                <div v-if="selectedRequest.age" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Age:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.age }}</p>
                                </div>
                                <div v-if="selectedRequest.sex" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Sex:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.sex }}</p>
                                </div>
                                <div v-if="selectedRequest.civilStatus" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Civil Status:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.civilStatus }}</p>
                                </div>
                                
                                <!-- Contact Info -->
                                <div v-if="selectedRequest.credential_info?.contact_number || selectedRequest.contact" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Contact:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.credential_info?.contact_number || selectedRequest.contact }}</p>
                                </div>
                                <div v-if="selectedRequest.credential_info?.secondary_contact_number" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Secondary:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.credential_info.secondary_contact_number }}</p>
                                </div>
                                <div v-if="selectedRequest.user_info?.email" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Email:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedRequest.user_info.email }}</p>
                                </div>
                                
                                <!-- Address Info (Compact) -->
                                <div v-if="selectedRequest.user_info?.house_number || selectedRequest.user_info?.street" class="detail-item" style="grid-column: span 2; margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Address:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; line-height: 1.5; color: #1a1a1a;">
                                        {{ [selectedRequest.user_info?.house_number, selectedRequest.user_info?.street].filter(Boolean).join(' ') }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.user_info?.phase || selectedRequest.user_info?.package" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Phase/Package:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">
                                        {{ [selectedRequest.user_info?.phase, selectedRequest.user_info?.package].filter(Boolean).join(' / ') }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.user_info?.barangay || selectedRequest.user_info?.city" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Location:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">
                                        {{ [selectedRequest.user_info?.barangay, selectedRequest.user_info?.city].filter(Boolean).join(', ') }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.user_info?.province || selectedRequest.user_info?.zip_code" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Province/Zip:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">
                                        {{ [selectedRequest.user_info?.province, selectedRequest.user_info?.zip_code].filter(Boolean).join(' ') }}
                                    </p>
                                </div>
                                
                            </div>
                        </div>

                        <!-- Extra Fields (Dynamic Fields) -->
                        <div v-if="(selectedRequest.extra_fields && getExtraFieldsCount(selectedRequest.extra_fields) > 0) || selectedRequest.valid_id_type || selectedRequest.valid_id_number" class="detail-section" style="margin-bottom: 24px;">
                            <h4 class="section-title" style="margin-bottom: 16px; font-size: 20px;">Additional Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 12px 16px;">
                                <!-- ID Type and ID Number -->
                                <div v-if="selectedRequest.valid_id_type" class="detail-item" style="margin: 0; padding: 12px 14px;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">ID Type:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; line-height: 1.5; color: #1a1a1a;">{{ getValidIdTypeName(selectedRequest.valid_id_type) }}</p>
                                </div>
                                <div v-if="selectedRequest.valid_id_number" class="detail-item" style="margin: 0; padding: 12px 14px;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">ID Number:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; line-height: 1.5; color: #1a1a1a;">{{ selectedRequest.valid_id_number }}</p>
                                </div>
                                
                                <!-- Dynamic Extra Fields -->
                                <template v-for="(value, key) in getExtraFieldsObject(selectedRequest.extra_fields)" :key="key">
                                    <div class="detail-item" v-if="isValidExtraFieldValue(value)" style="margin: 0; padding: 12px 14px;">
                                        <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">{{ formatFieldName(key) }}:</p>
                                        <p class="detail-value" style="font-size: 15px; margin: 0; line-height: 1.5; color: #1a1a1a;">
                                            <span v-if="Array.isArray(value)">{{ value.length > 0 ? value.join(', ') : 'Not provided' }}</span>
                                            <span v-else-if="typeof value === 'object' && value !== null && !(value instanceof File)">{{ JSON.stringify(value) }}</span>
                                            <span v-else>{{ String(value) }}</span>
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Valid ID Attachments Section -->
                        <div v-if="idAttachments.length > 0 || (selectedRequest.valid_id_url || selectedRequest.has_valid_id)" class="detail-section" style="margin-bottom: 24px;">
                            <h4 class="section-title" style="margin-bottom: 16px; font-size: 20px;">Valid Identification</h4>
                            
                            <!-- ID Front and Back Attachments -->
                            <template v-if="idAttachments.length > 0">
                                <div style="margin-top: 10px;">
                                    <div class="attachments-list" style="display: grid; gap: 10px;">
                                        <div 
                                            v-for="(attachment, index) in idAttachments" 
                                            :key="attachment.attachment_id || attachment.field_name || index" 
                                            class="attachment-item"
                                            style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; display: flex; flex-direction: column; gap: 8px;"
                                        >
                                            <div class="attachment-info" style="text-align: center;">
                                                <p class="attachment-label" style="font-size: 14px; font-weight: 700; color: #239640; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    {{ formatFieldName(attachment.field_name || 'Unknown') }}
                                                </p>
                                                <p class="attachment-filename" style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ attachment.file_name || 'Unnamed file' }}
                                                </p>
                                                <p v-if="attachment.file_size" class="attachment-size" style="font-size: 13px; color: #6b7280; margin: 0;">
                                                    Size: {{ formatFileSize(attachment.file_size) }}
                                                </p>
                                                
                                                <!-- Image Preview -->
                                                <div v-if="isImageFile(attachment.file_type)" class="attachment-preview-inline" style="margin-top: 10px; text-align: center;">
                                                    <img 
                                                        :src="getAttachmentUrl(attachment)" 
                                                        :alt="attachment.file_name"
                                                        class="attachment-image-inline-small"
                                                        style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: block; margin: 0 auto;"
                                                        @error="handleImageError"
                                                        @load="() => {}"
                                                    />
                                                </div>
                                                
                                                <!-- PDF Preview Link -->
                                                <div v-else-if="isPdfFile(attachment.file_type)" class="attachment-preview-inline" style="margin-top: 10px; text-align: center; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
                                                    <a :href="getAttachmentUrl(attachment)" target="_blank" class="pdf-preview-link" style="display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                                        View PDF Document
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <div style="text-align: center; margin-top: 10px;">
                                                <a 
                                                    :href="getAttachmentUrl(attachment)" 
                                                    target="_blank" 
                                                    class="attachment-download-btn"
                                                    style="display: inline-block; text-align: center; padding: 12px 24px; background: #239640; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(35, 150, 64, 0.2);"
                                                >
                                                    View/Download File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Fallback: Valid ID Image Preview (if no separate attachments but has valid_id_content) -->
                            <template v-else-if="selectedRequest.valid_id_url || selectedRequest.has_valid_id">
                                <div class="attachment-preview-section" style="margin-top: 15px;">
                                    <p class="detail-label" style="font-size: 14px; font-weight: 700; color: #6b7280; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">Valid ID Image:</p>
                                    <div class="image-preview-container-inline">
                                        <img 
                                            v-if="validIdImageUrl"
                                            :src="validIdImageUrl" 
                                            alt="Valid ID" 
                                            class="attachment-image-inline"
                                            style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                            @error="handleImageError"
                                            @load="handleImageLoad"
                                        />
                                        <div v-else-if="selectedRequest.valid_id_url" class="loading-placeholder">
                                            <p style="color: #999; padding: 20px; text-align: center;">Loading valid ID image...</p>
                                        </div>
                                        <div v-else class="loading-placeholder">
                                            <p style="color: #999; padding: 20px; text-align: center;">No valid ID image available</p>
                                        </div>
                                    </div>
                                    <div class="attachment-actions-inline" style="margin-top: 10px;">
                                        <a 
                                            v-if="validIdImageUrl"
                                            :href="validIdImageUrl" 
                                            :download="`valid_id_${selectedRequest.referenceCode || 'attachment'}.jpg`" 
                                            class="attachment-download-btn"
                                            style="display: inline-block; text-align: center; padding: 12px 24px; background: #239640; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(35, 150, 64, 0.2);"
                                            target="_blank"
                                        >
                                            Download Valid ID
                                        </a>
                                        <a 
                                            v-else-if="selectedRequest.valid_id_url"
                                            :href="selectedRequest.valid_id_url" 
                                            :download="`valid_id_${selectedRequest.referenceCode || 'attachment'}.jpg`" 
                                            class="attachment-download-btn"
                                            style="display: inline-block; text-align: center; padding: 12px 24px; background: #239640; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(35, 150, 64, 0.2);"
                                            target="_blank"
                                        >
                                            Download Valid ID
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                            
                        <!-- Other Attachments -->
                        <div class="detail-section" style="margin-bottom: 24px;">
                            <h4 class="section-title" style="margin-bottom: 16px; font-size: 20px;">Other Uploaded Files</h4>
                            
                            <!-- Show attachments if available (excluding ID front/back which are shown separately) -->
                            <div v-if="otherAttachments && Array.isArray(otherAttachments) && otherAttachments.length > 0" class="attachments-list" style="display: grid; gap: 10px; margin-top: 10px;">
                                <div 
                                    v-for="(attachment, index) in otherAttachments" 
                                    :key="attachment.attachment_id || attachment.field_name || index" 
                                    class="attachment-item"
                                    style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; display: flex; flex-direction: column; gap: 8px;"
                                >
                                    <div class="attachment-info" style="text-align: center;">
                                        <p class="attachment-label" style="font-size: 14px; font-weight: 700; color: #239640; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                            {{ formatFieldName(attachment.field_name || 'Unknown') }}
                                        </p>
                                        <p class="attachment-filename" style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ attachment.file_name || 'Unnamed file' }}
                                        </p>
                                        <p v-if="attachment.file_size" class="attachment-size" style="font-size: 13px; color: #6b7280; margin: 0;">
                                            Size: {{ formatFileSize(attachment.file_size) }}
                                        </p>
                                        
                                        <!-- Image Preview -->
                                        <div v-if="isImageFile(attachment.file_type)" class="attachment-preview-inline" style="margin-top: 10px; text-align: center;">
                                            <img 
                                                :src="getAttachmentUrl(attachment)" 
                                                :alt="attachment.file_name"
                                                class="attachment-image-inline-small"
                                                style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: block; margin: 0 auto;"
                                                @error="handleImageError"
                                                @load="() => {}"
                                            />
                                        </div>
                                        
                                        <!-- PDF Preview Link -->
                                        <div v-else-if="isPdfFile(attachment.file_type)" class="attachment-preview-inline" style="margin-top: 10px; text-align: center; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
                                            <a :href="getAttachmentUrl(attachment)" target="_blank" class="pdf-preview-link" style="display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                                View PDF Document
                                            </a>
                                        </div>
                                        
                                        <!-- Other file types -->
                                        <div v-else class="attachment-preview-inline" style="margin-top: 10px; text-align: center; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
                                            <p style="color: #666; margin: 0;">File type: {{ attachment.file_type || 'Unknown' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div style="text-align: center; margin-top: 10px;">
                                        <a 
                                            :href="getAttachmentUrl(attachment)" 
                                            target="_blank" 
                                            class="attachment-download-btn"
                                            style="display: inline-block; text-align: center; padding: 12px 24px; background: #239640; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(35, 150, 64, 0.2);"
                                                >
                                                    View/Download File
                                                </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Show message if no attachments -->
                            <div v-else class="no-attachments-message" style="padding: 30px; text-align: center; background: #f8f9fa; border-radius: 10px; border: 1px dashed #ddd;">
                                <p class="detail-value" style="color: #9ca3af; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px; justify-content: center;">
                                    <span v-if="!selectedRequest.attachments" style="display: flex; align-items: center; gap: 6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        No attachments data available.
                                    </span>
                                    <span v-else-if="!Array.isArray(selectedRequest.attachments)" style="display: flex; align-items: center; gap: 6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Attachments data format error (not an array).
                                    </span>
                                    <span v-else style="display: flex; align-items: center; gap: 6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        No additional attachments uploaded for this request.
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Admin Feedback/Rejection Reason (if already reviewed) -->
                        <div v-if="selectedRequest.admin_feedback" class="detail-section">
                            <h4 class="section-title">Admin Feedback</h4>
                            <div class="detail-item-full">
                                <p class="detail-value">{{ selectedRequest.admin_feedback }}</p>
                            </div>
                        </div>

                        <!-- Review Status (if already reviewed) -->
                        <div v-if="selectedRequest.status !== 'Pending'" class="detail-section">
                            <h4 class="section-title">Review Status</h4>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <p class="detail-label">Status:</p>
                                    <p class="detail-value" :style="{ color: selectedRequest.status === 'Approved' ? '#239640' : '#ef4444' }">
                                        {{ selectedRequest.status }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.reviewed_at" class="detail-item">
                                    <p class="detail-label">Reviewed At:</p>
                                    <p class="detail-value">{{ formatDate(selectedRequest.reviewed_at) }}</p>
                                </div>
                                <div v-if="selectedRequest.fk_approver_id" class="detail-item">
                                    <p class="detail-label">Reviewed By:</p>
                                    <p class="detail-value">Approver ID: {{ selectedRequest.fk_approver_id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons (only show if pending) -->
                        <div v-if="selectedRequest.status === 'Pending'" class="modal-actions">
                            <button @click.stop="openApprovalModal" class="approve-btn" type="button">
                                Approve
                            </button>
                            <button @click.stop.prevent="handleRejectClick" class="reject-btn" type="button">
                                Reject
                            </button>
                        </div>
                        <div v-else class="modal-actions">
                            <button @click="closeModal" class="cancel-btn" style="width: 100%;">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="modal-content" style="padding: 40px; text-align: center;">
                    <p style="font-size: 18px; color: #6b7280; font-weight: 500;">Loading request details...</p>
                    <p style="font-size: 15px; color: #9ca3af; margin-top: 12px;">If this message persists, please refresh the page.</p>
                </div>
            </div>
        </div>

        <!-- Approval Modal (Pickup Details) -->
        <div v-if="isApprovalModalOpen" class="modal-overlay" @click="closeApprovalModal">
            <div class="modal-container approval-modal" @click.stop>
                <button @click="closeApprovalModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

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
                        readonly
                        disabled
                        style="background-color: #f5f5f5; cursor: not-allowed;"
                    />
                    </div>

                    <div class="form-group">
                    <label class="form-label">Pickup Location</label>
                    <select
                        v-model="pickupLocation"
                        class="form-input"
                        @change="handleLocationChange"
                        required
                        style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\' stroke-width=\'2\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'m19.5 8.25-7.5 7.5-7.5-7.5\' /%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px;"
                    >
                        <option value="" disabled>Select pickup location</option>
                        <option value="Barangay Hall - Records Office">Barangay Hall - Records Office</option>
                        <option value="Barangay Hall - Main Office">Barangay Hall - Main Office</option>
                        <option value="Barangay Hall - Front Desk">Barangay Hall - Front Desk</option>
                        <option value="Barangay Hall - Treasurer's Office">Barangay Hall - Treasurer's Office</option>
                        <option value="Barangay Hall - Secretary's Office">Barangay Hall - Secretary's Office</option>
                        <option value="other">Other (specify)</option>
                    </select>
                    <input
                        v-if="pickupLocation === 'other'"
                        type="text"
                        v-model="customPickupLocation"
                        class="form-input"
                        placeholder="Enter custom pickup location"
                        required
                        style="margin-top: 10px;"
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

        <!-- Rejection Modal - Using Teleport to render at body level -->
        <Teleport to="body">
            <div 
                v-if="isRejectionModalOpen"
                class="modal-overlay rejection-modal-overlay" 
                @click="closeRejectionModal"
                style="position: fixed !important; inset: 0 !important; background: rgba(0, 0, 0, 0.6) !important; display: flex !important; align-items: center !important; justify-content: center !important; z-index: 99999 !important; visibility: visible !important; opacity: 1 !important;"
            >
            <div class="modal-container rejection-modal" @click.stop style="background: white !important; border-radius: 20px; padding: 30px; width: 90%; max-width: 600px; position: relative !important; z-index: 100000 !important;">
                <button @click="closeRejectionModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="modal-content">
                    <h2 class="rejection-title">Reason for Rejection</h2>
                    <p class="rejection-subtitle">Please provide a clear reason for rejecting this assistance request</p>

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
                            >
                                {{ isRejecting ? 'Processing…' : 'Confirm Rejection' }}
                            </button>
                            <button @click="closeRejectionModal" class="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </Teleport>

        <!-- Frontend-generated Document Modal -->
        <!-- Attachment Viewer Modal -->
        <div v-if="isAttachmentModalOpen" class="modal-overlay" @click="closeAttachmentModal">
            <div class="modal-container attachment-modal" @click.stop style="max-width: 90vw; max-height: 90vh; overflow-y: auto;">
                <button @click="closeAttachmentModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="modal-content">
                    <h2 class="document-title">Uploaded Attachments</h2>
                    
                    <!-- Valid ID Information -->
                    <div v-if="selectedRequestForAttachments" class="attachment-section">
                        <h3 class="section-title" style="margin-top: 0;">Valid Identification</h3>
                        <div class="details-grid">
                            <div v-if="selectedRequestForAttachments.valid_id_type" class="detail-item">
                                <p class="detail-label">ID Type:</p>
                                <p class="detail-value">{{ getValidIdTypeName(selectedRequestForAttachments.valid_id_type) }}</p>
                            </div>
                            <div v-if="selectedRequestForAttachments.valid_id_number" class="detail-item">
                                <p class="detail-label">ID Number:</p>
                                <p class="detail-value">{{ selectedRequestForAttachments.valid_id_number }}</p>
                            </div>
                        </div>
                        
                        <!-- Valid ID Content Display -->
                        <div v-if="validIdImageUrl" class="attachment-preview" style="margin-top: 15px;">
                            <h4 class="attachment-label">Valid ID Image:</h4>
                            <div class="image-preview-container">
                                <img 
                                    :src="validIdImageUrl" 
                                    alt="Valid ID" 
                                    class="attachment-image"
                                    @error="handleImageError"
                                />
                            </div>
                            <div class="attachment-actions" style="margin-top: 10px;">
                                <a :href="validIdImageUrl" :download="`valid_id_${selectedRequestForAttachments.referenceCode || 'attachment'}.jpg`" class="download-btn">
                                    Download Valid ID
                                </a>
                            </div>
                        </div>
                        <div v-else-if="selectedRequestForAttachments.has_valid_id" class="attachment-preview" style="margin-top: 15px;">
                            <p class="detail-value" style="color: #999;">Loading valid ID...</p>
                        </div>
                    </div>

                    <!-- Other Attachments -->
                    <div v-if="selectedRequestForAttachments && selectedRequestForAttachments.attachments && selectedRequestForAttachments.attachments.length > 0" class="attachment-section" style="margin-top: 30px;">
                        <h3 class="section-title">Other Uploaded Files</h3>
                        <div class="attachments-grid">
                            <div v-for="attachment in selectedRequestForAttachments.attachments" :key="attachment.attachment_id || attachment.field_name" class="attachment-card">
                                <div class="attachment-header">
                                    <h4 class="attachment-label">{{ formatFieldName(attachment.field_name) }}</h4>
                                    <p class="attachment-filename">{{ attachment.file_name || 'Unnamed file' }}</p>
                                    <p v-if="attachment.file_size" class="attachment-size">{{ formatFileSize(attachment.file_size) }}</p>
                                </div>
                                
                                <!-- Image Preview -->
                                <div v-if="isImageFile(attachment.file_type)" class="attachment-preview">
                                    <img 
                                        :src="getAttachmentUrl(attachment)" 
                                        :alt="attachment.file_name"
                                        class="attachment-image"
                                        @error="handleImageError"
                                    />
                                </div>
                                
                                <!-- PDF Preview -->
                                <div v-else-if="isPdfFile(attachment.file_type)" class="attachment-preview">
                                    <iframe 
                                        :src="getAttachmentUrl(attachment)" 
                                        class="attachment-iframe"
                                        style="width: 100%; height: 400px; border: 1px solid #ddd;"
                                    ></iframe>
                                </div>
                                
                                <!-- Download Button -->
                                <div class="attachment-actions">
                                    <a 
                                        :href="getAttachmentUrl(attachment)" 
                                        target="_blank" 
                                        class="download-btn"
                                    >
                                        View/Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else-if="selectedRequestForAttachments && (!selectedRequestForAttachments.attachments || selectedRequestForAttachments.attachments.length === 0)" class="attachment-section" style="margin-top: 30px;">
                        <p class="detail-value" style="color: #999;">No additional attachments uploaded.</p>
                    </div>

                    <div class="document-actions" style="margin-top: 20px;">
                        <button @click="closeAttachmentModal" class="close-btn">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generated Document Modal (for other uses) -->
        <div v-if="isDocumentModalOpen" class="modal-overlay" @click="closeFrontendDocumentModal">
            <div class="modal-container document-modal" @click.stop>
                <button @click="closeFrontendDocumentModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="modal-content">
                <h2 class="document-title">Generated Document</h2>
                <p v-if="generatedDocumentFilename" class="document-filename">{{ generatedDocumentFilename }}</p>

                <div class="document-preview" style="min-height:60vh;">
                    <iframe
                    id="doc-preview-iframe"
                    v-if="generatedDocumentObjectUrl || generatedDocumentUrl"
                    :src="generatedDocumentObjectUrl || generatedDocumentUrl"
                    style="width:100%; height:60vh; border:0;"
                    @load="handleIframeLoad"
                    @error="handleIframeError"
                    ></iframe>

                    <div v-if="iframeError" class="no-document" style="padding: 40px; text-align: center;">
                        <p style="color: #dc3545; margin-bottom: 20px;">Failed to load PDF document in preview.</p>
                        <a 
                            :href="generatedDocumentObjectUrl || generatedDocumentUrl" 
                            target="_blank" 
                            class="download-btn"
                            style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 6px;"
                        >
                            Open PDF in New Tab
                        </a>
                    </div>

                    <div v-else-if="!(generatedDocumentObjectUrl || generatedDocumentUrl)" class="no-document">
                    <p>No generated document available yet.</p>
                    </div>
                </div>

                <div class="document-actions" style="margin-top:12px; display:flex; gap:8px;">
                    <a
                    v-if="generatedDocumentObjectUrl || generatedDocumentUrl"
                    :href="generatedDocumentObjectUrl || generatedDocumentUrl"
                    :download="generatedDocumentFilename || ''"
                    class="download-btn"
                    >
                    Download
                    </a>

                    <button
                    v-if="generatedDocumentObjectUrl || generatedDocumentUrl"
                    @click="printGeneratedDocument"
                    class="print-btn"
                    >
                    Print / Save as PDF
                    </button>

                    <button @click="closeFrontendDocumentModal" class="close-btn">Close</button>
                </div>
                </div>
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
import axios from 'axios'

// --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

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

// Helper function to get attachment URL consistently (like profile pictures)
const getAttachmentUrl = (attachment) => {
  if (!attachment) return null
  
  // Prefer file_url if available (from accessor)
  if (attachment.file_url) {
    const url = attachment.file_url
    // If it's already a full URL, return as is
    if (url.startsWith('http://') || url.startsWith('https://')) {
      return url
    }
    // If it already has /storage/, return as is
    if (url.startsWith('/storage/')) {
      return url
    }
    // Otherwise prepend /storage/
    return `/storage/${url}`
  }
  
  // Fallback to file_path
  if (attachment.file_path) {
    const path = attachment.file_path
    // If it's already a full URL, return as is
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path
    }
    // If it already has /storage/, return as is
    if (path.startsWith('/storage/')) {
      return path
    }
    // Remove 'public/' prefix if present
    const cleanPath = path.startsWith('public/') ? path.substring(7) : path
    // Prepend /storage/
    return `/storage/${cleanPath}`
  }
  
  return null
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
const isAttachmentModalOpen = ref(false) // Attachment viewer modal
const selectedRequestForAttachments = ref(null) // Request data for attachment viewer
const validIdImageUrl = ref('') // URL for valid ID image
const selectedRequest = ref(null)
const pickupDate = ref('')
const pickupTime = ref('')
const rejectionReason = ref('')

const pickupItem = ref('')
const pickupLocation = ref('')
const customPickupLocation = ref('')
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
const iframeLoaded = ref(false)              // track if iframe has loaded
const iframeError = ref(false)               // track if iframe failed to load

// computed helper to detect image mime types
const isImage = computed(() => {
  try {
    return generatedDocumentMime.value && generatedDocumentMime.value.startsWith('image/')
  } catch (e) {
    return false
  }
})

// helper: show blob in modal (reuses your cleanup helpers)
const showGeneratedDocumentFromBlob = (blob, filename = '') => {
  cleanupGeneratedDocument()
  const objUrl = URL.createObjectURL(blob)
  generatedDocumentObjectUrl.value = objUrl
  generatedDocumentFilename.value = filename || ''
  generatedDocumentMime.value = blob.type || 'application/octet-stream'
  isDocumentModalOpen.value = true
}

// Fetch and display all attachments including valid ID
// NOTE: This should ONLY be called when user explicitly clicks "View Upload" button
// This function opens the attachment modal WITHOUT closing the main request details modal
const viewValidId = async (request) => {
  try {
    // Use selectedRequest if request is not provided
    const req = request || selectedRequest.value
    
    if (!req) {
      console.error('No request provided and selectedRequest is null')
      alert('No request selected. Please select a request first.')
      return
    }
    
    // Check for doc_request_id in various possible locations
    const requestId = req.doc_request_id || req.id || req.docRequestId
    if (!requestId) {
      console.error('Request missing ID:', req)
      console.error('Available keys:', Object.keys(req))
      alert('Request data is incomplete. Please refresh and try again.')
      return
    }
    
    // IMPORTANT: Do NOT close the main modal (isModalOpen) - keep it open so user can review
    // Only close approval/rejection modals if they're open
    isApprovalModalOpen.value = false
    isRejectionModalOpen.value = false
    isDocumentModalOpen.value = false
    
    // Set the request data for the attachment viewer
    selectedRequestForAttachments.value = { ...req }
    validIdImageUrl.value = ''
    
    const id = requestId

    // Fetch valid ID image if available
    if (req.has_valid_id || req.valid_id_url) {
      try {
        // build route (try Ziggy route() if present, otherwise fallback)
        let url = ''
        try {
          if (typeof route === 'function') {
            url = route('document_requests.valid_id', { id })
          } else {
            url = `/document-requests/${id}/valid-id`
          }
        } catch (e) {
          url = `/document-requests/${id}/valid-id`
        }

        const res = await axios.get(url, { responseType: 'blob' })
        const blob = res.data
        const objUrl = URL.createObjectURL(blob)
        validIdImageUrl.value = objUrl
      } catch (err) {
        console.error('Failed to fetch valid id:', err)
        // Don't show alert, just continue without valid ID image
      }
    }
    
    // Open attachment modal - use nextTick to ensure DOM updates
    await nextTick()
    isAttachmentModalOpen.value = true
  } catch (error) {
    console.error('Error in viewValidId:', error)
    alert('An error occurred while opening attachments. Please check the console for details.')
  }
}

// Close attachment modal and cleanup
// This keeps the main request details modal open
const closeAttachmentModal = () => {
  isAttachmentModalOpen.value = false
  selectedRequestForAttachments.value = null
  // Cleanup valid ID image URL
  if (validIdImageUrl.value) {
    try {
      URL.revokeObjectURL(validIdImageUrl.value)
    } catch (e) {
      // Ignore cleanup errors
    }
    validIdImageUrl.value = ''
  }
  // Note: We intentionally do NOT close isModalOpen here
  // so the user can continue reviewing the request details
}

// Helper to get valid ID type name
const getValidIdTypeName = (typeId) => {
  if (!typeId) return 'Not specified'
  // Map of common valid ID types (adjust based on your database)
  const idTypes = {
    1: 'National ID',
    2: "Driver's License",
    3: 'Passport',
    4: "Voter's ID",
    5: 'SSS ID',
    6: 'UMID',
    7: 'PhilHealth ID',
    8: 'TIN ID',
  }
  return idTypes[typeId] || `ID Type ${typeId}`
}

// Helper to check if file is an image
const isImageFile = (mimeType) => {
  if (!mimeType) return false
  return mimeType.startsWith('image/')
}

// Helper to check if file is a PDF
const isPdfFile = (mimeType) => {
  if (!mimeType) return false
  return mimeType === 'application/pdf' || mimeType === 'application/x-pdf'
}

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

// Handle successful image load
const handleImageLoad = (event) => {
  // Image loaded successfully
}

const pageProps = page?.props ?? {}

// Grab document requests from Inertia props
const serverRequests = computed(() => {
  return page?.props?.value?.document_requests ?? page?.props?.document_requests ?? []
})

// Helper to get request category (reason_type) from various possible locations
const getRequestCategory = (request) => {
  if (!request) return null
  
  // Priority 1: Check direct property on request object
  if (request.reason_type && String(request.reason_type).trim() !== '') {
    return String(request.reason_type).trim()
  }
  
  // Priority 2: Check original object (server response)
  if (request.original) {
    if (request.original.reason_type && String(request.original.reason_type).trim() !== '') {
      return String(request.original.reason_type).trim()
    }
  }
  
  // Priority 3: Check if it's nested in user_info or other nested objects (unlikely but safe)
  // This shouldn't be necessary, but checking for completeness
  
  return null
}

// Helper to get request purpose from various possible locations
const getRequestPurpose = (request) => {
  if (!request) return null
  // Check direct properties first
  if (request.purpose) return request.purpose
  if (request.description) return request.description
  // Check original object
  if (request.original?.purpose) return request.original.purpose
  if (request.original?.description) return request.original.description
  return null
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    if (Number.isNaN(d.getTime())) return dateStr
    
    // Use Intl.DateTimeFormat to format in Philippines timezone
    const formatter = new Intl.DateTimeFormat('en-US', {
      timeZone: 'Asia/Manila',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
    
    const parts = formatter.formatToParts(d)
    const mm = parts.find(p => p.type === 'month').value
    const dd = parts.find(p => p.type === 'day').value
    const yyyy = parts.find(p => p.type === 'year').value
    
    return `${mm}/${dd}/${yyyy}`
  } catch (e) {
    return dateStr || ''
  }
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

const formatFieldName = (fieldName) => {
  if (!fieldName) return ''
  
  // Special cases for ID fields
  if (fieldName === 'id_front') return 'ID Front'
  if (fieldName === 'id_back') return 'ID Back'
  
  return fieldName
    .replace(/_/g, ' ')
    .replace(/([A-Z])/g, ' $1')
    .trim()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ')
}

// Helper function to safely get extra_fields as an object
const getExtraFieldsObject = (extraFields) => {
  if (!extraFields) return {}
  
  // If it's already an object, return it
  if (typeof extraFields === 'object' && !Array.isArray(extraFields)) {
    return extraFields
  }
  
  // If it's a string, try to parse it
  if (typeof extraFields === 'string') {
    try {
      const parsed = JSON.parse(extraFields)
      return typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {}
    } catch (e) {
      console.warn('Failed to parse extra_fields string:', e)
      return {}
    }
  }
  
  return {}
}

// Helper function to count valid extra_fields
const getExtraFieldsCount = (extraFields) => {
  const obj = getExtraFieldsObject(extraFields)
  return Object.keys(obj).filter(key => {
    const value = obj[key]
    return isValidExtraFieldValue(value)
  }).length
}

// Helper function to check if a value is valid for display
const isValidExtraFieldValue = (value) => {
  if (value === null || value === undefined) return false
  if (value === '') return false
  if (Array.isArray(value) && value.length === 0) return false
  if (typeof value === 'object' && Object.keys(value).length === 0) return false
  return true
}

const formatFileSize = (bytes) => {
  if (!bytes || bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
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

  // Build profile image URL
  let profileImgUrl = '/assets/DEFAULT.jpg'
  if (r.user_info?.profile_pic) {
    const profilePic = r.user_info.profile_pic
    if (String(profilePic).startsWith('http')) {
      profileImgUrl = profilePic
    } else {
      profileImgUrl = `/storage/${profilePic}`.replace('//', '/')
    }
  } else if (uploadedFileUrl) {
    profileImgUrl = uploadedFileUrl
  }

  return {
    doc_request_id: r.doc_request_id ?? r.id ?? null,
    referenceCode: r.doc_request_ticket ?? r.doc_request_code ?? r.reference_code ?? '',
    name: fullName,
    profileImg: profileImgUrl,
    documentType: docType,
    date: formatDate(createdAt),
    dateObj: createdAt ? new Date(createdAt) : new Date(),
    purpose: r.purpose ?? '',
    description: r.purpose ?? r.description ?? '',
    reason_type: r.reason_type ?? null,
    birthdate: formatDate(r.birthdate),
    age: computeAge(r.birthdate),
    sex: r.sex ?? '',
    civilStatus: r.civil_status ?? '',
    contact: r.contact_number ?? r.contact ?? '',
    address: r.address ?? '',
    uploadedFile: r.valid_id_path ?? '',
    uploadedFileUrl,
    valid_id_type: r.valid_id_type ?? null,
    valid_id_number: r.valid_id_number ?? null,
    original: r, // Keep original for reference
    status: (r.status ?? 'Pending').trim(),
    fk_approver_id: r.fk_approver_id ?? null,
    reviewed_at: r.reviewed_at ?? null,
    admin_feedback: r.admin_feedback ?? null,
    applied_processing_fee: r.applied_processing_fee ?? null,
    pickup_item: r.pickup_item ?? null,
    pickup_location: r.pickup_location ?? null,
    pickup_start: r.pickup_start ?? null,
    pickup_end: r.pickup_end ?? null,
    person_to_look: r.person_to_look ?? null,
    created_at: createdAt,
    // Include all fields from server response
    // Parse extra_fields if it's a string, otherwise use as-is
    extra_fields: (() => {
      const fields = r.extra_fields ?? {}
      if (typeof fields === 'string') {
        try {
          return JSON.parse(fields)
        } catch (e) {
          console.warn('Failed to parse extra_fields:', e)
          return {}
        }
      }
      return fields || {}
    })(),
    attachments: r.attachments ?? [],
    user_info: r.user_info ?? {},
    credential_info: r.credential_info ?? null,
    // Ensure reason_type is explicitly included from the server response
    reason_type: r.reason_type ?? null,
    // Include additional fields that might be in the original response
    first_name: r.first_name ?? null,
    middle_name: r.middle_name ?? null,
    last_name: r.last_name ?? null,
    suffix: r.suffix ?? null,
    email: r.email ?? r.user_info?.email ?? null,
    fk_document_type_id: r.fk_document_type_id ?? null,
    document_type: r.document_type ?? null,
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
  // Only show Pending requests in the document request tab
  let filtered = localRequests.value.filter(item => 
    (item.status || 'Pending').trim() === 'Pending'
  )

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
const performSearch = () => {
  // Perform search functionality
}

// New function to view request details - simpler and more reliable
const viewRequestDetails = async (request) => {
  // Ensure document modal is closed when opening request details
  isDocumentModalOpen.value = false
  cleanupGeneratedDocument()
  
  if (!request) {
    console.error('Request is null or undefined')
    alert('Error: Request data is missing')
    return
  }
  
  // Set the selected request - create a new object to ensure reactivity
  // Parse extra_fields if needed
  let parsedExtraFields = {}
  if (request.extra_fields) {
    if (typeof request.extra_fields === 'string') {
      try {
        parsedExtraFields = JSON.parse(request.extra_fields)
      } catch (e) {
        console.warn('Failed to parse extra_fields in viewRequestDetails:', e)
        parsedExtraFields = {}
      }
    } else if (typeof request.extra_fields === 'object' && !Array.isArray(request.extra_fields)) {
      parsedExtraFields = { ...request.extra_fields }
    }
  }
  
  // Get attachments from original request object if available
  let requestAttachments = []
  
  // Try multiple sources for attachments
  if (request.attachments && Array.isArray(request.attachments) && request.attachments.length > 0) {
    // First priority: attachments from mapped request
    requestAttachments = [...request.attachments]
  } else if (request.original && request.original.attachments && Array.isArray(request.original.attachments)) {
    // Second priority: attachments from original server response
    requestAttachments = [...request.original.attachments]
  } else if (request.original?.attachments && Array.isArray(request.original.attachments)) {
    requestAttachments = [...request.original.attachments]
  } else {
    requestAttachments = []
  }
  
  selectedRequest.value = {
    ...request,
    // Ensure all nested objects are properly spread
    user_info: request.user_info ? { ...request.user_info } : {},
    credential_info: request.credential_info ? { ...request.credential_info } : null,
    extra_fields: parsedExtraFields,
    attachments: requestAttachments, // Explicitly set attachments
    // Explicitly include reason_type from request or original - check all possible locations
    reason_type: (() => {
      // Check direct property first
      if (request.reason_type && String(request.reason_type).trim() !== '') {
        return String(request.reason_type).trim()
      }
      // Check original object
      if (request.original?.reason_type && String(request.original.reason_type).trim() !== '') {
        return String(request.original.reason_type).trim()
      }
      return null
    })(),
    purpose: request.purpose ?? request.original?.purpose ?? request.description ?? null,
    // Also preserve original for debugging
    original: request.original || request,
  }
  
  // Reset form fields
  rejectionReason.value = ''
  
  // Load valid ID image if available
  validIdImageUrl.value = ''
  if (request.has_valid_id || request.valid_id_url) {
    const requestId = request.doc_request_id || request.id || request.docRequestId
    if (requestId) {
      try {
        let url = ''
        try {
          if (typeof route === 'function') {
            url = route('document_requests.valid_id', { id: requestId })
          } else {
            url = `/document-requests/${requestId}/valid-id`
          }
        } catch (e) {
          url = `/document-requests/${requestId}/valid-id`
        }
        
        const res = await axios.get(url, { responseType: 'blob' })
        const blob = res.data
        if (blob && blob.size > 0) {
          const objUrl = URL.createObjectURL(blob)
          validIdImageUrl.value = objUrl
        }
      } catch (err) {
        console.error('Failed to fetch valid id:', err)
        // Continue without valid ID image
      }
    }
  }
  
  // Open the modal
  isModalOpen.value = true
  
  // Use nextTick to ensure DOM is updated
  await nextTick()
}

// Keep old function for backward compatibility
const openModal = (request) => {
  viewRequestDetails(request)
}

const closeModal = () => {
  isModalOpen.value = false
  selectedRequest.value = null
  // Cleanup valid ID image URL when closing modal
  if (validIdImageUrl.value) {
    try {
      URL.revokeObjectURL(validIdImageUrl.value)
    } catch (e) {
      // Ignore cleanup errors
    }
    validIdImageUrl.value = ''
  }
}

// --- add this helper somewhere near your other helpers ---
// safer close helper — DOES NOT navigate to about:blank
const closeAllTabs = () => {
  try {
    // If your app keeps references to windows it opened, close them first
    if (window.openedWindows && Array.isArray(window.openedWindows)) {
      window.openedWindows.forEach(w => {
        try { w.close() } catch(e) { /* ignore */ }
      })
    }

    // Heuristic: window.opener is non-null when this window was opened via window.open()
    // Some apps also set window.name when opening a window; check that too.
    const isScriptOpened = !!(window.opener) || (window.name && window.name.startsWith('app_window_'))

    if (isScriptOpened) {
      try {
        // Attempt to close this tab/window (works only when script-opened)
        window.open('', '_self') // some browsers require this first
        window.close()
        return
      } catch (e) {
        // fall through to redirect fallback below
        console.warn('window.close() blocked or failed', e)
      }
    }

    // Not a script-opened window or close was blocked — redirect to a safe page in-app
    try {
      router.visit(route('document_request_approver'))
    } catch (e) {
      // As ultimate fallback: change location.href (should be same app)
      try { window.location.href = '/' } catch (ee) { /* ignore */ }
    }
  } catch (err) {
    console.warn('closeAllTabs: error', err)
  }
}


/**
 * Optional parameter `reopenMain` controls whether closing the smaller modal re-opens the main modal view.
 * Default `true` keeps previous behaviour for Cancel/overlay clicks. Pass `false` when you want everything closed.
 */
const openApprovalModal = () => {
  // Ensure all other modals are closed (including attachment modal)
  isDocumentModalOpen.value = false
  isAttachmentModalOpen.value = false
  cleanupGeneratedDocument()
  
  // Close main modal and open approval modal
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
  customPickupLocation.value = ''
  personToLook.value = ''
}

const handleLocationChange = () => {
  if (pickupLocation.value !== 'other') {
    customPickupLocation.value = ''
  }
}

const closeApprovalModal = (reopenMain = true) => {
  isApprovalModalOpen.value = false
  pickupDate.value = ''
  pickupTime.value = ''
  pickupEndDate.value = ''
  pickupEndTime.value = ''
  pickupItem.value = ''
  pickupLocation.value = ''
  customPickupLocation.value = ''
  personToLook.value = ''
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
 * Generate HTML document matching the official Barangay Certification template
 */
const generateDocumentHtml = (request, pickupPayload) => {
  // Build full name
  const firstName = request?.first_name || ''
  const middleName = request?.middle_name || ''
  const lastName = request?.last_name || ''
  const suffix = request?.suffix || ''
  const fullName = [firstName, middleName, lastName, suffix].filter(Boolean).join(' ') || request?.name || 'N/A'
  
  const docType = request?.document_type?.document_name || request?.documentType || request?.title || 'Barangay Certificate'
  const refCode = request?.doc_request_ticket || request?.referenceCode || 'N/A'
  const purpose = request?.purpose || 'N/A'
    const houseNumber = request?.house_number || ''
    const phase = request?.phase || ''
    const packageValue = request?.package || ''
    const fullAddress = [houseNumber, phase, packageValue].filter(Boolean).join(' ') || request?.address || 'N/A'
  
  // Get duration of residency from extra_fields
  const extraFields = request?.extra_fields || {}
  const durationOfResidency = extraFields.duration_of_residency || extraFields['Duration of Residency'] || '[Duration of Residency]'
  
  // Format dates
  const reviewedAt = pickupPayload?.reviewed_at ? new Date(pickupPayload.reviewed_at) : new Date()
  const dayApproved = reviewedAt.getDate().toString().padStart(2, '0')
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  const monthApproved = monthNames[reviewedAt.getMonth()]
  const yearApproved = reviewedAt.getFullYear()
  
  const approver = user.value?.name || user.value?.first_name + ' ' + (user.value?.last_name || '') || (user.value?.email ?? 'Approver')
  
  // Format pickup dates
  const formatDateTime = (dateStr) => {
    if (!dateStr) return ''
    try {
      const d = new Date(dateStr)
      return d.toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })
    } catch {
      return dateStr
    }
  }

  return `
  <!doctype html>
  <html>
  <head>
    <meta charset="utf-8" />
    <title>${docType} - ${refCode}</title>
    <style>
      @page {
        size: A4;
        margin: 1in;
      }
      body { 
        font-family: 'Times New Roman', Times, serif; 
        margin: 0;
        padding: 40px 60px;
        color: #000;
        line-height: 1.6;
        background: white;
      }
      .document-container {
        max-width: 8.5in;
        margin: 0 auto;
        position: relative;
      }
      .header {
        text-align: center;
        margin-bottom: 25px;
        position: relative;
      }
      .header-text {
        margin: 0;
        font-size: 12pt;
        font-weight: bold;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
      }
      .barangay-info {
        font-size: 11pt;
        margin: 3px 0;
        font-weight: bold;
      }
      .document-title {
        text-align: center;
        font-size: 16pt;
        font-weight: bold;
        text-decoration: underline;
        margin: 30px 0 25px 0;
        text-transform: uppercase;
        letter-spacing: 2px;
      }
      .salutation {
        margin: 25px 0 20px 0;
        font-size: 12pt;
        font-weight: bold;
      }
      .certification-text {
        text-align: justify;
        font-size: 12pt;
        margin: 18px 0;
        line-height: 1.9;
        text-indent: 0.5in;
      }
      .certification-text strong {
        font-weight: bold;
        text-decoration: underline;
      }
      .issuance-date {
        margin: 25px 0;
        font-size: 12pt;
        line-height: 1.8;
      }
      .issuance-date strong {
        font-weight: bold;
        text-decoration: underline;
      }
      .signature-block {
        text-align: right;
        margin-top: 80px;
        margin-right: 60px;
      }
      .certified-by {
        font-size: 12pt;
        margin-bottom: 50px;
        font-weight: bold;
      }
      .signatory-name {
        font-size: 12pt;
        margin-top: 50px;
        border-top: 1px solid #000;
        padding-top: 5px;
        display: inline-block;
        min-width: 250px;
        text-align: center;
        font-weight: bold;
      }
      .signatory-title {
        font-size: 11pt;
        margin-top: 5px;
        font-style: italic;
      }
      .meta-section {
        margin: 30px 0;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #f9f9f9;
        font-size: 11pt;
      }
      .meta-row {
        display: flex;
        margin-bottom: 8px;
      }
      .meta-label {
        width: 180px;
        font-weight: 600;
        color: #333;
      }
      .meta-value {
        flex: 1;
      }
      .separator {
        border-top: 1px solid #ddd;
        margin: 15px 0;
      }
      .footer-info {
        margin-top: 30px;
        font-size: 11pt;
        color: #555;
      }
    </style>
  </head>
  <body>
    <div class="document-container">
      <div class="header">
        <div class="header-text">REPUBLIC OF THE PHILIPPINES</div>
        <div class="barangay-info">Barangay 176-B</div>
        <div class="barangay-info">Zone 15 District 1</div>
        <div class="barangay-info">Caloocan City</div>
      </div>

      <div class="document-title">${docType.toUpperCase().replace('BARANGAY ', 'BARANGAY ')}</div>

      <div class="salutation">TO WHOM IT MAY CONCERN,</div>

      <div class="certification-text">
        This is to certify that <strong>${fullName}</strong> of legal age, with postal address at <strong>${fullAddress}</strong> Zip Code (1428). Is a Bonafide resident for <strong>${durationOfResidency}</strong> and he/she has no derogatory record on file of this date.
      </div>

      <div class="certification-text">
        This certification is issued upon request of <strong>${fullName}</strong> for the purpose of <strong>${purpose}</strong> and/or whatever legal purposes and intents this may serve.
      </div>

      <div class="issuance-date">
        Issued this <strong>${dayApproved}</strong> day of <strong>${monthApproved}</strong>, <strong>${yearApproved}</strong><br>
        City of Caloocan.
      </div>

      <div class="signature-block">
        <div class="certified-by">Certified by:</div>
        <div class="signatory-name">${approver}</div>
        <div class="signatory-title">Barangay Chairman</div>
      </div>

      <!-- Additional Information Section (for reference) -->
      <div class="meta-section">
        <div class="meta-row">
          <div class="meta-label">Document Ticket:</div>
          <div class="meta-value">${refCode}</div>
        </div>
        <div class="separator"></div>
        <div style="font-weight: bold; margin-bottom: 10px;">Pickup Information:</div>
        <div class="meta-row">
          <div class="meta-label">Pickup Item:</div>
          <div class="meta-value">${pickupPayload?.pickup_item || 'N/A'}</div>
        </div>
        <div class="meta-row">
          <div class="meta-label">Pickup Location:</div>
          <div class="meta-value">${pickupPayload?.pickup_location || 'N/A'}</div>
        </div>
        <div class="meta-row">
          <div class="meta-label">Pickup Start:</div>
          <div class="meta-value">${formatDateTime(pickupPayload?.pickup_start) || 'N/A'}</div>
        </div>
        <div class="meta-row">
          <div class="meta-label">Pickup End:</div>
          <div class="meta-value">${formatDateTime(pickupPayload?.pickup_end) || 'N/A'}</div>
        </div>
        <div class="meta-row">
          <div class="meta-label">Person to Look For:</div>
          <div class="meta-value">${pickupPayload?.person_to_look || 'N/A'}</div>
        </div>
      </div>

      <div class="footer-info">
        <div>Reviewed at: ${reviewedAt.toLocaleString('en-US', { timeZone: 'Asia/Manila' })}</div>
      </div>
    </div>
  </body>
  </html>
  `
}

/**
 * Show generated HTML in modal (creates object URL, sets filename)
 */
const showGeneratedDocumentFromHtml = async (htmlString, filename = 'document.html') => {
  cleanupGeneratedDocument()
  const blob = new Blob([htmlString], { type: 'text/html' })
  const url = URL.createObjectURL(blob)
  generatedDocumentObjectUrl.value = url
  generatedDocumentFilename.value = filename
  isDocumentModalOpen.value = true
  // Show print prompt after document loads
  await nextTick()
  setTimeout(() => {
    askToPrint()
  }, 1500)
}

/**
 * existing helpers for server-returned documents
 */
const showGeneratedDocumentFromBase64 = async (base64, filename = 'document.pdf', mime = 'application/pdf') => {
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
    // Show print prompt after document loads
    await nextTick()
    setTimeout(() => {
      askToPrint()
    }, 1500)
  } catch (e) {
    console.error('Failed to build blob from base64', e)
    // still open modal so user sees message
    isDocumentModalOpen.value = true
  }
}

const showGeneratedDocumentFromUrl = async (url, filename = '') => {
  cleanupGeneratedDocument()
  generatedDocumentFilename.value = filename || ''
  
  // Try to fetch and create object URL for better compatibility
  try {
    console.log('📄 Fetching document from URL:', url)
    const response = await fetch(url, {
      credentials: 'include', // Include cookies for authentication
      headers: {
        'Accept': 'application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,*/*'
      }
    })
    
    console.log('📊 Fetch response:', {
      status: response.status,
      statusText: response.statusText,
      contentType: response.headers.get('Content-Type'),
      contentLength: response.headers.get('Content-Length')
    })
    
    if (response.ok) {
      const blob = await response.blob()
      console.log('📦 Blob created:', {
        size: blob.size,
        type: blob.type
      })
      
      // Ensure blob type is set correctly for PDFs
      if (!blob.type || blob.type === 'application/octet-stream') {
        // Try to determine type from filename
        if (filename.toLowerCase().endsWith('.pdf')) {
          const typedBlob = new Blob([blob], { type: 'application/pdf' })
          const objUrl = URL.createObjectURL(typedBlob)
          generatedDocumentObjectUrl.value = objUrl
          generatedDocumentMime.value = 'application/pdf'
          console.log('✅ Created PDF blob URL')
        } else if (filename.toLowerCase().endsWith('.docx')) {
          const typedBlob = new Blob([blob], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' })
          const objUrl = URL.createObjectURL(typedBlob)
          generatedDocumentObjectUrl.value = objUrl
          generatedDocumentMime.value = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
          console.log('✅ Created DOCX blob URL')
        } else {
          const objUrl = URL.createObjectURL(blob)
          generatedDocumentObjectUrl.value = objUrl
          generatedDocumentMime.value = blob.type || 'application/pdf'
        }
      } else {
        const objUrl = URL.createObjectURL(blob)
        generatedDocumentObjectUrl.value = objUrl
        generatedDocumentMime.value = blob.type || 'application/pdf'
        console.log('✅ Created blob URL with type:', blob.type)
      }
    } else {
      console.error('❌ Fetch failed:', response.status, response.statusText)
      const errorText = await response.text()
      console.error('Error response:', errorText.substring(0, 200))
      // Fallback to direct URL if fetch fails
      generatedDocumentUrl.value = url
    }
  } catch (e) {
    console.error('❌ Exception fetching document:', e)
    // Fallback to direct URL
    generatedDocumentUrl.value = url
  }
  
  isDocumentModalOpen.value = true
  
  // Show print prompt after document loads - wait longer for iframe to render
  await nextTick()
  setTimeout(() => {
    askToPrint()
  }, 1500) // Wait longer for iframe to load PDF
}

const closeFrontendDocumentModal = () => {
  // reuse the existing cleanup/close helper
  closeDocumentModal()
}

const closeDocumentModal = () => {
  isDocumentModalOpen.value = false
  // cleanup object url if any
  cleanupGeneratedDocument()
}

const printGeneratedDocument = () => {
  // Try iframe method first
  const iframe = document.getElementById('doc-preview-iframe')
  
  if (iframe) {
    try {
      // Wait for iframe to load if it hasn't
      const tryPrint = () => {
        try {
          iframe.contentWindow.focus()
          iframe.contentWindow.print()
        } catch (e) {
          // If iframe method fails, try opening in new window
          openDocumentForPrinting()
        }
      }
      
      // Check if iframe is loaded
      if (iframe.contentDocument && iframe.contentDocument.readyState === 'complete') {
        tryPrint()
      } else {
        // Wait for iframe to load
        iframe.onload = () => {
          setTimeout(tryPrint, 500)
        }
        // Also try after a timeout
        setTimeout(tryPrint, 1000)
      }
    } catch (e) {
      console.error('Print failed via iframe:', e)
      openDocumentForPrinting()
    }
  } else {
    // If iframe doesn't exist, try direct URL method
    openDocumentForPrinting()
  }
}

// Alternative method: open document in new window for printing
const openDocumentForPrinting = () => {
  const documentUrl = generatedDocumentObjectUrl.value || generatedDocumentUrl.value
  if (documentUrl) {
    try {
      const printWindow = window.open(documentUrl, '_blank')
      if (printWindow) {
        printWindow.onload = () => {
          setTimeout(() => {
            printWindow.print()
          }, 500)
        }
      } else {
        alert('Please allow pop-ups for this site to print the document, or use the Download button.')
      }
    } catch (e) {
      console.error('Failed to open print window:', e)
      alert('Could not open print dialog. Please use the Download button to save and print the document.')
    }
  } else {
    alert('Document URL not available. Please try downloading the document instead.')
  }
}

// Handle iframe load event
const handleIframeLoad = () => {
  iframeLoaded.value = true
  iframeError.value = false
  
  // Check if iframe actually loaded content (PDFs in blob URLs sometimes fail silently)
  setTimeout(() => {
    const iframe = document.getElementById('doc-preview-iframe')
    if (iframe) {
      try {
        // Try to access iframe content - if it fails, the PDF might not have loaded
        const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document
        if (!iframeDoc || iframeDoc.body?.innerText?.includes('Failed to load PDF')) {
          iframeError.value = true
        }
      } catch (e) {
        // Cross-origin or other error - assume it's loading
        // PDFs in blob URLs should work, so we'll assume success
        console.log('Iframe load check:', e.message)
      }
    }
  }, 1000)
}

const handleIframeError = () => {
  console.error('Iframe failed to load document')
  iframeError.value = true
}

// Ask user if they want to print the document
const askToPrint = () => {
  const userWantsToPrint = confirm('Document generated successfully! Would you like to print it now?')
  if (userWantsToPrint) {
    // Give iframe more time to load before attempting print
    setTimeout(() => {
      printGeneratedDocument()
    }, 1000)
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

// Separate ID attachments (id_front, id_back) from other attachments
const idAttachments = computed(() => {
  if (!selectedRequest.value || !selectedRequest.value.attachments) return []
  if (!Array.isArray(selectedRequest.value.attachments)) return []
  
  return selectedRequest.value.attachments.filter(att => {
    const fieldName = att.field_name || ''
    return fieldName === 'id_front' || fieldName === 'id_back'
  })
})

// Other attachments (excluding ID front/back)
const otherAttachments = computed(() => {
  if (!selectedRequest.value || !selectedRequest.value.attachments) return []
  if (!Array.isArray(selectedRequest.value.attachments)) return []
  
  return selectedRequest.value.attachments.filter(att => {
    const fieldName = att.field_name || ''
    return fieldName !== 'id_front' && fieldName !== 'id_back'
  })
})

// CONFIRM APPROVAL (modified only for showing generated document - all other logic kept)
const confirmApproval = async () => {
  if (!pickupDate.value || !pickupTime.value) {
    alert('Please set both pickup date and time')
    return
  }
  if (!pickupLocation.value) {
    alert('Please select a pickup location')
    return
  }
  if (pickupLocation.value === 'other' && !customPickupLocation.value) {
    alert('Please enter a custom pickup location')
    return
  }
  if (!selectedRequest.value || !selectedRequest.value.doc_request_id) {
    alert('No request selected')
    return
  }

  const payload = {
    pickup_item: pickupItem.value || null,
    pickup_location: (pickupLocation.value === 'other' ? customPickupLocation.value : pickupLocation.value) || null,
    pickup_start: buildDateTime(pickupDate.value, pickupTime.value),
    pickup_end: (pickupEndDate.value && pickupEndTime.value) ? buildDateTime(pickupEndDate.value, pickupEndTime.value) : null,
    person_to_look: personToLook.value || null,
    status: 'Approved',
    fk_approver_id: user.value?.id ?? user.value?.user_id ?? null,
    reviewed_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
    admin_feedback: null,
  }

  isApproving.value = true
  const url = buildUrl('document_requests.approve', selectedRequest.value.doc_request_id)

  try {
    const res = await axios.post(url, payload)
    if (res?.data?.success) {
      // Remove the request from localRequests since it's now approved (should be in history)
      const idx = localRequests.value.findIndex(r => r.doc_request_id === selectedRequest.value.doc_request_id)
      if (idx > -1) {
        localRequests.value.splice(idx, 1)
      }

      // Check if document was generated
      const documentGenerated = res.data.document_generated || (res.data.data && res.data.data.document_generated) || false
      const documentError = res.data.document_generation_error || (res.data.data && res.data.data.document_generation_error) || null

      // Show appropriate message
      if (documentGenerated) {
        alert('Request is approved. Document generated successfully!')
      } else if (documentError) {
        alert(`Request is approved, but document generation failed: ${documentError}\n\nPlease check the logs or try regenerating the document.`)
      } else {
        alert('Request is approved.')
      }

      // Handle document download - prefer DOCX, fallback to PDF
      // IMPORTANT: Download directly from server to avoid wasting DocuGenerate API calls
      // Use direct link click method to trigger download (no blob URLs, no print dialogs)
      const docxUrl = res.data.document_docx_url || (res.data.data && res.data.data.document_docx_url) || null
      const pdfUrl = res.data.document_url || (res.data.data && res.data.data.document_url) || null
      const docxFilename = res.data.docx_filename || (res.data.data && res.data.data.docx_filename) || null
      const filename = docxFilename || res.data.filename || (res.data.data && res.data.data.filename) || `${selectedRequest.value.referenceCode || 'document'}.docx`

      // Function to trigger direct download using temporary link (forces download, no redirects, no prints)
      const triggerDirectDownload = (url, downloadFilename) => {
        const fullUrl = url.startsWith('http') ? url : window.location.origin + url
        console.log('📥 Triggering direct download:', fullUrl, 'as', downloadFilename)
        
        // Create a temporary anchor element and click it to trigger download
        // The download attribute forces the browser to download instead of navigating
        const link = document.createElement('a')
        link.href = fullUrl
        link.download = downloadFilename // Forces download instead of navigation
        link.style.display = 'none'
        link.rel = 'noopener noreferrer' // Security best practice
        
        // Append to body
        document.body.appendChild(link)
        
        // Trigger click immediately
        link.click()
        
        // Cleanup after a short delay
        setTimeout(() => {
          if (document.body.contains(link)) {
            document.body.removeChild(link)
          }
        }, 100)
        
        console.log('✅ Download triggered - file should download directly')
      }

      // Download DOCX file (preferred - opens in Word)
      if (docxUrl) {
        const docxFilename = filename.endsWith('.docx') ? filename : filename.replace(/\.(pdf|html)$/i, '.docx')
        triggerDirectDownload(docxUrl, docxFilename)
      } else if (pdfUrl) {
        // Fallback to PDF (but prefer DOCX)
        const pdfFilename = filename.endsWith('.pdf') ? filename : filename.replace(/\.(docx|html)$/i, '.pdf')
        triggerDirectDownload(pdfUrl, pdfFilename)
      } else {
        // No document URL available - document generation may have failed
        console.warn('⚠️ No document URL available in approval response', {
          documentGenerated,
          documentError,
          response: res.data
        })
        if (documentError) {
          console.error('Document generation error:', documentError)
        }
      }

      // close approval modal and clear selection (do NOT attempt to close browser tabs)
      // pass false so the main modal is not re-opened
      closeApprovalModal(false)

      // IMPORTANT: NO closeAllTabs() call here — user will manually close the document modal.
      // Rejection flow still keeps automatic close behavior as before.
    } else {
      alert(res?.data?.message ?? 'Approval completed, but server returned unexpected response.')
    }
  } catch (err) {
    console.error('Approval error:', err)
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

const handleRejectClick = async (event) => {
  // Explicitly stop all event propagation
  if (event) {
    event.preventDefault()
    event.stopPropagation()
    event.stopImmediatePropagation()
  }
  
  // Check if request is selected
  if (!selectedRequest.value || !selectedRequest.value.doc_request_id) {
    alert('No request selected')
    return
  }
  
  // Get rejection reason from user via prompt
  const rejectionReason = prompt('Please provide a reason for rejecting this request:')
  
  if (!rejectionReason || !rejectionReason.trim()) {
    // User cancelled or entered empty reason
    if (rejectionReason !== null) {
      alert('Rejection reason is required. Request not rejected.')
    }
    return
  }
  
  // Confirm rejection
  const confirmed = confirm(`Are you sure you want to reject this request?\n\nReason: ${rejectionReason.trim()}`)
  
  if (!confirmed) {
    console.log('Rejection cancelled by user')
    return
  }
  
  // Submit the rejection
  await submitRejection(rejectionReason.trim())
}

const submitRejection = async (reason) => {
  if (isRejecting.value) return
  isRejecting.value = true

  if (!reason || !reason.trim()) {
    alert("Rejection reason is required")
    isRejecting.value = false
    return
  }

  if (!selectedRequest.value || !selectedRequest.value.doc_request_id) {
    alert('No request selected')
    isRejecting.value = false
    return
  }

  const payload = {
    status: 'Rejected',
    rejection_reason: reason.trim(),
    admin_feedback: reason.trim(),
  }

  try {
    console.log('Submitting rejection with reason:', reason.trim())
    await router.post(
      `/document-requests/${selectedRequest.value.doc_request_id}/reject`,
      payload,
      {
        preserveState: false,
        onSuccess: () => {
          // Remove the request from localRequests since it's now rejected (should be in history)
          const idx = localRequests.value.findIndex(r => r.doc_request_id === selectedRequest.value.doc_request_id)
          if (idx > -1) {
            localRequests.value.splice(idx, 1)
          }

          // Close modal and reset
          isModalOpen.value = false
          selectedRequest.value = null

          // Show success message
          alert('Request rejected successfully.')
          console.log('Rejection successful')
          router.reload()
        },
        onError: (errors) => {
          console.error('Rejection error', errors)
          const errorMessages = Object.values(errors).flat().join('\n')
          alert('Failed to reject request:\n' + (errorMessages || 'Unknown error occurred'))
        }
      }
    )
  } catch (err) {
    console.error('Reject exception', err)
    alert('An error occurred while rejecting the request.')
  } finally {
    isRejecting.value = false
  }
}

const closeRejectionModal = () => {
  isRejectionModalOpen.value = false
  rejectionReason.value = ''
  // Main modal stays open (no need to reopen it)
}

const confirmRejection = async () => {
  if (isRejecting.value) return
  isRejecting.value = true

  if (!rejectionReason.value.trim()) {
    alert("Please provide a reason for rejection")
    isRejecting.value = false
    return
  }

  if (!selectedRequest.value || !selectedRequest.value.doc_request_id) {
    alert('No request selected')
    isRejecting.value = false
    return
  }

  const payload = {
    status: 'Rejected',
    rejection_reason: rejectionReason.value.trim(),
    admin_feedback: rejectionReason.value.trim(),
  }

  try {
    await router.post(
      `/document-requests/${selectedRequest.value.doc_request_id}/reject`,
      payload,
      {
        preserveState: false,
        onSuccess: () => {
          // Remove the request from localRequests since it's now rejected (should be in history)
          const idx = localRequests.value.findIndex(r => r.doc_request_id === selectedRequest.value.doc_request_id)
          if (idx > -1) {
            localRequests.value.splice(idx, 1)
          }

          // close modals / reset local fields
          isRejectionModalOpen.value = false
          selectedRequest.value = null
          rejectionReason.value = ''

          // show alert and refresh
          alert('Request rejected successfully.')
          router.reload()
        },
        onError: (errors) => {
          console.error('Rejection error', errors)
          const errorMessages = Object.values(errors).flat().join('\n')
          alert('Failed to reject request:\n' + (errorMessages || 'Unknown error occurred'))
        }
      }
    )
  } catch (err) {
    console.error('Reject exception', err)
    alert('An error occurred while rejecting the request.')
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
    display: flex;
    align-items: center;
    gap: 10px;
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
    color: #333;
}

.filter-dropdown-btn:hover {
    border-color: #ff8c42;
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

.search-input::placeholder {
    color: #999;
}

.search-btn {
    background: #f8f9fa;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-btn:hover {
    background: #e9ecef;
}

/* Requests Container */
.requests-container {
    padding: 25px;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
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

.modal-container {
    background: white !important;
    border-radius: 24px;
    padding: 40px;
    width: 90%;
    max-width: 1000px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative !important;
    box-shadow: 0 25px 80px rgba(0,0,0,0.15);
    z-index: 10000 !important;
    visibility: visible !important;
    opacity: 1 !important;
    display: block !important;
}

.approval-modal,
.rejection-modal {
    max-width: 600px;
}

.rejection-modal-overlay {
    z-index: 10001 !important;
}

.rejection-modal-overlay .modal-container {
    z-index: 10002 !important;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #f3f4f6;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.modal-close:hover {
    background: #e5e7eb;
    color: #1a1a1a;
    transform: scale(1.05);
}

.modal-content {
    margin-top: 0;
}

.modal-top {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    padding-bottom: 32px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 32px;
    align-items: start;
}

.modal-user-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-avatar {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    object-fit: cover;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.modal-name {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 6px 0;
    letter-spacing: -0.02em;
}

.modal-label {
    font-size: 15px;
    color: #6b7280;
    margin: 0 0 3px 0;
    font-weight: 500;
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
    gap: 32px;
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
    gap: 16px;
}

.detail-item {
    background: #f9fafb;
    padding: 16px 18px;
    border-radius: 12px;
    border: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.detail-item:hover {
    background: #f3f4f6;
    border-color: #e5e7eb;
}

.detail-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.detail-section:first-of-type {
    margin-top: 0;
    padding-top: 0;
    border-top: none;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #239640;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #239640;
    letter-spacing: -0.01em;
}

.detail-item-full {
    background: #f9fafb;
    padding: 20px 24px;
    border-radius: 12px;
    border: 1px solid #f3f4f6;
}

.detail-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 6px 0;
    text-transform: none;
    letter-spacing: 0;
}

.detail-value {
    font-size: 15px;
    color: #1a1a1a;
    font-weight: 500;
    margin: 0;
    line-height: 1.5;
}

.attachments-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.attachment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fafb;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.attachment-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.attachment-info {
    flex: 1;
}

.attachment-label {
    font-size: 14px;
    font-weight: 700;
    color: #239640;
    margin: 0 0 8px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.attachment-filename {
    font-size: 16px;
    color: #1a1a1a;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.attachment-size {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

.attachment-download-btn {
    background: #239640;
    color: white;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.2);
}

.attachment-download-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(35, 150, 64, 0.35);
}

/* Attachment Modal Styles */
.attachment-modal {
    max-width: 90vw;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 10001 !important; /* Higher than main modal to appear on top */
}

/* Ensure attachment modal overlay appears above main modal */
div.modal-overlay:has(.attachment-modal) {
    z-index: 10001 !important;
}

.attachment-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e0e0e0;
}

.attachments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.attachment-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: all 0.3s ease;
}

.attachment-card:hover {
    border-color: #239640;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.15);
}

.attachment-header {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.attachment-preview {
    width: 100%;
    margin-top: 10px;
}

.image-preview-container {
    width: 100%;
    max-height: 500px;
    overflow: hidden;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.attachment-image {
    max-width: 100%;
    max-height: 500px;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

.attachment-iframe {
    width: 100%;
    height: 400px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.attachment-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.attachment-actions .download-btn {
    flex: 1;
    text-align: center;
    text-decoration: none;
}

/* Inline attachment previews in request details modal */
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

.attachment-image-inline-small {
    max-width: 100%;
    max-height: 200px;
    object-fit: contain;
    display: block;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.attachment-preview-inline {
    width: 100%;
    margin-top: 10px;
}

.attachment-actions-inline {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.loading-placeholder {
    padding: 40px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.pdf-preview-link {
    color: #239640;
    text-decoration: none;
    font-weight: 600;
    padding: 8px 16px;
    border: 1px solid #239640;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s ease;
}

.pdf-preview-link:hover {
    background: #239640;
    color: white;
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
    padding: 14px 32px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.25);
    letter-spacing: 0.01em;
}

.approve-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(35, 150, 64, 0.35);
}

.reject-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
    letter-spacing: 0.01em;
}

.reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.35);
}

/* Approval Modal Styles */
.approval-title {
    font-size: 24px;
    font-weight: 700;
    color: #239640;
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
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.confirm-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.4);
}

.cancel-btn {
    flex: 1;
    background: #6b7280;
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.25);
    letter-spacing: 0.01em;
}

.cancel-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(107, 114, 128, 0.35);
}

/* Rejection Modal Styles */
.rejection-title {
    font-size: 24px;
    font-weight: 700;
    color: #ef4444;
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
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.confirm-reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
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
    
    .requests-container {
        grid-template-columns: 1fr;
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