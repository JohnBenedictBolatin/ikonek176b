<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3'
  // import { route } from 'ZiggyVue'
  import  LayoutResident  from '@/Layouts/LayoutResident.vue';
</script>


<template>
    <LayoutResident>
        <Head>
            <title>pickup info</title>
        </Head>

        <div class="container">
            <h2>REQUEST ACCEPTED</h2>
            <h4>Request Number</h4>
            <br />
            <div class="message">
                kfjdsjk <br />
                jkdfsjjhd
            </div>
            <br />
            <img src="" alt="Proof Image" />
            <br />
            <label>WHAT</label>
            <p>PROOF OF DOCUMENT (Barangay Certificate)</p>
            <br />
            <label>WHERE</label>
            <p>BARANGAY (FOR BARANGAY HALL)</p>
            <br />
            <label>WHEN</label>
            <p>DATE</p>
            <br />
            <label>WHO</label>
            <p>YOUR NAME</p>
            <br />

            <button type="button" @click="goToConfirm">CONFIRM</button>
        </div>
    </LayoutResident>
</template>

<script>
function goToConfirm() {
  // Replace with actual route name
  router.visit('/your-confirm-route')
}
</script>