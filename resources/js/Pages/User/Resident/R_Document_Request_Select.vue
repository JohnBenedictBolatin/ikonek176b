<template>
    <Head>
        <title>Document Request</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <button type="button" class="settings-burger-btn" @click="toggleSettings" aria-label="Settings">
                    <svg class="settings-burger-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                    <div v-if="showSettings" class="settings-dropdown">
                        <a href="#" class="settings-item" @click.prevent.stop="openTermsModal">TERMS & CONDITIONS</a>
                        <Link href="#" class="settings-item" @click.prevent="logout">SIGN OUT</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img :src="profilePictureUrl" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user?.name || 'Unknown User' }}</div>
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        Posts
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                        :class="{ active: activeTab === 'documents' }"
                        @click="setActiveTab('documents')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'events' }"
                        @click="navigateToEvents"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'notifications' }"
                        @click="navigateToNotifications"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        Notifications
                        <span v-if="unreadCount > 0" class="unread-badge-nav">{{ unreadCount }}</span>
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'profile' }"
                        @click="navigateToProfile"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Profile
                    </Link>
                </div>

                <button class="faq-btn" @click="openFAQ">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                    FAQS & HELP CENTER
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="main-content">
                    <!-- Header -->
                    <div class="document-header">
                        <h2>Document Request</h2>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Restriction Banner -->
                    <div v-if="restrictions?.restrict_document_request" class="restriction-banner">
                        <div class="restriction-content">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px; color: #e74c3c; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <div class="restriction-text">
                                <strong>Access Restricted</strong>
                                <p>You are restricted from making document requests. Please contact the admin for more information.</p>
                            </div>
                        </div>
                    </div>

                    <!-- View 0: Requests List (Default) -->
                    <div v-if="currentView === 'list'" class="requests-list-view">
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
                                    </div>
                                </div>
                                <div class="filter-dropdown-wrapper" style="margin-left: 15px;">
                                    <button class="filter-dropdown-btn" @click="toggleStatusDropdown">
                                        {{ statusFilter === 'all' ? 'ALL STATUS' : statusFilter.toUpperCase() }}
                                        <span class="filter-arrow" :class="{ rotated: showStatusDropdown }">▼</span>
                                    </button>
                                    <div v-if="showStatusDropdown" class="filter-dropdown-menu">
                                        <button @click="selectStatus('all')" :class="{ active: statusFilter === 'all' }">ALL STATUS</button>
                                        <button @click="selectStatus('pending')" :class="{ active: statusFilter === 'pending' }">PENDING</button>
                                        <button @click="selectStatus('approved')" :class="{ active: statusFilter === 'approved' }">APPROVED</button>
                                        <button @click="selectStatus('rejected')" :class="{ active: statusFilter === 'rejected' }">REJECTED</button>
                                        <button @click="selectStatus('resubmitted')" :class="{ active: statusFilter === 'resubmitted' }">RESUBMITTED</button>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="search-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                    </button>
                                </div>
                                <button 
                                    type="button"
                                    class="request-new-btn" 
                                    :class="{ 'disabled': restrictions?.restrict_document_request }"
                                    @click.stop="showRequestForm"
                                    :disabled="restrictions?.restrict_document_request"
                                >
                                    ＋ REQUEST NEW DOCUMENT
                                </button>
                            </div>
                        </div>

                        <div class="requests-table-container">
                            <table class="requests-table">
                                <thead>
                                    <tr>
                                        <th>Document Requested</th>
                                        <th>Request Number</th>
                                        <th>Date and Time</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="request in paginatedDocumentRequests" 
                                        :key="request.id"
                                        :data-request-id="request.id"
                                        @click="viewRequestDetails(request)"
                                        class="request-row"
                                    >
                                        <td>{{ request.title }}</td>
                                        <td>#{{ request.requestNumber }}</td>
                                        <td>{{ request.date }} | {{ request.time }}</td>
                                        <td>
                                            <span class="status-badge" :class="request.status.toLowerCase()">
                                                <span class="badge-icon">
                                                    <template v-if="request.status.toLowerCase() === 'pending' || request.status.toLowerCase() === 'resubmitted'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="badge-icon-svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                    </template>
                                                    <template v-else-if="request.status.toLowerCase() === 'approved'">✓</template>
                                                    <template v-else-if="request.status.toLowerCase() === 'rejected'">✕</template>
                                                </span>
                                                <span class="badge-text">{{ request.status }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <div v-if="request.status === 'APPROVED'">
                                                <div
                                                    v-if="getPaymentBadge(request)"
                                                    :class="['payment-list-badge', getPaymentBadge(request).cls]"
                                                    role="status"
                                                    aria-live="polite"
                                                >
                                                    <span class="badge-icon" aria-hidden="true">
                                                        <template v-if="getPaymentBadge(request).cls === 'pending'">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="badge-icon-svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                            </svg>
                                                        </template>
                                                        <template v-else-if="getPaymentBadge(request).cls === 'approved'">✓</template>
                                                        <template v-else-if="getPaymentBadge(request).cls === 'rejected'">✕</template>
                                                    </span>
                                                    <span class="badge-text">{{ getPaymentBadge(request).label }}</span>
                                                </div>
                                                <div v-else class="payment-list-badge none">
                                                    <span class="badge-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                        </svg>
                                                    </span>
                                                    <span class="badge-text">NOT PAID YET</span>
                                                </div>
                                            </div>
                                            <span v-else class="payment-status-na">—</span>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredDocumentRequests.length === 0">
                                        <td colspan="5" class="no-requests">No document requests found</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Pagination Controls -->
                            <div v-if="filteredDocumentRequests.length > 0" class="pagination-container">
                                <div class="pagination-info">
                                    Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredDocumentRequests.length) }} of {{ filteredDocumentRequests.length }} requests
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

                    <!-- View 1: Document Selection -->
                    <div v-if="currentView === 'selection'" class="document-selection">
                        <div class="document-types-wrapper">
                            <button type="button" class="back-btn-selection" @click.stop="backToList">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                BACK TO REQUESTS
                            </button>
                            <div class="document-types">
                                <button 
                                    v-for="doc in documentTypesWithStatus" 
                                    :key="doc.name"
                                    type="button"
                                    class="doc-type-btn" 
                                    :class="{ 
                                        active: selectedDocType === doc.name,
                                        disabled: doc.restricted
                                    }" 
                                    :disabled="doc.restricted"
                                    :title="doc.restricted ? 'This document type is restricted due to past offenses. Please contact the admin for more information.' : ''"
                                    @click.stop="handleDocumentSelect(doc)"
                                >
                                    {{ doc.name }}
                                    <span v-if="doc.restricted" class="restricted-badge" title="Restricted">🔒</span>
                                </button>
                            </div>
                        </div>

                        <div class="document-info">
                            <h3 class="doc-title">{{ displayDocumentType }}</h3>
                            <div class="doc-description">
                                <p>{{ documentDescriptions[selectedDocType] }}</p>
                            </div>

                            <!-- Permit Type Selection (only shown when Permit is selected) -->
                            <div v-if="selectedDocType === 'Permit'" class="permit-type-selection">
                                <h4 class="permit-type-title">Select Permit Type:</h4>
                                <div class="permit-type-options">
                                    <button 
                                        class="permit-type-btn" 
                                        :class="{ active: selectedPermitType === 'Building Permit' }"
                                        @click="selectPermitType('Building Permit')"
                                    >
                                        Building Permit
                                    </button>
                                    <button 
                                        class="permit-type-btn" 
                                        :class="{ active: selectedPermitType === 'Business Permit' }"
                                        @click="selectPermitType('Business Permit')"
                                    >
                                        Business Permit
                                    </button>
                                </div>
                                <div v-if="selectedPermitType" class="permit-type-description">
                                    <p><strong>{{ selectedPermitType }}:</strong> {{ permitTypeDescriptions[selectedPermitType] }}</p>
                                </div>
                            </div>

                            <div class="requirements-section">
                                <h4>REQUIREMENTS</h4>
                                <ol class="requirements-list">
                                    <li v-for="(req, index) in documentRequirements[selectedDocType]" :key="index">
                                        {{ req }}
                                    </li>
                                </ol>
                            </div>

                            <button 
                                type="button"
                                class="request-btn" 
                                @click.stop="handleProceedToForm"
                                :disabled="!selectedDocType || (selectedDocType === 'Permit' && !selectedPermitType)"
                            >
                                REQUEST DOCUMENT
                            </button>
                        </div>
                    </div>

                    <!-- View 2: Request Form -->
                    <div v-if="currentView === 'form'" class="request-form-container">
                        <div class="form-header-row">
                            <button class="back-btn" @click="backToSelection">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                                BACK TO DOCUMENT SELECTION
                            </button>
                            <p class="form-document-type">Document Type: <strong>{{ displayDocumentType }}</strong></p>
                        </div>

                        <h3 class="form-title">REQUEST FORM</h3>

                        <div class="form-sections">
                            <!-- Proof of Intent -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Purpose <span class="required-star">*</span>
                                </h4>
                                
                                <!-- Purpose and ID Type side by side -->
                                <div class="purpose-id-row">
                                    <div class="purpose-field">
                                        <!-- Select dropdown with predefined options for all document types -->
                                        <select 
                                            v-model="form.purpose" 
                                            class="form-input"
                                            required
                                            @change="handlePurposeChange"
                                        >
                                            <option value="">Select purpose</option>
                                            <option v-for="purpose in currentDocumentPurposes" :key="purpose" :value="purpose">
                                                {{ purpose }}
                                            </option>
                                        </select>
                                        <!-- Show text input when "Others" is selected -->
                                        <input
                                            v-if="form.purpose === 'Others'"
                                            v-model="purposeOthers"
                                            @input="form.purpose = purposeOthers"
                                            placeholder="Please specify your purpose..."
                                            class="form-input"
                                            style="margin-top: 10px;"
                                            required
                                        />
                                        <p v-if="form.purpose === 'Others'" style="margin-top: 8px; font-size: 14px; color: #333; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; color: #e74c3c; flex-shrink: 0;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                            Please provide a purpose similar to the existing options (e.g., "Medical Appointment", "Loan Application").
                                        </p>
                                    </div>
                                    
                                    <div class="id-type-field">
                                        <!-- ID Type select and Upload buttons side by side -->
                                        <div class="id-type-upload-row">
                                            <select v-model="form.id_type" class="form-input id-type-select">
                                                <option value="">Type of Identification</option>
                                                <option value="National ID">National ID</option>
                                                <option value="Driver's License">Driver's License</option>
                                                <option value="Passport">Passport</option>
                                                <option value="Voter's ID">Voter's ID</option>
                                                <option value="SSS ID">SSS ID</option>
                                                <option value="UMID">UMID</option>
                                            </select>
                                            
                                            <div class="upload-row">
                                                <button class="upload-btn" @click.prevent="triggerFileUpload('front')">
                                                    UPLOAD FRONT
                                                </button>
                                                <input 
                                                    type="file" 
                                                    ref="fileFrontInput" 
                                                    @change="handleFileUpload($event, 'front')" 
                                                    class="file-input-hidden"
                                                    accept="image/*,.pdf"
                                                    style="display: none"
                                                />

                                                <button class="upload-btn" @click.prevent="triggerFileUpload('back')">
                                                    UPLOAD BACK
                                                </button>
                                                <input 
                                                    type="file" 
                                                    ref="fileBackInput" 
                                                    @change="handleFileUpload($event, 'back')" 
                                                    class="file-input-hidden"
                                                    accept="image/*,.pdf"
                                                    style="display: none"
                                                />
                                            </div>
                                        </div>
                                        
                                        <!-- File name display -->
                                        <div class="uploaded-files-display">
                                            <p v-if="idFrontName" class="uploaded-file">Front: {{ idFrontName }}</p>
                                            <p v-if="idBackName" class="uploaded-file">Back: {{ idBackName }}</p>
                                        </div>
                                        
                                        <!-- ID number field appears only when an ID type is chosen AND at least one file uploaded -->
                                        <div v-if="showIdNumber" class="id-number-field" style="margin-top: 12px;">
                                            <label class="field-label">{{ idNumberLabel }} <span>*</span></label>
                                            <input
                                                type="text"
                                                v-model="form.id_number"
                                                placeholder="Enter ID Number"
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic fields for the selected document -->
                            <div class="form-section dynamic-fields" v-if="currentDocumentFields.length">
                                <h4 class="section-title">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Additional Information & Required Documents
                                </h4>

                                <!-- Special layout for Barangay Certificate -->
                                <template v-if="selectedDocType === 'Barangay Certificate'">
                                    <div class="dynamic-field-wrapper">
                                        <div class="field-header">
                                            <label class="field-label">
                                                {{ currentDocumentFields.find(f => f.name === 'duration_of_residency')?.label }}
                                            </label>
                                        </div>
                                        <div class="field-input-wrapper">
                                            <input 
                                                type="number"
                                                v-model.number="form.extra_fields.duration_of_residency"
                                                placeholder="Enter number of years"
                                                :min="0"
                                                :step="1"
                                                class="form-input"
                                            />
                                        </div>
                                    </div>
                                </template>

                                <!-- Non-file fields (text, select, number, etc.) - for other document types -->
                                <div v-for="field in currentDocumentFields.filter(f => f.type !== 'file')" :key="field.name" class="dynamic-field-wrapper" v-if="selectedDocType !== 'Barangay Certificate'">
                                    <div class="field-header">
                                        <label class="field-label">
                                            {{ field.label }} 
                                            <span v-if="field.required" class="required-star">*</span>
                                        </label>
                                        <p v-if="field.description" class="field-description">{{ field.description }}</p>
                                    </div>

                                    <div class="field-input-wrapper">
                                        <!-- text -->
                                        <input 
                                            v-if="field.type === 'text'"
                                            v-model="form.extra_fields[field.name]"
                                            :placeholder="field.placeholder || ''"
                                            class="form-input"
                                            :required="field.required"
                                        />

                                        <!-- textarea -->
                                        <textarea
                                            v-if="field.type === 'textarea'"
                                            v-model="form.extra_fields[field.name]"
                                            :placeholder="field.placeholder || ''"
                                            rows="3"
                                            class="form-textarea"
                                            :required="field.required"
                                        ></textarea>

                                        <!-- date -->
                                        <input
                                            v-if="field.type === 'date'"
                                            type="date"
                                            v-model="form.extra_fields[field.name]"
                                            :max="field.max || today"
                                            class="form-input"
                                            :required="field.required"
                                        />

                                        <!-- number -->
                                        <input
                                            v-if="field.type === 'number'"
                                            type="number"
                                            v-model.number="form.extra_fields[field.name]"
                                            :placeholder="field.placeholder || ''"
                                            :min="field.min !== undefined ? field.min : undefined"
                                            :step="field.step !== undefined ? field.step : undefined"
                                            class="form-input"
                                            :required="field.required"
                                            @keypress="(e) => { if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'Tab') e.preventDefault(); }"
                                        />

                                        <!-- select -->
                                        <select
                                            v-if="field.type === 'select'"
                                            v-model="form.extra_fields[field.name]"
                                            class="form-input"
                                            :required="field.required"
                                        >
                                            <option value="">{{ field.placeholder || 'Select an option' }}</option>
                                            <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                        </select>
                                        
                                        <!-- Text input for "Other" option in occupation field -->
                                        <input
                                            v-if="field.name === 'occupation' && form.extra_fields[field.name] === 'Other'"
                                            type="text"
                                            v-model="form.extra_fields.occupation_other"
                                            placeholder="Please specify your occupation or profession"
                                            class="form-input"
                                            style="margin-top: 10px;"
                                            :required="field.required && form.extra_fields[field.name] === 'Other'"
                                        />

                                        <!-- checkbox group -->
                                        <div v-if="field.type === 'checkbox'" class="checkbox-group">
                                            <label v-for="opt in field.options" :key="opt" class="checkbox-label">
                                                <input
                                                    type="checkbox"
                                                    :value="opt"
                                                    @change="toggleCheckbox(field.name, opt, $event.target.checked)"
                                                    :checked="Array.isArray(form.extra_fields[field.name]) && form.extra_fields[field.name].includes(opt)"
                                                />
                                                <span>{{ opt }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- File upload fields - displayed in grid (excluding Barangay Certificate) -->
                                <div v-if="currentDocumentFields.filter(f => f.type === 'file' && selectedDocType !== 'Barangay Certificate').length > 0" class="file-uploads-grid">
                                    <div v-for="field in currentDocumentFields.filter(f => f.type === 'file' && selectedDocType !== 'Barangay Certificate')" :key="field.name" class="file-upload-item">
                                        <div class="file-upload-header">
                                            <label class="file-upload-label">
                                                {{ field.label }} 
                                                <span v-if="field.required" class="required-star">*</span>
                                            </label>
                                            <p v-if="field.description" class="file-upload-description">{{ field.description }}</p>
                                        </div>
                                        <div class="file-upload-controls">
                                            <button 
                                                type="button"
                                                class="upload-btn-dynamic" 
                                                @click.prevent="triggerDynamicFileUpload(field.name)"
                                            >
                                                UPLOAD
                                            </button>
                                            <input
                                                type="file"
                                                :data-dyn-field="field.name"
                                                style="display: none"
                                                @change="handleDynamicFileUpload($event, field.name)"
                                                :accept="field.accept || 'image/*,.pdf'"
                                            />
                                            <div v-if="dynamicFileNames[field.name]" class="uploaded-file-info-compact">
                                                <svg class="file-checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 18px; height: 18px; color: #4caf50;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span class="file-name-compact">{{ dynamicFileNames[field.name] }}</span>
                                                <button 
                                                    type="button"
                                                    class="remove-file-btn-small" 
                                                    @click="removeDynamicFile(field.name)"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Error Message Display -->
                        <div v-if="submitError" class="error-message-container" style="margin-top: 20px; margin-bottom: 15px;">
                            <div class="error-alert" style="background: #fee; border: 1px solid #fcc; border-radius: 8px; padding: 15px; color: #c33;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px; flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span style="font-weight: 600;">{{ submitError }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Field Error Messages -->
                        <div v-if="formErrors && Object.keys(formErrors).length > 0" class="field-errors-container" style="margin-bottom: 15px;">
                            <div v-for="(error, field) in formErrors" :key="field" class="field-error" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 10px; margin-bottom: 8px; color: #856404; font-size: 14px;">
                                <strong>{{ field.replace('_', ' ').replace('extra_fields.', '') }}:</strong> {{ error }}
                            </div>
                        </div>

                        <button
                        type="button"
                        class="submit-btn"
                        :disabled="isSubmitting || restrictions?.restrict_document_request"
                        @click="submitRequest"
                        :title="restrictions?.restrict_document_request ? 'You are restricted from making document requests. Please contact the admin for more information.' : ''"
                        >
                            <span v-if="!isSubmitting">SUBMIT</span>
                            <span v-else>Submitting...</span>
                        </button>
                    </div>

                    <!-- View 3: Success Confirmation -->
                    <div v-if="currentView === 'success'" class="success-container">
                        <div class="success-content">
                            <div class="checkmark">
                                ✓
                            </div>
                            <h3 class="success-title">REQUEST SUBMITTED</h3>
                            <p class="request-number">REQUEST NO. #{{ requestNumber }}</p>
                            
                            <div class="success-message">
                                <p>You have successfully submitted your request form to acquire a <span class="highlight">{{ displayDocumentType }}</span>.</p>
                                <p>Please wait while we process your request — it is now under review by the barangay. Thank you for your patience!</p>
                                <p>Your request number is <span class="highlight">#{{ requestNumber }}</span>. Please note.</p>
                            </div>

                            <button class="view-request-btn" @click="viewRequest">
                                VIEW REQUEST
                            </button>
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
            <div class="modal-icon" :class="(selectedRequest?.status === 'RESUBMITTED' ? 'pending' : selectedRequest?.status.toLowerCase()) + '-icon'">
                <div class="status-badge">
                    <span v-if="selectedRequest?.status === 'APPROVED'">✓</span>
                    <span v-if="selectedRequest?.status === 'PENDING' || selectedRequest?.status === 'RESUBMITTED'">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width: 32px; height: 32px; display: block; margin: 0 auto;">
                        <circle cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" d="M12 6v6l4 2"/>
                      </svg>
                    </span>
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
                
                <!-- APPROVED Status - Show pickup instructions -->
                <div v-if="selectedRequest?.status === 'APPROVED'" class="accepted-section">
                    <p class="details-message">
                        You may now proceed to claim your certificate by following the instructions provided.
                    </p>

                    <div class="pickup-info">
                        <div class="info-item">
                            <span class="info-label">WHAT:</span>
                            <span class="info-value">
                                PICKUP OF DOCUMENT ({{ selectedRequest?.pickup_item ?? selectedRequest?.title }})
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">WHERE:</span>
                            <span class="info-value">
                                {{ selectedRequest?.pickup_location ?? selectedRequest?.raw?.pickup_location ?? 'BARANGAY 176B BARANGAY HALL' }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">WHEN:</span>
                            <span class="info-value">{{ getPickupSchedule(selectedRequest) }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">WHO:</span>
                            <span class="info-value">
                                {{ selectedRequest?.person_to_look ?? selectedRequest?.raw?.person_to_look ?? user?.name ?? 'N/A' }}
                            </span>
                        </div>

                        <div class="info-item" v-if="selectedRequest?.type === 'document'">
                            <span class="info-label">AMOUNT:</span>
                            <span class="info-value amount">₱{{ (selectedRequest?.amount ?? selectedRequest?.processing_fee) || '0.00' }}</span>
                        </div>
                    </div>

                    <!-- Payment Buttons for Document Requests -->
                    <div v-if="selectedRequest?.type === 'document' && 
                              (!selectedRequest?.payment || 
                                selectedRequest?.payment?.status?.toUpperCase() === 'REJECTED')" 
                        class="payment-buttons-modal">
                        <button class="pay-online-btn-modal" @click="showPaymentGateway(selectedRequest)">
                            PAY ONLINE
                        </button>
                        <button class="pay-onsite-btn-modal" @click="acknowledgeOnsite">
                            PAY ONSITE
                        </button>
                    </div>

                    <!-- Payment Status block -->
                    <div v-if="selectedRequest?.type === 'document'" class="payment-status-section">
                        <h4 class="section-title-payment">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v12.75A2.25 2.25 0 0 0 4.5 21.75Z" />
                                </svg>
                            </span> Payment Status
                        </h4>

                        <!-- If there's a payment object attached: show status details -->
                        <div v-if="selectedRequest?.payment" class="payment-info-card">
                            <!-- When payment is pending, show badge and amount side by side -->
                            <div v-if="selectedRequest.payment.status?.toLowerCase() === 'pending'" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 16px;">
                                <div :class="['payment-status-badge', selectedRequest.payment.status?.toLowerCase()]">
                                    <span class="badge-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; display: inline-block; vertical-align: middle; margin: 0;">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path stroke-linecap="round" d="M12 6v6l4 2"/>
                                        </svg>
                                    </span>
                                    <span class="badge-text">{{ formatPaymentStatus(selectedRequest.payment.status) }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-left: auto;">
                                    <span style="font-weight: 600; color: #374151;">Amount:</span>
                                    <span style="font-weight: 700; color: #1a1a1a; font-size: 18px;">₱{{ (selectedRequest.payment.amount ?? selectedRequest.amount) || '0.00' }}</span>
                                </div>
                            </div>

                            <!-- When payment is approved/rejected, show badge separately -->
                            <div v-else>
                                <div :class="['payment-status-badge', selectedRequest.payment.status?.toLowerCase()]">
                                    <span class="badge-icon">
                                        <template v-if="selectedRequest.payment.status?.toLowerCase() === 'approved' || selectedRequest.payment.status?.toLowerCase() === 'paid'">✓</template>
                                        <template v-else-if="selectedRequest.payment.status?.toLowerCase() === 'rejected'">✕</template>
                                    </span>
                                    <span class="badge-text">{{ formatPaymentStatus(selectedRequest.payment.status) }}</span>
                                </div>
                            </div>

                            <div class="payment-details-grid">
                                <!-- Only show amount in grid if payment is NOT pending (since it's shown above for pending) -->
                                <div v-if="selectedRequest.payment.status?.toLowerCase() !== 'pending'" class="payment-detail-item">
                                    <span class="detail-label">Amount:</span>
                                    <span class="detail-value">₱{{ (selectedRequest.payment.amount ?? selectedRequest.amount) || '0.00' }}</span>
                                </div>

                                <div v-if="selectedRequest.payment.transaction_ref" class="payment-detail-item">
                                    <span class="detail-label">Transaction Ref:</span>
                                    <span class="detail-value">{{ selectedRequest.payment.transaction_ref }}</span>
                                </div>

                                <div v-if="selectedRequest.payment.paid_at && (selectedRequest.payment.status?.toLowerCase() === 'approved' || selectedRequest.payment.status?.toLowerCase() === 'paid')" class="payment-detail-item">
                                    <span class="detail-label">Paid At:</span>
                                    <span class="detail-value">{{ formatDateTime(selectedRequest.payment.paid_at) }}</span>
                                </div>

                                <div v-if="selectedRequest.payment.receipt_path || selectedRequest.payment.receipt_image" class="payment-detail-item">
                                    <span class="detail-label">Receipt:</span>
                                    <span class="detail-value">
                                        <button @click="viewReceipt(selectedRequest)" class="receipt-view-btn">VIEW/DOWNLOAD RECEIPT</button>
                                    </span>
                                </div>
                            </div>

                            <!-- Change Payment Method Button for Cash/Onsite payments -->
                            <div v-if="canChangePaymentMethod(selectedRequest)" class="change-payment-method-section">
                                <button @click="showPaymentGateway(selectedRequest)" class="change-payment-method-btn">
                                    CHANGE PAYMENT METHOD
                                </button>
                            </div>
                        </div>

                        <!-- If no payment: prompt to pay -->
                        <div v-else class="no-payment-card">
                            <p>No payment record found. You can <strong>Pay Online</strong> or <strong>Pay Onsite</strong> above.</p>
                        </div>
                    </div>

                    <p class="present-message">
                        <strong>Present this request number upon pickup:</strong><br/>
                        <span class="highlight-number">#{{ selectedRequest?.requestNumber }}</span>
                    </p>
                </div>

                <!-- PENDING/RESUBMITTED Status -->
                <div v-if="selectedRequest?.status === 'PENDING' || selectedRequest?.status === 'RESUBMITTED'" class="pending-section">
                    <p class="details-message" v-if="selectedRequest?.status === 'RESUBMITTED'">
                        Your resubmitted request is currently being reviewed by the barangay officials. You will be notified once a decision has been made.
                    </p>
                    <p class="details-message" v-else>
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

                    <!-- Admin Feedback/Comment Section - Only show for rejected requests -->
                    <div v-if="selectedRequest?.admin_feedback || selectedRequest?.raw?.admin_feedback" class="feedback-section">
                        <h4 class="section-title-feedback">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                </svg>
                            </span> Approver Comment
                        </h4>
                        <div class="feedback-box">
                            <p>{{ selectedRequest?.admin_feedback ?? selectedRequest?.raw?.admin_feedback }}</p>
                        </div>
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
                        You can edit and resubmit this request by clicking the button below, or contact Ms. Mercy Alpaño at the barangay hall for assistance.
                    </p>
                    
                    <div class="appeal-actions">
                        <button @click="appealRequest" class="appeal-btn">
                            EDIT REQUEST
                        </button>
                    </div>
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

    <!-- QR Modal -->
    <div v-if="showQRModal" class="modal-overlay" @click="closeQRModal">
        <div class="modal-content qr-modal" @click.stop>
            <button class="close-modal-btn" @click="closeQRModal">✕</button>

            <h3 class="modal-title">Pay via QR - {{ selectedGateway }}</h3>
            <div v-if="paymentSuccess" class="payment-success-banner">
                <p><strong>Payment is successful ✅</strong></p>
            </div>

            <p class="modal-subtitle">Scan the QR code below to pay online</p>
            <p class="modal-subtitle">Click <strong>Upload Payment Screenshot</strong> to complete your payment.</p>

            <div class="qr-preview">
                <div class="qr-image-wrapper">
                    <img v-if="selectedGateway === 'GCash'" src="/assets/GQR.png" alt="GCash QR" class="qr-image" />
                    <img v-else-if="selectedGateway === 'Maya'" src="/assets/MQR.png" alt="Maya QR" class="qr-image" />
                    <div v-else class="qr-placeholder">NO QR</div>
                </div>
            </div>

            <div class="upload-section">
                <button class="upload-btn-payment" @click="triggerPaymentUpload">
                    UPLOAD
                </button>
                <input 
                    type="file" 
                    ref="paymentFileInput"
                    accept="image/*" 
                    @change="onFileChange" 
                    style="display: none"
                />

                <div v-if="uploadedFile" class="uploaded-file-info-compact">
                    <span class="file-checkmark">✓</span>
                    <span class="file-name-compact">{{ uploadedFile?.name }}</span>
                    <button 
                        type="button"
                        class="remove-file-btn-small" 
                        @click="clearEvidence"
                        :disabled="uploading"
                    >
                        ✕
                    </button>
                </div>

                <div v-if="uploadedFile" class="evidence-form">
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
                            {{ uploading ? 'UPLOADING...' : 'SUBMIT' }}
                        </button>

                        <button 
                            class="clear-evidence-btn" 
                            @click="clearEvidence" 
                            :disabled="uploading"
                        >
                            CLEAR
                        </button>
                    </div>
                </div>
            </div>

            <p class="note-message small">After submission, your payment will be reviewed by the Barangay Treasurer.</p>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div v-if="showReceiptModal" class="modal-overlay" @click="closeReceiptModal">
        <div class="receipt-modal-container" @click.stop>
            <button @click="closeReceiptModal" class="modal-close">✕</button>
            
            <div class="receipt-modal-content">
                <h3 class="receipt-title">Digital Receipt</h3>
                
                <div class="receipt-details-box">
                    <div class="receipt-row">
                        <span class="receipt-label">Request No:</span>
                        <span class="receipt-value">{{ selectedReceipt?.requestNumber || 'N/A' }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Document:</span>
                        <span class="receipt-value">{{ selectedReceipt?.title || 'N/A' }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Date:</span>
                        <span class="receipt-value">{{ selectedReceipt?.date || 'N/A' }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Payer:</span>
                        <span class="receipt-value">{{ user?.name || 'N/A' }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Amount Paid:</span>
                        <span class="receipt-value amount">₱{{ (selectedReceipt?.payment?.amount ?? selectedReceipt?.amount)?.toFixed(2) || '0.00' }}</span>
                    </div>
                    <div class="receipt-row" v-if="selectedReceipt?.payment?.transaction_ref">
                        <span class="receipt-label">Transaction ID:</span>
                        <span class="receipt-value">{{ selectedReceipt.payment.transaction_ref }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Status:</span>
                        <span class="receipt-value" :style="{ color: (selectedReceipt?.payment?.status?.toUpperCase() === 'APPROVED' || selectedReceipt?.payment?.status?.toUpperCase() === 'PAID') ? '#239640' : '#dc3545' }">
                            {{ formatPaymentStatus(selectedReceipt?.payment?.status) || 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="receipt-image-container" v-if="selectedReceipt?.payment?.receipt_image || selectedReceipt?.payment?.receipt_path">
                    <p class="receipt-image-label">Proof of Payment:</p>
                    <img :src="selectedReceipt?.payment?.receipt_image || selectedReceipt?.payment?.receipt_path" alt="Payment Receipt" class="receipt-image" @error="handleImageError" />
                </div>
                <div v-else class="no-receipt-container">
                    <p class="no-receipt-message">No proof of payment available</p>
                </div>

                <div class="receipt-note">
                    <p>Thank you for your payment. This digital receipt is proof of payment issued by Barangay.</p>
                </div>

                <div class="receipt-actions">
                    <button class="download-receipt-btn" @click="downloadReceipt" v-if="selectedReceipt?.payment?.receipt_image || selectedReceipt?.payment?.receipt_path">
                        DOWNLOAD
                    </button>
                    <button class="print-receipt-btn" @click="printReceipt" v-if="selectedReceipt?.payment?.receipt_image || selectedReceipt?.payment?.receipt_path">
                        PRINT
                    </button>
                    <button class="close-receipt-btn" @click="closeReceiptModal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    <div v-if="showPaymentConfirmation" class="modal-overlay" @click="closePaymentConfirmation">
        <div class="modal-content confirmation-modal" @click.stop>
            <button class="close-modal-btn" @click="closePaymentConfirmation">✕</button>
            
            <div class="confirmation-icon success-icon">
                <div class="status-badge">
                    <span>✓</span>
                </div>
            </div>
            
            <h3 class="modal-title">{{ isOnsitePayment ? 'Onsite Payment Recorded' : 'Payment Submitted Successfully' }}</h3>
            
            <div class="confirmation-content">
                <p class="confirmation-message" v-if="!isOnsitePayment">
                    Your payment has been successfully submitted and sent to the <strong>Barangay Treasurer</strong> for review.
                </p>
                <p class="confirmation-message" v-else>
                    Your onsite payment has been recorded. Please bring the <strong>exact amount</strong> and present this request number at the barangay office.
                </p>
                
                <div class="payment-summary">
                    <div class="summary-item">
                        <span class="summary-label">Request Number:</span>
                        <span class="summary-value">#{{ selectedRequest?.requestNumber }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Amount:</span>
                        <span class="summary-value">₱{{ (selectedRequest?.amount ?? selectedRequest?.processing_fee) || '0.00' }}</span>
                    </div>
                    <div class="summary-item" v-if="!isOnsitePayment && selectedGateway">
                        <span class="summary-label">Payment Method:</span>
                        <span class="summary-value">{{ selectedGateway }}</span>
                    </div>
                    <div class="summary-item" v-if="isOnsitePayment">
                        <span class="summary-label">Payment Method:</span>
                        <span class="summary-value">Onsite</span>
                    </div>
                    <div class="summary-item" v-if="!isOnsitePayment && referenceId && referenceId.trim()">
                        <span class="summary-label">Transaction Reference:</span>
                        <span class="summary-value">{{ referenceId }}</span>
                    </div>
                </div>
                
                <p class="confirmation-note" v-if="!isOnsitePayment">
                    You will be notified once the treasurer reviews your payment. Please check your notifications for updates.
                </p>
                <p class="confirmation-note" v-else>
                    <strong>Important:</strong> Remember to bring the exact amount (₱{{ (selectedRequest?.amount ?? selectedRequest?.processing_fee) || '0.00' }}) and present request number <strong>#{{ selectedRequest?.requestNumber }}</strong> when you visit the barangay office.
                </p>
            </div>
            
            <button class="confirmation-btn" @click="closePaymentConfirmation">
                GOT IT
            </button>
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
                    <h3 class="terms-section-title">1. Account Registration and Access</h3>
                    <p class="terms-text">
                        By creating an account and using the iKonek176B portal, you agree to provide accurate, current, and complete information during registration. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must immediately notify Barangay 176B of any unauthorized use of your account or any other breach of security.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">2. Use of Services</h3>
                    <p class="terms-text">
                        The iKonek176B portal is provided for legitimate barangay-related purposes only. You may use the portal to:
                        <ul class="terms-list">
                            <li>Submit document requests (e.g., Barangay Certificate, Barangay ID, Business Permit)</li>
                            <li>Request event assistance for community activities</li>
                            <li>View announcements and community updates</li>
                            <li>Participate in community discussions and forums</li>
                            <li>Access your request history and status</li>
                        </ul>
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">3. Accurate Information</h3>
                    <p class="terms-text">
                        You are responsible for ensuring that all information you submit through the portal is accurate, truthful, and complete. Providing false, misleading, or incomplete information may result in rejection of your requests, suspension of your account, and possible legal action. You must update your account information promptly if any changes occur.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">4. Document Requests</h3>
                    <p class="terms-text">
                        When submitting document requests, you must:
                        <ul class="terms-list">
                            <li>Provide all required documents and information</li>
                            <li>Ensure documents are authentic and valid</li>
                            <li>Pay applicable processing fees as required</li>
                            <li>Follow pickup instructions and deadlines</li>
                            <li>Use documents only for their intended legal purposes</li>
                        </ul>
                        The barangay reserves the right to verify all submitted information and documents. Approval of requests is subject to verification and compliance with barangay policies and regulations.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">5. Event Assistance Requests</h3>
                    <p class="terms-text">
                        When requesting event assistance, you must provide accurate event details, including date, time, location, and purpose. Event assistance is subject to availability and approval by barangay officials. You are responsible for ensuring your event complies with all applicable laws, regulations, and barangay policies. The barangay reserves the right to deny or cancel event assistance for any reason.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">6. Payment and Fees</h3>
                    <p class="terms-text">
                        Some services may require payment of processing fees. All fees must be paid according to the payment methods provided. Payments are non-refundable unless otherwise stated. The barangay is not responsible for delays or issues caused by payment processing errors or failures. You are responsible for ensuring payments are made correctly and on time.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">7. Data Privacy</h3>
                    <p class="terms-text">
                        Your personal information is collected and processed in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173). The barangay will use your information only for legitimate purposes related to service delivery, record-keeping, and compliance with legal requirements. Your information will not be shared with unauthorized third parties except as required by law.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">8. Prohibited Activities</h3>
                    <p class="terms-text">
                        You are strictly prohibited from:
                        <ul class="terms-list">
                            <li>Using the portal for any illegal or unauthorized purpose</li>
                            <li>Submitting false, fraudulent, or misleading information</li>
                            <li>Attempting to gain unauthorized access to the system or other users' accounts</li>
                            <li>Interfering with or disrupting the portal's operation</li>
                            <li>Harassing, threatening, or abusing other users or barangay officials</li>
                            <li>Posting inappropriate, offensive, or defamatory content</li>
                            <li>Violating any applicable laws or regulations</li>
                        </ul>
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">9. Account Suspension and Termination</h3>
                    <p class="terms-text">
                        The barangay reserves the right to suspend or terminate your account at any time, with or without notice, if you violate these terms and conditions, engage in prohibited activities, or for any other reason deemed necessary by barangay officials. Upon termination, your right to use the portal will immediately cease.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">10. Limitation of Liability</h3>
                    <p class="terms-text">
                        The barangay is not liable for any delays, errors, or failures in service delivery caused by technical issues, system maintenance, incorrect information provided by users, or circumstances beyond the barangay's reasonable control. The barangay does not guarantee uninterrupted or error-free access to the portal.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">11. Updates to Terms</h3>
                    <p class="terms-text">
                        These terms and conditions may be updated periodically. You will be notified of significant changes through the portal or other communication channels. Continued use of the portal after changes constitutes acceptance of the updated terms.
                    </p>
                </div>

                <div class="terms-section">
                    <h3 class="terms-section-title">12. Contact and Support</h3>
                    <p class="terms-text">
                        For questions, concerns, or to report issues related to your account or the portal, contact the Barangay 176B office at ikonek176b@dev.ph or +639193076338. For technical support or assistance with using the portal, please visit the Help Center section.
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

    <!-- Resubmission Modal -->
    <div v-if="showResubmissionModal" class="modal-overlay resubmission-modal-overlay" @click="closeResubmissionModal">
        <div class="modal-content resubmission-modal-content" @click.stop>
            <button @click="closeResubmissionModal" class="modal-close">✕</button>
            
            <div class="resubmission-modal-header">
                <h3 class="resubmission-modal-title">EDIT & RESUBMIT REQUEST</h3>
                <p class="resubmission-document-type" v-if="resubmissionDocumentTypeName">
                    Document Type: <strong>{{ resubmissionDocumentTypeName }}</strong>
                </p>
                <div v-if="resubmissionRequestData?.incorrect_fields && resubmissionRequestData.incorrect_fields.length > 0" class="editable-fields-notice">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <span>Only fields marked as incorrect by the approver can be edited. Other fields are locked.</span>
                </div>
            </div>

            <div v-if="resubmissionRequestData?.admin_feedback" class="rejection-box">
                <h3 class="rejection-title">⚠️ Reason for Rejection:</h3>
                <p class="rejection-text">{{ resubmissionRequestData.admin_feedback }}</p>
            </div>

            <div class="request-info-summary" v-if="resubmissionRequestData">
                <div class="info-row">
                    <span class="info-label">Request Ticket:</span>
                    <span class="info-value">{{ resubmissionRequestData.doc_request_ticket }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-pending">PENDING</span>
                </div>
            </div>

            <div class="resubmission-form-container">
                <!-- Purpose -->
                <div class="form-section">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Purpose <span class="required-star">*</span>
                    </h4>
                    
                    <div class="purpose-id-row">
                        <div class="purpose-field">
                            <select 
                                v-model="resubmissionForm.purpose" 
                                class="form-input"
                                :disabled="!isResubmissionFieldIncorrect('purpose')"
                                :class="{ 'field-disabled': !isResubmissionFieldIncorrect('purpose') }"
                                required
                                @change="handleResubmissionPurposeChange"
                            >
                                <option value="">Select purpose</option>
                                <option v-for="purpose in getResubmissionDocumentPurposes()" :key="purpose" :value="purpose">
                                    {{ purpose }}
                                </option>
                            </select>
                            <input
                                v-if="resubmissionForm.purpose === 'Others'"
                                v-model="resubmissionPurposeOthers"
                                @input="resubmissionForm.purpose = resubmissionPurposeOthers"
                                placeholder="Please specify your purpose..."
                                class="form-input"
                                :disabled="!isResubmissionFieldIncorrect('purpose')"
                                :class="{ 'field-disabled': !isResubmissionFieldIncorrect('purpose') }"
                                style="margin-top: 10px;"
                                required
                            />
                        </div>
                        
                        <div class="id-type-field">
                            <div class="id-type-upload-row">
                                <select 
                                    v-model="resubmissionForm.id_type" 
                                    class="form-input id-type-select"
                                    :disabled="!isResubmissionFieldIncorrect('id_type')"
                                    :class="{ 'field-disabled': !isResubmissionFieldIncorrect('id_type') }"
                                >
                                    <option value="">Type of Identification</option>
                                    <option value="National ID">National ID</option>
                                    <option value="Driver's License">Driver's License</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Voter's ID">Voter's ID</option>
                                    <option value="SSS ID">SSS ID</option>
                                    <option value="UMID">UMID</option>
                                </select>
                                
                                <div class="upload-row">
                                    <button 
                                        type="button" 
                                        class="upload-btn" 
                                        :disabled="!isResubmissionFieldIncorrect('id_front')"
                                        :class="{ 'btn-disabled': !isResubmissionFieldIncorrect('id_front') }"
                                        @click.prevent="triggerResubmissionFileUpload('front')"
                                    >
                                        UPLOAD FRONT
                                    </button>
                                    <input 
                                        type="file" 
                                        ref="resubmissionFileFrontInput" 
                                        @change="handleResubmissionFileUpload($event, 'front')" 
                                        class="file-input-hidden"
                                        accept="image/*,.pdf"
                                        style="display: none"
                                        :disabled="!isResubmissionFieldIncorrect('id_front')"
                                    />

                                    <button 
                                        type="button" 
                                        class="upload-btn" 
                                        :disabled="!isResubmissionFieldIncorrect('id_back')"
                                        :class="{ 'btn-disabled': !isResubmissionFieldIncorrect('id_back') }"
                                        @click.prevent="triggerResubmissionFileUpload('back')"
                                    >
                                        UPLOAD BACK
                                    </button>
                                    <input 
                                        type="file" 
                                        ref="resubmissionFileBackInput" 
                                        @change="handleResubmissionFileUpload($event, 'back')" 
                                        class="file-input-hidden"
                                        accept="image/*,.pdf"
                                        style="display: none"
                                        :disabled="!isResubmissionFieldIncorrect('id_back')"
                                    />
                                </div>
                            </div>
                            
                            <div class="uploaded-files-display">
                                <p v-if="resubmissionIdFrontName" class="uploaded-file">Front: {{ resubmissionIdFrontName }}</p>
                                <p v-if="resubmissionIdBackName" class="uploaded-file">Back: {{ resubmissionIdBackName }}</p>
                            </div>
                            
                            <div v-if="showResubmissionIdNumber" class="id-number-field" style="margin-top: 12px;">
                                <label class="field-label">{{ getResubmissionIdNumberLabel() }} <span>*</span></label>
                                <input
                                    type="text"
                                    v-model="resubmissionForm.id_number"
                                    placeholder="Enter ID Number"
                                    class="form-input"
                                    :disabled="!isResubmissionFieldIncorrect('id_number')"
                                    :class="{ 'field-disabled': !isResubmissionFieldIncorrect('id_number') }"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic fields for the selected document -->
                <div class="form-section dynamic-fields" v-if="resubmissionDocumentFields.length">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        Additional Information & Required Documents
                    </h4>

                    <!-- Special layout for Barangay Certificate -->
                    <template v-if="resubmissionDocumentTypeName === 'Barangay Certificate'">
                        <div class="dynamic-field-wrapper">
                            <div class="field-header">
                                <label class="field-label">
                                    {{ resubmissionDocumentFields.find(f => f.name === 'duration_of_residency')?.label }}
                                </label>
                            </div>
                            <div class="field-input-wrapper">
                                <input 
                                    type="number"
                                    v-model.number="resubmissionForm.extra_fields.duration_of_residency"
                                    placeholder="Enter number of years"
                                    :min="0"
                                    :step="1"
                                    class="form-input"
                                    :disabled="!isResubmissionExtraFieldIncorrect('duration_of_residency')"
                                    :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect('duration_of_residency') }"
                                />
                            </div>
                        </div>
                    </template>

                    <!-- Non-file fields (text, select, number, etc.) - for other document types -->
                    <div v-for="field in resubmissionDocumentFields.filter(f => f.type !== 'file')" :key="field.name" class="dynamic-field-wrapper" v-if="resubmissionDocumentTypeName !== 'Barangay Certificate'">
                        <div class="field-header">
                            <label class="field-label">
                                {{ field.label }} 
                                <span v-if="field.required" class="required-star">*</span>
                            </label>
                            <p v-if="field.description" class="field-description">{{ field.description }}</p>
                        </div>

                        <div class="field-input-wrapper">
                            <!-- text -->
                            <input 
                                v-if="field.type === 'text'"
                                v-model="resubmissionForm.extra_fields[field.name]"
                                :placeholder="field.placeholder || ''"
                                class="form-input"
                                :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                :required="field.required"
                            />

                            <!-- textarea -->
                            <textarea
                                v-if="field.type === 'textarea'"
                                v-model="resubmissionForm.extra_fields[field.name]"
                                :placeholder="field.placeholder || ''"
                                rows="3"
                                class="form-textarea"
                                :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                :required="field.required"
                            ></textarea>

                            <!-- date -->
                            <input
                                v-if="field.type === 'date'"
                                type="date"
                                v-model="resubmissionForm.extra_fields[field.name]"
                                :max="field.max || new Date().toISOString().split('T')[0]"
                                class="form-input"
                                :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                :required="field.required"
                            />

                            <!-- number -->
                            <input
                                v-if="field.type === 'number'"
                                type="number"
                                v-model.number="resubmissionForm.extra_fields[field.name]"
                                :placeholder="field.placeholder || ''"
                                :min="field.min !== undefined ? field.min : undefined"
                                :step="field.step !== undefined ? field.step : undefined"
                                class="form-input"
                                :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                :required="field.required"
                            />

                            <!-- select -->
                            <select
                                v-if="field.type === 'select'"
                                v-model="resubmissionForm.extra_fields[field.name]"
                                class="form-input"
                                :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                :required="field.required"
                            >
                                <option value="">{{ field.placeholder || 'Select an option' }}</option>
                                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                            </select>

                            <!-- checkbox group -->
                            <div v-if="field.type === 'checkbox'" class="checkbox-group">
                                <label v-for="opt in field.options" :key="opt" class="checkbox-label">
                                    <input
                                        type="checkbox"
                                        :value="opt"
                                        :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                        :class="{ 'field-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                        @change="toggleResubmissionCheckbox(field.name, opt, $event.target.checked)"
                                        :checked="Array.isArray(resubmissionForm.extra_fields[field.name]) && resubmissionForm.extra_fields[field.name].includes(opt)"
                                    />
                                    <span>{{ opt }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- File upload fields -->
                    <div v-if="resubmissionDocumentFields.filter(f => f.type === 'file' && resubmissionDocumentTypeName !== 'Barangay Certificate').length > 0" class="file-uploads-grid">
                        <div v-for="field in resubmissionDocumentFields.filter(f => f.type === 'file' && resubmissionDocumentTypeName !== 'Barangay Certificate')" :key="field.name" class="file-upload-item">
                            <div class="file-upload-header">
                                <label class="file-upload-label">
                                    {{ field.label }} 
                                    <span v-if="field.required" class="required-star">*</span>
                                </label>
                                <p v-if="field.description" class="file-upload-description">{{ field.description }}</p>
                            </div>
                            <div class="file-upload-controls">
                                <div v-if="getResubmissionExistingFile(field.name)" class="existing-file-display">
                                    <span class="existing-file-name">Current: {{ getResubmissionExistingFile(field.name)?.file_name }}</span>
                                    <a :href="getResubmissionExistingFile(field.name)?.url" target="_blank" class="view-file-link">View</a>
                                </div>
                                <button 
                                    type="button"
                                    class="upload-btn-dynamic" 
                                    :disabled="!isResubmissionExtraFieldIncorrect(field.name)"
                                    :class="{ 'btn-disabled': !isResubmissionExtraFieldIncorrect(field.name) }"
                                    @click.prevent="triggerResubmissionDynamicFileUpload(field.name)"
                                >
                                    {{ getResubmissionExistingFile(field.name) ? 'REPLACE' : 'UPLOAD' }}
                                </button>
                                <input
                                    type="file"
                                    :data-dyn-field="field.name"
                                    style="display: none"
                                    @change="handleResubmissionDynamicFileUpload($event, field.name)"
                                    :accept="field.accept || 'image/*,.pdf'"
                                />
                                <div v-if="resubmissionExtraFileNames[field.name]" class="uploaded-file-info-compact">
                                    <svg class="file-checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 18px; height: 18px; color: #4caf50;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="file-name-compact">{{ resubmissionExtraFileNames[field.name] }}</span>
                                    <button 
                                        type="button"
                                        class="remove-file-btn-small" 
                                        @click="removeResubmissionDynamicFile(field.name)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    class="submit-btn"
                    :disabled="isResubmitting"
                    @click="submitResubmission"
                >
                    <span v-if="!isResubmitting">SUBMIT</span>
                    <span v-else>Submitting...</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

// Inertia-shared auth user
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)
const restrictions = computed(() => page?.props?.value?.restrictions ?? page?.props?.restrictions ?? null)
const documentTypes = computed(() => page?.props?.value?.documentTypes ?? page?.props?.documentTypes ?? [])
const availableDocumentTypes = computed(() => page?.props?.value?.availableDocumentTypes ?? page?.props?.availableDocumentTypes ?? [])
const restrictedDocumentTypeIds = computed(() => page?.props?.value?.restrictedDocumentTypeIds ?? page?.props?.restrictedDocumentTypeIds ?? [])

// Profile picture URL
const profilePictureUrl = computed(() => {
  if (user.value?.profile_pic) {
    const pic = user.value.profile_pic
    let url = ''
    // If it's a full URL, return as is
    if (pic.startsWith('http')) {
      url = pic
    }
    // If it already has /storage/, return as is
    else if (pic.startsWith('/storage/')) {
      url = pic
    }
    // Otherwise prepend storage path
    else {
      url = `/storage/${pic}`
    }
    // Add cache-busting parameter based on profile_pic value to force browser reload when it changes
    const version = pic ? pic.split('/').pop() + pic.length : Date.now()
    return url + (url.includes('?') ? '&' : '?') + `v=${version}`
  }
  return '/assets/DEFAULT.jpg'
})

// role map (unchanged)
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
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  const role = id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
  return role.toUpperCase()
})

const showSettings = ref(false)
const activeTab = ref('documents')
const currentView = ref('list') // Default to 'list' view to show submitted requests
const unreadCount = ref(0)

// Fetch unread notification count
const fetchUnreadCount = async () => {
    try {
        const response = await axios.get('/api/notifications')
        if (response.data.success) {
            const notifications = response.data.notifications || []
            unreadCount.value = notifications.filter(n => !n.is_read).length
        }
    } catch (error) {
        console.error('Error fetching unread count:', error)
        unreadCount.value = 0
    }
}
const selectedDocType = ref('Barangay Certificate')
const selectedPermitType = ref(null) // Store selected permit type (Building Permit or Business Permit)
const requestNumber = ref('')
const isSubmitting = ref(false)
const purposeOthers = ref('') // For custom purpose input when "Others" is selected

// Filter state
const showSortDropdown = ref(false)
const showStatusDropdown = ref(false)
const sortOption = ref('newest')
const statusFilter = ref('all')
const searchQuery = ref('')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(5)

// Request details modal state
const showDetailsModal = ref(false)
const selectedRequest = ref(null)

// Payment modal state
const showPaymentModal = ref(false)
const showQRModal = ref(false)
const selectedGateway = ref(null) // 'GCash' or 'Maya'
const scanning = ref(false)
const uploadedFile = ref(null)
const uploadedPreview = ref(null)
const referenceId = ref('')
const uploading = ref(false)
const paymentFileInput = ref(null)
const paymentSuccess = ref(false)
const paymentError = ref('')
const showPaymentConfirmation = ref(false)
const isOnsitePayment = ref(false)
const showReceiptModal = ref(false)
const selectedReceipt = ref(null)

// Resubmission modal state
const showResubmissionModal = ref(false)
const resubmissionRequestData = ref(null)
const resubmissionForm = ref({
    purpose: '',
    id_type: '',
    id_number: '',
    extra_fields: {},
    id_front: null,
    id_back: null,
    valid_id_content: null,
})
const resubmissionIdFrontName = ref('')
const resubmissionIdBackName = ref('')
const resubmissionExtraFileNames = ref({})
const resubmissionFileFrontInput = ref(null)
const resubmissionFileBackInput = ref(null)
const resubmissionPurposeOthers = ref('')
const isResubmitting = ref(false)
const resubmissionDocumentTypeName = ref('')
const resubmissionDocumentFields = ref([])

// Form error state
const formErrors = ref({})
const submitError = ref('')
const fieldErrors = ref({})

// Get props from Inertia page
const documentRequestsRaw = computed(() => {
  return page?.props?.value?.documentRequests ?? page?.props?.documentRequests ?? page?.props?.value?.document_requests ?? page?.props?.document_requests ?? []
})

const paymentsRaw = computed(() => {
  return page?.props?.value?.payments ?? page?.props?.payments ?? []
})

// Map payments by doc_request_id
const paymentsMap = computed(() => {
  const map = {}
  const items = Array.isArray(paymentsRaw.value) ? paymentsRaw.value : []
  for (const p of items) {
    const fk = p.fk_doc_request_id ?? null
    if (!fk) continue
    if (!map[fk]) {
      map[fk] = {
        payment_id: p.payment_id ?? null,
        fk_doc_request_id: fk,
        status: (p.status ?? 'PENDING').toString().toUpperCase(),
        amount: p.amount ?? p.paid_amount ?? null,
        transaction_ref: p.transaction_ref ?? null,
        receipt_path: p.receipt_path ?? p.receipt_image ?? null,
        receipt_image: p.receipt_image ?? p.receipt_path ?? null,
        paid_at: p.paid_at ?? null,
        raw: p,
      }
    }
  }
  return map
})

// Map document requests into normalized shape
const mappedDocumentRequests = computed(() => {
  const server = documentRequestsRaw.value || []
  if (server.length === 0) return []

  return (Array.isArray(server) ? server : []).map((r) => {
    const createdAt = r.created_at
    let timestamp = null
    
    if (createdAt) {
      try { 
        timestamp = new Date(createdAt)
        if (isNaN(timestamp.getTime())) timestamp = null
      } catch (e) { 
        timestamp = null 
      }
    }

    let dateStr = ''
    let timeStr = ''
    if (timestamp && !isNaN(timestamp.getTime())) {
      try {
        dateStr = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(timestamp)
        timeStr = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }).format(timestamp)
      } catch (e) {
        dateStr = timestamp.toLocaleDateString()
        timeStr = timestamp.toLocaleTimeString()
      }
    } else {
      dateStr = 'N/A'
      timeStr = 'N/A'
    }

    const rawStatus = (r.status ?? 'PENDING').toString().toUpperCase()
    // If status is PENDING and has admin_feedback, it's a resubmission
    const status = (rawStatus === 'PENDING' && r.admin_feedback && r.admin_feedback.trim() !== '') 
      ? 'RESUBMITTED' 
      : rawStatus
    const title = r.document_name ?? 'Document Request'
    const requestNumber = r.doc_request_ticket ?? `DOC-${r.doc_request_id}`
    
    // Attach payment if exists - use doc_request_id as key
    const payment = paymentsMap.value[r.doc_request_id] ?? null

    return {
      id: r.doc_request_id,
      requestNumber,
      title,
      status,
      date: dateStr,
      time: timeStr,
      type: 'document',
      amount: r.processing_fee ?? r.applied_processing_fee ?? null,
      processing_fee: r.processing_fee ?? r.applied_processing_fee ?? null,
      pickup_item: r.pickup_item ?? null,
      pickup_location: r.pickup_location ?? null,
      pickup_start: r.pickup_start ?? null,
      pickup_end: r.pickup_end ?? null,
      person_to_look: r.person_to_look ?? null,
      admin_feedback: r.admin_feedback ?? null,
      payment,
      raw: r,
    }
  })
})

// Purpose options for each document type
const documentPurposes = {
  'Barangay Certificate': [
    'Employment',
    'School Admission',
    'Government Transaction',
    'Business Registration',
    'Bank Transaction',
    'Others'
  ],
  'Barangay ID': [
    'Identification',
    'Government Transaction',
    'Bank Transaction',
    'School Admission',
    'Employment',
    'Others'
  ],
  'Cedula': [
    'Employment',
    'Business Registration',
    'Government Transaction',
    'Tax Requirements',
    'Legal Documentation',
    'Others'
  ],
  'Certificate of Indigency': [
    'Medical Assistance',
    'Educational Scholarship',
    'Social Services',
    'Financial Assistance',
    'Housing Assistance',
    'Others'
  ],
  'Permit': [
    'New Business Registration',
    'Business Renewal',
    'Business Expansion',
    'New Construction',
    'Building Renovation',
    'Building Addition',
    'Others'
  ]
}

// Computed property to get purposes for current document type
const currentDocumentPurposes = computed(() => {
  return documentPurposes[selectedDocType.value] || []
})

// new refs for ID front/back input elements and filenames
const fileFrontInput = ref(null)
const fileBackInput = ref(null)
const idFrontName = ref('')
const idBackName = ref('')

// dynamic file names tracking for dynamic fields
const dynamicFileNames = ref({})

// --- useForm: include id front/back and id_number
const form = useForm({
  document_name: selectedDocType.value,
  fk_document_type_id: '',

  // mirrored user columns (prefill from user if available)
  last_name: user.value?.last_name ?? (user.value?.name ? user.value.name.split(' ').slice(-1).join(' ') : ''),
  first_name: user.value?.first_name ?? user.value?.name ?? '',
  middle_name: user.value?.middle_name ?? '',
  suffix: user.value?.suffix ?? '',
  birthdate: user.value?.birthdate ?? '',
  sex: user.value?.sex ?? '',
  civil_status: user.value?.civil_status ?? '',
  address: user.value?.address ?? '',
  contact_number: user.value?.contact_number ?? '',

  // request-specific
  purpose: '', // text description
  id_type: '',
  id_number: '',
  // legacy UI fields (kept so the component logic still references them)
  id_front: null,
  id_back: null,

  // IMPORTANT: these are the backend-target keys requested
  valid_id_content: null,
  valid_id_number: '',

  document: null, // other uploaded doc(s)
  extra_fields: {} // dynamic per-document fields (will be JSON on the backend)
})

// keep document_name synced with selectedDocType
watch(selectedDocType, (val) => {
  form.document_name = val
  initExtraFieldsForDocument(val)
  // Clear purpose and custom purpose when switching documents
  form.purpose = ''
  purposeOthers.value = ''
  // Clear permit_type when switching away from Permit
  if (val !== 'Permit') {
    selectedPermitType.value = null
    if (form.extra_fields?.permit_type) {
      form.extra_fields.permit_type = null
    }
  }
})

// Watch selectedPermitType to update form fields
watch(selectedPermitType, (newVal) => {
  if (selectedDocType.value === 'Permit' && newVal) {
    // Set permit_type in extra_fields
    if (!form.extra_fields) {
      form.extra_fields = {}
    }
    form.extra_fields.permit_type = newVal
    // Reinitialize fields with the selected permit type
    initExtraFieldsForDocument(selectedDocType.value)
  }
})

// when id_type changes, clear id_number (so user can re-enter)
watch(() => form.id_type, (newVal, oldVal) => {
  form.id_number = ''
})

// computed for showing id number field only after an id_type is selected AND at least one side uploaded
const showIdNumber = computed(() => {
  return !!form.id_type && (!!form.id_front || !!form.id_back)
})

// mapping labels per id type
const idNumberLabels = {
  'National ID': 'National ID No.',
  "Driver's License": "Driver's License No.",
  'Passport': 'Passport No.',
  "Voter's ID": "Voter\'s ID No.",
  'SSS ID': 'SSS No.',
  'UMID': 'UMID No.'
}
const idNumberLabel = computed(() => {
  return idNumberLabels[form.id_type] || 'ID Number'
})

// Display document type - show actual permit type if Permit is selected
const displayDocumentType = computed(() => {
  if (selectedDocType.value === 'Permit' && selectedPermitType.value) {
    return selectedPermitType.value
  }
  if (selectedDocType.value === 'Permit' && form.extra_fields?.permit_type) {
    return form.extra_fields.permit_type
  }
  return selectedDocType.value
})

// helper date
const today = new Date().toISOString().split('T')[0]

// Allowed document types - only these should be shown
const allowedDocumentTypes = [
    'Barangay Certificate',
    'Barangay ID',
    'Permit',
    'Cedula',
    'Certificate of Indigency'
]

// Create document types list with availability status
// Only show document types that the backend provides (from database)
// Filter to only show allowed document types
// Note: "Permit" is a UI-only parent type, not in database
const documentTypesWithStatus = computed(() => {
    let allDocs = []
    
    // If backend provided documentTypes with restriction status, use those
    if (documentTypes.value && documentTypes.value.length > 0) {
        allDocs = documentTypes.value.map(doc => ({
            name: doc.name,
            id: doc.id || null,
            available: doc.available !== false,
            restricted: doc.restricted === true,
            processing_fee: doc.processing_fee || null
        }))
    }
    // Fallback: if backend provided availableDocumentTypes, use only those
    else if (availableDocumentTypes.value && availableDocumentTypes.value.length > 0) {
        allDocs = availableDocumentTypes.value.map(doc => ({
            name: doc.name,
            id: doc.id || null,
            available: true,
            restricted: false,
            processing_fee: doc.processing_fee || null
        }))
    }
    
    // Filter to only show allowed document types (excluding Building Permit and Business Permit as they're handled by Permit)
    const filtered = allDocs.filter(doc => {
        // Exclude Building Permit and Business Permit as they're sub-types of Permit
        if (doc.name === 'Building Permit' || doc.name === 'Business Permit') {
            return false
        }
        return allowedDocumentTypes.includes(doc.name)
    })
    
    // Add "Permit" as a UI-only parent type if it's in the allowed list
    if (allowedDocumentTypes.includes('Permit')) {
        const permitExists = filtered.some(doc => doc.name === 'Permit')
        if (!permitExists) {
            filtered.push({
                name: 'Permit',
                id: null,
                available: true,
                restricted: false,
                processing_fee: null
            })
        }
    }
    
    return filtered
})

// For backward compatibility, keep documentNames as computed
const documentNames = computed(() => documentTypesWithStatus.value.map(doc => doc.name))

const documentDescriptions = {
    'Barangay Certificate': 'Ang Barangay Certificate ay isang opisyal na dokumentong ibinibigay ng barangay upang patunayan na ang isang tao ay lehitimong residente ng nasabing lugar. Karaniwan itong kinakailangan sa iba\'t ibang transaksyong legal at administratibo gaya ng pag-apply ng trabaho, pag-enroll sa paaralan, pagkuha ng tulong mula sa gobyerno, at pagproseso ng mga permit o lisensya.',
    
    'Barangay ID': 'Ang Barangay ID ay isang opisyal na identification card na ibinibigay ng barangay sa mga lehitimong residente. Ito ay ginagamit bilang proof of residency at maaaring gamitin sa iba\'t ibang transaksyon sa loob at labas ng barangay. May kasamang larawan at personal na impormasyon ng may-ari.',
    
    'Cedula': 'Ang Cedula o Community Tax Certificate ay isang dokumento na nagpapatunay na ang isang indibidwal ay nagbayad ng community tax. Ito ay kailangan sa iba\'t ibang legal at business transactions, at ginagamit din bilang valid ID sa ilang transaksyon.',
        
    'Certificate of Indigency': 'Ang Certificate of Indigency ay sertipikasyon na nagpapatunay na ang isang pamilya o indibidwal ay walang sapat na kita at nangangailangan ng tulong. Ito ay ginagamit upang makakuha ng medical assistance, educational scholarships, at iba pang social services mula sa gobyerno at pribadong organisasyon.',
    
    'Permit': 'Ang Permit ay maaaring maging Barangay Business Permit o Barangay Building Permit. Piliin ang uri ng permit na nais mong i-request.',
}

// Permit type descriptions
const permitTypeDescriptions = {
    'Building Permit': 'Ang Barangay Building Permit ay kinakailangan bago magsimula ng anumang konstruksyon o renovation sa loob ng barangay. Ito ay bahagi ng proseso ng pagkuha ng building permit mula sa munisipyo at nagsisiguro na ang plano ay sumusunod sa zoning at safety regulations.',
    'Business Permit': 'Ang Barangay Business Permit ay kinakailangan para sa lahat ng negosyo na nais magsimula ng operasyon sa loob ng barangay. Ito ay nagpapatunay na ang negosyo ay sumusunod sa mga regulasyon ng barangay at hindi nakakasagabal sa kapakanan ng mga residente.',
}

const documentRequirements = {
    'Barangay Certificate': [
        '• Valid ID of the requestor',
        '• Supporting documents for residency verification',
        '• Personal appearance',
        '• Processing fee',
    ],
    'Barangay ID': [
        '• Valid ID of the requestor',
        '• 2x2 photo (2 copies)',
        '• Supporting documents for residency verification',
        '• Processing fee',
        '• Birth Certificate',
        '• Personal appearance',
    ],
    'Cedula': [
        '• Valid ID of the requestor',
        '• Tax declaration',
        '• Supporting documents for residency verification',
        '• Processing fee',
        '• Income Statement (if employed)',
        '• Personal appearance',
    ],
    'Certificate of Indigency': [
        '• Valid ID of the requestor',
        '• Proof of low income',
        '• Proof of residency',
        '• Personal appearance',
        '• Processing fee',
    ],
    'Permit': [
        '• Valid ID of the requestor (owner)',
        '• Supporting documents for residency verification',
        '• Barangay Clearance',
        '• Processing fee',
        '• Personal appearance',
        '• Additional requirements depend on permit type (Building/Business)',
    ],
}

const documentFields = {
  'Barangay Certificate': [
    { name: 'duration_of_residency', label: 'Duration of Residency (years)', type: 'number', required: true, placeholder: 'Enter number of years', min: 0, step: 1, description: 'Enter the number of years you have been a resident of this barangay' }
  ],

  'Barangay ID': [
    { name: 'photo', label: '2x2 Photo (2 copies)', type: 'file', required: true, accept: 'image/*', description: 'Upload 2x2 ID picture (2 copies)' },
    { name: 'birth_certificate', label: 'Birth Certificate', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload birth certificate' }
  ],

  'Cedula': [
    { name: 'income_source', label: 'Income Source', type: 'select', required: true, placeholder: 'Select income source', options: ['Employment', 'Business', 'Pension', 'Remittance', 'Other'] },
    { name: 'annual_income', label: 'Annual Income/Salary (PHP)', type: 'number', required: true, placeholder: 'Enter annual income or salary', min: 0, step: 0.01, description: 'Your annual income or salary from employment/profession (for tax calculation)' },
    { name: 'occupation', label: 'Occupation/Profession', type: 'select', required: true, placeholder: 'Select your occupation or profession', options: ['Teacher', 'Nurse', 'Engineer', 'Accountant', 'Lawyer', 'Doctor', 'Police Officer', 'Firefighter', 'Soldier', 'Government Employee', 'Private Employee', 'Business Owner', 'Entrepreneur', 'Farmer', 'Fisherman', 'Driver', 'Construction Worker', 'Electrician', 'Plumber', 'Carpenter', 'Mechanic', 'Chef', 'Waiter/Waitress', 'Salesperson', 'Cashier', 'Security Guard', 'Janitor', 'Housekeeper', 'Barber/Hairdresser', 'Beautician', 'Student', 'Unemployed', 'Retired', 'Housewife/Househusband', 'Freelancer', 'Other'], description: 'Your current job or profession' },
    { name: 'height', label: 'Height (cm)', type: 'number', required: true, placeholder: 'Enter height in centimeters', min: 0, step: 0.1, description: 'Your height in centimeters' },
    { name: 'weight', label: 'Weight (kg)', type: 'number', required: true, placeholder: 'Enter weight in kilograms', min: 0, step: 0.1, description: 'Your weight in kilograms' },
    { name: 'income_statement', label: 'Income Statement', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload income statement or payslip' }
  ],

  'Certificate of Indigency': [
    { name: 'household_members', label: 'Household Member Count', type: 'number', required: true, placeholder: 'Enter number of household members', min: 1, step: 1, description: 'Enter the total number of members in your household' },
    { name: 'income_proof', label: 'Proof of Low Income', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload documents proving low income status' }
  ],

  'Permit': [
    // Common fields for both permit types
    // Note: permit_type is selected in the document selection view, not in the form
    // Note: supporting_documents and barangay_clearance are not shown in the form, so they're not required
  ],
}

// Separate fields for Building and Business permits
const buildingPermitFields = [
  { name: 'building_type', label: 'Building Type', type: 'select', required: true, placeholder: 'Select building type', options: ['Residential', 'Commercial', 'Mixed Use', 'Industrial', 'Institutional', 'Other'] },
  { name: 'building_plans', label: 'Building Plans (3 copies)', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload building plans (3 copies)' },
  { name: 'engineer_cert', label: 'Engineer\'s Certification', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload engineer\'s certification document' },
  { name: 'lot_title', label: 'Lot Title or Tax Declaration', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload lot title or tax declaration' },
]

const businessPermitFields = [
  { name: 'business_name', label: 'Business Name', type: 'text', required: true, placeholder: 'Enter your business name' },
  { name: 'business_type', label: 'Business Type', type: 'select', required: true, placeholder: 'Select business type', options: ['Retail', 'Wholesale', 'Service', 'Manufacturing', 'Food & Beverage', 'Other'] },
  { name: 'business_registration', label: 'Business Registration Documents', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload business registration documents' },
  { name: 'dti_registration', label: 'DTI Registration Document', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload DTI registration certificate' },
  { name: 'location_plan', label: 'Location Plan', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload location plan or site map of business' },
]

// computed array for current selected doc fields
const currentDocumentFields = computed(() => {
  const baseFields = documentFields[selectedDocType.value] ?? []
  
  // If Permit is selected, add fields based on permit_type
  if (selectedDocType.value === 'Permit') {
    const permitType = selectedPermitType.value || form.extra_fields?.permit_type
    if (permitType === 'Building Permit') {
      return [...baseFields, ...buildingPermitFields]
    } else if (permitType === 'Business Permit') {
      return [...baseFields, ...businessPermitFields]
    }
    // If no permit type selected yet, return only base fields (permit_type selector)
    return baseFields
  }
  
  return baseFields
})

// initialize extra_fields when document is chosen to make sure keys exist
const initExtraFieldsForDocument = (docName) => {
  const defs = documentFields[docName] ?? []
  // ensure form.extra_fields exists
  form.extra_fields = form.extra_fields || {}

  // For Permit documents, also include permit-specific fields
  let allDefs = [...defs]
  if (docName === 'Permit') {
    const permitType = selectedPermitType.value || form.extra_fields?.permit_type
    if (permitType === 'Building Permit') {
      allDefs = [...defs, ...buildingPermitFields]
    } else if (permitType === 'Business Permit') {
      allDefs = [...defs, ...businessPermitFields]
    }
  }

  allDefs.forEach((f) => {
    // only initialize if not present
    if (form.extra_fields[f.name] === undefined) {
      if (f.type === 'checkbox') form.extra_fields[f.name] = []
      else if (f.type === 'select') form.extra_fields[f.name] = null 
      else form.extra_fields[f.name] = null
    }
    // clear dynamic file name tracking if none
    if (!dynamicFileNames.value[f.name]) dynamicFileNames.value[f.name] = ''
  })
}

// initialize for default selected
initExtraFieldsForDocument(selectedDocType.value)

// toggle checkbox helper (stores array)
const toggleCheckbox = (fieldName, value, checked) => {
  if (!Array.isArray(form.extra_fields[fieldName])) {
    form.extra_fields[fieldName] = []
  }
  const idx = form.extra_fields[fieldName].indexOf(value)
  if (checked && idx === -1) form.extra_fields[fieldName].push(value)
  if (!checked && idx !== -1) form.extra_fields[fieldName].splice(idx, 1)
}

// handle ID front/back upload triggers
const triggerFileUpload = (side) => {
  if (side === 'front' && fileFrontInput.value && typeof fileFrontInput.value.click === 'function') {
    fileFrontInput.value.click()
  } else if (side === 'back' && fileBackInput.value && typeof fileBackInput.value.click === 'function') {
    fileBackInput.value.click()
  } else {
    console.warn('file input ref not available yet for', side)
  }
}

const handleFileUpload = (event, side) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (side === 'front') {
    idFrontName.value = file.name
    form.id_front = file

    // IMPORTANT: assign the front-uploaded file to the backend-target key
    // so Inertia will send it as "valid_id_content" in FormData.
    form.valid_id_content = file
  } else if (side === 'back') {
    idBackName.value = file.name
    form.id_back = file

    // do NOT overwrite valid_id_content when uploading the back; front must remain the source
    // (If front wasn't uploaded, you can fallback - see below in submit)
    if (!form.valid_id_content) {
      // fallback only if front wasn't provided
      form.valid_id_content = file
    }
  }
}


// handle dynamic file upload
const triggerDynamicFileUpload = (fieldName) => {
  // Find the file input by its data attribute
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input && typeof input.click === 'function') {
    input.click()
  } else {
    console.error('Could not find file input for field:', fieldName)
    // Fallback: try to find by searching all file inputs
    const allInputs = Array.from(document.querySelectorAll('input[type="file"]'))
    const targetInput = allInputs.find(inp => {
      // Check if this input is in a container that matches the field name
      const container = inp.closest('.file-upload-item')
      if (container) {
        const label = container.querySelector('.file-upload-label')
        // This is a last resort - not ideal but might work
        return label && label.textContent.includes(fieldName)
      }
      return false
    })
    if (targetInput) {
      targetInput.click()
    }
  }
}

const handleDynamicFileUpload = (event, fieldName) => {
  const file = event.target.files[0]
  if (!file) return
  // store filename for UI
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: file.name }
  // attach file into extra_fields — Inertia will convert to FormData with forceFormData
  form.extra_fields = { ...form.extra_fields, [fieldName]: file }
  console.log('attached dynamic file', fieldName, file.name)
}

const removeDynamicFile = (fieldName) => {
  // Remove file from form
  form.extra_fields = { ...form.extra_fields, [fieldName]: null }
  // Clear filename display
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: '' }
  // Reset file input
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input) input.value = ''
}

// small helper to access refs in <script setup>
const refsSafe = () => {
  const res = {}
  currentDocumentFields.value.forEach((f) => {
    const dom = document.querySelector(`input[ref="dynFile_${f.name}"]`)
    if (dom) res[`dynFile_${f.name}`] = dom
  })
  return res
}

// other UI navigation & settings (unchanged)
const toggleSettings = () => { showSettings.value = !showSettings.value }
const closeSettings = () => { showSettings.value = false }

// Terms & Conditions modal
const showTermsModal = ref(false)
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
const logout = () => {
    showSettings.value = false
    // Properly logout by calling the logout endpoint
    router.post('/logout', {}, {
        onSuccess: () => {
            // Clear any local storage or session storage if needed
            if (typeof window !== 'undefined') {
                localStorage.clear()
                sessionStorage.clear()
            }
            // Redirect to login page after successful logout
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onError: () => {
            // Even if logout fails, redirect to login
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onFinish: () => {
            // Ensure we redirect even if something goes wrong
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        }
    })
}
const setActiveTab = (tab) => { activeTab.value = tab }
const navigateToPosts = () => { activeTab.value = 'posts'; router.visit(route('announcement_resident')) }
const navigateToEvents = () => { activeTab.value = 'events'; router.visit(route('event_assistance_resident')) }
const navigateToProfile = () => { activeTab.value = 'profile'; router.visit(route('profile_resident')) }
const navigateToNotifications = () => { activeTab.value = 'notifications'; router.visit(route('notification_activities_resident')) }
const openFAQ = () => { router.visit(route('help_center_resident')) }

// Show request form (switch to selection view)
const showRequestForm = (event) => {
  console.log('showRequestForm called', event)
  event?.preventDefault?.()
  event?.stopPropagation?.()
  
  if (restrictions?.value?.restrict_document_request) {
    alert('You are restricted from making document requests. Please contact the admin for more information.')
    return
  }
  
  // Reset form when showing request form
  form.reset()
  selectedDocType.value = 'Barangay Certificate'
  selectedPermitType.value = null
  console.log('Setting currentView to selection')
  currentView.value = 'selection'
  console.log('currentView is now:', currentView.value)
}

// View request details (open modal - same as notification page)
const viewRequestDetails = (request) => {
  selectedRequest.value = request
  showDetailsModal.value = true
}

// Close details modal
const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedRequest.value = null
}

const handleDocumentSelect = (doc) => {
  console.log('handleDocumentSelect called', doc)
  if (!doc.restricted) {
    selectDocument(doc.name)
  } else {
    console.log('Document is restricted:', doc.name)
  }
}

const selectDocument = (docType) => {
  console.log('selectDocument called with:', docType)
  selectedDocType.value = docType
  console.log('selectedDocType set to:', selectedDocType.value)
}

const selectPermitType = (permitType) => {
  selectedPermitType.value = permitType
  // Set permit_type in extra_fields immediately
  if (!form.extra_fields) {
    form.extra_fields = {}
  }
  form.extra_fields.permit_type = permitType
  // Reinitialize fields to include permit-specific fields
  initExtraFieldsForDocument(selectedDocType.value)
}

const handleProceedToForm = (event) => {
  console.log('handleProceedToForm called', event)
  event?.preventDefault?.()
  event?.stopPropagation?.()
  
  // Ensure a document type is selected
  if (!selectedDocType.value) {
    console.warn('No document type selected')
    alert('Please select a document type first')
    return
  }
  
  console.log('Proceeding to form with document:', selectedDocType.value)
  proceedToForm()
}

const proceedToForm = () => {
  console.log('proceedToForm called')
  // If Permit is selected, ensure permit type is chosen
  if (selectedDocType.value === 'Permit' && !selectedPermitType.value) {
    console.warn('Permit selected but no permit type chosen')
    alert('Please select a permit type (Building Permit or Business Permit)')
    return // Don't proceed if permit type not selected
  }
  
  // Set permit_type in extra_fields if Permit is selected
  if (selectedDocType.value === 'Permit' && selectedPermitType.value) {
    if (!form.extra_fields) {
      form.extra_fields = {}
    }
    form.extra_fields.permit_type = selectedPermitType.value
  }
  
  console.log('Initializing fields for document:', selectedDocType.value)
  initExtraFieldsForDocument(selectedDocType.value)
  console.log('Setting currentView to form')
  currentView.value = 'form'
  console.log('currentView is now:', currentView.value)
}

const backToSelection = () => {
  currentView.value = 'selection'
  // Clear purpose fields when going back
  form.purpose = ''
  purposeOthers.value = ''
  // Don't clear selectedPermitType - keep it so user doesn't have to reselect
}

// Go back to list view from form
const backToList = () => {
  currentView.value = 'list'
  form.purpose = ''
  purposeOthers.value = ''
}

// Payment-related functions
const showPaymentGateway = (request) => {
  selectedRequest.value = request
  showDetailsModal.value = false
  showPaymentModal.value = true
}

const openQR = (gateway) => {
  selectedGateway.value = gateway
  showQRModal.value = true
  showPaymentModal.value = false
}

const closeQRModal = () => {
  showQRModal.value = false
  selectedGateway.value = null
  scanning.value = false
}

const closePaymentModal = () => {
  showPaymentModal.value = false
}

const closePaymentConfirmation = () => {
  showPaymentConfirmation.value = false
  isOnsitePayment.value = false
  clearEvidence()
  router.reload({ only: ['documentRequests', 'payments'] })
}

const triggerPaymentUpload = () => {
  if (paymentFileInput.value && typeof paymentFileInput.value.click === 'function') {
    paymentFileInput.value.click()
  }
}

const onFileChange = (event) => {
  const file = (event.target && event.target.files && event.target.files[0]) || null
  if (!file) {
    clearEvidence()
    return
  }

  const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp']
  if (!allowedTypes.includes(file.type)) {
    alert('Please upload a PNG/JPEG/WebP image.')
    event.target.value = ''
    return
  }

  const maxMB = 5
  if (file.size / 1024 / 1024 > maxMB) {
    alert(`File too large. Max ${maxMB} MB allowed.`)
    event.target.value = ''
    return
  }

  uploadedFile.value = file

  // Create preview URL
  if (uploadedPreview.value) {
    try { URL.revokeObjectURL(uploadedPreview.value) } catch (e) {}
  }
  uploadedPreview.value = URL.createObjectURL(file)
}

const clearEvidence = () => {
  if (uploadedPreview.value) {
    try { URL.revokeObjectURL(uploadedPreview.value) } catch (e) {}
  }
  uploadedFile.value = null
  uploadedPreview.value = null
  referenceId.value = ''
  uploading.value = false
  paymentSuccess.value = false
  paymentError.value = ''
  if (paymentFileInput.value) {
    paymentFileInput.value.value = ''
  }
}

const submitEvidence = async () => {
  if (!selectedRequest.value) {
    alert('No selected request.')
    return
  }

  // require either a file or a reference ID
  if (!uploadedFile.value && (!referenceId.value || referenceId.value.trim() === '')) {
    alert('Please upload a screenshot or enter a transaction reference ID before submitting.')
    return
  }

  uploading.value = true
  paymentError.value = ''
  paymentSuccess.value = false

  try {
    const formData = new FormData()
    formData.append('fk_doc_request_id', Number(selectedRequest.value.id || selectedRequest.value.requestNumber))
    const userId = user.value?.id ?? user.value?.user_id ?? null
    if (userId) {
      formData.append('fk_user_id', Number(userId))
    }
    formData.append('request_reference_ticket', selectedRequest.value.requestNumber ?? '')
    formData.append('paid_amount', Number(selectedRequest.value.amount ?? selectedRequest.value.processing_fee ?? 0))
    formData.append('gateway', selectedGateway.value ?? '')
    
    if (uploadedFile.value) {
      formData.append('receipt_content', uploadedFile.value)
    }
    if (referenceId.value && referenceId.value.trim()) {
      formData.append('transaction_ref', referenceId.value.trim())
    }

    const response = await window.axios.post(route('payments.store'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-Requested-With': 'XMLHttpRequest',
      },
      withCredentials: true,
    })

    if (response.data && (response.status === 201 || response.status === 200)) {
      paymentSuccess.value = true
      uploading.value = false
      
      // Show confirmation modal
      setTimeout(() => {
        showQRModal.value = false
        showPaymentConfirmation.value = true
        clearEvidence()
      }, 1000)
    }
  } catch (axiosError) {
    uploading.value = false
    console.error('Failed to submit payment evidence:', axiosError)
    if (axiosError.response && axiosError.response.data) {
      const errors = axiosError.response.data.errors || {}
      const errorMessage = axiosError.response.data.message || 'Please try again.'
      paymentError.value = errorMessage
      alert('Failed to submit payment: ' + errorMessage)
    } else {
      paymentError.value = 'An unexpected error occurred.'
      alert('Failed to submit payment. Please try again.')
    }
  }
}

const acknowledgeOnsite = async () => {
  if (!selectedRequest.value) {
    alert('No request selected.')
    return
  }

  const payload = {
    fk_doc_request_id: Number(selectedRequest.value.id ?? selectedRequest.value.requestNumber ?? 0),
    fk_user_id: Number(user.value?.id ?? user.value?.user_id ?? 0),
    request_reference_ticket: selectedRequest.value.requestNumber ?? '',
    paid_amount: Number(selectedRequest.value.amount ?? selectedRequest.value.processing_fee ?? 0),
    onsite: true,
  }

  try {
    const response = await window.axios.post(route('payments.store'), payload, {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      withCredentials: true,
    })

    if (response.data && response.status === 201) {
      isOnsitePayment.value = true
      showPaymentConfirmation.value = true
      showDetailsModal.value = false
      router.reload({ only: ['documentRequests', 'payments'] })
    }
  } catch (axiosError) {
    console.error('Failed to create onsite payment:', axiosError)
    if (axiosError.response && axiosError.response.data) {
      alert('Failed to create onsite payment: ' + (axiosError.response.data.message || 'Please try again.'))
    } else {
      alert('Failed to create onsite payment. Please try again.')
    }
  }
}

const appealRequest = async () => {
  if (!selectedRequest.value) {
    alert('No request selected.')
    return
  }

  if (selectedRequest.value.status?.toUpperCase() !== 'REJECTED') {
    alert('Only rejected requests can be appealed.')
    return
  }

  const confirmed = confirm('Do you want to edit and resubmit this rejected request? The request will be reset to pending status and you can fix any issues.')
  if (!confirmed) return

  // Get request ID from various possible locations
  const requestId = selectedRequest.value.id 
    ?? selectedRequest.value.raw?.doc_request_id 
    ?? selectedRequest.value.doc_request_id
    ?? null

  if (!requestId) {
    alert('Invalid request ID. Please try again.')
    console.error('Request ID not found. Request object:', selectedRequest.value)
    return
  }

  // NOTE: We don't need to call the appeal POST endpoint first
  // The appealForm GET endpoint will handle resetting the status
  // Calling it here might cause issues with incorrect_fields

  // Fetch full request data for the modal (this will also reset status to Pending)
  try {
    console.log('Fetching appeal data for request ID:', requestId)
    const response = await axios.get(`/document-requests/${requestId}/appeal`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      }
    })
    
    console.log('Appeal API response:', response.data)
    
    let requestData = null
    if (response.data && response.data.requestData) {
      requestData = response.data.requestData
    } else if (response.data && response.data.props && response.data.props.requestData) {
      requestData = response.data.props.requestData
    } else if (response.data && response.data.success && response.data.data) {
      requestData = response.data.data
    }
    
    if (requestData) {
      resubmissionRequestData.value = requestData
      // Ensure incorrect_fields is an array
      if (resubmissionRequestData.value.incorrect_fields && typeof resubmissionRequestData.value.incorrect_fields === 'string') {
        try {
          resubmissionRequestData.value.incorrect_fields = JSON.parse(resubmissionRequestData.value.incorrect_fields)
        } catch (e) {
          console.warn('Failed to parse incorrect_fields from API:', e)
          resubmissionRequestData.value.incorrect_fields = []
        }
      }
      if (!Array.isArray(resubmissionRequestData.value.incorrect_fields)) {
        console.warn('incorrect_fields is not an array, converting. Value:', resubmissionRequestData.value.incorrect_fields)
        resubmissionRequestData.value.incorrect_fields = []
      }
      console.log('Set resubmissionRequestData.value.incorrect_fields to:', resubmissionRequestData.value.incorrect_fields)
    } else {
      console.warn('No requestData found in response, using selectedRequest fallback')
      // Use selectedRequest data directly
      openResubmissionModalFromSelectedRequest()
    }
    
    console.log('Resubmission data after fetch:', {
      incorrect_fields: resubmissionRequestData.value?.incorrect_fields,
      incorrect_fields_type: typeof resubmissionRequestData.value?.incorrect_fields,
      incorrect_fields_is_array: Array.isArray(resubmissionRequestData.value?.incorrect_fields),
      admin_feedback: resubmissionRequestData.value?.admin_feedback,
      full_resubmissionRequestData: resubmissionRequestData.value,
    })
  } catch (error) {
    console.error('Error fetching appeal form data:', error)
    console.error('Error details:', error.response?.data)
    // Fallback: use selectedRequest data directly
    openResubmissionModalFromSelectedRequest()
  }
  
  initializeResubmissionForm()
  
  // Debug: Log incorrect_fields when modal opens
  console.log('=== RESUBMISSION MODAL OPENING ===')
  console.log('resubmissionRequestData.value:', resubmissionRequestData.value)
  console.log('incorrect_fields:', resubmissionRequestData.value?.incorrect_fields)
  console.log('incorrect_fields type:', typeof resubmissionRequestData.value?.incorrect_fields)
  console.log('incorrect_fields is array:', Array.isArray(resubmissionRequestData.value?.incorrect_fields))
  
  showResubmissionModal.value = true
  closeDetailsModal()
  
  // Debug after modal is shown (functions are now available)
  setTimeout(() => {
    const testFields = getResubmissionIncorrectFields()
    console.log('=== FINAL CHECK AFTER MODAL OPEN ===')
    console.log('Parsed incorrect_fields:', testFields)
    console.log('Test: isResubmissionFieldIncorrect("purpose"):', isResubmissionFieldIncorrect('purpose'))
    console.log('Test: isResubmissionFieldIncorrect("id_type"):', isResubmissionFieldIncorrect('id_type'))
    console.log('Test: isResubmissionFieldIncorrect("id_number"):', isResubmissionFieldIncorrect('id_number'))
    console.log('Test: isResubmissionExtraFieldIncorrect("duration_of_residency"):', isResubmissionExtraFieldIncorrect('duration_of_residency'))
    console.log('resubmissionRequestData.value:', JSON.stringify(resubmissionRequestData.value, null, 2))
  }, 200)
}

const openResubmissionModalFromSelectedRequest = () => {
  if (!selectedRequest.value) return
  
  // Use selectedRequest data to populate the form
  const raw = selectedRequest.value.raw || selectedRequest.value
  
  // Get incorrect_fields from multiple possible locations (check top level first, then raw)
  let incorrectFields = selectedRequest.value.incorrect_fields 
    || raw.incorrect_fields
    || []
  
  // Parse incorrect_fields if it's a string
  if (typeof incorrectFields === 'string') {
    try {
      incorrectFields = JSON.parse(incorrectFields)
    } catch (e) {
      console.warn('Failed to parse incorrect_fields:', e)
      incorrectFields = []
    }
  }
  if (!Array.isArray(incorrectFields)) {
    incorrectFields = []
  }
  
  resubmissionRequestData.value = {
    doc_request_id: raw.doc_request_id || selectedRequest.value.id,
    doc_request_ticket: raw.doc_request_ticket || selectedRequest.value.requestNumber,
    document_type: raw.document_type || selectedRequest.value.document_type || { name: selectedRequest.value.title },
    document_name: raw.document_name || selectedRequest.value.title,
    purpose: raw.purpose || '',
    id_type: raw.id_type || raw.valid_id_type || '',
    valid_id_number: raw.valid_id_number || '',
    extra_fields: raw.extra_fields || {},
    attachments: raw.attachments || [],
    admin_feedback: raw.admin_feedback || selectedRequest.value.admin_feedback || '',
    incorrect_fields: incorrectFields,
  }
  
  console.log('Resubmission data initialized from selectedRequest:', {
    incorrect_fields: resubmissionRequestData.value.incorrect_fields,
    incorrect_fields_type: typeof resubmissionRequestData.value.incorrect_fields,
    incorrect_fields_is_array: Array.isArray(resubmissionRequestData.value.incorrect_fields),
    selectedRequest_top_level: selectedRequest.value.incorrect_fields,
    raw_incorrect_fields: raw.incorrect_fields,
  })
}

const closeResubmissionModal = () => {
  showResubmissionModal.value = false
  resubmissionRequestData.value = null
  resetResubmissionForm()
}

const resetResubmissionForm = () => {
  resubmissionForm.value = {
    purpose: '',
    id_type: '',
    id_number: '',
    extra_fields: {},
    id_front: null,
    id_back: null,
    valid_id_content: null,
  }
  resubmissionIdFrontName.value = ''
  resubmissionIdBackName.value = ''
  resubmissionExtraFileNames.value = {}
  resubmissionPurposeOthers.value = ''
  resubmissionDocumentTypeName.value = ''
  resubmissionDocumentFields.value = []
}

const initializeResubmissionForm = () => {
  if (!resubmissionRequestData.value) return
  
  resubmissionDocumentTypeName.value = resubmissionRequestData.value.document_type?.name || resubmissionRequestData.value.document_name || 'Document'
  
  // Initialize form with existing data
  resubmissionForm.value.purpose = resubmissionRequestData.value.purpose || ''
  resubmissionForm.value.id_type = resubmissionRequestData.value.id_type || ''
  resubmissionForm.value.id_number = resubmissionRequestData.value.valid_id_number || ''
  resubmissionForm.value.extra_fields = { ...(resubmissionRequestData.value.extra_fields || {}) }
  
  // Initialize document fields
  initResubmissionDocumentFields()
}

const initResubmissionDocumentFields = () => {
  const docName = resubmissionDocumentTypeName.value
  const baseFields = documentFields[docName] || []
  
  let allFields = [...baseFields]
  
  // For Permit, check permit_type and add appropriate fields
  if (docName === 'Permit') {
    const permitType = resubmissionForm.value.extra_fields?.permit_type
    if (permitType === 'Building Permit') {
      allFields = [...allFields, ...buildingPermitFields]
    } else if (permitType === 'Business Permit') {
      allFields = [...allFields, ...businessPermitFields]
    }
  }
  
  resubmissionDocumentFields.value = allFields
  
  // Initialize extra fields
  allFields.forEach((f) => {
    if (resubmissionForm.value.extra_fields[f.name] === undefined) {
      if (f.type === 'checkbox') resubmissionForm.value.extra_fields[f.name] = []
      else if (f.type === 'select') resubmissionForm.value.extra_fields[f.name] = null 
      else resubmissionForm.value.extra_fields[f.name] = null
    }
    if (!resubmissionExtraFileNames.value[f.name]) resubmissionExtraFileNames.value[f.name] = ''
  })
}

const getResubmissionDocumentPurposes = () => {
  return documentPurposes[resubmissionDocumentTypeName.value] || []
}

const getResubmissionIdNumberLabel = () => {
  const idNumberLabels = {
    'National ID': 'National ID No.',
    "Driver's License": "Driver's License No.",
    'Passport': 'Passport No.',
    "Voter's ID": "Voter's ID No.",
    'SSS ID': 'SSS No.',
    'UMID': 'UMID No.'
  }
  return idNumberLabels[resubmissionForm.value.id_type] || 'ID Number'
}

const showResubmissionIdNumber = computed(() => {
  return !!resubmissionForm.value.id_type && (!!resubmissionForm.value.id_front || !!resubmissionForm.value.id_back)
})

const triggerResubmissionFileUpload = (side) => {
  if (side === 'front' && resubmissionFileFrontInput.value) {
    resubmissionFileFrontInput.value.click()
  } else if (side === 'back' && resubmissionFileBackInput.value) {
    resubmissionFileBackInput.value.click()
  }
}

const handleResubmissionFileUpload = (event, side) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (side === 'front') {
    resubmissionIdFrontName.value = file.name
    resubmissionForm.value.id_front = file
    resubmissionForm.value.valid_id_content = file
  } else if (side === 'back') {
    resubmissionIdBackName.value = file.name
    resubmissionForm.value.id_back = file
    if (!resubmissionForm.value.valid_id_content) {
      resubmissionForm.value.valid_id_content = file
    }
  }
}

const triggerResubmissionDynamicFileUpload = (fieldName) => {
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input && typeof input.click === 'function') {
    input.click()
  }
}

const handleResubmissionDynamicFileUpload = (event, fieldName) => {
  const file = event.target.files[0]
  if (!file) return
  resubmissionExtraFileNames.value = { ...resubmissionExtraFileNames.value, [fieldName]: file.name }
  resubmissionForm.value.extra_fields = { ...resubmissionForm.value.extra_fields, [fieldName]: file }
}

const removeResubmissionDynamicFile = (fieldName) => {
  resubmissionForm.value.extra_fields = { ...resubmissionForm.value.extra_fields, [fieldName]: null }
  resubmissionExtraFileNames.value = { ...resubmissionExtraFileNames.value, [fieldName]: '' }
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input) input.value = ''
}

const toggleResubmissionCheckbox = (fieldName, value, checked) => {
  if (!Array.isArray(resubmissionForm.value.extra_fields[fieldName])) {
    resubmissionForm.value.extra_fields[fieldName] = []
  }
  const idx = resubmissionForm.value.extra_fields[fieldName].indexOf(value)
  if (checked && idx === -1) resubmissionForm.value.extra_fields[fieldName].push(value)
  if (!checked && idx !== -1) resubmissionForm.value.extra_fields[fieldName].splice(idx, 1)
}

const handleResubmissionPurposeChange = () => {
  if (resubmissionForm.value.purpose !== 'Others') {
    resubmissionPurposeOthers.value = ''
  }
}

const getResubmissionExistingFile = (fieldName) => {
  return resubmissionRequestData.value?.attachments?.find(att => att.field_name === fieldName)
}

// Helper to get and normalize incorrect_fields array
const getResubmissionIncorrectFields = () => {
  if (!resubmissionRequestData.value) {
    console.warn('getResubmissionIncorrectFields: resubmissionRequestData.value is null')
    return []
  }
  
  let incorrectFields = resubmissionRequestData.value.incorrect_fields
  
  // If incorrect_fields is undefined or null, return empty array (all fields disabled)
  if (incorrectFields === undefined || incorrectFields === null) {
    console.warn('incorrect_fields is undefined or null')
    return []
  }
  
  // Handle string format (in case it's stored as JSON string)
  if (typeof incorrectFields === 'string') {
    try {
      incorrectFields = JSON.parse(incorrectFields)
    } catch (e) {
      console.warn('Failed to parse incorrect_fields:', e, 'Raw value:', incorrectFields)
      return []
    }
  }
  
  // Ensure it's an array
  if (!Array.isArray(incorrectFields)) {
    console.warn('incorrect_fields is not an array:', incorrectFields, 'Type:', typeof incorrectFields)
    return []
  }
  
  // Only log once per modal open to reduce console spam
  if (incorrectFields.length > 0) {
    console.log('getResubmissionIncorrectFields returning:', incorrectFields)
  }
  return incorrectFields
}

// Check if a field is marked as incorrect
const isResubmissionFieldIncorrect = (fieldName) => {
  if (!resubmissionRequestData.value) {
    return false
  }
  const incorrectFields = getResubmissionIncorrectFields()
  const isIncorrect = incorrectFields.includes(fieldName)
  // Only log if field is being checked (reduce spam)
  if (incorrectFields.length > 0) {
    console.log(`isResubmissionFieldIncorrect("${fieldName}"):`, isIncorrect, 'Available fields:', incorrectFields)
  }
  return isIncorrect
}

// Check if an extra field is marked as incorrect
const isResubmissionExtraFieldIncorrect = (fieldName) => {
  if (!resubmissionRequestData.value) {
    return false
  }
  const incorrectFields = getResubmissionIncorrectFields()
  const fieldKey = `extra_fields.${fieldName}`
  const isIncorrect = incorrectFields.includes(fieldKey)
  // Only log if field is being checked (reduce spam)
  if (incorrectFields.length > 0) {
    console.log(`isResubmissionExtraFieldIncorrect("${fieldName}") -> checking "${fieldKey}":`, isIncorrect, 'Available fields:', incorrectFields)
  }
  return isIncorrect
}

const submitResubmission = async () => {
  // Validate purpose only if it's an incorrect field
  if (isResubmissionFieldIncorrect('purpose')) {
    const purposeValue = resubmissionForm.value.purpose || resubmissionPurposeOthers.value || ''
    if (!purposeValue || purposeValue.trim() === '') {
      alert('Please provide a purpose for your request.')
      return
    }
  }

  // Validate required dynamic fields - only for incorrect fields
  const missing = resubmissionDocumentFields.value
    .filter(f => f.required && isResubmissionExtraFieldIncorrect(f.name))
    .find(f => {
      const val = resubmissionForm.value.extra_fields?.[f.name]
      if (f.type === 'file') return !val && !getResubmissionExistingFile(f.name)
      if (f.type === 'checkbox') return !Array.isArray(val) || val.length === 0
      return !val
    })
  if (missing) {
    alert(`Please provide: ${missing.label}`)
    return
  }

  // Validate ID fields only if they are incorrect
  if (isResubmissionFieldIncorrect('id_type') && resubmissionForm.value.id_type && (!resubmissionForm.value.id_front || !resubmissionForm.value.id_back)) {
    if (isResubmissionFieldIncorrect('id_front') || isResubmissionFieldIncorrect('id_back')) {
      alert('Please upload both the front and back of your selected ID.')
      return
    }
  }

  if (isResubmissionFieldIncorrect('id_number') && resubmissionForm.value.id_type && !resubmissionForm.value.id_number) {
    alert(`Please enter ${getResubmissionIdNumberLabel()}`)
    return
  }

  if (isResubmitting.value) return
  isResubmitting.value = true

  try {
    const formData = new FormData()
    const requestId = resubmissionRequestData.value.doc_request_id

    // First, call appeal endpoint to reset status to Pending
    try {
      await axios.post(route('document_requests.appeal', { id: requestId }))
    } catch (appealError) {
      console.warn('Appeal endpoint call failed, continuing with update:', appealError)
    }

    // Append scalar fields - only send incorrect fields
    if (isResubmissionFieldIncorrect('purpose')) {
      const purposeValue = resubmissionForm.value.purpose || resubmissionPurposeOthers.value || ''
      if (purposeValue && purposeValue.trim() !== '') {
        formData.append('purpose', purposeValue.trim())
      }
    }
    
    if (isResubmissionFieldIncorrect('id_type') && resubmissionForm.value.id_type) {
      formData.append('id_type', resubmissionForm.value.id_type)
    }
    
    if (isResubmissionFieldIncorrect('id_number') && resubmissionForm.value.id_number) {
      formData.append('id_number', resubmissionForm.value.id_number)
      formData.append('valid_id_number', resubmissionForm.value.id_number)
    }

    // Append ID files - only if field is incorrect
    if (isResubmissionFieldIncorrect('id_front') && resubmissionForm.value.id_front instanceof File) {
      formData.append('id_front', resubmissionForm.value.id_front)
      formData.append('valid_id_content', resubmissionForm.value.id_front)
    }
    if (isResubmissionFieldIncorrect('id_back') && resubmissionForm.value.id_back instanceof File) {
      formData.append('id_back', resubmissionForm.value.id_back)
    }

    // Append extra_fields - only incorrect ones
    Object.keys(resubmissionForm.value.extra_fields).forEach(key => {
      // Only send if this field is marked as incorrect
      if (!isResubmissionExtraFieldIncorrect(key)) return
      
      const val = resubmissionForm.value.extra_fields[key]
      if (val === null || val === undefined || val === '') return

      if (val instanceof File) {
        formData.append(`extra_fields[${key}]`, val)
      } else if (Array.isArray(val)) {
        val.forEach(v => {
          if (v !== null && v !== undefined && v !== '') {
            formData.append(`extra_fields[${key}][]`, v)
          }
        })
      } else {
        formData.append(`extra_fields[${key}]`, val)
      }
    })

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const response = await axios.put(
      route('document_requests.update', { id: requestId }),
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
      }
    )

    if (response.data.success) {
      alert('Request updated successfully! Your request has been resubmitted for review.')
      closeResubmissionModal()
      closeDetailsModal()
      // Reload the page to refresh the request list
      router.reload()
    } else {
      alert(response.data.message || 'Failed to update request. Please try again.')
    }
  } catch (error) {
    console.error('Resubmission error:', error)
    const errorMessage = error.response?.data?.message || 
                       error.response?.data?.errors ? 
                       Object.values(error.response.data.errors).flat().join(', ') : 
                       'Failed to update request. Please try again.'
    alert(errorMessage)
  } finally {
    isResubmitting.value = false
  }
}

const getRejectionReason = (request) => {
  const adminFeedback = request?.admin_feedback ?? request?.raw?.admin_feedback ?? null
  if (adminFeedback && adminFeedback.trim() !== '') {
    return adminFeedback.trim()
  }
  return 'The submitted information does not meet the requirements. Please contact the barangay office for more details.'
}

const parseDate = (val) => {
  if (!val) return null
  if (val instanceof Date && !isNaN(val)) return val
  try {
    const d = new Date(val)
    if (!isNaN(d)) return d
  } catch (e) {}
  return null
}

const getPickupSchedule = (request = null) => {
  const startRaw = request?.pickup_start ?? request?.raw?.pickup_start ?? null
  const endRaw = request?.pickup_end ?? request?.raw?.pickup_end ?? null

  const startDate = parseDate(startRaw)
  const endDate = parseDate(endRaw)

  const dateOptions = { month: 'short', day: '2-digit', year: 'numeric' }
  const timeOptions = { hour: 'numeric', minute: '2-digit' }

  if (startDate) {
    const dateStr = startDate.toLocaleDateString('en-US', dateOptions).toUpperCase()
    const startTimeStr = startDate.toLocaleTimeString('en-US', timeOptions)

    // Only show start time, not end time
    const hasTime = !(startTimeStr === '12:00 AM' && startDate.getHours() === 0 && startDate.getMinutes() === 0)
    if (hasTime) {
      return `${dateStr}, ${startTimeStr}`
    }
    return `${dateStr}, 9:00 AM`
  }

  const today = new Date()
  const pickupDate = new Date(today)
  pickupDate.setDate(today.getDate() + 3)

  const defaultDateStr = pickupDate.toLocaleDateString('en-US', dateOptions).toUpperCase()
  return `${defaultDateStr}, 9:00 AM`
}

const formatPaymentStatus = (s) => {
  if (!s) return 'UNKNOWN'
  const v = s.toString().toUpperCase()
  if (v === 'PENDING') return 'Payment Pending'
  if (v === 'APPROVED' || v === 'PAID' || v === 'SUCCESS') return 'Payment Approved'
  if (v === 'REJECTED' || v === 'FAILED') return 'Payment Rejected'
  return v
}

const getPaymentBadge = (request) => {
  // prefer normalized attached payment (see previous mapping in mappedDocumentRequests)
  const p = request?.payment ?? request?.raw?.payment ?? request?.raw?.latest_payment ?? null

  // If there's no payment record, return null -> show "No payment"
  if (!p) return null

  // try different possible status fields
  const rawStatus =
    p.status ??
    p.payment_status ??
    p.raw?.status ??
    p.raw?.payment_status ??
    null

  const s = rawStatus ? String(rawStatus).toUpperCase().trim() : null

  // map to three buckets
  if (s) {
    if (['PENDING', 'PROCESSING', 'AWAITING', 'ONHOLD', 'IN_REVIEW', 'IN-REVIEW'].includes(s)) {
      return { label: 'Payment Pending', cls: 'pending' }
    }
    if (['APPROVED', 'PAID', 'SUCCESS', 'COMPLETED', 'CONFIRMED'].includes(s)) {
      return { label: 'Payment Approved', cls: 'approved' }
    }
    if (['REJECTED', 'FAILED', 'CANCELLED', 'DECLINED'].includes(s)) {
      return { label: 'Payment Rejected', cls: 'rejected' }
    }
  }

  // If payment exists but status unknown, treat as pending to indicate action required/review
  return { label: 'Payment Pending', cls: 'pending' }
}

// Filtered document requests
const filteredDocumentRequests = computed(() => {
  let filtered = [...mappedDocumentRequests.value]

  // Filter by status
  if (statusFilter.value !== 'all') {
    if (statusFilter.value === 'resubmitted') {
      // Show only resubmitted requests (status is RESUBMITTED)
      filtered = filtered.filter(request => 
        request.status.toLowerCase() === 'resubmitted'
      )
    } else {
    filtered = filtered.filter(request => 
      request.status.toLowerCase() === statusFilter.value.toLowerCase()
    )
    }
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(request => {
      const title = (request.title || '').toLowerCase()
      const requestNumber = (request.requestNumber || '').toLowerCase()
      const date = (request.date || '').toLowerCase()
      const status = (request.status || '').toLowerCase()
      return title.includes(query) || 
             requestNumber.includes(query) || 
             date.includes(query) || 
             status.includes(query)
    })
  }

  // Sort
  if (sortOption.value === 'newest') {
    filtered.sort((a, b) => {
      // Use raw created_at for accurate sorting
      const dateA = a.raw?.created_at ? new Date(a.raw.created_at) : new Date(0)
      const dateB = b.raw?.created_at ? new Date(b.raw.created_at) : new Date(0)
      return dateB - dateA
    })
  } else if (sortOption.value === 'oldest') {
    filtered.sort((a, b) => {
      // Use raw created_at for accurate sorting
      const dateA = a.raw?.created_at ? new Date(a.raw.created_at) : new Date(0)
      const dateB = b.raw?.created_at ? new Date(b.raw.created_at) : new Date(0)
      return dateA - dateB
    })
  }

  return filtered
})

// Paginated requests
const paginatedDocumentRequests = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredDocumentRequests.value.slice(start, end)
})

// Total pages
const totalPages = computed(() => {
  return Math.ceil(filteredDocumentRequests.value.length / itemsPerPage.value)
})

// Pagination functions
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    // Scroll to top of table
    const tableContainer = document.querySelector('.requests-table-container')
    if (tableContainer) {
      tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' })
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
watch([statusFilter, searchQuery, sortOption], () => {
  currentPage.value = 1
})

// Filter functions
const toggleSortDropdown = () => {
  showSortDropdown.value = !showSortDropdown.value
  showStatusDropdown.value = false
}

const toggleStatusDropdown = () => {
  showStatusDropdown.value = !showStatusDropdown.value
  showSortDropdown.value = false
}

const selectSort = (option) => {
  sortOption.value = option
  showSortDropdown.value = false
}

const selectStatus = (status) => {
  statusFilter.value = status
  showStatusDropdown.value = false
}

const performSearch = () => {
  // Search is handled by computed property automatically
}

// Handle click outside to close dropdowns and settings
const handleClickOutside = (event) => {
  if (!event.target.closest('.filter-dropdown-wrapper')) {
    showSortDropdown.value = false
    showStatusDropdown.value = false
  }
  if (!event.target.closest('.header-actions')) {
    showSettings.value = false
  }
}

const formatDateTime = (val) => {
  if (!val) return ''
  try {
    const d = new Date(val)
    return d.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: '2-digit' })
  } catch (e) { return String(val) }
}

const canChangePaymentMethod = (request) => {
  if (!request?.payment) return false
  
  const paymentStatus = (request.payment.status ?? '').toString().toUpperCase()
  if (paymentStatus !== 'PENDING') return false
  
  const paymentMethodRaw = request.payment.raw?.paymentMethod?.pay_method_name ?? 
                          request.payment.raw?.payment_method_name ?? 
                          request.payment.raw?.paymentMethod ?? 
                          request.payment.paymentMethod ?? 
                          request.payment.raw?.pay_method_name ??
                          request.payment.payment_method_name ??
                          ''
  
  const methodUpper = paymentMethodRaw.toString().toUpperCase().trim()
  
  const isCashOrOnsite = methodUpper.includes('CASH') || 
                         methodUpper.includes('ONSITE') || 
                         methodUpper.includes('ON-SITE') || 
                         methodUpper.includes('ON SITE')
  
  if (!isCashOrOnsite && (!request.payment.transaction_ref || request.payment.transaction_ref === '' || request.payment.transaction_ref === null)) {
    return true
  }
  
  return isCashOrOnsite
}

const viewReceipt = (request) => {
  selectedReceipt.value = request
  showReceiptModal.value = true
}

const closeReceiptModal = () => {
  showReceiptModal.value = false
  selectedReceipt.value = null
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
  
  const receipt = selectedReceipt.value
  const receiptImageHtml = (receipt.payment?.receipt_image || receipt.payment?.receipt_path)
    ? `<div class="receipt-image-container">
        <p class="receipt-image-label">Proof of Payment:</p>
        <img src="${receipt.payment?.receipt_image || receipt.payment?.receipt_path}" alt="Payment Receipt" class="receipt-image" />
       </div>`
    : '<div class="no-receipt-container"><p class="no-receipt-message">No proof of payment available</p></div>'
  
  const printWindow = window.open('', '_blank')
  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Payment Receipt</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        .receipt-header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #ff8c42; padding-bottom: 20px; }
        .receipt-title { font-size: 28px; font-weight: 700; color: #ff8c42; margin: 0 0 10px 0; }
        .receipt-details-box { background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 25px; }
        .receipt-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e0e0e0; }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { font-weight: 600; color: #666; font-size: 14px; }
        .receipt-value { font-weight: 600; color: #333; font-size: 14px; }
        .receipt-value.amount { color: #239640; font-size: 20px; font-family: 'Courier New', monospace; }
        .receipt-image-container { margin: 25px 0; text-align: center; }
        .receipt-image-label { font-weight: 600; color: #666; font-size: 14px; margin-bottom: 10px; }
        .receipt-image { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .receipt-note { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin: 25px 0; }
        .receipt-note p { margin: 0; font-size: 13px; color: #856404; line-height: 1.6; }
      </style>
    </head>
    <body>
      <div class="receipt-header">
        <h1 class="receipt-title">Payment Receipt</h1>
        <p style="color: #666; margin: 0;">Barangay</p>
      </div>
      <div class="receipt-details-box">
        <div class="receipt-row">
          <span class="receipt-label">Request No:</span>
          <span class="receipt-value">${receipt.requestNumber || 'N/A'}</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Document:</span>
          <span class="receipt-value">${receipt.title || 'N/A'}</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Date:</span>
          <span class="receipt-value">${receipt.date || 'N/A'}</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Payer:</span>
          <span class="receipt-value">${user.value?.name || 'N/A'}</span>
        </div>
        <div class="receipt-row">
          <span class="receipt-label">Amount Paid:</span>
          <span class="receipt-value amount">₱${((receipt.payment?.amount ?? receipt.amount) || 0).toFixed(2)}</span>
        </div>
        ${receipt.payment?.transaction_ref ? `
        <div class="receipt-row">
          <span class="receipt-label">Transaction ID:</span>
          <span class="receipt-value">${receipt.payment.transaction_ref}</span>
        </div>
        ` : ''}
        <div class="receipt-row">
          <span class="receipt-label">Status:</span>
          <span class="receipt-value" style="color: ${(receipt.payment?.status?.toUpperCase() === 'APPROVED' || receipt.payment?.status?.toUpperCase() === 'PAID') ? '#239640' : '#dc3545'}">
            ${formatPaymentStatus(receipt.payment?.status) || 'N/A'}
          </span>
        </div>
      </div>
      ${receiptImageHtml}
      <div class="receipt-note">
        <p>Thank you for your payment. This digital receipt is proof of payment issued by Barangay.</p>
      </div>
    </body>
    </html>
  `)
  
  printWindow.document.close()
  printWindow.focus()
  
  setTimeout(() => {
    printWindow.print()
  }, 500)
}

const downloadReceipt = async () => {
  try {
    if (!selectedReceipt.value) return
    
    const receipt = selectedReceipt.value
    const receiptImageHtml = (receipt.payment?.receipt_image || receipt.payment?.receipt_path)
      ? `<div class="receipt-image-container">
          <p class="receipt-image-label">Proof of Payment:</p>
          <img src="${receipt.payment?.receipt_image || receipt.payment?.receipt_path}" alt="Payment Receipt" class="receipt-image" />
         </div>`
      : '<div class="no-receipt-container"><p class="no-receipt-message">No proof of payment available</p></div>'
    
    const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Payment Receipt - ${receipt.requestNumber}</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 40px; }
          .receipt-header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #ff8c42; padding-bottom: 20px; }
          .receipt-title { font-size: 28px; font-weight: 700; color: #ff8c42; margin: 0 0 10px 0; }
          .receipt-details-box { background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 25px; }
          .receipt-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e0e0e0; }
          .receipt-row:last-child { border-bottom: none; }
          .receipt-label { font-weight: 600; color: #666; font-size: 14px; }
          .receipt-value { font-weight: 600; color: #333; font-size: 14px; }
          .receipt-value.amount { color: #239640; font-size: 20px; font-family: 'Courier New', monospace; }
          .receipt-image-container { margin: 25px 0; text-align: center; }
          .receipt-image-label { font-weight: 600; color: #666; font-size: 14px; margin-bottom: 10px; }
          .receipt-image { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
          .receipt-note { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin: 25px 0; }
          .receipt-note p { margin: 0; font-size: 13px; color: #856404; line-height: 1.6; }
        </style>
      </head>
      <body>
        <div class="receipt-header">
          <h1 class="receipt-title">Payment Receipt</h1>
          <p style="color: #666; margin: 0;">Barangay</p>
        </div>
        <div class="receipt-details-box">
          <div class="receipt-row">
            <span class="receipt-label">Request No:</span>
            <span class="receipt-value">${receipt.requestNumber || 'N/A'}</span>
          </div>
          <div class="receipt-row">
            <span class="receipt-label">Document:</span>
            <span class="receipt-value">${receipt.title || 'N/A'}</span>
          </div>
          <div class="receipt-row">
            <span class="receipt-label">Date:</span>
            <span class="receipt-value">${receipt.date || 'N/A'}</span>
          </div>
          <div class="receipt-row">
            <span class="receipt-label">Payer:</span>
            <span class="receipt-value">${user.value?.name || 'N/A'}</span>
          </div>
          <div class="receipt-row">
            <span class="receipt-label">Amount Paid:</span>
            <span class="receipt-value amount">₱${((receipt.payment?.amount ?? receipt.amount) || 0).toFixed(2)}</span>
          </div>
          ${receipt.payment?.transaction_ref ? `
          <div class="receipt-row">
            <span class="receipt-label">Transaction ID:</span>
            <span class="receipt-value">${receipt.payment.transaction_ref}</span>
          </div>
          ` : ''}
          <div class="receipt-row">
            <span class="receipt-label">Status:</span>
            <span class="receipt-value" style="color: ${(receipt.payment?.status?.toUpperCase() === 'APPROVED' || receipt.payment?.status?.toUpperCase() === 'PAID') ? '#239640' : '#dc3545'}">
              ${formatPaymentStatus(receipt.payment?.status) || 'N/A'}
            </span>
          </div>
        </div>
        ${receiptImageHtml}
        <div class="receipt-note">
          <p>Thank you for your payment. This digital receipt is proof of payment issued by Barangay.</p>
        </div>
      </body>
      </html>
    `
    
    const blob = new Blob([htmlContent], { type: 'text/html' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `receipt-${receipt.requestNumber}.html`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Failed to download receipt:', err)
    alert('Failed to download receipt. Please try again.')
  }
}

const handlePurposeChange = () => {
  if (form.purpose !== 'Others') {
    purposeOthers.value = ''
  }
}

const testing = true  // keep while you test — set false when you want strict validation

// Submit handler — sends form.extra_fields as JSON (or file objects — Inertia's FormData handles it)
// Clear errors helper
const clearErrors = () => {
  formErrors.value = {}
  submitError.value = ''
  fieldErrors.value = {}
}

// Validation helper
const validateForm = () => {
  clearErrors()
  let isValid = true
  const errors = {}

  // Check if user is restricted
  if (restrictions?.restrict_document_request) {
    submitError.value = 'You are restricted from making document requests. Please contact the admin for more information.'
    isValid = false
    return isValid
  }

  // Validate purpose
  if (!form.purpose || form.purpose.trim() === '') {
    errors.purpose = 'Please provide a description/purpose for your request.'
    isValid = false
  }

  // Validate basic fields
  if (!form.first_name || form.first_name.trim() === '') {
    errors.first_name = 'First name is required.'
    isValid = false
  }
  if (!form.last_name || form.last_name.trim() === '') {
    errors.last_name = 'Last name is required.'
    isValid = false
  }

  // Validate ID fields
  if (form.id_type) {
    if (!form.id_front) {
      errors.id_front = 'Please upload the front of your ID.'
      isValid = false
    }
    if (!form.id_back) {
      errors.id_back = 'Please upload the back of your ID.'
      isValid = false
    }
    if (!form.id_number || form.id_number.trim() === '') {
      errors.id_number = `Please enter ${idNumberLabel.value}`
      isValid = false
    }
  }

  // Validate dynamic required fields
  const missingFields = currentDocumentFields.value
    .filter(f => f.required)
    .filter(f => {
      const val = form.extra_fields?.[f.name]
      if (f.type === 'file') return !val
      if (f.type === 'checkbox') return !Array.isArray(val) || val.length === 0
      return !val || val === '' || val === null
    })

  missingFields.forEach(field => {
    errors[`extra_fields.${field.name}`] = `${field.label} is required.`
    isValid = false
  })

  formErrors.value = errors
  return isValid
}

const submitRequest = () => {
  // Clear previous errors
  clearErrors()

  // Validate form
  if (!validateForm()) {
    // Scroll to first error
    const firstErrorField = Object.keys(formErrors.value)[0]
    if (firstErrorField) {
      const errorElement = document.querySelector(`[data-field="${firstErrorField}"]`) || 
                          document.querySelector(`[name="${firstErrorField}"]`)
      if (errorElement) {
        errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
        errorElement.focus()
      }
    }
    return
  }

    // ensure backend receives a fallback single-file field if it expects 'document'
    if (!form.document && form.id_front) {
        form.document = form.id_front
    }

    // Map the visible UI id_number to the backend field valid_id_number
  form.valid_id_number = form.id_number || ''

  // Ensure valid_id_content is the front file if available (safety fallback)
  if (form.id_front) {
    form.valid_id_content = form.id_front
  }

  // Handle Permit type - use the actual permit type as document_name
  const permitType = selectedPermitType.value || form.extra_fields?.permit_type
  if (selectedDocType.value === 'Permit' && permitType) {
    form.document_name = permitType
    // Ensure permit_type is set in extra_fields
    if (!form.extra_fields) {
      form.extra_fields = {}
    }
    form.extra_fields.permit_type = permitType
  } else {
    form.document_name = selectedDocType.value
  }

  if (isSubmitting.value) return
  isSubmitting.value = true

  // Manually construct FormData to ensure all files are properly sent
  // This is necessary because Inertia's form.post might not properly serialize nested File objects
  const formData = new FormData()
  
  // Append all scalar fields
  const scalarFields = [
    'document_name', 'fk_document_type_id', 'last_name', 'first_name', 'middle_name', 'suffix',
    'birthdate', 'sex', 'civil_status', 'address', 'contact_number', 'purpose',
    'id_type', 'id_number', 'valid_id_number', 'pickup_item', 'pickup_location', 'pickup_start', 'pickup_end', 'person_to_look'
  ]
  
  scalarFields.forEach(key => {
    const value = form[key]
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  
  // Append ID files (valid_id_content, id_front, id_back)
  if (form.valid_id_content instanceof File) {
    formData.append('valid_id_content', form.valid_id_content)
  }
  if (form.id_front instanceof File) {
    formData.append('id_front', form.id_front)
  }
  if (form.id_back instanceof File) {
    formData.append('id_back', form.id_back)
  }
  if (form.document instanceof File) {
    formData.append('document', form.document)
  }
  
  // Handle occupation field: if "Other" is selected, use the custom input value
  if (form.extra_fields?.occupation === 'Other' && form.extra_fields?.occupation_other) {
    form.extra_fields.occupation = form.extra_fields.occupation_other
    // Remove the temporary occupation_other field
    delete form.extra_fields.occupation_other
  }
  
  // Append extra_fields: files, arrays, or scalars
  if (form.extra_fields && typeof form.extra_fields === 'object') {
    Object.keys(form.extra_fields).forEach(key => {
      const val = form.extra_fields[key]
      if (val === null || val === undefined || val === '') return
      
      if (val instanceof File) {
        // Single file - append as extra_fields[fieldName]
        formData.append(`extra_fields[${key}]`, val)
        console.log(`Appending file: extra_fields[${key}] =`, val.name)
      } else if (Array.isArray(val)) {
        // Arrays (e.g., checkbox values)
        val.forEach(v => {
          if (v !== null && v !== undefined && v !== '') {
            formData.append(`extra_fields[${key}][]`, v)
          }
        })
      } else {
        // Scalar values
        formData.append(`extra_fields[${key}]`, val)
      }
    })
  }
  
  console.log('FormData entries:')
  for (let pair of formData.entries()) {
    console.log(pair[0], ':', pair[1] instanceof File ? `File: ${pair[1].name}` : pair[1])
  }
  
  // Use axios to send FormData directly (axios is available globally via bootstrap.js)
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  
  window.axios.post(route('requests.store'), formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
    },
    onUploadProgress: (progressEvent) => {
      if (progressEvent.total) {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        console.log('Upload progress:', percentCompleted + '%')
      }
    }
  }).then(response => {
    // Extract ticket from response - try multiple possible locations
    const ticket = response?.data?.ticket 
      || response?.data?.data?.ticket 
      || (response?.data?.success && response?.data?.ticket)
      || 'TICKET_PENDING'
    
    console.log('Full response:', response)
    console.log('Response data:', response?.data)
    console.log('Extracted ticket:', ticket)
    
    if (ticket === 'TICKET_PENDING') {
      console.error('Failed to extract ticket from response. Full response:', response)
    }
    
    requestNumber.value = ticket
    currentView.value = 'success'
    isSubmitting.value = false
    console.log('Request submitted successfully, ticket:', ticket)
    
    // Reload document requests list to show the newly submitted request
    router.reload({ only: ['documentRequests', 'payments'] })
  }).catch(error => {
    console.error('Error submitting request:', error)
    console.error('Error response:', error.response?.data)
    isSubmitting.value = false
    
    // Handle different error types
    if (error.response) {
      // Server responded with error
      if (error.response.status === 422) {
        // Validation errors
        const validationErrors = error.response.data.errors || {}
        const newErrors = {}
        
        // Map validation errors to form fields
        Object.keys(validationErrors).forEach(key => {
          const errorMessages = Array.isArray(validationErrors[key]) 
            ? validationErrors[key] 
            : [validationErrors[key]]
          newErrors[key] = errorMessages[0]
        })
        
        formErrors.value = { ...formErrors.value, ...newErrors }
        
        // Set general error message
        const firstError = Object.values(validationErrors)[0]
        submitError.value = Array.isArray(firstError) ? firstError[0] : firstError
        
        // Scroll to first error
        const firstErrorField = Object.keys(newErrors)[0]
        if (firstErrorField) {
          setTimeout(() => {
            const errorElement = document.querySelector(`[data-field="${firstErrorField}"]`) || 
                                document.querySelector(`[name="${firstErrorField}"]`)
            if (errorElement) {
              errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
              errorElement.focus()
            }
          }, 100)
        }
      } else if (error.response.status === 403) {
        submitError.value = 'You do not have permission to perform this action.'
      } else if (error.response.status === 500) {
        submitError.value = 'A server error occurred. Please try again later or contact support.'
      } else {
        submitError.value = error.response.data?.message || 'An error occurred while submitting your request. Please try again.'
      }
    } else if (error.request) {
      // Request made but no response received
      submitError.value = 'Network error. Please check your internet connection and try again.'
    } else {
      // Error setting up request
      submitError.value = error.message || 'An unexpected error occurred. Please try again.'
    }
    
    // Scroll to error message
    setTimeout(() => {
      const errorContainer = document.querySelector('.error-message-container')
      if (errorContainer) {
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }
    }, 100)
  })
}

const viewRequest = async () => {
  currentView.value = 'list'
  
  // Wait for Vue to update the view and for the reloaded data to be available
  await nextTick()
  
  // Function to find and show the request
  const findAndShowRequest = () => {
    const ticket = requestNumber.value
    if (!ticket) return false
    
    // Normalize the ticket for comparison (remove # if present)
    const normalizedTicket = ticket.replace(/^#/, '')
    
    // Search in mappedDocumentRequests (which includes all requests)
    const foundRequest = mappedDocumentRequests.value.find(req => {
      const reqNumber = (req.requestNumber || '').toString()
      // Match by requestNumber - handle different formats
      const normalizedReqNumber = reqNumber.replace(/^#/, '')
      return normalizedReqNumber === normalizedTicket || 
             normalizedReqNumber === ticket || 
             reqNumber === ticket ||
             reqNumber === `#${ticket}`
    })
    
    if (foundRequest) {
      // Open the detail modal with the found request
      viewRequestDetails(foundRequest)
      return true
    }
    return false
  }
  
  // Try to find immediately
  if (!findAndShowRequest()) {
    // If not found, wait a bit for the async reload to complete and try again
    setTimeout(() => {
      if (!findAndShowRequest()) {
        // Final retry after a longer delay in case the reload takes longer
        setTimeout(() => {
          findAndShowRequest()
        }, 800)
      }
    }, 300)
  }
}

// Function to scroll to a specific request and highlight it
const scrollToRequest = async (requestId) => {
  // Find the request in the full filtered list
  const requestIndex = filteredDocumentRequests.value.findIndex(req => req.id === requestId)
  
  if (requestIndex === -1) {
    // Request not found in filtered list
    return
  }
  
  // Calculate which page the request is on (0-indexed, so add 1)
  const targetPage = Math.floor(requestIndex / itemsPerPage.value) + 1
  
  // Navigate to the correct page if not already there
  if (currentPage.value !== targetPage) {
    goToPage(targetPage)
    // Wait for Vue to update the DOM after page change
    await nextTick()
    // Give a bit more time for the scroll animation to complete
    await new Promise(resolve => setTimeout(resolve, 300))
  }
  
  // Now find and highlight the request element
  await nextTick()
  const requestElement = document.querySelector(`[data-request-id="${requestId}"]`)
  if (requestElement) {
    requestElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
    // Highlight the request briefly with light green (same as posts)
    requestElement.style.transition = 'background-color 0.3s'
    requestElement.style.backgroundColor = '#d4edda'
    setTimeout(() => {
      requestElement.style.backgroundColor = ''
    }, 2000)
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  activeTab.value = 'documents'

  // Fetch unread notification count
  fetchUnreadCount()
  
  // Set up polling to update unread count every 30 seconds
  const unreadCountInterval = setInterval(() => {
    fetchUnreadCount()
  }, 30000)
  
  // Store interval ID for cleanup
  window.unreadCountInterval = unreadCountInterval

  // try to read flashed ticket - if there's a success ticket, show success view
  try {
    const flashedTicket = page?.props?.value?.flash?.ticket ?? page?.props?.flash?.ticket ?? page?.props?.value?.ticket ?? page?.props?.ticket ?? null
    if (flashedTicket) {
      requestNumber.value = flashedTicket
      currentView.value = 'success'
    } else {
      // Default to list view to show submitted requests
      currentView.value = 'list'
    }
  } catch (e) {
    currentView.value = 'list'
  }

  // Check for request query parameter from notification click
  const urlParams = new URLSearchParams(window.location.search)
  const requestId = urlParams.get('request')
  if (requestId) {
    // Convert to number if it's a numeric string
    const requestIdNum = Number(requestId)
    if (!isNaN(requestIdNum)) {
      // Try to scroll to request, with retries in case data isn't loaded yet
      const tryScrollToRequest = async (retries = 3) => {
        if (mappedDocumentRequests.value.length > 0) {
          await scrollToRequest(requestIdNum)
        } else if (retries > 0) {
          // Wait a bit and retry if data isn't loaded yet
          setTimeout(() => tryScrollToRequest(retries - 1), 500)
        }
      }
      // Start trying after a short delay to allow initial render
      setTimeout(() => tryScrollToRequest(), 500)
    }
  }
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    
    // Clear unread count polling interval
    if (window.unreadCountInterval) {
        clearInterval(window.unreadCountInterval)
        window.unreadCountInterval = null
    }
})
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Legacy styles - keeping for backward compatibility */
.dynamic-field {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 1rem;
}

.field-label {
    width: 220px;
    margin: 0;
    font-weight: 600;
    white-space: nowrap;
}

.short-input {
    width: 200px;
    max-width: 250px;
}

/* Dynamic Fields Styling */
.barangay-cert-fields-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.barangay-cert-fields-row .dynamic-field-wrapper {
    margin-bottom: 0;
}

.dynamic-field-wrapper {
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dynamic-field-wrapper:hover {
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.15);
    transform: translateY(-1px);
}

.field-header {
    margin-bottom: 12px;
}

.field-label {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #333;
    margin-bottom: 6px;
}

.required-star {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
    font-weight: 700;
}

.section-title .required-star {
    color: #e74c3c;
    font-size: 18px;
    margin-left: 4px;
    font-weight: 700;
}

.optional-text {
    color: #999;
    font-size: 13px;
    font-weight: 500;
    margin-left: 8px;
}

.field-description {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
    line-height: 1.5;
    font-style: italic;
}

.field-input-wrapper {
    width: 100%;
}

.form-input,
.form-textarea,
select {
    width: 100%;
    max-width: 100%;
}

select option[value=""] {
    color: #888;
}

/* File Upload Styling */
.file-upload-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* File Uploads Grid Layout */
.file-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.file-upload-item {
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.file-upload-item:hover {
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.15);
    transform: translateY(-1px);
}

.file-upload-header {
    margin-bottom: 10px;
}

.file-upload-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
}

.file-upload-description {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
    line-height: 1.4;
    font-style: italic;
}

.file-upload-controls {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.uploaded-file-info-compact {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: #e8f8ed;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(76, 175, 80, 0.15);
    font-size: 13px;
    width: 100%;
}

.file-name-compact {
    flex: 1;
    color: #2e7d32;
    font-weight: 600;
    font-size: 12px;
    word-break: break-all;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.upload-btn-dynamic {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    justify-content: center;
    width: auto;
    min-width: 100px;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

.upload-btn-dynamic:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.upload-icon {
    font-size: 18px;
}

.uploaded-file-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-radius: 8px;
    border: 1px solid #4caf50;
    box-shadow: 0 2px 6px rgba(76, 175, 80, 0.2);
}

.file-checkmark {
    color: #4caf50;
    font-size: 18px;
    font-weight: 700;
}

.file-name {
    flex: 1;
    color: #2e7d32;
    font-weight: 600;
    font-size: 14px;
    word-break: break-all;
}

.remove-file-btn-small {
    background: #e74c3c;
    color: white;
    border: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}

.remove-file-btn-small:hover {
    background: #c0392b;
    transform: scale(1.1);
}

/* Checkbox Group Styling */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    transform: translateX(3px);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff8c42;
}

.checkbox-label span {
    font-size: 14px;
    color: #333;
    font-weight: 500;
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
    white-space: nowrap;
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
    font-size: 15px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.profile-role {
    font-size: 12px;
    background: #239640;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    backdrop-filter: blur(10px);
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
    stroke: currentColor;
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

.unread-badge-nav {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 12px;
    min-width: 20px;
    text-align: center;
    margin-left: auto;
    box-shadow: 0 2px 6px rgba(255, 140, 66, 0.4);
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
    transition: all 0.3s ease;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.faq-btn .nav-icon {
    stroke: white;
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

.document-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.document-header h2 {
    font-size: 22px;
    font-weight: 700;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

.document-selection {
    display: grid;
    grid-template-columns: 320px 1fr;
    min-height: 600px;
    gap: 0;
}

.document-types-wrapper {
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    padding: 20px 0;
}

.document-types-wrapper .document-types {
    padding: 0;
    margin-top: 0;
    background: transparent;
    border-right: none;
}

.back-btn-selection {
    background: transparent;
    border: none;
    color: #000;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 15px;
    padding: 10px 25px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
}

.back-btn-selection:hover {
    color: #ff8c42;
}

.back-btn-selection svg {
    margin-right: 6px;
}

.document-types {
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

.doc-type-btn {
    background: transparent;
    border: none;
    padding: 15px 25px;
    text-align: left;
    font-size: 14px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    border-left: 4px solid transparent;
    transition: all 0.2s;
}

.doc-type-btn:hover {
    background: #fff;
    color: #ff8c42;
}

.doc-type-btn.active {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border-left-color: #ff8c42;
}

.doc-type-btn.disabled,
.doc-type-btn:disabled {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
    opacity: 0.6;
    position: relative;
}

.doc-type-btn.disabled:hover,
.doc-type-btn:disabled:hover {
    background: #f5f5f5;
    color: #999;
}

.restricted-badge {
    margin-left: 8px;
    font-size: 12px;
    opacity: 0.8;
}

.document-info {
    padding: 40px;
    overflow-y: auto;
    max-height: calc(100vh - 250px);
}

.doc-title {
    font-size: 40px;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
}

.doc-description {
    font-size: 20px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 30px;
    text-align: justify;
}

.requirements-section h4 {
    color: #2bb24a;
    font-size: 25px;
    font-weight: 700;
    margin-bottom: 15px;
}

.requirements-list {
    list-style-position: inside;
    color: #333;
    font-size: 18px;
    line-height: 2;
    font-weight: 600;
    margin-bottom: 40px;
}

.permit-type-selection {
    margin: 30px 0;
    padding: 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
}

.permit-type-title {
    color: #333;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
}

.permit-type-options {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.permit-type-btn {
    flex: 1;
    background: white;
    border: 2px solid #ddd;
    padding: 15px 25px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.permit-type-btn:hover {
    border-color: #ff8c42;
    color: #ff8c42;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.2);
}

.permit-type-btn.active {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.permit-type-description {
    padding: 15px;
    background: white;
    border-radius: 8px;
    border-left: 4px solid #ff8c42;
}

.permit-type-description p {
    margin: 0;
    font-size: 16px;
    line-height: 1.6;
    color: #555;
}

.permit-type-description strong {
    color: #ff8c42;
}

.request-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    display: block;
    margin-left: auto;
    margin-top: 20px;
}

.request-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.request-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.request-form-container {
    padding: 30px 40px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.form-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    width: 100%;
}

.back-btn {
    background: transparent;
    border: none;
    color: #000;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    padding: 0;
    flex-shrink: 0;
}

.back-btn:hover {
    color: #ff8c42;
}

.form-document-type {
    font-size: 16px;
    color: #666;
    margin: 0;
    padding: 0;
    background: transparent;
    border-radius: 0;
    text-align: right;
    flex-shrink: 0;
    white-space: nowrap;
}

.form-document-type strong {
    color: #ff8c42;
    font-weight: 700;
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    text-align: center;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
    display: block;
    width: 100%;
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-bottom: 30px;
}

.form-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dynamic-fields {
    background: white;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.section-title .icon {
    font-size: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.form-grid:last-child {
    margin-bottom: 0;
}

.form-input {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.form-input:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
}

.contact-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.full-width {
    width: 100%;
}

.otp-section {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
}

.verify-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.verify-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    background: white;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-textarea:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
}

.purpose-id-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.purpose-field {
    display: flex;
    flex-direction: column;
}

.id-type-field {
    display: flex;
    flex-direction: column;
}

.id-type-upload-row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.id-type-select {
    flex: 1;
    min-width: 0;
}

.id-type-upload-row .upload-row {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.id-number-field {
    display: flex;
    flex-direction: column;
}

.id-number-field .field-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
    width: auto;
}

.id-number-field .field-label span {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
    font-weight: 700;
}

.upload-section {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 25px;
    width: 100%;
}

.upload-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
    font-size: 12px;
    white-space: nowrap;
    flex-shrink: 0;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}
.upload-row {
    display: flex;
    gap: 8px;
}

.upload-btn:hover {
    transform: translateY(-2px);
}

.file-input-hidden {
    display: none;
}

.uploaded-files-display {
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.uploaded-file {
    font-size: 13px;
    color: #2bb24a;
    font-weight: 600;
    margin: 0;
}

.submit-btn {
    width: 30%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: block;
    margin-left: auto;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.success-container {
    padding: 60px 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 600px;
}

.success-content {
    text-align: center;
    max-width: 600px;
}

.checkmark {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    font-weight: 700;
    box-shadow: 0 8px 25px rgba(43, 178, 74, 0.4);
    margin: 0 auto 30px;
}

.success-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.request-number {
    font-size: 14px;
    color: #ff8c42;
    font-weight: 700;
    margin-bottom: 30px;
}

.success-message {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 40px;
}

.success-message p {
    margin-bottom: 15px;
}

.success-message .highlight {
    color: #ff8c42;
    font-weight: 700;
}

.view-request-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    transition: all 0.3s;
}

.view-request-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
}

/* Scrollbar Styles */
.document-types::-webkit-scrollbar,
.document-info::-webkit-scrollbar,
.request-form-container::-webkit-scrollbar {
    width: 6px;
}

.document-types::-webkit-scrollbar-track,
.document-info::-webkit-scrollbar-track,
.request-form-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.document-types::-webkit-scrollbar-thumb,
.document-info::-webkit-scrollbar-thumb,
.request-form-container::-webkit-scrollbar-thumb {
    background: #e2e2e2;
    border-radius: 3px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }

    .document-selection {
        grid-template-columns: 280px 1fr;
    }

    .form-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .purpose-id-row {
        grid-template-columns: 1fr;
    }
    
    .id-type-upload-row {
        flex-direction: column;
    }
    
    .id-type-upload-row .upload-row {
        width: 100%;
    }
    
    .id-type-upload-row .upload-btn {
        flex: 1;
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
    }

    .document-selection {
        grid-template-columns: 1fr;
    }

    .document-types {
        border-right: none;
        border-bottom: 1px solid #e0e0e0;
        max-height: 300px;
    }

    .document-info {
        padding: 25px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .purpose-id-row {
        grid-template-columns: 1fr;
    }
    
    .id-type-upload-row {
        flex-direction: column;
    }
    
    .id-type-upload-row .upload-row {
        width: 100%;
    }
    
    .id-type-upload-row .upload-btn {
        flex: 1;
    }
    
    .barangay-cert-fields-row {
        grid-template-columns: 1fr;
    }

    .upload-section {
        flex-direction: column;
    }

    .otp-section {
        grid-template-columns: 1fr;
    }

    .request-form-container {
        padding: 20px;
    }

    /* Responsive Requests List View */
    .requests-list-view {
        padding: 20px;
    }

    .requests-header {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }

    .request-new-btn {
        width: 100%;
        justify-content: center;
    }

    .requests-table-container {
        overflow-x: auto;
    }

    .requests-table {
        min-width: 600px;
    }

    .requests-table th,
    .requests-table td {
        padding: 12px 15px;
        font-size: 13px;
    }

    .requests-table th:first-child,
    .requests-table td:first-child {
        padding-left: 15px;
    }

    .requests-table th:last-child,
    .requests-table td:last-child {
        padding-right: 15px;
    }

    .requests-table     .status-badge {
        font-size: 11px;
        padding: 6px 12px;
    }

    /* Filter section responsive */
    .filter-section {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
        padding: 15px;
    }

    .filter-left {
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-right {
        flex-direction: column;
        width: 100%;
    }

    .search-container {
        width: 100%;
    }

    .search-input {
        width: 100%;
        flex: 1;
    }

    .request-new-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .profile-card {
        padding: 15px;
    }

    .document-info {
        padding: 20px;
    }

    .doc-title {
        font-size: 22px;
    }

    .request-form-container {
        padding: 15px;
    }

    .form-section {
        padding: 20px;
    }

    .success-container {
        padding: 40px 20px;
    }

    .checkmark {
        width: 100px;
        height: 100px;
        font-size: 50px;
    }

    /* Mobile Responsive for Requests List */
    .requests-list-view {
        padding: 15px;
    }

    .requests-header {
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .request-new-btn {
        font-size: 12px;
        padding: 8px 16px;
    }

    .requests-table {
        font-size: 12px;
    }

    .requests-table th,
    .requests-table td {
        padding: 10px 12px;
        font-size: 12px;
    }

    .requests-table th {
        font-size: 11px;
    }

    .requests-table .status-badge {
        font-size: 10px;
        padding: 5px 10px;
    }
}

/* Requests List View Styles */
.requests-list-view {
    padding: 25px 30px;
    min-height: 600px;
}

/* Filter Section Styles */
.filter-section {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-radius: 12px 12px 0 0;
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
    color: #333;
}

.filter-dropdown-btn:hover {
    border-color: #ff8c42;
}

.filter-arrow {
    font-size: 10px;
    transition: transform 0.3s ease;
    color: #666;
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
    gap: 5px;
    background: white;
    border-radius: 8px;
    padding: 2px;
    border: 1px solid #ddd;
    align-items: center;
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
    background: transparent;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: #666;
}

.search-btn:hover {
    background: #f0f0f0;
    color: #ff8c42;
}

.search-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
}

.requests-header {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
}

.request-new-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.request-new-btn:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.request-new-btn.disabled {
    background: #ccc;
    color: #666;
    cursor: not-allowed;
    opacity: 0.6;
}

.request-new-wrapper {
    position: relative;
    display: inline-block;
}

.restriction-info-icon {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: help;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.restriction-banner {
    background: linear-gradient(135deg, #fee, #fdd);
    border-left: 4px solid #e74c3c;
    padding: 15px 25px;
    margin: 20px 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.2);
}

.restriction-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.restriction-text {
    flex: 1;
}

.restriction-text strong {
    display: block;
    color: #e74c3c;
    font-size: 16px;
    margin-bottom: 5px;
}

.restriction-text p {
    color: #c0392b;
    font-size: 14px;
    margin: 0;
    line-height: 1.5;
}

.request-new-btn:active {
    transform: translateY(0);
}

.requests-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.requests-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.requests-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
}

.requests-table th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #666;
}

.requests-table th:first-child {
    padding-left: 25px;
}

.requests-table th:nth-child(4) {
    text-align: center;
}

.requests-table th:last-child {
    padding-right: 25px;
    text-align: center;
}

.requests-table td:nth-child(4) {
    text-align: center;
    vertical-align: middle;
}

.requests-table td:nth-child(5) {
    text-align: center;
    vertical-align: middle;
}

.payment-status-na {
    color: #999;
    font-size: 14px;
}

.requests-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
    cursor: pointer;
    background: white;
}

.requests-table tbody tr:hover {
    background: #f5f5f5;
}

.requests-table tbody tr:last-child {
    border-bottom: none;
}

.requests-table td {
    padding: 18px 20px;
    font-size: 16px;
    color: #333;
    vertical-align: middle;
}

.requests-table td:first-child {
    padding-left: 25px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
}

.requests-table td:last-child {
    padding-right: 25px;
    vertical-align: middle;
    text-align: center;
}

.requests-table td:nth-child(2) {
    color: #ff8c42;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    font-size: 17px;
}

.requests-table td:nth-child(3) {
    color: #666;
    font-size: 15px;
}

.no-requests {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 60px 40px !important;
    font-size: 15px;
}

.request-row {
    transition: all 0.2s ease;
}

.request-row:hover {
    background: #f5f5f5 !important;
}

/* Pagination Styles */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding: 20px;
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

/* Status badges in table - specific selector to avoid conflicts */
.requests-table .status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: auto;
    height: auto;
    margin: 0 auto;
    line-height: 1;
}

.requests-table .status-badge .badge-icon {
    font-size: 12px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.requests-table .status-badge .badge-icon-svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
}

.requests-table .status-badge .badge-text {
    font-weight: bold;
    font-size: 12px;
    text-transform: uppercase;
}

.requests-table .status-badge.pending,
.requests-table .status-badge.resubmitted {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.requests-table .status-badge.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.requests-table .status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Payment List Badge Styles */
.payment-list-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 12px;
    white-space: nowrap;
    margin: 0 auto;
}

/* Payment badge variants */
.payment-list-badge.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.payment-list-badge.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.payment-list-badge.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* When no payment */
.payment-list-badge.none {
    background: transparent !important;
    border: 2px solid #dc3545 !important;
    color: #dc3545 !important;
}

.payment-list-badge.none .badge-icon svg {
    stroke: #dc3545;
}

/* Badge icon */
.payment-list-badge .badge-icon {
    font-size: 12px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.payment-list-badge .badge-icon-svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
}

/* Badge text */
.payment-list-badge .badge-text {
    font-weight: bold;
    font-size: 12px;
    text-transform: uppercase;
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
    z-index: 10000;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 15px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.details-modal {
    padding: 30px 40px;
}

.close-modal-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.close-modal-btn:hover {
    background: #f0f0f0;
    color: #333;
}

.modal-icon {
    text-align: center;
    margin-bottom: 20px;
}

.status-badge {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: 700;
}

.approved-icon .status-badge {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
}

.pending-icon .status-badge {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    color: white;
}

.rejected-icon .status-badge {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 10px;
    color: #333;
}

.modal-subtitle {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin: 8px 0;
    line-height: 1.5;
}

.modal-subtitle strong {
    color: #333;
    font-weight: 600;
}

.request-number-display {
    text-align: center;
    font-size: 14px;
    color: #ff8c42;
    font-weight: 700;
    margin-bottom: 30px;
}

.details-content {
    margin-top: 20px;
}

.details-message {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 20px;
    text-align: center;
}

.accepted-section,
.pending-section,
.rejected-section {
    margin-top: 20px;
}

.rejection-box {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.rejection-box h4 {
    color: #721c24;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 10px;
}

.rejection-box p {
    color: #721c24;
    font-size: 14px;
    margin: 0;
}

.appeal-actions {
    margin-top: 25px;
    text-align: center;
}

.appeal-btn {
    background: linear-gradient(135deg, #ff8c42, #e6763a);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.appeal-btn:hover {
    background: linear-gradient(135deg, #e6763a, #d4652a);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.appeal-btn:active {
    transform: translateY(0);
}

/* Payment Modal Styles */
.payment-modal {
    max-width: 500px;
    padding: 30px 40px;
}

.payment-options {
    display: flex;
    gap: 20px;
    margin: 30px 0;
    justify-content: center;
}

.payment-option-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 30px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 150px;
}

.payment-option-btn:hover {
    border-color: #ff8c42;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.2);
}

.payment-logo-placeholder {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-logo {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.payment-details {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}

.payment-details p {
    margin: 5px 0;
    font-size: 14px;
    color: #333;
}

/* QR Modal Styles */
.qr-modal {
    max-width: 550px;
    padding: 35px 40px;
}

.payment-success-banner {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 15px;
    text-align: center;
}

.payment-success-banner p {
    color: #155724;
    margin: 0;
    font-size: 14px;
}

.qr-preview {
    margin: 25px 0;
    padding: 0;
    background: transparent;
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.qr-image-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding: 25px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px solid #e9ecef;
}

.qr-image {
    max-width: 280px;
    width: 100%;
    height: auto;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.qr-placeholder {
    padding: 40px;
    color: #999;
    font-style: italic;
}

.upload-btn-payment {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    width: 100%;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

.upload-btn-payment:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.evidence-form {
    margin-top: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}

.reference-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.reference-input:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 0 0 3px rgba(255, 140, 66, 0.1);
}

.evidence-actions {
    display: flex;
    gap: 12px;
    width: 100%;
}

.submit-evidence-btn {
    flex: 1;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.submit-evidence-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.submit-evidence-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.clear-evidence-btn {
    background: white;
    color: #4a4a4a;
    border: 1px solid #e0e0e0;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    text-transform: uppercase;
    min-width: 100px;
}

.clear-evidence-btn:hover:not(:disabled) {
    background: #f8f9fa;
    border-color: #d0d0d0;
}

.clear-evidence-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Payment Status Section */
.payment-status-section {
    margin-top: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.section-title-payment {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.payment-info-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.payment-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 15px;
}

.payment-status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.payment-status-badge.approved {
    background: #d4edda;
    color: #155724;
}

.payment-status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
}

.badge-icon {
    font-size: 16px;
}

.badge-text {
    font-size: 14px;
}

.payment-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 15px;
}

.payment-detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.detail-label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
}

.detail-value {
    font-size: 14px;
    color: #333;
    font-weight: 700;
}

.receipt-view-btn {
    background: #ff8c42;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.receipt-view-btn:hover {
    background: #ff7a28;
}

.change-payment-method-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.change-payment-method-btn {
    width: 100%;
    background: transparent;
    border: 2px solid #ff8c42;
    color: #ff8c42;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.change-payment-method-btn:hover {
    background: #ff8c42;
    color: white;
}

.no-payment-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
}

.no-payment-card p {
    margin: 0;
    font-size: 14px;
    color: #495057;
}

/* Payment Buttons in Modal */
.payment-buttons-modal {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    justify-content: center;
}

.pay-online-btn-modal {
    flex: 1;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    text-transform: uppercase;
}

.pay-online-btn-modal:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.pay-onsite-btn-modal {
    flex: 1;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    text-transform: uppercase;
}

.pay-onsite-btn-modal:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

/* Pickup Info Styles */
.pickup-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 700;
    color: #666;
    font-size: 13px;
    min-width: 80px;
}

.info-value {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    text-align: right;
    flex: 1;
}

.info-value.amount {
    color: #239640;
    font-size: 18px;
    font-weight: 700;
}

/* Request Info Box */
.request-info-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
}

/* Feedback Section */
.feedback-section {
    margin-top: 25px;
    padding: 20px;
    background: #e7f3ff;
    border-radius: 12px;
    border-left: 4px solid #2196F3;
}

.section-title-feedback {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.feedback-box {
    background: white;
    padding: 15px;
    border-radius: 8px;
}

.feedback-box p {
    margin: 0;
    font-size: 14px;
    color: #333;
    line-height: 1.6;
}

.present-message {
    text-align: center;
    margin-top: 25px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    font-size: 14px;
    color: #495057;
    border: 1px solid #dee2e6;
}

.highlight-number {
    color: #ff8c42;
    font-weight: 700;
    font-size: 18px;
}

.note-message {
    font-size: 13px;
    color: #666;
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    line-height: 1.6;
}

.note-message.small {
    font-size: 12px;
    margin-top: 20px;
    padding: 12px 15px;
    text-align: center;
    background: #f0f7ff;
    border: 1px solid #d0e7ff;
    border-radius: 8px;
    color: #555;
}

/* Confirmation Modal */
.confirmation-modal {
    max-width: 500px;
    padding: 30px 40px;
    text-align: center;
}

.confirmation-icon {
    margin-bottom: 20px;
}

.success-icon .status-badge {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
}

.confirmation-content {
    margin: 20px 0;
}

.confirmation-message {
    font-size: 14px;
    color: #555;
    line-height: 1.8;
    margin-bottom: 20px;
}

.payment-summary {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.summary-value {
    font-weight: 700;
    color: #333;
    font-size: 14px;
}

.confirmation-note {
    font-size: 13px;
    color: #666;
    margin-top: 15px;
    line-height: 1.6;
}

.confirmation-btn {
    width: 100%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    margin-top: 20px;
}

.confirmation-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

/* Receipt Modal Styles */
.receipt-modal-container {
    background: white;
    border-radius: 15px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.receipt-modal-content {
    padding: 30px 40px;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f0f0f0;
    color: #333;
}

.receipt-title {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 25px;
    color: #ff8c42;
}

.receipt-details-box {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #e0e0e0;
}

.receipt-row:last-child {
    border-bottom: none;
}

.receipt-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.receipt-value {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.receipt-value.amount {
    color: #239640;
    font-size: 20px;
    font-family: 'Courier New', monospace;
}

.receipt-image-container {
    margin: 25px 0;
    text-align: center;
}

.receipt-image-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}

.receipt-image {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 15px;
    border-radius: 8px;
    margin: 25px 0;
}

.receipt-note p {
    margin: 0;
    font-size: 13px;
    color: #856404;
    line-height: 1.6;
}

.receipt-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.download-receipt-btn,
.print-receipt-btn,
.close-receipt-btn {
    flex: 1;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    border: none;
}

.download-receipt-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
}

.download-receipt-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.print-receipt-btn {
    background: linear-gradient(135deg, #2196F3, #1976D2);
    color: white;
}

.print-receipt-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
}

.close-receipt-btn {
    background: white;
    border: 1px solid #e0e0e0;
    color: #4a4a4a;
}

.close-receipt-btn:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
}

/* Resubmission Modal Styles */
.resubmission-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    padding: 20px;
}

.resubmission-modal-content {
    background: white;
    border-radius: 15px;
    max-width: 1200px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    overflow-x: visible;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    padding: 40px;
}

.resubmission-modal-header {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
}

.resubmission-modal-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    text-align: center;
}

.resubmission-document-type {
    font-size: 16px;
    color: #666;
    text-align: center;
    margin: 0;
}

.resubmission-document-type strong {
    color: #ff8c42;
    font-weight: 700;
}

.resubmission-form-container {
    margin-top: 30px;
    position: relative;
    overflow: visible;
}

.rejection-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.rejection-title {
    font-size: 18px;
    color: #856404;
    margin-bottom: 10px;
    font-weight: 600;
}

.rejection-text {
    font-size: 15px;
    color: #856404;
    line-height: 1.6;
    margin: 0;
}

.request-info-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #dee2e6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.info-value {
    color: #1a1a1a;
    font-size: 14px;
}

.status-pending {
    color: #ff8c42;
    font-weight: 600;
}

.form-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    padding: 25px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    position: relative;
    overflow: visible;
}

.dynamic-fields {
    background: white;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.required-star {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
}

.purpose-id-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.purpose-field,
.id-type-field {
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
}

/* Ensure select dropdowns are not clipped - native selects render above everything */
.purpose-field,
.id-type-field {
    overflow: visible;
    position: relative;
}

.purpose-field select,
.id-type-field select {
    position: relative;
}

/* Ensure form containers don't clip dropdowns */
.resubmission-form-container {
    overflow: visible;
}

.form-section {
    overflow: visible;
}

.purpose-id-row {
    overflow: visible;
    position: relative;
}

.id-type-upload-row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.id-type-select {
    flex: 1;
}

.upload-row {
    display: flex;
    gap: 8px;
}

.upload-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s;
}

.upload-btn:hover:not(.btn-disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.upload-btn.btn-disabled {
    background: #ccc !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.uploaded-files-display {
    margin-top: 10px;
}

.uploaded-file {
    font-size: 14px;
    color: #4caf50;
    font-weight: 500;
    margin: 5px 0;
}

.field-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
}

.form-input,
.form-textarea {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
    width: 100%;
    font-family: inherit;
}

.form-input:focus:not(.field-disabled),
.form-textarea:focus:not(.field-disabled) {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

.field-disabled {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
    opacity: 0.7;
    color: #666;
}

.dynamic-field-wrapper {
    margin-bottom: 20px;
}

.field-header {
    margin-bottom: 10px;
}

.field-description {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.file-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.file-upload-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.file-upload-header {
    margin-bottom: 10px;
}

.file-upload-label {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.file-upload-description {
    font-size: 12px;
    color: #666;
}

.file-upload-controls {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.upload-btn-dynamic {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s;
    width: fit-content;
}

.upload-btn-dynamic:hover:not(.btn-disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.upload-btn-dynamic.btn-disabled {
    background: #ccc !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.existing-file-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: white;
    border-radius: 6px;
    margin-bottom: 10px;
}

.existing-file-name {
    font-size: 13px;
    color: #666;
}

.view-file-link {
    color: #ff8c42;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
}

.view-file-link:hover {
    text-decoration: underline;
}

.uploaded-file-info-compact {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #e8f5e9;
    border-radius: 6px;
    font-size: 13px;
}

.file-checkmark {
    color: #4caf50;
}

.file-name-compact {
    flex: 1;
    color: #2e7d32;
    font-weight: 500;
}

.remove-file-btn-small {
    background: transparent;
    border: none;
    color: #e74c3c;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-file-btn-small:hover {
    background: #ffebee;
    border-radius: 4px;
}

.submit-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    display: block;
    margin: 30px auto 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%;
    max-width: 300px;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.editable-fields-notice {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 8px;
    padding: 12px 15px;
    margin-top: 15px;
    font-size: 13px;
    color: #1565c0;
}

.editable-fields-notice svg {
    color: #1976d2;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .resubmission-modal-content {
        padding: 20px;
        max-height: 95vh;
    }
    
    .purpose-id-row {
        grid-template-columns: 1fr;
    }
    
    .file-uploads-grid {
        grid-template-columns: 1fr;
    }
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
    color: #ff8c42;
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