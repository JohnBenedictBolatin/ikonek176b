<template>
  <Head>
    <title>Event Assistance</title>
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
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'posts' }" @click="navigateToPosts">
            📋 Posts
          </Link>
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'documents' }" @click="navigateToDocuments">
            📄 Document Request
          </Link>
          <Link href="#" class="nav-item active" :class="{ active: activeTab === 'events' }" @click="setActiveTab('events')">
            🤝 Event Assistance
          </Link>
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'notifications' }" @click="navigateToNotifications">
            🔔 Notifications
          </Link>
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'profile' }" @click="navigateToProfile">
            👤 Profile
          </Link>
        </div>

        <button class="faq-btn" @click="openFAQ">❓ FAQs & Help Center</button>
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

              <button class="request-btn" @click="proceedToForm">REQUEST ASSISTANCE</button>
            </div>
          </div>

          <!-- View 2: Request Form -->
          <div v-if="currentView === 'form'" class="request-form-container">
            <button class="back-btn" @click="backToSelection">◀ BACK TO EVENTS</button>

            <h3 class="form-title">REQUEST FORM</h3>

            <div class="form-sections">
              <!-- Event Details -->
              <div class="form-section">
                <h4 class="section-title"><span class="icon">📅</span> Event Details</h4>
                <div class="form-grid">
                  <input type="date" v-model="form.event_date" class="form-input" />
                  <input type="time" v-model="form.event_time" class="form-input" />
                  <input type="number" v-model.number="form.duration" placeholder="Duration (hours)" class="form-input" />
                  <input type="number" v-model.number="form.attendees" placeholder="Expected Attendees" class="form-input" />
                </div>
              </div>

              <!-- Purpose and Documents -->
              <div class="form-section">
                <h4 class="section-title"><span class="icon">📝</span> Purpose and Supporting Documents</h4>

                <select v-model="form.purpose" class="form-input">
                  <option value="" disabled>Select purpose of request</option>
                  <option v-for="(opt, idx) in purposeOptions" :key="idx" :value="opt">{{ opt }}</option>
                  <option value="Other">Other</option>
                </select>

                <div v-if="form.purpose === 'Other'" class="form-grid">
                  <input type="text" v-model="form.others" placeholder="Specify purpose" class="form-input full-width" />
                </div>

                <input type="text" v-model="form.location" placeholder="Event Location (optional)" class="form-input" />

                <div class="upload-section">
                  <select v-model="form.document_type" class="form-input upload-select">
                    <option value="">Type of Supporting Document</option>
                    <option value="Valid ID">Valid ID</option>
                    <option value="Death Certificate">Death Certificate</option>
                    <option value="Medical Certificate">Medical Certificate</option>
                    <option value="Authorization Letter">Authorization Letter</option>
                    <option value="Barangay Clearance">Barangay Clearance</option>
                    <option value="Other">Other Documents</option>
                  </select>

                  <button class="upload-btn" @click="triggerMainFileUpload">UPLOAD</button>
                  <input
                    type="file"
                    ref="mainFileInput"
                    @change="handleMainFileUpload"
                    class="file-input-hidden"
                    accept="image/*,.pdf"
                    multiple
                    style="display:none"
                  />
                </div>

                <div v-if="mainUploadedNames.length > 0" class="uploaded-files-list">
                  <p class="uploaded-label">Uploaded Files:</p>
                  <div v-for="(file, index) in mainUploadedNames" :key="index" class="uploaded-file-item">
                    <span>{{ file }}</span>
                    <button class="remove-file" @click="removeMainFile(index)">✕</button>
                  </div>
                </div>
              </div>

              <!-- Dynamic fields for selected event type -->
              <div v-if="currentEventFields.length" class="form-section">
                <h4 class="section-title"><span class="icon">📎</span> Event-specific Information</h4>

                <div v-for="field in currentEventFields" :key="field.name" class="dynamic-field">
                  <label class="field-label">{{ field.label }} <span v-if="field.required">*</span></label>

                  <input v-if="field.type === 'text'" v-model="form.extra_fields[field.name]" :placeholder="field.placeholder || ''" class="form-input" :required="field.required" />

                  <textarea v-if="field.type === 'textarea'" v-model="form.extra_fields[field.name]" :placeholder="field.placeholder || ''" rows="3" class="form-textarea" :required="field.required"></textarea>

                  <input v-if="field.type === 'date'" type="date" v-model="form.extra_fields[field.name]" :max="today" class="form-input" :required="field.required" />

                  <input v-if="field.type === 'number'" type="number" v-model.number="form.extra_fields[field.name]" :placeholder="field.placeholder || ''" class="form-input" :required="field.required" />

                  <select v-if="field.type === 'select'" v-model="form.extra_fields[field.name]" class="form-input" :required="field.required">
                    <option value="">{{ field.placeholder || 'Select' }}</option>
                    <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                  </select>

                  <div v-if="field.type === 'checkbox'">
                    <label v-for="opt in field.options" :key="opt" class="checkbox-label">
                      <input type="checkbox" :value="opt" @change="toggleCheckbox(field.name, opt, $event.target.checked)" :checked="Array.isArray(form.extra_fields[field.name]) && form.extra_fields[field.name].includes(opt)" />
                      {{ opt }}
                    </label>
                  </div>

                  <!-- file dynamic field -->
                  <div v-if="field.type === 'file'">
                    <button class="upload-btn" @click.prevent="triggerDynamicFileUpload(field.name)">UPLOAD {{ field.label }}</button>
                    <input
                      type="file"
                      :ref="`dynFile_${field.name}`"
                      style="display:none"
                      @change="handleDynamicFileUpload($event, field.name)"
                      :accept="field.accept || 'image/*,.pdf'"
                    />
                    <p v-if="dynamicFileNames[field.name]">Uploaded: {{ dynamicFileNames[field.name] }}</p>
                  </div>
                </div>
              </div>
            </div>

            <button class="submit-btn" :disabled="isSubmitting" @click="submitRequest">
              <span v-if="!isSubmitting">SUBMIT REQUEST</span>
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
                <p>You have successfully submitted your request for <span class="highlight">{{ selectedEventType }}</span>.</p>
                <p>Your request is now being reviewed by the barangay officials. We will contact you within 2-3 business days regarding the status of your application.</p>
                <p>Your reference number is <span class="highlight">#{{ requestNumber }}</span>. Please keep this for tracking purposes.</p>
              </div>

              <button class="view-request-btn" @click="viewRequest">VIEW REQUEST</button>
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
import axios from 'axios'

// ensure axios uses X-Requested-With and CSRF (Laravel default)
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
if (csrf) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf

// --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// role map
const roleMap = {
  1: 'Resident', 2: 'Barangay Captain', 3: 'Barangay Secretary',
  4: 'Barangay Treasurer', 5: 'Barangay Kagawad',
  6: 'Sangguniang Kabataan Chairman', 7: 'Sangguniang Kabataan Kagawad',
  9: 'System Admin'
}
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  return id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
})

// UI state
const showSettings = ref(false)
const activeTab = ref('events')
const currentView = ref('selection')
const selectedEventType = ref('Court Reservation')
const requestNumber = ref('')
const isSubmitting = ref(false)

// main file input ref + names
const mainFileInput = ref(null)
const mainUploadedNames = ref([])

// dynamic file names container
const dynamicFileNames = ref({})

// basic helper date
const today = new Date().toISOString().split('T')[0]

// PURPOSE options
const purposeOptions = [
  'Personal Celebration', 'Sports Activity', 'Barangay Escort', 'Community Event', 'Religious or Cultural Activity', 'Logistical Support'
]

// Use Inertia form so nested file uploads are handled (forceFormData true on post)
const form = useForm({
  event_type: selectedEventType.value,
  purpose: '',
  others: '',
  location: '',
  event_date: '',
  event_time: '',
  start_datetime: null,
  duration: null,
  end_datetime: null,
  attendees: null,
  document_type: '',
  documents: [],         // main uploaded files array (files will be attached)
  extra_fields: {}       // per-event dynamic fields; can contain file objects too
})

// keep event_type synced
watch(selectedEventType, (v) => {
  form.event_type = v
  initExtraFieldsForEvent(v)
})

// sample event list, descriptions and requirements (kept from original)
const eventNames = [
  'Court Reservation', 'Covered Court Event', 'Community Hall Reservation', 'Medical Mission Assistance',
  'Feeding Program Request', 'Sports Equipment Lending', 'Sound System Rental', 'Tent and Tables Rental', 'Ambulance Service Request',
  'Disaster Relief Assistance', 'Livelihood Program Application'
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

// Define dynamic per-event fields (similar structure to previous docs mapping)
const eventFields = {
  'Funeral Assistance': [
    { name: 'death_certificate', label: 'Death Certificate', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'relation_to_deceased', label: 'Relation to Deceased', type: 'text', required: true },
    { name: 'funeral_service_contract', label: 'Funeral Service Contract (if any)', type: 'file', required: false, accept: '.pdf' }
  ],

  'Court Reservation': [
    { name: 'waiver_form', label: 'Waiver Form', type: 'file', required: true, accept: '.pdf' },
    { name: 'tournament_type', label: 'Tournament Type', type: 'select', required: false, options: ['Basketball', 'Volleyball', 'Badminton', 'Other'], placeholder: 'Select tournament type' },
    { name: 'security_deposit_amount', label: 'Security Deposit Amount (PHP)', type: 'number', required: false }
  ],

  'Covered Court Event': [
    { name: 'event_proposal', label: 'Event Proposal / Details', type: 'file', required: true, accept: '.pdf' },
    { name: 'organizer_contact', label: 'Organizer Contact Number', type: 'text', required: true }
  ],

  'Community Hall Reservation': [
    { name: 'purpose_letter', label: 'Purpose of Use Letter', type: 'file', required: true, accept: '.pdf' },
    { name: 'expected_guests', label: 'Expected Guests', type: 'number', required: false }
  ],

  'Medical Mission Assistance': [
    { name: 'medical_records', label: 'Medical Records (if available)', type: 'file', required: false, accept: '.pdf,image/*' },
    { name: 'target_group', label: 'Target Group', type: 'select', required: true, options: ['Children', 'Adults', 'Senior Citizens', 'All'], placeholder: 'Select target group' }
  ],

  'Feeding Program Request': [
    { name: 'organization_certificate', label: 'Organization Certificate', type: 'file', required: false, accept: '.pdf' },
    { name: 'list_of_beneficiaries', label: 'List of Beneficiaries (file)', type: 'file', required: true, accept: '.pdf' }
  ],

  'Sports Equipment Lending': [
    { name: 'equipment_list', label: 'Equipment List (what you want to borrow)', type: 'textarea', required: true },
    { name: 'deposit_receipt', label: 'Deposit Receipt', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Sound System Rental': [
    { name: 'rental_hours', label: 'Number of Hours', type: 'number', required: true },
    { name: 'technician_request', label: 'Need Technician?', type: 'select', required: false, options: ['Yes','No'] }
  ],

  'Tent and Tables Rental': [
    { name: 'quantity_needed', label: 'Quantity Needed (tents/tables/chairs)', type: 'text', required: true },
    { name: 'rental_deposit', label: 'Rental Deposit Proof', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Ambulance Service Request': [
    { name: 'patient_name', label: 'Patient Name', type: 'text', required: true },
    { name: 'medical_certificate', label: 'Medical Certificate / Referral', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Disaster Relief Assistance': [
    { name: 'incident_report', label: 'Incident Report / BLOTTER', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'photos_of_damage', label: 'Photos of Damage', type: 'file', required: false, accept: 'image/*' }
  ],

  'Livelihood Program Application': [
    { name: 'livelihood_proposal', label: 'Livelihood Proposal', type: 'file', required: true, accept: '.pdf' },
    { name: 'proof_low_income', label: 'Proof of Low Income (if required)', type: 'file', required: false, accept: '.pdf,image/*' }
  ]
}

// computed fields for current event
const currentEventFields = computed(() => eventFields[selectedEventType.value] ?? [])

// initialize extra_fields keys so UI bindings are ready
const initExtraFieldsForEvent = (eventName) => {
  form.extra_fields = form.extra_fields || {}
  const defs = eventFields[eventName] ?? []
  defs.forEach(f => {
    if (form.extra_fields[f.name] === undefined) {
      form.extra_fields[f.name] = f.type === 'checkbox' ? [] : null
    }
    if (!dynamicFileNames.value[f.name]) dynamicFileNames.value[f.name] = ''
  })
}

// initialize default selected
initExtraFieldsForEvent(selectedEventType.value)

// checkbox toggle helper
const toggleCheckbox = (fieldName, value, checked) => {
  if (!Array.isArray(form.extra_fields[fieldName])) form.extra_fields[fieldName] = []
  const idx = form.extra_fields[fieldName].indexOf(value)
  if (checked && idx === -1) form.extra_fields[fieldName].push(value)
  if (!checked && idx !== -1) form.extra_fields[fieldName].splice(idx, 1)
}

// main file uploads handlers (multiple)
const triggerMainFileUpload = () => {
  if (mainFileInput.value && typeof mainFileInput.value.click === 'function') mainFileInput.value.click()
}
const handleMainFileUpload = (e) => {
  const files = Array.from(e.target.files || [])
  // store file objects in form.documents array (Inertia will package them when forceFormData is used)
  form.documents = form.documents || []
  files.forEach(f => {
    form.documents.push(f)
    mainUploadedNames.value.push(f.name)
  })
}

// remove main file
const removeMainFile = (index) => {
  form.documents.splice(index, 1)
  mainUploadedNames.value.splice(index, 1)
}

// dynamic file handlers for per-event fields
const triggerDynamicFileUpload = (fieldName) => {
  // attempt to access dynamic input via document query (safe fallback)
  const sel = document.querySelector(`input[ref="dynFile_${fieldName}"], input[ref='dynFile_${fieldName}']`)
  // But template refs are set as attributes, so we query by attribute
  const input = sel || document.querySelector(`input[ref="dynFile_${fieldName}"]`)
  // find the actual element by looking for input with attribute ref equal to name
  const els = document.getElementsByTagName('input')
  let found = null
  for (const el of els) {
    if (el.getAttribute('ref') === `dynFile_${fieldName}`) { found = el; break }
  }
  (found || input)?.click()
}

const handleDynamicFileUpload = (e, fieldName) => {
  const file = (e.target.files && e.target.files[0]) || null
  if (!file) return
  // store file object in form.extra_fields[fieldName] — will be handled by FormData
  form.extra_fields = { ...form.extra_fields, [fieldName]: file }
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: file.name }
}

// navigation & UI helpers (kept from your original)
const toggleSettings = () => { showSettings.value = !showSettings.value }
const closeSettings = () => { showSettings.value = false }
const logout = () => { showSettings.value = false; router.visit(route('login')) }
const setActiveTab = (tab) => { activeTab.value = tab }
const navigateToPosts = () => { activeTab.value = 'posts'; router.visit(route('announcement_resident')) }
const navigateToDocuments = () => { activeTab.value = 'documents'; router.visit(route('document_request_select_resident')) }
const navigateToProfile = () => { activeTab.value = 'profile'; router.visit(route('profile_resident')) }
const navigateToNotifications = () => { activeTab.value = 'notifications'; router.visit(route('notification_request_resident')) }
const openFAQ = () => { router.visit(route('help_center_resident')) }

const selectEvent = (eventType) => {
  selectedEventType.value = eventType
  // ensure extra fields init
  initExtraFieldsForEvent(eventType)
}

const proceedToForm = () => {
  initExtraFieldsForEvent(selectedEventType.value)
  currentView.value = 'form'
}

const backToSelection = () => {
  currentView.value = 'selection'
}

// basic submission with dynamic validation and Inertia POST (forceFormData)
const testing = true // flip to false to enable stricter client validation

const submitRequest = async () => {
  if (!testing) {
    if (!form.purpose) { alert('Please select a purpose'); return }
    if (!form.event_date || !form.event_time) { alert('Please specify event date/time'); return }
  }

  // compute start & end datetimes (optional; backend should validate)
  if (form.event_date && form.event_time) {
    form.start_datetime = `${form.event_date} ${form.event_time}`
    if (form.duration) {
      const start = new Date(form.start_datetime)
      const end = new Date(start.getTime() + Number(form.duration) * 60 * 60 * 1000)
      const pad = n => String(n).padStart(2, '0')
      form.end_datetime = `${end.getFullYear()}-${pad(end.getMonth()+1)}-${pad(end.getDate())} ${pad(end.getHours())}:${pad(end.getMinutes())}:${pad(end.getSeconds())}`
    }
  }

  // client-side dynamic field validation (if testing=false)
  if (!testing) {
    const missing = currentEventFields.value.find(f => {
      const v = form.extra_fields?.[f.name]
      if (f.required) {
        if (f.type === 'file') return !v
        if (f.type === 'checkbox') return !Array.isArray(v) || v.length === 0
        return v === null || v === '' || v === undefined
      }
      return false
    })
    if (missing) { alert(`Please provide: ${missing.label}`); return }
  }

  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    const fd = new FormData()

    // append scalar fields
    const scalarKeys = ['event_type','purpose','others','location','event_date','event_time','start_datetime','duration','end_datetime','attendees','document_type']
    scalarKeys.forEach(k => {
      if (form[k] !== undefined && form[k] !== null && form[k] !== '') fd.append(k, form[k])
    })

    // append multiple main documents as documents[]
    if (Array.isArray(form.documents)) {
      form.documents.forEach(file => {
        fd.append('documents[]', file)
      })
    }

    // append extra_fields: files, arrays, or scalars
    if (form.extra_fields && typeof form.extra_fields === 'object') {
      Object.keys(form.extra_fields).forEach(key => {
        const val = form.extra_fields[key]
        if (val === null || val === undefined) return

        if (val instanceof File) {
          // single file
          fd.append(`extra_fields[${key}]`, val)
        } else if (Array.isArray(val)) {
          // arrays (e.g., checkbox values)
          val.forEach(v => fd.append(`extra_fields[${key}][]`, v))
        } else {
          fd.append(`extra_fields[${key}]`, val)
        }
      })
    }

    // POST to Laravel JSON endpoint (your controller returns JSON with ticket)
    const url = route('event_assist.store') // assumes Ziggy route helper is available in window
    const res = await axios.post(url, fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    // read ticket from response
    const ticket = res?.data?.ticket || res?.data?.request_number || res?.data?.id ? res?.data?.ticket : null
    requestNumber.value = ticket ?? (res?.data?.id ? `EA-${String(res.data.id).padStart(3,'0')}` : 'EA-TICKET-PENDING')
    currentView.value = 'success'
  } catch (err) {
    if (err.response && err.response.status === 422) {
      const errors = err.response.data.errors || {}
      const firstKey = Object.keys(errors)[0]
      const firstMsg = firstKey ? (errors[firstKey][0] || 'Validation error') : 'Validation error'
      alert(firstMsg)
    } else {
      console.error('Submit error', err)
      alert('An error occurred while submitting. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}


const viewRequest = () => { currentView.value = 'selection'; alert(`Viewing request #${requestNumber.value}`) }

const handleClickOutside = (event) => {
  if (!event.target.closest('.header-actions')) showSettings.value = false
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

.dynamic-field { margin-bottom: 1rem; }
.field-label { display:block; margin-bottom:.25rem; font-weight:600; }
.checkbox-label { display:block; margin-bottom:.25rem; }
.uploaded-files-list { margin-top:.5rem; }
.uploaded-file-item { display:flex; align-items:center; gap:.5rem; margin-bottom:.25rem; }
.remove-file { border:none; background:transparent; cursor:pointer; color:#900; }

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
    transition: all 0.3s;
    margin-left: 825px;
    margin-top: 10px;
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
    font-weight: 650;
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