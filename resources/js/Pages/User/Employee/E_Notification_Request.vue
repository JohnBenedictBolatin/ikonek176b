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
                        <button type="button" class="settings-item" @click="openTerms">Terms & Conditions</button>
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
                        class="nav-item"
                        :class="{ active: activeTab === 'documents' }"
                        @click="navigateToDocuments"
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
                        class="nav-item active"
                        :class="{ active: activeTab === 'notifications' }"
                        @click="navigateToNotifications"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        Notifications
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
                    FAQs & Help Center
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
                            <div class="filter-dropdown-wrapper" style="margin-left: 15px;">
                                <button class="filter-dropdown-btn" @click="toggleRequestTypeDropdown">
                                    {{ requestTypeFilter === 'all' ? 'ALL REQUESTS' : requestTypeFilter === 'document' ? 'DOCUMENT REQUESTS' : 'EVENT ASSISTANCE' }}
                                    <span class="filter-arrow" :class="{ rotated: showRequestTypeDropdown }">▼</span>
                                </button>
                                <div v-if="showRequestTypeDropdown" class="filter-dropdown-menu">
                                    <button @click="selectRequestType('all')" :class="{ active: requestTypeFilter === 'all' }">ALL REQUESTS</button>
                                    <button @click="selectRequestType('document')" :class="{ active: requestTypeFilter === 'document' }">DOCUMENT REQUESTS</button>
                                    <button @click="selectRequestType('event')" :class="{ active: requestTypeFilter === 'event' }">EVENT ASSISTANCE</button>
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
                                    
                                    <div class="request-status-wrapper">
                                        <div class="request-status" :class="request.status.toLowerCase()">
                                            {{ request.status }}
                                        </div>
                                        
                                        <!-- Add the payment badge RIGHT HERE, inside request-status-wrapper -->
                                        <div v-if="request.type === 'document' && request.status === 'APPROVED'" class="request-payment-row">
                                            <div
                                              v-if="getPaymentBadge(request)"
                                              :class="['payment-list-badge', getPaymentBadge(request).cls]"
                                              role="status"
                                              aria-live="polite"
                                            >
                                              <span class="badge-icon" aria-hidden="true">
                                                <template v-if="getPaymentBadge(request).cls === 'pending'">⏱</template>
                                                <template v-else-if="getPaymentBadge(request).cls === 'approved'">✓</template>
                                                <template v-else-if="getPaymentBadge(request).cls === 'rejected'">✕</template>
                                              </span>
                                              <span class="badge-text">{{ getPaymentBadge(request).label }}</span>
                                            </div>

                                            <div v-else class="payment-list-badge none">
                                              <span class="badge-icon"></span>
                                              <span class="badge-text">Not paid yet</span>
                                            </div>
                                        </div>
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
                    
                    <!-- APPROVED Status - Show pickup instructions -->
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

                        <!-- Payment Buttons for Document Requests -->
                        <!-- Only show if no payment exists OR payment was rejected (user can try again) -->
                        <div v-if="selectedRequest?.type === 'document' && 
                                  (!selectedRequest?.payment || 
                                    selectedRequest?.payment?.status?.toUpperCase() === 'REJECTED')" 
                            class="payment-buttons-modal">
                            <button class="pay-online-btn-modal" @click="showPaymentGateway(selectedRequest)">
                                Pay Online
                            </button>
                            <button class="pay-onsite-btn-modal" @click="acknowledgeOnsite">
                                Pay Onsite
                            </button>
                        </div>

                        <!-- NEW: Payment Status block -->
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
                                <div :class="['payment-status-badge', selectedRequest.payment.status?.toLowerCase()]">
                                    <span class="badge-icon">
                                        <template v-if="selectedRequest.payment.status?.toLowerCase() === 'pending'">⏱</template>
                                        <template v-else-if="selectedRequest.payment.status?.toLowerCase() === 'approved' || selectedRequest.payment.status?.toLowerCase() === 'paid'">✓</template>
                                        <template v-else-if="selectedRequest.payment.status?.toLowerCase() === 'rejected'">✕</template>
                                    </span>
                                    <span class="badge-text">{{ formatPaymentStatus(selectedRequest.payment.status) }}</span>
                                </div>

                                <div class="payment-details-grid">
                                    <div class="payment-detail-item">
                                        <span class="detail-label">Amount:</span>
                                        <span class="detail-value">₱{{ (selectedRequest.payment.amount ?? selectedRequest.amount) || '0.00' }}</span>
                                    </div>

                                    <div v-if="selectedRequest.payment.transaction_ref" class="payment-detail-item">
                                        <span class="detail-label">Transaction Ref:</span>
                                        <span class="detail-value">{{ selectedRequest.payment.transaction_ref }}</span>
                                    </div>

                                    <div v-if="selectedRequest.payment.paid_at" class="payment-detail-item">
                                        <span class="detail-label">Paid At:</span>
                                        <span class="detail-value">{{ formatDateTime(selectedRequest.payment.paid_at) }}</span>
                                    </div>

                                    <div v-if="selectedRequest.payment.receipt_path" class="payment-detail-item">
                                        <span class="detail-label">Receipt:</span>
                                        <span class="detail-value">
                                            <a :href="selectedRequest.payment.receipt_path" target="_blank" class="receipt-link">View Receipt</a>
                                        </span>
                                    </div>
                                </div>

                                <!-- Change Payment Method Button for Cash/Onsite payments -->
                                <div v-if="canChangePaymentMethod(selectedRequest)" class="change-payment-method-section">
                                    <button @click="showPaymentGateway(selectedRequest)" class="change-payment-method-btn">
                                        Change Payment Method
                                    </button>
                                </div>
                            </div>

                            <!-- If no payment: prompt to pay -->
                            <div v-else class="no-payment-card">
                                <p>No payment record found. You can <strong>Pay Online</strong> or <strong>Pay Onsite</strong> above.</p>
                            </div>
                        </div>
                        <!-- END payment-status-block -->

                        <!-- Admin Feedback/Comment Section for Approved Requests -->
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

                        <p class="present-message">
                            <strong>Present this request number upon pickup:</strong><br/>
                            <span class="highlight-number">#{{ selectedRequest?.requestNumber }}</span>
                        </p>

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
            <div v-if="paymentSuccess" class="payment-success-banner">
              <p><strong>Payment is successful ✅</strong></p>
            </div>

            <p class="modal-subtitle">Scan the QR code below to pay online</p>
             <p class="modal-subtitle">Click <strong>Upload Payment Screenshot</strong> to complete your payment.</p>

            <div class="qr-preview">
                <img v-if="selectedGateway === 'GCash'" src="/assets/GQR.png" alt="GCash QR" class="qr-image" />
                <img v-else-if="selectedGateway === 'Maya'" src="/assets/MQR.png" alt="Maya QR" class="qr-image" />
                <div v-else class="qr-placeholder">NO QR</div>
            </div>

            <div class="upload-section">
              <label class="upload-btn">
                  Upload Payment Screenshot
                  <input type="file" accept="image/*" @change="onFileChange" />
              </label>

              <div v-if="uploadedPreview" class="upload-preview">
                  <img :src="uploadedPreview" alt="Uploaded preview" class="uploaded-thumb" />
                  <div class="upload-filename">{{ uploadedFile?.name }}</div>
              </div>

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

            <p class="note-message small">After submission, your payment will be reviewed by the Barangay Treasurer.</p>
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
                    Got it
                </button>
            </div>
        </div>
    </div>

    <!-- Terms & Conditions Modal -->
    <TermsModal :open="showTerms" @close="closeTerms" />
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue'
import { router } from '@inertiajs/vue3'
import TermsModal from '@/Components/TermsModal.vue'

// --- Inertia-shared auth user ---
const page = usePage()

// Helper to get props (supports both Inertia v1 and v0.8+)
const getPageProp = (key) => {
  const props = page?.props?.value ?? page?.props ?? {}
  return props[key] ?? null
}

// role map
const roleMap = {
  1: 'Resident', 2: 'Barangay Captain', 3: 'Barangay Secretary',
  4: 'Barangay Treasurer', 5: 'Barangay Kagawad',
  6: 'Sangguniang Kabataan Chairman', 7: 'Sangguniang Kabataan Kagawad',
  9: 'System Admin'
}

const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  return id ? (roleMap[id] ?? `Role ${id}`) : 'Employee'
})

// Profile picture URL
const profilePictureUrl = computed(() => {
    if (user.value?.profile_pic) {
        const pic = user.value.profile_pic
        // If it's a full URL, return as is
        if (pic.startsWith('http')) {
            return pic
        }
        // If it already has /storage/, return as is
        if (pic.startsWith('/storage/')) {
            return pic
        }
        // Otherwise prepend storage path
        return `/storage/${pic}`
    }
    return '/assets/DEFAULT.jpg'
})

const showSettings = ref(false)
const showModeDropdown = ref(false)
const showSortDropdown = ref(false)
const showRequestTypeDropdown = ref(false)
const showStatusDropdown = ref(false)
const activeTab = ref('notifications')
const currentTab = ref('requests')
const sortOption = ref('newest')
const requestTypeFilter = ref('all') // 'all', 'document', 'event'
const statusFilter = ref('all') // 'all', 'pending', 'approved', 'rejected'
const searchQuery = ref('')
const showPaymentModal = ref(false)
const showDetailsModal = ref(false)
const selectedRequest = ref(null)
// QR modal state
const showQRModal = ref(false)
const selectedGateway = ref(null) // 'GCash' or 'Maya'
const scanning = ref(false)

const uploadedFile = ref(null)
const uploadedPreview = ref(null)
const referenceId = ref('')
const uploading = ref(false)
const paymentSuccess = ref(false)
const paymentError = ref('')
const showPaymentConfirmation = ref(false)
const isOnsitePayment = ref(false)

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

// Get raw data from server (supports both key naming conventions)
const documentRequestsRaw = computed(() => getPageProp('documentRequests') ?? getPageProp('document_requests') ?? [])
const eventRequestsRaw = computed(() => getPageProp('eventAssistanceRequests') ?? getPageProp('event_assistance_requests') ?? [])

// --- NEW: payments passed from controller
const paymentsRaw = computed(() => getPageProp('payments') ?? getPageProp('payment') ?? [])

// build a map fk_doc_request_id -> latest payment object
const paymentsMap = computed(() => {
  const map = {}
  const items = Array.isArray(paymentsRaw.value) ? paymentsRaw.value : []
  for (const p of items) {
    // Normalize keys
    const fk = p.fk_doc_request_id ?? p.fkDocRequestId ?? p.doc_request_id ?? p.fk_doc_request ?? null
    if (!fk) continue

    // only set if not already present (controller sorts desc so first is latest)
    if (!map[fk]) {
      const status = (p.status ?? p.payment_status ?? 'PENDING').toString().toUpperCase()
      map[fk] = {
        payment_id: p.payment_id ?? p.id ?? null,
        fk_doc_request_id: fk,
        status,
        amount: p.amount ?? p.payment_amount ?? null,
        transaction_ref: p.transaction_ref ?? p.reference_id ?? p.reference_ticket ?? null,
        receipt_path: p.receipt_path ?? p.receipt_url ?? null,
        paid_at: p.paid_at ?? p.created_at ?? p.payment_date ?? null,
        raw: p,
      }
    }
  }
  return map
})

// Map document requests into normalized shape
const mappedDocumentRequests = computed(() => {
  const server = documentRequestsRaw.value || []
  if (!Array.isArray(server) || server.length === 0) return []

  return server.map((r) => {
    const raw = r.raw ?? r
    const createdAt = r.created_at ?? raw?.created_at
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
        const dateFormatter = new Intl.DateTimeFormat('en-US', {
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          timeZone: 'Asia/Manila'
        })
        const timeFormatter = new Intl.DateTimeFormat('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          timeZone: 'Asia/Manila',
          hour12: true
        })
        dateStr = dateFormatter.format(timestamp)
        timeStr = timeFormatter.format(timestamp)
      } catch (e) {
        dateStr = timestamp.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
        timeStr = timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
      }
    }

    const requestNumber = r.doc_request_ticket ?? raw?.doc_request_ticket ?? String(r.doc_request_id ?? raw?.doc_request_id ?? '')
    const docType = r.documentType ?? raw?.documentType
    const title = docType?.document_name ?? raw?.document_name ?? r.purpose ?? raw?.purpose ?? 'Document Request'
    const status = (r.status ?? raw?.status ?? 'PENDING').toString().toUpperCase()
    const type = 'document'

    const amountVal = (
      (r.amount !== undefined && r.amount !== null) ? Number(r.amount) :
      (r.processing_fee !== undefined && r.processing_fee !== null) ? Number(r.processing_fee) :
      (r.applied_processing_fee !== undefined && r.applied_processing_fee !== null) ? Number(r.applied_processing_fee) :
      (docType?.processing_fee !== undefined && docType?.processing_fee !== null) ? Number(docType.processing_fee) :
      (raw?.processing_fee !== undefined && raw?.processing_fee !== null) ? Number(raw.processing_fee) :
      null
    )
    const amount = amountVal !== null ? Number(amountVal).toFixed(2) : null

    // find payment if any (use id or doc_request_id as normalized key)
    const fk = r.doc_request_id ?? raw?.doc_request_id ?? requestNumber
    const payment = paymentsMap.value[fk] ?? null

    return {
      id: r.doc_request_id ?? raw?.doc_request_id ?? requestNumber,
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
      pickup_item: r.pickup_item ?? raw?.pickup_item ?? null,
      person_to_look: r.person_to_look ?? raw?.person_to_look ?? null,
      admin_feedback: r.admin_feedback ?? raw?.admin_feedback ?? null,
      raw,
      payment, // attached payment object or null
    }
  })
})

// Map event assistance requests into normalized shape
const mappedEventRequests = computed(() => {
  const server = eventRequestsRaw.value || []
  if (!Array.isArray(server) || server.length === 0) return []

  return server.map((r) => {
    const raw = r.raw ?? r
    const dt = raw?.start_datetime ?? raw?.created_at
    let timestamp = null
    if (dt) {
      try { timestamp = new Date(dt) } catch (e) { timestamp = null }
    } else if (raw?.created_at) {
      try { timestamp = new Date(raw.created_at) } catch (e) { timestamp = null }
    }

    let dateStr = ''
    let timeStr = ''
    if (timestamp && !isNaN(timestamp.getTime())) {
      try {
        const dateFormatter = new Intl.DateTimeFormat('en-US', {
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          timeZone: 'Asia/Manila'
        })
        const timeFormatter = new Intl.DateTimeFormat('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          timeZone: 'Asia/Manila',
          hour12: true
        })
        dateStr = dateFormatter.format(timestamp)
        timeStr = timeFormatter.format(timestamp)
      } catch (e) {
        dateStr = timestamp.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
        timeStr = timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
      }
    }

    const requestNumber = raw?.eventassist_request_ticket ?? raw?.event_assist_request_ticket ?? String(raw?.event_assist_request_id ?? raw?.id ?? '')
    // Use title from server (which uses event_type), fallback to event_type, then purpose
    const title = raw?.title ?? raw?.event_type ?? (raw?.purpose ? String(raw.purpose) : (raw?.event_location ? String(raw.event_location) : 'Event Assistance'))
    const status = (raw?.status ?? 'PENDING').toString().toUpperCase()
    const type = 'event'

    return {
      id: raw?.event_assist_request_id ?? requestNumber,
      requestNumber,
      title,
      date: dateStr,
      time: timeStr,
      status,
      type,
      amount: null,
      timestamp: timestamp || new Date(0),
      admin_feedback: raw?.admin_feedback ?? null,
      raw,
    }
  })
})

// Combine documents + events into a single requests array for the UI
const combinedRequests = computed(() => {
  const combined = [
    ...(mappedDocumentRequests.value || []),
    ...(mappedEventRequests.value || [])
  ]

  combined.forEach(c => {
    if (!(c.timestamp instanceof Date)) {
      try { c.timestamp = new Date(c.timestamp) } catch (e) { c.timestamp = new Date(0) }
    }
  })

  combined.sort((a,b) => b.timestamp - a.timestamp)
  return combined
})

// Alias for backward compatibility
const requests = combinedRequests

// Filtered Activities
const filteredActivities = computed(() => {
    let filtered = [...activities.value]

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(activity => 
            activity.user.toLowerCase().includes(query) ||
            activity.action.toLowerCase().includes(query)
        )
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.timestamp - a.timestamp)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.timestamp - b.timestamp)
    }

    return filtered
})

// Filtered Requests
const filteredRequests = computed(() => {
  let filtered = [...combinedRequests.value]

  // Filter by request type
  if (requestTypeFilter.value !== 'all') {
    filtered = filtered.filter(req => req.type === requestTypeFilter.value)
  }

  // Filter by status
  if (statusFilter.value !== 'all') {
    filtered = filtered.filter(req => {
      const reqStatus = (req.status || '').toLowerCase()
      return reqStatus === statusFilter.value.toLowerCase()
    })
  }

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

const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

// Terms & Conditions modal
const showTerms = ref(false)
const openTerms = () => {
    showSettings.value = false
    showTerms.value = true
}
const closeTerms = () => {
    showTerms.value = false
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

const toggleRequestTypeDropdown = () => {
    showRequestTypeDropdown.value = !showRequestTypeDropdown.value
}

const selectRequestType = (type) => {
    requestTypeFilter.value = type
    showRequestTypeDropdown.value = false
}

const toggleStatusDropdown = () => {
    showStatusDropdown.value = !showStatusDropdown.value
}

const selectStatus = (status) => {
    statusFilter.value = status
    showStatusDropdown.value = false
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
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

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_employee'))
}
const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_employee'))
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_employee'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_employee'))
}

const navigateToNotifications = () => { activeTab.value = 'notifications' }

const switchTab = (tab) => {
    currentTab.value = tab
    showModeDropdown.value = false
}

const openFAQ = () => {
    router.visit(route('help_center_employee'))
}

const viewRequestDetails = (request) => {
    selectedRequest.value = request
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedRequest.value = null
}

// NEW: payment methods from server props (optional)
const paymentMethodsRaw = computed(() => getPageProp('paymentMethods') ?? getPageProp('payment_methods') ?? [])
const paymentMethodsMap = computed(() => {
  const map = {}
  for (const m of (Array.isArray(paymentMethodsRaw.value) ? paymentMethodsRaw.value : [])) {
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

/**
 * File upload helpers and payment flows
 */
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

  if (uploadedPreview.value) {
    try { URL.revokeObjectURL(uploadedPreview.value) } catch (e) {}
    uploadedPreview.value = null
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
}

/**
 * submitEvidence()
 * - sends FormData to backend payments.upload_evidence route (adjust route name as needed)
 */
const submitEvidence = async () => {
  if (!selectedRequest.value) {
    alert('No selected request.');
    return;
  }

  // require either a file or a reference ID
  if (!uploadedFile.value && (!referenceId.value || referenceId.value.trim() === '')) {
    alert('Please upload a screenshot or enter a transaction reference ID before submitting.');
    return;
  }

  uploading.value = true;
  paymentError.value = '';
  paymentSuccess.value = false;

  try {
    const formData = new FormData();
    formData.append('fk_doc_request_id', Number(selectedRequest.value.id || selectedRequest.value.requestNumber));
    formData.append('fk_user_id', Number(user.value?.user_id ?? user.value?.id ?? ''));
    // send keys the controller expects
    formData.append('request_reference_ticket', selectedRequest.value.requestNumber ?? '');
    formData.append('paid_amount', String(Number(selectedRequest.value.amount ?? 0)));
    if (selectedGateway.value) formData.append('gateway', selectedGateway.value);
    if (referenceId.value && referenceId.value.trim() !== '') {
      formData.append('transaction_ref', referenceId.value.trim());
    }
    if (uploadedFile.value) {
      formData.append('evidence', uploadedFile.value, uploadedFile.value.name);
    }

    // Store referenceId temporarily before clearing
    const tempReferenceId = referenceId.value;
    
    try {
      const response = await window.axios.post(route('payments.store'), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'X-Requested-With': 'XMLHttpRequest',
        },
        withCredentials: true,
      });

      if (response.data && response.status === 201) {
        paymentSuccess.value = true;
        // Preserve referenceId for confirmation modal
        referenceId.value = tempReferenceId;
        // Mark as online payment (not onsite)
        isOnsitePayment.value = false;
        // Show confirmation modal instead of alert
        showPaymentConfirmation.value = true;
        // Close QR modal after a short delay
        setTimeout(() => {
          showQRModal.value = false;
          selectedGateway.value = null;
          // Don't clear evidence yet - let user see confirmation first
        }, 500);
        // Refresh payment data
        router.reload({ only: ['documentRequests', 'payments'] });
      }
    } catch (axiosError) {
      console.error('Upload errors:', axiosError);
      if (axiosError.response && axiosError.response.data) {
        paymentError.value = JSON.stringify(axiosError.response.data);
        alert('Failed to submit payment evidence: ' + (axiosError.response.data.message || 'Please try again.'));
      } else {
        alert('Failed to submit payment evidence. Please try again.');
      }
    }
  } catch (err) {
    console.error('Unexpected error in submitEvidence:', err);
    alert('An unexpected error occurred while submitting evidence.');
  } finally {
    uploading.value = false;
  }
};

const acknowledgeOnsite = async () => {
  if (!selectedRequest.value) {
    alert('No request selected.')
    return
  }

  const payload = {
    fk_doc_request_id: Number(selectedRequest.value.id ?? selectedRequest.value.requestNumber ?? 0),
    fk_user_id: Number(user.value?.user_id ?? user.value?.id ?? 0),
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
      // Mark as onsite payment for different confirmation message
      isOnsitePayment.value = true
      // Show confirmation modal
      showPaymentConfirmation.value = true
      // Close details modal
      showDetailsModal.value = false
      // Refresh payment data
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

const closePaymentConfirmation = () => {
  showPaymentConfirmation.value = false
  isOnsitePayment.value = false
  // Clear evidence after confirmation is closed
  clearEvidence()
  // Refresh the page data to show updated payment status
  router.reload({ only: ['documentRequests', 'payments'] })
}

const getRejectionReason = (request) => {
    // First, try to get admin_feedback from the request
    const adminFeedback = request?.admin_feedback ?? request?.raw?.admin_feedback ?? null
    
    if (adminFeedback && adminFeedback.trim() !== '') {
        return adminFeedback.trim()
    }
    
    // Fallback to default message if no feedback is available
    return 'The submitted information does not meet the requirements. Please contact the barangay office for more details.'
}

const parseDate = (val) => {
  if (!val) return null;
  if (val instanceof Date && !isNaN(val)) return val;
  try {
    const d = new Date(val);
    if (!isNaN(d)) return d;
  } catch (e) {}
  return null;
};

const getPickupSchedule = (request = null) => {
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
      const sameDay = startDate.toDateString() === endDate.toDateString();
      const endTimeStr = endDate.toLocaleTimeString('en-US', timeOptions);
      if (sameDay) {
        return `${dateStr}, ${startTimeStr} - ${endTimeStr}`;
      } else {
        const endDateStr = endDate.toLocaleDateString('en-US', dateOptions).toUpperCase();
        return `${dateStr}, ${startTimeStr} - ${endDateStr}, ${endTimeStr}`;
      }
    } else {
      const hasTime = !(startTimeStr === '12:00 AM' && startDate.getHours() === 0 && startDate.getMinutes() === 0);
      if (hasTime) {
        return `${dateStr}, ${startTimeStr}`;
      }
      return `${dateStr}, 9:00 AM - 3:00 PM`;
    }
  }

  const today = new Date();
  const pickupDate = new Date(today);
  pickupDate.setDate(today.getDate() + 3);

  const defaultDateStr = pickupDate.toLocaleDateString('en-US', dateOptions).toUpperCase();
  return `${defaultDateStr}, 9:00 AM - 3:00 PM`;
};

// Payment badge helper: call as getPaymentBadge(request)
const getPaymentBadge = (request) => {
  // prefer normalized attached payment
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

// UI Helper: format payment status display string
const formatPaymentStatus = (s) => {
  if (!s) return 'UNKNOWN'
  const v = s.toString().toUpperCase()
  if (v === 'PENDING') return 'Payment Pending'
  if (v === 'APPROVED' || v === 'PAID' || v === 'SUCCESS') return 'Payment Approved'
  if (v === 'REJECTED' || v === 'FAILED') return 'Payment Rejected'
  return v
}

const formatDateTime = (val) => {
  if (!val) return ''
  try {
    const d = new Date(val)
    return d.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: '2-digit' })
  } catch (e) { return String(val) }
}

// Check if payment method can be changed (cash/onsite with pending status)
const canChangePaymentMethod = (request) => {
  if (!request?.payment) return false
  
  const paymentStatus = (request.payment.status ?? '').toString().toUpperCase()
  // Only allow change if status is pending
  if (paymentStatus !== 'PENDING') return false
  
  // Check payment method from various possible locations
  const paymentMethodRaw = request.payment.raw?.paymentMethod?.pay_method_name ?? 
                          request.payment.raw?.payment_method_name ?? 
                          request.payment.raw?.paymentMethod ?? 
                          request.payment.paymentMethod ?? 
                          request.payment.raw?.pay_method_name ??
                          request.payment.payment_method_name ??
                          ''
  
  const methodUpper = paymentMethodRaw.toString().toUpperCase().trim()
  
  // Check if it's cash or onsite
  const isCashOrOnsite = methodUpper.includes('CASH') || 
                         methodUpper.includes('ONSITE') || 
                         methodUpper.includes('ON-SITE') || 
                         methodUpper.includes('ON SITE')
  
  // Also check if transaction_ref is null/empty (often indicates onsite/cash payments)
  // But only if we haven't already determined it's cash/onsite
  if (!isCashOrOnsite && (!request.payment.transaction_ref || request.payment.transaction_ref === '' || request.payment.transaction_ref === null)) {
    // If there's no transaction ref and status is pending, it's likely onsite/cash
    return true
  }
  
  return isCashOrOnsite
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.notification-title')) {
        showModeDropdown.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showRequestTypeDropdown.value = false
        showStatusDropdown.value = false
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
    background: #2e2e2e;
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
    background: #2e2e2e;
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
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

.faq-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
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
.request-card.accepted {
border-left-color: #2bb24a;
background: linear-gradient(90deg, rgba(43, 178, 74, 0.05), transparent);
}
.request-card.pending {
border-left-color: #ff9800;
background: linear-gradient(90deg, rgba(255, 152, 0, 0.05), transparent);
}
.request-card.rejected {
border-left-color: #dc3545;
background: linear-gradient(90deg, rgba(220, 53, 69, 0.05), transparent);
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
.request-status.pending {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}
.request-status.rejected {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}
.request-status.approved {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

/* Payment Badge Styles */
.request-status-wrapper {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 12px;
}

.request-payment-row {
  display: flex;
  justify-content: flex-start;
  order: -1;
}

.payment-list-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.92rem;
}

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

.payment-list-badge.none {
  background: transparent;
  color: #6c757d;
  border: 1px dashed rgba(0,0,0,0.06);
}

.payment-list-badge .badge-icon {
  font-size: 1.08rem;
  line-height: 1;
}

.payment-list-badge .badge-text {
  font-weight: 600;
  font-size: 0.9rem;
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
.accepted-icon .status-badge {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
box-shadow: 0 6px 20px rgba(43, 178, 74, 0.4);
}
.pending-icon .status-badge {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
}
.rejected-icon .status-badge {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
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
  width: 540px;
  padding: 12px 15px;
  background: #fffbf0;
  border-left: 4px solid #ff9800;
  border-radius: 5px;
  margin: 20px auto;
  text-align: left;
}

.note-message.small {
  max-width: 400px;
  width: auto;
  margin: 20px auto;
  text-align: center;
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
padding: 12px 20px;
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
box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}
.pay-online-btn-modal:hover {
transform: translateY(-2px);
box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}
.pay-onsite-btn-modal {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}
.pay-onsite-btn-modal:hover {
transform: translateY(-2px);
box-shadow: 0 6px 18px rgba(43, 178, 74, 0.4);
}

/* Payment Status Section */
.payment-status-section {
    margin-top: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
}

.section-title-payment {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title-payment .icon {
    font-size: 20px;
}

/* Feedback Section */
.feedback-section {
    margin-top: 20px;
    margin-bottom: 20px;
}

.section-title-feedback {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title-feedback .icon {
    font-size: 20px;
}

.feedback-box {
    background: #f0f7ff;
    border: 2px solid #4a90e2;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.feedback-box p {
    color: #333;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.payment-info-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.payment-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 15px;
}

.payment-status-badge.pending {
    background: linear-gradient(135deg, #fff3cd, #ffeeba);
    color: #856404;
    border: 1px solid #ffc107;
}

.payment-status-badge.approved,
.payment-status-badge.paid {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border: 1px solid #28a745;
}

.payment-status-badge.rejected {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border: 1px solid #dc3545;
}

/* Change Payment Method Section */
.change-payment-method-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(0,0,0,0.1);
}

.change-payment-method-btn {
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.change-payment-method-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
    background: linear-gradient(135deg, #ff7a28, #ff6a18);
}

.payment-details-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.payment-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.payment-detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.detail-value {
    color: #333;
    font-size: 14px;
    text-align: right;
}

.receipt-link {
    color: #ff8c42;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.receipt-link:hover {
    color: #ff7a28;
    text-decoration: underline;
}

.no-payment-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    text-align: center;
    color: #666;
    font-size: 14px;
}
.close-btn {
width: 100%;
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
margin-bottom: 30px;
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

/* QR Modal Styles */
.qr-modal {
    padding: 40px 30px;
    max-width: 500px;
}

.qr-preview {
    margin: 20px auto;
    width: 250px;
    height: 250px;
    background: #f8f9fa;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ddd;
}

.qr-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 10px;
}

.qr-placeholder {
    color: #999;
    font-size: 14px;
}

.upload-section {
    margin: 16px 0;
    text-align: center;
}

.upload-btn {
    display: inline-block;
    padding: 10px 30px;
    font-weight: bold;
    color: white;
    font-size: 15px;
    background: #ff8c42;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
    max-width: 300px;
    width: auto;
}

.upload-btn:hover {
    background: #ff7a28;
    transform: translateY(-2px);
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
    justify-content: center;
    text-align: center;
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
    max-width: 350px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.input-and-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.reference-input {
    height: 45px;
    width: 300px;
    padding: 0 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-sizing: border-box;
    font-size: 14px;
}

.evidence-actions {
    display: flex;
    gap: 5px;
}

.submit-evidence-btn,
.clear-evidence-btn {
    height: 45px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    box-sizing: border-box;
    transition: all 0.2s;
}

.submit-evidence-btn {
    background: #28a745;
    color: white;
}

.submit-evidence-btn:hover {
    background: #218838;
}

.submit-evidence-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.clear-evidence-btn {
    background: #e0e0e0;
    color: #333;
}

.clear-evidence-btn:hover {
    background: #d0d0d0;
}

.clear-evidence-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.payment-success-banner {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
    color: #155724;
}

.payment-success-banner p {
    margin: 0;
    font-weight: 600;
}

.note-message.small {
    font-size: 11px;
    margin-top: 15px;
    max-width: 400px;
    width: auto;
    margin: 20px auto;
    text-align: center;
}

/* QR Modal Styles */
.qr-modal {
    padding: 40px 30px;
    max-width: 500px;
}

.qr-preview {
    margin: 20px auto;
    width: 250px;
    height: 250px;
    background: #f8f9fa;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ddd;
}

.qr-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 10px;
}

.qr-placeholder {
    color: #999;
    font-size: 14px;
}

.upload-section {
    margin: 16px 0;
    text-align: center;
}

.upload-btn {
    display: inline-block;
    padding: 10px 30px;
    font-weight: bold;
    color: white;
    font-size: 15px;
    background: #ff8c42;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
    max-width: 300px;
    width: auto;
}

.upload-btn:hover {
    background: #ff7a28;
    transform: translateY(-2px);
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
    justify-content: center;
    text-align: center;
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
    max-width: 350px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.input-and-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.reference-input {
    height: 45px;
    width: 300px;
    padding: 0 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-sizing: border-box;
    font-size: 14px;
}

.evidence-actions {
    display: flex;
    gap: 5px;
}

.submit-evidence-btn,
.clear-evidence-btn {
    height: 45px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    box-sizing: border-box;
    transition: all 0.2s;
}

.submit-evidence-btn {
    background: #28a745;
    color: white;
}

.submit-evidence-btn:hover {
    background: #218838;
}

.submit-evidence-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.clear-evidence-btn {
    background: #e0e0e0;
    color: #333;
}

.clear-evidence-btn:hover {
    background: #d0d0d0;
}

.clear-evidence-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.payment-success-banner {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
    color: #155724;
}

.payment-success-banner p {
    margin: 0;
    font-weight: 600;
}

.note-message.small {
    font-size: 11px;
    margin-top: 15px;
    max-width: 400px;
    width: auto;
    margin: 20px auto;
    text-align: center;
}

/* Payment Confirmation Modal Styles */
.confirmation-modal {
    padding: 40px 30px;
    max-width: 550px;
    text-align: center;
}

.confirmation-icon {
    margin-bottom: 20px;
}

.confirmation-icon.success-icon .status-badge {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: 700;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.3);
}

.confirmation-content {
    text-align: left;
    margin: 25px 0;
}

.confirmation-message {
    font-size: 15px;
    color: #555;
    line-height: 1.6;
    margin-bottom: 25px;
    text-align: center;
}

.confirmation-message strong {
    color: #2bb24a;
    font-weight: 700;
}

.payment-summary {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
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
    color: #333;
    font-size: 14px;
    font-weight: 600;
    text-align: right;
}

.confirmation-note {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
    text-align: center;
    padding: 15px;
    background: #fffbf0;
    border-left: 4px solid #ff9800;
    border-radius: 5px;
    margin: 20px 0;
}

.confirmation-btn {
    width: 100%;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}

.confirmation-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(43, 178, 74, 0.4);
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
background: #888;
border-radius: 3px;
}
.notifications-container::-webkit-scrollbar-thumb:hover {
background: #666;
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