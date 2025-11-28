<template>
    <Head>
        <title>Document Request</title>
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
                        class="nav-item active"
                        :class="{ active: activeTab === 'documents' }"
                        @click="setActiveTab('documents')"
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

                <button class="faq-btn" @click="openFAQ">
                    ❓ FAQs & Help Center
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

                    <!-- View 1: Document Selection -->
                    <div v-if="currentView === 'selection'" class="document-selection">
                        <div class="document-types">
                            <button 
                                v-for="docName in documentNames" 
                                :key="docName"
                                class="doc-type-btn" 
                                :class="{ active: selectedDocType === docName }" 
                                @click="selectDocument(docName)"
                            >
                                {{ docName }}
                            </button>
                        </div>

                        <div class="document-info">
                            <h3 class="doc-title">{{ selectedDocType }}</h3>
                            <div class="doc-description">
                                <p>{{ documentDescriptions[selectedDocType] }}</p>
                            </div>

                            <div class="requirements-section">
                                <h4>REQUIREMENTS</h4>
                                <ol class="requirements-list">
                                    <li v-for="(req, index) in documentRequirements[selectedDocType]" :key="index">
                                        {{ req }}
                                    </li>
                                </ol>
                            </div>

                            <button class="request-btn" @click="proceedToForm">
                                REQUEST DOCUMENT
                            </button>
                        </div>
                    </div>

                    <!-- View 2: Request Form -->
                    <div v-if="currentView === 'form'" class="request-form-container">
                        <button class="back-btn" @click="backToSelection">
                            ◀ Back to Documents
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
                                    <input type="date" v-model="formData.birthdate" placeholder="Birthdate" class="form-input" />
                                    <select v-model="formData.sex" class="form-input">
                                        <option value="">Sex</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <select v-model="formData.civilStatus" class="form-input">
                                        <option value="">Civil Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                    <input type="text" v-model="formData.rolePosition" placeholder="Role/Position" class="form-input" />
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

                            <!-- Proof of Intent -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <span class="icon">📝</span> Proof of Intent
                                </h4>
                                <textarea 
                                    v-model="formData.purpose" 
                                    placeholder="What is the purpose of your request?"
                                    class="form-textarea"
                                    rows="5"
                                ></textarea>
                                
                                <div class="upload-section">
                                    <select v-model="formData.idType" class="form-input upload-select">
                                        <option value="">Type of Identification/Document</option>
                                        <option value="National ID">National ID</option>
                                        <option value="Driver's License">Driver's License</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Voter's ID">Voter's ID</option>
                                        <option value="SSS ID">SSS ID</option>
                                        <option value="UMID">UMID</option>
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
                                    />
                                </div>
                                <p v-if="uploadedFileName" class="uploaded-file">Uploaded: {{ uploadedFileName }}</p>
                            </div>
                        </div>

                        <button class="submit-btn" @click="submitRequest">
                            SUBMIT
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
                                <p>You have successfully submitted your request form to acquire a <span class="highlight">{{ selectedDocType }}</span>.</p>
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const showSettings = ref(false)
const activeTab = ref('documents')
const currentView = ref('selection')
const selectedDocType = ref('Barangay Certificate')
const uploadedFileName = ref('')
const requestNumber = ref('')
const fileInput = ref(null)

const formData = ref({
    lastName: '',
    firstName: '',
    middleName: '',
    suffix: '',
    birthdate: '',
    sex: '',
    civilStatus: '',
    rolePosition: '',
    primaryNumber: '',
    secondaryNumber: '',
    otpCode: '',
    purpose: '',
    idType: '',
    document: null
})

// List of all document names
const documentNames = [
    'Barangay Certificate',
    'Barangay Clearance',
    'Barangay ID',
    'Cedula',
    'Certificate of Residency',
    'Certificate of Indigency',
    'Business Permit',
    'Building Permit',
    'Barangay Protection Order',
    'Certificate of Good Moral',
    'First Time Job Seeker Certificate',
    'Certificate for Solo Parent',
    'Certificate for PWD',
    'Certificate for Senior Citizen',
    'Certificate of No Pending Case',
    'Demolition Permit'
]

const documentDescriptions = {
    'Barangay Certificate': 'Ang Barangay Certificate ay isang opisyal na dokumentong ibinibigay ng barangay upang patunayan na ang isang tao ay lehitimong residente ng nasabing lugar. Karaniwan itong kinakailangan sa iba\'t ibang transaksyong legal at administratibo gaya ng pag-apply ng trabaho, pag-enroll sa paaralan, pagkuha ng tulong mula sa gobyerno, at pagproseso ng mga permit o lisensya.',
    
    'Barangay Clearance': 'Ang Barangay Clearance ay sertipikasyon na nagpapatunay na ang isang residente ay walang pending case o anumang kaso sa loob ng barangay. Ito ay kailangan para sa employment, business permits, at iba pang legal transactions. Nagpapakita rin ito ng mabuting asal ng isang tao sa komunidad.',
    
    'Barangay ID': 'Ang Barangay ID ay isang opisyal na identification card na ibinibigay ng barangay sa mga lehitimong residente. Ito ay ginagamit bilang proof of residency at maaaring gamitin sa iba\'t ibang transaksyon sa loob at labas ng barangay. May kasamang larawan at personal na impormasyon ng may-ari.',
    
    'Cedula': 'Ang Cedula o Community Tax Certificate ay isang dokumento na nagpapatunay na ang isang indibidwal ay nagbayad ng community tax. Ito ay kailangan sa iba\'t ibang legal at business transactions, at ginagamit din bilang valid ID sa ilang transaksyon.',
    
    'Certificate of Residency': 'Ang Certificate of Residency ay nagpapatunay ng aktwal na tirahan ng isang tao sa loob ng barangay. Ito ay iba sa Barangay Certificate dahil mas specific sa paninirahan at karaniwang ginagamit para sa school enrollment, government benefits, at iba pang administrative requirements.',
    
    'Certificate of Indigency': 'Ang Certificate of Indigency ay sertipikasyon na nagpapatunay na ang isang pamilya o indibidwal ay walang sapat na kita at nangangailangan ng tulong. Ito ay ginagamit upang makakuha ng medical assistance, educational scholarships, at iba pang social services mula sa gobyerno at pribadong organisasyon.',
    
    'Business Permit': 'Ang Barangay Business Permit ay kinakailangan para sa lahat ng negosyo na nais magsimula ng operasyon sa loob ng barangay. Ito ay nagpapatunay na ang negosyo ay sumusunod sa mga regulasyon ng barangay at hindi nakakasagabal sa kapakanan ng mga residente.',
    
    'Building Permit': 'Ang Barangay Building Permit ay kinakailangan bago magsimula ng anumang konstruksyon o renovation sa loob ng barangay. Ito ay bahagi ng proseso ng pagkuha ng building permit mula sa munisipyo at nagsisiguro na ang plano ay sumusunod sa zoning at safety regulations.',
    
    'Barangay Protection Order': 'Ang Barangay Protection Order ay isang legal na order na ibinibigay upang protektahan ang mga biktima ng violence against women and children (VAWC). Ito ay nagbabawal sa abuser na lumapit o mang-harass sa biktima at nagbibigay ng karagdagang proteksyon sa kanilang kaligtasan.',
    
    'Certificate of Good Moral': 'Ang Certificate of Good Moral ay sertipikasyon na nagpapatunay na ang isang residente ay may mabuting asal at walang record ng maling gawa sa loob ng barangay. Ito ay kailangan para sa employment, school admission, at iba pang professional requirements.',
    
    'First Time Job Seeker Certificate': 'Ang First Time Job Seeker Certificate ay sertipikasyon na kailangan ng mga bagong graduates o first-time job applicants. Ito ay exemption mula sa Documentary Stamp Tax para sa kanilang unang trabaho, ayon sa batas.',
    
    'Certificate for Solo Parent': 'Ang Certificate for Solo Parent ay sertipikasyon para sa mga magulang na nag-iisa sa pag-aalaga ng kanilang anak. Ito ay nagbibigay ng access sa iba\'t ibang benefits at privileges tulad ng work schedule flexibility, educational assistance, at iba pa ayon sa Solo Parents Welfare Act.',
    
    'Certificate for PWD': 'Ang Certificate for PWD (Person with Disability) ay sertipikasyon para sa mga residente na may kapansanan. Ito ay kailangan upang makakuha ng PWD ID at makapag-enjoy ng benefits tulad ng discounts, priority lanes, at iba pang privileges na itinakda ng batas.',
    
    'Certificate for Senior Citizen': 'Ang Certificate for Senior Citizen ay sertipikasyon para sa mga residente na edad 60 pataas. Ito ay kailangan upang makakuha ng Senior Citizen ID at makapag-enjoy ng 20% discount sa gamot, pagkain, transportasyon, at iba pang serbisyo.',
    
    'Certificate of No Pending Case': 'Ang Certificate of No Pending Case ay sertipikasyon na nagpapatunay na ang isang residente ay walang pending na kaso sa Barangay o sa Lupong Tagapamayapa. Ito ay kailangan para sa employment verification, visa application, at iba pang legal procedures.',
    
    'Demolition Permit': 'Ang Demolition Permit ay permit na kailangan bago magsimula ng demolisyon ng anumang istruktura sa loob ng barangay. Ito ay nagsisiguro na ang demolisyon ay gagawin ng ligtas at may tamang proseso, at hindi makakaapekto sa mga kapitbahay.'
}

const documentRequirements = {
    'Barangay Certificate': [
        'VALID ID',
        'BARANGAY CLEARANCE FEE',
        'PROOF OF RESIDENCY',
        'PERSONAL APPEARANCE',
        'DULY ACCOMPLISHED FORM'
    ],
    'Barangay Clearance': [
        'VALID ID (Original and Photocopy)',
        '2x2 ID PICTURE (2 copies)',
        'PROOF OF RESIDENCY',
        'CLEARANCE FEE',
        'CEDULA (if applicable)'
    ],
    'Barangay ID': [
        'VALID ID',
        '2x2 PHOTO (2 copies)',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE FEE',
        'BIRTH CERTIFICATE (for first time applicants)'
    ],
    'Cedula': [
        'VALID ID',
        'TAX DECLARATION (if applicable)',
        'PROOF OF RESIDENCY',
        'INCOME STATEMENT (if employed)'
    ],
    'Certificate of Residency': [
        'VALID ID',
        'PROOF OF RESIDENCY (Utility Bills, Lease Contract)',
        'BARANGAY CLEARANCE FEE',
        'PERSONAL APPEARANCE'
    ],
    'Certificate of Indigency': [
        'VALID ID',
        'PROOF OF LOW INCOME',
        'PROOF OF RESIDENCY',
        'PERSONAL APPEARANCE',
        'RECOMMENDATION FROM BARANGAY OFFICIALS'
    ],
    'Business Permit': [
        'BUSINESS REGISTRATION DOCUMENTS',
        'VALID ID OF OWNER',
        'PROOF OF RESIDENCY',
        'LEASE CONTRACT (if renting)',
        'BARANGAY CLEARANCE',
        'DTI/SEC REGISTRATION',
        'LOCATION PLAN'
    ],
    'Building Permit': [
        'BUILDING PLANS (3 copies)',
        'LOT TITLE OR TAX DECLARATION',
        'VALID ID OF OWNER',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE',
        'ENGINEER\'S CERTIFICATION'
    ],
    'Barangay Protection Order': [
        'VALID ID',
        'AFFIDAVIT OF COMPLAINT',
        'MEDICAL CERTIFICATE (if applicable)',
        'POLICE REPORT (if filed)',
        'WITNESS AFFIDAVITS (if available)',
        'PROOF OF RESIDENCY'
    ],
    'Certificate of Good Moral': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE FEE',
        'PERSONAL APPEARANCE',
        'PURPOSE OF REQUEST'
    ],
    'First Time Job Seeker Certificate': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'BIRTH CERTIFICATE',
        'SCHOOL DIPLOMA/TOR',
        'AFFIDAVIT OF FIRST TIME JOB SEEKER',
        'BARANGAY CLEARANCE'
    ],
    'Certificate for Solo Parent': [
        'VALID ID',
        'BIRTH CERTIFICATE OF CHILD/CHILDREN',
        'PROOF OF SOLO PARENT STATUS (Death Certificate, Annulment Papers, Affidavit)',
        'PROOF OF RESIDENCY',
        '2x2 ID PICTURE'
    ],
    'Certificate for PWD': [
        'VALID ID',
        'MEDICAL CERTIFICATE FROM LICENSED PHYSICIAN',
        'PROOF OF RESIDENCY',
        '2x2 ID PICTURE (2 copies)',
        'BIRTH CERTIFICATE'
    ],
    'Certificate for Senior Citizen': [
        'VALID ID',
        'BIRTH CERTIFICATE or VALID ID showing birth date',
        'PROOF OF RESIDENCY',
        '2x2 ID PICTURE (2 copies)'
    ],
    'Certificate of No Pending Case': [
        'VALID ID',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE FEE',
        'PERSONAL APPEARANCE',
        'PURPOSE OF REQUEST'
    ],
    'Demolition Permit': [
        'DEMOLITION PLAN',
        'LOT TITLE OR TAX DECLARATION',
        'VALID ID OF OWNER',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE',
        'CONTRACTOR\'S CERTIFICATION',
        'NOTARIZED CONSENT FROM NEIGHBORS'
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

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_employee'))
}
const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_employee'))
}
const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_request_employee'))
}
const openFAQ = () => {
    router.visit(route('help_center_employee'))
}

const selectDocument = (docType) => {
    selectedDocType.value = docType
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
    const file = event.target.files[0]
    if (file) {
        uploadedFileName.value = file.name
        formData.value.document = file
    }
}

const testing = true  // set false when you want real validation

const submitRequest = () => {
    if (!testing) {
        if (!formData.value.lastName || !formData.value.firstName || !formData.value.primaryNumber) {
            alert('Please fill in all required fields')
            return
        }
    }

    requestNumber.value = '00' + Math.floor(Math.random() * 1000).toString().padStart(3, '0')
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
    activeTab.value = 'documents'
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
    margin-left: 825px;  /* push right */
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
    margin-left: 550px;
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
    transition: border-color 0.2s;
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

.uploaded-file {
    margin-top: 10px;
    font-size: 13px;
    color: #2bb24a;
    font-weight: 600;
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

    .upload-section {
        grid-template-columns: 1fr;
    }

    .otp-section {
        grid-template-columns: 1fr;
    }

    .request-form-container {
        padding: 20px;
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
}
</style>