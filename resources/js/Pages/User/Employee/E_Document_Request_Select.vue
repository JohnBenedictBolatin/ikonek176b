<template>
    <Head>
        <title>Document Request</title>
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
                    <!-- Settings Dropdown -->
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
                    <img :src="profilePictureUrl" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
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
                        class="nav-item active"
                        :class="{ active: activeTab === 'documents' }"
                        @click="setActiveTab('documents')"
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            BACK TO DOCUMENTS
                        </button>

                        <h3 class="form-title">REQUEST FORM</h3>

                        <div class="form-sections">
                            <!-- Proof of Intent -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Purpose/Description <span class="required-star">*</span>
                                </h4>
                                <!-- Barangay Certificate: Select dropdown with predefined options -->
                                <template v-if="selectedDocType === 'Barangay Certificate'">
                                    <select 
                                        v-model="form.purpose" 
                                        class="form-input"
                                        required
                                        @change="if (form.purpose !== 'Others') purposeOthers = ''"
                                    >
                                        <option value="">Select purpose</option>
                                        <option v-for="purpose in barangayCertificatePurposes" :key="purpose" :value="purpose">
                                            {{ purpose }}
                                        </option>
                                    </select>
                                    <!-- Show text input when "Others" is selected -->
                                    <input
                                        v-if="form.purpose === 'Others'"
                                        v-model="purposeOthers"
                                        @input="form.purpose = purposeOthers"
                                        placeholder="Please specify your purpose..."
                                        class="form-input"
                                        style="margin-top: 10px;"
                                        required
                                    />
                                </template>
                                <!-- Other documents: Keep textarea -->
                                <textarea 
                                    v-else
                                    v-model="form.purpose" 
                                    placeholder="Please provide a brief description of your request..."
                                    class="form-textarea"
                                    rows="4"
                                    required
                                ></textarea>
                                
                                <div class="upload-section">
                                    <!-- ID Type select -->
                                    <select v-model="form.id_type" class="form-input upload-select">
                                        <option value="">Type of Identification</option>
                                        <option value="National ID">National ID</option>
                                        <option value="Driver's License">Driver's License</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Voter's ID">Voter's ID</option>
                                        <option value="SSS ID">SSS ID</option>
                                        <option value="UMID">UMID</option>
                                    </select>

                                    <!-- Upload front -->
                                    <div class="upload-row">
                                        <button class="upload-btn" @click.prevent="triggerFileUpload('front')">
                                            UPLOAD FRONT
                                        </button>
                                        <input 
                                            type="file" 
                                            ref="fileFrontInput" 
                                            @change="handleFileUpload($event, 'front')" 
                                            class="file-input-hidden"
                                            accept="image/*,.pdf"
                                            style="display: none"
                                        />

                                        <button class="upload-btn" @click.prevent="triggerFileUpload('back')">
                                            UPLOAD BACK
                                        </button>
                                        <input 
                                            type="file" 
                                            ref="fileBackInput" 
                                            @change="handleFileUpload($event, 'back')" 
                                            class="file-input-hidden"
                                            accept="image/*,.pdf"
                                            style="display: none"
                                        />
                                    </div>
                                </div>

                                <p v-if="idFrontName" class="uploaded-file">Front: {{ idFrontName }}</p>
                                <p v-if="idBackName" class="uploaded-file">Back: {{ idBackName }}</p>

                                <!-- ID number field appears only when an ID type is chosen AND at least one file uploaded -->
                                <div v-if="showIdNumber" class="id-number-field" style="margin-top: 12px;">
                                    <label class="field-label">{{ idNumberLabel }} <span>*</span></label>
                                    <input
                                        type="text"
                                        v-model="form.id_number"
                                        placeholder="Enter ID Number"
                                        class="form-input"
                                    />
                                </div>
                            </div>

                            <!-- Dynamic fields for the selected document -->
                            <div class="form-section dynamic-fields" v-if="currentDocumentFields.length">
                                <h4 class="section-title">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Additional Information & Required Documents
                                </h4>

                                <!-- Non-file fields (text, select, number, etc.) -->
                                <div v-for="field in currentDocumentFields.filter(f => f.type !== 'file')" :key="field.name" class="dynamic-field-wrapper">
                                    <div class="field-header">
                                        <label class="field-label">
                                            {{ field.label }} 
                                            <span v-if="field.required" class="required-star">*</span>
                                            <span v-else class="optional-text">(Optional)</span>
                                        </label>
                                        <p v-if="field.description" class="field-description">{{ field.description }}</p>
                                    </div>

                                    <div class="field-input-wrapper">
                                        <!-- text -->
                                        <input 
                                            v-if="field.type === 'text'"
                                            v-model="form.extra_fields[field.name]"
                                            :placeholder="field.placeholder || ''"
                                            class="form-input"
                                            :required="field.required"
                                        />

                                        <!-- textarea -->
                                        <textarea
                                            v-if="field.type === 'textarea'"
                                            v-model="form.extra_fields[field.name]"
                                            :placeholder="field.placeholder || ''"
                                            rows="3"
                                            class="form-textarea"
                                            :required="field.required"
                                        ></textarea>

                                        <!-- date -->
                                        <input
                                            v-if="field.type === 'date'"
                                            type="date"
                                            v-model="form.extra_fields[field.name]"
                                            :max="field.max || today"
                                            class="form-input"
                                            :required="field.required"
                                        />

                                        <!-- number -->
                                        <input
                                            v-if="field.type === 'number'"
                                            type="number"
                                            v-model.number="form.extra_fields[field.name]"
                                            :placeholder="field.placeholder || ''"
                                            :min="field.min !== undefined ? field.min : undefined"
                                            :step="field.step !== undefined ? field.step : undefined"
                                            class="form-input"
                                            :required="field.required"
                                            @keypress="(e) => { if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'Tab') e.preventDefault(); }"
                                        />

                                        <!-- select -->
                                        <select
                                            v-if="field.type === 'select'"
                                            v-model="form.extra_fields[field.name]"
                                            class="form-input"
                                            :required="field.required"
                                        >
                                            <option value="">{{ field.placeholder || 'Select an option' }}</option>
                                            <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                        </select>

                                        <!-- checkbox group -->
                                        <div v-if="field.type === 'checkbox'" class="checkbox-group">
                                            <label v-for="opt in field.options" :key="opt" class="checkbox-label">
                                                <input
                                                    type="checkbox"
                                                    :value="opt"
                                                    @change="toggleCheckbox(field.name, opt, $event.target.checked)"
                                                    :checked="Array.isArray(form.extra_fields[field.name]) && form.extra_fields[field.name].includes(opt)"
                                                />
                                                <span>{{ opt }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- File upload fields - displayed in grid -->
                                <div v-if="currentDocumentFields.filter(f => f.type === 'file').length > 0" class="file-uploads-grid">
                                    <div v-for="field in currentDocumentFields.filter(f => f.type === 'file')" :key="field.name" class="file-upload-item">
                                        <div class="file-upload-header">
                                            <label class="file-upload-label">
                                                {{ field.label }} 
                                                <span v-if="field.required" class="required-star">*</span>
                                                <span v-else class="optional-text">(Optional)</span>
                                            </label>
                                            <p v-if="field.description" class="file-upload-description">{{ field.description }}</p>
                                        </div>
                                        <div class="file-upload-controls">
                                            <button 
                                                type="button"
                                                class="upload-btn-dynamic" 
                                                @click.prevent="triggerDynamicFileUpload(field.name)"
                                            >
                                                UPLOAD
                                            </button>
                                            <input
                                                type="file"
                                                :data-dyn-field="field.name"
                                                style="display: none"
                                                @change="handleDynamicFileUpload($event, field.name)"
                                                :accept="field.accept || 'image/*,.pdf'"
                                            />
                                            <div v-if="dynamicFileNames[field.name]" class="uploaded-file-info-compact">
                                                <svg class="file-checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 18px; height: 18px; color: #4caf50;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span class="file-name-compact">{{ dynamicFileNames[field.name] }}</span>
                                                <button 
                                                    type="button"
                                                    class="remove-file-btn-small" 
                                                    @click="removeDynamicFile(field.name)"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <button
                        type="button"
                        class="submit-btn"
                        :disabled="isSubmitting"
                        @click="submitRequest"
                        >
                            <span v-if="!isSubmitting">SUBMIT</span>
                            <span v-else>Submitting...</span>
                        </button>
                    </div>

                    <!-- View 3: Success Confirmation -->
                    <div v-if="currentView === 'success'" class="success-container">
                        <div class="success-content">
                            <div class="success-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 60px; height: 60px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
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
import { Link, usePage } from '@inertiajs/vue3'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

// Get page props
const page = usePage()

// Props definition
const props = defineProps({
    auth: {
        type: Object,
        default: () => ({})
    }
})

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
    const role = roleMap[id] ?? 'Employee'
    return role.toUpperCase()
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
const activeTab = ref('documents')
const currentView = ref('selection')
const selectedDocType = ref('Barangay Certificate')
const requestNumber = ref('')
const isSubmitting = ref(false)
const purposeOthers = ref('') // For custom purpose input when "Others" is selected

// Common purposes for Barangay Certificate
const barangayCertificatePurposes = [
  'Employment',
  'School Admission',
  'Government Transaction',
  'Business Registration',
  'Bank Transaction',
  'Others'
]

// new refs for ID front/back input elements and filenames
const fileFrontInput = ref(null)
const fileBackInput = ref(null)
const idFrontName = ref('')
const idBackName = ref('')

// dynamic file names tracking for dynamic fields
const dynamicFileNames = ref({})

// --- useForm: include id front/back and id_number
const form = useForm({
  document_name: selectedDocType.value,
  fk_document_type_id: '',

  // mirrored user columns (prefill from user if available)
  last_name: user.value?.last_name ?? (user.value?.name ? user.value.name.split(' ').slice(-1).join(' ') : ''),
  first_name: user.value?.first_name ?? user.value?.name ?? '',
  middle_name: user.value?.middle_name ?? '',
  suffix: user.value?.suffix ?? '',
  birthdate: user.value?.birthdate ?? '',
  sex: user.value?.sex ?? '',
  civil_status: user.value?.civil_status ?? '',
  address: user.value?.address ?? '',
  contact_number: user.value?.contact_number ?? '',

  // request-specific
  purpose: '', // text description
  id_type: '',
  id_number: '',
  // legacy UI fields (kept so the component logic still references them)
  id_front: null,
  id_back: null,

  // IMPORTANT: these are the backend-target keys requested
  valid_id_content: null,
  valid_id_number: '',

  document: null, // other uploaded doc(s)
  extra_fields: {} // dynamic per-document fields (will be JSON on the backend)
})

// keep document_name synced with selectedDocType
watch(selectedDocType, (val) => {
  form.document_name = val
  initExtraFieldsForDocument(val)
  // Clear purpose and custom purpose when switching documents
  form.purpose = ''
  purposeOthers.value = ''
})

// when id_type changes, clear id_number (so user can re-enter)
watch(() => form.id_type, (newVal, oldVal) => {
  form.id_number = ''
})

// computed for showing id number field only after an id_type is selected AND at least one side uploaded
const showIdNumber = computed(() => {
  return !!form.id_type && (!!form.id_front || !!form.id_back)
})

// mapping labels per id type
const idNumberLabels = {
  'National ID': 'National ID No.',
  "Driver's License": "Driver's License No.",
  'Passport': 'Passport No.',
  "Voter's ID": "Voter\'s ID No.",
  'SSS ID': 'SSS No.',
  'UMID': 'UMID No.'
}
const idNumberLabel = computed(() => {
  return idNumberLabels[form.id_type] || 'ID Number'
})

// helper date
const today = new Date().toISOString().split('T')[0]

// Document lists and descriptions
const documentNames = [
    'Barangay Certificate',
    'Barangay Clearance',
    'Barangay ID',
    'Cedula',
    'Business Permit',
    'Building Permit',
    'Certificate of Indigency',
    'Certificate of Good Moral',
]

const documentDescriptions = {
    'Barangay Certificate': 'Ang Barangay Certificate ay isang opisyal na dokumentong ibinibigay ng barangay upang patunayan na ang isang tao ay lehitimong residente ng nasabing lugar. Karaniwan itong kinakailangan sa iba\'t ibang transaksyong legal at administratibo gaya ng pag-apply ng trabaho, pag-enroll sa paaralan, pagkuha ng tulong mula sa gobyerno, at pagproseso ng mga permit o lisensya.',
    
    'Barangay Clearance': 'Ang Barangay Clearance ay sertipikasyon na nagpapatunay na ang isang residente ay walang pending case o anumang kaso sa loob ng barangay. Ito ay kailangan para sa employment, business permits, at iba pang legal transactions. Nagpapakita rin ito ng mabuting asal ng isang tao sa komunidad.',
    
    'Barangay ID': 'Ang Barangay ID ay isang opisyal na identification card na ibinibigay ng barangay sa mga lehitimong residente. Ito ay ginagamit bilang proof of residency at maaaring gamitin sa iba\'t ibang transaksyon sa loob at labas ng barangay. May kasamang larawan at personal na impormasyon ng may-ari.',
    
    'Cedula': 'Ang Cedula o Community Tax Certificate ay isang dokumento na nagpapatunay na ang isang indibidwal ay nagbayad ng community tax. Ito ay kailangan sa iba\'t ibang legal at business transactions, at ginagamit din bilang valid ID sa ilang transaksyon.',
        
    'Certificate of Indigency': 'Ang Certificate of Indigency ay sertipikasyon na nagpapatunay na ang isang pamilya o indibidwal ay walang sapat na kita at nangangailangan ng tulong. Ito ay ginagamit upang makakuha ng medical assistance, educational scholarships, at iba pang social services mula sa gobyerno at pribadong organisasyon.',
    
    'Business Permit': 'Ang Barangay Business Permit ay kinakailangan para sa lahat ng negosyo na nais magsimula ng operasyon sa loob ng barangay. Ito ay nagpapatunay na ang negosyo ay sumusunod sa mga regulasyon ng barangay at hindi nakakasagabal sa kapakanan ng mga residente.',
    
    'Building Permit': 'Ang Barangay Building Permit ay kinakailangan bago magsimula ng anumang konstruksyon o renovation sa loob ng barangay. Ito ay bahagi ng proseso ng pagkuha ng building permit mula sa munisipyo at nagsisiguro na ang plano ay sumusunod sa zoning at safety regulations.',
    
    'Certificate of Good Moral': 'Ang Certificate of Good Moral ay sertipikasyon na nagpapatunay na ang isang residente ay may mabuting asal at walang record ng maling gawa sa loob ng barangay. Ito ay kailangan para sa employment, school admission, at iba pang professional requirements.',
}

const documentRequirements = {
    'Barangay Certificate': [
        '• Valid ID of the requestor',
        '• Supporting documents for residency verification',
        '• Personal appearance',
        '• Processing fee',
    ],
    'Barangay Clearance': [
        '• Valid ID of the requestor',
        '• 2x2 photo (2 copies)',
        '• Supporting documents for residency verification',
        '• Processing fee',
        '• Cedula',
    ],
    'Barangay ID': [
        '• Valid ID of the requestor',
        '• 2x2 photo (2 copies)',
        '• Supporting documents for residency verification',
        '• Processing fee',
        '• Birth Certificate',
        '• Personal appearance',
    ],
    'Cedula': [
        '• Valid ID of the requestor',
        '• Tax declaration',
        '• Supporting documents for residency verification',
        '• Processing fee',
        '• Income Statement (if employed)',
        '• Personal appearance',
    ],
    'Certificate of Indigency': [
        '• Valid ID of the requestor',
        '• Proof of low income',
        '• Proof of residency',
        '• Personal appearance',
        '• Processing fee',
    ],
    'Business Permit': [
        '• Valid ID of the requestor (owner)',
        '• Business registration documents',
        '• Supporting documents for residency verification',
        '• Lease Contract (if renting)',
        '• Barangay Clearance',
        '• DTI Registration',
        '• Location Plan',
        '• Processing fee',
        '• Personal appearance',
    ],
    'Building Permit': [
        '• Valid ID of the requestor (owner)',
        '• Building plans (3 copies)',
        '• Lot title or tax declaration',
        '• Supporting documents for residency verification',
        '• Barangay Clearance',
        '• Engineer/s Certification',
        '• Processing fee',
        '• Personal appearance',
    ],
    'Certificate of Good Moral': [
        '• Valid ID of the requestor',
        '• Supporting documents for residency verification',
        '• Processing fee',
        '• Personal appearance',
    ],
}

const documentFields = {
  'Barangay Certificate': [
    { name: 'duration_of_residency', label: 'Duration of Residency (years)', type: 'number', required: false, placeholder: 'Enter number of years', min: 0, step: 1 },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency Verification', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload utility bills, lease contract, or other proof of residency' }
  ],

  'Barangay Clearance': [
    { name: 'clearance_for', label: 'Clearance Type', type: 'select', required: true, placeholder: 'Select clearance purpose', options: ['Employment', 'Business', 'Travel', 'School Admission', 'Government Transaction', 'Other'] },
    { name: '2x2_photo', label: '2x2 Photo (2 copies)', type: 'file', required: true, accept: 'image/*', description: 'Upload 2x2 ID picture (2 copies)' },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload proof of residency documents' },
    { name: 'cedula', label: 'Cedula (if applicable)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload your Cedula document' }
  ],

  'Barangay ID': [
    { name: 'photo', label: '2x2 Photo (2 copies)', type: 'file', required: true, accept: 'image/*', description: 'Upload 2x2 ID picture (2 copies)' },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload proof of residency documents' },
    { name: 'birth_certificate', label: 'Birth Certificate (for first time applicants)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload birth certificate if this is your first application' }
  ],

  'Cedula': [
    { name: 'income_source', label: 'Income Source', type: 'select', required: false, placeholder: 'Select income source', options: ['Employment', 'Business', 'Pension', 'Remittance', 'Other'] },
    { name: 'tax_declaration', label: 'Tax Declaration (if applicable)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload tax declaration document' },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload proof of residency documents' },
    { name: 'income_statement', label: 'Income Statement (if employed)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload income statement or payslip if employed' }
  ],

  'Certificate of Indigency': [
    { name: 'household_members', label: 'Household Member Count', type: 'select', required: false, placeholder: 'Select number of members', options: ['1-2', '3-4', '5-6', '7-8', '9 or more'] },
    { name: 'income_proof', label: 'Proof of Low Income', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload documents proving low income status' },
    { name: 'proof_of_residency', label: 'Proof of Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload utility bills, lease contract, or other proof of residency' }
  ],

  'Business Permit': [
    { name: 'business_name', label: 'Business Name', type: 'text', required: true, placeholder: 'Enter your business name' },
    { name: 'business_type', label: 'Business Type', type: 'select', required: true, placeholder: 'Select business type', options: ['Retail', 'Wholesale', 'Service', 'Manufacturing', 'Food & Beverage', 'Other'] },
    { name: 'dtI_sec_number', label: 'DTI/SEC Registration Number', type: 'text', required: false, placeholder: 'Enter DTI/SEC registration number' },
    { name: 'business_registration', label: 'Business Registration Documents', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload business registration documents' },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload proof of residency documents' },
    { name: 'lease_contract', label: 'Lease Contract (if renting)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload lease contract if business location is rented' },
    { name: 'barangay_clearance', label: 'Barangay Clearance', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload Barangay Clearance document' },
    { name: 'dti_registration', label: 'DTI Registration Document', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload DTI registration certificate' },
    { name: 'location_plan', label: 'Location Plan', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload location plan or site map of business' }
  ],

  'Building Permit': [
    { name: 'building_type', label: 'Building Type', type: 'select', required: true, placeholder: 'Select building type', options: ['Residential', 'Commercial', 'Mixed Use', 'Industrial', 'Institutional', 'Other'] },
    { name: 'building_plans', label: 'Building Plans (3 copies)', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload building plans (3 copies)' },
    { name: 'engineer_cert', label: 'Engineer\'s Certification', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload engineer\'s certification document' },
    { name: 'lot_title', label: 'Lot Title or Tax Declaration', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload lot title or tax declaration' },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload proof of residency documents' },
    { name: 'barangay_clearance', label: 'Barangay Clearance', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload Barangay Clearance document' }
  ],
  
  'Certificate of Good Moral': [
    { name: 'photo', label: '2x2 Photo (2 copies)', type: 'file', required: false, accept: 'image/*', description: 'Upload 2x2 ID picture (2 copies)' },
    { name: 'supporting_documents', label: 'Supporting Documents for Residency', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload proof of residency documents' }
  ],
}

// computed array for current selected doc fields
const currentDocumentFields = computed(() => {
  return documentFields[selectedDocType.value] ?? []
})

// initialize extra_fields when document is chosen to make sure keys exist
const initExtraFieldsForDocument = (docName) => {
  const defs = documentFields[docName] ?? []
  // ensure form.extra_fields exists
  form.extra_fields = form.extra_fields || {}

  defs.forEach((f) => {
    // only initialize if not present
    if (form.extra_fields[f.name] === undefined) {
      if (f.type === 'checkbox') form.extra_fields[f.name] = []
      else if (f.type === 'select') form.extra_fields[f.name] = null 
      else form.extra_fields[f.name] = null
    }
    // clear dynamic file name tracking if none
    if (!dynamicFileNames.value[f.name]) dynamicFileNames.value[f.name] = ''
  })
}

// initialize for default selected
initExtraFieldsForDocument(selectedDocType.value)

// toggle checkbox helper (stores array)
const toggleCheckbox = (fieldName, value, checked) => {
  if (!Array.isArray(form.extra_fields[fieldName])) {
    form.extra_fields[fieldName] = []
  }
  const idx = form.extra_fields[fieldName].indexOf(value)
  if (checked && idx === -1) form.extra_fields[fieldName].push(value)
  if (!checked && idx !== -1) form.extra_fields[fieldName].splice(idx, 1)
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
const navigateToNotifications = () => { activeTab.value = 'notifications'; router.visit(route('notification_request_employee')) }
const openFAQ = () => {
    router.visit(route('help_center_employee'))
}

const selectDocument = (docType) => {
    selectedDocType.value = docType
}

const proceedToForm = () => {
  initExtraFieldsForDocument(selectedDocType.value)
  currentView.value = 'form'
}

const backToSelection = () => {
  currentView.value = 'selection'
  // Clear purpose fields when going back
  form.purpose = ''
  purposeOthers.value = ''
}

// handle ID front/back upload triggers
const triggerFileUpload = (side) => {
  if (side === 'front' && fileFrontInput.value && typeof fileFrontInput.value.click === 'function') {
    fileFrontInput.value.click()
  } else if (side === 'back' && fileBackInput.value && typeof fileBackInput.value.click === 'function') {
    fileBackInput.value.click()
  } else {
    console.warn('file input ref not available yet for', side)
  }
}

const handleFileUpload = (event, side) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (side === 'front') {
    idFrontName.value = file.name
    form.id_front = file

    // IMPORTANT: assign the front-uploaded file to the backend-target key
    // so Inertia will send it as "valid_id_content" in FormData.
    form.valid_id_content = file
  } else if (side === 'back') {
    idBackName.value = file.name
    form.id_back = file

    // do NOT overwrite valid_id_content when uploading the back; front must remain the source
    // (If front wasn't uploaded, you can fallback - see below in submit)
    if (!form.valid_id_content) {
      // fallback only if front wasn't provided
      form.valid_id_content = file
    }
  }
}

// handle dynamic file upload
const triggerDynamicFileUpload = (fieldName) => {
  // Find the file input by its data attribute
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input && typeof input.click === 'function') {
    input.click()
  } else {
    console.error('Could not find file input for field:', fieldName)
    // Fallback: try to find by searching all file inputs
    const allInputs = Array.from(document.querySelectorAll('input[type="file"]'))
    const targetInput = allInputs.find(inp => {
      // Check if this input is in a container that matches the field name
      const container = inp.closest('.file-upload-item')
      if (container) {
        const label = container.querySelector('.file-upload-label')
        // This is a last resort - not ideal but might work
        return label && label.textContent.includes(fieldName)
      }
      return false
    })
    if (targetInput) {
      targetInput.click()
    }
  }
}

const handleDynamicFileUpload = (event, fieldName) => {
  const file = event.target.files[0]
  if (!file) return
  // store filename for UI
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: file.name }
  // attach file into extra_fields — Inertia will convert to FormData with forceFormData
  form.extra_fields = { ...form.extra_fields, [fieldName]: file }
  console.log('attached dynamic file', fieldName, file.name)
}

const removeDynamicFile = (fieldName) => {
  // Remove file from form
  form.extra_fields = { ...form.extra_fields, [fieldName]: null }
  // Clear filename display
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: '' }
  // Reset file input
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input) input.value = ''
}

const testing = true  // keep while you test — set false when you want strict validation

// Submit handler — sends form.extra_fields as JSON (or file objects — Inertia's FormData handles it)
const submitRequest = () => {
  if (!testing) {
    // dynamic required fields
    const missing = currentDocumentFields.value
      .filter(f => f.required)
      .find(f => {
        const val = form.extra_fields?.[f.name]
        if (f.type === 'file') return !val
        if (f.type === 'checkbox') return !Array.isArray(val) || val.length === 0
        return !val
      })
    if (missing) {
      alert(`Please provide: ${missing.label}`)
      return
    }

    if (!form.first_name || !form.last_name) {
      alert('Please fill in your first and last name.')
      return
    }

    // if user chose an ID type, require both front and back (change to require only front if you'd like)
    if (form.id_type && (!form.id_front || !form.id_back)) {
      alert('Please upload both the front and back of your selected ID.')
      return
    }

    // require id number when id_type selected
    if (form.id_type && !form.id_number) {
      alert(`Please enter ${idNumberLabel.value}`)
      return
    }
  }

  // ensure backend receives a fallback single-file field if it expects 'document'
  if (!form.document && form.id_front) {
      form.document = form.id_front
  }

  // Map the visible UI id_number to the backend field valid_id_number
  form.valid_id_number = form.id_number || ''

  // Ensure valid_id_content is the front file if available (safety fallback)
  if (form.id_front) {
    form.valid_id_content = form.id_front
  }

  form.document_name = selectedDocType.value

  if (isSubmitting.value) return
  isSubmitting.value = true

  // Manually construct FormData to ensure all files are properly sent
  const formData = new FormData()
  
  // Append all scalar fields
  const scalarFields = [
    'document_name', 'fk_document_type_id', 'last_name', 'first_name', 'middle_name', 'suffix',
    'birthdate', 'sex', 'civil_status', 'address', 'contact_number', 'purpose',
    'id_type', 'id_number', 'valid_id_number', 'pickup_item', 'pickup_location', 'pickup_start', 'pickup_end', 'person_to_look'
  ]
  
  scalarFields.forEach(key => {
    const value = form[key]
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  
  // Append ID files (valid_id_content, id_front, id_back)
  if (form.valid_id_content instanceof File) {
    formData.append('valid_id_content', form.valid_id_content)
  }
  if (form.id_front instanceof File) {
    formData.append('id_front', form.id_front)
  }
  if (form.id_back instanceof File) {
    formData.append('id_back', form.id_back)
  }
  if (form.document instanceof File) {
    formData.append('document', form.document)
  }
  
  // Append extra_fields: files, arrays, or scalars
  if (form.extra_fields && typeof form.extra_fields === 'object') {
    Object.keys(form.extra_fields).forEach(key => {
      const val = form.extra_fields[key]
      if (val === null || val === undefined || val === '') return
      
      if (val instanceof File) {
        // Single file - append as extra_fields[fieldName]
        formData.append(`extra_fields[${key}]`, val)
        console.log(`Appending file: extra_fields[${key}] =`, val.name)
      } else if (Array.isArray(val)) {
        // Arrays (e.g., checkbox values)
        val.forEach(v => {
          if (v !== null && v !== undefined && v !== '') {
            formData.append(`extra_fields[${key}][]`, v)
          }
        })
      } else {
        // Scalar values
        formData.append(`extra_fields[${key}]`, val)
      }
    })
  }
  
  console.log('FormData entries:')
  for (let pair of formData.entries()) {
    console.log(pair[0], ':', pair[1] instanceof File ? `File: ${pair[1].name}` : pair[1])
  }
  
  // Use axios to send FormData directly
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  
  window.axios.post(route('requests.store'), formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
    },
    onUploadProgress: (progressEvent) => {
      if (progressEvent.total) {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        console.log('Upload progress:', percentCompleted + '%')
      }
    }
  }).then(response => {
    // Extract ticket from response
    const ticket = response.data?.ticket || response.data?.data?.ticket || 'TICKET_PENDING'
    requestNumber.value = ticket
    currentView.value = 'success'
    isSubmitting.value = false
    console.log('Request submitted successfully, ticket:', ticket)
  }).catch(error => {
    console.error('Error submitting request:', error)
    console.error('Error response:', error.response?.data)
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const firstError = Object.values(errors)[0]
      alert(Array.isArray(firstError) ? firstError[0] : firstError)
    } else if (error.response?.data?.message) {
      alert(error.response.data.message)
    } else {
      alert('Failed to submit request. Please try again. ' + (error.message || ''))
    }
    isSubmitting.value = false
  })
}

const viewRequest = () => {
    currentView.value = 'selection'
    // Navigate to notifications or request details page
    router.visit(route('notification_request_employee'))
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

.header-logo {
    width: 180px;
    height: 60px;
    padding: 1px;
}

.header-actions {
    position: relative;
}

.settings-btn-img {
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
    font-size: 17px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.profile-info {
    flex: 1;
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
    gap: 10px;
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
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
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
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    display: block;
    margin-left: auto;
    margin-top: 20px;
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
    color: #000;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.2s;
}

.back-btn:hover {
    color: #333;
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-bottom: 30px;
}

.form-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dynamic-fields {
    background: white;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.section-title .icon {
    font-size: 20px;
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
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.form-input:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
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
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-textarea:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
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
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.upload-row {
    display: flex;
    gap: 12px;
    margin-left: 10px;
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

/* Dynamic Fields Styling */
.dynamic-field-wrapper {
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dynamic-field-wrapper:hover {
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.15);
    transform: translateY(-1px);
}

.field-header {
    margin-bottom: 12px;
}

.field-label {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #333;
    margin-bottom: 6px;
}

.required-star {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
    font-weight: 700;
}

.section-title .required-star {
    color: #e74c3c;
    font-size: 18px;
    margin-left: 4px;
    font-weight: 700;
}

.optional-text {
    color: #999;
    font-size: 13px;
    font-weight: 500;
    margin-left: 8px;
}

.field-description {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
    line-height: 1.5;
    font-style: italic;
}

.field-input-wrapper {
    width: 100%;
}

.form-input,
.form-textarea,
select {
    width: 100%;
    max-width: 100%;
}

select option[value=""] {
    color: #888;
}

/* File Upload Styling */
.file-upload-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* File Uploads Grid Layout */
.file-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.file-upload-item {
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.file-upload-item:hover {
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.15);
    transform: translateY(-1px);
}

.file-upload-header {
    margin-bottom: 10px;
}

.file-upload-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
}

.file-upload-description {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
    line-height: 1.4;
    font-style: italic;
}

.file-upload-controls {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.uploaded-file-info-compact {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-radius: 6px;
    border: 1px solid #4caf50;
    box-shadow: 0 1px 4px rgba(76, 175, 80, 0.2);
    font-size: 12px;
}

.file-name-compact {
    flex: 1;
    color: #2e7d32;
    font-weight: 600;
    font-size: 12px;
    word-break: break-all;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.upload-btn-dynamic {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    justify-content: center;
    width: auto;
    min-width: 100px;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

.upload-btn-dynamic:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.upload-icon {
    font-size: 18px;
}

.file-checkmark {
    color: #4caf50;
    font-size: 18px;
    font-weight: 700;
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

/* Checkbox Group Styling */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    transform: translateX(3px);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff8c42;
}

.checkbox-label span {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

.id-number-field {
    margin-top: 12px;
}

.id-number-field .field-label {
    margin-bottom: 8px;
}

.submit-btn {
    width: 30%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: block;
    margin-left: auto;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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