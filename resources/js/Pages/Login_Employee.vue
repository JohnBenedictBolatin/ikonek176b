<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3'
  import { useForm } from '@inertiajs/vue3'
  import axios from 'axios'

  const loginUrl = '/login/check'

  const form = useForm({
    phone: '',
    password: '',
  })

  function submit() {
    form.post(loginUrl, {
      onError: () => {
        // errors are available in form.errors
      }
    })
  }

  // Forgot Password Modal State
  const showForgotPasswordModal = ref(false)
  const forgotPasswordStep = ref(1)
  const forgotPasswordPhone = ref('')
  const forgotPasswordOtp = ref('')
  const forgotPasswordNewPassword = ref('')
  const forgotPasswordConfirmPassword = ref('')
  const showNewPassword = ref(false)
  const showConfirmPassword = ref(false)
  const isSendingOtp = ref(false)
  const isVerifyingOtp = ref(false)
  const isResettingPassword = ref(false)
  const forgotPasswordError = ref('')
  const forgotPasswordSuccess = ref('')
  const resendCooldown = ref(0)

  function openForgotPasswordModal() {
    showForgotPasswordModal.value = true
    forgotPasswordStep.value = 1
    forgotPasswordPhone.value = ''
    forgotPasswordOtp.value = ''
    forgotPasswordNewPassword.value = ''
    forgotPasswordConfirmPassword.value = ''
    forgotPasswordError.value = ''
    forgotPasswordSuccess.value = ''
    resendCooldown.value = 0
  }

  function closeForgotPasswordModal() {
    showForgotPasswordModal.value = false
    forgotPasswordStep.value = 1
    forgotPasswordPhone.value = ''
    forgotPasswordOtp.value = ''
    forgotPasswordNewPassword.value = ''
    forgotPasswordConfirmPassword.value = ''
    forgotPasswordError.value = ''
    forgotPasswordSuccess.value = ''
    resendCooldown.value = 0
  }

  function normalizePhone(phone) {
    let normalized = phone.replace(/\D/g, '')
    if (normalized.length === 10 && normalized[0] === '9') {
      normalized = '0' + normalized
    }
    return normalized
  }

  async function sendForgotPasswordOtp() {
    if (!forgotPasswordPhone.value || forgotPasswordPhone.value.trim() === '') {
      forgotPasswordError.value = 'Please enter your mobile number.'
      return
    }

    isSendingOtp.value = true
    forgotPasswordError.value = ''
    forgotPasswordSuccess.value = ''

    try {
      const normalizedPhone = normalizePhone(forgotPasswordPhone.value)
      const response = await axios.post(route('forgot-password.send-otp'), {
        phone_number: normalizedPhone
      })

      if (response.data.success) {
        forgotPasswordSuccess.value = 'OTP sent successfully! Please check your phone.'
        forgotPasswordStep.value = 2
        resendCooldown.value = 300
        const timer = setInterval(() => {
          resendCooldown.value--
          if (resendCooldown.value <= 0) {
            clearInterval(timer)
          }
        }, 1000)
      } else {
        forgotPasswordError.value = response.data.message || 'Failed to send OTP. Please try again.'
      }
    } catch (error) {
      forgotPasswordError.value = error.response?.data?.message || 'Failed to send OTP. Please try again.'
    } finally {
      isSendingOtp.value = false
    }
  }

  async function verifyForgotPasswordOtp() {
    if (!forgotPasswordOtp.value || forgotPasswordOtp.value.length !== 6) {
      forgotPasswordError.value = 'Please enter a 6-digit OTP code.'
      return
    }

    isVerifyingOtp.value = true
    forgotPasswordError.value = ''
    forgotPasswordSuccess.value = ''

    try {
      const normalizedPhone = normalizePhone(forgotPasswordPhone.value)
      const response = await axios.post(route('forgot-password.verify-otp'), {
        phone_number: normalizedPhone,
        otp_code: forgotPasswordOtp.value
      })

      if (response.data.success) {
        forgotPasswordSuccess.value = 'OTP verified successfully! You can now set a new password.'
        forgotPasswordStep.value = 3
      } else {
        forgotPasswordError.value = response.data.message || 'Invalid OTP code. Please try again.'
        forgotPasswordOtp.value = ''
      }
    } catch (error) {
      forgotPasswordError.value = error.response?.data?.message || 'Invalid OTP code. Please try again.'
      forgotPasswordOtp.value = ''
    } finally {
      isVerifyingOtp.value = false
    }
  }

  async function resetForgotPassword() {
    if (!forgotPasswordNewPassword.value || forgotPasswordNewPassword.value.trim() === '') {
      forgotPasswordError.value = 'Please enter a new password.'
      return
    }

    if (forgotPasswordNewPassword.value.length < 6) {
      forgotPasswordError.value = 'Password must be at least 6 characters long.'
      return
    }

    if (forgotPasswordNewPassword.value !== forgotPasswordConfirmPassword.value) {
      forgotPasswordError.value = 'Passwords do not match.'
      return
    }

    isResettingPassword.value = true
    forgotPasswordError.value = ''
    forgotPasswordSuccess.value = ''

    try {
      const normalizedPhone = normalizePhone(forgotPasswordPhone.value)
      const response = await axios.post(route('forgot-password.reset'), {
        phone_number: normalizedPhone,
        password: forgotPasswordNewPassword.value,
        password_confirmation: forgotPasswordConfirmPassword.value
      })

      if (response.data.success) {
        forgotPasswordSuccess.value = 'Password reset successfully! You can now login with your new password.'
        setTimeout(() => {
          closeForgotPasswordModal()
        }, 2000)
      } else {
        forgotPasswordError.value = response.data.message || 'Failed to reset password. Please try again.'
      }
    } catch (error) {
      forgotPasswordError.value = error.response?.data?.message || 'Failed to reset password. Please try again.'
    } finally {
      isResettingPassword.value = false
    }
  }

  async function resendForgotPasswordOtp() {
    if (resendCooldown.value > 0 || isSendingOtp.value) {
      return
    }
    await sendForgotPasswordOtp()
  }

  function handleOtpInput(event) {
    let value = event.target.value.replace(/\D/g, '')
    if (value.length > 6) {
      value = value.substring(0, 6)
    }
    forgotPasswordOtp.value = value
    if (forgotPasswordError.value) {
      forgotPasswordError.value = ''
    }
  }

  function handlePhoneInput(event) {
    let value = event.target.value.replace(/\D/g, '')
    if (value.length > 11) {
      value = value.substring(0, 11)
    }
    forgotPasswordPhone.value = value
  }

  // Format cooldown time as minutes and seconds
  function formatCooldownTime(seconds) {
    if (seconds <= 0) return ''
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    if (mins > 0) {
      return `${mins}m ${secs}s`
    }
    return `${secs}s`
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
        <div class="form-group">
          <label class="form-label">Contact Number</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
              </svg>
            </span>
            <input 
              type="tel" 
              name="phone" 
              v-model="form.phone" 
              placeholder="+639"
              class="form-input"
            />
          </div>
          <div v-if="form.errors.phone" class="error-message">
            {{ form.errors.phone }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
            </span>
            <input 
              type="password" 
              name="password" 
              v-model="form.password"
              class="form-input"
            />
          </div>
          <div v-if="form.errors.password" class="error-message">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Forgot Password Link -->
        <div class="forgot-link-container">
          <button type="button" @click="openForgotPasswordModal" class="forgot-link">
            Forgot Password?
          </button>
        </div>

        <Link :href="route('announcement_employee')">
        <button type="submit" class="login-btn" :disabled="form.processing">
          LOGIN
        </button>
        </Link>

        <div class="register-buttons">
          <Link :href="route('registration_resident')" class="register-btn">
            REGISTER AS RESIDENT
          </Link>
          <Link :href="route('registration_employee')" class="register-btn">
            REGISTER AS OFFICIAL
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

    <!-- Forgot Password Modal -->
    <div v-if="showForgotPasswordModal" class="modal-backdrop" @click.self="closeForgotPasswordModal">
      <div class="forgot-password-modal" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">Forgot Password</h2>
          <button type="button" @click="closeForgotPasswordModal" class="modal-close">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="modal-content">
          <!-- Step 1: Enter Phone Number -->
          <div v-if="forgotPasswordStep === 1" class="modal-step">
            <p class="step-description">
              Enter your mobile number to receive a verification code.
            </p>
            
            <div class="form-group">
              <label class="form-label">Mobile Number</label>
              <div class="input-wrapper" :class="{ 'input-error': forgotPasswordError && !forgotPasswordSuccess }">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                  </svg>
                </span>
                <input 
                  type="tel" 
                  v-model="forgotPasswordPhone" 
                  @input="handlePhoneInput"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  placeholder="Please enter your contact number"
                  class="form-input"
                  maxlength="11"
                  required
                />
              </div>
            </div>

            <div v-if="forgotPasswordError && !forgotPasswordSuccess" class="error-message">
              {{ forgotPasswordError }}
            </div>
            <div v-if="forgotPasswordSuccess" class="success-message">
              {{ forgotPasswordSuccess }}
            </div>

            <button 
              type="button" 
              @click="sendForgotPasswordOtp" 
              class="modal-btn-primary"
              :disabled="isSendingOtp || !forgotPasswordPhone"
            >
              {{ isSendingOtp ? 'SENDING...' : 'SEND OTP' }}
            </button>
          </div>

          <!-- Step 2: Verify OTP -->
          <div v-if="forgotPasswordStep === 2" class="modal-step">
            <p class="step-description">
              We've sent a 6-digit OTP code to <strong>+63{{ forgotPasswordPhone.replace(/\D/g, '').substring(1) }}</strong>. 
              Please enter it below.
            </p>
            
            <div class="form-group">
              <label class="form-label">OTP Code</label>
              <div class="input-wrapper" :class="{ 'input-error': forgotPasswordError && !forgotPasswordSuccess }">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>
                </span>
                <input 
                  type="text" 
                  v-model="forgotPasswordOtp" 
                  @input="handleOtpInput"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  placeholder="Enter 6-digit OTP"
                  class="form-input"
                  maxlength="6"
                  required
                />
              </div>
            </div>

            <div v-if="forgotPasswordError && !forgotPasswordSuccess" class="error-message">
              {{ forgotPasswordError }}
            </div>
            <div v-if="forgotPasswordSuccess" class="success-message">
              {{ forgotPasswordSuccess }}
            </div>

            <div class="modal-actions">
              <button 
                type="button" 
                @click="forgotPasswordStep = 1" 
                class="modal-btn-secondary"
                :disabled="isVerifyingOtp"
              >
                BACK
              </button>
              <button 
                type="button" 
                @click="verifyForgotPasswordOtp" 
                class="modal-btn-primary"
                :disabled="isVerifyingOtp || forgotPasswordOtp.length !== 6"
              >
                {{ isVerifyingOtp ? 'VERIFYING...' : 'VERIFY OTP' }}
              </button>
            </div>

            <div class="resend-otp-container">
              <button 
                type="button" 
                @click="resendForgotPasswordOtp" 
                class="resend-otp-btn"
                :disabled="isSendingOtp || resendCooldown > 0"
              >
                {{ resendCooldown > 0 ? `RESEND IN ${formatCooldownTime(resendCooldown)}` : (isSendingOtp ? 'SENDING...' : 'RESEND OTP') }}
              </button>
            </div>
          </div>

          <!-- Step 3: Set New Password -->
          <div v-if="forgotPasswordStep === 3" class="modal-step">
            <p class="step-description">
              Please enter your new password below.
            </p>
            
            <div class="form-group">
              <label class="form-label">New Password</label>
              <div class="input-wrapper" :class="{ 'input-error': forgotPasswordError && !forgotPasswordSuccess }">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>
                </span>
                <input 
                  :type="showNewPassword ? 'text' : 'password'" 
                  v-model="forgotPasswordNewPassword" 
                  placeholder="Enter new password"
                  class="form-input"
                  required
                />
                <button 
                  type="button" 
                  @click="showNewPassword = !showNewPassword"
                  class="password-toggle"
                >
                  <svg v-if="!showNewPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <div class="input-wrapper" :class="{ 'input-error': forgotPasswordError && !forgotPasswordSuccess }">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>
                </span>
                <input 
                  :type="showConfirmPassword ? 'text' : 'password'" 
                  v-model="forgotPasswordConfirmPassword" 
                  placeholder="Confirm new password"
                  class="form-input"
                  required
                />
                <button 
                  type="button" 
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="password-toggle"
                >
                  <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>
                </button>
              </div>
            </div>

            <div v-if="forgotPasswordError && !forgotPasswordSuccess" class="error-message">
              {{ forgotPasswordError }}
            </div>
            <div v-if="forgotPasswordSuccess" class="success-message">
              {{ forgotPasswordSuccess }}
            </div>

            <div class="modal-actions">
              <button 
                type="button" 
                @click="forgotPasswordStep = 2" 
                class="modal-btn-secondary"
                :disabled="isResettingPassword"
              >
                BACK
              </button>
              <button 
                type="button" 
                @click="resetForgotPassword" 
                class="modal-btn-primary"
                :disabled="isResettingPassword || !forgotPasswordNewPassword || !forgotPasswordConfirmPassword"
              >
                {{ isResettingPassword ? 'RESETTING...' : 'RESET PASSWORD' }}
              </button>
            </div>
          </div>
        </div>
      </div>
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
  background-image: url('assets/BACKGROUNDIMAGE.png');
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
  font-size: 17px;
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
  width: 20px;
  height: 20px;
  stroke: currentColor;
}

.form-input {
  flex: 1;
  border: none;
  padding: 12px 14px;
  font-size: 14px;
  color: #333;
  outline: none;
  background: white;
}

.form-input::placeholder {
  color: #999;
}

.input-wrapper:focus-within {
  border-color: #ff8c42;
}

.error-message {
  color: #e74c3c;
  font-size: 12px;
  margin-top: 4px;
}

/* Forgot Link */
.forgot-link-container {
  text-align: right;
  margin-bottom: 20px;
  font-size: 20px;
}

.forgot-link {
  color: #9c9c9c;
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
  font-size: 16px;
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
  padding: 12px 8px;
  text-decoration: none;
  font-size: 15px;
  font-weight: 700;
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
  font-size: 20px;
}

.continue-link {
  color: #666;
  text-decoration: underline;
  font-size: 15px;
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
  font-size: 15px;
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
  width: 20px;
  height: 20px;
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

/* Forgot Password Modal */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.forgot-password-modal {
  background: white;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-top: 8px solid #ff8c42;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 24px 16px;
  border-bottom: 1px solid #e6e6e6;
}

.modal-title {
  font-size: 24px;
  font-weight: 700;
  color: #333;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  transition: color 0.2s;
}

.modal-close:hover {
  color: #333;
}

.modal-close .icon-svg {
  width: 24px;
  height: 24px;
}

.modal-content {
  padding: 24px;
}

.modal-step {
  width: 100%;
}

.step-description {
  font-size: 14px;
  color: #666;
  line-height: 1.5;
  margin-bottom: 20px;
}

.step-description strong {
  color: #333;
  font-weight: 600;
}

.success-message {
  background: #d4edda;
  border: 1px solid #c3e6cb;
  color: #155724;
  padding: 12px;
  border-radius: 4px;
  margin-bottom: 18px;
  font-size: 14px;
}

.modal-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.modal-btn-primary {
  flex: 1;
  background: #ff8c42;
  color: white;
  border: none;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-radius: 4px;
  transition: background 0.3s;
}

.modal-btn-primary:hover:not(:disabled) {
  background: #e6763a;
}

.modal-btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.modal-btn-secondary {
  flex: 1;
  background: #f5f5f5;
  color: #333;
  border: 1px solid #ddd;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-radius: 4px;
  transition: background 0.3s;
}

.modal-btn-secondary:hover:not(:disabled) {
  background: #e8e8e8;
}

.modal-btn-secondary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.resend-otp-container {
  text-align: center;
  margin-top: 16px;
}

.resend-otp-btn {
  background: none;
  border: none;
  color: #ff8c42;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
  padding: 8px;
  transition: color 0.2s;
}

.resend-otp-btn:hover:not(:disabled) {
  color: #e6763a;
}

.resend-otp-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  text-decoration: none;
}

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
  color: #ff8c42;
}

.password-toggle .icon-svg {
  width: 20px;
  height: 20px;
}

@media (max-width: 768px) {
  .forgot-password-modal {
    max-width: 100%;
    margin: 10px;
  }
  
  .modal-header {
    padding: 20px 20px 12px;
  }
  
  .modal-content {
    padding: 20px;
  }
  
  .modal-actions {
    flex-direction: column;
  }
  
  .modal-btn-primary,
  .modal-btn-secondary {
    width: 100%;
  }
}
</style>