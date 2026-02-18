<template>
    <Head>
        <title>Help Center</title>
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

                <button class="faq-btn active">
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
                    <div class="help-header">
                        <h2>Help Center</h2>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <div class="help-content">
                        <!-- Two Column Layout -->
                        <div class="help-grid">
                            <!-- Left Column: FAQs -->
                            <div class="faqs-section">
                                <h3 class="section-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                    </svg>
                                    FAQs
                                </h3>
                                <div class="faqs-list">
                                    <div 
                                        v-for="(faq, index) in faqs" 
                                        :key="index"
                                        class="faq-item"
                                    >
                                        <button
                                            @click="toggleFAQ(index)"
                                            class="faq-question"
                                        >
                                            <span>{{ faq.question }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="faq-arrow" :class="{ rotated: faq.open }">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <div v-if="faq.open" class="faq-answer">
                                            {{ faq.answer }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Contact and Message -->
                            <div class="contact-message-column">
                                <!-- Contact Us -->
                                <div class="contact-section">
                                    <h3 class="section-title">Contact Us</h3>
                                    <div class="contact-info">
                                        <div class="contact-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="contact-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                            </svg>
                                            <span>+639193076338</span>
                                        </div>
                                        <div class="contact-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="contact-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                            </svg>
                                            <span>ikonek176b@dev.ph</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Send Message -->
                                <div class="message-section">
                                    <h3 class="section-title">Send us a message</h3>
                                    <div v-if="submitError" class="error-message" style="color: #dc3545; font-size: 14px; margin-bottom: 12px; padding: 10px; background-color: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb;">
                                        {{ submitError }}
                                    </div>
                                    <textarea
                                        v-model="message"
                                        @input="submitError = ''"
                                        placeholder="Add your message here..."
                                        class="message-input"
                                        rows="4"
                                    ></textarea>
                                    <button @click="submitMessage" class="submit-btn">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
import { Link, usePage } from '@inertiajs/vue3'
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
  6: 'SK Chairman',
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
    const version = pic ? pic.split('/').pop() + pic.length : Date.now()
    return url + (url.includes('?') ? '&' : '?') + `v=${version}`
  }
  return '/assets/DEFAULT.jpg'
})

const showSettings = ref(false)
const activeTab = ref('help')
const message = ref('')
const submitError = ref('')
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

const faqs = ref([
    {
        question: 'Paano ako makakakuha ng barangay clearance o certificate?',
        answer: 'Pumunta sa "Document Request" section sa inyong dashboard. Piliin ang "Barangay Clearance" o ang certificate na kailangan ninyo. Fill-up ang form at i-upload ang mga required documents. Karaniwan ay 2-3 araw bago ma-approve ang request. Makakakuha kayo ng notification kapag ready na ang inyong document.',
        open: false,
    },
    {
        question: 'Ano ang mga requirements para sa document request?',
        answer: 'Ang requirements ay depende sa type ng document na hinihingi. Karaniwang kailangan ay valid ID, proof of residency, at iba pang supporting documents. Makikita ang complete list ng requirements sa document request form. Siguraduhing kumpleto at valid ang lahat ng documents bago mag-submit.',
        open: false,
    },
    {
        question: 'Paano ko malalaman kung approved o rejected ang aking document request?',
        answer: 'Makikita ninyo ang status ng inyong request sa "Notifications" tab. May notification din kayo kapag may update sa inyong request. Kung rejected, makikita ninyo ang reason kung bakit at pwede ninyong i-resubmit pagkatapos i-correct ang issues.',
        open: false,
    },
    {
        question: 'Paano ako makakapag-request ng event assistance?',
        answer: 'Pumunta sa "Event Assistance" section at piliin ang type ng event na gusto ninyong i-request. Fill-up ang event details form kasama ang date, venue, at purpose ng event. I-submit ang request at hintayin ang approval mula sa barangay officials. Makakakuha kayo ng notification kapag approved na.',
        open: false,
    },
    {
        question: 'Paano ako makakapag-post sa Discussions section?',
        answer: 'Pumunta sa "Discussions" tab at i-click ang "+ ADD POST" button. Fill-up ang form kasama ang title, content, at piliin ang appropriate tags. I-click ang submit button. Ang inyong post ay makikita na agad sa discussions feed.',
        open: false,
    },
    {
        question: 'Paano ko makikita ang mga bagong announcements mula sa barangay?',
        answer: 'Ang mga announcements ay makikita sa "Posts" page ng inyong dashboard. May notification din kayo kapag may bagong announcement. Pwede ninyong i-filter ang posts ayon sa tags para mas madaling makita ang relevant announcements.',
        open: false,
    },
    {
        question: 'Paano ko mapapalitan ang aking profile picture?',
        answer: 'Pumunta sa "Profile" tab at i-click ang "Edit Profile Picture" button sa ilalim ng inyong profile photo. Piliin ang bagong image mula sa inyong device at i-click ang "Save Changes". Ang bagong profile picture ay makikita na agad.',
        open: false,
    },
    {
        question: 'Nakalimutan ko ang aking password. Paano ko ito ma-recover?',
        answer: 'Pumunta sa login page at i-click ang "Forgot Password" link. Ilagay ang inyong email address o username at susundan ang instructions na ipapadala sa inyo. Kung hindi ninyo ma-access ang email, tumawag sa +639193076338 o pumunta sa barangay office para sa assistance.',
        open: false,
    },
    {
        question: 'Paano ko makikita ang status ng aking mga requests?',
        answer: 'Lahat ng inyong requests (document requests, event assistance, etc.) ay makikita sa "Notifications" tab. Pwede ninyong i-filter ayon sa type ng notification. May color coding din: green para sa approved, red para sa rejected, at yellow para sa pending.',
        open: false,
    },
    {
        question: 'Ano ang dapat kong gawin kung may technical issue sa website?',
        answer: 'Kung may technical issue, pwede ninyong gamitin ang "Send us a message" section sa Help Center page. Ilagay ang details ng issue na naranasan ninyo at i-submit. Pwede din kayong tumawag sa +639193076338 o mag-email sa ikonek176b@dev.ph para sa immediate assistance.',
        open: false,
    },
])

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
    router.visit(route('profile_resident'))
}

const toggleFAQ = (index) => {
    faqs.value[index].open = !faqs.value[index].open
}

const submitMessage = () => {
    submitError.value = ''
    
    if (!message.value.trim()) {
        submitError.value = 'Please enter a message before submitting.'
        return
    }

    if (message.value.trim().length < 10) {
        submitError.value = 'Please enter a message with at least 10 characters.'
        return
    }

    router.post(route('help_center.contact'), {
        message: message.value.trim()
    }, {
        preserveScroll: true,
        onSuccess: () => {
            submitError.value = ''
            alert('Thank you for your message! We will get back to you as soon as possible.')
            message.value = ''
        },
        onError: (errors) => {
            if (errors && typeof errors === 'object') {
                if (errors.message) {
                    submitError.value = errors.message
                } else {
                    const firstError = Object.values(errors).flat()[0]
                    submitError.value = firstError || 'An error occurred while submitting your message. Please try again.'
                }
            } else {
                submitError.value = 'An error occurred while submitting your message. Please try again.'
            }
        }
    })
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    
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
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #ff8c42;
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.faq-btn .nav-icon {
    stroke: white;
}

.faq-btn.active {
    background: #ff8c42;
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

.help-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.help-header h2 {
    font-size: 22px;
    font-weight: 700;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

.help-content {
    padding: 30px;
}

.help-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 30px;
}

.faqs-section {
    display: flex;
    flex-direction: column;
}

.contact-message-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #000000;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title .icon {
    width: 20px;
    height: 20px;
    stroke: currentColor;
}

.faqs-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.faq-item {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}

.faq-item:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    border-color: #dee2e6;
}

.faq-question {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: white;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    transition: all 0.2s ease;
    text-align: left;
    gap: 12px;
}

.faq-question:hover {
    background: #fafbfc;
    color: #2bb24a;
}

.faq-arrow {
    width: 18px;
    height: 18px;
    stroke: #2bb24a;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.faq-arrow.rotated {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 16px 20px;
    font-size: 14px;
    line-height: 1.7;
    color: #666;
    background: #fafbfc;
    border-top: 1px solid #e9ecef;
}

.contact-section,
.message-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #333;
    padding: 14px 16px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.contact-item:hover {
    border-color: #ff8c42;
    background: #fff7ef;
}

.contact-icon {
    width: 20px;
    height: 20px;
    stroke: #ff8c42;
    flex-shrink: 0;
}

.message-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    resize: vertical;
    margin-bottom: 12px;
    transition: border-color 0.2s;
}

.message-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }

    .help-grid {
        grid-template-columns: 1fr;
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

    .help-content {
        padding: 20px;
    }

    .help-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .help-content {
        padding: 15px;
    }

    .help-grid {
        gap: 20px;
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