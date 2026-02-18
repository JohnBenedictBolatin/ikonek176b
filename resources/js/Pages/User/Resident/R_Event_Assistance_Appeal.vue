<template>
    <Head>
        <title>Edit & Resubmit Event Assistance Request</title>
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
                <Link :href="route('event_assistance_resident')" class="back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    BACK TO REQUEST LIST
                </Link>
                <p class="form-document-type">Event Type: <strong>{{ eventTypeName }}</strong></p>
            </div>

            <h3 class="form-title">EDIT & RESUBMIT REQUEST</h3>

            <div v-if="requestData?.admin_feedback" class="rejection-box">
                <h3 class="rejection-title">⚠️ Reason for Rejection:</h3>
                <p class="rejection-text">{{ requestData.admin_feedback }}</p>
            </div>

            <div class="request-info-summary">
                <div class="info-row">
                    <span class="info-label">Request Ticket:</span>
                    <span class="info-value">{{ requestData?.event_assist_request_ticket }}</span>
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
                    
                    <div class="field-input-wrapper">
                        <textarea 
                            v-model="form.purpose" 
                            class="form-input form-textarea"
                            placeholder="Enter the purpose of your event assistance request..."
                            rows="4"
                            required
                        ></textarea>
                    </div>
                </div>

                <!-- Event Date and Time -->
                <div class="form-section">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Event Date & Time <span class="required-star">*</span>
                    </h4>
                    
                    <div class="date-time-row">
                        <div class="field-input-wrapper">
                            <label class="field-label">Event Date</label>
                            <input 
                                type="date" 
                                v-model="form.event_date" 
                                class="form-input"
                                :min="today"
                                required
                            />
                        </div>
                        <div class="field-input-wrapper">
                            <label class="field-label">Start Time</label>
                            <input 
                                type="time" 
                                v-model="form.event_start" 
                                class="form-input"
                                required
                            />
                        </div>
                        <div class="field-input-wrapper">
                            <label class="field-label">End Time</label>
                            <input 
                                type="time" 
                                v-model="form.event_end" 
                                class="form-input"
                                required
                            />
                        </div>
                    </div>
                </div>

                <!-- Event Location -->
                <div class="form-section">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        Event Location
                    </h4>
                    
                    <div class="field-input-wrapper">
                        <input 
                            type="text" 
                            v-model="form.event_location" 
                            class="form-input"
                            placeholder="Enter event location"
                        />
                    </div>
                </div>

                <!-- Expected Attendees -->
                <div class="form-section">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Expected Attendees
                    </h4>
                    
                    <div class="field-input-wrapper">
                        <input 
                            type="number" 
                            v-model.number="form.expected_attendees" 
                            class="form-input"
                            placeholder="Enter number of expected attendees"
                            min="0"
                        />
                    </div>
                </div>

                <!-- Valid ID Upload -->
                <div class="form-section">
                    <h4 class="section-title">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 18.75h3" />
                        </svg>
                        Valid Identification
                    </h4>
                    
                    <div class="id-upload-section">
                        <select v-model="form.id_type" class="form-input" style="margin-bottom: 12px;">
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
                        
                        <div class="uploaded-files-display">
                            <p v-if="idFrontName" class="uploaded-file">Front: {{ idFrontName }}</p>
                            <p v-if="idBackName" class="uploaded-file">Back: {{ idBackName }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="submitError" class="error-message" style="color: #dc3545; font-size: 14px; margin-bottom: 16px; padding: 12px; background-color: #f8d7da; border-radius: 4px; border: 1px solid #f5c6cb;">
                {{ submitError }}
            </div>
            <button class="submit-btn" :disabled="isSubmitting" @click="submitUpdate">
                <span v-if="!isSubmitting">RESUBMIT REQUEST</span>
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
import { Link } from '@inertiajs/vue3'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
    requestData: Object
})

const eventTypeName = computed(() => {
    return props.requestData?.event_type || props.requestData?.extra_fields?.event_type || 'Event Assistance'
})

const form = ref({
    purpose: '',
    event_date: '',
    event_start: '',
    event_end: '',
    event_location: '',
    expected_attendees: null,
    id_type: '',
    id_front: null,
    id_back: null,
})

const isSubmitting = ref(false)
const submitError = ref('')
const formErrors = ref({})
const idFrontName = ref('')
const idBackName = ref('')
const fileFrontInput = ref(null)
const fileBackInput = ref(null)

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

const today = new Date().toISOString().split('T')[0]

// Initialize form with existing data
onMounted(() => {
    if (props.requestData) {
        form.value.purpose = props.requestData.purpose || ''
        form.value.event_date = props.requestData.event_date || ''
        form.value.event_start = props.requestData.event_start || ''
        form.value.event_end = props.requestData.event_end || ''
        form.value.event_location = props.requestData.event_location || ''
        form.value.expected_attendees = props.requestData.expected_attendees || null
        form.value.id_type = props.requestData.id_type || ''
    }
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
    } else if (side === 'back') {
        idBackName.value = file.name
        form.value.id_back = file
    }
}

// Submit update
const submitUpdate = async () => {
    submitError.value = ''
    formErrors.value = {}
    
    if (!form.value.purpose || form.value.purpose.trim() === '') {
        submitError.value = 'Please provide a purpose for your event assistance request.'
        return
    }

    if (!form.value.event_date) {
        submitError.value = 'Please select an event date.'
        return
    }

    if (!form.value.event_start || !form.value.event_end) {
        submitError.value = 'Please provide both start and end times for your event.'
        return
    }

    if (isSubmitting.value) return
    isSubmitting.value = true

    try {
        const formData = new FormData()
        const requestId = props.requestData.event_assist_request_id

        // Append basic fields
        formData.append('purpose', form.value.purpose.trim())
        if (form.value.event_date) {
            formData.append('event_date', form.value.event_date)
        }
        if (form.value.event_start) {
            formData.append('event_start', form.value.event_start)
        }
        if (form.value.event_end) {
            formData.append('event_end', form.value.event_end)
        }
        if (form.value.event_location) {
            formData.append('event_location', form.value.event_location)
        }
        if (form.value.expected_attendees !== null) {
            formData.append('expected_attendees', form.value.expected_attendees)
        }
        if (form.value.id_type) {
            formData.append('id_type', form.value.id_type)
        }

        // Append ID files
        if (form.value.id_front instanceof File) {
            formData.append('id_front', form.value.id_front)
            formData.append('valid_id_content', form.value.id_front)
        }
        if (form.value.id_back instanceof File) {
            formData.append('id_back', form.value.id_back)
        }

        // Preserve event_type in extra_fields
        if (props.requestData?.event_type || props.requestData?.extra_fields?.event_type) {
            const extraFields = { ...(props.requestData.extra_fields || {}) }
            extraFields.event_type = props.requestData.event_type || props.requestData.extra_fields?.event_type
            formData.append('extra_fields[event_type]', extraFields.event_type)
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

        const response = await window.axios.put(
            route('event_assistance.update', { id: requestId }),
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
            router.visit(route('event_assistance_resident'))
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
    padding: 0 20px;
}

.header-logo {
    height: 40px;
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

.request-form-container {
    max-width: 900px;
    margin: 100px auto 40px;
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.form-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    color: #666;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: color 0.2s;
}

.back-btn:hover {
    color: #ff8c42;
}

.form-document-type {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.form-title {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 30px 0;
    text-align: center;
}

.rejection-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.rejection-title {
    font-size: 18px;
    font-weight: 700;
    color: #856404;
    margin: 0 0 10px 0;
}

.rejection-text {
    font-size: 15px;
    color: #856404;
    margin: 0;
    line-height: 1.6;
}

.request-info-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.info-row {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-label {
    font-size: 13px;
    color: #666;
    font-weight: 600;
}

.info-value {
    font-size: 16px;
    color: #1a1a1a;
    font-weight: 600;
}

.status-pending {
    color: #ff8c42;
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.form-section {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    background: #fafafa;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 20px 0;
}

.required-star {
    color: #e74c3c;
}

.field-input-wrapper {
    margin-bottom: 15px;
}

.field-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 15px;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.date-time-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px;
}

.id-upload-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.upload-row {
    display: flex;
    gap: 12px;
}

.upload-btn {
    flex: 1;
    padding: 12px 20px;
    background: #ff8c42;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.upload-btn:hover {
    background: #e67e22;
}

.uploaded-files-display {
    margin-top: 10px;
}

.uploaded-file {
    font-size: 14px;
    color: #4caf50;
    margin: 5px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #ff8c42, #e67e22);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    margin-top: 30px;
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

    .date-time-row {
        grid-template-columns: 1fr;
    }

    .upload-row {
        flex-direction: column;
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


