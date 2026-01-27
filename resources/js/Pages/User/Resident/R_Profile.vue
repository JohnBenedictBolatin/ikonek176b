<template>
    <Head>
        <title>Profile</title>
    </Head>

    <!-- Full Screen Layout -->
    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <!-- Settings Dropdown -->
                    <div v-if="showSettings" class="settings-dropdown">
                        <a href="#" class="settings-item" @click.prevent.stop="openTermsModal">TERMS & CONDITIONS</a>
                        <Link href="#" class="settings-item" @click.prevent="logout">SIGN OUT</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card-sidebar">
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
                        class="nav-item active"
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
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-title">
                            <h2>My Profile</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Profile Content Container -->
                    <div class="profile-content-container">
                        <!-- Profile Details Card -->
                        <div class="profile-details-card">
                            <div class="profile-photo-section">
                                <img :src="profilePictureUrl" alt="User Photo" class="profile-photo" />
                                <div class="button-stats-row">
                                    <button class="edit-profile-btn" @click="showEditModal = true">EDIT PROFILE PICTURE</button>
                                </div>
                            </div>
                            <div class="profile-info-section">
                                <div class="profile-header-info">
                                    <div class="profile-name-stats-row">
                                        <div class="profile-name-badge-group">
                                            <h3 class="profile-display-name">{{ user?.name || 'Unknown User' }}</h3>
                                            <div class="profile-badge">{{ displayRole.toUpperCase() }}</div>
                                        </div>
                                        <div class="profile-stats">
                                            <div class="stat-item">
                                                <div class="stat-value">{{ stats?.posts || 0 }}</div>
                                                <div class="stat-label">Posts</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value">{{ stats?.reactions || 0 }}</div>
                                                <div class="stat-label">Reactions</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value">{{ stats?.comments || 0 }}</div>
                                                <div class="stat-label">Comments</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Basic Information -->
                                <div class="user-info-grid" v-if="userInfo">
                                    <div class="info-item" v-if="userInfo.contact_number">
                                        <span class="info-label">Contact:</span>
                                        <span class="info-value">{{ userInfo.contact_number }}</span>
                                    </div>
                                    <div class="info-item" v-if="userInfo.birthdate">
                                        <span class="info-label">Birthdate:</span>
                                        <span class="info-value">{{ userInfo.birthdate }}</span>
                                    </div>
                                    <div class="info-item" v-if="userInfo.sex">
                                        <span class="info-label">Sex:</span>
                                        <span class="info-value">{{ userInfo.sex }}</span>
                                    </div>
                                    <div class="info-item" v-if="userInfo.civil_status">
                                        <span class="info-label">Civil Status:</span>
                                        <span class="info-value">{{ userInfo.civil_status }}</span>
                                    </div>
                                    <div class="info-item" v-if="userInfo.address">
                                        <span class="info-label">Address:</span>
                                        <span class="info-value">{{ userInfo.address }}</span>
                                    </div>
                                </div>
                                
                                <!-- Fallback if no userInfo available -->
                                <p class="profile-description" v-else>
                                    {{ user?.profile_description || 'No description available.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Recent Activities Section -->
                        <!-- Recent Activities Section -->
                            <div class="activities-section">
                                <div class="activities-header">
                                    <div class="activities-title">
                                        <h3>Recent Activities</h3>
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
                                    <div class="filter-dropdown-wrapper">
                                        <button class="filter-dropdown-btn" @click="toggleFilterDropdown">
                                            {{ filterOption.toUpperCase() }}
                                            <span class="filter-arrow" :class="{ rotated: showFilterDropdown }">▼</span>
                                        </button>
                                        <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                            <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                            <button @click="selectFilter('question')" :class="{ active: filterOption === 'question' }">QUESTION</button>
                                            <button @click="selectFilter('suggestion')" :class="{ active: filterOption === 'suggestion' }">SUGGESTION</button>
                                            <button @click="selectFilter('concern')" :class="{ active: filterOption === 'concern' }">CONCERN</button>
                                            <button @click="selectFilter('environment')" :class="{ active: filterOption === 'environment' }">ENVIRONMENT</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Posts -->
                            <div class="activity-posts">
                                <div 
                                    v-for="post in filteredUserPosts" 
                                    :key="post.id"
                                    class="activity-card"
                                    @click="viewPost(post.id)"
                                >
                                    <div class="activity-header-row">
                                        <div class="activity-author">
                                            <img :src="post.avatar" alt="Author Photo" class="activity-avatar" />
                                            <div class="activity-author-info">
                                                <div class="activity-author-name">{{ post.author }}</div>
                                                <div class="activity-tags">
                                                    <span 
                                                        v-for="tag in post.tags" 
                                                        :key="tag"
                                                        class="tag"
                                                        :class="getTagClass(tag)"
                                                    >
                                                        #{{ tag }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="activity-time">
                                            <div class="date">{{ formatDate(post.date) }}</div>
                                            <div class="time">{{ post.time }}</div>
                                        </div>
                                    </div>

                                    <div class="activity-content">
                                        <h4 class="activity-title">{{ post.title }}</h4>
                                        <p class="activity-text">{{ post.content }}</p>
                                    </div>

                                    <div class="activity-reactions">
                                        <button class="reaction-btn" @click.stop="toggleLike(post.id)" :class="{ liked: post.userLiked }">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                            </svg>
                                            {{ post.likes }}
                                        </button>
                                        <button class="reaction-btn" @click.stop="toggleDislike(post.id)" :class="{ disliked: post.userDisliked }">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                            </svg>
                                            {{ post.dislikes }}
                                        </button>
                                        <button class="comment-btn" @click.stop="viewComments(post.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="reaction-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.578-1.087a9.034 9.034 0 0 0 2.422 0Z" />
                                            </svg>
                                            {{ post.comments }}
                                        </button>
                                    </div>
                                </div>

                                <!-- No posts message -->
                                <div v-if="filteredUserPosts.length === 0" class="no-posts">
                                    <p>No posts found matching your criteria.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Picture Edit Modal -->
        <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
            <div class="modal-content edit-profile-modal" @click.stop>
                <button class="close-modal-btn" @click="closeEditModal">✕</button>
                <h3 class="modal-title">Edit Profile Picture</h3>
                
                <div class="modal-profile-preview">
                    <img :src="previewImage || profilePictureUrl" alt="Profile Preview" class="preview-image" />
                </div>
                
                <div class="modal-upload-section">
                    <input 
                        type="file" 
                        ref="fileInput" 
                        @change="handleFileSelect" 
                        accept="image/*" 
                        style="display: none"
                    />
                    <button class="upload-btn-modal" @click="triggerFileInput">
                        {{ pictureForm.profile_picture ? 'CHANGE IMAGE' : 'CHOOSE IMAGE' }}
                    </button>
                    <p v-if="pictureForm.profile_picture" class="file-name">{{ pictureForm.profile_picture.name }}</p>
                </div>
                
                <!-- Error Message Display -->
                <div v-if="profileError" class="error-message-container" style="margin-top: 15px; margin-bottom: 15px;">
                    <div class="error-alert" style="background: #fee; border: 1px solid #fcc; border-radius: 8px; padding: 12px; color: #c33;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span style="font-weight: 600; font-size: 14px;">{{ profileError }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button class="cancel-btn" @click="closeEditModal">CANCEL</button>
                    <button 
                        class="save-btn" 
                        @click="saveProfilePicture"
                        :disabled="!pictureForm.profile_picture || pictureForm.processing || isUploading"
                    >
                        {{ (pictureForm.processing || isUploading) ? 'UPLOADING...' : 'SAVE CHANGES' }}
                    </button>
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
import { Link, usePage, useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
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
  const role = id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
  return role.toUpperCase()
})

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
    // Use a hash of the profile_pic path as version to ensure same image gets same URL
    const version = pic ? pic.split('/').pop() + pic.length : Date.now()
    return url + (url.includes('?') ? '&' : '?') + `v=${version}`
  }
  return '/assets/DEFAULT.jpg'
})

// Helper to get props
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

// Get stats from props
const stats = computed(() => getPageProp('stats') ?? { posts: 0, reactions: 0, comments: 0 })

// Get user info from props
const userInfo = computed(() => getPageProp('userInfo') ?? null)

// File input ref
const fileInput = ref(null)

// Modal state
const showEditModal = ref(false)
const previewImage = ref(null)

// Form for profile picture upload
const pictureForm = useForm({
  profile_picture: null,
})

// Reactive data
const showSettings = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const activeTab = ref('profile')
const sortOption = ref('newest')
const filterOption = ref('all')
const unreadCount = ref(0)

// Error handling state
const profileErrors = ref({})
const profileError = ref('')
const isUploading = ref(false)

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

// Get user posts from props
const userPostsRaw = computed(() => {
  const posts = getPageProp('userPosts') ?? []
  console.log('Raw userPosts from props:', posts)
  return posts
})

// Normalize posts from database
function normalizePost(raw) {
    if (!raw) {
        console.warn('normalizePost: raw is null/undefined')
        return null
    }

    const id = raw.id ?? raw.post_id ?? null
    if (!id) {
        console.warn('normalizePost: Post missing id, raw data:', raw)
        return null
    }
    if (!id) {
        console.warn('Post missing id:', raw)
        return null
    }
    
    const authorName = raw.author ?? user.value?.name ?? 'Unknown'
    const avatar = raw.avatar ?? (user.value?.profile_pic ? (user.value.profile_pic.startsWith('/storage/') ? user.value.profile_pic : `/storage/${user.value.profile_pic}`) : '/assets/DEFAULT.jpg')
    
    // Tags as array of strings
    let tags = []
    if (Array.isArray(raw.tags)) {
        tags = raw.tags.map(t => (typeof t === 'string' ? t : (t.tag_name ?? t.name ?? ''))).filter(Boolean)
    }
    
    // Date/time
    const dateIso = raw.date ?? raw.created_at ?? raw.createdAt ?? null
    const date = dateIso ? new Date(dateIso) : new Date()
    const time = raw.time ?? (dateIso ? new Date(dateIso).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }) : '')
    
    // Title from content
    const title = raw.title ?? (raw.content ? (raw.content.length > 50 ? raw.content.substring(0, 50) + '...' : raw.content) : 'Untitled Post')
    
    // Images
    let images = []
    if (raw.image_content) {
        // Handle both string paths and array of paths
        if (Array.isArray(raw.image_content)) {
            images = raw.image_content.map(img => img.startsWith('http') || img.startsWith('/storage/') ? img : `/storage/${img}`)
        } else {
            images = [raw.image_content.startsWith('http') || raw.image_content.startsWith('/storage/') ? raw.image_content : `/storage/${raw.image_content}`]
        }
    }
    
    return {
        id: id,
        author: authorName,
        avatar: avatar,
        title: title,
        content: raw.content ?? '',
        tags: tags,
        date: date.toISOString(),
        time: time,
        likes: raw.likes ?? 0,
        dislikes: raw.dislikes ?? 0,
        comments: raw.comments ?? 0,
        images: images,
        userLiked: false,
        userDisliked: false
    }
}

// Normalized user posts
const userPosts = computed(() => {
    const raw = userPostsRaw.value
    if (!Array.isArray(raw) || raw.length === 0) {
        console.log('No posts found - raw data:', raw)
        return []
    }
    const normalized = raw.map(normalizePost).filter(Boolean)
    console.log('Normalized user posts:', normalized)
    console.log('Raw posts count:', raw.length)
    console.log('Normalized posts count:', normalized.length)
    return normalized
})

// Computed filtered posts
const filteredUserPosts = computed(() => {
    let filtered = [...userPosts.value]
    
    console.log('Filtering posts:', {
        total: filtered.length,
        filterOption: filterOption.value,
        sortOption: sortOption.value
    })

    if (filterOption.value !== 'all') {
        filtered = filtered.filter(post => {
            const hasMatchingTag = post.tags && post.tags.some(tag => tag.toLowerCase() === filterOption.value.toLowerCase())
            console.log('Post tags:', post.tags, 'matches filter?', hasMatchingTag)
            return hasMatchingTag
        })
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => new Date(b.date) - new Date(a.date))
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => new Date(a.date) - new Date(b.date))
    } else if (sortOption.value === 'relevant') {
        filtered.sort((a, b) => (b.likes + b.comments) - (a.likes + a.comments))
    }

    return filtered
})

// Methods
const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
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

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_resident'))
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

const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile.edit'))
}

const viewPost = (postId) => {
    router.visit(route('discussion_clickpost_resident', { id: postId }))
}

const viewComments = (postId) => {
    router.visit(route('discussion_clickpost_resident', { id: postId }))
}

const toggleLike = (postId) => {
    const post = userPosts.value.find(p => p.id === postId)
    if (post) {
        if (post.userLiked) {
            post.likes--
            post.userLiked = false
        } else {
            if (post.userDisliked) {
                post.dislikes--
                post.userDisliked = false
            }
            post.likes++
            post.userLiked = true
        }
    }
}

const toggleDislike = (postId) => {
    const post = userPosts.value.find(p => p.id === postId)
    if (post) {
        if (post.userDisliked) {
            post.dislikes--
            post.userDisliked = false
        } else {
            if (post.userLiked) {
                post.likes--
                post.userLiked = false
            }
            post.dislikes++
            post.userDisliked = true
        }
    }
}

const openFAQ = () => {
    router.visit(route('help_center_resident'))
}

const formatDate = (date) => {
    if (!date) return ''
    const d = date instanceof Date ? date : new Date(date)
    if (isNaN(d.getTime())) return ''
    return d.toLocaleDateString('en-US', { 
        month: 'long', 
        day: '2-digit', 
        year: 'numeric' 
    })
}

// Tag class helper (same as in announcement/discussion pages)
const getTagClass = (tag) => {
    if (!tag) return ''
    const normalized = String(tag).toLowerCase().trim()
    
    const tagMap = {
        'business': 'business',
        'education': 'education',
        'emergency': 'emergency',
        'employment': 'employment',
        'environment': 'environment',
        'governance': 'governance',
        'health': 'health',
        'incident': 'incident',
        'infrastructure': 'infrastructure',
        'inquiries': 'inquiries',
        'livelihood': 'livelihood',
        'maintenance': 'maintenance',
        'sanitation': 'sanitation',
        'sports': 'sports',
        'traffic': 'traffic',
        'weather': 'weather',
        'welfare': 'welfare',
        'youth': 'youth',
    }
    
    return tagMap[normalized] || ''
}

// Profile picture editing
const triggerFileInput = () => {
    fileInput.value?.click()
}

const handleFileSelect = (event) => {
    const file = event.target.files?.[0]
    if (!file) return
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file.')
        event.target.value = ''
        return
    }
    
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image size must be less than 2MB.')
        event.target.value = ''
        return
    }
    
    pictureForm.profile_picture = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
        previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const saveProfilePicture = () => {
    // Clear previous errors
    profileErrors.value = {}
    profileError.value = ''
    
    if (!pictureForm.profile_picture) {
        profileError.value = 'Please select an image first.'
        return
    }
    
    // Validate file type
    const file = pictureForm.profile_picture
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
    if (!allowedTypes.includes(file.type)) {
        profileError.value = 'Please upload a valid image file (JPEG, PNG, or WebP).'
        return
    }
    
    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024 // 5MB
    if (file.size > maxSize) {
        profileError.value = 'Image size must be less than 5MB. Please compress your image and try again.'
        return
    }
    
    isUploading.value = true
    
    pictureForm.post(route('profile.update_picture'), {
        preserveScroll: true,
        onSuccess: () => {
            // Close modal and refresh to show updated profile picture
            showEditModal.value = false
            previewImage.value = null
            pictureForm.profile_picture = null
            profileError.value = ''
            profileErrors.value = {}
            isUploading.value = false
            // Refresh the page to show updated profile picture everywhere
            router.reload({ only: ['auth'] })
        },
        onError: (errors) => {
            console.error('Error uploading profile picture:', errors)
            isUploading.value = false
            
            // Handle validation errors
            if (errors && typeof errors === 'object') {
                const errorMessages = Object.values(errors).flat()
                profileError.value = errorMessages[0] || 'Failed to upload profile picture. Please try again.'
                profileErrors.value = errors
            } else if (typeof errors === 'string') {
                profileError.value = errors
            } else {
                profileError.value = 'Failed to upload profile picture. Please check the file and try again.'
            }
        },
        onFinish: () => {
            isUploading.value = false
        }
    })
}

const closeEditModal = () => {
    showEditModal.value = false
    previewImage.value = null
    pictureForm.profile_picture = null
    pictureForm.clearErrors()
    profileError.value = ''
    profileErrors.value = {}
    isUploading.value = false
    // Reset file input
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

// Close dropdowns when clicking outside
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
    activeTab.value = 'profile'
    
    // Fetch unread notification count
    fetchUnreadCount()
    
    // Set up polling to update unread count every 30 seconds
    const unreadCountInterval = setInterval(() => {
        fetchUnreadCount()
    }, 30000)
    
    // Store interval ID for cleanup
    window.unreadCountInterval = unreadCountInterval
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
    transition: transform 0.3s ease;
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
    background: #fff7ef;
    color: #ff8c42;
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

.profile-card-sidebar {
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

.faq-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.faq-btn .nav-icon {
    stroke: white;
}


/* Content Area */
.content-area {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.main-content {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
    flex: 1;
    border: 1px solid rgba(0,0,0,0.04);
}

.profile-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 20px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.profile-title h2 {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    position: relative;
    letter-spacing: -0.3px;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

/* Profile Content Container */
.profile-content-container {
    padding: 32px;
    overflow-y: auto;
}

/* Profile Details Card */
.profile-details-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 40px;
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 40px;
    margin-bottom: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
}

.profile-photo-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
    width: 100%;
}

.button-stats-row {
    display: flex;
    align-items: center;
    width: 100%;
    margin-top: 20px;
    justify-content: flex-start;
}

.profile-photo {
    width: 100%;        /* scales with parent container */
    max-width: 200px;  /* doesn't get too big */
    height: auto;      /* keeps aspect ratio */
    border-radius: 20px;
    object-fit: cover;
    border: 3px solid #f5f5f5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.edit-profile-btn {
    background: #ff8c42;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 14px;
    white-space: nowrap;
    flex-shrink: 0;
    width: 100%;
    max-width: 200px;
    height: fit-content;
    box-shadow: 0 2px 4px rgba(255, 140, 66, 0.2);
}

.edit-profile-btn:hover {
    background: #ff7a28;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 140, 66, 0.3);
}

.profile-info-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.profile-header-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 5px;
}

.profile-name-stats-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    width: 100%;
}

.profile-name-badge-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.profile-display-name {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.profile-badge {
    font-size: 11px;
    background: #239640;
    color: white;
    padding: 6px 14px;
    border-radius: 12px;
    display: inline-block;
    width: fit-content;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(35, 150, 64, 0.2);
}

.profile-description {
    font-size: 15px;
    line-height: 1.7;
    color: #555;
    margin: 10px 0;
}

/* User Information Grid */
.user-info-grid {
    background: #fafbfc;
    border-radius: 16px;
    padding: 24px 28px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 24px;
    margin: 16px 0;
    border: 1px solid #f0f0f0;
}

.user-info-grid .info-item {
    display: flex;
    gap: 12px;
    font-size: 14px;
    align-items: flex-start;
    padding: 4px 0;
}

.user-info-grid .info-item:last-child {
    grid-column: 1 / -1;
}

.user-info-grid .info-label {
    font-weight: 600;
    color: #666;
    min-width: 110px;
    flex-shrink: 0;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.user-info-grid .info-value {
    color: #1a1a1a;
    flex: 1;
    font-weight: 500;
}

.profile-stats {
    display: flex;
    gap: 32px;
    align-items: flex-start;
    flex-shrink: 0;
}

.stat-item {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #ff8c42;
    margin-bottom: 4px;
    line-height: 1;
    letter-spacing: -0.5px;
}

.stat-label {
    font-size: 11px;
    color: #888;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Activities Section */
.activities-section {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
}

.activities-header {
    background: white;
    color: #1a1a1a;
    padding: 24px 32px;
    border-bottom: 1px solid #f0f0f0;
}

.activities-title h3 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #1a1a1a;
    letter-spacing: -0.3px;
}

/* Remove or comment out this style since we're removing the subtitle */
.activities-subtitle {
    display: none; /* Hide the subtitle */
}

/* Filter Section */
.filter-section {
    padding: 20px 32px;
    background: #fafbfc;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
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
    padding: 10px 16px;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    background: white;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 130px;
    justify-content: space-between;
    color: #666;
}

.filter-dropdown-btn:hover {
    border-color: #ff8c42;
    background: #fff;
    color: #333;
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
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08);
    min-width: 150px;
    z-index: 1000;
    margin-top: 8px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.06);
}

.filter-dropdown-menu button {
    display: block;
    width: 100%;
    padding: 12px 16px;
    background: none;
    border: none;
    text-align: left;
    color: #333;
    cursor: pointer;
    transition: all 0.15s ease;
    font-weight: 500;
    font-size: 13px;
}

.filter-dropdown-menu button:hover {
    background: #fafbfc;
    color: #ff8c42;
}

.filter-dropdown-menu button.active {
    background: #fff7ef;
    color: #ff8c42;
    font-weight: 600;
}

/* Activity Posts */
.activity-posts {
    padding: 0;
}

.activity-card {
    padding: 28px 32px;
    border-bottom: 1px solid #f5f5f5;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.activity-card:hover {
    background: #fafbfc;
    padding-left: 36px;
}

.activity-card:last-child {
    border-bottom: none;
}

.activity-header-row {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.activity-author {
    display: flex;
    align-items: start;
    gap: 15px;
}

.activity-avatar {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    object-fit: cover;
    border: 2px solid #f5f5f5;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
}

.activity-author-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.activity-author-name {
    font-weight: 600;
    font-size: 15px;
    color: #1a1a1a;
    letter-spacing: -0.2px;
}

.activity-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.tag {
    font-size: 10px;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
}

.tag.question {
    background: linear-gradient(135deg, #ff9800, #f57c00);
}

.tag.suggestion {
    background: linear-gradient(135deg, #273cb0, #273cb0);
}

.tag.concern {
    background: linear-gradient(135deg, #ff0303, #ff0303);
}

.tag.environment {
    background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
}

/* Business - Blue/Purple */
.tag.business {
    background: linear-gradient(135deg, #6c5ce7, #5f3dc4) !important;
}

/* Education - Blue */
.tag.education {
    background: linear-gradient(135deg, #3498db, #2980b9) !important;
}

/* Emergency - Red */
.tag.emergency {
    background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
}

/* Employment - Green */
.tag.employment {
    background: linear-gradient(135deg, #27ae60, #229954) !important;
}

/* Governance - Purple */
.tag.governance {
    background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
}

/* Health - Red/Pink */
.tag.health {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Incident - Dark Red */
.tag.incident {
    background: linear-gradient(135deg, #c0392b, #a93226) !important;
}

/* Infrastructure - Orange */
.tag.infrastructure {
    background: linear-gradient(135deg, #f39c12, #e67e22) !important;
}

/* Inquiries - Yellow/Orange */
.tag.inquiries {
    background: linear-gradient(135deg, #f1c40f, #f39c12) !important;
}

/* Livelihood - Teal */
.tag.livelihood {
    background: linear-gradient(135deg, #1abc9c, #16a085) !important;
}

/* Maintenance - Brown/Orange */
.tag.maintenance {
    background: linear-gradient(135deg, #d35400, #ba4a00) !important;
}

/* Sanitation - Cyan */
.tag.sanitation {
    background: linear-gradient(135deg, #00bcd4, #0097a7) !important;
}

/* Sports - Green */
.tag.sports {
    background: linear-gradient(135deg, #4caf50, #388e3c) !important;
}

/* Traffic - Yellow */
.tag.traffic {
    background: linear-gradient(135deg, #ffc107, #ff9800) !important;
}

/* Weather - Light Blue */
.tag.weather {
    background: linear-gradient(135deg, #03a9f4, #0288d1) !important;
}

/* Welfare - Pink */
.tag.welfare {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Youth - Magenta */
.tag.youth {
    background: linear-gradient(135deg, #e91e63, #ad1457) !important;
}

.activity-time {
    text-align: right;
    font-size: 12px;
    color: #666;
    font-weight: 500;
}

.date {
    font-weight: 600;
}

.time {
    color: #999;
    margin-top: 2px;
}

.activity-content {
    margin: 15px 0;
}

.activity-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 12px 0;
    color: #1a1a1a;
    line-height: 1.5;
    letter-spacing: -0.2px;
}

.activity-text {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin: 0;
}

.activity-reactions {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #f5f5f5;
}

.reaction-btn,
.comment-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    color: #666;
    padding: 10px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.reaction-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    flex-shrink: 0;
}

.reaction-btn:hover,
.comment-btn:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    color: #333;
}

.reaction-btn.liked {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    color: white;
    box-shadow: 0 4px 15px rgba(74, 222, 128, 0.4);
}

.reaction-btn.liked:hover {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 222, 128, 0.5);
}

.reaction-btn.disliked {
    background: linear-gradient(135deg, #f87171, #ef4444);
    color: white;
    box-shadow: 0 4px 15px rgba(248, 113, 113, 0.4);
}

.reaction-btn.disliked:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(248, 113, 113, 0.5);
}


.no-posts {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.no-posts p {
    font-size: 16px;
    color: #999;
}

/* Custom Scrollbar */
.profile-content-container::-webkit-scrollbar {
    width: 6px;
}

.profile-content-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.profile-content-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.profile-content-container::-webkit-scrollbar-thumb:hover {
    background: #666;
}

/* Responsive */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
        margin-top: 70px;
    }
    
    .profile-details-card {
        grid-template-columns: 150px 1fr;
        gap: 20px;
        padding: 25px;
    }
    
    .profile-photo {
        width: 200px;
        height: 150px;
    }
}

@media (max-width: 768px) {
    .main-layout {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
        margin-top: 70px;
    }
    
    .sidebar {
        order: 2;
        padding-right: 0;
    }
    
    .profile-details-card {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 20px;
    }
    
    .profile-photo-section {
        align-items: center;
    }
    
    .profile-photo {
        max-width: 100%;
    }
    
    .profile-badge {
        margin: 0 auto;
    }
    
    .button-stats-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .edit-profile-btn {
        width: 100%;
    }
    
    .profile-name-stats-row {
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }
    
    .profile-name-badge-group {
        align-items: center;
        text-align: center;
    }
    
    .profile-stats {
        width: 100%;
        justify-content: center;
    }
    
    .filter-section {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .activity-header-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .activity-time {
        text-align: left;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
        margin-top: 70px;
    }
    
    .profile-details-card {
        padding: 15px;
    }
    
    .profile-display-name {
        font-size: 22px;
    }
    
    .user-info-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .user-info-grid .info-item:last-child {
        grid-column: 1;
    }
    
    .profile-stats {
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .activity-card {
        padding: 20px 15px;
    }
    
    .activity-reactions {
        flex-wrap: wrap;
    }
}

/* Profile Picture Edit Modal */
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

.edit-profile-modal {
    padding: 40px 30px;
    text-align: center;
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

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
    text-align: center;
}

.modal-profile-preview {
    margin: 20px 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.preview-image {
    width: 200px;
    height: 200px;
    border-radius: 15px;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border: 3px solid #ff8c42;
}

.modal-upload-section {
    margin: 25px 0;
}

.upload-btn-modal {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.upload-btn-modal:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.file-name {
    margin-top: 10px;
    font-size: 13px;
    color: #666;
    font-style: italic;
}

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.cancel-btn,
.save-btn {
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.cancel-btn {
    background: white;
    border: 1px solid #e0e0e0;
    color: #4a4a4a;
}

.cancel-btn:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
    transform: translateY(-2px);
}

.save-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}

.save-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(43, 178, 74, 0.4);
}

.save-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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