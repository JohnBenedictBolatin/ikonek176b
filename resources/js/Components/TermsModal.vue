<template>
  <div v-if="open" class="modal-backdrop" @click.self="emitClose">
    <div class="modal terms-modal" role="dialog" aria-modal="true" aria-labelledby="termsTitle">
      <h2 id="termsTitle" class="modal-title">Terms &amp; Conditions</h2>

      <div class="modal-body">
        <p class="justified-text">
          By using the iKonek176B portal, you agree to use this system only for lawful and legitimate
          barangay-related purposes. You are responsible for the accuracy of the information you submit.
        </p>
        <p class="justified-text">
          All data submitted through this portal may be used by Barangay 176B for processing document requests,
          community services, and official records, in accordance with the Data Privacy Act of 2012. Misuse of the
          system—including providing false information or attempting unauthorized access—may result in suspension of
          access and possible legal action.
        </p>
        <p class="justified-text">
          The barangay is not liable for delays caused by unforeseen technical issues, maintenance, or incorrect
          information provided by the user. For concerns, please contact the barangay office or the Help Center.
        </p>
      </div>

      <div class="modal-actions">
        <button class="btn-primary" type="button" @click="emitClose">I Understand</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  open: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

function emitClose() {
  emit('close')
}
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3000;
}

.modal.terms-modal {
  background: #ffffff;
  border-radius: 16px;
  max-width: 640px;
  width: 92%;
  padding: 24px 28px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
}

.modal-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #111827;
}

.modal-body {
  max-height: 60vh;
  overflow: auto;
  padding-right: 4px;
}

.justified-text {
  font-size: 14px;
  line-height: 1.6;
  color: #374151;
  text-align: justify;
  margin: 0 0 10px 0;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}

.btn-primary {
  background: #239640;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.1s ease;
}

.btn-primary:hover {
  background: #1e7d36;
  transform: translateY(-1px);
}
</style>









