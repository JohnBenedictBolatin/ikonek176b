<template>
  <Head>
    <title>Resident Registration Form</title>
  </Head>

  <!-- Privacy Modal -->
  <div v-if="showPrivacy" class="modal-backdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="privacyTitle">
      <h2 id="privacyTitle" class="privacy-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-title">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
        Privacy Notice
      </h2>
      <p class="justified-text">
        Thank you for registering! Your privacy is important to us. This privacy notice is issued in compliance with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong> of the Philippines, which protects the fundamental human right to privacy and communication while ensuring the free flow of information for innovation, growth, and national development.
      </p>
      <p class="justified-text">
        This platform collects personal information such as your name, contact details, address, and other relevant data when you request barangay documents or use our community interaction features. We may also gather non-personal data like device and browser information to improve the system's performance and security.
      </p>
      <p class="justified-text" style="margin-top: 15px; margin-bottom: 10px;">
        <strong>How We Use Your Data:</strong>
      </p>
      <ol class="privacy-list justified-text">
        <li>To process your document requests and service applications</li>
        <li>To verify your identity and residency status</li>
        <li>To enable communication with barangay officials and staff</li>
        <li>To send you important updates, notifications, and announcements via SMS or other communication channels</li>
        <li>To improve our services and user experience</li>
        <li>To maintain records for barangay administration and governance</li>
        <li>To comply with legal and regulatory requirements</li>
        <li>To ensure system security and prevent fraud or unauthorized access</li>
        <li>To facilitate community engagement features such as posts, discussions, and event participation</li>
        <li>To generate reports and analytics for barangay planning and development</li>
      </ol>
      <p class="justified-text">
        We ensure that your data is stored securely and protected against unauthorized access. While we take appropriate security measures, please note that no system can guarantee absolute security. We do not sell or share your personal data with third parties, except with authorized barangay personnel or government agencies when required by law.
      </p>
      <p class="justified-text">
        As a data subject under the Data Privacy Act, you have the right to access, correct, or delete your personal information, or to withdraw your consent at any time by contacting us at ikonek176b@dev.ph. Our website uses cookies to improve user experience.
      </p>
      <p class="justified-text">
        By using iKonek176B, you agree to the terms of this Privacy Policy. For any concerns, contact us at ikonek176b@dev.ph, +639193076338, or visit the Barangay 176B office.
      </p>
      <div class="privacy-checkbox-container">
        <label class="privacy-checkbox-label">
          <input 
            type="checkbox" 
            v-model="privacyAcknowledged" 
            class="privacy-checkbox"
          />
          <span class="privacy-checkbox-text">I have read and understand the Privacy Notice</span>
        </label>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" @click="closePrivacy" :disabled="!privacyAcknowledged">Continue</button>
      </div>
    </div>
  </div>

  <!-- OTP Verification Modal -->
  <div v-if="showOtpModal" class="modal-backdrop" @click.self="closeOtpModal">
    <div class="modal otp-modal" role="dialog" aria-modal="true" aria-labelledby="otpTitle">
      <h2 id="otpTitle" class="privacy-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-title">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
        </svg>
        Verify Phone Number
      </h2>
      <p class="justified-text">
        We've sent a 6-digit OTP code to <strong>+63{{ displayPhoneNumber }}</strong>. 
        Please enter the code below to verify your phone number.
      </p>
      
      <div class="otp-input-group">
        <input 
          v-model="otpCode" 
          type="text" 
          placeholder="Enter 6-digit OTP" 
          class="otp-input"
          maxlength="6"
          @input="onOtpInput"
          ref="otpInputRef"
        />
        <div v-if="otpError" class="error-message" style="margin-top: 8px;">
          {{ otpError }}
        </div>
        <div v-if="otpSuccess" class="success-message" style="margin-top: 8px;">
          {{ otpSuccess }}
        </div>
      </div>

      <div class="otp-actions">
        <button 
          type="button" 
          class="btn-secondary" 
          @click="closeOtpModal"
          :disabled="isVerifyingOtp || form.processing"
        >
          CANCEL
        </button>
        <button 
          type="button" 
          class="btn-primary" 
          @click="verifyOtp"
          :disabled="isVerifyingOtp || otpCode.length !== 6 || form.processing"
        >
          {{ isVerifyingOtp ? 'VERIFYING...' : (form.processing ? 'SUBMITTING...' : 'VERIFY & SUBMIT') }}
        </button>
      </div>

      <div class="resend-section">
        <p class="resend-text">Didn't receive the code?</p>
        <button 
          type="button" 
          class="btn-resend-otp" 
          @click="resendOtp"
          :disabled="isSendingOtp || resendCooldown > 0"
        >
          {{ resendCooldown > 0 ? `Resend in ${resendCooldown}s` : (isSendingOtp ? 'SENDING...' : 'RESEND OTP') }}
        </button>
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

      <!-- Registration Form -->
      <div>
        <div class="form-title">
          <h1>Registration Form - Resident</h1>
          <p style="color: #666; margin-top: 8px; font-size: 14px;">Fill out the form below to register as a resident</p>
        </div>

        <form @submit.prevent="submit" class="registration-form" enctype="multipart/form-data" @keydown.enter.prevent>
        <!-- Top Row: Personal Information and Contact Information -->
        <div class="form-columns">
          <!-- Left Column: Personal Information -->
          <div class="form-column">
            <div class="form-section">
              <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-section">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                Personal Information
              </h3>
              
              <div class="form-group">
                <input v-model="form.last_name" @blur="form.last_name = toTitleCase(form.last_name)" placeholder="Last Name*"  class="form-input" required />
              </div>

              <div class="form-row">
                <div class="form-group half">
                  <input v-model="form.first_name" @blur="form.first_name = toTitleCase(form.first_name)" placeholder="First Name*" class="form-input" required />
                </div>
                
                <div class="form-group half">
                  <input v-model="form.middle_name" @blur="form.middle_name = toTitleCase(form.middle_name)" placeholder="Middle Name" class="form-input" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group half">
                  <div class="select-wrapper">
                    <select v-model="form.suffix" class="form-input">
                      <option value="">Suffix</option>
                      <option value="Jr.">Jr.</option>
                      <option value="Sr.">Sr.</option>
                      <option value="II">II</option>
                      <option value="III">III</option>
                      <option value="IV">IV</option>
                      <option value="V">V</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </div>
                </div>

                <div class="form-group half">
                  <div class="select-wrapper">
                    <select v-model="form.sex" class="form-input" required>
                      <option value="">Sex*</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </div>
                </div>
              </div>

              <div class="form-row" style="margin-top:8px;">
                <div class="form-group" style="max-width: 400px;">
                  <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 500; color: #333;">Birthdate*</label>
                  <div class="input-with-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-input">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-9-6h.008v.008H12v-.008Z" />
                    </svg>
                    <!-- REPLACED: single date input with month/day/year split -->
                    <div class="birthdate-split" style="display:flex;gap:6px;align-items:center;">
                      <div class="select-wrapper" style="flex: 1; min-width: 0; position: relative;">
                        <select v-model="birth_month" class="form-input" required @change="onMonthOrYearChange" style="flex: 1; min-width: 0;">
                          <option value="">Month</option>
                          <option v-for="(m, idx) in months" :key="idx" :value="idx+1">{{ m }}</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                      </div>

                      <div class="select-wrapper" style="flex: 0.8; min-width: 0; position: relative;">
                        <select v-model.number="birth_day" class="form-input" required style="flex: 0.8; min-width: 0;">
                          <option value="">Day</option>
                          <option v-for="d in daysInSelectedMonth" :key="d" :value="d">{{ d }}</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                      </div>

                      <div class="select-wrapper" style="flex: 1; min-width: 0; position: relative;">
                        <select v-model.number="birth_year" class="form-input" required @change="onMonthOrYearChange" style="flex: 1; min-width: 0;">
                          <option value="">Year</option>
                          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group half">
                  <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 500; color: #333;">Nationality*</label>
                  <input v-model="form.nationality" placeholder="Nationality*" class="form-input" readonly style="background-color: #f5f5f5; cursor: not-allowed;" required />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group half">
                  <div class="select-wrapper">
                    <select v-model="form.civil_status" class="form-input" required>
                      <option value="">Civil Status*</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Contact Information -->
          <div class="form-column">
            <div class="form-section">
              <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-section">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                </svg>
                Contact Information
              </h3>
              
              <div class="form-group">
                <div class="phone-input">
                  <span class="country-code">+63</span>
                  <input 
                    v-model="form.contact_number" 
                    @input="handlePhoneInput"
                    @paste.prevent="handlePhonePaste"
                    @keydown="handlePhoneKeydown"
                    type="tel"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    placeholder="9XXXXXXXXX or 09XXXXXXXXX*" 
                    maxlength="11" 
                    class="form-input" 
                    required 
                  />
                </div>
                <small class="helper-text">Enter 10 or 11 digits (e.g., 9123456789 or 09123456789)</small>
                <div v-if="form.errors.contact_number" class="error-message">
                  {{ form.errors.contact_number }}
                </div>
              </div>

              <!-- Password Section -->
              <h3 class="section-title" style="margin-top: 25px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-section">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                Account Security
              </h3>
              
              <div class="form-group">
                <div class="password-input-wrapper" style="position: relative;">
                  <input :type="showPassword ? 'text' : 'password'" v-model="form.password" @input="clearError('password'); validatePasswordRequirements(); validateMatch()" placeholder="Enter password" class="form-input" required style="padding-right: 45px;" />
                  <button type="button" @click="showPassword = !showPassword" class="password-toggle-btn" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #000; display: flex; align-items: center; justify-content: center;">
                    <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg" style="width: 20px; height: 20px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg" style="width: 20px; height: 20px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="form-group">
                <div class="password-input-wrapper" style="position: relative;">
                  <input :type="showConfirmPassword ? 'text' : 'password'" v-model="confirmPassword" @input="clearError('confirmPassword'); validateMatch()" placeholder="Confirm password" class="form-input" required style="padding-right: 45px;" />
                  <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="password-toggle-btn" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #000; display: flex; align-items: center; justify-content: center;">
                    <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg" style="width: 20px; height: 20px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg" style="width: 20px; height: 20px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                  </button>
                </div>
                <div class="error-message" v-if="localErrors.confirmPassword">{{ localErrors.confirmPassword }}</div>
                <div class="password-requirements">
                  <p class="requirements-title">Password Requirements:</p>
                  <ul class="requirements-list">
                    <li :class="{ 'requirement-met': hasMinLength, 'requirement-unmet': !hasMinLength }">
                      <span class="requirement-icon">
                        <svg v-if="hasMinLength" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-check">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-x">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </span>
                      At least 8 characters
                    </li>
                    <li :class="{ 'requirement-met': hasUppercase, 'requirement-unmet': !hasUppercase }">
                      <span class="requirement-icon">
                        <svg v-if="hasUppercase" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-check">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-x">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </span>
                      At least one uppercase letter (A-Z)
                    </li>
                    <li :class="{ 'requirement-met': hasLowercase, 'requirement-unmet': !hasLowercase }">
                      <span class="requirement-icon">
                        <svg v-if="hasLowercase" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-check">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-x">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </span>
                      At least one lowercase letter (a-z)
                    </li>
                    <li :class="{ 'requirement-met': hasNumber, 'requirement-unmet': !hasNumber }">
                      <span class="requirement-icon">
                        <svg v-if="hasNumber" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-check">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-x">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </span>
                      At least one number (0-9)
                    </li>
                    <li :class="{ 'requirement-met': hasSpecialChar, 'requirement-unmet': !hasSpecialChar }">
                      <span class="requirement-icon">
                        <svg v-if="hasSpecialChar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-check">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-x">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </span>
                      At least one special character (!@#$%^&*()_+-=[]{}|;:,.<>?)
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Full Width: Address Information (SPLIT INTO FIELDS) -->
        <div class="form-section full-width">
          <h3 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-section">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            Address Information
          </h3>
          
          <!-- house_number & street -->
          <div class="form-row">
            <div class="form-group half">
              <input v-model="form.house_number" placeholder="House / Unit / Building No.*" class="form-input" required />
            </div>

            <div class="form-group half">
              <input v-model="form.street" @blur="form.street = toTitleCase(form.street)" placeholder="Street*" class="form-input" required />
            </div>
          </div>

          <!-- city/municipality & barangay -->
          <div class="form-row" style="margin-top:8px;">
            <div class="form-group half">
              <input v-model="form.city" placeholder="City / Municipality*" class="form-input" readonly style="background-color: #f5f5f5; cursor: not-allowed;" />
            </div>

            <div class="form-group half">
              <input v-model="form.province" placeholder="Province / Region*" class="form-input" readonly style="background-color: #f5f5f5; cursor: not-allowed;" />
            </div>
          </div>

          <!-- Original 3-column row kept exactly as before (barangay, phase, package) -->
          <div class="form-row" style="margin-top:8px;">
            <div class="form-group third">
              <input v-model="form.barangay" placeholder="Barangay*" class="form-input" readonly style="background-color: #f5f5f5; cursor: not-allowed;" />
            </div>

            <div class="form-group third">
              <div class="select-wrapper">
                <select v-model="form.phase" class="form-input" required>
                  <option value="">Phase</option>
                  <option value="Phase 2">Phase 2</option>
                  <option value="Phase 3">Phase 3</option>
                  <option value="Phase 5">Phase 5</option>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
              </div>
            </div>

            <div class="form-group third">
              <div class="select-wrapper">
                <select v-model="form.package" class="form-input" required>
                  <option value="">Package</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Full Width: Proof of Residency -->
        <div class="form-section full-width">
          <h3 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg-section">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            Supporting Documents
          </h3>

          <div class="form-row" style="align-items:center;">
            <div class="form-group flex-grow">
              <div class="select-wrapper">
                <select v-model="form.proof_of_residency" class="form-input" required>
                  <option value="">Type of Identification/Document*</option>
                  <option>Government-Issued IDs</option>
                  <option>Utility Bill</option>
                  <option>Barangay and Local Documents</option>
                </select>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
              </div>
            </div>

            <!-- IMPORTANT: button must be type="button" to avoid submitting the form -->
            <div class="form-group">
              <button type="button" class="upload-btn" @click="uploadId">
                UPLOAD
              </button>
            </div>
          </div>

          <!-- Hidden file input. NOTE ref name matches the script variable proofInputRef -->
          <input
            ref="proofInputRef"
            type="file"
            accept="image/*"
            style="display:none"
            @change="handleFileChange"
          />

          <!-- Preview area -->
          <div v-if="previewUrl" class="form-row" style="margin-top:10px;align-items:center;">
            <div class="form-group">
              <img :src="previewUrl" alt="Preview" style="max-width:120px;max-height:120px;border-radius:8px;object-fit:cover" />
            </div>
            <div class="form-group">
              <div>{{ selectedFileName }}</div>
              <button type="button" class="remove-btn" @click="removeFile">Remove</button>
            </div>
          </div>

          <div v-if="form.errors.proof" class="error-message">{{ form.errors.proof }}</div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
          <Link :href="route('login')" class="back-btn">
            BACK TO LOGIN
          </Link>
          <button 
            v-if="!phoneVerified"
            class="verify-btn" 
            :disabled="isSendingOtp || !canVerifyPhone" 
            type="button"
            @click="openOtpModal"
          >
            {{ isSendingOtp ? 'SENDING OTP...' : 'VERIFY PHONE NUMBER' }}
          </button>
          <div v-else class="verified-status">
            <span class="verified-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
              </svg>
            </span>
            <span>Phone verified. Registration will be submitted automatically...</span>
          </div>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
          <span class="disclaimer-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
          </span>
          <div class="disclaimer-content">
            <strong>Disclaimer</strong><br>
            Your registration request is subject for approval. Check your messages regularly for an update.
          </div>
        </div>
      </form>
      </div>
    </div>

    <!-- Right side for background/illustrations -->
    <div class="right-side">
      <!-- This space is for your background image/3D character -->
    </div>

    <!-- Success Confirmation Modal -->
    <div v-if="showSuccessModal" class="modal-overlay" @click.stop>
      <div class="success-modal" @click.stop>
        <div class="success-modal-header">
          <div class="success-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="success-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="success-modal-title">Registration Request Submitted Successfully!</h3>
        </div>
        <div class="success-modal-body">
          <p class="success-message">
            Your registration request has been successfully submitted and is now pending approval. 
            Your request will be reviewed within 1-2 business days. 
            You will receive an update via SMS once your registration has been reviewed. 
            Please check your messages regularly for updates.
          </p>
        </div>
        <div class="success-modal-footer">
          <button @click="closeSuccessModal" class="success-modal-btn">
            OK
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Link, Head, useForm, router } from '@inertiajs/vue3'
import axios from 'axios'
import { toTitleCase } from '@/utils/textCase'

// showPrivacy defaults to true so modal is visible on page load
const showPrivacy = ref(true)
const privacyAcknowledged = ref(false)
function closePrivacy() { 
  if (privacyAcknowledged.value) {
    showPrivacy.value = false 
  }
}

// prevent background scrolling while modal is open  
watch(showPrivacy, (val) => {
  document.body.style.overflow = val ? 'hidden' : ''
})

// cleanup in case component unmounts
onBeforeUnmount(() => { 
  document.body.style.overflow = ''
})

// Close success modal and redirect to login
const closeSuccessModal = () => {
  showSuccessModal.value = false
  // Redirect to login page after closing modal with a slight delay for smooth transition
  setTimeout(() => {
    router.visit(route('login'), {
      replace: true,
      preserveState: false,
      preserveScroll: false
    })
  }, 300)
}

// NOTE: keys below are aligned to database columns where applicable
const form = useForm({
  last_name: '',
  first_name: '',
  middle_name: '',
  suffix: '',
  birthdate: '',
  sex: '',
  civil_status: '',
  contact_number: '',
  password: '',
  address: '',
  house_number: '',
  street: '',
  city: 'Caloocan City',
  province: 'Metro Manila',
  barangay: 'Barangay 176B',
  fk_role_id: '1',
  phase: '',
  package: '',
  proof_of_residency: '',
  proof: null,
  // personal info fields - included so they are posted
  nationality: 'Filipino',
  email: '',
})

const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const localErrors = reactive({ password: null, confirmPassword: null })

function normalizeCasing() {
  form.first_name = toTitleCase(form.first_name)
  form.middle_name = toTitleCase(form.middle_name)
  form.last_name = toTitleCase(form.last_name)
  form.street = toTitleCase(form.street)
}

// OTP Verification state
const phoneVerified = ref(false)
const showOtpModal = ref(false)
const otpCode = ref('')
const isSendingOtp = ref(false)
const isVerifyingOtp = ref(false)
const otpError = ref('')
const otpSuccess = ref('')
const showSuccessModal = ref(false)
const resendCooldown = ref(0)
const otpInputRef = ref(null)

const passwordsMatch = computed(() => form.password === confirmPassword.value)

// Password requirement validators
const hasMinLength = computed(() => form.password && form.password.length >= 8)
const hasUppercase = computed(() => form.password && /[A-Z]/.test(form.password))
const hasLowercase = computed(() => form.password && /[a-z]/.test(form.password))
const hasNumber = computed(() => form.password && /[0-9]/.test(form.password))
const hasSpecialChar = computed(() => form.password && /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(form.password))

// Check if all required fields are filled before allowing phone verification
const canVerifyPhone = computed(() => {
  const phoneDigits = form.contact_number ? form.contact_number.replace(/\D/g, '') : ''
  const isValidPhone = phoneDigits.length === 10 || phoneDigits.length === 11
  
  // Check all required fields are filled
  return isValidPhone &&
         form.first_name &&
         form.last_name &&
         birth_month.value && birth_day.value && birth_year.value &&
         form.sex &&
         form.civil_status &&
         form.nationality &&
         form.house_number &&
         form.street &&
         form.phase &&
         form.package &&
         form.proof_of_residency &&
         form.password &&
         hasMinLength.value &&
         hasUppercase.value &&
         hasLowercase.value &&
         hasNumber.value &&
         hasSpecialChar.value &&
         confirmPassword.value &&
         passwordsMatch.value
})

// Display phone number for OTP modal (formatted)
const displayPhoneNumber = computed(() => {
  const phoneDigits = form.contact_number ? form.contact_number.replace(/\D/g, '') : ''
  if (phoneDigits.length === 11 && phoneDigits[0] === '0') {
    return phoneDigits.substring(1)
  }
  return phoneDigits
})

const canSubmit = computed(() => {
  const phoneDigits = form.contact_number ? form.contact_number.replace(/\D/g, '') : ''
  
  return form.password && 
         hasMinLength.value &&
         hasUppercase.value &&
         hasLowercase.value &&
         hasNumber.value &&
         hasSpecialChar.value &&
         confirmPassword.value && 
         passwordsMatch.value &&
         form.contact_number && (phoneDigits.length === 10 || phoneDigits.length === 11) &&
         form.first_name &&
         form.last_name &&
         birth_month.value && birth_day.value && birth_year.value &&
         form.sex &&
         form.civil_status &&
         form.nationality &&
         form.house_number &&
         form.street &&
         form.phase &&
         form.package &&
         form.proof_of_residency &&
         form.proof && // File must be uploaded
         phoneVerified.value // Phone must be verified
})

// ---------- Birthdate split fields & helpers ----------
const months = [
  'January','February','March','April','May','June','July','August','September','October','November','December'
]

// default years: from current year down to currentYear - 100
const currentYear = new Date().getFullYear()
const years = Array.from({length: 101}, (_, i) => currentYear - i)

// reactive split values (numbers for month & day & year where appropriate)
const birth_month = ref('') // 1-12
const birth_day = ref('')   // 1-31
const birth_year = ref('')  // full year (e.g., 1980)

// compute days available based on month/year (handles leap year)
function daysInMonthCount(y, m) {
  if (!y || !m) return 31
  // JS months are 0-indexed
  return new Date(y, m, 0).getDate()
}

const daysInSelectedMonth = computed(() => {
  const y = Number(birth_year.value)
  const m = Number(birth_month.value)
  const count = daysInMonthCount(y || currentYear, m || 1)
  return Array.from({length: count}, (_, i) => i + 1)
})

// Ensure day stays valid when month/year changes
function onMonthOrYearChange() {
  const max = daysInMonthCount(Number(birth_year.value) || currentYear, Number(birth_month.value) || 1)
  if (birth_day.value && Number(birth_day.value) > max) {
    birth_day.value = max
  }
}

// Parse form.birthdate (expects 'YYYY-MM-DD') into split fields
function splitBirthdate() {
  const v = form.birthdate
  if (!v) {
    birth_month.value = ''
    birth_day.value = ''
    birth_year.value = ''
    return
  }
  // Accept ISO YYYY-MM-DD
  const parts = ('' + v).split('-')
  if (parts.length >= 3) {
    birth_year.value = Number(parts[0]) || ''
    birth_month.value = Number(parts[1]) || ''
    birth_day.value = Number(parts[2]) || ''
    // ensure day is valid for month/year
    onMonthOrYearChange()
  } else {
    // fallback: try Date parsing
    const d = new Date(v)
    if (!isNaN(d.getTime())) {
      birth_year.value = d.getFullYear()
      birth_month.value = d.getMonth() + 1
      birth_day.value = d.getDate()
    } else {
      birth_month.value = ''
      birth_day.value = ''
      birth_year.value = ''
    }
  }
}

// Merge split fields back into form.birthdate as YYYY-MM-DD
function pad(num) {
  return num.toString().padStart(2, '0')
}
function mergeBirthdate() {
  // if any part missing, clear form.birthdate so server can validate requiredness
  if (!birth_year.value || !birth_month.value || !birth_day.value) {
    form.birthdate = ''
    return
  }
  const y = Number(birth_year.value)
  const m = Number(birth_month.value)
  const d = Number(birth_day.value)
  // ensure day <= daysInMonth
  const max = daysInMonthCount(y, m)
  const safeDay = Math.min(d, max)
  form.birthdate = `${y}-${pad(m)}-${pad(safeDay)}`
}

// watch form.birthdate so server-provided value populates split fields
watch(() => form.birthdate, () => {
  splitBirthdate()
})

// Reset phone verification if phone number changes
watch(() => form.contact_number, () => {
  if (phoneVerified.value) {
    phoneVerified.value = false
  }
})

// Restrict phone number input to numbers only
function handlePhoneInput(event) {
  // Remove all non-numeric characters
  let value = event.target.value.replace(/\D/g, '')
  
  // Limit to 11 characters maximum
  if (value.length > 11) {
    value = value.substring(0, 11)
  }
  
  // Update the form value
  form.contact_number = value
}

// Handle paste events to filter non-numeric characters
function handlePhonePaste(event) {
  event.preventDefault()
  const pastedText = (event.clipboardData || window.clipboardData).getData('text')
  
  // Remove all non-numeric characters
  let value = pastedText.replace(/\D/g, '')
  
  // Limit to 11 characters maximum
  if (value.length > 11) {
    value = value.substring(0, 11)
  }
  
  // Update the form value
  form.contact_number = value
}

// Prevent non-numeric key presses
function handlePhoneKeydown(event) {
  // Allow: backspace, delete, tab, escape, enter, home, end, left arrow, right arrow
  if ([8, 9, 27, 13, 46, 35, 36, 37, 39].indexOf(event.keyCode) !== -1 ||
      // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
      (event.keyCode === 65 && event.ctrlKey === true) ||
      (event.keyCode === 67 && event.ctrlKey === true) ||
      (event.keyCode === 86 && event.ctrlKey === true) ||
      (event.keyCode === 88 && event.ctrlKey === true)) {
    return
  }
  // Ensure that it is a number and stop the keypress
  if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
    event.preventDefault()
  }
}

// call on mount to populate if server provided birthdate
onMounted(() => {
  showPrivacy.value = true
  splitBirthdate()
})

function clearError(field) {
  if (field === 'password') {
    localErrors.password = null
    if (form.clearErrors) form.clearErrors('password')
  } else if (field === 'confirmPassword') {
    localErrors.confirmPassword = null
  }
}

function validatePasswordRequirements() {
  if (!form.password) {
    localErrors.password = null
    return
  }
  
  const errors = []
  if (!hasMinLength.value) {
    errors.push('Password must be at least 8 characters long')
  }
  if (!hasUppercase.value) {
    errors.push('Password must contain at least one uppercase letter')
  }
  if (!hasLowercase.value) {
    errors.push('Password must contain at least one lowercase letter')
  }
  if (!hasNumber.value) {
    errors.push('Password must contain at least one number')
  }
  if (!hasSpecialChar.value) {
    errors.push('Password must contain at least one special character')
  }
  
  if (errors.length > 0) {
    localErrors.password = errors.join('. ')
  } else {
    localErrors.password = null
  }
}

function validateMatch() {
  localErrors.confirmPassword = null
  if (!confirmPassword.value) {
    localErrors.confirmPassword = 'Please confirm your password.'
    return
  }
  if (!passwordsMatch.value) localErrors.confirmPassword = 'Passwords do not match.'
}

function uploadId() {
  // open the hidden file input
  if (proofInputRef.value) proofInputRef.value.click()
}

const proofInputRef = ref(null)
const previewUrl = ref(null)
const selectedFileName = ref('')

// handle file selection and attach File object to Inertia form
function handleFileChange(e) {
  const files = e.target.files || e.dataTransfer?.files
  if (!files || files.length === 0) {
    form.proof = null
    previewUrl.value = null
    selectedFileName.value = ''
    return
  }
  const file = files[0]
  // simple client-side type/size checks (optional)
  if (!file.type.startsWith('image/')) {
    form.errors.proof = 'Selected file must be an image.'
    form.proof = null
    return
  }
  // limit to 5MB client-side (server will also validate)
  if (file.size > 5 * 1024 * 1024) {
    form.errors.proof = 'Image size must be 5MB or less.'
    form.proof = null
    return
  }

  form.proof = file
  selectedFileName.value = file.name

  // release previous object URL
  if (previewUrl.value) URL.revokeObjectURL(previewUrl.value)
  previewUrl.value = URL.createObjectURL(file)
  
  console.log('[DEBUG:file-change] Accepted file:', {
    name: file.name,
    type: file.type,
    size_bytes: file.size,
  });
}

// remove file selection
function removeFile() {
  form.proof = null
  if (proofInputRef.value) {
    proofInputRef.value.value = ''
  }
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
    previewUrl.value = null
  }
  selectedFileName.value = ''
}

// merge the split address fields into form.address before posting
function mergeAddressFields() {
  // prefer explicit form.barangay if provided
  const barangayToUse = (form.barangay && form.barangay.toString().trim()) ? form.barangay.toString().trim() : ''
  const parts = [
    form.house_number && form.house_number.toString().trim() ? form.house_number.toString().trim() : '',
    form.street && form.street.toString().trim() ? form.street.toString().trim() : '',
    barangayToUse,
    form.city && form.city.toString().trim() ? form.city.toString().trim() : '',
    form.province && form.province.toString().trim() ? form.province.toString().trim() : ''
  ].filter(Boolean)
  form.address = parts.join(', ')
}

// Separate function for actual submission (can be called from OTP verification or button click)
async function submitRegistration() {
  console.log('[DEBUG:submitRegistration] ========== SUBMIT REGISTRATION CALLED ==========');
  console.log('[DEBUG:submitRegistration] canSubmit:', canSubmit.value);
  console.log('[DEBUG:submitRegistration] form.processing:', form.processing);
  
  normalizeCasing()

  // Check if form is already processing
  if (form.processing) {
    console.log('[DEBUG:submitRegistration] Form is already processing, ignoring duplicate submit')
    return
  }
  
  // Ensure phone is verified
  if (!phoneVerified.value) {
    console.error('[DEBUG:submitRegistration] ❌ Phone not verified! Cannot submit.');
    alert('Please verify your phone number with OTP before submitting the registration form.');
    return;
  }
  
  localErrors.password = null
  localErrors.confirmPassword = null

  // Validate password - read directly from form and ref, don't trim (user might have spaces intentionally)
  const passwordStr = form.password ? String(form.password) : ''
  const confirmPasswordStr = confirmPassword.value ? String(confirmPassword.value) : ''
  
  console.log('[DEBUG:submit] Password validation:', {
    password: passwordStr,
    passwordLength: passwordStr.length,
    confirmPassword: confirmPasswordStr,
    confirmPasswordLength: confirmPasswordStr.length,
    passwordsMatch: passwordStr === confirmPasswordStr,
    formPasswordRaw: form.password,
    confirmPasswordRaw: confirmPassword.value
  })
  
  // Validate password requirements
  validatePasswordRequirements()
  
  if (!confirmPasswordStr) {
    localErrors.confirmPassword = 'Please confirm your password.'
    console.error('[DEBUG:submit] ❌ Confirm password missing');
  } else if (passwordStr !== confirmPasswordStr) {
    localErrors.confirmPassword = 'Passwords do not match.'
    console.error('[DEBUG:submit] ❌ Passwords do not match');
  }

  // Don't block submission - let backend handle validation
  if (localErrors.password || localErrors.confirmPassword) {
    console.warn('[DEBUG:submit] ⚠️ Password validation warnings, but proceeding with submission');
  }
  
  console.log('[DEBUG:submit] ✅ Proceeding with submission');

  // MERGE birthdate fields into form.birthdate BEFORE posting
  mergeBirthdate()

  // MERGE address fields here (IMPORTANT: done immediately before posting)
  mergeAddressFields()

  console.log('[DEBUG:submitRegistration] ========== STARTING FORM POST ==========');
  console.log('[DEBUG:submitRegistration] Contact number (raw):', form.contact_number);
  console.log('[DEBUG:submitRegistration] Phone verified:', phoneVerified.value);
  
  // Normalize phone number before submission to match OTP verification format
  const phoneDigits = form.contact_number ? form.contact_number.replace(/\D/g, '') : ''
  let normalizedPhone = phoneDigits
  if (phoneDigits.length === 10 && phoneDigits[0] === '9') {
    normalizedPhone = '0' + phoneDigits
  }
  form.contact_number = normalizedPhone
  
  console.log('[DEBUG:submitRegistration] Contact number (normalized):', form.contact_number);
  console.log('[DEBUG:submitRegistration] First name:', form.first_name);
  console.log('[DEBUG:submitRegistration] Last name:', form.last_name);
  console.log('[DEBUG:submitRegistration] Password:', form.password ? 'Present (' + form.password.length + ' chars)' : 'Missing');
  console.log('[DEBUG:submitRegistration] Route:', route('register_resident.store'));

  console.log('[DEBUG:submitRegistration] ========== CALLING form.post NOW ==========');
  const routeUrl = route('register_resident.store');
  console.log('[DEBUG:submitRegistration] Route URL:', routeUrl);
  console.log('[DEBUG:submitRegistration] Form object exists:', !!form);
  console.log('[DEBUG:submitRegistration] form.post type:', typeof form.post);
  
  // Ensure form.post exists
  if (typeof form.post !== 'function') {
    console.error('[DEBUG:submitRegistration] ❌ form.post is not a function! Form object:', form);
    alert('Form submission error. Please refresh the page and try again.');
    return
  }
  
  try {
    console.log('[DEBUG:submitRegistration] Executing form.post NOW...');
    form.post(routeUrl, {
    // force multipart/form-data so files are uploaded reliably
    forceFormData: true,

    onStart: () => {
      console.log('[DEBUG:inertia] Request started (forceFormData ON)');
      console.log('[DEBUG:inertia] Form data being sent:', {
        contact_number: form.contact_number,
        first_name: form.first_name,
        last_name: form.last_name,
        password: form.password ? '***' : 'MISSING',
        has_proof: !!form.proof
      });
    },
    onProgress: (progressEvent) => {
      if (progressEvent && progressEvent.percentage) {
        console.log('upload progress', progressEvent.percentage);
      }
    },
    onError: (errors) => {
      // show server validation errors in console for debugging
      console.error('[DEBUG:inertia] ========== SERVER VALIDATION ERRORS ==========');
      console.error('[DEBUG:inertia] Errors object:', errors);
      console.error('[DEBUG:inertia] Errors JSON:', JSON.stringify(errors, null, 2));
      console.error('[DEBUG:inertia] Phone verified status:', phoneVerified.value);
      console.error('[DEBUG:inertia] Contact number in form:', form.contact_number);
      
      // Show user-friendly error messages
      let errorMessages = []
      
      // Check for OTP verification error first (most important)
      if (errors.contact_number) {
        const contactError = Array.isArray(errors.contact_number) ? errors.contact_number[0] : errors.contact_number
        errorMessages.push('Contact number: ' + contactError)
        
        // If OTP verification failed, reset the verification status
        if (contactError.includes('verify') || contactError.includes('OTP')) {
          phoneVerified.value = false
          console.error('[DEBUG:inertia] OTP verification failed, resetting phoneVerified status')
        }
      }
      
      if (errors.proof) {
        const proofError = Array.isArray(errors.proof) ? errors.proof[0] : errors.proof
        errorMessages.push('Proof file: ' + proofError)
        console.error('[DEBUG:inertia] File upload error:', errors.proof);
      }
      if (errors.password) {
        const passwordError = Array.isArray(errors.password) ? errors.password[0] : errors.password
        errorMessages.push('Password: ' + passwordError)
        // Server rejected password - reset it for security
        form.reset('password')
        confirmPassword.value = ''
      }
      if (errors.email) {
        const emailError = Array.isArray(errors.email) ? errors.email[0] : errors.email
        errorMessages.push('Email: ' + emailError)
      }
      if (errors.first_name) {
        const firstNameError = Array.isArray(errors.first_name) ? errors.first_name[0] : errors.first_name
        errorMessages.push('First name: ' + firstNameError)
      }
      if (errors.last_name) {
        const lastNameError = Array.isArray(errors.last_name) ? errors.last_name[0] : errors.last_name
        errorMessages.push('Last name: ' + lastNameError)
      }
      
      // Check for any other errors
      Object.keys(errors).forEach(key => {
        if (!['contact_number', 'proof', 'password', 'email', 'first_name', 'last_name'].includes(key)) {
          const errorValue = Array.isArray(errors[key]) ? errors[key][0] : errors[key]
          errorMessages.push(`${key}: ${errorValue}`)
        }
      })
      
      // Show all errors to user
      if (errorMessages.length > 0) {
        alert('Please fix the following errors:\n\n' + errorMessages.join('\n'))
      } else {
        // Show generic error if no specific errors found
        alert('An error occurred while submitting your registration. Please check the console for details and try again.')
        console.error('[DEBUG:inertia] No specific error messages found, but errors object exists:', errors)
      }
    },
    onSuccess: (page) => {
      console.log('[DEBUG:inertia] SUCCESS — Server accepted all data.');
      console.log('[DEBUG:inertia] Response page:', page);
      confirmPassword.value = ''
      localErrors.password = null
      localErrors.confirmPassword = null
      
      // Show success modal immediately - don't redirect until user clicks OK
      showSuccessModal.value = true
      
      // Prevent any automatic redirects - let user see the success message first
    },
    onFinish: () => {
      console.log('[DEBUG:inertia] Request finished.');
      console.log('[DEBUG:inertia] Form processing state:', form.processing);
    }
  })
  } catch (error) {
    console.error('[DEBUG:submit] ❌ EXCEPTION CAUGHT IN TRY BLOCK:', error);
    console.error('[DEBUG:submit] Error message:', error.message);
    console.error('[DEBUG:submit] Error stack:', error.stack);
    alert('An error occurred while submitting the form: ' + (error.message || 'Unknown error') + '\n\nPlease check the console for details and try again.');
  }
}

// Wrapper function for form submit event (called when button is clicked)
async function submit(e) {
  // Prevent default form submission
  if (e) {
    e.preventDefault()
  }
  
  // Check if privacy notice has been acknowledged
  if (showPrivacy.value || !privacyAcknowledged.value) {
    alert('Please read and acknowledge the Privacy Notice before submitting the registration form.')
    showPrivacy.value = true
    return
  }
  
  // If phone is not verified, open OTP modal instead
  if (!phoneVerified.value) {
    openOtpModal()
    return
  }
  
  // If phone is verified, proceed with submission
  await submitRegistration()
}

// OTP Verification Functions
async function openOtpModal() {
  normalizeCasing()

  if (!canVerifyPhone.value) {
    alert('Please enter a valid phone number (10 or 11 digits)')
    return
  }

  otpError.value = ''
  otpSuccess.value = ''
  otpCode.value = ''
  showOtpModal.value = true
  
  // Focus OTP input after modal opens
  await nextTick()
  if (otpInputRef.value) {
    otpInputRef.value.focus()
  }

  // Automatically send OTP when modal opens
  await sendOtp()
}

function closeOtpModal() {
  showOtpModal.value = false
  otpCode.value = ''
  otpError.value = ''
  otpSuccess.value = ''
  resendCooldown.value = 0
}

function onOtpInput() {
  // Only allow digits
  otpCode.value = otpCode.value.replace(/\D/g, '')
  // Clear errors when user types
  if (otpError.value) {
    otpError.value = ''
  }
}

async function sendOtp() {
  if (!canVerifyPhone.value) {
    otpError.value = 'Please enter a valid phone number'
    return
  }

  isSendingOtp.value = true
  otpError.value = ''
  otpSuccess.value = ''

  try {
    // Normalize phone number the same way backend does
    const phoneDigits = form.contact_number.replace(/\D/g, '')
    let normalizedPhone = phoneDigits
    if (phoneDigits.length === 10 && phoneDigits[0] === '9') {
      normalizedPhone = '0' + phoneDigits
    }
    
    // Update form.contact_number to normalized version for consistency
    form.contact_number = normalizedPhone
    
    console.log('[DEBUG:sendOtp] Sending OTP to phone:', normalizedPhone)
    
    const response = await axios.post(route('otp.send'), {
      phone_number: normalizedPhone
    })

    if (response.data.success) {
      otpSuccess.value = 'OTP sent successfully! Please check your phone.'
      // Start cooldown timer (60 seconds)
      resendCooldown.value = 60
      const timer = setInterval(() => {
        resendCooldown.value--
        if (resendCooldown.value <= 0) {
          clearInterval(timer)
        }
      }, 1000)
    } else {
      otpError.value = response.data.message || 'Failed to send OTP. Please try again.'
    }
  } catch (error) {
    console.error('Error sending OTP:', error)
    
    // Handle timeout errors
    if (error.code === 'ECONNABORTED' || error.message?.includes('timeout')) {
      otpError.value = 'Request timed out. The SMS service may be slow. Please try again.'
      return
    }
    
    // Handle network errors
    if (!error.response && error.request) {
      otpError.value = 'Network error. Please check your connection and try again.'
      return
    }
    
    // Handle CSRF token mismatch specifically
    if (error.response && error.response.status === 419) {
      otpError.value = 'Session expired. Please refresh the page and try again.'
      // Optionally refresh the page after a delay
      setTimeout(() => {
        window.location.reload()
      }, 2000)
    } else {
      otpError.value = error.response?.data?.message || 'Failed to send OTP. Please try again.'
    }
  } finally {
    isSendingOtp.value = false
  }
}

async function resendOtp() {
  if (resendCooldown.value > 0 || isSendingOtp.value) {
    return
  }
  await sendOtp()
}

async function verifyOtp() {
  if (otpCode.value.length !== 6) {
    otpError.value = 'Please enter a 6-digit OTP code'
    return
  }

  isVerifyingOtp.value = true
  otpError.value = ''
  otpSuccess.value = ''

  try {
    // Normalize phone number the same way backend does
    const phoneDigits = form.contact_number.replace(/\D/g, '')
    let normalizedPhone = phoneDigits
    if (phoneDigits.length === 10 && phoneDigits[0] === '9') {
      normalizedPhone = '0' + phoneDigits
    }
    
    // Also update form.contact_number to normalized version
    form.contact_number = normalizedPhone
    
    console.log('[DEBUG:verifyOtp] Verifying OTP for phone:', normalizedPhone)
    
    const response = await axios.post(route('otp.verify'), {
      phone_number: normalizedPhone,
      otp_code: otpCode.value
    })

    if (response.data.success) {
      otpSuccess.value = 'Phone number verified successfully! Submitting registration...'
      phoneVerified.value = true
      
      console.log('[DEBUG:verifyOtp] Phone verified successfully:', normalizedPhone)
      
      // Auto-submit registration form after successful OTP verification
      setTimeout(() => {
        closeOtpModal()
        // Trigger form submission
        submitRegistration()
      }, 1000)
    } else {
      otpError.value = response.data.message || 'Invalid OTP code. Please try again.'
      otpCode.value = '' // Clear OTP input on error
    }
  } catch (error) {
    console.error('Error verifying OTP:', error)
    // Handle CSRF token mismatch specifically
    if (error.response && error.response.status === 419) {
      otpError.value = 'Session expired. Please refresh the page and try again.'
      // Optionally refresh the page after a delay
      setTimeout(() => {
        window.location.reload()
      }, 2000)
    } else {
      otpError.value = error.response?.data?.message || 'Invalid OTP code. Please try again.'
    }
    otpCode.value = '' // Clear OTP input on error
  } finally {
    isVerifyingOtp.value = false
  }
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
  background-image: url('assets/BACKGROUNDIMAGEREG1.png');
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

.form-columns {
  display: flex;
  gap: 40px;
  margin-bottom: 20px;
}

.form-column {
  flex: 1;
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
  color: #000;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.icon-svg-section {
  width: 20px;
  height: 20px;
  stroke: currentColor;
  flex-shrink: 0;
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

/* Input with Icon */
.input-with-icon {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding-left: 12px;
  gap: 8px;
}

.input-with-icon .form-input {
  border: none;
  padding-left: 0;
}

.icon-svg-input {
  width: 18px;
  height: 18px;
  stroke: currentColor;
  color: #666;
  flex-shrink: 0;
}

/* Select Wrapper with Dropdown Icon */
.select-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.select-wrapper .form-input {
  padding-right: 40px;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}

.select-icon {
  position: absolute;
  right: 12px;
  width: 18px;
  height: 18px;
  stroke: currentColor;
  color: #666;
  pointer-events: none;
  flex-shrink: 0;
}

/* Buttons */
.resend-btn {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: #4CAF50;
  color: white;
  border: none;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border-radius: 3px;
}

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

/* Helper Text and Errors */
.helper-text {
  display: block;
  color: #666;
  font-size: 12px;
  margin-top: 3px;
}

.error-message {
  color: #e74c3c;
  font-size: 12px;
  margin-top: 4px;
}

.success-message {
  background: #d4edda;
  border: 1px solid #c3e6cb;
  color: #155724;
  padding: 12px;
  border-radius: 4px;
  margin-top: 8px;
  margin-bottom: 18px;
  font-size: 14px;
  font-weight: 500;
}

/* Password Requirements */
.password-requirements {
  margin-top: 10px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #e0e0e0;
}

.requirements-title {
  font-size: 13px;
  font-weight: 600;
  color: #333;
  margin: 0 0 8px 0;
}

.requirements-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.requirements-list li {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #666;
  margin-bottom: 6px;
  transition: color 0.2s;
}

.requirements-list li:last-child {
  margin-bottom: 0;
}

.requirement-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 18px;
  height: 18px;
}

.requirement-met {
  color: #28a745;
}

.requirement-unmet {
  color: #666;
}

.icon-check {
  width: 18px;
  height: 18px;
  stroke: #28a745;
  stroke-width: 2.5;
}

.icon-x {
  width: 18px;
  height: 18px;
  stroke: #999;
  stroke-width: 2;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 12px;
  margin: 30px 0;
  width: 100%;
}

.back-btn {
  flex: 1;
  padding: 12px 20px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #4CAF50;
  background: white;
  border: 2px solid #4CAF50;
  border-radius: 4px;
  transition: all 0.2s ease;
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
  padding: 12px 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.register-btn:hover:not(:disabled) {
  background: #e6763a;
}

.register-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.verify-btn {
  flex: 1;
  background: #ff8c42;
  color: white;
  border: none;
  padding: 12px 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.verify-btn:hover:not(:disabled) {
  background: #e6763a;
}

.verify-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
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
  margin-top: 0;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  flex-shrink: 0;
  padding-top: 2px;
}

.disclaimer-icon .icon-svg {
  width: 20px;
  height: 20px;
  stroke: currentColor;
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

.icon-svg-title {
  width: 24px;
  height: 24px;
  stroke: currentColor;
  flex-shrink: 0;
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

.privacy-list {
  margin: 10px 0;
  padding-left: 30px;
  line-height: 1.8;
  list-style-type: decimal;
  list-style-position: outside;
}

.privacy-list li {
  margin-bottom: 8px;
  color: #374151;
  padding-left: 8px;
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
  font-weight: 600;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.privacy-checkbox-container {
  margin: 20px 0;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 2px solid #e0e0e0;
}

.privacy-checkbox-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  user-select: none;
}

.privacy-checkbox {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: #ff8c42;
  flex-shrink: 0;
}

.privacy-checkbox-text {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  line-height: 1.4;
}

.btn-secondary {
  background: #f8f9fa;
  color: #6c757d;
  padding: 10px 18px;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-secondary:hover:not(:disabled) {
  background: #e9ecef;
  border-color: #adb5bd;
  color: #495057;
}

.btn-secondary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* OTP Modal Styles */
.otp-modal {
  max-width: 500px;
}

.otp-input-group {
  margin: 20px 0;
}

.otp-input {
  width: 100%;
  padding: 15px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 24px;
  text-align: center;
  letter-spacing: 8px;
  font-weight: 600;
  outline: none;
  transition: border-color 0.2s;
}

.otp-input:focus {
  border-color: #ff8c42;
}

.otp-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.otp-actions .btn-primary,
.otp-actions .btn-secondary {
  flex: 1;
}

.resend-section {
  margin-top: 20px;
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.resend-text {
  margin: 0 0 10px 0;
  color: #666;
  font-size: 14px;
}

.btn-resend-otp {
  background: transparent;
  color: #ff8c42;
  border: 1px solid #ff8c42;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-resend-otp:hover:not(:disabled) {
  background: #ff8c42;
  color: white;
}

.btn-resend-otp:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.verified-status {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  background: #d4edda;
  border: 1px solid #c3e6cb;
  border-radius: 4px;
  color: #155724;
  font-weight: 500;
  font-size: 14px;
}

.verified-icon {
  font-size: 18px;
  font-weight: bold;
  color: #28a745;
  display: flex;
  align-items: center;
  justify-content: center;
}

.verified-icon .icon-svg {
  width: 18px;
  height: 18px;
  stroke: currentColor;
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
  
  .form-columns {
    flex-direction: column;
    gap: 20px;
  }
  
  .form-row {
    flex-direction: column;
    gap: 10px;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
/* Success Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.success-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 550px;
    width: 90%;
    overflow: hidden;
    animation: slideUp 0.3s ease;
    position: relative;
    z-index: 10001;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.success-modal-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 35px 30px 25px 30px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.success-icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.success-icon {
    width: 100px;
    height: 100px;
    color: #239640;
    background: rgba(35, 150, 64, 0.1);
    border-radius: 50%;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.2);
}

.success-modal-title {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    color: #239640;
    line-height: 1.3;
}

.success-modal-body {
    padding: 30px 35px;
    text-align: center;
    background: white;
}

.success-message {
    margin: 0;
    font-size: 16px;
    line-height: 1.7;
    color: #555;
    text-align: justify;
}

.success-modal-footer {
    padding: 25px 30px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.success-modal-btn {
    padding: 14px 50px;
    background: linear-gradient(135deg, #239640, #1e7d35);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.success-modal-btn:hover {
    background: linear-gradient(135deg, #1e7d35, #166529);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(35, 150, 64, 0.4);
}

.success-modal-btn:active {
    transform: translateY(0);
}
</style>