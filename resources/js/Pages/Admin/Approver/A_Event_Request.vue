<template>
    <Head>
        <title>Event Assistance Request</title>
    </Head>

    <div class="app-container">
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/ADMIN LOGO1.png" alt="Logo" class="header-logo" />
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

        <div class="main-layout">
            <div class="sidebar">
                <div class="profile-card">
                    <img src="/assets/ADMIN.png" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link href="#" class="nav-item" @click="navigateToDocumentRequest">
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Document Request
                    </Link>
                    <Link href="#" class="nav-item active">
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Event Assistance Request
                    </Link>
                    <Link href="#" class="nav-item" @click="navigateToHistory">
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                    </Link>
                </div>
            </div>

            <div class="content-area">
                <div class="main-content">
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Event Assistance Request</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

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
                                    <button @click="selectFilter('venue')" :class="{ active: filterOption === 'venue' }">VENUE</button>
                                    <button @click="selectFilter('equipment')" :class="{ active: filterOption === 'equipment' }">EQUIPMENT</button>
                                    <button @click="selectFilter('personnel')" :class="{ active: filterOption === 'personnel' }">PERSONNEL</button>
                                </div>
                            </div>
                        </div>
                        <div class="filter-right">  
                            <div class="search-container">
                                <input type="text" v-model="searchQuery" @input="performSearch" placeholder="SEARCH..." class="search-input" />
                                <button class="search-btn" @click="performSearch">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="requests-container">
                        <div 
                            v-for="(request, index) in filteredRequests" 
                            :key="request.id || request.referenceCode || index"
                            class="request-card"
                        >
                            <div class="request-content">
                                <div class="request-left">
                                    <img :src="request.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" />
                                    <div class="request-info">
                                        <h3 class="request-name">{{ request.name }}</h3>
                                        <p class="request-doc-type">{{ request.event_type || request.assistanceType || 'Event Assistance' }}</p>
                                        <p class="request-ref-code">{{ request.referenceCode }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <p class="request-date">{{ request.date }}</p>
                                    <button 
                                        @click.stop="openModal(request)" 
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
                            <p>No event assistance requests found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
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
                                <p class="modal-label" style="font-size: 12px; margin: 0;">Event Assistance Request</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: row; gap: 15px; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ff8c42 0%, #ff7a28 100%); color: white; padding: 10px 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(255, 122, 40, 0.3); min-height: fit-content;">
                            <div style="flex: 1;">
                                <p style="font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; opacity: 0.9;">Type of Assistance</p>
                                <h3 style="font-size: 15px; font-weight: 700; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.2); line-height: 1.2;">{{ selectedRequest.event_type || selectedRequest.assistanceType || 'Unknown Assistance Type' }}</h3>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.3); flex-shrink: 0;"></div>
                            <div style="flex: 0 0 auto; text-align: right;">
                                <p style="font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin: 0 0 3px 0; opacity: 0.9;">Request Number</p>
                                <p style="font-size: 13px; font-weight: 700; margin: 0; font-family: monospace; letter-spacing: 0.8px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ selectedRequest.referenceCode }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="modal-details">
                        <!-- Request Information - Compact -->
                        <div class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 16px;">Request Information</h4>
                            <div class="details-grid" style="grid-template-columns: auto 1fr; gap: 12px 15px; align-items: start;">
                                <div v-if="selectedRequest.purpose || selectedRequest.eventName" class="detail-item" style="margin: 0; min-width: 120px;">
                                    <p class="detail-label" style="font-size: 12px; margin-bottom: 3px;">Purpose:</p>
                                    <p class="detail-value" style="font-size: 13px; font-weight: 600; color: #239640; margin: 0;">
                                        {{ selectedRequest.purpose || selectedRequest.eventName || 'Not specified' }}
                                    </p>
                                </div>
                                <div v-if="selectedRequest.eventDescription || selectedRequest.other_purpose" class="detail-item" style="margin: 0; flex: 1;">
                                    <p class="detail-label" style="font-size: 12px; margin-bottom: 3px;">Description:</p>
                                    <p class="detail-value" style="font-size: 13px; margin: 0; line-height: 1.4; text-align: left; word-wrap: break-word;">{{ selectedRequest.eventDescription || selectedRequest.other_purpose || 'No description provided' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 16px;">Event Details</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 8px 12px;">
                                <div v-if="selectedRequest.eventDate" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Event Date:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.eventDate }}</p>
                                </div>
                                <div v-if="selectedRequest.eventTime" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Event Time:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.eventTime }}</p>
                                </div>
                                <div v-if="selectedRequest.expectedAttendees" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Expected Attendees:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.expectedAttendees }}</p>
                                </div>
                                <div v-if="selectedRequest.venue || selectedRequest.event_location" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Venue/Location:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.venue || selectedRequest.event_location || 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- User & Personal Information - Merged (like document requests) -->
                        <div class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 16px;">User Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 8px 12px;">
                                <!-- Personal Info -->
                                <div v-if="selectedRequest.first_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">First Name:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.first_name }}</p>
                                </div>
                                <div v-if="selectedRequest.middle_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Middle Name:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.middle_name }}</p>
                                </div>
                                <div v-if="selectedRequest.last_name" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Last Name:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.last_name }}</p>
                                </div>
                                <div v-if="selectedRequest.suffix" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Suffix:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.suffix }}</p>
                                </div>
                                <div v-if="selectedRequest.birthdate" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Birthdate:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ formatDate(selectedRequest.birthdate) }}</p>
                                </div>
                                <div v-if="selectedRequest.age" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Age:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.age }}</p>
                                </div>
                                <div v-if="selectedRequest.sex" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Sex:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.sex }}</p>
                                </div>
                                <div v-if="selectedRequest.civilStatus" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Civil Status:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.civilStatus }}</p>
                                </div>
                                
                                <!-- Contact Info -->
                                <div v-if="selectedRequest.contact" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Contact:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0;">{{ selectedRequest.contact }}</p>
                                </div>
                                
                                <!-- Address Info (Compact) -->
                                <div v-if="selectedRequest.address" class="detail-item" style="grid-column: span 2; margin: 0;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">Address:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0; line-height: 1.3;">
                                        {{ selectedRequest.address }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Extra Fields (Dynamic Fields) -->
                        <div v-if="(selectedRequest.extra_fields && getExtraFieldsCount(selectedRequest.extra_fields) > 0) || selectedRequest.valid_id_type || selectedRequest.valid_id_number" class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 16px;">Additional Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 8px 12px;">
                                <!-- ID Type and ID Number -->
                                <div v-if="selectedRequest.valid_id_type" class="detail-item" style="margin: 0; padding: 8px 10px;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">ID Type:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0; line-height: 1.3;">{{ getValidIdTypeName(selectedRequest.valid_id_type) }}</p>
                                </div>
                                <div v-if="selectedRequest.valid_id_number" class="detail-item" style="margin: 0; padding: 8px 10px;">
                                    <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">ID Number:</p>
                                    <p class="detail-value" style="font-size: 12px; margin: 0; line-height: 1.3;">{{ selectedRequest.valid_id_number }}</p>
                                </div>
                                
                                <!-- Dynamic Extra Fields -->
                                <template v-for="(value, key) in getExtraFieldsObject(selectedRequest.extra_fields)" :key="key">
                                    <div class="detail-item" v-if="isValidExtraFieldValue(value)" style="margin: 0; padding: 8px 10px;">
                                        <p class="detail-label" style="font-size: 11px; margin-bottom: 2px; color: #666;">{{ formatFieldName(key) }}:</p>
                                        <p class="detail-value" style="font-size: 12px; margin: 0; line-height: 1.3;">
                                            <span v-if="Array.isArray(value)">{{ value.length > 0 ? value.join(', ') : 'Not provided' }}</span>
                                            <span v-else-if="typeof value === 'object' && value !== null && !(value instanceof File)">{{ JSON.stringify(value) }}</span>
                                            <span v-else>{{ String(value) }}</span>
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Valid ID Attachments Section -->
                        <div v-if="idAttachments.length > 0 || (selectedRequest.valid_id_url || selectedRequest.hasValidId)" class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 16px;">Valid Identification</h4>
                            
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
                                                <p class="attachment-label" style="font-size: 12px; font-weight: 700; color: #239640; margin: 0 0 5px 0; text-transform: uppercase;">
                                                    {{ formatFieldName(attachment.field_name || 'Unknown') }}
                                                </p>
                                                <p class="attachment-filename" style="font-size: 14px; color: #333; font-weight: 600; margin: 0 0 5px 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; flex-shrink: 0; display: inline-block; vertical-align: middle; margin-right: 6px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ attachment.file_name || 'Unnamed file' }}
                                                </p>
                                                <p v-if="attachment.file_size" class="attachment-size" style="font-size: 11px; color: #666; margin: 0;">
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
                                                    style="display: inline-block; text-align: center; padding: 10px 20px; background: #239640; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;"
                                                >
                                                    View/Download File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Fallback: Valid ID Image Preview (if no separate attachments but has valid_id_content) -->
                            <template v-else-if="selectedRequest.valid_id_url || selectedRequest.hasValidId">
                                <div class="attachment-preview-section" style="margin-top: 15px;">
                                    <p class="detail-label" style="font-size: 12px; font-weight: 700; color: #666; margin: 0 0 10px 0; text-transform: uppercase;">Valid ID Image:</p>
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
                                            style="display: inline-block; text-align: center; padding: 10px 20px; background: #239640; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;"
                                            target="_blank"
                                        >
                                            Download Valid ID
                                        </a>
                                        <a 
                                            v-else-if="selectedRequest.valid_id_url"
                                            :href="selectedRequest.valid_id_url" 
                                            :download="`valid_id_${selectedRequest.referenceCode || 'attachment'}.jpg`" 
                                            class="attachment-download-btn"
                                            style="display: inline-block; text-align: center; padding: 10px 20px; background: #239640; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;"
                                            target="_blank"
                                        >
                                            Download Valid ID
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                            
                        <!-- Other Attachments -->
                        <div class="detail-section" style="margin-bottom: 15px;">
                            <h4 class="section-title" style="margin-bottom: 10px; font-size: 16px;">Other Uploaded Files</h4>
                            
                            <!-- Show attachments if available (excluding ID front/back which are shown separately) -->
                            <div v-if="otherAttachments && Array.isArray(otherAttachments) && otherAttachments.length > 0" class="attachments-list" style="display: grid; gap: 10px; margin-top: 10px;">
                                <div 
                                    v-for="(attachment, index) in otherAttachments" 
                                    :key="attachment.attachment_id || attachment.field_name || index" 
                                    class="attachment-item"
                                    style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; display: flex; flex-direction: column; gap: 8px;"
                                >
                                    <div class="attachment-info" style="text-align: center;">
                                        <p class="attachment-label" style="font-size: 12px; font-weight: 700; color: #239640; margin: 0 0 5px 0; text-transform: uppercase;">
                                            {{ formatFieldName(attachment.field_name || 'Unknown') }}
                                        </p>
                                        <p class="attachment-filename" style="font-size: 14px; color: #333; font-weight: 600; margin: 0 0 5px 0;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; flex-shrink: 0; display: inline-block; vertical-align: middle; margin-right: 6px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ attachment.file_name || 'Unnamed file' }}
                                        </p>
                                        <p v-if="attachment.file_size" class="attachment-size" style="font-size: 11px; color: #666; margin: 0;">
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
                                            style="display: inline-block; text-align: center; padding: 10px 20px; background: #239640; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;"
                                                >
                                                    View/Download File
                                                </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Show message if no attachments -->
                            <div v-else class="no-attachments-message" style="padding: 30px; text-align: center; background: #f8f9fa; border-radius: 10px; border: 1px dashed #ddd;">
                                <p class="detail-value" style="color: #999; font-size: 14px; margin: 0;">
                                    <span v-if="!selectedRequest.attachments" style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        No attachments data available.
                                    </span>
                                    <span v-else-if="!Array.isArray(selectedRequest.attachments)" style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Attachments data format error (not an array).
                                    </span>
                                    <span v-else style="display: flex; align-items: center; gap: 8px; justify-content: center;">
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
                            </div>
                        </div>

                        <!-- Action Buttons (only show if pending) -->
                        <div v-if="selectedRequest.status === 'Pending'" class="modal-actions">
                            <button @click.stop="openApprovalModal" class="approve-btn">
                                Approve
                            </button>
                            <button @click="openRejectionModal" class="reject-btn">
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
                    <p style="font-size: 16px; color: #666;">Loading request details...</p>
                    <p style="font-size: 14px; color: #999; margin-top: 10px;">If this message persists, please refresh the page.</p>
                </div>
            </div>
        </div>

        <div v-if="isApprovalModalOpen" class="modal-overlay" @click="closeApprovalModal">
            <div class="modal-container approval-modal" @click.stop>
                <button @click="closeApprovalModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="modal-content">
                    <h2 class="approval-title">Set Assistance Details</h2>
                    <p class="approval-subtitle">Provide the assistance details for the approved request</p>

                    <div class="approval-form">
                        <!-- Comment/Feedback (Required - this is what residents see) -->
                        <div class="form-group">
                            <label class="form-label">Comment/Feedback <span style="color: #dc3545;">*</span></label>
                            <textarea v-model="approvalDetails.comment" class="form-textarea" rows="8" placeholder="Provide details about the approval, instructions, or any important information for the resident..." required></textarea>
                            <p class="helper-text" style="margin-top: 6px; font-size: 12px; color: #666;">This comment will be displayed to the resident in their notification.</p>
                        </div>

                        <div class="approval-actions">
                            <button 
                                @click="confirmApproval"
                                class="confirm-btn"
                                :disabled="isSubmittingApproval"
                            >
                                {{ isSubmittingApproval ? 'Submitting...' : 'Confirm Approval' }}
                            </button>

                            <button @click="closeApprovalModal" class="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isRejectionModalOpen" class="modal-overlay" @click="closeRejectionModal">
            <div class="modal-container rejection-modal" @click.stop>
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
                            <textarea v-model="rejectionReason" class="form-textarea" rows="6" placeholder="Explain why this request cannot be approved..." required></textarea>
                        </div>

                        <div class="rejection-actions">
                            <button 
                                @click="confirmRejection"
                                class="confirm-reject-btn"
                                :disabled="isSubmittingRejection"
                            >
                                {{ isSubmittingRejection ? 'Submitting...' : 'Confirm Rejection' }}
                            </button>
                            <button @click="closeRejectionModal" class="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3'
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
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const isModalOpen = ref(false)
const isApprovalModalOpen = ref(false)
const isRejectionModalOpen = ref(false)
const selectedRequest = ref(null)
const rejectionReason = ref('')
const isSubmittingApproval = ref(false)
const isSubmittingRejection = ref(false)



// update approvalDetails to include only comment
const approvalDetails = ref({
    comment: '' // This goes to admin_feedback and is displayed to residents
})


// server-provided requests via Inertia props (defensive: support multiple shapes)
const serverRequests = computed(() => {
  try {
    return (
      (page.props && page.props.value && page.props.value.requests) ||
      (page.props && page.props.requests) ||
      []
    )
  } catch (e) {
    return []
  }
})

// DEBUG: show raw inertia props at mount
onMounted(() => {
  // show the top-level props object so you can inspect exact path
  console.log('INERTIA_PROPS_RAW', page.props?.value ?? page.props)
  console.log('INERTIA_PROPS_KEYS', Object.keys((page.props?.value ?? page.props) ?? {}))
  console.log('SERVER_REQUESTS (initial computed):', serverRequests.value)
})

// small debug to see what Inertia actually provides (remove in production)
console.log('INERTIA PROPS (sample):', page.props && (page.props.value ?? page.props))


// normalize server request into the shape used by UI (matching document request pattern)
function normalizeRequest(r) {
    if (!r) {
        return {
            id: null,
            name: 'None',
            profileImg: '/assets/DEFAULT.jpg',
            assistanceType: null,
            referenceCode: null,
            date: null,
            dateObj: new Date(),
            eventName: null,
            eventDescription: null,
            eventDate: null,
            eventTime: null,
            expectedAttendees: null,
            contact: null,
            venue: null,
            assistanceDetails: null,
            age: null,
            sex: null,
            civilStatus: null,
            address: null,
            status: null,
            start_datetime: null,
            end_datetime: null,
            created_at: null,
            attachments: [],
            extra_fields: {},
            user_info: {},
            credential_info: null,
        }
    }

    // Parse extra_fields if it's a string
    let parsedExtraFields = {}
    if (r.extra_fields) {
        if (typeof r.extra_fields === 'string') {
            try {
                parsedExtraFields = JSON.parse(r.extra_fields)
            } catch (e) {
                console.warn('Failed to parse extra_fields:', e)
                parsedExtraFields = {}
            }
        } else if (typeof r.extra_fields === 'object' && !Array.isArray(r.extra_fields)) {
            parsedExtraFields = { ...r.extra_fields }
        }
    }

    // Get attachments
    let requestAttachments = []
    if (r.attachments && Array.isArray(r.attachments) && r.attachments.length > 0) {
        requestAttachments = [...r.attachments]
    }

    // Build profile image URL
    let profileImgUrl = '/assets/DEFAULT.jpg'
    if (r.user_info?.profile_pic) {
        const profilePic = r.user_info.profile_pic
        if (String(profilePic).startsWith('http')) {
            profileImgUrl = profilePic
        } else {
            profileImgUrl = `/storage/${profilePic}`.replace('//', '/')
        }
    }

    const parsedDateObj = r?.dateObj ? new Date(r.dateObj) : (r?.created_at ? new Date(r.created_at) : new Date())
    
    return {
        id: r.id ?? null,
        name: r.name ?? 'None',
        first_name: r.first_name ?? null,
        middle_name: r.middle_name ?? null,
        last_name: r.last_name ?? null,
        suffix: r.suffix ?? null,
        profileImg: profileImgUrl,
        assistanceType: r.assistanceType ?? null,
        event_type: r.event_type ?? null,
        referenceCode: r.referenceCode ?? null,
        date: r.date ?? (r.created_at ? new Date(r.created_at).toLocaleDateString() : null),
        dateObj: parsedDateObj,
        eventName: r.eventName ?? r.purpose ?? null,
        eventDescription: r.eventDescription ?? r.other_purpose ?? null,
        eventDate: r.eventDate ?? null,
        eventTime: r.eventTime ?? null,
        expectedAttendees: r.expectedAttendees ?? null,
        contact: r.contact ?? null,
        venue: r.venue ?? r.event_location ?? null,
        assistanceDetails: r.assistanceDetails ?? null,
        age: r.age ?? null,
        sex: r.sex ?? null,
        civilStatus: r.civilStatus ?? null,
        address: r.address ?? null,
        birthdate: r.birthdate ?? null,
        status: r.status ?? null,
        start_datetime: r.start_datetime ?? null,
        end_datetime: r.end_datetime ?? null,
        created_at: r.created_at ?? null,
        attachments: requestAttachments,
        extra_fields: parsedExtraFields,
        user_info: r.user_info ?? {},
        credential_info: r.credential_info ?? null,
        hasValidId: r.hasValidId ?? false,
        validIdUrl: r.validIdUrl ?? null,
        valid_id_type: r.valid_id_type ?? null,
        valid_id_number: r.valid_id_number ?? null,
        purpose: r.purpose ?? null,
        other_purpose: r.other_purpose ?? null,
    }
}

// local reactive requests list (used by filters / modals)
const requests = ref([])

// populate requests initially and when serverRequests changes
const updateLocalRequests = () => {
    requests.value = (serverRequests.value || []).map(normalizeRequest)
    console.log('LOCAL_REQUESTS after normalization:', requests.value)
    // Quick mismatch detection: server has items but normalized result is empty
    if ((serverRequests.value || []).length > 0 && requests.value.length === 0) {
        console.warn('POSSIBLE NORMALIZATION_ISSUE: serverRequests has items but local normalized requests is empty. Inspect SERVER_REQUESTS and LOCAL_REQUESTS above.')
    }
}

// watch serverRequests but log changes for debugging
watch(serverRequests, (newVal, oldVal) => {
    console.log('SERVER_REQUESTS_CHANGED', { newVal, oldVal })
    updateLocalRequests()
}, { immediate: true })

// also watch local requests for changes (helpful when items are removed by approve/reject)
watch(requests, (newVal, oldVal) => {
    console.log('LOCAL_REQUESTS_CHANGED', { length: newVal.length, removedFrom: oldVal.length - newVal.length })
}, { deep: true })

watch(serverRequests, updateLocalRequests, { immediate: true })

const filteredRequests = computed(() => {
    let filtered = [...requests.value]

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) ||
            (item.event_type || item.assistanceType || '').toLowerCase().includes(query) ||
            item.eventName.toLowerCase().includes(query) ||
            item.referenceCode.toLowerCase().includes(query)
        )
    }

    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            (item.event_type || item.assistanceType || '').toLowerCase().includes(filterOption.value.toLowerCase())
        )
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'relevant') {
        filtered.sort((a, b) => {
            const urgentKeywords = ['urgent', 'asap', 'soonest', 'deadline', 'immediately']
            const aUrgent = urgentKeywords.some(keyword => 
                a.eventDescription.toLowerCase().includes(keyword)
            )
            const bUrgent = urgentKeywords.some(keyword => 
                b.eventDescription.toLowerCase().includes(keyword)
            )
            if (aUrgent && !bUrgent) return -1
            if (!aUrgent && bUrgent) return 1
            return b.dateObj - a.dateObj
        })
    }

    return filtered
})

/*
  Helper: show alert, try to close current window, fallback to redirect
*/
const closeEverythingAndExit = (message) => {
  try {
    // show blocking alert first
    window.alert(message)

    // try to close current tab/window (may be blocked by browser)
    // an extra trick: open blank in same tab then close it
    try {
      window.open('', '_self') // some browsers require the same-origin holder
      window.close()
    } catch (closeErr) {
      // ignore
      console.warn('window.close() failed', closeErr)
    }

    // After attempting to close — if the browser prevented close,
    // redirect as a fallback so the user leaves the page.
    // We use a small timeout so the close attempt runs first.
    setTimeout(() => {
      try {
        router.visit(route('event_request_approver'))
      } catch (e) {
        // final fallback - hard redirect
        window.location.href = '/'
      }
    }, 300)
  } catch (err) {
    console.error('closeEverythingAndExit error', err)
  }
}

const toggleSettings = () => showSettings.value = !showSettings.value
const closeSettings = () => showSettings.value = false
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
    router.visit(route('login_approver'))
}
const performSearch = () => console.log('Performing search:', searchQuery.value)

// Helper function to get attachment URL consistently
const getAttachmentUrl = (attachment) => {
  if (!attachment) return null
  
  // Prefer file_url if available (from accessor)
  if (attachment.file_url) {
    const url = attachment.file_url
    if (url.startsWith('http://') || url.startsWith('https://')) {
      return url
    }
    if (url.startsWith('/storage/')) {
      return url
    }
    return `/storage/${url}`
  }
  
  // Fallback to file_path
  if (attachment.file_path) {
    const path = attachment.file_path
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path
    }
    if (path.startsWith('/storage/')) {
      return path
    }
    const cleanPath = path.startsWith('public/') ? path.substring(7) : path
    return `/storage/${cleanPath}`
  }
  
  return null
}

// Helper to format file size
const formatFileSize = (bytes) => {
  if (!bytes || bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
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

// Helper to format field names
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

// Helper to get valid ID type name
const getValidIdTypeName = (typeId) => {
  if (!typeId) return 'Not specified'
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

// Helper function to safely get extra_fields as an object
const getExtraFieldsObject = (extraFields) => {
  if (!extraFields) return {}
  
  if (typeof extraFields === 'object' && !Array.isArray(extraFields)) {
    return extraFields
  }
  
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

// Format date helper
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    if (Number.isNaN(d.getTime())) return dateStr
    
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

// Valid ID image URL
const validIdImageUrl = ref('')

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

const openModal = async (request) => {
    // Parse extra_fields if needed
    let parsedExtraFields = {}
    if (request.extra_fields) {
      if (typeof request.extra_fields === 'string') {
        try {
          parsedExtraFields = JSON.parse(request.extra_fields)
        } catch (e) {
          console.warn('Failed to parse extra_fields:', e)
          parsedExtraFields = {}
        }
      } else if (typeof request.extra_fields === 'object' && !Array.isArray(request.extra_fields)) {
        parsedExtraFields = { ...request.extra_fields }
      }
    }
    
    // Get attachments from request
    let requestAttachments = []
    if (request.attachments && Array.isArray(request.attachments) && request.attachments.length > 0) {
      requestAttachments = [...request.attachments]
    }
    
    selectedRequest.value = {
      ...request,
      extra_fields: parsedExtraFields,
      attachments: requestAttachments,
    }
    
    validIdImageUrl.value = ''
    
    // Fetch valid ID image if available
    if (request.hasValidId || request.validIdUrl) {
      try {
        const requestId = request.id || request.event_assist_request_id
        let url = ''
        try {
          if (typeof route === 'function') {
            url = route('event_requests.valid_id', { id: requestId })
          } else {
            url = `/approver/event-requests/${requestId}/valid-id`
          }
        } catch (e) {
          url = `/approver/event-requests/${requestId}/valid-id`
        }

        const res = await axios.get(url, { responseType: 'blob' })
        const blob = res.data
        const objUrl = URL.createObjectURL(blob)
        validIdImageUrl.value = objUrl
      } catch (err) {
        console.error('Failed to fetch valid id:', err)
      }
    }
    
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedRequest.value = null
    // Cleanup valid ID image URL
    if (validIdImageUrl.value) {
      try {
        URL.revokeObjectURL(validIdImageUrl.value)
      } catch (e) {
        // Ignore cleanup errors
      }
      validIdImageUrl.value = ''
    }
}

const openApprovalModal = () => {
    isModalOpen.value = false
    isApprovalModalOpen.value = true
}

const closeApprovalModal = () => {
    isApprovalModalOpen.value = false
    approvalDetails.value = {
        comment: ''
    }
    isModalOpen.value = true
}

const confirmApproval = async () => {
    if (isSubmittingApproval.value) return
    isSubmittingApproval.value = true

    // same validations...
    // Validate comment is provided (required)
    if (!approvalDetails.value.comment || approvalDetails.value.comment.trim() === '') {
        alert("Please provide a comment/feedback. This will be displayed to the resident.")
        isSubmittingApproval.value = false
        return
    }

    const payload = {
        comment: approvalDetails.value.comment
    }

    try {
        // route name set in web.php
        await router.post(
        `/approver/event-requests/${selectedRequest.value.id}/approve`,
        payload,
        {
            preserveState: false,
            onSuccess: () => {
                // remove request from local list
                const index = requests.value.findIndex(r => r.id === selectedRequest.value.id)
                if (index > -1) requests.value.splice(index, 1)

                // close modals / reset local fields
                isApprovalModalOpen.value = false
                selectedRequest.value = null
                comment.value = ''
                approvalDetails.value = {
                    comment: ''
                }

                // show alert and attempt to close/redirect
                closeEverythingAndExit('Request approved successfully.')
            },
            onError: (errors) => {
                console.error('Approval error', errors)
                alert('Failed to approve. Check console for details.')
            }
        }
        )
    } catch (err) {
        console.error('Approve exception', err)
        alert('An error occurred while approving.')
    }
}

const openRejectionModal = () => {
    isModalOpen.value = false
    isRejectionModalOpen.value = true
}

const closeRejectionModal = () => {
    isRejectionModalOpen.value = false
    rejectionReason.value = ''
    isModalOpen.value = true
}

const confirmRejection = async () => {
    if (isSubmittingRejection.value) return
    isSubmittingRejection.value = true

    if (!rejectionReason.value.trim()) {
        alert("Please provide a reason for rejection")
        isSubmittingRejection.value = false
        return
    }

    try {
        await router.post(
        `/approver/event-requests/${selectedRequest.value.id}/reject`,
        { reason: rejectionReason.value },
        {
            preserveState: false,
            onSuccess: () => {
                // remove request from local list
                const index = requests.value.findIndex(r => r.id === selectedRequest.value.id)
                if (index > -1) requests.value.splice(index, 1)

                // close modals / reset local fields
                isRejectionModalOpen.value = false
                selectedRequest.value = null
                rejectionReason.value = ''

                // show alert and attempt to close/redirect
                closeEverythingAndExit('Request rejected successfully.')
            },
            onError: (errors) => {
                console.error('Rejection error', errors)
                alert('Failed to reject. Check console for details.')
            }
        }
        )
    } catch (err) {
        console.error('Reject exception', err)
        alert('An error occurred while rejecting.')
    }
}

const navigateToDocumentRequest = () => router.visit(route('document_request_approver'))
const navigateToHistory = () => router.visit(route('history_approver'))

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) showSettings.value = false
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showFilterDropdown.value = false
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
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
    border-radius: 20px;
    padding: 30px;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    z-index: 10000 !important;
    visibility: visible !important;
    opacity: 1 !important;
    display: block !important;
}

.approval-modal, .rejection-modal {
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
    align-items: left;
}

.upload-btn {
    background: #ff7a28;;
    color: white;
    border: none;
    padding: 5px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
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
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

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
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.cancel-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
}

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

.requests-container::-webkit-scrollbar,
.modal-container::-webkit-scrollbar {
    width: 6px;
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

.attachment-item:hover {
    background: #e8f8ed;
    border-color: #239640;
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
    
    .requests-container {
        grid-template-columns: 1fr;
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