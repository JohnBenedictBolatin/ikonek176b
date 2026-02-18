<template>
    <Head>
        <title>Payments</title>
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
                        <div class="profile-name">{{user.name || 'Unknown User'}}</div>
                        <div class="profile-role">{{displayRole}}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item active"
                    >
                        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        Payments
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
                    <!-- Payments Header -->
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Payments</h2>
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
                                    <button @click="selectSort('amount-high')" :class="{ active: sortOption === 'amount-high' }">AMOUNT (HIGH-LOW)</button>
                                    <button @click="selectSort('amount-low')" :class="{ active: sortOption === 'amount-low' }">AMOUNT (LOW-HIGH)</button>
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
                                    <button @click="selectFilter('cash')" :class="{ active: filterOption === 'cash' }">CASH</button>
                                    <button @click="selectFilter('gcash')" :class="{ active: filterOption === 'gcash' }">GCASH</button>
                                    <button @click="selectFilter('maya')" :class="{ active: filterOption === 'maya' }">MAYA</button>
                                    <button @click="selectFilter('onsite')" :class="{ active: filterOption === 'onsite' }">ONSITE</button>
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

                    <!-- Payments Container -->
                    <div class="requests-container">
                        <div 
                            v-for="payment in paginatedPayments" 
                            :key="payment.id"
                            class="request-card"
                        >
                            <div class="request-content">
                                <div class="request-left">
                                    <img :src="payment.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" />
                                    <div class="request-info">
                                        <h3 class="request-name">{{ payment.requestor }}</h3>
                                        <p class="request-doc-type">{{ payment.documentType }}</p>
                                        <p class="request-ref-code">{{ payment.document }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <p class="request-date">{{ payment.date }}</p>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button 
                                            @click.stop="openModal(payment)" 
                                            class="view-btn" 
                                            type="button"
                                        >
                                            VIEW DETAILS
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No payments message -->
                        <div v-if="filteredPayments.length === 0" class="no-requests" style="grid-column: 1 / -1;">
                            <p>No payments found matching your criteria.</p>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div v-if="filteredPayments.length > 0" class="pagination-container">
                        <div class="pagination-info">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredPayments.length) }} of {{ filteredPayments.length }} payments
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
                                PREVIOUS
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
                                NEXT
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details Modal -->
        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <!-- Close Button -->
                <button @click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div v-if="selectedPayment" class="modal-content">
                    <!-- Top Section -->
                    <div class="modal-top" style="grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
                        <div class="modal-user-section" style="display: flex; align-items: center; gap: 20px;">
                            <img :src="selectedPayment.profileImg || '/assets/DEFAULT.jpg'" alt="Profile" class="modal-avatar" style="width: 80px; height: 80px; flex-shrink: 0;" />
                            <div style="flex: 1; min-width: 0;">
                                <h3 class="modal-name" style="font-size: 24px; margin-bottom: 6px; font-weight: 700; color: #1a1a1a;">{{ selectedPayment.requestor || 'Unknown' }}</h3>
                                <p class="modal-label" style="font-size: 15px; margin: 0; color: #6b7280; font-weight: 500;">Payment Request</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: row; gap: 20px; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ff8c42 0%, #ff7a28 100%); color: white; padding: 20px 24px; border-radius: 16px; box-shadow: 0 4px 16px rgba(255, 122, 40, 0.25); min-height: fit-content;">
                            <div style="flex: 1;">
                                <p style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0; opacity: 0.95;">Payment Method</p>
                                <h3 style="font-size: 20px; font-weight: 700; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.2); line-height: 1.3;">{{ (selectedPayment.paymentMethod || 'UNKNOWN METHOD').toUpperCase() }}</h3>
                            </div>
                            <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.3); flex-shrink: 0;"></div>
                            <div style="flex: 0 0 auto; text-align: right;">
                                <p style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0; opacity: 0.95;">Document Number</p>
                                <p style="font-size: 18px; font-weight: 700; margin: 0; font-family: 'SF Mono', 'Monaco', 'Cascadia Code', monospace; letter-spacing: 0.5px; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ selectedPayment.document }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="modal-details">
                        <!-- Payment Information -->
                        <div class="detail-section" style="margin-bottom: 24px;">
                            <h4 class="section-title" style="margin-bottom: 16px; font-size: 20px;">Payment Information</h4>
                            <div class="details-grid" style="grid-template-columns: repeat(4, 1fr); gap: 12px 16px;">
                                <div class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Document Type:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedPayment.documentType }}</p>
                                </div>
                                <div class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Amount:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #239640; font-weight: 600;">₱{{ selectedPayment.amount.toFixed(2) }}</p>
                                </div>
                                <div class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Date Submitted:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedPayment.date }}</p>
                                </div>
                                <div class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Contact Number:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedPayment.contact || 'N/A' }}</p>
                                </div>
                                <div v-if="selectedPayment.transactionId && selectedPayment.transactionId !== 'N/A'" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Transaction ID:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a; font-family: monospace;">{{ selectedPayment.transactionId }}</p>
                                </div>
                                <div v-if="selectedPayment.paymentTime && selectedPayment.paymentTime !== 'N/A'" class="detail-item" style="margin: 0;">
                                    <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Payment Time:</p>
                                    <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a;">{{ selectedPayment.paymentTime }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Proof of Payment Section -->
                        <div class="detail-section">
                            <h4 class="section-title">Proof of Payment</h4>
                            <div style="margin-top: 15px;">
                                <div v-if="selectedPayment.receiptImage" class="attachment-preview-section">
                                    <p class="detail-label" style="font-size: 14px; font-weight: 700; color: #6b7280; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">Receipt Image:</p>
                                    <div v-if="selectedPayment.transactionId && selectedPayment.transactionId !== 'N/A'" style="margin-bottom: 12px; text-align: center;">
                                        <p class="detail-label" style="font-size: 13px; margin-bottom: 6px; color: #6b7280;">Transaction ID:</p>
                                        <p class="detail-value" style="font-size: 15px; margin: 0; color: #1a1a1a; font-family: monospace;">{{ selectedPayment.transactionId }}</p>
                                    </div>
                                    <div class="image-preview-container-inline">
                                        <img 
                                            :src="selectedPayment.receiptImage" 
                                            alt="Payment Receipt" 
                                            class="attachment-image-inline"
                                            style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: block; margin: 0 auto;"
                                            @error="handleImageError"
                                        />
                                    </div>
                                    <div class="attachment-actions-inline" style="margin-top: 15px; text-align: center;">
                                        <a 
                                            :href="selectedPayment.receiptImage" 
                                            :download="`receipt_${selectedPayment.transactionId || selectedPayment.document || 'attachment'}.jpg`" 
                                            class="download-receipt-btn"
                                            target="_blank"
                                        >
                                            DOWNLOAD RECEIPT
                                        </a>
                                    </div>
                                </div>
                                <div v-else style="padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                                    <p style="color: #999; font-size: 14px; margin: 0;">No proof of payment uploaded</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - Only show for PENDING payments -->
                        <div class="modal-actions" v-if="selectedPayment.status === 'PENDING'">
                            <button @click="openApproveModal" class="approve-btn">
                                CONFIRM PAYMENT
                            </button>
                            <button @click="openRejectModal" class="reject-btn">
                                REJECT PAYMENT
                            </button>
                        </div>
                        
                        <!-- Status display for non-pending payments -->
                        <div class="modal-status-display" v-else style="text-align: center; margin-top: 20px;">
                            <div class="status-info">
                                <p class="status-label" style="font-size: 14px; color: #666; margin-bottom: 8px;">Payment Status:</p>
                                <span class="payment-status-badge-large" :class="selectedPayment.status?.toLowerCase() || 'pending'">
                                    {{ selectedPayment.status || 'PENDING' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Confirmed Success Modal -->
        <div v-if="showConfirmModal" class="modal-overlay" @click="closeConfirmModal">
            <div class="success-modal" @click.stop>
                <div class="success-modal-header">
                    <div class="success-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="success-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="success-modal-title">Payment Confirmed!</h3>
                </div>
                <div class="success-modal-body">
                    <p class="success-message">
                        Payment from <strong>{{ confirmedPaymentSnapshot?.requestor || 'Unknown' }}</strong> for 
                        <strong>{{ confirmedPaymentSnapshot?.document || 'N/A' }}</strong> has been successfully confirmed.
                    </p>
                </div>
                <div class="success-modal-footer" style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <button @click="openConfirmReceiptModal" class="success-modal-btn" style="background: linear-gradient(135deg, #239640 0%, #1e7e34 100%); color: white;">
                        VIEW & PRINT RECEIPT
                    </button>
                    <button @click="closeConfirmModal" class="success-modal-btn">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Post-Confirmation Receipt Modal (for onsite & online - print/download right after approval) -->
        <div v-if="showConfirmReceiptModal && confirmedPaymentSnapshot" class="modal-overlay" @click="closeConfirmReceiptModal">
            <div class="receipt-modal-container" @click.stop style="max-width: 600px;">
                <button @click="closeConfirmReceiptModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="receipt-modal-content">
                    <h3 class="receipt-title">Payment Receipt</h3>
                    <div class="receipt-details-box">
                        <div class="receipt-row">
                            <span class="receipt-label">Payment No:</span>
                            <span class="receipt-value">{{ confirmedPaymentSnapshot.payment_no || confirmedPaymentSnapshot.transactionId || 'N/A' }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Request Ticket:</span>
                            <span class="receipt-value">{{ confirmedPaymentSnapshot.document || 'N/A' }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Document Type:</span>
                            <span class="receipt-value">{{ confirmedPaymentSnapshot.documentType || 'N/A' }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Payer Name:</span>
                            <span class="receipt-value">{{ confirmedPaymentSnapshot.name || confirmedPaymentSnapshot.requestor || 'N/A' }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Amount Paid:</span>
                            <span class="receipt-value amount">₱{{ (confirmedPaymentSnapshot.amount || 0).toFixed(2) }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Payment Method:</span>
                            <span class="receipt-value">{{ (confirmedPaymentSnapshot.paymentMethod || 'ONSITE').toUpperCase() }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Payment Date:</span>
                            <span class="receipt-value">{{ confirmedPaymentSnapshot.paymentTime || confirmedPaymentSnapshot.date || 'N/A' }}</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Status:</span>
                            <span class="receipt-value" style="color: #239640;">APPROVED</span>
                        </div>
                        <div class="receipt-row">
                            <span class="receipt-label">Approved By:</span>
                            <span class="receipt-value">{{ confirmedPaymentSnapshot.treasurerName || 'N/A' }}</span>
                        </div>
                    </div>
                    <div v-if="confirmedPaymentSnapshot.receiptImage" class="receipt-image-container">
                        <div class="receipt-image-label">Payment Receipt Image</div>
                        <img :src="confirmedPaymentSnapshot.receiptImage" alt="Payment Receipt" class="receipt-image" @error="handleImageError" />
                    </div>
                    <div class="receipt-note">
                        <p>Thank you for your payment. This digital receipt is proof of payment issued by Barangay 176B.</p>
                    </div>
                    <div class="receipt-actions">
                        <button class="download-receipt-btn" @click="printOrDownloadConfirmReceipt('download')">DOWNLOAD</button>
                        <button class="print-receipt-btn" @click="printOrDownloadConfirmReceipt('print')">PRINT</button>
                        <button class="close-receipt-btn" @click="closeConfirmReceiptModal">CLOSE</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Confirmation Modal (Are you sure?) -->
        <div v-if="showApproveModal" class="modal-overlay" @click="closeApproveModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content">
                    <div class="confirm-icon-wrapper confirm-icon-green">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="confirm-title confirm-title-green">Confirm Payment</h3>
                    <p class="confirm-message">
                        Are you sure you want to confirm the payment from 
                        <strong>{{ selectedPayment?.requestor }}</strong> for 
                        <strong>{{ selectedPayment?.document }}</strong>?
                        This action cannot be undone.
                    </p>
                    <div class="confirm-actions">
                        <button @click="closeApproveModal" class="confirm-cancel-btn">CANCEL</button>
                        <button @click="confirmPayment" class="confirm-approve-btn">CONFIRM</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Confirmation Modal (Are you sure?) -->
        <div v-if="showRejectModal" class="modal-overlay" @click="closeRejectModal">
            <div class="confirm-modal-container" @click.stop>
                <div class="confirm-modal-content">
                    <div class="confirm-icon-wrapper confirm-icon-red">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="confirm-title confirm-title-red">Reject Payment</h3>
                    <p class="confirm-message">
                        Are you sure you want to reject the payment from 
                        <strong>{{ selectedPayment?.requestor }}</strong> for 
                        <strong>{{ selectedPayment?.document }}</strong>?
                        This action cannot be undone.
                    </p>
                    <div class="confirm-actions">
                        <button @click="closeRejectModal" class="confirm-cancel-btn">CANCEL</button>
                        <button @click="rejectPaymentConfirmed" class="confirm-reject-btn">REJECT</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Rejected Success Modal -->
        <div v-if="showRejectConfirmModal" class="modal-overlay" @click="closeRejectConfirmModal">
            <div class="error-modal" @click.stop>
                <div class="error-modal-header">
                    <div class="error-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="error-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="error-modal-title">Payment Rejected!</h3>
                </div>
                <div class="error-modal-body">
                    <p class="error-message-text">
                        Payment from <strong>{{ rejectedPaymentSnapshot?.requestor }}</strong> for 
                        <strong>{{ rejectedPaymentSnapshot?.document }}</strong> has been rejected.
                    </p>
                </div>
                <div class="error-modal-footer">
                    <button @click="closeRejectConfirmModal" class="error-modal-btn">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Receipt Viewer Modal -->
        <div v-if="showReceiptModal && selectedPayment" class="modal-overlay" @click="closeReceiptModal">
            <div class="receipt-modal-container" @click.stop>
                <button @click="closeReceiptModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="receipt-modal-content">
                    <h3 class="receipt-title">Proof of Payment</h3>
                    <div class="receipt-image-container" v-if="selectedPayment.receiptImage">
                        <img :src="selectedPayment.receiptImage" alt="Payment Receipt" class="receipt-image" @error="handleImageError" />
                    </div>
                    <div v-else class="no-receipt-container">
                        <p class="no-receipt-message">No proof of payment image available</p>
                    </div>
                    <div class="receipt-details">
                        <p><strong>Transaction ID:</strong> {{ selectedPayment.transactionId || 'N/A' }}</p>
                        <p><strong>Payment Method:</strong> {{ (selectedPayment.paymentMethod || 'N/A').toUpperCase() }}</p>
                        <p><strong>Amount:</strong> ₱{{ selectedPayment.amount?.toFixed(2) || '0.00' }}</p>
                        <p><strong>Date & Time:</strong> {{ selectedPayment.paymentTime || 'N/A' }}</p>
                        <p v-if="selectedPayment.status"><strong>Status:</strong> {{ selectedPayment.status }}</p>
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
                            As a Treasurer/Payment Handler, you are responsible for processing payment requests, verifying payment receipts, approving or rejecting payments, and maintaining accurate payment records for the iKonek176B system. You must exercise your payment handling privileges with care and in accordance with barangay financial policies and regulations.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">2. Access and Security</h3>
                        <p class="terms-text">
                            You have been granted access to payment processing functions. You must maintain the confidentiality of your login credentials and immediately report any suspected security breaches. Sharing your account credentials with unauthorized persons is strictly prohibited and may result in immediate account termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">3. Payment Processing</h3>
                        <p class="terms-text">
                            You are responsible for reviewing payment requests in a timely manner. When processing payments, you must:
                            <ul class="terms-list">
                                <li>Verify that payment receipts are valid and match the payment amount</li>
                                <li>Ensure payment methods are correctly identified (Cash, GCash, Maya, Onsite)</li>
                                <li>Approve or reject payments based on valid criteria and documented requirements</li>
                                <li>Maintain accurate records of all payment transactions</li>
                                <li>Process payments in accordance with barangay financial policies</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">4. Financial Data Privacy and Confidentiality</h3>
                        <p class="terms-text">
                            You have access to sensitive financial information of residents and officials. You must handle all payment data in accordance with the Data Privacy Act of 2012. Financial information must only be accessed for legitimate payment processing purposes and must never be disclosed to unauthorized parties or used for personal gain.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">5. Payment Approval and Rejection</h3>
                        <p class="terms-text">
                            When approving or rejecting payments, you must ensure that all decisions are justified, documented, and in compliance with barangay financial policies. Discrimination or bias in processing payments is strictly prohibited. All payment actions are logged and may be subject to audit.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">6. Receipt Verification</h3>
                        <p class="terms-text">
                            You must carefully verify payment receipts to ensure they are legitimate and match the payment request. If a receipt appears fraudulent or does not match the payment details, you must reject the payment and document the reason. Failure to properly verify receipts may result in financial discrepancies.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">7. Limitations and Restrictions</h3>
                        <p class="terms-text">
                            Your payment processing privileges do not grant you the right to:
                            <ul class="terms-list">
                                <li>Access or modify system code or database structure without authorization</li>
                                <li>Bypass system security measures or attempt to exploit system vulnerabilities</li>
                                <li>Use payment processing functions for personal purposes or to gain unfair advantage</li>
                                <li>Delete or modify payment logs or audit trails</li>
                                <li>Grant payment processing privileges to other users without proper authorization</li>
                                <li>Approve payments without proper verification</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">8. Prohibited Activities</h3>
                        <p class="terms-text">
                            The following activities are strictly prohibited:
                            <ul class="terms-list">
                                <li>Unauthorized access to payment records or financial data</li>
                                <li>Tampering with payment records or documentation</li>
                                <li>Approving payments without proper verification</li>
                                <li>Sharing financial information outside of official channels</li>
                                <li>Engaging in any activity that compromises system security or financial integrity</li>
                                <li>Accepting bribes or favors in exchange for payment approval</li>
                            </ul>
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">9. Accountability and Auditing</h3>
                        <p class="terms-text">
                            All payment processing actions are logged and monitored. You are accountable for all actions performed using your account. Regular audits may be conducted to ensure compliance with these terms and financial policies. Failure to comply may result in disciplinary action, including but not limited to account suspension or termination.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">10. Violations and Consequences</h3>
                        <p class="terms-text">
                            Violation of these terms and conditions may result in immediate suspension or termination of your payment processing account, legal action if applicable, and reporting to appropriate barangay authorities. The severity of consequences will depend on the nature and extent of the violation.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">11. Updates to Terms</h3>
                        <p class="terms-text">
                            These terms and conditions may be updated periodically. You will be notified of significant changes. Continued use of payment processing privileges after changes constitutes acceptance of the updated terms.
                        </p>
                    </div>

                    <div class="terms-section">
                        <h3 class="terms-section-title">12. Contact and Support</h3>
                        <p class="terms-text">
                            For questions, concerns, or to report issues related to your payment processing role, contact the Barangay 176B office at ikonek176b@dev.ph or +639193076338.
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
const isModalOpen = ref(false)
const selectedPayment = ref(null)
const showApproveModal = ref(false)
const showConfirmModal = ref(false)
const showReceiptModal = ref(false)
const showConfirmReceiptModal = ref(false)
const showRejectModal = ref(false)
const showRejectConfirmModal = ref(false)
const rejectedPaymentSnapshot = ref(null)
const confirmedPaymentSnapshot = ref(null)

// --- payments from server (Inertia props) ---
// page.props can vary: check both shapes
const serverPayments = computed(() => {
  // try the common shapes Inertia exposes
  return page?.props?.value?.payments ?? page?.props?.payments ?? []
})

// Convert server payments into the same shape the UI expects, and ensure dateObj is a JS Date
const payments = ref([])

const initializePaymentsFromServer = () => {
  console.log('🔄 Initializing payments from server', {
    serverPaymentsCount: serverPayments.value?.length ?? 0,
    firstPayment: serverPayments.value?.[0] ?? null
  })
  
  payments.value = (serverPayments.value || []).map(p => {
    // compute dateObj safely
    let dateObj = null
    try {
      dateObj = p.date_iso ? new Date(p.date_iso) : (p.date ? new Date(p.date) : new Date())
    } catch (e) {
      dateObj = new Date()
    }

    // Normalize incoming payment method into stable keys:
    // resultMethod will be one of: 'gcash', 'maya', 'cash', 'onsite'
    const raw = (p.paymentMethod ?? '').toString().trim().toLowerCase()

    let resultMethod = 'onsite' // safe default
    if (raw.includes('gcash')) {
    resultMethod = 'gcash'
    } else if (raw.includes('maya')) {
    resultMethod = 'maya'
    } else if (raw.includes('cash')) {
    resultMethod = 'cash'
    } else if (raw.includes('onsite') || raw.includes('on-site') || raw.includes('on site')) {
    resultMethod = 'onsite'
    } else if (raw === '') {
    // if backend gave empty, try to detect from paymentMethodRaw (if available)
    const raw2 = (p.paymentMethodRaw ?? '').toString().trim().toLowerCase();
    if (raw2.includes('onsite') || raw2.includes('on-site') || raw2.includes('on site')) {
        resultMethod = 'onsite'
    }
    // else leave default onsite or set to 'manual'
    }

    // Debug receipt image
    console.log(`📸 Payment ${p.id} receipt image:`, {
      receiptImage: p.receiptImage,
      receiptImageType: typeof p.receiptImage,
      receiptImageLength: p.receiptImage?.length,
      hasReceiptImage: !!p.receiptImage,
      fullPayment: p
    })

    return {
      id: p.id,
      requestor: p.requestor ?? 'Unknown',
      document: p.document ?? 'N/A',
      documentType: p.documentType ?? 'N/A',
      amount: Number(p.amount ?? 0),
      date: p.date ?? (dateObj.toLocaleDateString()),
      dateObj: dateObj,
      // keep a lowercase stable key for logic / classes:
      paymentMethod: (p.paymentMethod ?? resultMethod).toUpperCase(),
      // keep a lowercase stable key for logic / classes:
      paymentMethodKey: resultMethod,
      // keep original casing string available if you want the original label:
      paymentMethodRaw: p.paymentMethod ?? null,
      contact: p.contact ?? 'N/A',
      address: p.address ?? 'N/A',
      transactionId: p.transactionId ?? 'N/A',
      paymentTime: p.paymentTime ?? 'N/A',
      receiptImage: p.receiptImage ?? null,
      status: (p.status ?? 'PENDING').toUpperCase(),
      profileImg: p.profileImg ?? '/assets/DEFAULT.jpg',
    }
  })
  
  console.log('✅ Payments initialized:', {
    count: payments.value.length,
    paymentsWithReceipt: payments.value.filter(p => p.receiptImage).length,
    paymentsWithReceiptDetails: payments.value.filter(p => p.receiptImage).map(p => ({
      id: p.id,
      receiptImage: p.receiptImage
    }))
  })
}

// initialize once on mount
onMounted(() => {
  initializePaymentsFromServer()
})

// Computed filtered payments
// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

const filteredPayments = computed(() => {
    let filtered = [...payments.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.requestor.toLowerCase().includes(query) ||
            item.document.toLowerCase().includes(query) ||
            item.documentType.toLowerCase().includes(query) ||
            item.paymentMethod.toLowerCase().includes(query) ||
            item.transactionId.toLowerCase().includes(query) ||
            (item.paymentMethodKey && item.paymentMethodKey.toLowerCase().includes(query))
        )
    }

    // Payment method filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.paymentMethodKey === filterOption.value.toLowerCase()
        )
    }

    // Sorting
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'amount-high') {
        filtered.sort((a, b) => b.amount - a.amount)
    } else if (sortOption.value === 'amount-low') {
        filtered.sort((a, b) => a.amount - b.amount)
    }

    return filtered
})

// Paginated payments
const paginatedPayments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredPayments.value.slice(start, end)
})

// Total pages
const totalPages = computed(() => {
    return Math.ceil(filteredPayments.value.length / itemsPerPage.value)
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

// -- helper to call backend and handle success/failure
const sendStatusUpdate = (paymentId, status, notes = '') => {
  // Use a direct URL so Ziggy missing-route errors won't break the call.
  const url = `/payments/${paymentId}/status`

  return new Promise((resolve, reject) => {
    router.post(url, { status, notes }, {
      preserveState: true,
      preserveScroll: true,
      only: [],
      onSuccess: (page) => {
        resolve(page)
      },
      onError: (errors) => {
        // Handle Inertia validation errors gracefully
        const errorMessage = errors?.payment_status || errors?.payment || errors?.message || 'An error occurred. Please try again.'
        reject({ message: errorMessage, response: { data: { message: errorMessage } } })
      },
      onFinish: () => {
        // optional cleanup hook
      }
    })
  })
}

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
    router.visit(route('login_treasurer'))
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const openModal = (payment) => {
    console.log('🔍 Opening payment modal:', {
      paymentId: payment.id,
      receiptImage: payment.receiptImage,
      hasReceiptImage: !!payment.receiptImage,
      paymentMethod: payment.paymentMethod,
      paymentMethodKey: payment.paymentMethodKey,
      fullPayment: payment
    })
    selectedPayment.value = { ...payment } // Create a copy to ensure reactivity
    isModalOpen.value = true
    console.log('📋 Selected payment after setting:', {
      selectedPayment: selectedPayment.value,
      receiptImage: selectedPayment.value?.receiptImage,
      receiptImageType: typeof selectedPayment.value?.receiptImage,
      receiptImageLength: selectedPayment.value?.receiptImage?.length
    })
}

const closeModal = () => {
    isModalOpen.value = false
    selectedPayment.value = null
}

const confirmPayment = async () => {
  if (!selectedPayment.value) return

  const paymentId = selectedPayment.value.id

  // Store full snapshot for success message and receipt (all approved payments get a receipt)
  const pay = selectedPayment.value
  const paymentNo = (pay.transactionId && pay.transactionId !== 'N/A') ? pay.transactionId : ('PAY-' + paymentId)
  const nowStr = new Date().toLocaleString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
  confirmedPaymentSnapshot.value = {
    requestor: pay.requestor,
    document: pay.document,
    name: pay.requestor,
    documentType: pay.documentType,
    doc_type: pay.documentType,
    amount: pay.amount,
    paymentMethod: pay.paymentMethod,
    method: pay.paymentMethodKey || 'onsite',
    transactionId: paymentNo,
    payment_no: paymentNo,
    date: pay.date,
    paymentTime: nowStr,
    status: 'APPROVED',
    receiptImage: pay.receiptImage,
    contact: pay.contact,
    address: pay.address,
    treasurerName: user.value?.name || 'Treasurer',
  }

  // Close the approval confirmation modal
  closeApproveModal()

  try {
    await sendStatusUpdate(paymentId, 'APPROVED', '')

    // Remove payment from list since it's no longer PENDING
    const index = payments.value.findIndex(p => p.id === paymentId)
    if (index > -1) payments.value.splice(index, 1)

    // Close main modal and show success confirmation
    closeModal()
    showConfirmModal.value = true
  } catch (err) {
    console.error('Failed to confirm payment:', err)
    // Handle error gracefully - show user-friendly message
    const errorMessage = err?.response?.data?.message || err?.message || 'Failed to confirm payment. Please try again.'
    alert(errorMessage)
    confirmedPaymentSnapshot.value = null
  }
}

const closeConfirmModal = () => {
    showConfirmModal.value = false
    showConfirmReceiptModal.value = false
    confirmedPaymentSnapshot.value = null
}

const openConfirmReceiptModal = () => {
    if (confirmedPaymentSnapshot.value) {
        showConfirmModal.value = false
        showConfirmReceiptModal.value = true
    }
}

const closeConfirmReceiptModal = () => {
    showConfirmReceiptModal.value = false
    confirmedPaymentSnapshot.value = null
}

const printOrDownloadConfirmReceipt = (mode) => {
    const receipt = confirmedPaymentSnapshot.value
    if (!receipt) return

    const receiptImageHtml = receipt.receiptImage
        ? `<div class="receipt-image-container"><div class="receipt-image-label">Payment Receipt Image</div><img src="${receipt.receiptImage}" alt="Receipt" style="max-width:100%;border-radius:8px;" /></div>`
        : ''

    const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt - ${receipt.payment_no || 'N/A'}</title>
            <meta charset="UTF-8">
            <style>
                body { font-family: 'Segoe UI', sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
                .receipt-header { text-align: center; margin-bottom: 30px; }
                .receipt-title { font-size: 28px; font-weight: 700; color: #2e2e2e; margin: 0 0 8px 0; border-bottom: 3px solid #ff8c42; padding-bottom: 20px; }
                .receipt-details-box { background: #f8f9fa; padding: 25px; border-radius: 16px; margin: 25px 0; }
                .receipt-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
                .receipt-row:last-child { border-bottom: none; }
                .receipt-label { font-weight: 600; color: #666; }
                .receipt-value { font-weight: 600; color: #2e2e2e; }
                .receipt-value.amount { color: #239640; font-size: 22px; }
                .receipt-note { background: #e8f8ed; border-left: 4px solid #239640; padding: 18px; border-radius: 12px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="receipt-header">
                <h1 class="receipt-title">Payment Receipt</h1>
                <p style="color: #666; margin: 0;">Barangay 176B</p>
            </div>
            <div class="receipt-details-box">
                <div class="receipt-row"><span class="receipt-label">Payment No:</span><span class="receipt-value">${receipt.payment_no || 'N/A'}</span></div>
                <div class="receipt-row"><span class="receipt-label">Request Ticket:</span><span class="receipt-value">${receipt.document || 'N/A'}</span></div>
                <div class="receipt-row"><span class="receipt-label">Document Type:</span><span class="receipt-value">${receipt.documentType || 'N/A'}</span></div>
                <div class="receipt-row"><span class="receipt-label">Payer Name:</span><span class="receipt-value">${receipt.name || receipt.requestor || 'N/A'}</span></div>
                <div class="receipt-row"><span class="receipt-label">Amount Paid:</span><span class="receipt-value amount">₱${(receipt.amount || 0).toFixed(2)}</span></div>
                <div class="receipt-row"><span class="receipt-label">Payment Method:</span><span class="receipt-value">${(receipt.paymentMethod || 'ONSITE').toUpperCase()}</span></div>
                <div class="receipt-row"><span class="receipt-label">Payment Date:</span><span class="receipt-value">${receipt.paymentTime || receipt.date || 'N/A'}</span></div>
                <div class="receipt-row"><span class="receipt-label">Status:</span><span class="receipt-value" style="color:#239640;">APPROVED</span></div>
                <div class="receipt-row"><span class="receipt-label">Approved By:</span><span class="receipt-value">${receipt.treasurerName || 'N/A'}</span></div>
            </div>
            ${receiptImageHtml}
            <div class="receipt-note">
                <p style="margin:0;font-size:14px;color:#1e7e34;">Thank you for your payment. This digital receipt is proof of payment issued by Barangay 176B.</p>
            </div>
        </body>
        </html>`

    if (mode === 'print') {
        const pw = window.open('', '_blank')
        pw.document.write(html)
        pw.document.close()
        pw.focus()
        setTimeout(() => { pw.print() }, 500)
    } else {
        const blob = new Blob([html], { type: 'text/html' })
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `receipt_${receipt.payment_no || 'payment'}.html`
        a.click()
        URL.revokeObjectURL(url)
    }
}

const viewReceipt = () => {
    if (selectedPayment.value && selectedPayment.value.receiptImage) {
        showReceiptModal.value = true
    } else {
        alert('No proof of payment available for this payment.')
    }
}

const handleImageError = (event) => {
    console.error('❌ Image load error:', {
      src: event.target.src,
      selectedPayment: selectedPayment.value,
      receiptImage: selectedPayment.value?.receiptImage
    })
    event.target.style.display = 'none'
    const container = event.target.closest('.receipt-image-container')
    if (container) {
        container.innerHTML = '<p class="no-receipt-message">Failed to load image</p>'
    }
}

const closeReceiptModal = () => {
    showReceiptModal.value = false
}

const openApproveModal = () => {
  if (!selectedPayment.value) return
  showApproveModal.value = true
}

const closeApproveModal = () => {
    showApproveModal.value = false
}

const openRejectModal = () => {
  if (!selectedPayment.value) return
  showRejectModal.value = true
}

// close without rejecting
const closeRejectModal = () => {
    showRejectModal.value = false
}

const rejectPaymentConfirmed = async () => {
  if (!selectedPayment.value) {
    showRejectModal.value = false
    return
  }

  const paymentId = selectedPayment.value.id

  // snapshot for UI feedback
  rejectedPaymentSnapshot.value = {
    id: selectedPayment.value.id,
    requestor: selectedPayment.value.requestor,
    document: selectedPayment.value.document,
  }

  try {
    await sendStatusUpdate(paymentId, 'REJECTED', '')

    // Remove payment from list since it's no longer PENDING
    const index = payments.value.findIndex(p => p.id === paymentId)
    if (index > -1) payments.value.splice(index, 1)

    // Close modals and show confirmation
    showRejectModal.value = false
    closeModal()
    showRejectConfirmModal.value = true
  } catch (err) {
    console.error('Failed to reject payment:', err)
    alert('Failed to reject payment. Check console for details.')
  }
}

// close the reject-success modal
const closeRejectConfirmModal = () => {
    showRejectConfirmModal.value = false
    rejectedPaymentSnapshot.value = null
}

const navigateToHistory = () => {
    router.visit(route('history_treasurer'))
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    // If the terms modal is open and the click is inside it, do nothing
    if (showTermsModal.value && event.target.closest('.terms-modal')) {
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

.modal-actions {
  display: flex;
  gap: 12px;
  margin-top: 12px;
}
.approve-btn {
  padding: 8px 14px;
  border-radius: 6px;
  border: none;
  background-color: #28a745;
  color: white;
  cursor: pointer;
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

/* Confirm modal action buttons */
.confirm-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 12px;
}
.confirm-cancel-btn {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #e0e0e0;
  background: white;
  color: #4a4a4a;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}
.confirm-approve-btn {
  padding: 8px 12px;
  border-radius: 6px;
  border: none;
  background-color: #239640;
  color: white;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.confirm-approve-btn:hover {
  background-color: #1e7d36;
  transform: translateY(-1px);
}

.confirm-reject-btn {
  padding: 8px 12px;
  border-radius: 6px;
  border: none;
  background-color: #dc3545;
  color: white;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.confirm-reject-btn:hover {
  background-color: #c82333;
  transform: translateY(-1px);
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
    min-width: 200px;
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
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
}

.search-btn:hover {
    background: #e9ecef;
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
    flex-shrink: 0;
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

.request-status-badge-wrapper {
    display: flex;
    align-items: center;
}

.request-date {
    font-size: 13px;
    color: #999;
    margin: 0;
}

.payment-status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-status-badge.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.payment-status-badge.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.payment-status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.request-type {
    font-size: 13px;
    color: #666;
    margin: 5px 0;
}

.payment-method-highlight {
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    color: white;
}

.payment-method-highlight.cash {
    background: #239640;
}

.payment-method-highlight.gcash {
    background: linear-gradient(135deg, #007DFF, #0062CC);
}

.payment-method-highlight.maya {
    background: linear-gradient(135deg, #00D632, #00A827);
}

.payment-info {
    font-size: 14px;
    color: #555;
    margin: 5px 0;
}

.request-date {
    font-size: 13px;
    color: #999;
    margin: 10px 0 0 0;
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

.view-btn {
    pointer-events: auto;
    position: relative;
    z-index: 10;
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
    background: #f8f9fa;
    color: #333;
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

.modal-payment-method {
    font-size: 19px;
    font-weight: 500;
    margin: 20px 0 0 0;
    margin-left: 100px;
    padding: 10px 20px;
    border-radius: 10px;
    color: white;
}

.modal-payment-method.cash {
    background: #239640;
}

.modal-payment-method.gcash {
    background: linear-gradient(135deg, #007DFF, #0062CC);
}

.modal-payment-method.paymaya {
    background: linear-gradient(135deg, #00D632, #00A827);
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
    box-shadow: 0 2px 8px rgba(255, 122, 40, 0.3);
    text-align: center;
}

.proof-btn:hover {
    background: #e66a1f;
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


.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 10px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.modal-status-display {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    text-align: center;
}

.status-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.status-label {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    margin: 0;
}

.payment-status-badge-large {
    padding: 8px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-status-badge-large.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.payment-status-badge-large.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.payment-status-badge-large.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.no-receipt-text {
    color: #999;
    font-size: 13px;
    font-style: italic;
    margin: 0;
    padding: 10px;
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

/* Confirmation Modal */
.confirm-modal-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    text-align: center;
}

.confirm-modal-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.confirm-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.confirm-icon-wrapper svg {
    width: 120px;
    height: 120px;
}

.confirm-icon-green svg {
    color: #239640;
}

.confirm-icon-red svg {
    color: #dc2626;
}

.confirm-title {
    font-size: 26px;
    font-weight: 700;
    margin: 0;
}

.confirm-title-green {
    color: #239640;
}

.confirm-title-red {
    color: #dc2626;
}

.confirm-message {
    font-size: 15px;
    color: #666;
    line-height: 1.6;
    margin: 0;
}

.confirm-ok-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 12px 40px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
    margin-top: 10px;
}

.confirm-ok-btn:hover {
    background: #1e7d36;
}


.reject-ok-btn {
    background: #dc3545 !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
}

.reject-ok-btn:hover {
    background: #c82333 !important;
}

/* Receipt Modal */
.receipt-modal-container {
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

.receipt-modal-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.receipt-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0;
    text-align: center;
}

.receipt-image-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.no-receipt-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
}

.no-receipt-message {
    color: #999;
    font-size: 14px;
    font-style: italic;
    margin: 0;
}

.receipt-image {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.receipt-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Receipt details box (for post-confirmation receipt modal) */
.receipt-details-box {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 25px;
    border-radius: 16px;
    margin: 15px 0;
    border: 1px solid #e9ecef;
}
.receipt-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}
.receipt-row:last-child { border-bottom: none; }
.receipt-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}
.receipt-value {
    font-weight: 600;
    color: #2e2e2e;
    font-size: 14px;
}
.receipt-value.amount {
    color: #239640;
    font-size: 18px;
}
.receipt-note {
    background: linear-gradient(135deg, #e8f8ed 0%, #d4f1dd 100%);
    border-left: 4px solid #239640;
    padding: 18px;
    border-radius: 12px;
    margin: 15px 0;
}
.receipt-note p { margin: 0; font-size: 14px; color: #1e7e34; }
.receipt-image-label {
    font-weight: 700;
    color: #2e2e2e;
    font-size: 14px;
    margin-bottom: 12px;
}
.receipt-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 20px;
}
.download-receipt-btn,
.print-receipt-btn,
.close-receipt-btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    font-size: 14px;
}
.download-receipt-btn {
    background: linear-gradient(135deg, #239640 0%, #1e7e34 100%);
    color: white;
}
.print-receipt-btn {
    background: linear-gradient(135deg, #ff8c42 0%, #ff7a28 100%);
    color: white;
}
.close-receipt-btn {
    background: #6c757d;
    color: white;
}

.receipt-details p {
    margin: 0;
    font-size: 14px;
    color: #333;
}

.receipt-details strong {
    color: #666;
    font-weight: 600;
}

/* Custom Scrollbar */
.requests-container::-webkit-scrollbar,
.modal-container::-webkit-scrollbar,
.receipt-modal-container::-webkit-scrollbar {
    width: 6px;
}

.requests-container::-webkit-scrollbar-track,
.modal-container::-webkit-scrollbar-track,
.receipt-modal-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb,
.modal-container::-webkit-scrollbar-thumb,
.receipt-modal-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.requests-container::-webkit-scrollbar-thumb:hover,
.modal-container::-webkit-scrollbar-thumb:hover,
.receipt-modal-container::-webkit-scrollbar-thumb:hover {
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
    
    .modal-container,
    .receipt-modal-container,
    .confirm-modal-container {
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

    .view-btn {
        width: 100%;
    }
}

/* Success Modal Styles */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.success-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.success-modal-header {
    background: white;
    padding: 30px 25px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.success-icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 15px;
}

.success-icon {
    width: 120px;
    height: 120px;
    color: #239640;
    background: transparent;
    border-radius: 50%;
    padding: 0;
}

.success-modal-title {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #239640;
}

.success-modal-body {
    padding: 30px 25px;
    text-align: center;
}

.success-message {
    margin: 0;
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.success-modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
    background: #f8f9fa;
}

.success-modal-btn {
    padding: 12px 40px;
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

.success-modal-btn:hover {
    background: #ff7a28;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

/* Error Modal Styles */
.error-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.error-modal-header {
    background: white;
    padding: 30px 25px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.error-icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 15px;
}

.error-icon {
    width: 120px;
    height: 120px;
    color: #dc2626;
    background: transparent;
    border-radius: 50%;
    padding: 0;
}

.error-modal-title {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #dc2626;
}

.error-modal-body {
    padding: 30px 25px;
    text-align: center;
}

.error-message-text {
    margin: 0;
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.error-modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
    background: #f8f9fa;
}

.error-modal-btn {
    padding: 12px 40px;
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

.error-modal-btn:hover {
    background: #ff7a28;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

/* Terms and Conditions Modal Styles */
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

.settings-item {
    text-transform: uppercase;
    white-space: nowrap;
}

.download-receipt-btn {
    display: inline-block;
    text-align: center;
    padding: 12px 24px;
    background: transparent;
    color: #239640;
    border: 2px solid #239640;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.download-receipt-btn:hover {
    background: #239640;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}
</style>