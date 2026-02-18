<template>
    <Head>
        <title>Edit & Resubmit Request</title>
    </Head>

    <div class="app-container">
        <!-- Dark Grey Header -->
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
                        <Link href="#" class="settings-item" @click.prevent="logout">SIGN OUT</Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="request-form-container">
            <div class="form-header-row">
                <Link :href="route('document_request_select_resident')" class="back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    BACK TO REQUEST LIST
                </Link>
                <p class="form-document-type">Document Type: <strong>{{ documentTypeName }}</strong></p>
            </div>

            <h3 class="form-title">EDIT & RESUBMIT REQUEST</h3>

            <div v-if="requestData?.admin_feedback" class="rejection-box">
                <h3 class="rejection-title">⚠️ Reason for Rejection:</h3>
                <p class="rejection-text">{{ requestData.admin_feedback }}</p>
            </div>

            <div class="request-info-summary">
                <div class="info-row">
                    <span class="info-label">Request Ticket:</span>
                    <span class="info-value">{{ requestData?.doc_request_ticket }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value status-pending">PENDING</span>
                </div>
            </div>

            <div class="form-sections">
                <!-- Purpose -->
                <div class="form-section">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Purpose <span class="required-star">*</span>
                    </h4>
                    
                    <div class="purpose-id-row">
                        <div class="purpose-field">
                            <select 
                                v-model="form.purpose" 
                                class="form-input"
                                required
                                @change="handlePurposeChange"
                            >
                                <option value="">Select purpose</option>
                                <option v-for="purpose in currentDocumentPurposes" :key="purpose" :value="purpose">
                                    {{ purpose }}
                                </option>
                            </select>
                            <input
                                v-if="form.purpose === 'Others'"
                                v-model="purposeOthers"
                                @input="form.purpose = purposeOthers"
                                placeholder="Please specify your purpose..."
                                class="form-input"
                                style="margin-top: 10px;"
                                required
                            />
                            <p v-if="form.purpose === 'Others'" style="margin-top: 8px; font-size: 14px; color: #333; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; color: #e74c3c; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                Please provide a purpose similar to the existing options (e.g., "Medical Appointment", "Loan Application").
                            </p>
                        </div>
                        
                        <div class="id-type-field">
                            <div class="id-type-upload-row">
                                <select 
                                    v-model="form.id_type" 
                                    class="form-input id-type-select"
                                >
                                    <option value="">Type of Identification</option>
                                    <option value="National ID">National ID</option>
                                    <option value="Driver's License">Driver's License</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Voter's ID">Voter's ID</option>
                                    <option value="SSS ID">SSS ID</option>
                                    <option value="UMID">UMID</option>
                                </select>
                                
                                <div class="upload-row">
                                    <button 
                                        type="button" 
                                        class="upload-btn" 
                                        @click.prevent="triggerFileUpload('front')"
                                    >
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

                                    <button 
                                        type="button" 
                                        class="upload-btn" 
                                        @click.prevent="triggerFileUpload('back')"
                                    >
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
                            
                            <div class="uploaded-files-display">
                                <p v-if="idFrontName" class="uploaded-file">Front: {{ idFrontName }}</p>
                                <p v-if="idBackName" class="uploaded-file">Back: {{ idBackName }}</p>
                            </div>
                            
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

                    <!-- Special layout for Barangay Certificate -->
                    <template v-if="documentTypeName === 'Barangay Certificate'">
                        <div class="dynamic-field-wrapper">
                            <div class="field-header">
                                <label class="field-label">
                                    {{ currentDocumentFields.find(f => f.name === 'duration_of_residency')?.label }}
                                </label>
                            </div>
                            <div class="field-input-wrapper">
                                <input 
                                    type="number"
                                    v-model.number="form.extra_fields.duration_of_residency"
                                    placeholder="Enter number of years"
                                    :min="0"
                                    :step="1"
                                    class="form-input"
                                />
                            </div>
                        </div>
                    </template>

                    <!-- Non-file fields (text, select, number, etc.) - for other document types -->
                    <div v-for="field in currentDocumentFields.filter(f => f.type !== 'file')" :key="field.name" class="dynamic-field-wrapper" v-if="documentTypeName !== 'Barangay Certificate'">
                        <div class="field-header">
                            <label class="field-label">
                                {{ field.label }} 
                                <span v-if="field.required" class="required-star">*</span>
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
                            
                            <!-- Text input for "Other" option in occupation field -->
                            <input
                                v-if="field.name === 'occupation' && form.extra_fields[field.name] === 'Other'"
                                type="text"
                                v-model="form.extra_fields.occupation_other"
                                placeholder="Please specify your occupation or profession"
                                class="form-input"
                                style="margin-top: 10px;"
                                :required="field.required && form.extra_fields[field.name] === 'Other'"
                            />

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

                    <!-- File upload fields - displayed in grid (excluding Barangay Certificate) -->
                    <div v-if="currentDocumentFields.filter(f => f.type === 'file' && documentTypeName !== 'Barangay Certificate').length > 0" class="file-uploads-grid">
                        <div v-for="field in currentDocumentFields.filter(f => f.type === 'file' && documentTypeName !== 'Barangay Certificate')" :key="field.name" class="file-upload-item">
                            <div class="file-upload-header">
                                <label class="file-upload-label">
                                    {{ field.label }} 
                                    <span v-if="field.required" class="required-star">*</span>
                                </label>
                                <p v-if="field.description" class="file-upload-description">{{ field.description }}</p>
                            </div>
                            <div class="file-upload-controls">
                                <div v-if="getExistingFile(field.name)" class="existing-file-display">
                                    <span class="existing-file-name">Current: {{ getExistingFile(field.name)?.file_name }}</span>
                                    <a :href="getExistingFile(field.name)?.url" target="_blank" class="view-file-link">View</a>
                                </div>
                                <button 
                                    type="button"
                                    class="upload-btn-dynamic" 
                                    @click.prevent="triggerDynamicFileUpload(field.name)"
                                >
                                    {{ getExistingFile(field.name) ? 'REPLACE' : 'UPLOAD' }}
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

            <div v-if="submitError" class="error-message" style="color: #dc3545; font-size: 14px; margin-bottom: 16px; padding: 12px; background-color: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb;">
                {{ submitError }}
            </div>

            <button
                type="button"
                class="submit-btn"
                :disabled="isSubmitting"
                @click="submitUpdate"
            >
                <span v-if="!isSubmitting">SUBMIT</span>
                <span v-else>Submitting...</span>
            </button>
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
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    requestData: {
        type: Object,
        default: () => ({})
    }
})

// Document type name
const documentTypeName = computed(() => {
    return props.requestData?.document_type?.name || props.requestData?.document_name || 'Document'
})

// Document purposes (same as original form)
const documentPurposes = {
    'Barangay Certificate': [
        'Employment',
        'School Enrollment',
        'Government Transaction',
        'Bank Transaction',
        'Medical Appointment',
        'Loan Application',
        'Others'
    ],
    'Barangay ID': [
        'Identification',
        'Government Transaction',
        'Bank Transaction',
        'Employment',
        'School Enrollment',
        'Others'
    ],
    'Cedula': [
        'Employment',
        'Business Registration',
        'Government Transaction',
        'Tax Requirements',
        'Legal Documentation',
        'Others'
    ],
    'Certificate of Indigency': [
        'Medical Assistance',
        'Educational Scholarship',
        'Social Services',
        'Financial Assistance',
        'Housing Assistance',
        'Others'
    ],
    'Permit': [
        'New Business Registration',
        'Business Renewal',
        'Business Expansion',
        'New Construction',
        'Building Renovation',
        'Building Addition',
        'Others'
    ]
}

// Computed property to get purposes for current document type
const currentDocumentPurposes = computed(() => {
    return documentPurposes[documentTypeName.value] || []
})

// Document fields (same as original form)
const documentFields = {
    'Barangay Certificate': [
        { name: 'duration_of_residency', label: 'Duration of Residency (years)', type: 'number', required: false, placeholder: 'Enter number of years', min: 0, step: 1 }
    ],
    'Barangay ID': [
        { name: 'photo', label: '2x2 Photo (2 copies)', type: 'file', required: true, accept: 'image/*', description: 'Upload 2x2 ID picture (2 copies)' },
        { name: 'birth_certificate', label: 'Birth Certificate (for first time applicants)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload birth certificate if this is your first application' }
    ],
    'Cedula': [
        { name: 'income_source', label: 'Income Source', type: 'select', required: false, placeholder: 'Select income source', options: ['Employment', 'Business', 'Pension', 'Remittance', 'Other'] },
        { name: 'annual_income', label: 'Annual Income/Salary (PHP)', type: 'number', required: false, placeholder: 'Enter annual income or salary', min: 0, step: 0.01, description: 'Your annual income or salary from employment/profession (for tax calculation)' },
        { name: 'business_gross_receipts', label: 'Business Gross Receipts (PHP)', type: 'number', required: false, placeholder: 'Enter business gross receipts', min: 0, step: 0.01, description: 'If you have a business, enter gross receipts from preceding year (optional)' },
        { name: 'real_property_income', label: 'Real Property Income (PHP)', type: 'number', required: false, placeholder: 'Enter income from real property', min: 0, step: 0.01, description: 'Income from real property if applicable (optional)' },
        { name: 'occupation', label: 'Occupation/Profession', type: 'select', required: false, placeholder: 'Select your occupation or profession', options: ['Teacher', 'Nurse', 'Engineer', 'Accountant', 'Lawyer', 'Doctor', 'Police Officer', 'Firefighter', 'Soldier', 'Government Employee', 'Private Employee', 'Business Owner', 'Entrepreneur', 'Farmer', 'Fisherman', 'Driver', 'Construction Worker', 'Electrician', 'Plumber', 'Carpenter', 'Mechanic', 'Chef', 'Waiter/Waitress', 'Salesperson', 'Cashier', 'Security Guard', 'Janitor', 'Housekeeper', 'Barber/Hairdresser', 'Beautician', 'Student', 'Unemployed', 'Retired', 'Housewife/Househusband', 'Freelancer', 'Other'], description: 'Your current job or profession' },
        { name: 'tin', label: 'Tax Identification Number (TIN)', type: 'text', required: false, placeholder: 'Enter TIN if available', description: 'Your TIN if you have one (optional)' },
        { name: 'height', label: 'Height (cm)', type: 'number', required: false, placeholder: 'Enter height in centimeters', min: 0, step: 0.1, description: 'Your height (optional)' },
        { name: 'weight', label: 'Weight (kg)', type: 'number', required: false, placeholder: 'Enter weight in kilograms', min: 0, step: 0.1, description: 'Your weight (optional)' },
        { name: 'tax_declaration', label: 'Tax Declaration (if applicable)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload tax declaration document' },
        { name: 'income_statement', label: 'Income Statement (if employed)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload income statement or payslip if employed' }
    ],
    'Certificate of Indigency': [
        { name: 'household_members', label: 'Household Member Count', type: 'number', required: false, placeholder: 'Enter number of household members', min: 1, step: 1, description: 'Enter the total number of members in your household' },
        { name: 'income_proof', label: 'Proof of Low Income', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload documents proving low income status' }
    ],
    'Permit': [
        { name: 'barangay_clearance', label: 'Barangay Clearance', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload Barangay Clearance document' },
    ],
}

// Building and Business permit fields
const buildingPermitFields = [
    { name: 'building_type', label: 'Building Type', type: 'select', required: true, placeholder: 'Select building type', options: ['Residential', 'Commercial', 'Mixed Use', 'Industrial', 'Institutional', 'Other'] },
    { name: 'building_reg_number', label: 'Building Registration Number', type: 'text', required: false, placeholder: 'Enter building registration number (if available)' },
    { name: 'building_plans', label: 'Building Plans (3 copies)', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload building plans (3 copies)' },
    { name: 'engineer_cert', label: 'Engineer\'s Certification', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload engineer\'s certification document' },
    { name: 'lot_title', label: 'Lot Title or Tax Declaration', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload lot title or tax declaration' },
]

const businessPermitFields = [
    { name: 'business_name', label: 'Business Name', type: 'text', required: true, placeholder: 'Enter your business name' },
    { name: 'business_type', label: 'Business Type', type: 'select', required: true, placeholder: 'Select business type', options: ['Retail', 'Wholesale', 'Service', 'Manufacturing', 'Food & Beverage', 'Other'] },
    { name: 'dtI_sec_number', label: 'DTI/SEC Registration Number', type: 'text', required: false, placeholder: 'Enter DTI/SEC registration number' },
    { name: 'business_registration', label: 'Business Registration Documents', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload business registration documents' },
    { name: 'lease_contract', label: 'Lease Contract (if renting)', type: 'file', required: false, accept: '.pdf,image/*', description: 'Upload lease contract if business location is rented' },
    { name: 'dti_registration', label: 'DTI Registration Document', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload DTI registration certificate' },
    { name: 'location_plan', label: 'Location Plan', type: 'file', required: true, accept: '.pdf,image/*', description: 'Upload location plan or site map of business' },
]

// Computed array for current selected doc fields
const currentDocumentFields = computed(() => {
    const baseFields = documentFields[documentTypeName.value] ?? []
    
    // If Permit is selected, add fields based on permit_type
    if (documentTypeName.value === 'Permit') {
        const permitType = form.value.extra_fields?.permit_type
        if (permitType === 'Building Permit') {
            return [...baseFields, ...buildingPermitFields]
        } else if (permitType === 'Business Permit') {
            return [...baseFields, ...businessPermitFields]
        }
    }
    
    return baseFields
})

// Form data
const form = ref({
    purpose: '',
    id_type: '',
    id_number: '',
    extra_fields: {},
    id_front: null,
    id_back: null,
    valid_id_content: null,
})

const isSubmitting = ref(false)
const submitError = ref('')
const formErrors = ref({})
const idFrontName = ref('')
const idBackName = ref('')
const extraFileNames = ref({})
const fileFrontInput = ref(null)
const fileBackInput = ref(null)
const purposeOthers = ref('')

// Settings dropdown
const showSettings = ref(false)
const toggleSettings = () => { showSettings.value = !showSettings.value }
const closeSettings = () => { showSettings.value = false }

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
    router.post('/logout', {}, {
        onSuccess: () => {
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onError: () => {
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onFinish: () => {
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        }
    })
}

// Helper date
const today = new Date().toISOString().split('T')[0]

// Check if a field is marked as incorrect
const isFieldIncorrect = (fieldName) => {
    const incorrectFields = props.requestData?.incorrect_fields || []
    return incorrectFields.includes(fieldName)
}

// Check if an extra field is marked as incorrect
const isExtraFieldIncorrect = (fieldName) => {
    const incorrectFields = props.requestData?.incorrect_fields || []
    return incorrectFields.includes(`extra_fields.${fieldName}`)
}

// Initialize form with existing data
onMounted(() => {
    if (props.requestData) {
        form.value.purpose = props.requestData.purpose || ''
        form.value.id_type = props.requestData.id_type || ''
        form.value.id_number = props.requestData.valid_id_number || ''
        form.value.extra_fields = { ...(props.requestData.extra_fields || {}) }
        
        // Initialize extra fields for document
        initExtraFieldsForDocument(documentTypeName.value)
    }
})

// Initialize extra fields
const initExtraFieldsForDocument = (docName) => {
    const defs = documentFields[docName] ?? []
    form.value.extra_fields = form.value.extra_fields || {}

    let allDefs = [...defs]
    if (docName === 'Permit') {
        const permitType = form.value.extra_fields?.permit_type
        if (permitType === 'Building Permit') {
            allDefs = [...defs, ...buildingPermitFields]
        } else if (permitType === 'Business Permit') {
            allDefs = [...defs, ...businessPermitFields]
        }
    }

    allDefs.forEach((f) => {
        if (form.value.extra_fields[f.name] === undefined) {
            if (f.type === 'checkbox') form.value.extra_fields[f.name] = []
            else if (f.type === 'select') form.value.extra_fields[f.name] = null 
            else form.value.extra_fields[f.name] = null
        }
        if (!extraFileNames.value[f.name]) extraFileNames.value[f.name] = ''
    })
}

// Get existing file attachment
const getExistingFile = (fieldName) => {
    return props.requestData?.attachments?.find(att => att.field_name === fieldName)
}

// ID number label
const idNumberLabels = {
    'National ID': 'National ID No.',
    "Driver's License": "Driver's License No.",
    'Passport': 'Passport No.',
    "Voter's ID": "Voter's ID No.",
    'SSS ID': 'SSS No.',
    'UMID': 'UMID No.'
}
const idNumberLabel = computed(() => {
    return idNumberLabels[form.value.id_type] || 'ID Number'
})

// Show ID number field
const showIdNumber = computed(() => {
    return !!form.value.id_type && (!!form.value.id_front || !!form.value.id_back)
})

// File upload handlers
const triggerFileUpload = (side) => {
    if (side === 'front' && fileFrontInput.value) {
        fileFrontInput.value.click()
    } else if (side === 'back' && fileBackInput.value) {
        fileBackInput.value.click()
    }
}

const handleFileUpload = (event, side) => {
    const file = event.target.files?.[0]
    if (!file) return

    if (side === 'front') {
        idFrontName.value = file.name
        form.value.id_front = file
        form.value.valid_id_content = file
    } else if (side === 'back') {
        idBackName.value = file.name
        form.value.id_back = file
        if (!form.value.valid_id_content) {
            form.value.valid_id_content = file
        }
    }
}

const triggerDynamicFileUpload = (fieldName) => {
    const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
    if (input && typeof input.click === 'function') {
        input.click()
    }
}

const handleDynamicFileUpload = (event, fieldName) => {
    const file = event.target.files[0]
    if (!file) return
    extraFileNames.value = { ...extraFileNames.value, [fieldName]: file.name }
    form.value.extra_fields = { ...form.value.extra_fields, [fieldName]: file }
}

const removeDynamicFile = (fieldName) => {
    form.value.extra_fields = { ...form.value.extra_fields, [fieldName]: null }
    extraFileNames.value = { ...extraFileNames.value, [fieldName]: '' }
    const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
    if (input) input.value = ''
}

const toggleCheckbox = (fieldName, value, checked) => {
    if (!Array.isArray(form.value.extra_fields[fieldName])) {
        form.value.extra_fields[fieldName] = []
    }
    const idx = form.value.extra_fields[fieldName].indexOf(value)
    if (checked && idx === -1) form.value.extra_fields[fieldName].push(value)
    if (!checked && idx !== -1) form.value.extra_fields[fieldName].splice(idx, 1)
}

const handlePurposeChange = () => {
    if (form.value.purpose !== 'Others') {
        purposeOthers.value = ''
    }
}

// Submit update
const submitUpdate = async () => {
    submitError.value = ''
    formErrors.value = {}
    
    // Validate purpose
    const purposeValue = form.value.purpose || purposeOthers.value || ''
    if (!purposeValue || purposeValue.trim() === '') {
        submitError.value = 'Please provide a purpose for your request.'
        return
    }

    // Validate required dynamic fields
    const missing = currentDocumentFields.value
        .filter(f => f.required)
        .find(f => {
            const val = form.value.extra_fields?.[f.name]
            if (f.type === 'file') return !val && !getExistingFile(f.name)
            if (f.type === 'checkbox') return !Array.isArray(val) || val.length === 0
            return !val
        })
    if (missing) {
        submitError.value = `Please provide: ${missing.label}`
        return
    }

    // Validate ID fields
    if (form.value.id_type && (!form.value.id_front || !form.value.id_back)) {
        submitError.value = 'Please upload both the front and back of your selected ID.'
        return
    }

    if (form.value.id_type && !form.value.id_number) {
        submitError.value = `Please enter ${idNumberLabel.value}`
        return
    }

    if (isSubmitting.value) return
    isSubmitting.value = true

    try {
        const formData = new FormData()
        const requestId = props.requestData.doc_request_id

        // Append all scalar fields (not just incorrect ones)
        const purposeValue = form.value.purpose || purposeOthers.value || ''
        if (purposeValue && purposeValue.trim() !== '') {
            formData.append('purpose', purposeValue.trim())
        }
        
        if (form.value.id_type) {
            formData.append('id_type', form.value.id_type)
        }
        
        if (form.value.id_number) {
            formData.append('id_number', form.value.id_number)
            formData.append('valid_id_number', form.value.id_number)
        }

        // Append ID files
        if (form.value.id_front instanceof File) {
            formData.append('id_front', form.value.id_front)
            formData.append('valid_id_content', form.value.id_front)
        }
        if (form.value.id_back instanceof File) {
            formData.append('id_back', form.value.id_back)
        }

        // Handle occupation field: if "Other" is selected, use the custom input value
        if (form.value.extra_fields?.occupation === 'Other' && form.value.extra_fields?.occupation_other) {
            form.value.extra_fields.occupation = form.value.extra_fields.occupation_other
            // Remove the temporary occupation_other field
            delete form.value.extra_fields.occupation_other
        }

        // Append all extra_fields (not just incorrect ones)
        Object.keys(form.value.extra_fields).forEach(key => {
            const val = form.value.extra_fields[key]
            if (val === null || val === undefined || val === '') return

            if (val instanceof File) {
                formData.append(`extra_fields[${key}]`, val)
            } else if (Array.isArray(val)) {
                val.forEach(v => {
                    if (v !== null && v !== undefined && v !== '') {
                        formData.append(`extra_fields[${key}][]`, v)
                    }
                })
            } else {
                formData.append(`extra_fields[${key}]`, val)
            }
        })

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

        const response = await window.axios.put(
            route('document_requests.update', { id: requestId }),
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }
        )

        if (response.data.success) {
            submitError.value = ''
            formErrors.value = {}
            alert('Request updated successfully! Your request has been resubmitted for review.')
            router.visit(route('document_request_select_resident'))
        } else {
            submitError.value = response.data.message || 'Failed to update request. Please try again.'
        }
    } catch (error) {
        console.error('Update error:', error)
        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.errors || {}
                formErrors.value = errors
                const firstError = Object.values(errors).flat()[0]
                submitError.value = firstError || 'Validation error. Please check your form and try again.'
            } else if (error.response.status === 403) {
                submitError.value = 'You do not have permission to update this request.'
            } else if (error.response.status === 404) {
                submitError.value = 'Request not found. Please refresh the page and try again.'
            } else {
                submitError.value = error.response.data?.message || 'Failed to update request. Please try again.'
            }
        } else if (error.request) {
            submitError.value = 'Network error. Please check your internet connection and try again.'
        } else {
            submitError.value = error.message || 'An unexpected error occurred. Please try again.'
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

<style scoped>

.app-container {
    min-height: 100vh;
    background: url('/assets/BG MAIN.png') no-repeat center center fixed;
    background-size: cover;
    padding-top: 0;
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
    display: flex;
    align-items: center;
    gap: 15px;
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

.back-link-header {
    color: white;
    text-decoration: none;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 6px;
    background: rgba(255,255,255,0.1);
    transition: all 0.3s ease;
    font-size: 14px;
}

.back-link-header:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-1px);
}

.request-form-container {
    max-width: 1200px;
    margin: 80px auto 30px;
    padding: 30px 40px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.rejection-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.rejection-title {
    font-size: 18px;
    color: #856404;
    margin-bottom: 10px;
    font-weight: 600;
}

.rejection-text {
    font-size: 15px;
    color: #856404;
    line-height: 1.6;
    margin: 0;
}

.request-info-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #dee2e6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.info-value {
    color: #1a1a1a;
    font-size: 14px;
}

.status-pending {
    color: #ff8c42;
    font-weight: 600;
}

/* Import all form styles from original - these should match exactly */
.form-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    width: 100%;
}

.back-btn {
    background: transparent;
    border: none;
    color: #000;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    padding: 0;
    text-decoration: none;
}

.back-btn:hover {
    color: #ff8c42;
}

.form-document-type {
    font-size: 16px;
    color: #666;
    margin: 0;
}

.form-document-type strong {
    color: #ff8c42;
    font-weight: 700;
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
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
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dynamic-fields {
    background: white;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.required-star {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
}

.purpose-id-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.purpose-field,
.id-type-field {
    display: flex;
    flex-direction: column;
}

.id-type-upload-row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.id-type-select {
    flex: 1;
}

.upload-row {
    display: flex;
    gap: 8px;
}

.upload-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.uploaded-files-display {
    margin-top: 10px;
}

.uploaded-file {
    font-size: 14px;
    color: #4caf50;
    font-weight: 500;
    margin: 5px 0;
}

.id-number-field {
    margin-top: 12px;
}

.field-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
}

.form-input {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
    width: 100%;
}

.form-input:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    background: white;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-textarea:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
}

.dynamic-field-wrapper {
    margin-bottom: 20px;
}

.field-header {
    margin-bottom: 10px;
}

.field-description {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.field-input-wrapper {
    width: 100%;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.file-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.file-upload-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.file-upload-header {
    margin-bottom: 10px;
}

.file-upload-label {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.file-upload-description {
    font-size: 12px;
    color: #666;
}

.file-upload-controls {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.upload-btn-dynamic {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s;
    width: fit-content;
}

.upload-btn-dynamic:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.existing-file-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: white;
    border-radius: 6px;
    margin-bottom: 10px;
}

.existing-file-name {
    font-size: 13px;
    color: #666;
}

.view-file-link {
    color: #ff8c42;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
}

.view-file-link:hover {
    text-decoration: underline;
}

.uploaded-file-info-compact {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #e8f5e9;
    border-radius: 6px;
    font-size: 13px;
}

.file-checkmark {
    color: #4caf50;
}

.file-name-compact {
    flex: 1;
    color: #2e7d32;
    font-weight: 500;
}

.remove-file-btn-small {
    background: transparent;
    border: none;
    color: #e74c3c;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-file-btn-small:hover {
    background: #ffebee;
    border-radius: 4px;
}

.field-disabled {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
    opacity: 0.7;
}

.btn-disabled {
    background: #ccc !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.btn-disabled:hover {
    transform: none !important;
    box-shadow: none !important;
}

.incorrect-fields-checklist {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 10px;
    max-height: 300px;
    overflow-y: auto;
}

.checkbox-field-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: background 0.2s;
}

.checkbox-field-label:hover {
    background: #e9ecef;
}

.checkbox-field-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-field-label span {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.field-help-text {
    font-size: 13px;
    color: #666;
    margin-bottom: 10px;
    font-style: italic;
}

.submit-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    display: block;
    margin: 30px auto 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .request-form-container {
        padding: 20px;
        margin: 20px;
    }

    .purpose-id-row {
        grid-template-columns: 1fr;
    }

    .file-uploads-grid {
        grid-template-columns: 1fr;
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
