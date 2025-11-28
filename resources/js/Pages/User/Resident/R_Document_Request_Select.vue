<template>
    <Head>
        <title>Document Request</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
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
                    <img src="/assets/DEFAULT.jpg" alt="Profile" class="profile-avatar" />
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
                            ◀ BACK TO DOCUMENTS
                        </button>

                        <h3 class="form-title">REQUEST FORM</h3>

                        <div class="form-sections">
                            <!-- Proof of Intent -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <span class="icon">📝</span> Reason for Request *
                                </h4>
                                <textarea 
                                    v-model="form.purpose" 
                                    placeholder="What is the purpose of your request?"
                                    class="form-textarea"
                                    rows="5"
                                ></textarea>
                                
                                <div class="upload-section">
                                    <!-- ID Type select -->
                                    <select v-model="form.id_type" class="form-input upload-select">
                                        <option value="">Type of Identification/Document</option>
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
                                    <span class="icon">📎</span> Additional Information *
                                </h4>

                                <div v-for="field in currentDocumentFields" :key="field.name" class="dynamic-field">
                                    <label class="field-label">{{ field.label }} <span v-if="field.required">*</span></label>

                                    <!-- text -->
                                    <input 
                                        v-if="field.type === 'text'"
                                        v-model="form.extra_fields[field.name]"
                                        :placeholder="field.placeholder || ''"
                                        class="form-input short-input"
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
                                        class="form-input short-input"
                                        :required="field.required"
                                    />

                                    <!-- number -->
                                    <input
                                        v-if="field.type === 'number'"
                                        type="number"
                                        v-model.number="form.extra_fields[field.name]"
                                        :placeholder="field.placeholder || ''"
                                        class="form-input short-input"
                                        :required="field.required"
                                    />

                                    <!-- select -->
                                    <select
                                        v-if="field.type === 'select'"
                                        v-model="form.extra_fields[field.name]"
                                        class="form-input short-input"
                                        :required="field.required"
                                    >
                                        <option value="">{{ field.placeholder || 'Select' }}</option>
                                        <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>

                                    <!-- checkbox group -->
                                    <div v-if="field.type === 'checkbox'">
                                        <label v-for="opt in field.options" :key="opt" class="checkbox-label short-input">
                                            <input
                                                type="checkbox"
                                                :value="opt"
                                                @change="toggleCheckbox(field.name, opt, $event.target.checked)"
                                                :checked="Array.isArray(form.extra_fields[field.name]) && form.extra_fields[field.name].includes(opt)"
                                            />
                                            {{ opt }}
                                        </label>
                                    </div>

                                    <!-- file -->
                                    <div v-if="field.type === 'file'">
                                        <button class="upload-btn" @click.prevent="triggerDynamicFileUpload(field.name)">UPLOAD {{ field.label }}</button>
                                        <input
                                            type="file"
                                            :ref="`dynFile_${field.name}`"
                                            style="display: none"
                                            @change="handleDynamicFileUpload($event, field.name)"
                                            :accept="field.accept || 'image/*,.pdf'"
                                        />
                                        <p v-if="dynamicFileNames[field.name]">Uploaded: {{ dynamicFileNames[field.name] }}</p>
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
import { Link, usePage } from '@inertiajs/vue3'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

// Inertia-shared auth user
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// role map (unchanged)
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
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  return id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
})

const showSettings = ref(false)
const activeTab = ref('documents')
const currentView = ref('selection')
const selectedDocType = ref('Barangay Certificate')
const requestNumber = ref('')
const isSubmitting = ref(false)

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
  purpose: '',
  id_type: '',
  id_number: '',
  id_front: null,
  id_back: null,
  document: null, // other uploaded doc(s)
  extra_fields: {} // dynamic per-document fields (will be JSON on the backend)
})

// keep document_name synced with selectedDocType
watch(selectedDocType, (val) => {
  form.document_name = val
  initExtraFieldsForDocument(val)
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

// Document lists and descriptions (unchanged)
const documentNames = [
    'Barangay Certificate',
    'Barangay Clearance',
    'Barangay ID',
    'Cedula',
    'Certificate of Residency',
    'Certificate of Indigency',
    'Business Permit',
    'Building Permit',
    'Certificate of Good Moral',
    'First Time Job Seeker Certificate'
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
    
    'Certificate of Good Moral': 'Ang Certificate of Good Moral ay sertipikasyon na nagpapatunay na ang isang residente ay may mabuting asal at walang record ng maling gawa sa loob ng barangay. Ito ay kailangan para sa employment, school admission, at iba pang professional requirements.',
    
    'First Time Job Seeker Certificate': 'Ang First Time Job Seeker Certificate ay sertipikasyon na kailangan ng mga bagong graduates o first-time job applicants. Ito ay exemption mula sa Documentary Stamp Tax para sa kanilang unang trabaho, ayon sa batas.',
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
    ]
}

/*
documentFields maps each documentName to an array of field definitions.
Field definition structure:
{
  name: 'field_key',            // key stored under form.extra_fields[field_key]
  label: 'Label shown to user',
  type: 'text'|'textarea'|'date'|'select'|'number'|'checkbox'|'file',
  required: true|false,
  options: [ ... ]              // for select / checkbox
  placeholder: '...'
  accept: 'image/*,.pdf'        // for file inputs
}
*/
const documentFields = {
  'Barangay Certificate': [
    { name: 'duration_of_residency', label: 'Duration of Residency (years)', type: 'number', required: false }
  ],

  'Barangay Clearance': [
    { name: '2x2_photo', label: '2x2 Photo (2 copies)', type: 'file', required: true, accept: 'image/*' },
    { name: 'clearance_for', label: 'Clearance Type', type: 'select', required: true, options: ['Employment', 'Business', 'Travel', 'Other'] }
  ],

  'Barangay ID': [
    { name: 'photo', label: 'Photo (2x2)', type: 'file', required: true, accept: 'image/*' },
    { name: 'id_purpose', label: 'Purpose of ID', type: 'text', required: false }
  ],

  'Cedula': [
    { name: 'income_source', label: 'Income Source', type: 'text', required: false },
    { name: 'tax_declaration', label: 'Tax Declaration (if applicable)', type: 'file', required: false, accept: '.pdf' }
  ],

  'Certificate of Residency': [
    { name: 'proof_of_residency', label: 'Proof of Residency', type: 'file', required: true, accept: 'image/*,.pdf' },
    { name: 'residency_since', label: 'Resident Since (date)', type: 'date', required: false }
  ],

  'Certificate of Indigency': [
    { name: 'income_proof', label: 'Proof of Low Income', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'household_members', label: 'Household Member Count', type: 'number', required: false }
  ],

  'Business Permit': [
    { name: 'business_name', label: 'Business Name', type: 'text', required: true },
    { name: 'owner_id', label: 'Owner Valid ID', type: 'file', required: true, accept: 'image/*,.pdf' },
    { name: 'dtI_sec_number', label: 'DTI/SEC Registration #', type: 'text', required: false }
  ],

  'Building Permit': [
    { name: 'building_plans', label: 'Building Plans (3 copies)', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'engineer_cert', label: 'Engineer Certification', type: 'file', required: false, accept: '.pdf' },
    { name: 'lot_title', label: 'Lot Title or Tax Declaration', type: 'file', required: true, accept: '.pdf,image/*' }
  ],

  'Barangay Protection Order': [
    { name: 'affidavit', label: 'Affidavit of Complaint', type: 'file', required: true, accept: '.pdf' },
    { name: 'medical_certificate', label: 'Medical Certificate (if any)', type: 'file', required: false, accept: '.pdf,image/*' },
    { name: 'police_report', label: 'Police Report (if filed)', type: 'file', required: false, accept: '.pdf' }
  ],

  'Certificate of Good Moral': [
    { name: 'purpose_of_request', label: 'Purpose of Request', type: 'text', required: true },
    { name: 'photo', label: '2x2 Photo (2 copies)', type: 'file', required: false, accept: 'image/*' }
  ],

  'First Time Job Seeker Certificate': [
    { name: 'school_diploma', label: 'School Diploma / TOR', type: 'file', required: true, accept: '.pdf' },
    { name: 'birth_cert', label: 'Birth Certificate', type: 'file', required: true, accept: '.pdf,image/*' }
  ],

  'Certificate for Solo Parent': [
    { name: 'proof_solo_parent', label: 'Proof of Solo Parent Status', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'child_birth_cert', label: 'Child Birth Certificate', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Certificate for PWD': [
    { name: 'medical_cert', label: 'Medical Certificate from Physician', type: 'file', required: true, accept: '.pdf' },
    { name: 'pwD_type', label: 'Type of Disability', type: 'text', required: false }
  ],

  'Certificate for Senior Citizen': [
    { name: 'proof_age', label: 'Proof of Age (Birth cert / valid ID)', type: 'file', required: true, accept: '.pdf,image/*' }
  ],

  'Certificate of No Pending Case': [
    { name: 'purpose_of_request', label: 'Purpose of Request', type: 'text', required: true },
    { name: 'additional_notes', label: 'Additional Notes', type: 'textarea', required: false }
  ],

  'Demolition Permit': [
    { name: 'demolition_plan', label: 'Demolition Plan', type: 'file', required: true, accept: '.pdf' },
    { name: 'neighbor_consent', label: 'Notarized Consent from Neighbors', type: 'file', required: true, accept: '.pdf' },
    { name: 'contractor_cert', label: 'Contractor\'s Certification', type: 'file', required: true, accept: '.pdf' }
  ]
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
  } else if (side === 'back') {
    idBackName.value = file.name
    form.id_back = file
  }
  // if no id_type selected yet, leave it to user to choose; id-number field appears after both conditions met
}

// handle dynamic file upload (unchanged)
const triggerDynamicFileUpload = (fieldName) => {
  const refName = `dynFile_${fieldName}`
  const el = (refsSafe()[refName])
  if (el && typeof el.click === 'function') {
    el.click()
  } else {
    // if not yet available, try using $refs via DOM query
    const input = document.querySelector(`input[ref="dynFile_${fieldName}"]`)
    if (input) input.click()
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

// small helper to access refs in <script setup>
const refsSafe = () => {
  const res = {}
  currentDocumentFields.value.forEach((f) => {
    const dom = document.querySelector(`input[ref="dynFile_${f.name}"]`)
    if (dom) res[`dynFile_${f.name}`] = dom
  })
  return res
}

// other UI navigation & settings (unchanged)
const toggleSettings = () => { showSettings.value = !showSettings.value }
const closeSettings = () => { showSettings.value = false }
const logout = () => { showSettings.value = false; router.visit(route('login')) }
const setActiveTab = (tab) => { activeTab.value = tab }
const navigateToPosts = () => { activeTab.value = 'posts'; router.visit(route('announcement_resident')) }
const navigateToEvents = () => { activeTab.value = 'events'; router.visit(route('event_assistance_resident')) }
const navigateToProfile = () => { activeTab.value = 'profile'; router.visit(route('profile_resident')) }
const navigateToNotifications = () => { activeTab.value = 'notifications'; router.visit(route('notification_request_resident')) }
const openFAQ = () => { router.visit(route('help_center_resident')) }

const selectDocument = (docType) => {
  selectedDocType.value = docType
}

const proceedToForm = () => {
  initExtraFieldsForDocument(selectedDocType.value)
  currentView.value = 'form'
}

const backToSelection = () => {
  currentView.value = 'selection'
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

  form.document_name = selectedDocType.value

  if (isSubmitting.value) return
  isSubmitting.value = true

  form.post(route('requests.store'), {
    forceFormData: true,
    onStart: () => {
      console.log('Submitting request; keys:', Object.keys(form))
    },
    onSuccess: (page) => {
      // extract ticket from many variations
      const extractTicket = (p) => {
        try {
          const candidates = [
            p?.props?.flash?.ticket,
            p?.props?.value?.flash?.ticket,
            p?.props?.ticket,
            p?.props?.value?.ticket,
            p?.ticket,
            p?.data?.ticket,
            usePage()?.props?.flash?.ticket,
            usePage()?.props?.ticket
          ];
          for (const c of candidates) {
            if (c !== undefined && c !== null && c !== '') return c;
          }
        } catch (e) {}
        return null;
      };

      const ticket = extractTicket(page);
      requestNumber.value = ticket ?? 'TICKET_PENDING';
      currentView.value = 'success';
      isSubmitting.value = false;
    },
    onError: (errs) => {
      console.log('Validation errors', errs)
      alert('There were validation errors. Check the form.')
      isSubmitting.value = false
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

const viewRequest = () => {
  currentView.value = 'selection'
  alert(`Viewing request #${requestNumber.value}`)
  router.visit(route('notification_request_resident'))
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  activeTab.value = 'documents'

  // try to read flashed ticket
  try {
    const flashedTicket = page?.props?.value?.flash?.ticket ?? page?.props?.flash?.ticket ?? page?.props?.value?.ticket ?? page?.props?.ticket ?? null
    if (flashedTicket) {
      requestNumber.value = flashedTicket
      currentView.value = 'success'
    }
  } catch (e) {}
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

.dynamic-field {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 1rem;
}

.field-label {
    width: 220px;            /* adjust depending on design */
    margin: 0;
    font-weight: 600;
    white-space: nowrap;
}

.form-input,
.form-textarea,
select {
    flex: 1;                 /* makes all inputs equal width */
}

.upload-btn {
    flex-shrink: 0;          /* keep upload button from stretching */
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
.short-input {
    width: 200px;     /* adjust to any size you like */
    max-width: 250px;
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
    transition: all 0.3s;
    margin-left: 825px;  /* push right */
    margin-top: 10px;
}

.request-btn:hover {
    transform: translateY(-2px);
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
    color: #000000;
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
    background: linear-gradient(135deg, #2bb24a, #2bb24a);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;

}
.upload-row {
    display: flex;
    gap: 12px;           /* <— controls spacing between the buttons */
    margin-left: 10px;   /* optional: space away from the input field */
}

.upload-btn:hover {
    transform: translateY(-2px);
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
    width: 30%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 10px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: block;       /* Make it block-level */
    margin-left: auto;    /* Pushes it to the right */
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