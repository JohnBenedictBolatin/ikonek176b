<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3'
  import { useForm } from '@inertiajs/vue3'

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
</style>