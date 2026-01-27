<template>
    <Head>
        <title>Add Post</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <div v-if="showSettings" class="settings-dropdown">
                        <a href="#" class="settings-item" @click.prevent.stop="openTerms">TERMS & CONDITIONS</a>
                        <Link href="#" class="settings-item" @click="logout">SIGN OUT</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
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
                        @click="setActiveTab('posts')"
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
                        @click="setActiveTab('events')"
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
                        @click="setActiveTab('notifications')"
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
                        @click="setActiveTab('profile')"
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
                    <!-- Discussions Header -->
                    <div class="discussions-header">
                        <div class="discussions-title">
                            <h2>Discussions</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Add Post Section -->
                    <div class="add-post-section">
                        <button class="back-btn" @click="backToPosts">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            BACK TO POSTS
                        </button>

                        <button class="add-tags-btn" @click="openTagsModal">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            ADD TAGS
                        </button>

                        <!-- Post Input Area -->
                        <div class="post-input-container">
                            <input 
                                v-model="postHeader"
                                type="text"
                                placeholder="Add a post header (optional)..."
                                class="post-header-input"
                                maxlength="255"
                            />
                            <textarea 
                                v-model="postContent"
                                placeholder="Write a post..."
                                class="post-textarea"
                                @input="updateCharCount"
                            ></textarea>

                            <div class="post-actions">
                                <button class="attach-btn" @click="triggerFileUpload">
                                    ATTACH
                                </button>
                                <span class="char-count">{{ charCount }}/250</span>
                                <input 
                                    type="file" 
                                    ref="fileInput" 
                                    @change="handleFileUpload" 
                                    class="file-input-hidden"
                                    accept="image/*"
                                    multiple
                                />
                            </div>

                            <!-- Uploaded Files Preview -->
                            <div v-if="uploadedFiles.length > 0" class="uploaded-files">
                                <div 
                                    v-for="(file, index) in uploadedFiles" 
                                    :key="index"
                                    class="file-preview"
                                >
                                    <img :src="file.preview" alt="Preview" class="file-preview-img" />
                                    <button class="remove-file-btn" @click="removeFile(index)">✕</button>
                                </div>
                            </div>
                        </div>

                        <!-- Poll Section -->
                        <div class="poll-section">
                            <label class="poll-toggle">
                                <input 
                                    type="checkbox" 
                                    v-model="isPoll"
                                    @change="togglePoll"
                                />
                                <span class="poll-toggle-label">Create a Poll</span>
                            </label>
                            
                            <div v-if="isPoll" class="poll-options-container">
                                <div class="poll-options-header">
                                    <span class="poll-options-label">Poll Options (minimum 2, maximum 10)</span>
                                    <button 
                                        v-if="pollOptions.length < 10"
                                        type="button"
                                        class="add-poll-option-btn"
                                        @click="addPollOption"
                                    >
                                        + Add Option
                                    </button>
                                </div>
                                
                                <div 
                                    v-for="(option, index) in pollOptions" 
                                    :key="index"
                                    class="poll-option-input-wrapper"
                                >
                                    <input 
                                        v-model="pollOptions[index]"
                                        type="text"
                                        :placeholder="`Option ${index + 1}`"
                                        class="poll-option-input"
                                        maxlength="255"
                                    />
                                    <button 
                                        v-if="pollOptions.length > 2"
                                        type="button"
                                        class="remove-poll-option-btn"
                                        @click="removePollOption(index)"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tags Selection -->
                        <div class="tags-section">
                            <span class="tags-label">TAGS:</span>
                            <div class="selected-tags">
                                <span 
                                    v-for="tag in selectedTagsData" 
                                    :key="tag.tag_id"
                                    class="tag-chip"
                                    :class="getTagClass(tag.tag_name)"
                                >
                                    #{{ tag.tag_name }}
                                    <button class="remove-tag-btn" @click="removeTag(tag.tag_id)">⊖</button>
                                </span>
                                <span v-if="selectedTagsData.length === 0" class="no-tags-text">
                                    No tags selected
                                </span>
                            </div>
                        </div>

                        

                        <!-- Error Message Display -->
                        <div v-if="submitError" class="error-message-container" style="margin-top: 15px; margin-bottom: 15px;">
                            <div class="error-alert" style="background: #fee; border: 1px solid #fcc; border-radius: 8px; padding: 12px; color: #c33;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span style="font-weight: 600; font-size: 14px;">{{ submitError }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Field Error Messages -->
                        <div v-if="Object.keys(formErrors).length > 0" class="field-errors-container" style="margin-bottom: 15px;">
                            <div v-for="(error, field) in formErrors" :key="field" class="field-error" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 10px; margin-bottom: 8px; color: #856404; font-size: 14px;">
                                <strong>{{ field.replace('_', ' ') }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
                            </div>
                        </div>

                        <!-- Publish Button -->
                        <button class="publish-btn" @click="publishPost" :disabled="form.processing">
                            {{ form.processing ? 'PUBLISHING...' : 'PUBLISH' }}
                        </button>

                        <!-- Drafts Section -->
                        <div class="drafts-section">
                            <h3 class="drafts-title">DRAFTS</h3>
                            
                            <div 
                                v-for="draft in drafts" 
                                :key="draft.id"
                                class="draft-card"
                            >
                                <div class="draft-content">
                                    <h4 class="draft-text">{{ draft.title }}</h4>
                                    <p class="draft-preview">{{ draft.content }}</p>
                                </div>
                                <div class="draft-footer">
                                    <span class="draft-date">{{ draft.date }}</span>
                                    <button class="delete-draft-btn" @click="deleteDraft(draft.id)">🗑</button>
                                </div>
                            </div>

                            <div v-if="drafts.length === 0" class="no-drafts">
                                <p>No drafts available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tags Modal Popup -->
        <div v-if="showTagsModal" class="modal-overlay" @click="closeTagsModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Select Tags</h3>
                    <button class="modal-close-btn" @click="closeTagsModal">✕</button>
                </div>
                <div class="modal-body">
                    <div v-if="availableTags && availableTags.length > 0" class="tags-grid">
                        <button 
                            v-for="tag in availableTags" 
                            :key="tag.tag_id"
                            class="tag-option"
                            :class="{ selected: isTagSelected(tag.tag_id) }"
                            @click="toggleTag(tag)"
                        >
                            <span class="tag-checkbox">
                                {{ isTagSelected(tag.tag_id) ? '✓' : '' }}
                            </span>
                            {{ tag.tag_name }}
                        </button>
                    </div>
                    <div v-else class="no-tags-available">
                        <p>No tags available. Please contact administrator.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="modal-cancel-btn" @click="closeTagsModal">CANCEL</button>
                    <button class="modal-confirm-btn" @click="confirmTags">CONFIRM</button>
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

    <!-- Success Confirmation Modal -->
    <div v-if="showSuccessModal" class="modal-overlay success-modal-overlay" @click.stop>
        <div class="success-modal" @click.stop>
            <div class="success-modal-header">
                <div class="success-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="success-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="success-modal-title">
                    {{ publishedPostIsPoll ? 'Poll Published Successfully!' : 'Post Published Successfully!' }}
                </h3>
            </div>
            <div class="success-modal-body">
                <p class="success-message" v-if="publishedPostIsPoll">
                    Your poll has been successfully published and is now visible on the discussions page. 
                    Other residents and barangay officials can now view your poll, vote on the options, react, and comment. 
                    You can track the poll results in real-time as community members participate. 
                    Thank you for engaging the community with your poll!
                </p>
                <p class="success-message" v-else>
                    Your post has been successfully published and is now visible on the discussions page. 
                    Other residents and barangay officials can now view, react, and comment on your post. 
                    Thank you for contributing to the community discussions!
                </p>
            </div>
            <div class="success-modal-footer">
                <button @click="closeSuccessModal" class="success-modal-btn">
                    OK
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    availableTags: {
        type: Array,
        default: () => []
    }
})

// Get page props
const page = usePage()

// SAFE user access - prioritize prop, fallback to page.props
const user = computed(() => {
    // First try from props
    if (props.auth?.user) {
        return props.auth.user
    }
    
    // Then try from page props
    const authUser = page?.props?.auth?.user
    
    // Return default if undefined
    if (!authUser) {
        return {
            user_id: null,
            name: 'Guest',
            avatar: '/assets/DEFAULT.jpg',
            role: 'Employee',
            fk_role_id: 2
        }
    }
    
    return authUser
})

// Map of role_id -> role_name
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

// Computed display role
const displayRole = computed(() => {
    const id = user.value?.fk_role_id ?? 2
    const role = roleMap[id] ?? 'Resident'
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
    const version = pic ? pic.split('/').pop() + pic.length : Date.now()
    return url + (url.includes('?') ? '&' : '?') + `v=${version}`
  }
  return '/assets/DEFAULT.jpg'
})

const showSettings = ref(false)
const activeTab = ref('posts')
const postHeader = ref('')
const postContent = ref('')
const charCount = ref(0)
const selectedTagIds = ref([])
const showTagsModal = ref(false)
const uploadedFiles = ref([])
const fileInput = ref(null)
const isPoll = ref(false)
const pollOptions = ref(['', ''])

const drafts = ref([])

// Error handling state
const formErrors = ref({})
const submitError = ref('')

// Success modal state
const showSuccessModal = ref(false)
const publishedPostIsPoll = ref(false)

// computed to map selected IDs to tag objects
const selectedTagsData = computed(() => {
    if (!props.availableTags || !Array.isArray(props.availableTags)) return []
    return props.availableTags.filter(tag => selectedTagIds.value.includes(tag.tag_id))
})

const form = useForm({
    header: '',
    content: '',
    tag_ids: [],      // will be an array of tag ids
    image: null,
    video_path: null,
    is_poll: false,
    poll_options: [],
    status: 'published',
})

// Helper function to normalize tag names for CSS classes
const getTagClass = (tagName) => {
    if (!tagName) return ''
    // Convert to lowercase and replace spaces/special chars with nothing
    let normalized = String(tagName).toLowerCase().trim().replace(/\s+/g, '').replace(/[^a-z0-9]/g, '')
    
    // Map to specific tag classes
    if (normalized === 'business') return 'business'
    if (normalized === 'education') return 'education'
    if (normalized === 'emergency') return 'emergency'
    if (normalized === 'employment') return 'employment'
    if (normalized === 'environment' || normalized === 'env') return 'environment'
    if (normalized === 'governance') return 'governance'
    if (normalized === 'health' || normalized === 'medical') return 'health'
    if (normalized === 'incident') return 'incident'
    if (normalized === 'infrastructure') return 'infrastructure'
    if (normalized === 'inquiries' || normalized === 'inquiry') return 'inquiries'
    if (normalized === 'livelihood') return 'livelihood'
    if (normalized === 'maintenance') return 'maintenance'
    if (normalized === 'sanitation') return 'sanitation'
    if (normalized === 'sports') return 'sports'
    if (normalized === 'traffic') return 'traffic'
    if (normalized === 'weather') return 'weather'
    if (normalized === 'welfare') return 'welfare'
    if (normalized === 'youth') return 'youth'
    
    return normalized
}

const toggleSettings = () => showSettings.value = !showSettings.value
const closeSettings = () => showSettings.value = false

// Terms & Conditions modal
const showTermsModal = ref(false)
const openTerms = (e) => {
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
const setActiveTab = (tab) => activeTab.value = tab
const navigateToDocuments = () => { activeTab.value = 'documents'; router.visit(route('document_request_select_resident')) }
const backToPosts = () => router.visit(route('discussion_resident'))

const openTagsModal = () => { showTagsModal.value = true }
const closeTagsModal = () => { showTagsModal.value = false }

const isTagSelected = (tagId) => selectedTagIds.value.includes(tagId)

const toggleTag = (tag) => {
    const idx = selectedTagIds.value.indexOf(tag.tag_id)
    if (idx > -1) selectedTagIds.value.splice(idx, 1)
    else selectedTagIds.value.push(tag.tag_id)
}

const removeTag = (tagId) => {
    const idx = selectedTagIds.value.indexOf(tagId)
    if (idx > -1) selectedTagIds.value.splice(idx, 1)
}

const confirmTags = () => {
    // just hide modal — selectedTagIds already updated by toggleTag
    closeTagsModal()
}

const updateCharCount = () => charCount.value = postContent.value.length
const triggerFileUpload = () => fileInput.value && fileInput.value.click()

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files || [])
    files.forEach(file => {
        const reader = new FileReader()
        reader.onload = (e) => {
            uploadedFiles.value.push({ file, preview: e.target.result })
        }
        reader.readAsDataURL(file)
    })
}

const removeFile = (index) => uploadedFiles.value.splice(index, 1)

const togglePoll = () => {
    if (!isPoll.value) {
        pollOptions.value = ['', '']
    }
}

const addPollOption = () => {
    if (pollOptions.value.length < 10) {
        pollOptions.value.push('')
    }
}

const removePollOption = (index) => {
    if (pollOptions.value.length > 2) {
        pollOptions.value.splice(index, 1)
    }
}

const publishPost = () => {
    // Clear previous errors
    formErrors.value = {}
    submitError.value = ''
    
    if (!postContent.value.trim()) {
        submitError.value = 'Please write something before publishing.'
        return
    }
    if (selectedTagIds.value.length === 0) {
        alert('Please select at least one tag')
        return
    }
    
    // Validate poll if it's enabled
    if (isPoll.value) {
        const validOptions = pollOptions.value.filter(opt => opt.trim().length > 0)
        if (validOptions.length < 2) {
            alert('Please provide at least 2 poll options')
            return
        }
    }

    // prepare form
    form.clearErrors()
    form.header = postHeader.value.trim()
    form.content = postContent.value
    // Ensure tag_ids is always an array, even if empty (though validation should prevent empty)
    form.tag_ids = Array.isArray(selectedTagIds.value) ? selectedTagIds.value.slice() : []
    form.is_poll = isPoll.value
    form.poll_options = isPoll.value ? pollOptions.value.filter(opt => opt.trim().length > 0) : []

    // include first file if available (you can change to send all files)
    if (uploadedFiles.value.length > 0) {
        form.image = uploadedFiles.value[0].file
    } else {
        form.image = null
    }

    form.post(route('posts.store'), {
        preserveState: false,
        preserveScroll: false,
        onStart: () => console.log('Posting...'),
        onSuccess: (page) => {
            console.log('Post published successfully')
            // Track if this was a poll post
            publishedPostIsPoll.value = isPoll.value && pollOptions.value.filter(opt => opt.trim().length > 0).length >= 2
            // Show success modal instead of redirecting immediately
            showSuccessModal.value = true
            // Prevent body scrolling while modal is open
            document.body.style.overflow = 'hidden'
        },
        onError: (errors) => {
            console.error('Server validation errors', errors)
            console.error('Form data sent:', {
                header: form.header,
                content: form.content,
                tag_ids: form.tag_ids,
                is_poll: form.is_poll,
                poll_options: form.poll_options
            })
            // Handle validation errors
            if (errors && typeof errors === 'object') {
                formErrors.value = errors
                // Get the first error message from any field
                const errorMessages = Object.values(errors).flat()
                const firstError = errorMessages.length > 0 ? errorMessages[0] : 'Failed to publish post. Please check the form and try again.'
                submitError.value = firstError
                
                // If tag_ids validation failed, show a more specific message
                if (errors.tag_ids) {
                    submitError.value = Array.isArray(errors.tag_ids) ? errors.tag_ids[0] : 'Please select at least one tag before publishing.'
                }
                
                // Scroll to error
                setTimeout(() => {
                    const errorContainer = document.querySelector('.error-message-container')
                    if (errorContainer) {
                        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' })
                    }
                }, 100)
            } else {
                submitError.value = 'An error occurred while publishing your post. Please try again.'
            }
        }
    })
}

const deleteDraft = (draftId) => {
    const idx = drafts.value.findIndex(d => d.id === draftId)
    if (idx > -1) drafts.value.splice(idx, 1)
}

const closeSuccessModal = () => {
    showSuccessModal.value = false
    publishedPostIsPoll.value = false
    document.body.style.overflow = ''
    // Reset form state
    postHeader.value = ''
    postContent.value = ''
    charCount.value = 0
    selectedTagIds.value = []
    uploadedFiles.value = []
    isPoll.value = false
    pollOptions.value = ['', '']
    form.reset()
    formErrors.value = {}
    submitError.value = ''
    // Redirect to discussion page after closing modal
    setTimeout(() => {
        router.visit(route('discussion_resident'), {
            replace: true,
            preserveState: false,
            preserveScroll: false
        })
    }, 300)
}

const openFAQ = () => console.log('Opening FAQ')

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) showSettings.value = false
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'posts'
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    // Restore body overflow in case modal was open
    document.body.style.overflow = ''
})
</script>

<style scoped>
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
    z-index: 9999;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
}

.modal-header {
    background: white;
    color: #333;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #333;
}

.modal-close-btn {
    background: none;
    border: none;
    color: #666;
    font-size: 24px;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s;
}

.modal-close-btn:hover {
    background: #f8f9fa;
    color: #333;
}

.modal-body {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
}

.tags-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}


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
    background:#239640;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    backdrop-filter: blur(10px);
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

.discussions-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.discussions-title h2 {
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

.add-post-section {
    padding: 30px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    position: relative;
}

.back-btn {
    color: #000;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    background: transparent;
}

.back-btn:hover {
    transform: translateY(-2px);
}

.nav-icon {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    flex-shrink: 0;
}

.add-tags-btn {
    position: absolute;
    top: 30px;
    right: 50px;
    background: transparent;
    color: #000;
    border: 1px solid #e0e0e0;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.add-tags-btn:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
    transform: translateY(-2px);
}


.post-input-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.post-header-input {
    width: 100%;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: white;
    margin-bottom: 15px;
    transition: border-color 0.2s;
}

.post-header-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.post-textarea {
    width: 100%;
    min-height: 150px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    resize: vertical;
    background: white;
    margin-bottom: 15px;
    transition: border-color 0.2s;
}

.post-textarea:focus {
    outline: none;
    border-color: #ff8c42;
}

.post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.attach-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}


.char-count {
    font-size: 13px;
    color: #999;
    font-weight: 600;
}

.file-input-hidden {
    display: none;
}

.uploaded-files {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.file-preview {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.file-preview-img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.remove-file-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
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
}

.remove-file-btn:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.tags-section {
    position: relative;
    margin-bottom: 25px;
}

.tags-label {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-right: 15px;
}

.selected-tags {
    display: inline-flex;
    gap: 10px;
    flex-wrap: wrap;
}

.tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    /* Default background for unmatched tags */
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

.tag-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Business - Blue/Purple */
.tag-chip.business {
    background: linear-gradient(135deg, #6c5ce7, #5f3dc4) !important;
}

/* Education - Blue */
.tag-chip.education {
    background: linear-gradient(135deg, #3498db, #2980b9) !important;
}

/* Emergency - Red */
.tag-chip.emergency {
    background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
}

/* Employment - Green */
.tag-chip.employment {
    background: linear-gradient(135deg, #27ae60, #229954) !important;
}

/* Environment - Green */
.tag-chip.environment {
    background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
}

/* Governance - Purple */
.tag-chip.governance {
    background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
}

/* Health - Red/Pink */
.tag-chip.health {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Incident - Dark Red */
.tag-chip.incident {
    background: linear-gradient(135deg, #c0392b, #a93226) !important;
}

/* Infrastructure - Orange */
.tag-chip.infrastructure {
    background: linear-gradient(135deg, #f39c12, #e67e22) !important;
}

/* Inquiries - Yellow/Orange */
.tag-chip.inquiries {
    background: linear-gradient(135deg, #f1c40f, #f39c12) !important;
}

/* Livelihood - Teal */
.tag-chip.livelihood {
    background: linear-gradient(135deg, #1abc9c, #16a085) !important;
}

/* Maintenance - Brown/Orange */
.tag-chip.maintenance {
    background: linear-gradient(135deg, #d35400, #ba4a00) !important;
}

/* Sanitation - Cyan */
.tag-chip.sanitation {
    background: linear-gradient(135deg, #00bcd4, #0097a7) !important;
}

/* Sports - Green */
.tag-chip.sports {
    background: linear-gradient(135deg, #4caf50, #388e3c) !important;
}

/* Traffic - Yellow */
.tag-chip.traffic {
    background: linear-gradient(135deg, #ffc107, #ff9800) !important;
}

/* Weather - Light Blue */
.tag-chip.weather {
    background: linear-gradient(135deg, #03a9f4, #0288d1) !important;
}

/* Welfare - Pink */
.tag-chip.welfare {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Youth - Magenta */
.tag-chip.youth {
    background: linear-gradient(135deg, #e91e63, #ad1457) !important;
}

.remove-tag-btn {
    border: none;
    color: white;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}


.tags-dropdown {
    position: absolute;
    top: 100%;
    left: 50px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 10px;
    z-index: 100;
    margin-top: 10px;
    border: 1px solid rgba(0,0,0,0.1);
}

.tag-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 600;
    text-align: left;
}

.tag-option:hover {
    background: #fff7ef;
    border-color: #ff8c42;
}

.tag-option.selected {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    border-color: #ff8c42;
    color: #ff8c42;
}

.tag-checkbox {
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ff8c42;
}

.tag-option.selected .tag-checkbox {
    border-color: #ff8c42;
    background: #ff8c42;
    color: white;
}

.no-tags-available {
    text-align: center;
    padding: 40px;
    color: #999;
}

.modal-footer {
    padding: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #e0e0e0;
}

.modal-cancel-btn,
.modal-confirm-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.modal-cancel-btn {
    background: white;
    border: 1px solid #e0e0e0;
    color: #4a4a4a;
}

.modal-cancel-btn:hover {
    background: #f8f9fa;
    border-color: #d0d0d0;
}

.modal-confirm-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
}

.modal-confirm-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}



.publish-btn {
    width: auto;
    min-width: 200px;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 40px;
    margin-left: auto;
    display: block;
}


.drafts-section {
    margin-top: 40px;
}

.drafts-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
}

.draft-card {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.2);
    border: 2px solid #4caf50;
}

.draft-content {
    margin-bottom: 15px;
}

.draft-text {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.draft-preview {
    font-size: 14px;
    color: #555;
    line-height: 1.6;
}

.draft-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.draft-date {
    font-size: 13px;
    color: #4caf50;
    font-weight: 600;
}

.delete-draft-btn {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #dc3545;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
}


.no-drafts {
    text-align: center;
    padding: 40px;
    color: #999;
    font-size: 14px;
}

.add-post-section::-webkit-scrollbar {
    width: 6px;
}

.add-post-section::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.add-post-section::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.add-post-section::-webkit-scrollbar-thumb:hover {
    background: #666;
}

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

    .add-tags-btn {
        position: static;
        width: 100%;
        margin-bottom: 20px;
    }

    .uploaded-files {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .add-post-section {
        padding: 20px 15px;
    }

    .profile-card {
        padding: 15px;
    }
}

/* Global scrollbar styling */
body::-webkit-scrollbar,
html::-webkit-scrollbar {
    width: 8px;
}

body::-webkit-scrollbar-track,
html::-webkit-scrollbar-track {
    background: #f1f1f1;
}

body::-webkit-scrollbar-thumb,
html::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

body::-webkit-scrollbar-thumb:hover,
html::-webkit-scrollbar-thumb:hover {
    background: #666;
}

/* Poll Section Styles */
.poll-section {
    margin-bottom: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
}

.poll-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    margin-bottom: 15px;
}

.poll-toggle input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #ff8c42;
}

.poll-toggle-label {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    cursor: pointer;
}

.poll-options-container {
    margin-top: 15px;
}

.poll-options-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.poll-options-label {
    font-size: 14px;
    font-weight: 600;
    color: #666;
}

.add-poll-option-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.add-poll-option-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}

.poll-option-input-wrapper {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
}

.poll-option-input {
    flex: 1;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: border-color 0.2s;
}

.poll-option-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.remove-poll-option-btn {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #dc3545;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 700;
    transition: all 0.2s;
    min-width: 40px;
}

.remove-poll-option-btn:hover {
    background: rgba(220, 53, 69, 0.2);
    border-color: #dc3545;
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

/* Success Modal Styles */
.success-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10002;
    padding: 20px;
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

.success-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 550px;
    width: 90%;
    overflow: hidden;
    animation: slideUp 0.3s ease;
    position: relative;
    z-index: 10003;
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
</style>