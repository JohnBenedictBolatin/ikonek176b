<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import LayoutEmployee from '@/Layouts/LayoutEmployee.vue'

// UI state
const stage = ref('CHOICE') // CHOICE | ONSITE_PROCESS | ONLINE_OPTIONS | ONLINE_PROCESS | DONE
const selectedOnline = ref(null) // 'gcash' | 'paypal' | 'paymaya'
const processing = ref(false)
const message = ref('')

function goToNotifications() {
  // navigate to notifications (change route name if different)
  router.visit(route('notification_request_employee'))
}

function chooseOnsite() {
  // show onsite flow then complete and redirect
  stage.value = 'ONSITE_PROCESS'
  message.value = 'Please proceed to Barangay Hall and pay the required fee at the cashier.\n\nWhen payment is received, your document will be released.'
}

function confirmOnsitePaid() {
  processing.value = true
  message.value = 'Recording payment...'
  // simulate processing then finish
  setTimeout(() => {
    processing.value = false
    stage.value = 'DONE'
    message.value = 'Payment recorded. Your request is complete.'
    // redirect to notifications after a short pause so user sees confirmation
    setTimeout(() => goToNotifications(), 900)
  }, 900)
}

function chooseOnline() {
  stage.value = 'ONLINE_OPTIONS'
  selectedOnline.value = null
}

function selectOnline(method) {
  selectedOnline.value = method
  stage.value = 'ONLINE_PROCESS'
  message.value = ''
}

function proceedOnlinePayment() {
  if (!selectedOnline.value) return
  processing.value = true
  message.value = `Redirecting to ${selectedOnline.value.toUpperCase()} payment gateway...`
  // simulate online payment flow
  setTimeout(() => {
    message.value = `Payment via ${selectedOnline.value.toUpperCase()} completed successfully.`
    processing.value = false
    stage.value = 'DONE'
    setTimeout(() => goToNotifications(), 900)
  }, 1400)
}

function backToChoice() {
  stage.value = 'CHOICE'
  selectedOnline.value = null
  message.value = ''
  processing.value = false
}
</script>

<template>
  <LayoutEmployee>
    <Head>
      <title>Payment</title>
    </Head>

    <div class="page">
      <div class="card">
        <h2 class="title">Payment Options</h2>

        <div v-if="stage === 'CHOICE'" class="choice">
          <p class="lead">Choose how you'd like to pay for your document.</p>

          <div class="options">
            <button class="btn onsite" @click="chooseOnsite">Pay Onsite (at Barangay Hall)</button>
            <button class="btn online" @click="chooseOnline">Pay Online</button>
          </div>
        </div>

        <!-- Onsite flow -->
        <div v-if="stage === 'ONSITE_PROCESS'" class="onsite-flow">
          <h3 class="section-title">Pay Onsite</h3>
          <p class="muted">Follow the instructions below and confirm when you've paid at the cashier.</p>
          <div class="info-box">
            <p>{{ message }}</p>
            <ul>
              <li>Bring a valid ID when claiming the document.</li>
              <li>Cash payment at the Barangay Hall cashier.</li>
            </ul>
          </div>

          <div class="actions">
            <button class="btn confirm" :disabled="processing" @click="confirmOnsitePaid">
              <span v-if="processing">Processing...</span>
              <span v-else>I've Paid — Record Payment</span>
            </button>
            <button class="btn ghost" @click="backToChoice" :disabled="processing">Back</button>
          </div>
        </div>

        <!-- Online options -->
        <div v-if="stage === 'ONLINE_OPTIONS'" class="online-options">
          <h3 class="section-title">Online Payment</h3>
          <p class="muted">Select one of the online payment providers below.</p>

          <div class="providers">
            <button class="provider" @click="selectOnline('gcash')">
              <img src="/mnt/data/gcash.png" alt="GCash" />
              <div>GCash</div>
            </button>

            <button class="provider" @click="selectOnline('paypal')">
              <img src="/mnt/data/paypal.png" alt="PayPal" />
              <div>PayPal</div>
            </button>

            <button class="provider" @click="selectOnline('paymaya')">
              <img src="/mnt/data/paymaya.png" alt="PayMaya" />
              <div>PayMaya</div>
            </button>
          </div>

          <div class="actions">
            <button class="btn ghost" @click="backToChoice">Back</button>
          </div>
        </div>

        <!-- Online process -->
        <div v-if="stage === 'ONLINE_PROCESS'" class="online-process">
          <h3 class="section-title">{{ selectedOnline?.toUpperCase() }} Payment</h3>
          <p class="muted">You will be redirected to the provider to complete the payment. (Simulation)</p>

          <div class="info-box">
            <p v-if="message">{{ message }}</p>
            <p v-else>Click proceed to continue to the payment gateway.</p>
          </div>

          <div class="actions">
            <button class="btn confirm" :disabled="processing" @click="proceedOnlinePayment">
              <span v-if="processing">Processing...</span>
              <span v-else>Proceed to Pay</span>
            </button>
            <button class="btn ghost" @click="backToChoice" :disabled="processing">Cancel</button>
          </div>
        </div>

        <!-- Done -->
        <div v-if="stage === 'DONE'" class="done">
          <h3 class="section-title">Payment Completed</h3>
          <p class="muted">{{ message }}</p>
          <div class="actions">
            <button class="btn confirm" @click="goToNotifications">Go to Notifications</button>
          </div>
        </div>
      </div>
    </div>
  </LayoutEmployee>
</template>

<style scoped>
.page { max-width: 900px; margin: 20px auto; padding: 0 12px; }
.card { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #f0f0f0; box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
.title { margin:0 0 8px; color: #ff8a00; font-weight:900 }
.lead { color: #374151; margin-bottom:12px }
.options { display:flex; gap:12px; }
.btn { padding:10px 16px; border-radius:10px; font-weight:800; border:none; cursor:pointer }
.btn.onsite { background:#fff; border:2px solid #2bb24a; color:#2bb24a }
.btn.online { background:#2bb24a; color:#fff }

.section-title { color:#ff8a00; margin-bottom:8px; font-weight:800 }
.muted { color:#6b7280 }
.info-box { background:#f9fafb; border:1px solid #eef2ff; padding:12px; border-radius:8px; margin:12px 0 }
.actions { display:flex; gap:8px; justify-content:flex-end; margin-top:8px }
.btn.confirm { background:#2bb24a; color:#fff }
.btn.ghost { background:transparent; border:1px solid #e5e7eb }

.providers { display:flex; gap:12px; margin-top:12px }
.provider { display:flex; flex-direction:column; align-items:center; gap:8px; padding:12px; border-radius:8px; background:#fff; border:1px solid #e6e6e6; cursor:pointer; min-width:110px }
.provider img { width:64px; height:40px; object-fit:contain }

.done { text-align:center }

@media (max-width:600px){
  .options { flex-direction:column }
  .providers { flex-direction:column }
  .actions { justify-content:center }
}
</style>
