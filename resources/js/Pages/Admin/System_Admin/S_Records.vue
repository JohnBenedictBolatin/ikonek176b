<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import LayoutAdmin from '@/Layouts/LayoutAdmin.vue'

  // --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

// map of role_id -> role_name (based on the table you provided)
const roleMap = {
  1: 'Resident',
  2: 'Barangay Captain',
  3: 'Barangay Secretary',
  4: 'Barangay Treasurer',
  5: 'Barangay Kagawad',
  6: 'Sangguniang Kabataan Chairman',
  7: 'Sangguniang Kabataan Kagawad',
  9: 'System Admin',
}

// computed display role (safe if user is null)
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  return id ? (roleMap[id] ?? `Role ${id}`) : 'Resident' // fallback to 'Resident' or whatever you prefer
})

// editable offenses list (static example)
const offenses = reactive([
  { id: 1, selected: false, offense: 'Littering in public area', category: 'Environment', date: '2023-04-12', status: 'Fined' },
  { id: 2, selected: false, offense: 'Noise disturbance (late night)', category: 'Public Order', date: '2024-01-21', status: 'Warning' },
  { id: 3, selected: false, offense: 'Unauthorized vendor stationing', category: 'Business Regulation', date: '2024-09-05', status: 'Fined' },
])

// local next id (for adding rows)
const nextId = ref(4)

function addRow() {
  offenses.push({ id: nextId.value++, selected: false, offense: '', category: '', date: '', status: '' })
}

function removeSelected() {
  for (let i = offenses.length - 1; i >= 0; i--) {
    if (offenses[i].selected) offenses.splice(i, 1)
  }
}

function saveChanges() {
  // In real app, send data to server via Inertia or fetch:
  // form.post(route('resident.offenses.update'), { data: offenses })
  // For now simulate save
  alert('Changes saved (simulation).\n' + JSON.stringify(offenses.map(o => ({ id: o.id, offense: o.offense, category: o.category, date: o.date, status: o.status })), null, 2))
}

function toggleSelectAll(e) {
  const v = e.target.checked
  offenses.forEach(o => o.selected = v)
}
</script>

<template>
  <LayoutAdmin>
    <Head>
      <title>Resident Offenses (Checklist)</title>
    </Head>

    <template #navigation>
            <!-- Navigation Buttons -->
            <nav class="flex-grow flex flex-col justify-center space-y-2 px-6">
                <Link :href="route('dashboard_admin')" class="nav-button">Dashboard</Link>
                <Link :href="route('users_admin')" class="nav-button">Users</Link>
                <Link :href="route('history_admin')" class="nav-button">History</Link>
                <Link :href="route('register_request_admin')" class="active">Register Request</Link>
                <Link :href="route('report_admin')" class="nav-button">Report</Link>
            </nav>
        </template>

    <div class="page">
      <div class="card">
        <h2 class="title">Recorded Offenses — Checklist</h2>
        <p class="muted">Edit the list, add new offenses, or select multiple rows to remove.</p>

        <div class="table-controls">
          <button class="btn add" @click="addRow">+ Add Row</button>
          <button class="btn remove" @click="removeSelected">− Remove Selected</button>
          <button class="btn save" @click="saveChanges">💾 Save Changes</button>
        </div>

        <div class="offenses-table-wrap">
          <table class="offenses-table">
            <thead>
              <tr>
                <th><input type="checkbox" @change="toggleSelectAll" aria-label="Select all" /></th>
                <th>#</th>
                <th>Offense</th>
                <th>Category</th>
                <th>Date Committed</th>
                <th>Action / Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in offenses" :key="item.id">
                <td><input type="checkbox" v-model="item.selected" /></td>
                <td>{{ item.id }}</td>
                <td>
                  <input v-model="item.offense" placeholder="Enter offense" />
                </td>
                <td>
                  <select v-model="item.category">
                    <option value="">-- Select Category --</option>
                    <option>Environment</option>
                    <option>Public Order</option>
                    <option>Business Regulation</option>
                    <option>Traffic</option>
                    <option>Other</option>
                  </select>
                </td>
                <td>
                  <input type="date" v-model="item.date" />
                </td>
                <td>
                  <select v-model="item.status">
                    <option value="">-- Select Status --</option>
                    <option>Fined</option>
                    <option>Warning</option>
                    <option>Completed</option>
                    <option>Dismissed</option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="hint">Tip: use <strong>Remove Selected</strong> after ticking rows on the left.</div>

        <div class="actions">
          <Link :href="route('register_request_admin')" class="cancel">Back to History</Link>
          <button class="next" @click="saveChanges">Next →</button>
        </div>
      </div>
    </div>
  </LayoutAdmin>
</template>

<style scoped>
.page { max-width: 1000px; margin: 20px auto; padding: 0 16px; }
.card { background:#fff; padding:20px; border-radius:12px; border:1px solid #f0f0f0; box-shadow:0 6px 24px rgba(0,0,0,0.1) }
.title { margin-bottom:8px; color:#ff8a00; font-weight:800; font-size:20px }
.muted { color:#6b7280; margin-bottom:12px }

.table-controls { display:flex; gap:8px; margin-bottom:12px }
.btn { padding:8px 12px; border-radius:8px; border:none; font-weight:700; cursor:pointer }
.btn.add { background:#2bb24a; color:#fff }
.btn.remove { background:#dc2626; color:#fff }
.btn.save { background:#ff8a00; color:#fff }

.offenses-table-wrap { margin-top:12px; overflow:auto }
.offenses-table { width:100%; border-collapse:collapse; min-width:760px }
.offenses-table th, .offenses-table td { padding:10px 8px; border-bottom:1px solid #f1f5f9; text-align:left; vertical-align:middle }
.offenses-table thead th { background:#fffaf0; color:#b04a00; font-weight:800 }
.offenses-table tbody tr:hover { background:#fff7ef }
.offenses-table input[type="text"], .offenses-table input[type="date"], .offenses-table select, .offenses-table input[type="text"] { width:100%; padding:8px; border:1px solid #e6e6e6; border-radius:6px }

.hint { margin-top:10px; color:#6b7280 }

.actions { display:flex; justify-content:space-between; align-items:center; margin-top:18px }
.cancel { color:#6b7280; text-decoration:none; font-weight:700 }
.next { background:#ff8a00; color:#fff; border:none; padding:10px 18px; border-radius:8px; font-weight:800; cursor:pointer }

@media (max-width: 800px) {
  .offenses-table { min-width:600px }
  .table-controls { flex-direction:column; }
  .actions { flex-direction:column; gap:8px }
}
</style>
