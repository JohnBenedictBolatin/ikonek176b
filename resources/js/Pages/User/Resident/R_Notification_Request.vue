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
                    <button type="button" class="settings-burger-btn" @click="toggleSettings" aria-label="Settings">
                    <svg class="settings-burger-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                    <div v-if="showSettings" class="settings-dropdown">
                        <a href="#" class="settings-item" @click.prevent.stop="openTermsModal">TERMS & CONDITIONS</a>
                        <Link href="#" class="settings-item" @click="logout">SIGN OUT</Link>
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
                        @click.prevent="navigateToPosts"
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
                    FAQS & HELP CENTER
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
                <template v-if="getPaymentBadge(request).cls === 'pending'">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; display: inline-block; vertical-align: middle; margin: 0;">
                    <circle cx="12" cy="12" r="10"/>
                    <path stroke-linecap="round" d="M12 6v6l4 2"/>
                  </svg>
                </template>
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
</div></div></div>
                            <div v-if="filteredRequests.length === 0" class="no-notifications">
                                <p>No requests found</p>
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
                        <span v-if="selectedRequest?.status === 'PENDING'">
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
                    
                    <!-- ACCEPTED Status - Show pickup instructions -->
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
                            <div v-if="paymentError" class="error-message" style="color: #dc3545; font-size: 14px; margin-bottom: 12px; padding: 10px; background-color: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb; width: 100%;">
                                {{ paymentError }}
                            </div>
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
                                        <template v-if="selectedRequest.payment.status?.toLowerCase() === 'pending'">
                                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; display: inline-block; vertical-align: middle; margin: 0;">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path stroke-linecap="round" d="M12 6v6l4 2"/>
                                          </svg>
                                        </template>
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

                                    <div v-if="selectedRequest.payment.paid_at && (selectedRequest.payment.status?.toLowerCase() === 'approved' || selectedRequest.payment.status?.toLowerCase() === 'paid')" class="payment-detail-item">
                                        <span class="detail-label">Paid At:</span>
                                        <span class="detail-value">{{ formatDateTime(selectedRequest.payment.paid_at) }}</span>
                                    </div>

                                    <div v-if="selectedRequest.payment.receipt_path || selectedRequest.payment.receipt_image" class="payment-detail-item">
                                        <span class="detail-label">Receipt:</span>
                                        <span class="detail-value">
                                            <button @click="viewReceipt(selectedRequest)" class="receipt-view-btn">View/Download Receipt</button>
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

              <div v-if="uploadedFile" class="uploaded-file-info-compact" style="margin-top: 10px;">
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
                  <div v-if="paymentError" class="error-message" style="color: #dc3545; font-size: 14px; margin-bottom: 12px; padding: 10px; background-color: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb;">
                      {{ paymentError }}
                  </div>
                  <div class="input-and-actions">
                      <input
                          type="text"
                          v-model="referenceId"
                          @input="paymentError = ''"
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
                    Got it
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue'
import { router } from '@inertiajs/vue3'

// --- Inertia-shared auth user ---
const page = usePage()

// helper to read props in both shapes: page.props.value?.X or page.props.X
const getPageProp = (key) => {
  try {
    if (page?.props?.value && Object.prototype.hasOwnProperty.call(page.props.value, key)) {
      return page.props.value[key]
    }
    if (page?.props && Object.prototype.hasOwnProperty.call(page.props, key)) {
      return page.props[key]
    }
  } catch (e) {
    // fallback silent
  }
  return undefined
}

const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

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

// raw documentRequests from the server (supports both keys)
const documentRequestsRaw = computed(() => getPageProp('documentRequests') ?? getPageProp('document_requests') ?? [])

// event requests raw
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
        receipt_image: p.receipt_image ?? p.receipt_path ?? p.receipt_url ?? null,
        paid_at: p.paid_at ?? null, // Only use paid_at if actually set (not created_at fallback)
        raw: p,
      }
    }
  }
  return map
})

const selectedPaymentStatusNormalized = computed(() => {
  // read possible status fields from attached payment object
  const rawStatus =
    selectedRequest.value?.payment?.status ??
    selectedRequest.value?.payment?.raw?.status ??
    selectedRequest.value?.payment?.raw?.payment_status ??
    selectedRequest.value?.payment?.raw?.status_text ??
    null

  if (!rawStatus) return null

  const s = String(rawStatus).toUpperCase().trim()

  // Map common variants to three buckets: 'pending', 'approved', 'rejected'
  if (['PENDING', 'PROCESSING', 'AWAITING', 'ONHOLD', 'IN_REVIEW', 'IN-REVIEW'].includes(s)) return 'pending'
  if (['APPROVED', 'PAID', 'SUCCESS', 'COMPLETED', 'CONFIRMED'].includes(s)) return 'approved'
  if (['REJECTED', 'FAILED', 'CANCELLED', 'DECLINED'].includes(s)) return 'rejected'

  // fallback: if the request itself is approved but there's no explicit payment success, show pending
  if (selectedRequest.value?.status === 'APPROVED') {
    // if there's any payment record but status is unknown, treat as pending
    return selectedRequest.value?.payment ? 'pending' : null
  }

  return null
})

// Payment badge helper: call as getPaymentBadge(request)
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


// Map documents into normalized shape and attach payment if exists
const mappedDocumentRequests = computed(() => {
  const server = documentRequestsRaw.value || []
  if (server.length === 0) return []

  return (Array.isArray(server) ? server : []).map((r) => {
    // Get created_at directly from r (controller sends it directly, not nested)
    // This matches how event assistance works - it checks r.created_at first
    const createdAt = r.created_at
    let timestamp = null
    
    // Parse timestamp - use exact same pattern as event assistance
    if (createdAt) {
      try { 
        timestamp = new Date(createdAt)
        if (isNaN(timestamp.getTime())) timestamp = null
      } catch (e) { 
        timestamp = null 
      }
    }

    // Format date and time using Intl.DateTimeFormat to properly handle timezone
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
        console.error('Date formatting error:', e, 'timestamp:', timestamp, 'createdAt:', createdAt)
        // Fallback to local timezone if Intl fails
        dateStr = timestamp.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
        timeStr = timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
      }
    } else {
      dateStr = r.date ?? ''
      timeStr = r.time ?? ''
    }

    const raw = r.raw ?? r
    const requestNumber = r.requestNumber ?? r.doc_request_ticket ?? raw?.doc_request_ticket ?? String(r.id ?? raw?.doc_request_id ?? '')
    const title = r.title ?? raw?.document_name ?? raw?.purpose ?? 'Request'
    const status = (r.status ?? raw?.status ?? 'PENDING').toString().toUpperCase()
    const type = r.type ?? (raw?.fk_document_type_id ? 'document' : (raw?.pickup_item ? 'document' : 'event'))

    const amountVal = (
      (r.amount !== undefined && r.amount !== null) ? Number(r.amount) :
      (r.processing_fee !== undefined && r.processing_fee !== null) ? Number(r.processing_fee) :
      (r.applied_processing_fee !== undefined && r.applied_processing_fee !== null) ? Number(r.applied_processing_fee) :
      (raw?.processing_fee !== undefined && raw?.processing_fee !== null) ? Number(raw.processing_fee) :
      (raw?.documentType?.processing_fee !== undefined && raw?.documentType?.processing_fee !== null) ? Number(raw.documentType.processing_fee) :
      null
    )
    const amount = amountVal !== null ? Number(amountVal).toFixed(2) : null

    // find payment if any (use id or doc_request_id as normalized key)
    const fk = r.id ?? raw?.doc_request_id ?? requestNumber
    const payment = paymentsMap.value[fk] ?? null

    return {
      id: r.id ?? raw?.doc_request_id ?? requestNumber,
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

// Map event assistance requests into the same normalized shape
const mappedEventRequests = computed(() => {
  const server = eventRequestsRaw.value || []
  if (!Array.isArray(server) || server.length === 0) return []

  return server.map((r) => {
    const raw = r.raw ?? r
    const dt = raw?.start_datetime ?? raw?.created_at ?? raw?.createdAt ?? raw?.created_at
    let timestamp = null
    if (dt) {
      try { timestamp = new Date(dt) } catch (e) { timestamp = null }
    } else if (raw?.created_at) {
      try { timestamp = new Date(raw.created_at) } catch (e) { timestamp = null }
    }

    // Format date and time using Intl.DateTimeFormat to properly handle timezone
    let dateStr = ''
    let timeStr = ''
    if (timestamp) {
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
        // Fallback to local timezone if Intl fails
        dateStr = timestamp.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
        timeStr = timestamp.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
      }
    } else if (raw?.created_at) {
      try {
        const fallbackDate = new Date(raw.created_at)
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
        dateStr = dateFormatter.format(fallbackDate)
        timeStr = timeFormatter.format(fallbackDate)
      } catch (e) {
        dateStr = ''
        timeStr = ''
      }
    }

    const requestNumber = raw?.eventassist_request_ticket ?? raw?.ticket ?? String(raw?.eventassist_request_id ?? raw?.id ?? '')
    // Use title from server (which uses event_type), fallback to event_type, then purpose
    const title = raw?.title ?? raw?.event_type ?? (raw?.purpose ? String(raw.purpose) : (raw?.location ? String(raw.location) : 'Event Assistance'))
    const status = (raw?.status ?? 'PENDING').toString().toUpperCase()
    const type = 'event'

    return {
      id: raw?.eventassist_request_id ?? requestNumber,
      requestNumber,
      title,
      date: dateStr,
      time: timeStr,
      status,
      type,
      amount: null,
      timestamp: timestamp || new Date(0),
      start_datetime: raw?.start_datetime ?? null,
      end_datetime: raw?.end_datetime ?? null,
      location: raw?.location ?? null,
      purpose: raw?.purpose ?? null,
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

// map of role_id -> role_name
const roleMap = {
  1: 'Resident',
  2: 'Barangay Captain',
  3: 'Barangay Secretary',
  4: 'Barangay Treasurer',
  5: 'Barangay Kagawad',
  6: 'SK Chairman',
  7: 'Sangguniang Kabawad',
  9: 'System Admin',
}

const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  const role = id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
  return role.toUpperCase()
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
const paymentFileInput = ref(null)
const paymentSuccess = ref(false)
const paymentError = ref('')
const showPaymentConfirmation = ref(false)
const isOnsitePayment = ref(false)
const showReceiptModal = ref(false)
const selectedReceipt = ref(null)

// Sample Activities Data (keep your own or server-based)
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

const filteredActivities = computed(() => {
  let filtered = [...activities.value]
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    filtered = filtered.filter(a => (a.user || '').toLowerCase().includes(q) || (a.action || '').toLowerCase().includes(q))
  }
  if (sortOption.value === 'newest') filtered.sort((a,b) => b.timestamp - a.timestamp)
  else if (sortOption.value === 'oldest') filtered.sort((a,b) => a.timestamp - b.timestamp)
  return filtered
})

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

/**
 * File upload helpers and payment flows
 */
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

  // Keep preview for potential future use, but we're not displaying it in the new design
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
  paymentError.value = ''
  
  if (!selectedRequest.value) {
    paymentError.value = 'No selected request. Please refresh the page and try again.'
    return;
  }

  // require either a file or a reference ID
  if (!uploadedFile.value && (!referenceId.value || referenceId.value.trim() === '')) {
    paymentError.value = 'Please upload a screenshot or enter a transaction reference ID before submitting.'
    return;
  }

  uploading.value = true;
  paymentError.value = '';
  paymentSuccess.value = false;

  try {
    const formData = new FormData();
    formData.append('fk_doc_request_id', Number(selectedRequest.value.id || selectedRequest.value.requestNumber));
    // Support both id and user_id fields
    const userId = user.value?.id ?? user.value?.user_id ?? null;
    if (userId) {
      formData.append('fk_user_id', Number(userId));
    }
    // send keys the controller expects
    formData.append('request_reference_ticket', selectedRequest.value.requestNumber ?? '');
    formData.append('paid_amount', String(Number(selectedRequest.value.amount ?? selectedRequest.value.processing_fee ?? 0)));
    
    // Always send the payment method (gateway)
    if (selectedGateway.value) {
      formData.append('gateway', selectedGateway.value);
    }
    
    // Send transaction reference ID if provided
    if (referenceId.value && referenceId.value.trim() !== '') {
      formData.append('transaction_ref', referenceId.value.trim());
    }
    
    // Send payment proof screenshot if uploaded
    if (uploadedFile.value) {
      formData.append('evidence', uploadedFile.value, uploadedFile.value.name);
    }

    // Use axios directly for file uploads to handle JSON responses properly
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
      if (axiosError.response) {
        if (axiosError.response.status === 422) {
          const errors = axiosError.response.data.errors || {}
          const firstError = Object.values(errors).flat()[0]
          paymentError.value = firstError || 'Validation error. Please check your payment information and try again.'
        } else if (axiosError.response.status === 403) {
          paymentError.value = 'You do not have permission to submit this payment.'
        } else if (axiosError.response.status === 404) {
          paymentError.value = 'Request not found. Please refresh the page and try again.'
        } else {
          paymentError.value = axiosError.response.data?.message || 'Failed to submit payment evidence. Please try again.'
        }
      } else if (axiosError.request) {
        paymentError.value = 'Network error. Please check your internet connection and try again.'
      } else {
        paymentError.value = axiosError.message || 'An unexpected error occurred. Please try again.'
      }
    }
  } catch (err) {
    console.error('Unexpected error in submitEvidence:', err);
    paymentError.value = err.message || 'An unexpected error occurred while submitting evidence. Please try again.'
  } finally {
    uploading.value = false;
  }
};


const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

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
    showSettings.value = false
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

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_resident'))
}
const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_resident'))
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_resident'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_resident'))
}

const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_activities_resident'))
}

const switchTab = (tab) => {
    currentTab.value = tab
    showModeDropdown.value = false
}

const openFAQ = () => {
    router.visit(route('help_center_resident'))
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

const closePaymentConfirmation = () => {
  showPaymentConfirmation.value = false
  isOnsitePayment.value = false
  // Clear evidence after confirmation is closed
  clearEvidence()
  // Refresh the page data to show updated payment status
  router.reload({ only: ['documentRequests', 'payments'] })
}

const simulateScan = async (createReceipt = false) => {
  if (!selectedRequest.value) {
    alert('No selected request.');
    return;
  }
  if (!selectedGateway.value) {
    alert('No gateway selected.');
    return;
  }

  scanning.value = true;

  const methodId = paymentMethodsMap.value[selectedGateway.value] ?? null;

  const payload = {
    fk_doc_request_id: selectedRequest.value.id,
    fk_user_id: user.value?.id ?? null,
    request_reference_ticket: selectedRequest.value.requestNumber ?? null,
    paid_amount: Number(selectedRequest.value.amount ?? selectedRequest.value.processing_fee ?? 0),
    fk_pay_method_id: methodId,
    gateway: selectedGateway.value,
    client_note: `Simulated scan from ${selectedGateway.value}`,
    create_receipt: createReceipt ? true : false,
  };

  try {
    router.post(route('payments.store'), payload, {
      preserveScroll: true,
      onSuccess: (page) => {
        scanning.value = false;
        showQRModal.value = false;
        alert('Payment record created (simulated). If you selected receipt, a receipt was also created.');
        selectedGateway.value = null;
        selectedRequest.value = null;
      },
      onError: (errors) => {
        scanning.value = false;
        console.error('Payment create errors:', errors);
        if (typeof errors === 'object' && errors !== null) {
          const firstError = Object.values(errors).flat()[0]
          paymentError.value = firstError || 'Failed to create payment. Please try again.'
        } else {
          paymentError.value = 'Failed to create payment. Please try again.'
        }
      },
    });
  } catch (err) {
    scanning.value = false;
    console.error(err);
    paymentError.value = err.message || 'An unexpected error occurred while simulating the scan. Please try again.'
  }
};

const closePaymentModal = () => {
    showPaymentModal.value = false
}

const processPayment = (gateway) => {
    alert(`Processing payment via ${gateway}...\nRequest: ${selectedRequest.value.title}\nAmount: ₱${selectedRequest.value.amount}`)
    closePaymentModal()
    selectedRequest.value = null
}

const acknowledgeOnsite = async () => {
  paymentError.value = ''
  
  if (!selectedRequest.value) {
    paymentError.value = 'No request selected. Please refresh the page and try again.'
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
    if (axiosError.response) {
      if (axiosError.response.status === 422) {
        const errors = axiosError.response.data.errors || {}
        const firstError = Object.values(errors).flat()[0]
        paymentError.value = firstError || 'Validation error. Please check your payment information and try again.'
      } else if (axiosError.response.status === 403) {
        paymentError.value = 'You do not have permission to submit this payment.'
      } else if (axiosError.response.status === 404) {
        paymentError.value = 'Request not found. Please refresh the page and try again.'
      } else {
        paymentError.value = axiosError.response.data?.message || 'Failed to create onsite payment. Please try again.'
      }
    } else if (axiosError.request) {
      paymentError.value = 'Network error. Please check your internet connection and try again.'
    } else {
      paymentError.value = axiosError.message || 'An unexpected error occurred. Please try again.'
    }
  }
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
  // For event assistance requests, prioritize event_date + event_start
  // For document requests, use pickup_start
  let startRaw = null;
  
  // Check if this is an event assistance request
  if (request?.event_assist_request_id || request?.event_type) {
    // Event assistance: try pickup_start first (set by backend), then event_date + event_start
    startRaw = request?.pickup_start ?? request?.raw?.pickup_start ?? null;
    
    // If pickup_start is not available, try to combine event_date + event_start
    if (!startRaw && request?.event_date && request?.event_start) {
      try {
        const eventDate = parseDate(request.event_date);
        const eventStart = request.event_start;
        
        // event_start might be a time string (H:i:s) or a datetime
        if (eventDate) {
          const dateStr = eventDate.toISOString().split('T')[0]; // YYYY-MM-DD
          const timeStr = typeof eventStart === 'string' ? eventStart : (eventStart instanceof Date ? eventStart.toTimeString().split(' ')[0] : null);
          
          if (timeStr && timeStr.match(/^\d{2}:\d{2}:\d{2}$/)) {
            // It's a time string, combine with date
            startRaw = `${dateStr}T${timeStr}`;
          } else {
            // It's already a datetime
            startRaw = request.event_start;
          }
        }
      } catch (e) {
        // Fallback to event_start as-is
        startRaw = request.event_start;
      }
    } else if (!startRaw && request?.event_start) {
      startRaw = request.event_start;
    }
  } else {
    // Document request: use pickup_start
    startRaw = request?.pickup_start ?? request?.raw?.pickup_start ?? null;
  }

  const startDate = parseDate(startRaw);

  const dateOptions = { month: 'short', day: '2-digit', year: 'numeric' };
  const timeOptions = { hour: 'numeric', minute: '2-digit' };

  if (startDate) {
    const dateStr = startDate.toLocaleDateString('en-US', dateOptions).toUpperCase();
    const startTimeStr = startDate.toLocaleTimeString('en-US', timeOptions);

    // Only show start time, not end time
    const hasTime = !(startTimeStr === '12:00 AM' && startDate.getHours() === 0 && startDate.getMinutes() === 0);
    if (hasTime) {
      return `${dateStr}, ${startTimeStr}`;
    }
    return `${dateStr}, 9:00 AM`;
  }

  // If no date available, return a message instead of a default date
  return 'Date not specified';
};

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
      <title>Payment Receipt - ${receipt.requestNumber || 'N/A'}</title>
      <meta charset="UTF-8">
      <style>
        @media print {
          @page {
            margin: 20mm;
            size: A4;
          }
        }
        body {
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          padding: 40px;
          max-width: 800px;
          margin: 0 auto;
          background: white;
        }
        .receipt-header {
          text-align: center;
          margin-bottom: 30px;
          border-bottom: 3px solid #ff8c42;
          padding-bottom: 20px;
        }
        .receipt-title {
          font-size: 28px;
          font-weight: 700;
          color: #ff8c42;
          margin: 0 0 10px 0;
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
    
    const printContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Payment Receipt - ${receipt.requestNumber || 'N/A'}</title>
        <meta charset="UTF-8">
        <style>
          @media print {
            @page {
              margin: 20mm;
              size: A4;
            }
          }
          body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            background: white;
          }
          .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #ff8c42;
            padding-bottom: 20px;
          }
          .receipt-title {
            font-size: 28px;
            font-weight: 700;
            color: #ff8c42;
            margin: 0 0 10px 0;
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
    
    const blob = new Blob([printContent], { type: 'text/html' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `payment-receipt-${receipt.requestNumber || 'receipt'}-${Date.now()}.html`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error downloading receipt:', error)
    alert('Failed to download receipt. Please try printing instead.')
  }
}
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* list-level payment badge */
.request-status-and-payment {
  display: flex;
  flex-direction: column;                  /* Change from 2px to 4px or 6px */
  align-items: flex-end;
  width: 100%;
}   
.request-status-wrapper {
  display: flex;
  flex-direction: row;  /* Change from column to row */
  align-items: center;  /* Change from flex-end to center */
  gap: 12px;  /* Adjust this for horizontal spacing */
}

/* container for payment row (keeps it compact) */
.request-payment-row {
  display: flex;
  justify-content: flex-start;  /* Change from flex-end to flex-start */
  order: -1;  /* Add this to make payment appear first */
}

/* badge base */
.payment-list-badge {
  display:inline-flex;
  align-items:center;
  gap:4px;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight:600;
  font-size:0.92rem;
}

/* variants */
.payment-list-badge.pending { background:#fff3cd; color:#856404; border:1px solid #ffeeba; }
.payment-list-badge.approved { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
.payment-list-badge.rejected { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

/* when no payment */
.payment-list-badge.none { background:transparent; color:#6c757d; border:1px dashed rgba(0,0,0,0.06); }

/* small icon */
.payment-list-badge .badge-icon { font-size:1.08rem; line-height:1; }

/* text */
.payment-list-badge .badge-text { font-weight:600; font-size:0.9rem; }


/* Payment icon + label */
.payment-status-icon-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 1px; /* visual spacing from header */
}

.payment-status-icon {
  width: 44px;
  height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  padding: 6px;
  box-shadow: 0 1px 0 rgba(0,0,0,0.04);
}

/* color variants */
.payment-status-icon.pending { color: #856404; background: #fff3cd; border: 1px solid #ffeeba; }
.payment-status-icon.approved { color: #155724; background: #d4edda; border: 1px solid #c3e6cb; }
.payment-status-icon.rejected { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; }

/* svg sizing */
.payment-status-icon .icon-svg { width: 24px; height: 24px; display:block; }

/* label to the right */
.payment-status-label {
  font-weight: 600;
  font-size: 0.98rem;
}


.upload-section {
  margin: 16px 0;
  text-align: center;
}
.upload-btn-payment {
  background: linear-gradient(135deg, #2bb24a, #239640);
  color: white;
  border: none;
  padding: 12px 40px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: auto;
  max-width: 300px;
  text-transform: uppercase;
  box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
  margin: 0 auto;
}
.upload-btn-payment:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}
.uploaded-file-info-compact {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
  border-radius: 8px;
  border: 1px solid #4caf50;
  box-shadow: 0 1px 4px rgba(76, 175, 80, 0.2);
  font-size: 14px;
  width: 100%;
}
.file-checkmark {
  color: #4caf50;
  font-size: 18px;
  font-weight: 700;
}
.file-name-compact {
  flex: 1;
  color: #2e7d32;
  font-weight: 600;
  font-size: 14px;
  word-break: break-word;
  overflow: hidden;
  text-overflow: ellipsis;
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
.remove-file-btn-small:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.upload-preview {
  margin-top: 12px;
  display: flex;
  gap: 12px;
  align-items: center;
  justify-content: center; /* <-- center everything horizontally */
  text-align: center;      /* <-- center text */
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

  max-width: 350px;   /* <-- adjust here */
  width: 100%;        /* stays responsive */
  margin-left: auto;  
  margin-right: auto; /* centers the form */
}
.label {
  font-size: 0.85rem;
  display: block;
  margin-bottom: 6px;
}
.reference-input {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-bottom: 8px;
}
.evidence-actions {
  display: flex;
  gap: 8px;
}
.submit-evidence-btn, .clear-evidence-btn {
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.submit-evidence-btn {
  background: #28a745;
  color: white;
  font-weight: bold;
  border: none;
}
.clear-evidence-btn {
  background: #e0e0e0;
  border: none;
  font-weight: bold;
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
    min-width: 220px;
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
    text-transform: uppercase;
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
.request-card.approved {
border-left-color: #2bb24a;
}
.request-card.pending {
border-left-color: #ff9800;
}
.request-card.rejected {
border-left-color: #dc3545;
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
font-size: 12px;
font-weight: bold;
text-transform: uppercase;
white-space: nowrap;
}
.request-status.approved {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
}
.request-status.pending {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
}
.request-status.rejected {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
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
.approved-icon .status-badge {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
}
.pending-icon .status-badge {
background: linear-gradient(135deg, #ff9800, #f57c00);
color: white;
}
.rejected-icon .status-badge {
background: linear-gradient(135deg, #dc3545, #c82333);
color: white;
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
  width: 540px; /* adjust width as needed */
  padding: 12px 15px;
  background: #fffbf0;
  border-left: 4px solid #ff9800;
  border-radius: 5px;
  margin: 20px auto; /* centers the box horizontally and keeps bottom margin */
  text-align: left; /* keeps text aligned to the left inside */
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
padding: 12px 10px;
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
}
.pay-online-btn-modal:hover {
transform: translateY(-2px);
}
.pay-onsite-btn-modal {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
}
.pay-onsite-btn-modal:hover {
transform: translateY(-2px);
}
.close-btn {
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
margin-bottom: 1%;
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

.badge-icon {
    font-size: 18px;
}

.badge-text {
    font-size: 14px;
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

.receipt-view-btn {
    background: linear-gradient(135deg, #239640, #1e7d36);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.receipt-view-btn:hover {
    background: linear-gradient(135deg, #1e7d36, #155724);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.4);
}

/* Receipt Modal Styles */
.receipt-modal-container {
    background: white;
    border-radius: 20px;
    padding: 35px;
    width: 90%;
    max-width: 650px;
    max-height: 85vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.receipt-modal-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.receipt-title {
    font-size: 24px;
    font-weight: 700;
    color: #ff8c42;
    margin: 0;
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.receipt-details-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
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
    font-size: 18px;
    font-family: 'Courier New', monospace;
}

.receipt-image-container {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 12px;
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
    display: block;
    margin: 0 auto;
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
    padding: 12px 15px;
    border-radius: 8px;
}

.receipt-note p {
    margin: 0;
    font-size: 13px;
    color: #856404;
    line-height: 1.6;
}

.receipt-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.download-receipt-btn,
.print-receipt-btn,
.close-receipt-btn {
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.download-receipt-btn {
    background: #239640;
    color: white;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.download-receipt-btn:hover {
    background: #1e7d36;
    transform: translateY(-1px);
}

.print-receipt-btn {
    background: #007DFF;
    color: white;
    box-shadow: 0 2px 8px rgba(0, 125, 255, 0.3);
}

.print-receipt-btn:hover {
    background: #0062CC;
    transform: translateY(-1px);
}

.close-receipt-btn {
    background: #ff8c42;
    color: white;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.close-receipt-btn:hover {
    background: #e67a2d;
    transform: translateY(-1px);
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
.input-and-actions {
  display: flex;
  align-items: center; /* vertically center items */
  gap: 8px;
}

/* Make input and buttons the same height */
.reference-input {
  height: 45px;  
  width: 300px;              /* keep height fixed */
  padding: 0 12px;             /* remove vertical padding */
  border-radius: 6px;
  border: 1px solid #ccc;
  box-sizing: border-box;      /* ensures height is exact */
  font-size: 14px;
}

.evidence-actions {
  display: flex;
  gap: 5px;
}

.submit-evidence-btn,
.clear-evidence-btn {
  height: 45px;                /* same as input */
  padding: 0 16px;             /* only horizontal padding */
  display: flex;
  align-items: center;         /* vertical centering */
  justify-content: center;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  box-sizing: border-box;
}

.submit-evidence-btn {
  background: #28a745;
  color: white;
}

.clear-evidence-btn {
  background: #e0e0e0;
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
}

/* Terms Modal Styles */
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