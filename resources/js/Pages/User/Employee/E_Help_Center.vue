<template>
    <Head>
        <title>Help Center</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/WHITE LOGO.png" alt="Logo" class="header-logo" />
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

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img src="/assets/KAPITAN.jpg" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">Kap. Sammy Reyes</div>
                        <div class="profile-role">BARANGAY CAPTAIN</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'posts' }"
                        @click="navigateToPosts"
                    >
                        📋 Posts
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'documents' }"
                        @click="navigateToDocuments"
                    >
                        📄 Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'events' }"
                        @click="navigateToEvents"
                    >
                        🤝 Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'notifications' }"
                        @click="navigateToNotifications"
                    >
                        🔔 Notifications
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'profile' }"
                        @click="navigateToProfile"
                    >
                        👤 Profile
                    </Link>
                </div>

                <button class="faq-btn active">
                    ❓ FAQs & Help Center
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
                                    <span class="icon">❓</span> FAQs
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
                                            <span class="faq-arrow" :class="{ rotated: faq.open }">▼</span>
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
                                            <span class="contact-icon">📞</span>
                                            <span>+639193076338</span>
                                        </div>
                                        <div class="contact-item">
                                            <span class="contact-icon">📧</span>
                                            <span>ikonek176b@dev.ph</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Send Message -->
                                <div class="message-section">
                                    <h3 class="section-title">Send us a message</h3>
                                    <textarea
                                        v-model="message"
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const showSettings = ref(false)
const activeTab = ref('help')
const message = ref('')

const faqs = ref([
    {
        question: 'Ilang araw bago ma-accept ang document request?',
        answer: 'Karaniwan ay 2-3 araw bago ma-approve ang request depende sa dami ng requests at availability ng barangay officials. Makakakuha kayo ng notification kapag na-process na ang inyong request.',
        open: false,
    },
    {
        question: 'Bakit hindi natatanggap ang document request ko?',
        answer: 'Siguraduhing tama ang mga impormasyon at kumpleto ang requirements na ipinasa. Maaaring rejected ang request kung may kulang na dokumento o hindi valid ang mga ID na isinumite. I-check ang notifications para sa specific reason.',
        open: false,
    },
    {
        question: 'Paano makita ang mga bagong announcement?',
        answer: 'Makikita ang mga bagong announcements sa Posts page ng inyong resident dashboard. May notification din kayo kapag may bagong announcement mula sa barangay officials.',
        open: false,
    },
    {
        question: 'Paano palitan ang aking profile picture?',
        answer: 'Pumunta sa Profile tab at i-click ang "Edit Profile" button. Doon ay pwede ninyong i-upload ang bagong profile picture at i-save ang changes.',
        open: false,
    },
    {
        question: 'Paano mag post sa discussions section?',
        answer: 'Pumunta sa Discussions tab at i-click ang "+ ADD POST" button sa taas. Fill-up ang form kasama ang title, content, at tags, tapos i-click ang submit.',
        open: false,
    },
])

const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const logout = () => {
    showSettings.value = false
    router.visit(route('login'))
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_employee'))
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_employee'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_employee'))
}

const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_request_employee'))
}

const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_employee'))
}

const toggleFAQ = (index) => {
    faqs.value[index].open = !faqs.value[index].open
}

const submitMessage = () => {
    if (message.value.trim()) {
        alert('Thank you for your message! We will get back to you as soon as possible.\n\nYour message:\n' + message.value)
        message.value = ''
    } else {
        alert('Please enter a message before submitting.')
    }
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
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
    background: #239640;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
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
    display: block;
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
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.faq-btn.active {
    background: linear-gradient(135deg, #239640, #1e7a35);
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

.faqs-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.faq-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.faq-question {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: white;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #333;
    transition: background 0.2s;
    text-align: left;
}

.faq-question:hover {
    background: #f8f9fa;
}

.faq-arrow {
    color: #2bb24a;
    font-size: 11px;
    transition: transform 0.3s ease;
}

.faq-arrow.rotated {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 12px 15px;
    font-size: 12px;
    line-height: 1.5;
    color: #555;
    background: #fafbfc;
    border-top: 1px solid #e0e0e0;
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
    gap: 10px;
    font-size: 13px;
    color: #333;
    padding: 10px;
    background: white;
    border-radius: 6px;
}

.contact-icon {
    font-size: 18px;
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
</style>  