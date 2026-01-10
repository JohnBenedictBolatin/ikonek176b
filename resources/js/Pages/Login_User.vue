<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref, reactive } from 'vue';
  import { router } from '@inertiajs/vue3'
  import { useForm } from '@inertiajs/vue3'

  const loginUrl = route ? route('login.check') : '/login/check'

  const form = useForm({
    phone: '',
    password: '',
    // important: identify this form as a user login so backend only checks user_credentials
    login_for: 'user',
  })

  // Add state for password visibility
  const showPassword = ref(false)
  
  // Local validation errors
  const localErrors = reactive({
    phone: null,
    password: null,
    general: null
  })

  // Clear error when user starts typing
  function clearError(field) {
    if (field === 'phone') {
      localErrors.phone = null
      if (form.clearErrors) form.clearErrors('phone')
    } else if (field === 'password') {
      localErrors.password = null
      if (form.clearErrors) form.clearErrors('password')
    }
    localErrors.general = null
  }

  // Validate phone number format
  function validatePhone(phone) {
    if (!phone || phone.trim() === '') {
      return 'Contact number is required.'
    }
    
    // Remove all non-digit characters for validation
    const phoneDigits = phone.replace(/\D/g, '')
    
    if (phoneDigits.length === 0) {
      return 'Contact number must contain at least one digit.'
    }
    
    // Phone should be 10 or 11 digits (Philippine format: 9xxxxxxxxx or 09xxxxxxxxx)
    if (phoneDigits.length < 10 || phoneDigits.length > 11) {
      return 'Contact number must be 10 or 11 digits (e.g., 9123456789 or 09123456789).'
    }
    
    // First digit should be 0 or 9
    if (phoneDigits.length === 11 && phoneDigits[0] !== '0') {
      return 'Contact number starting with 0 must be 11 digits (e.g., 09123456789).'
    }
    
    if (phoneDigits.length === 10 && phoneDigits[0] !== '9') {
      return 'Contact number without 0 must start with 9 (e.g., 9123456789).'
    }
    
    return null
  }

  // Validate password
  function validatePassword(password) {
    if (!password || password.trim() === '') {
      return 'Password is required.'
    }
    
    if (password.length < 6) {
      return 'Password must be at least 6 characters long.'
    }
    
    return null
  }

  // Client-side validation before submission
  function validateForm() {
    localErrors.phone = null
    localErrors.password = null
    localErrors.general = null
    
    const phoneError = validatePhone(form.phone)
    const passwordError = validatePassword(form.password)
    
    if (phoneError) {
      localErrors.phone = phoneError
    }
    
    if (passwordError) {
      localErrors.password = passwordError
    }
    
    return !phoneError && !passwordError
  }

  function submit() {
    // Clear previous errors
    localErrors.phone = null
    localErrors.password = null
    localErrors.general = null
    
    // Client-side validation
    if (!validateForm()) {
      return
    }
    
    form.post(loginUrl, {
      onStart: () => {
        // Clear general error when starting new request
        localErrors.general = null
      },
      onError: (errors) => {
        // Handle server-side validation errors
        if (errors.phone) {
          const phoneError = Array.isArray(errors.phone) ? errors.phone[0] : errors.phone
          localErrors.phone = phoneError
        }
        
        if (errors.password) {
          const passwordError = Array.isArray(errors.password) ? errors.password[0] : errors.password
          localErrors.password = passwordError
        }
        
        // Handle general errors (network, server errors, etc.)
        if (errors.message) {
          localErrors.general = Array.isArray(errors.message) ? errors.message[0] : errors.message
        }
        
        // If no specific field errors but form has errors, show general error
        if (!errors.phone && !errors.password && !errors.message && Object.keys(errors).length > 0) {
          const firstError = Object.values(errors)[0]
          localErrors.general = Array.isArray(firstError) ? firstError[0] : firstError
        }
      },
      onSuccess: () => {
        // Clear all errors on success
        localErrors.phone = null
        localErrors.password = null
        localErrors.general = null
      },
      onFinish: () => {
        // Clear password field for security after attempt (only if login failed)
        if (form.hasErrors || localErrors.phone || localErrors.password || localErrors.general) {
          form.password = ''
        }
      }
    })
  }

  // Toggle password visibility
  function togglePasswordVisibility() {
    showPassword.value = !showPassword.value
  }

  // Restrict phone number input to numbers only
  function handlePhoneInput(event) {
    // Remove all non-numeric characters
    let value = event.target.value.replace(/\D/g, '')
    
    // Limit to 11 characters maximum
    if (value.length > 11) {
      value = value.substring(0, 11)
    }
    
    // Update the form value
    form.phone = value
    // Clear error when user types
    clearError('phone')
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
    form.phone = value
    // Clear error when user pastes
    clearError('phone')
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
</script>

<template>
  <Head>
    <title>Login</title>
  </Head>

  <div class="page-container">
    <!-- Left side form -->
    <div class="left-side">
      <!-- Logo -->
      <div class="logo-section">
          <img src="assets/LOGO.png" alt="Logo" class="logo-icon" />
      </div>

      <h2 class="greeting">
        Magandang araw, <span class="highlight">Ka-Barangay!</span>
      </h2>

      <form @submit.prevent="submit" class="login-form">
        <!-- hidden field to tell server this is a user login -->
        <input type="hidden" name="login_for" v-model="form.login_for" />

        <div class="form-group">
          <label class="form-label">Contact Number</label>
          <div class="input-wrapper" :class="{ 'input-error': localErrors.phone || form.errors.phone }">
            <span class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
              </svg>
            </span>
            <input 
              type="tel" 
              name="phone" 
              v-model="form.phone" 
              @input="handlePhoneInput"
              @paste.prevent="handlePhonePaste"
              @keydown="handlePhoneKeydown"
              inputmode="numeric"
              pattern="[0-9]*"
              placeholder="Please enter your contact number (E.g., 9xxxxxxxxx)"
              class="form-input"
              maxlength="11"
              required
            />
          </div>
          <div v-if="localErrors.phone" class="error-message">
            {{ localErrors.phone }}
          </div>
          <div v-else-if="form.errors.phone" class="error-message">
            {{ Array.isArray(form.errors.phone) ? form.errors.phone[0] : form.errors.phone }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper" :class="{ 'input-error': localErrors.password || form.errors.password }">
            <span class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
            </span>
            <input 
              :type="showPassword ? 'text' : 'password'" 
              name="password" 
              v-model="form.password"
              @input="clearError('password')"
              placeholder="Please enter your password"
              class="form-input"
              required
            />
            <button 
              type="button" 
              @click="togglePasswordVisibility"
              class="password-toggle"
              :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
              <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            </button>
          </div>
          <div v-if="localErrors.password" class="error-message">
            {{ localErrors.password }}
          </div>
          <div v-else-if="form.errors.password" class="error-message">
            {{ Array.isArray(form.errors.password) ? form.errors.password[0] : form.errors.password }}
          </div>
        </div>

        <!-- General Error Message -->
        <div v-if="localErrors.general" class="general-error-message">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="error-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
          <span>{{ localErrors.general }}</span>
        </div>

        <button type="submit" class="login-btn" :disabled="form.processing">
          <span v-if="form.processing">Logging in...</span>
          <span v-else>LOGIN</span>
        </button>

        <!-- <Link :href="route('announcement_resident')">
        <button type="submit" class="login-btn" :disabled="form.processing">
          LOGIN
        </button>
        </Link> -->

        <div class="register-buttons">
          <Link :href="route('registration_resident')" class="register-btn">
            REGISTER
          </Link>
        </div>

        <div class="continue-link-container">
          <Link :href="route('guest_announcement')" class="continue-link">
          </Link>
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
            This website is exclusive for Barangay 176B residents only.
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
  position: fixed;
  top: 0;
  left: 0;
  background-image: url('assets/BACKGROUND IMAGE1.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
}

/* LEFT SIDE - Login Form */
.left-side {
  width: 600px;
  height: 300vh;
  background: white;
  padding: 50px;
  overflow-y: auto;
  flex-shrink: 0;
}

/* RIGHT SIDE - Background/Illustration Space */
.right-side {
  flex: 1;
  height: 100vh;
  /* This space is for your background elements */
}

/* Logo Section */
.logo-section {
  display: flex;
  align-items: center;
  margin-bottom: 25px;
}

.logo-icon {
  width: 250px;
  height: 100x;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 100px;
  margin-left: 115px; /* pushes logo itself to the right */
  font-size: 20px;
}

/* Greeting */
.greeting {
  font-size: 25px;
  font-weight: 400;
  color: #333;
  margin-bottom: 35px;
  margin-left: 80Px; /* pushes logo itself to the right */
  line-height: 1.2;
}

.highlight {
  color: #ff8c42;
  font-weight: 600;
}

/* Form Styles */
.login-form {
  width: 100%;
}

.form-group {
  margin-bottom: 18px;
}

.form-label {
  display: block;
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin-bottom: 6px;
}

/* Input wrapper with icons */
.input-wrapper {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  background: white;
  position: relative;
}

.input-icon {
  padding: 12px;
  font-size: 16px;
  color: #000;
  border-right: 1px solid #ddd;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-svg {
  width: 22px;
  height: 22px;
  stroke: currentColor;
}

.form-input {
  flex: 1;
  border: none;
  padding: 12px 14px;
  font-size: 16px;
  color: #333;
  outline: none;
  background: white;
}

.form-input::placeholder {
  color: #999;
  font-size: 16px;
}

.input-wrapper:focus-within {
  border-color: #ff8c42;
}

.input-wrapper.input-error {
  border-color: #e74c3c;
}

.input-wrapper.input-error:focus-within {
  border-color: #e74c3c;
  box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.1);
}

/* Password Toggle Button */
.password-toggle {
  background: none;
  border: none;
  padding: 12px;
  cursor: pointer;
  font-size: 18px;
  color: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.password-toggle:hover {
  color: #333;
}

.password-toggle .icon-svg {
  width: 22px;
  height: 22px;
}

.password-toggle:hover {
  color: #ff8c42;
}


.error-message {
  color: #e74c3c;
  font-size: 14px;
  margin-top: 4px;
}

.general-error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 12px;
  border-radius: 4px;
  margin-bottom: 18px;
  font-size: 16px;
}

.general-error-message .error-icon {
  width: 22px;
  height: 22px;
  flex-shrink: 0;
  stroke: currentColor;
}

/* Forgot Link */
.forgot-link-container {
  text-align: right;
  margin-bottom: 20px;
  font-size: 20px;
}

.forgot-link {
  color: #8c8c8c;
  text-decoration: underline;
  font-size: 15px;
}

.forgot-link:hover {
  color: #e6763a;
}

/* Login Button */
.login-btn {
  width: 100%;
  background: #ff8c42;
  color: white;
  border: none;
  padding: 14px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 18px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.login-btn:hover:not(:disabled) {
  background: #e6763a;
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Register Buttons */
.register-buttons {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.register-btn {
  flex: 1;
  padding: 15px 50px;
  text-decoration: none;
  font-size: 18px;
  font-weight: bold;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  line-height: 1.2;
  color: white;
  background: #4CAF50;
}

.register-btn:hover {
  background: #45a049;
}

/* Continue Link */
.continue-link-container {
  text-align: center;
  margin-bottom: 25px;
  font-size: 22px;
}

.continue-link {
  color: #666;
  text-decoration: underline;
  font-size: 17px;
}

.continue-link:hover {
  color: #333;
}

/* Disclaimer */
.disclaimer {
  background: #fff3cd;
  border: 1px solid #ffeaa7;
  padding: 12px 12px 12px 20px;
  margin-top: 170px;
  font-size: 17px;
  color: #856404;
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.disclaimer-icon {
  font-size: 20px;
  margin-top: 0;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  color: #856404;
  flex-shrink: 0;
  padding-top: 2px;
}

.disclaimer-icon .icon-svg {
  width: 22px;
  height: 22px;
}

.disclaimer-content {
  flex: 1;
  line-height: 1.4;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .left-side {
    width: 100vw;
    padding: 30px 25px;
  }
  
  .right-side {
    display: none;
  }
  
  .register-buttons {
    flex-direction: column;
  }
}
</style>