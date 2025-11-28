<template>
    <Head>
        <title>Event Assistance</title>
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
                        class="nav-item active"
                        :class="{ active: activeTab === 'events' }"
                        @click="setActiveTab('events')"
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

                <button class="faq-btn" @click="openFAQ">
                    ❓ FAQs & Help Center
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="main-content">
                    <!-- Header -->
                    <div class="event-header">
                        <h2>Event Assistance</h2>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- View 1: Event Selection -->
                    <div v-if="currentView === 'selection'" class="event-selection">
                        <div class="event-types">
                            <button 
                                v-for="eventName in eventNames" 
                                :key="eventName"
                                class="event-type-btn" 
                                :class="{ active: selectedEventType === eventName }" 
                                @click="selectEvent(eventName)"
                            >
                                {{ eventName }}
                            </button>
                        </div>

                        <div class="event-info">
                            <h3 class="event-title">{{ selectedEventType }}</h3>
                            <div class="event-description">
                                <p>{{ eventDescriptions[selectedEventType] }}</p>
                            </div>

                            <div class="requirements-section">
                                <h4>REQUIREMENTS</h4>
                                <ol class="requirements-list">
                                    <li v-for="(req, index) in eventRequirements[selectedEventType]" :key="index">
                                        {{ req }}
                                    </li>
                                </ol>
                            </div>

                            <button class="request-btn" @click="proceedToForm">
                                REQUEST ASSISTANCE
                            </button>
                        </div>
                    </div>

                    <!-- View 2: Request Form -->
                    <div v-if="currentView === 'form'" class="request-form-container">
                        <button class="back-btn" @click="backToSelection">
                            ◀ Back to Events
                        </button>

                        <h3 class="form-title">REQUEST FORM</h3>

                        <div class="form-sections">
                            <!-- Personal Information -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <span class="icon">👤</span> Personal Information
                                </h4>
                                <div class="form-grid">
                                    <input type="text" v-model="formData.lastName" placeholder="Last Name" class="form-input" />
                                    <input type="text" v-model="formData.firstName" placeholder="First Name" class="form-input" />
                                    <input type="text" v-model="formData.middleName" placeholder="Middle Name" class="form-input" />
                                    <select v-model="formData.suffix" class="form-input">
                                        <option value="">Suffix</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                    </select>
                                </div>
                                <div class="form-grid">
                                    <input type="text" v-model="formData.address" placeholder="Complete Address" class="form-input full-width" />
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <span class="icon">📞</span> Contact Information
                                </h4>
                                <div class="contact-grid">
                                    <input type="tel" v-model="formData.primaryNumber" placeholder="+639 | Primary Number" class="form-input full-width" />
                                    <input type="tel" v-model="formData.secondaryNumber" placeholder="+639 | Secondary Number (Optional)" class="form-input full-width" />
                                    <div class="otp-section">
                                        <input type="text" v-model="formData.otpCode" placeholder="Enter OTP Code (Primary Number)" class="form-input otp-input" />
                                        <button class="verify-btn" @click="verifyOTP">VERIFY</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Details -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <span class="icon">📅</span> Event Details
                                </h4>
                                <div class="form-grid">
                                    <input type="date" v-model="formData.eventDate" placeholder="Event Date" class="form-input" />
                                    <input type="time" v-model="formData.eventTime" placeholder="Event Time" class="form-input" />
                                    <input type="number" v-model="formData.duration" placeholder="Duration (hours)" class="form-input" />
                                    <input type="number" v-model="formData.attendees" placeholder="Expected Attendees" class="form-input" />
                                </div>
                            </div>

                            <!-- Purpose and Documents -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <span class="icon">📝</span> Purpose and Supporting Documents
                                </h4>
                                <textarea 
                                    v-model="formData.purpose" 
                                    placeholder="Please describe the purpose of your request and provide any additional details..."
                                    class="form-textarea"
                                    rows="5"
                                ></textarea>
                                
                                <div class="upload-section">
                                    <select v-model="formData.documentType" class="form-input upload-select">
                                        <option value="">Type of Supporting Document</option>
                                        <option value="Valid ID">Valid ID</option>
                                        <option value="Death Certificate">Death Certificate (for Funeral Assistance)</option>
                                        <option value="Medical Certificate">Medical Certificate</option>
                                        <option value="Authorization Letter">Authorization Letter</option>
                                        <option value="Barangay Clearance">Barangay Clearance</option>
                                        <option value="Other">Other Documents</option>
                                    </select>
                                    <button class="upload-btn" @click="triggerFileUpload">
                                        UPLOAD
                                    </button>
                                    <input 
                                        type="file" 
                                        ref="fileInput" 
                                        @change="handleFileUpload" 
                                        class="file-input-hidden"
                                        accept="image/*,.pdf"
                                        multiple
                                    />
                                </div>
                                <div v-if="uploadedFiles.length > 0" class="uploaded-files-list">
                                    <p class="uploaded-label">Uploaded Files:</p>
                                    <div v-for="(file, index) in uploadedFiles" :key="index" class="uploaded-file-item">
                                        <span>{{ file }}</span>
                                        <button class="remove-file" @click="removeFile(index)">✕</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="submit-btn" @click="submitRequest">
                            SUBMIT REQUEST
                        </button>
                    </div>

                    <!-- View 3: Success Confirmation -->
                    <div v-if="currentView === 'success'" class="success-container">
                        <div class="success-content">
                            <div class="success-icon">
                                <div class="checkmark">✓</div>
                            </div>
                            <h3 class="success-title">REQUEST SUBMITTED</h3>
                            <p class="request-number">REQUEST NO. #{{ requestNumber }}</p>
                            
                            <div class="success-message">
                                <p>You have successfully submitted your request for <span class="highlight">{{ selectedEventType }}</span>.</p>
                                <p>Your request is now being reviewed by the barangay officials. We will contact you within 2-3 business days regarding the status of your application.</p>
                                <p>Your reference number is <span class="highlight">#{{ requestNumber }}</span>. Please keep this for tracking purposes.</p>
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const showSettings = ref(false)
const activeTab = ref('events')
const currentView = ref('selection')
const selectedEventType = ref('Funeral Assistance')
const uploadedFiles = ref([])
const requestNumber = ref('')
const fileInput = ref(null)

const formData = ref({
    lastName: '',
    firstName: '',
    middleName: '',
    suffix: '',
    address: '',
    primaryNumber: '',
    secondaryNumber: '',
    otpCode: '',
    eventDate: '',
    eventTime: '',
    duration: '',
    attendees: '',
    purpose: '',
    documentType: '',
    documents: []
})

// List of all event assistance types
const eventNames = [
    'Funeral Assistance',
    'Court Reservation',
    'Covered Court Event',
    'Community Hall Reservation',
    'Medical Mission Assistance',
    'Feeding Program Request',
    'Sports Equipment Lending',
    'Sound System Rental',
    'Tent and Tables Rental',
    'Ambulance Service Request',
    'Disaster Relief Assistance',
    'Livelihood Program Application'
]

const eventDescriptions = {
    'Funeral Assistance': 'Ang Funeral Assistance ay tulong na ibinibigay ng barangay sa mga pamilyang naulila upang makatulong sa gastos ng libing. Kasama dito ang financial assistance, tent rental, at iba pang logistics support. Layunin nito na mapagaan ang pasanin ng pamilya sa mahirap na panahon.',
    
    'Court Reservation': 'Ang Court Reservation ay para sa mga residente na nais gumamit ng barangay covered court para sa basketball, volleyball, o iba pang sports activities. Libre ito para sa barangay events at activities, ngunit may schedule at guidelines na dapat sundin.',
    
    'Covered Court Event': 'Ang serbisyong ito ay para sa mga residente na nais mag-organisa ng event sa covered court tulad ng birthday parties, wedding receptions, o community gatherings. May kasamang tables, chairs, at basic facilities. May bayad depende sa oras at kagamitan.',
    
    'Community Hall Reservation': 'Ang Community Hall ay available para sa meetings, seminars, trainings, at iba pang indoor activities. Equipped ito ng aircon, sound system, at seating capacity na 100 persons. Libre para sa barangay-organized events at may minimal fee para sa private functions.',
    
    'Medical Mission Assistance': 'Ito ay request para sa medical mission sa inyong area o para sa family members na nangangailangan ng medical assistance. Ang barangay ay tumutulong mag-coordinate sa healthcare providers at government hospitals para sa free checkup, medicines, at laboratory tests.',
    
    'Feeding Program Request': 'Ang Feeding Program ay para sa mga komunidad o organisasyon na nais mag-conduct ng feeding activity para sa kabataan o senior citizens. Ang barangay ay tumutulong sa logistics, food preparation area, at minsan ay may konting contribution sa ingredients.',
    
    'Sports Equipment Lending': 'Ang barangay ay may available na sports equipment tulad ng basketball, volleyball, chess set, at iba pa na pwedeng hiramin ng mga residente para sa sports activities o tournaments. May deposit at dapat ibalik sa mabuting kondisyon.',
    
    'Sound System Rental': 'Available ang sound system ng barangay para hiramin para sa events, programs, o gatherings. Kasama dito ang speakers, microphone, at basic audio equipment. May kasamang technician para sa malaking events.',
    
    'Tent and Tables Rental': 'Ang barangay ay may available na tents, tables, at chairs na pwedeng hiramin para sa outdoor events, birthday parties, o family gatherings. May limited quantity kaya advance reservation ay recommended.',
    
    'Ambulance Service Request': 'Emergency ambulance service para sa mga residenteng nangangailangan ng urgent medical transport. Available 24/7 para sa emergency cases. May nurse o trained personnel na kasama. Priority sa senior citizens at pregnant women.',
    
    'Disaster Relief Assistance': 'Tulong para sa mga pamilyang naapektuhan ng kalamidad tulad ng sunog, baha, o iba pang disasters. Kasama dito ang relief goods, temporary shelter, at assistance sa documentation para sa government aid programs.',
    
    'Livelihood Program Application': 'Application para sa livelihood programs ng barangay tulad ng skills training, microfinance assistance, at business development programs. Layunin nito ay tulungan ang mga residente na magkaroon ng sustainable income source.'
}

const eventRequirements = {
    'Funeral Assistance': [
        'DEATH CERTIFICATE (Original and Photocopy)',
        'VALID ID OF APPLICANT',
        'BARANGAY CLEARANCE',
        'PROOF OF RESIDENCY',
        'PROOF OF RELATIONSHIP TO DECEASED',
        'FUNERAL SERVICE CONTRACT (if available)'
    ],
    'Court Reservation': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE (for tournaments)',
        'WAIVER FORM',
        'SECURITY DEPOSIT (refundable)'
    ],
    'Covered Court Event': [
        'VALID ID OF ORGANIZER',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE',
        'EVENT PROPOSAL/DETAILS',
        'WAIVER AND HOLD HARMLESS AGREEMENT',
        'RENTAL FEE PAYMENT'
    ],
    'Community Hall Reservation': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'RESERVATION FORM',
        'PURPOSE OF USE LETTER',
        'RENTAL FEE (if applicable)',
        'SECURITY DEPOSIT'
    ],
    'Medical Mission Assistance': [
        'VALID ID',
        'BARANGAY CLEARANCE',
        'MEDICAL RECORDS (if available)',
        'PRESCRIPTION FROM DOCTOR (if applicable)',
        'PHILHEALTH ID (if available)',
        'SENIOR CITIZEN/PWD ID (if applicable)'
    ],
    'Feeding Program Request': [
        'VALID ID OF COORDINATOR',
        'ORGANIZATION CERTIFICATE (if applicable)',
        'PROGRAM PROPOSAL',
        'BARANGAY CLEARANCE',
        'LIST OF BENEFICIARIES',
        'MENU PLAN'
    ],
    'Sports Equipment Lending': [
        'VALID ID',
        'BARANGAY CLEARANCE',
        'PROOF OF RESIDENCY',
        'EQUIPMENT BORROWING FORM',
        'SECURITY DEPOSIT (refundable)',
        'WAIVER OF LIABILITY'
    ],
    'Sound System Rental': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE',
        'EVENT DETAILS',
        'RENTAL FEE',
        'SECURITY DEPOSIT'
    ],
    'Tent and Tables Rental': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'RENTAL FORM',
        'EVENT DATE AND VENUE',
        'QUANTITY NEEDED',
        'RENTAL FEE AND DEPOSIT'
    ],
    'Ambulance Service Request': [
        'VALID ID OF PATIENT OR RELATIVE',
        'MEDICAL CERTIFICATE OR REFERRAL LETTER',
        'PROOF OF RESIDENCY',
        'EMERGENCY CONTACT INFORMATION',
        'DESTINATION HOSPITAL/CLINIC'
    ],
    'Disaster Relief Assistance': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'INCIDENT REPORT OR BLOTTER',
        'PHOTOS OF DAMAGE (if applicable)',
        'BARANGAY CERTIFICATION OF INCIDENT',
        'LIST OF AFFECTED FAMILY MEMBERS'
    ],
    'Livelihood Program Application': [
        'VALID ID',
        'BARANGAY CLEARANCE',
        'PROOF OF RESIDENCY',
        'LIVELIHOOD PROPOSAL',
        'PROOF OF LOW INCOME (if required)',
        'CERTIFICATE OF ATTENDANCE IN ORIENTATION'
    ]
}

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
const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_request_employee'))
}
const openFAQ = () => {
    router.visit(route('help_center_employee'))
}

const selectEvent = (eventType) => {
    selectedEventType.value = eventType
}

const proceedToForm = () => {
    currentView.value = 'form'
}

const backToSelection = () => {
    currentView.value = 'selection'
}

const verifyOTP = () => {
    if (formData.value.otpCode.trim()) {
        alert('OTP verified successfully!')
    } else {
        alert('Please enter OTP code')
    }
}

const triggerFileUpload = () => {
    fileInput.value.click()
}

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files)
    files.forEach(file => {
        uploadedFiles.value.push(file.name)
        formData.value.documents.push(file)
    })
}

const removeFile = (index) => {
    uploadedFiles.value.splice(index, 1)
    formData.value.documents.splice(index, 1)
}

const testing = true  // set false when you want real validation

const submitRequest = () => {
    if (!testing) {
        if (!formData.value.lastName || !formData.value.firstName || !formData.value.primaryNumber) {
            alert('Please fill in all required fields')
            return
        }
        if (!formData.value.eventDate || !formData.value.eventTime) {
            alert('Please specify event date and time')
            return
        }
    }

    requestNumber.value = 'EA-' + Math.floor(Math.random() * 10000).toString().padStart(4, '0')
    currentView.value = 'success'
}

const viewRequest = () => {
    currentView.value = 'selection'
    alert(`Viewing request #${requestNumber.value}`)
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'events'
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
    box-shadow: 0 6px 20px rgba(43, 178, 74, 0.3);
    transition: all 0.3s ease;
    font-size: 14px;
}

.faq-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(43, 178, 74, 0.4);
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

.event-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.event-header h2 {
    font-size: 22px;
    font-weight: 700;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

.event-selection {
    display: grid;
    grid-template-columns: 320px 1fr;
    min-height: 600px;
}

.event-types {
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

.event-type-btn {
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

.event-type-btn:hover {
    background: #fff;
    color: #ff8c42;
}

.event-type-btn.active {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border-left-color: #ff8c42;
}

.event-info {
    padding: 40px;
    overflow-y: auto;
    max-height: calc(100vh - 250px);
}

.event-title {
    font-size: 40px;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
}

.event-description {
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

.request-btn {
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
    margin-left: 825px;
    margin-top: 10px;
}

.request-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
}

.request-form-container {
    padding: 30px 40px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.back-btn {
    background: transparent;
    border: none;
    color: #ff8c42;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.2s;
}

.back-btn:hover {
    color: #e6763a;
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    text-align: center;
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-bottom: 30px;
}

.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #ff8c42;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
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
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.full-width {
    grid-column: 1 / -1;
}

.contact-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
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
    transition: border-color 0.2s;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-textarea:focus {
    outline: none;
    border-color: #ff8c42;
}

.upload-section {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
}

.upload-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.file-input-hidden {
    display: none;
}

.uploaded-files-list {
    margin-top: 15px;
}

.uploaded-label {
    font-size: 13px;
    font-weight: 600;
    color: #2bb24a;
    margin-bottom: 10px;
}

.uploaded-file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    border: 1px solid #e0e0e0;
}

.uploaded-file-item span {
    font-size: 13px;
    color: #555;
}

.remove-file {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #dc3545;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.remove-file:hover {
    background: rgba(220, 53, 69, 0.2);
}

.submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    transition: all 0.3s;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
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
.event-types::-webkit-scrollbar,
.event-info::-webkit-scrollbar,
.request-form-container::-webkit-scrollbar {
    width: 6px;
}

.event-types::-webkit-scrollbar-track,
.event-info::-webkit-scrollbar-track,
.request-form-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.event-types::-webkit-scrollbar-thumb,
.event-info::-webkit-scrollbar-thumb,
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

    .event-selection {
        grid-template-columns: 280px 1fr;
    }

    .form-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .request-btn {
        margin-left: auto;
        margin-right: 0;
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

    .event-selection {
        grid-template-columns: 1fr;
    }

    .event-types {
        border-right: none;
        border-bottom: 1px solid #e0e0e0;
        max-height: 300px;
    }

    .event-info {
        padding: 25px;
    }

    .event-title {
        font-size: 28px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .upload-section {
        grid-template-columns: 1fr;
    }

    .otp-section {
        grid-template-columns: 1fr;
    }

    .request-form-container {
        padding: 20px;
    }

    .request-btn {
        width: 100%;
        margin-left: 0;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .profile-card {
        padding: 15px;
    }

    .event-info {
        padding: 20px;
    }

    .event-title {
        font-size: 22px;
    }

    .event-description {
        font-size: 16px;
    }

    .requirements-section h4 {
        font-size: 20px;
    }

    .requirements-list {
        font-size: 16px;
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
}
</style>