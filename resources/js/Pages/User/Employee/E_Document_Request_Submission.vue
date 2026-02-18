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
        <title>Request Submitted</title>
      </Head>

      <div class="flex flex-col items-center justify-center min-h-screen bg-gray-50 p-6 text-center">
        <!-- Large Check Icon -->
        <img src="/images/checkmark.png" alt="Success" class="w-24 h-24 mb-4" />

        <!-- Text Content -->
        <h1 class="text-2xl font-bold text-green-600 mb-2">Request Submitted!</h1>
        <p class="text-lg font-medium text-gray-700 mb-1">Request Number: <span class="font-bold">#CED-00234</span></p>
        <p class="text-sm text-gray-600 mb-6">
          Your request for a <strong>Cedula</strong> has been successfully submitted. You will receive a notification once it is ready for pickup at the Barangay Hall. Kindly bring a valid ID upon claiming.
        </p>

        <!-- Button -->
        <Link
          :href="route('notification_request_resident')"
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded shadow"
        >
          View Request
        </Link>
      </div>
  </LayoutEmployee>>
</template>