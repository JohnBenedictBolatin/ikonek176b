<template>
  <Head>
    <title>Admin Login - iKONEK 176B</title>
  </Head>

  <div class="page-container">
    <div class="login-container">
      <!-- Logo Section -->
      <div class="logo-section">
        <img src="/assets/LOGO.png" alt="iKONEK 176B Logo" class="logo-icon" />
      </div>

      <!-- Login Header -->
      <div class="login-header">
        <h1 class="login-title">Welcome!</h1>
        <p class="login-subtitle">
          Thank you for keeping the platform safe and helpful.
        </p>
      </div>

      <!-- Login Form (kept same design) -->
      <form @submit.prevent="submit" class="login-form">
        <div class="form-section">
          <h3 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            System Admin Access
          </h3>

          <div class="form-group">
            <div class="input-wrapper">
              <span class="input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
              </span>
              <input
                v-model="form.phone"
                type="text"
                placeholder="Username"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-group">
            <div class="password-wrapper">
              <div class="input-wrapper">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                  </svg>
                </span>
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Password"
                  class="form-input"
                />
                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="password-toggle"
                  :aria-label="showPassword ? 'Hide password' : 'Show password'"
                >
                  <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 01-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Button goes directly to dashboard -->
        <button type="submit" class="login-btn" :disabled="form.processing">
          <span v-if="form.processing">Logging in...</span>
          <span v-else>LOGIN</span>
        </button>
      </form>

      <!-- Disclaimer -->
      <div class="disclaimer">
        <svg class="disclaimer-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div class="disclaimer-content">
          <strong>Disclaimer:</strong> This portal is exclusive for
          iKonek-176B System Admin only.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import { router, Head } from '@inertiajs/vue3'
  import { useForm } from '@inertiajs/vue3'

const showPassword = ref(false)
// const phone = ref('')
// const password = ref('')

// const goToDashboard = () => {
//   // 🚀 Just redirect to dashboard, ignoring inputs
//   router.get(route('dashboard_admin'))
// }

  const loginUrl = route ? route('login.check') : '/login/check'

  const form = useForm({
    phone: '',
    password: '',
  })

function submit() {
  form.post(loginUrl, {
    onSuccess: (page) => {
      // server usually handles redirects; optionally handle client-side here
    },
    onError: (errors) => {
      // errors are already placed into form.errors by useForm
      // you can log or show extra UI here
      console.log('login errors', errors)
    },
    onFinish: () => {
      // clear password for security
      form.reset('password')
    },
  })
}

</script>

<style scoped>
* {
  box-sizing: border-box;
}

html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

.page-container {
  width: 100vw;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  background-image: url('/assets/BACKGROUND IMAGE REG1.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
}

.login-container {
  width: 480px;
  max-height: 90vh;
  background: white;
  padding: 40px;
  overflow-y: auto;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-top: 8px solid #ff8c42;
}

.logo-section {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.logo-icon {
  width: 200px;
  height: auto;
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}

.login-title {
  font-size: 28px;
  font-weight: 700;
  color: #4caf50;
  margin: 0 0 8px 0;
}

.login-subtitle {
  font-size: 14px;
  color: #666;
  line-height: 1.5;
  margin: 0;
}

.login-form {
  width: 100%;
}

.form-section {
  margin-bottom: 25px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #000000;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-group {
  margin-bottom: 15px;
  position: relative;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 15px;
  display: flex;
  align-items: center;
  color: #666;
  pointer-events: none;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  color: #333;
  outline: none;
  background: white;
  transition: border-color 0.3s;
}

.form-input::placeholder {
  color: #999;
}

.form-input:focus {
  border-color: #ff8c42;
}

.password-wrapper {
  position: relative;
  width: 100%;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  z-index: 2;
}

.password-toggle:hover {
  color: #333;
}

.password-wrapper .input-wrapper {
  width: 100%;
}

.password-wrapper .form-input {
  padding-right: 45px;
}

.login-btn {
  width: 100%;
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
  margin-top: 20px;
  transition: background 0.3s;
}

.login-btn:hover:not(:disabled) {
  background: #e6763a;
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.disclaimer {
  background: #fff3cd;
  border: 1px solid #ffeaa7;
  padding: 15px;
  font-size: 13px;
  color: #856404;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-top: 25px;
  border-radius: 4px;
}

.disclaimer-icon {
  width: 20px;
  height: 20px;
  margin-top: 2px;
  flex-shrink: 0;
  color: #856404;
}

.disclaimer-content {
  flex: 1;
  line-height: 1.4;
}

@media (max-width: 768px) {
  .login-container {
    width: 95vw;
    padding: 30px 20px;
  }

  .logo-icon {
    width: 80px;
  }

  .login-title {
    font-size: 24px;
  }
}

.toggle-password{
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  cursor: pointer;
  font-weight: 600;
  padding: 4px 6px;
}

.error {
  color: #c92020;
  font-size: 13px;
  margin-top: 6px;
}
</style>
