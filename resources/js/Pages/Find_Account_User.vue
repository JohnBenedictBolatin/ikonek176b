<template>
  <Head>
    <title>Find Account</title>
  </Head>

  <!-- Privacy Modal -->
  <div v-if="showPrivacy" class="modal-backdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="privacyTitle">
      <h2 id="privacyTitle" class="privacy-title">⚠️ Privacy Notice</h2>
      <p class="justified-text">
       Thank you for using our account recovery service! Your privacy is important to us. This platform collects personal information such as your name, contact details, and identification documents to verify your identity and help you recover your account. We may also gather non-personal data like device and browser information to improve the system's performance and security.
      </p>
      <p class="justified-text">
All information collected is used only to verify your identity, process your account recovery request, and improve our services. We ensure that your data is stored securely and protected against unauthorized access. While we take appropriate security measures, please note that no system can guarantee absolute security.
We do not sell or share your personal data with third parties, except with authorized barangay personnel or government agencies when required by law. As a user, you have the right to access, correct, or delete your personal information, or to withdraw your consent at any time by contacting us at ikonek176b@dev.ph
<p></p>Our website uses cookies to improve user experience.
      </p>
      <p class="justified-text">
        By using iKonek176B, you agree to the terms of this Privacy Policy.
For any concerns, contact us at ikonek176b@dev.ph, +639193076338, or visit the Barangay 176B office.
      </p>
      <div class="modal-actions">
        <button class="btn-primary" @click="closePrivacy">I Understand</button>
      </div>
    </div>
  </div>

  <div class="page-container">
    <!-- Left side form -->
    <div class="left-side">
      <!-- Logo -->
      <div class="logo-section">
        <img src="assets/LOGO.png" alt="Logo" class="logo-icon" />
      </div>

      <div class="form-title">
        <h1>Find Account - Change Password</h1>
      </div>

      <form @submit.prevent="submitForm" class="registration-form">
        <!-- Account Details Section -->
        <div class="form-section full-width">
          <h3 class="section-title">
            👤 Account Details
          </h3>
          
          <div class="form-group">
            <input v-model="form.last_name" placeholder="Last Name" class="form-input" required />
          </div>

          <div class="form-row">
            <div class="form-group half">
              <input v-model="form.first_name" placeholder="First Name" class="form-input" required />
            </div>
            
            <div class="form-group half">
              <input v-model="form.middle_name" placeholder="Middle Name" class="form-input" />
            </div>
          </div>

          <div class="form-group">
            <select v-model="form.suffix" class="form-input">
              <option value="">Suffix</option>
              <option>Jr.</option>
              <option>Sr.</option>
              <option>III</option>
              <option>IV</option>
              <option>None</option>
            </select>
          </div>
        </div>

        <!-- Proof of Ownership Section -->
        <div class="form-section full-width">
          <h3 class="section-title">
            📁 Proof of Ownership
          </h3>
          
          <div class="form-row">
            <div class="form-group flex-grow">
              <select v-model="form.proofType" class="form-input" required>
                <option value="">Type of Identification</option>
                <option value="1">1</option>
                <option value="6">6</option>
              </select>
            </div>
            <div class="form-group">
              <button type="button" class="upload-btn" @click="uploadProof">
                UPLOAD
              </button>
            </div>
          </div>
        </div>

        <!-- New Contact Number Section -->
        <div class="form-section full-width">
          <h3 class="section-title">
            📞 Register New Contact Number
          </h3>
          
          <div class="form-group">
            <div class="phone-input">
              <span class="country-code">+63</span>
              <input v-model="form.contact_number" placeholder="Primary Number" maxlength="10" class="form-input" required />
            </div>
          </div>

          <div class="form-group">
            <div class="phone-input">
              <span class="country-code">+63</span>
              <input v-model="form.secondary_contact" placeholder="Secondary Number (Optional)" maxlength="10" class="form-input" />
            </div>
          </div>
        </div>

        <!-- New Password Section -->
        <div class="form-section full-width">
          <h3 class="section-title">
            🔒 Set New Password
          </h3>
          
          <div class="form-group">
            <input type="password" v-model="form.password" placeholder="New Password" class="form-input" required />
          </div>

          <div class="form-group">
            <input type="password" v-model="form.password_confirmation" placeholder="Confirm New Password" class="form-input" required />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
          <Link :href="route('login')" class="back-btn">
            BACK TO LOGIN
          </Link>
          <button class="register-btn" :disabled="form.processing" type="submit">
            CONFIRM
          </button>
        </div>

        <!-- Display validation errors -->
        <div v-if="Object.keys(form.errors).length" class="error-section">
          <div class="error-list">
            <span class="error-icon">⚠️</span>
            <div class="error-content">
              <strong>Please fix the following errors:</strong>
              <ul>
                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
          <span class="disclaimer-icon">⚠️</span>
          <div class="disclaimer-content">
            <strong>Important</strong><br>
            Please ensure all information provided is accurate. Your account recovery request will be verified against our records.
          </div>
        </div>
      </form>
    </div>

    <!-- Right side for background/illustrations -->
    <div class="right-side">
      <!-- This space is for your background image/3D character -->
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { Link, Head, useForm } from '@inertiajs/vue3'

// showPrivacy defaults to true so modal is visible on page load
const showPrivacy = ref(true)
function closePrivacy() { showPrivacy.value = false }

// prevent background scrolling while modal is open
watch(showPrivacy, (val) => {
  document.body.style.overflow = val ? 'hidden' : ''
})

// cleanup in case component unmounts
onBeforeUnmount(() => { document.body.style.overflow = '' })

const form = useForm({
  last_name: '',
  first_name: '',
  middle_name: '',
  suffix: '',
  proofType: '',
  contact_number: '',
  secondary_contact: '',
  password: '',
  password_confirmation: ''
})

function uploadProof() {
  alert('Upload functionality not implemented yet.')
}

function submitForm() {
  form.post(route('account.update'))
}

// optional: ensure modal shows on first mount (helps if server-side rendered state differs)
onMounted(() => {
  showPrivacy.value = true
})
</script>

<style scoped>
/* Reset and prevent scrolling */
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

* {
  box-sizing: border-box;
}

/* Main page container - fixed and unscrollable */
.page-container {
  width: 100vw;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  background-image: url('assets/BACKGROUND IMAGE REG1.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
}

/* LEFT SIDE - Registration Form - Made Wider */
.left-side {
  width: 900px;
  max-height: 90vh;
  background: white;
  padding: 20px 40px;
  overflow-y: auto;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* RIGHT SIDE - Background/Illustration Space */
.right-side {
  display: none;
}

/* Logo Section */
.logo-section {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
}

.logo-icon {
  width:250px;
  height: 80px;
}

/* Form Title */
.form-title {
  text-align: center;
  margin-bottom: 30px;
}

.form-title h1 {
  font-size: 28px;
  font-weight: 700;
  color: #333;
  margin: 0;
}

/* Form Layout */
.registration-form {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.form-section {
  background: transparent;
}

.form-section.full-width {
  width: 100%;
  margin-bottom: 20px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #ff8c42;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Form Groups and Rows */
.form-group {
  margin-bottom: 12px;
  position: relative;
}

.form-group.half {
  flex: 1;
}

.form-group.third {
  flex: 1;
}

.form-group.flex-grow {
  flex-grow: 1;
}

.form-row {
  display: flex;
  gap: 15px;
}

/* Input Styles */
.form-input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  color: #333;
  outline: none;
  background: white;
}

.form-input.disabled {
  background: #f8f8f8;
  color: #666;
}

.form-input::placeholder {
  color: #999;
}

.form-input:focus {
  border-color: #ff8c42;
}

/* Phone Input */
.phone-input {
  display: flex;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}

.country-code {
  padding: 12px 15px;
  background: #f8f8f8;
  color: #333;
  font-weight: 500;
  border-right: 1px solid #ddd;
}

.phone-input .form-input {
  border: none;
  border-radius: 0;
  flex: 1;
}

/* Buttons */
.upload-btn {
  background: #4CAF50;
  color: white;
  border: none;
  padding: 12px 24px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border-radius: 4px;
  white-space: nowrap;
}

.upload-btn:hover {
  background: #45a049;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 15px;
  margin: 30px 0;
}

.back-btn {
  flex: 1;
  padding: 14px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #4CAF50;
  background: white;
  border: 2px solid #4CAF50;
  border-radius: 4px;
}

.back-btn:hover {
  background: #4CAF50;
  color: white;
}

.register-btn {
  flex: 1;
  background: #ff8c42;
  color: white;
  border: none;
  padding: 14px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  border-radius: 4px;
}

.register-btn:hover:not(:disabled) {
  background: #e6763a;
}

.register-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Error Section */
.error-section {
  margin: 20px 0;
}

.error-list {
  background: #fef2f2;
  border: 1px solid #fecaca;
  padding: 15px;
  font-size: 13px;
  color: #dc2626;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  border-radius: 4px;
}

.error-icon {
  font-size: 18px;
  margin-top: 2px;
}

.error-content {
  flex: 1;
  line-height: 1.4;
}

.error-content ul {
  margin: 8px 0 0 16px;
  padding: 0;
}

.error-content li {
  margin: 4px 0;
}

/* Disclaimer */
.disclaimer {
  background: #fff3cd;
  border: 1px solid #ffeaa7;
  padding: 15px;
  font-size: 13px;
  color: #856404;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-top: 20px;
  border-radius: 4px;
}

.disclaimer-icon {
  font-size: 18px;
  margin-top: 2px;
}

.disclaimer-content {
  flex: 1;
  line-height: 1.4;
}

/* Modal styles */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: #fff;
  padding: 30px;
  border-radius: 8px;
  max-width: 700px;
  width: 95%;
  box-shadow: 0 2px 12px rgba(0,0,0,0.25);
  max-height: 80vh;
  overflow-y: auto;
}

.privacy-title {
  margin-top: 0;
  color: #ff8c42;
  font-size: 24px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 10px;
}

.modal h2 {
  margin-top: 0;
  color: #ff8c42;
}

.modal p {
  margin: 15px 0;
  color: #374151;
  line-height: 1.6;
}

.justified-text {
  text-align: justify;
}

.modal-actions {
  text-align: right;
  margin-top: 16px;
}

.btn-primary {
  background: #ff8c42;
  color: #fff;
  padding: 10px 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

/* Mobile Responsive */
@media (max-width: 1200px) {
  .left-side {
    width: 95vw;
    padding: 20px;
  }
  
  .right-side {
    display: none;
  }
  
  .form-row {
    flex-direction: column;
    gap: 10px;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>