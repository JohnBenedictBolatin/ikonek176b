<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref, computed, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3'
  import LayoutEmployee  from '@/Layouts/LayoutEmployee.vue';
</script>

<template>
    <LayoutEmployee>
    <Head>
      <title>Document Request • Form</title>
    </Head>

    <div class="page-root">
      <!-- Back link + page title bar -->
      <div class="title-bar">
        <Link :href="route('document_request_select_employee')" class="back-link">&lt; BACK</Link>
        <div class="page-title">Document Request</div>
      </div>

      <!-- form card centered on the map background (Layoutemployee provides the left sidebar) -->
      <div class="card request-card">
        <div class="card-head">REQUEST FORM</div>

        <form class="form-grid" @submit.prevent="submit">
          <!-- LEFT: Personal Information -->
          <div class="section">
            <div class="section-title"><span class="icon">👤</span> Personal Information</div>

            <div class="names">
              <input v-model="form.lName" type="text" placeholder="Last Name" />
              <input v-model="form.fName" type="text" placeholder="First Name" />
              <input v-model="form.mName" type="text" placeholder="Middle Name" />
              <select v-model="form.suffix">
                <option disabled value="">Suffix</option>
                <option>Jr.</option>
                <option>Sr.</option>
                <option>III</option>
                <option>IV</option>
                <option>None</option>
              </select>
            </div>

            <div class="personal-row">
              <input v-model="form.dob" type="date" />
              <select v-model="form.sex"><option disabled value="">Sex</option><option>Male</option><option>Female</option></select>
              <select v-model="form.civilStatus"><option disabled value="">Civil Status</option><option>Married</option><option>Single</option></select>
              <select v-model="form.role"><option disabled value="">Role/Position</option><option>employee</option><option>Kagawad</option></select>
            </div>
          </div>

          <!-- RIGHT: Contact Information -->
          <div class="section">
            <div class="section-title"><span class="icon">☎️</span> Contact Information</div>

            <div class="phone-row">
              <div class="prefix">+63</div>
              <input v-model="form.fContact" type="tel" maxlength="10" placeholder="Primary Number" />
            </div>

            <div class="phone-row">
              <div class="prefix">+63</div>
              <input v-model="form.sContact" type="tel" maxlength="10" placeholder="Secondary Number (Optional)" />
            </div>

            <div class="otp-row">
              <input v-model="form.otp" type="text" placeholder="Enter OTP Code (Primary Number)" />
              <button type="button" class="btn-resend" @click="resendOtp">RESEND</button>
            </div>
          </div>

          <!-- Proof of Intent - full width -->
          <div class="proof full">
            <div class="section-title"><span class="icon">📝</span> Proof of Intent</div>
            <textarea v-model="form.proofText" placeholder="What is the purpose of your request?"></textarea>

            <div class="upload-row">
              <select v-model="form.idType">
                <option value="">Type of Identification/Document</option>
                <option>Barangay ID</option>
                <option>Utility Bill</option>
                <option>Gov ID</option>
              </select>

              <label class="btn-upload">
                UPLOAD
                <input type="file" @change="handleFile" />
              </label>
            </div>
          </div>

          <!-- Submit button (full width) -->
          <!-- <div class="submit-row full">
            <button type="submit" class="btn-submit">SUBMIT</button>
          </div> -->
          <Link :href="route('document_request_submission_employee', { type: 'barangay-id' })" class="submit-row full">
            SUBMIT
            </Link>
        </form>
      </div>
    </div>
  </LayoutEmployee>
</template>

<script>


const form = ref({
  lName: '',
  fName: '',
  mName: '',
  suffix: '',
  dob: '',
  sex: '',
  civilStatus: '',
  role: '',
  fContact: '',
  sContact: '',
  otp: '',
  idType: '',
  proofText: ''
})

const goToLogin = () => {
  router.visit('/login')
}

const resendOtp = () => {
  alert('OTP resent.')
}

const goToSubmission = () => {
  router.visit('/employee_document_request_submission')
}
</script>

<style scoped>
.page-root { padding: 20px 28px 60px; }
.title-bar { display:flex; align-items:center; gap:16px; margin-bottom:12px }
.back-link { color:#374151; text-decoration:none; font-weight:700 }
.page-title { background:#ff8a00; color:#fff; padding:10px 18px; border-radius:6px; font-weight:800 }

.request-card { max-width:1000px; margin: -10px auto 30px; background: rgba(255,255,255,0.95); padding:20px; border-radius:10px; border:2px solid #f1f1f1; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
.card-head { font-weight:800; color:#111827; margin-bottom:12px }

.form-grid { display:grid; grid-template-columns: 1fr 1fr; gap:18px }
.section { background:transparent }
.section-title { color:#ff8a00; font-weight:800; margin-bottom:10px; display:flex; align-items:center; gap:8px }
.names { display:flex; gap:10px; }
.names input, .names select { flex:1; padding:8px 10px; border:1px solid #e6e6e6; border-radius:6px }
.personal-row { display:flex; gap:10px }
.personal-row input, .personal-row select { flex:1; padding:8px 10px; border:1px solid #e6e6e6; border-radius:6px }

.phone-row { display:flex; gap:10px; align-items:center }
.phone-row .prefix { background:#f2f2f2; padding:8px 10px; border:1px solid #e6e6e6; border-radius:6px }
.phone-row input { flex:1; padding:8px 10px; border:1px solid #e6e6e6; border-radius:6px }

.otp-row { display:flex; gap:10px; align-items:center }
.otp-row input { flex:1; padding:8px 10px; border:1px solid #e6e6e6; border-radius:6px }
.btn-resend { background:#10b981; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer }

.full { grid-column: 1 / -1 }
.proof textarea { width:100%; min-height:120px; padding:10px; border-radius:6px; border:1px solid #e6e6e6; resize:vertical }
.upload-row { display:flex; gap:12px; align-items:center; margin-top:10px }
.btn-upload { background:#10b981; color:#fff; padding:8px 14px; border-radius:6px; position:relative; overflow:hidden }
.btn-upload input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer }

.submit-row { display:flex; justify-content:flex-end }
.btn-submit { background:#ff8a00; color:#fff; padding:12px 40px; border-radius:6px; border:none; font-weight:800; cursor:pointer }

@media (max-width: 920px) {
  .form-grid { grid-template-columns: 1fr }
  .names { flex-direction:column }
  .personal-row { flex-direction:column }
  .phone-row { flex-direction:column }
}
</style>