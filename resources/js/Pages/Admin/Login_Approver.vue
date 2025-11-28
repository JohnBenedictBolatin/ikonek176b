<template>
  <Head>
    <title>Approver Login - iKONEK 176B</title>
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

      <!-- Login Form -->
      <form @submit.prevent="submit" class="login-form">
        <div class="form-section">
          <h3 class="section-title">🛡️ Approver Access</h3>

          <div class="form-group">
            <input
              v-model="form.phone"
              type="text"
              placeholder="Username"
              class="form-input"
            />
          </div>

          <div class="form-group">
            <div class="password-wrapper">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Password"
                class="form-input"
              />
              <!-- password toggle -->
              <!-- <button
                type="button"
                class="toggle-password"
                @click="showPassword = !showPassword"
                :aria-pressed="showPassword"
                title="Toggle password visibility"
              >
                {{ showPassword ? 'Hide' : 'Show' }}
              </button> -->
            </div>
          </div>
        </div>

        <!-- Button goes directly to approver dashboard -->
        <button type="submit" class="login-btn" :disabled="form.processing">
          <span v-if="form.processing">Logging in...</span>
          <span v-else>LOGIN</span>
        </button>
      </form>

      <!-- Disclaimer -->
      <div class="disclaimer">
        <span class="disclaimer-icon">⚠️</span>
        <div class="disclaimer-content">
          <strong>Disclaimer:</strong> This portal is exclusive for
          iKonek-176B Approver only.
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

.form-input {
  width: 100%;
  padding: 12px 15px;
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
  font-size: 18px;
  margin-top: 2px;
  flex-shrink: 0;
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
