<template>
  <LayoutEmployee>
    <Head>
      <title>document req description - {{ documentType }}</title>
    </Head>
resident
    <div class="main-content">
      <!-- header -->
      <div class="document-header">
        <div>
          <h2>{{ documentType }}</h2>
        </div>

        <div class="small-logo"><!-- optional logo --></div>
      </div>

      <!-- selection / info area -->
      <div class="document-selection">
        <!-- left: list of document types -->
        <aside class="document-types">
          <nav class="nav-menu" style="padding:0 20px;">
            <Link :href="route('document_request_select_employee')" class="nav-item" style="margin-bottom:12px;">Back to selection</Link>
          </nav>

          <div style="padding: 10px 20px;">
            <div
              v-for="(desc, key) in documentDescriptions"
              :key="key"
            >
              <button
                class="doc-type-btn"
                :class="{ active: selectedDocType === key }"
                @click="selectDocument(key)"
                type="button"
              >
                {{ key }}
              </button>
            </div>
          </div>
        </aside>

        <!-- right: selected document info -->
        <section class="document-info">
          <div class="doc-title">{{ selectedDocType || documentType }}</div>

          <div class="doc-description">
            {{ documentDescriptions[selectedDocType] || documentDescriptions[documentType] }}
          </div>

          <div class="requirements-section">
            <h4>Requirements</h4>
            <ul class="requirements-list">
              <li
                v-for="(req, idx) in (documentRequirements[selectedDocType] || documentRequirements[documentType] || [])"
                :key="idx"
              >
                {{ req }}
              </li>
            </ul>
          </div>

          <div style="display:flex; gap:16px;">
            <Link
              :href="route('document_request_form_employee', { type: selectedDocType || documentType, mode: 'single' })"
              class="request-btn"
            >
              FOR MYSELF
            </Link>

            <Link
              :href="route('document_request_form_employee', { type: selectedDocType || documentType, mode: 'someone' })"
              class="request-btn"
              style="background: linear-gradient(135deg,#ffcf42,#ffb428);"
            >
              FOR SOMEONE ELSE
            </Link>
          </div>
        </section>
      </div>

      <!-- optional form view (keeps same classes as styles) -->
      <div v-if="currentView === 'form'" class="request-form-container">
        <button class="back-btn" @click="backToSelection">← Back</button>

        <div class="form-title">Request: {{ selectedDocType || documentType }}</div>

        <div class="form-sections">
          <div class="form-section">
            <div class="section-title">Personal Information</div>
            <div class="form-grid">
              <input class="form-input" v-model="formData.lastName" placeholder="Last name" />
              <input class="form-input" v-model="formData.firstName" placeholder="First name" />
              <input class="form-input" v-model="formData.middleName" placeholder="Middle name" />
              <input class="form-input" v-model="formData.suffix" placeholder="Suffix" />
            </div>

            <div class="form-grid">
              <input class="form-input" v-model="formData.birthdate" placeholder="Birthdate" />
              <input class="form-input" v-model="formData.sex" placeholder="Sex" />
              <input class="form-input" v-model="formData.civilStatus" placeholder="Civil Status" />
              <input class="form-input" v-model="formData.primaryNumber" placeholder="Primary Contact" />
            </div>
          </div>

          <div class="form-section">
            <div class="section-title">Purpose & Uploads</div>

            <textarea class="form-textarea" v-model="formData.purpose" placeholder="Purpose"></textarea>

            <div class="upload-section">
              <div>
                <button type="button" class="upload-btn" @click="triggerFileUpload">Upload Document</button>
                <input ref="fileInput" @change="handleFileUpload" class="file-input-hidden" type="file" />
                <div v-if="uploadedFileName" class="uploaded-file">{{ uploadedFileName }}</div>
              </div>

              <div style="display:flex; flex-direction:column; gap:8px;">
                <button class="verify-btn" @click="verifyOTP">Verify OTP</button>
              </div>
            </div>
          </div>

          <div style="display:flex; gap:16px;">
            <button class="submit-btn" @click="submitRequest">Submit Request</button>
          </div>
        </div>
      </div>

      <!-- success view -->
      <div v-if="currentView === 'success'" class="success-container">
        <div class="success-content">
          <div class="checkmark">✓</div>
          <div class="success-title">Request Submitted</div>
          <div class="request-number">Request #: {{ requestNumber }}</div>
          <div class="success-message">
            <p>Your request for <span class="highlight">{{ selectedDocType || documentType }}</span> has been submitted.</p>
            <p>We will notify you once it's processed.</p>
          </div>

          <div style="display:flex; gap:12px; justify-content:center;">
            <button class="view-request-btn" @click="viewRequest">View Request</button>
            <button class="request-btn" @click="backToSelection">Make Another Request</button>
          </div>
        </div>
      </div>
    </div>
  </LayoutEmployee>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import LayoutEmployee from '@/Layouts/LayoutEmployee.vue'

const page = usePage()
const documentType = computed(() => page.props.type || 'Barangay Certificate')

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

const documentDescriptions = {
    'Barangay ID': 'Ang Barangay ID ay isang opisyal na identification card na ibinibigay ng barangay sa mga lehitimong residente. Ito ay ginagamit bilang proof of residency at maaaring gamitin sa iba\'t ibang transaksyon sa loob at labas ng barangay.',
    'Barangay Certificate': 'Ang Barangay Certificate ay isang opisyal na dokumentong ibinibigay ng barangay upang patunayan na ang isang tao ay lehitimong residente ng nasabing lugar. Karaniwan itong kinakailangan sa iba\'t ibang transaksyong legal at administratibo gaya ng pag-apply ng trabaho, pag-enroll sa paaralan, pagkuha ng tulong mula sa gobyerno, at pagproseso ng mga permit o lisensya. Ginagamit din ito bilang patunay ng paninirahan kapag hinhingi ng korte o iba pang ahensya. Bukod sa pagiging katibayan ng paninirahan, maaari rin itong magsilbing patunay ng mabuting asal o katayuan ng isang tao sa komunidad. Nilagdaan ito ng Barangay Captain at may selyo ng barangay bilang patunay ng pagiging lehitimo ng dokumento.',
    'Cedula': 'Ang Cedula o Community Tax Certificate ay isang dokumento na nagpapatunay na ang isang indibidwal ay nagbayad ng community tax. Ito ay kailangan sa iba\'t ibang legal at business transactions.',
    'Barangay Business Permit': 'Ang Barangay Business Permit ay kinakailangan para sa lahat ng negosyo na nais magsimula ng operasyon sa loob ng barangay. Ito ay nagpapatunay na ang negosyo ay sumusunod sa mga regulasyon ng barangay.',
    'Barangay Building Permit': 'Ang Barangay Building Permit ay kinakailangan bago magsimula ng anumang konstruksyon o renovation sa loob ng barangay. Ito ay bahagi ng proseso ng pagkuha ng building permit mula sa munisipyo.'
}

const documentRequirements = {
    'Barangay ID': [
        'VALID ID',
        '2x2 PHOTO',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE FEE'
    ],
    'Barangay Certificate': [
        'VALID ID',
        'BARANGAY CLEARANCE FEE',
        'PROOF OF RESIDENCY',
        'PERSONAL APPEARANCE',
        'DULY ACCOMPLISED FORM'
    ],
    'Cedula': [
        'VALID ID',
        'TAX DECLARATION (if applicable)',
        'PROOF OF RESIDENCY'
    ],
    'Barangay Business Permit': [
        'BUSINESS REGISTRATION DOCUMENTS',
        'VALID ID OF OWNER',
        'PROOF OF RESIDENCY',
        'LEASE CONTRACT (if renting)',
        'BARANGAY CLEARANCE'
    ],
    'Barangay Building Permit': [
        'BUILDING PLANS',
        'LOT TITLE OR TAX DECLARATION',
        'VALID ID OF OWNER',
        'PROOF OF RESIDENCY',
        'BARANGAY CLEARANCE'
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
    if (tab === 'posts') {
        router.visit(route('announcement_employee'))
    }
}

const openFAQ = () => {
    console.log('Opening FAQ')
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

const submitRequest = () => {
    if (!formData.value.lastName || !formData.value.firstName || !formData.value.primaryNumber) {
        alert('Please fill in all required fields')
        return
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
}

.profile-avatar {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    object-fit: cover;
}

.profile-name {
    font-weight: 700;
    font-size: 15px;
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
}

.nav-item {
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    font-weight: 500;
}

.nav-item:hover {
    background: #fff7ef;
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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.document-header h2 {
    font-size: 22px;
    font-weight: 700;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
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
}

.doc-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
}

.doc-description {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 30px;
    text-align: justify;
}

.requirements-section h4 {
    color: #2bb24a;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 15px;
}

.requirements-list {
    list-style-position: inside;
    color: #333;
    font-size: 14px;
    line-height: 2;
    font-weight: 600;
    margin-bottom: 40px;
}

.request-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
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
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 30px;
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
}

.request-form-container::-webkit-scrollbar {
    width: 6px;
}

.request-form-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.request-form-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

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