<template>
  <Head>
    <title>Barangay Official Registration Form</title>
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
       This registration form is used by the System Administrator to register Barangay Officials into the iKonek176B system. The personal information collected (name, contact details, address, and other relevant data) will be used to create a user account with an official title/role designation. Officials are registered as residents with their respective titles and will have access to the same community features as other residents.
      </p>
      <p class="justified-text">
All information collected is used to create and manage user accounts, verify identity, enable communication within the community, and process barangay-related requests and services. We ensure that all data is stored securely and protected against unauthorized access. While we take appropriate security measures, please note that no system can guarantee absolute security.
We do not sell or share personal data with third parties, except with authorized barangay personnel or government agencies when required by law. As a registered user, you have the right to access, correct, or update your personal information at any time through the system or by contacting the System Administrator.
      </p>
      <p class="justified-text">
        By registering this official, you acknowledge that the information provided will be used to create a user account with an official title designation in the iKonek176B system.
For any concerns or questions regarding data privacy, contact us at ikonek176b@dev.ph, +639193076338, or visit the Barangay 176B office.
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
        <button class="btn-primary" @click="closePrivacy" :disabled="!privacyAcknowledged">I Understand</button>
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
        <h1>Registration Form - Barangay Official</h1>
      </div>

      <form @submit.prevent="submit" class="registration-form">
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
                <input v-model="form.last_name" @blur="form.last_name = toTitleCase(form.last_name)" placeholder="Last Name" class="form-input" required />
              </div>

              <div class="form-row">
                <div class="form-group half">
                  <input v-model="form.first_name" @blur="form.first_name = toTitleCase(form.first_name)" placeholder="First Name" class="form-input" required />
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
                      <option value="Jr">Jr.</option>
                      <option value="Sr">Sr.</option>
                      <option value="III">III</option>
                      <option value="IV">IV</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </div>
                </div>

                <div class="form-group half">
                  <div class="select-wrapper">
                    <select v-model="form.sex" class="form-input" required>
                      <option value="">Sex</option>
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
                      <option value="">Civil Status</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Separated">Separated</option>
                      <option value="Divorced">Divorced</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="select-wrapper">
                  <select v-model="form.fk_role_id" class="form-input" required>
                    <option value="">Position/Role</option>
                    <option value="2">Barangay Captain</option>
                    <option value="3">Barangay Secretary</option>
                    <option value="4">Barangay Treasurer</option>
                    <option value="5">Barangay Kagawad</option>
                    <option value="6">SK Chairman</option>
                    <option value="7">SK Kagawad</option>
                  </select>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="select-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </div>

              <!-- END NEW fields -->
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
                  <input v-model="form.contact_number" placeholder="Primary Number" maxlength="10" class="form-input" required />
                </div>
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
                <div class="password-input-wrapper">
                  <input :type="showPassword ? 'text' : 'password'" v-model="form.password" @input="clearError('password'); validatePasswordRequirements(); validateMatch()" placeholder="Enter password" class="form-input" required style="padding-right: 45px;" />
                  <button type="button" @click="showPassword = !showPassword" class="password-toggle-btn" style="z-index: 10;">
                    <template v-if="showPassword">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                      </svg>
                    </template>
                    <template v-else>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      </svg>
                    </template>
                  </button>
                </div>
              </div>

              <div class="form-group">
                <div class="password-input-wrapper">
                  <input :type="showConfirmPassword ? 'text' : 'password'" v-model="confirmPassword" @input="clearError('confirmPassword'); validateMatch()" placeholder="Confirm password" class="form-input" required style="padding-right: 45px;" />
                  <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="password-toggle-btn" style="z-index: 10;">
                    <template v-if="showConfirmPassword">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                      </svg>
                    </template>
                    <template v-else>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      </svg>
                    </template>
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
                <div class="error-message" v-if="localErrors.password">{{ localErrors.password }}</div>
                <div class="error-message" v-else-if="form.errors.password">{{ form.errors.password }}</div>
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
          
          <!-- house & street -->
          <div class="form-row">
            <div class="form-group half">
              <input v-model="form.house" @blur="form.house = toTitleCase(form.house)" placeholder="House / Unit / Building No." class="form-input" />
            </div>

            <div class="form-group half">
              <input v-model="form.street" @blur="form.street = toTitleCase(form.street)" placeholder="Street" class="form-input" />
            </div>
          </div>

          <!-- city/municipality & province -->
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

        <!-- General Error Display -->
        <div v-if="form.errors.error || Object.keys(form.errors).length > 0" class="general-error" style="margin: 20px 0; padding: 15px; background: #fee; border: 1px solid #fcc; border-radius: 4px;">
          <div v-if="form.errors.error" class="error-message" style="font-weight: 600; margin-bottom: 10px;">
            {{ form.errors.error }}
          </div>
          <div v-for="(error, field) in form.errors" :key="field" v-if="field !== 'error'">
            <strong>{{ field }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
          <Link :href="route('dashboard_admin')" class="back-btn">
            BACK TO DASHBOARD
          </Link>
          <button class="register-btn" :disabled="form.processing || !canSubmit" type="submit">
            REGISTER
          </button>
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
            Upon clicking "REGISTER", the user will be automatically registerd to the system as a Barangay Official.
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
onBeforeUnmount(() => { document.body.style.overflow = '' })

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
  // remove legacy single 'address' reliance: backend expects house_number & street etc.
  address: '',
  // NEW split address fields (UI fields)
  house: '',
  street: '',
  // fields matching DB columns (will be filled before submit)
  house_number: '',
  // keep separate user-editable barangay part
  barangay_input: 'Barangay 176B',
  barangay: 'Barangay 176B',
  city: 'Caloocan City',
  province: 'Metro Manila',           // default to NCR as requested
  fk_role_id: '',
  phase: '',
  package: '',

  // other personal fields that map to users table
  nationality: 'Filipino',
})

const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const localErrors = reactive({ password: null, confirmPassword: null })

function normalizeCasing() {
  form.first_name = toTitleCase(form.first_name)
  form.middle_name = toTitleCase(form.middle_name)
  form.last_name = toTitleCase(form.last_name)
  form.house = toTitleCase(form.house)
  form.street = toTitleCase(form.street)
}

const passwordsMatch = computed(() => form.password === confirmPassword.value)

// Password requirement validators
const hasMinLength = computed(() => form.password && form.password.length >= 8)
const hasUppercase = computed(() => form.password && /[A-Z]/.test(form.password))
const hasLowercase = computed(() => form.password && /[a-z]/.test(form.password))
const hasNumber = computed(() => form.password && /[0-9]/.test(form.password))
const hasSpecialChar = computed(() => form.password && /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(form.password))

const canSubmit = computed(() => {
  return form.password && 
         hasMinLength.value &&
         hasUppercase.value &&
         hasLowercase.value &&
         hasNumber.value &&
         hasSpecialChar.value &&
         confirmPassword.value && 
         passwordsMatch.value
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


// merge the split address fields into individual DB-mapped fields BEFORE posting
function mergeAddressFields() {
  // Use barangay_input if provided, otherwise fallback to the static barangay value
  const barangayToUse = (form.barangay_input && form.barangay_input.trim()) ? form.barangay_input.trim() : (form.barangay || '')

  // assign DB column names expected by User model
  form.house_number = form.house && form.house.toString().trim() ? form.house.toString().trim() : ''
  form.street = form.street && form.street.toString().trim() ? form.street.toString().trim() : ''
  form.barangay = barangayToUse
  form.city = form.city && form.city.toString().trim() ? form.city.toString().trim() : 'Caloocan City'
  form.province = form.province && form.province.toString().trim() ? form.province.toString().trim() : 'Metro Manila'

  // Optionally set a legacy address string if your backend still expects it (not required):
  const parts = [
    form.house_number,
    form.street,
    form.barangay,
    form.city,
    form.province,
  ].filter(Boolean)
  form.address = parts.join(', ')
}

function submit() {
  // Check if privacy notice has been acknowledged
  if (showPrivacy.value || !privacyAcknowledged.value) {
    alert('Please read and acknowledge the Privacy Notice before submitting the registration form.')
    showPrivacy.value = true
    return
  }

  normalizeCasing()
  
  localErrors.password = null
  localErrors.confirmPassword = null

  // Validate password requirements
  validatePasswordRequirements()
  
  if (!confirmPassword.value) {
    localErrors.confirmPassword = 'Please confirm your password.'
  } else if (!passwordsMatch.value) {
    localErrors.confirmPassword = 'Passwords do not match.'
  }

  if (localErrors.password || localErrors.confirmPassword) return

  // MERGE birthdate fields into form.birthdate BEFORE posting
  mergeBirthdate()

  // MERGE address fields here (IMPORTANT: done immediately before posting)
  mergeAddressFields()

  // Ensure fk_role_id is an integer
  if (form.fk_role_id) {
    form.fk_role_id = parseInt(form.fk_role_id, 10)
  }

  // Log the payload before sending (excluding password for security)
  const payloadToLog = { ...form.data() };
  delete payloadToLog.password;
  console.log('posting payload', payloadToLog);
  
  form.post(route('register_employee.store'), {
    onStart: () => {
      console.log('starting request');
    },
    onError: (errors) => {
      // Only reset password if there's an actual error, and preserve it in form for retry
      console.log('server validation errors', errors);
      // Don't reset password - keep it so user can see what went wrong and retry
    },
    onSuccess: (page) => {
      // Clear password fields only on success
      form.reset('password')
      confirmPassword.value = ''
      console.log('server success response', page);
    },
    onFinish: () => {
      console.log('request finished');
    }
  })
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

.password-input-wrapper {
  position: relative;
}

.password-input-wrapper input[type="password"]::-ms-reveal,
.password-input-wrapper input[type="password"]::-ms-clear {
  display: none;
}

.password-input-wrapper input[type="password"]::-webkit-credentials-auto-fill-button {
  display: none;
}

.password-toggle-btn {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  padding: 0;
  margin: 0;
  width: 30px;
  height: 30px;
}

.password-toggle-btn svg {
  width: 20px;
  height: 20px;
  display: block;
}

.icon-svg {
  width: 20px;
  height: 20px;
  pointer-events: none;
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
</style>