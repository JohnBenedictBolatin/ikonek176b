<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3'
  import LayoutEmployee  from '@/Layouts/LayoutEmployee.vue';
</script>


<template>
    <LayoutEmployee>
        <Head>
            <title>pickup info</title>
        </Head>

        <div class="container">
            <div class="header">
                <h2>REQUEST ACCEPTED</h2>
                <div class="request-number">REQUEST NO. #00235</div>
            </div>

            <div class="status-message">
                <p>Your request to acquire a <strong>Barangay Certificate</strong> has been <strong>approved</strong>.</p>
                <p>You may now proceed to claim your certificate by following the instructions provided. Thank you!</p>
            </div>

            <div class="info-card">
                <img src="/mnt/data/ccb8e182-7b27-4660-b43e-cafeeba08825.png" alt="Barangay Hall" class="info-img" />

                <div class="details">
                <div class="detail-item">
                    <label>WHAT</label>
                    <p>Pickup of Document (Barangay Certificate)</p>
                </div>

                <div class="detail-item">
                    <label>WHERE</label>
                    <p>Barangay 176B Barangay Hall</p>
                </div>

                <div class="detail-item">
                    <label>WHEN</label>
                    <p>May 25-27, 2025, 9:00 AM - 3:00 PM</p>
                </div>

                <div class="detail-item">
                    <label>WHO</label>
                    <p>Find Ms. Mercy Alpano</p>
                </div>
                </div>
            </div>

            <!-- Payment Option -->
            <div class="payment-btn">
                <!-- <button @click="goToPayment" class="btn-payment">PROCEED TO PAYMENT</button> -->
                 <Link :href="route('payment_employee')" class="btn-payment">PROCEED TO PAYMENT</Link>
            </div>

            <div class="confirm-btn">
                <!-- <button @click="goToConfirm" class="btn-confirm">CONFIRM</button> -->
                <Link :href="route('notification_request_employee')" class="btn-confirm">CONFIRM</Link>
                
            </div>
        </div>
    </LayoutEmployee>
</template>

<script>
function goToConfirm() {
  // Replace with actual route name
  router.visit('/notification_request_employee')
}

function goToPayment() {
  router.visit('/payment_employee')
}
</script>

<style scoped>
.container { max-width: 1180px; margin: 0 auto; padding: 20px; background: #fff; border-radius: 12px; border: 1px solid #f3f4f6; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }

.header { margin-bottom: 20px; text-align: center; }
.request-number { font-size: 18px; color: #ff8a00; font-weight: 700; margin-top: 8px; }

.status-message { font-size: 14px; color: #4b5563; text-align: center; margin-bottom: 20px; }

.info-card { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #f0f0f0; box-shadow: 0 6px 24px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; gap: 16px; }
.info-img { width: 100%; max-width: 400px; border-radius: 8px; object-fit: cover; }

.details { display: flex; flex-direction: column; width: 100%; gap: 12px; }
.detail-item { display: flex; flex-direction: column; align-items: flex-start; }
.detail-item label { font-weight: 700; color: #f58200; font-size: 14px; }
.detail-item p { font-size: 14px; color: #333; margin-top: 6px; }

.payment-btn,
.confirm-btn { display: flex; justify-content: center; margin-top: 20px; }

.btn-payment { background: #4CAF50; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; transition: background-color 0.3s ease; }
.btn-payment:hover { background-color: #449d48; }

.btn-confirm { background: #ff8a00; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; transition: background-color 0.3s ease; }
.btn-confirm:hover { background-color: #e57c00; }
</style>