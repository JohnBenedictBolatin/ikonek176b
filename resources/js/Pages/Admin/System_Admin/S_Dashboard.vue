<template>
    <Head>
        <title>System Admin Dashboard</title>
    </Head>

    <!-- Full Screen Layout -->
    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/ADMIN LOGO1.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <!-- Settings Dropdown -->
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Terms & Conditions</Link>
                        <Link href="#" class="settings-item" @click.prevent="logout">
                            Sign Out
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area - Full Width -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img src="/assets/ADMIN.png" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{user.name || 'Unknown User'}}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        :href="route('dashboard_admin')" 
                        class="nav-item active"
                    >
                        📊 Dashboard
                    </Link>
                    <Link 
                        :href="route('users_admin')" 
                        class="nav-item"
                    >
                        👥 Users
                    </Link>
                    <Link 
                        :href="route('history_admin')" 
                        class="nav-item"
                    >
                        📜 History
                    </Link>
                    <Link 
                        :href="route('register_request_admin')" 
                        class="nav-item"
                    >
                        📝 Register Request
                    </Link>
                    <Link 
                        :href="route('report_admin')" 
                        class="nav-item"
                    >
                        🚩 Report
                    </Link>
                </div>

            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Dashboard Header -->
                    <div class="dashboard-header">
                        <div class="dashboard-title">
                            <h2>Dashboard Overview</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="dashboard-container">
                        <!-- Top Metrics Grid -->
                        <div class="metrics-grid">
                            <!-- Total Registration Requests Card -->
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-info">
                                        <div class="metric-title">📨 Total Registration Requests</div>
                                        <div class="metric-subtitle">As of {{ currentDate }}</div>
                                    </div>
                                    <div class="metric-value green">467</div>
                                </div>

                                <div class="chart-placeholder">
                                    <img src="/assets/REQUEST GRAPH.png" alt="Graph placeholder" style="width: 100%; height: 100%; object-fit: contain;" />
                                </div>

                                <div class="metric-controls">
                                    <select class="control-select" v-model="postRequestYear">
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                    </select>
                                    <select class="control-select" v-model="postRequestRange">
                                        <option value="jan">January</option>
                                        <option value="feb">February</option>
                                        <option value="mar">March</option>
                                        <option value="apr">April</option>
                                        <option value="may">May</option>
                                        <option value="jun">June</option>
                                        <option value="jul">July</option>
                                        <option value="aug">August</option>
                                        <option value="sep">September</option>
                                        <option value="oct">October</option>
                                        <option value="nov">November</option>
                                        <option value="dec">Dec</option>
                                        
                                    </select>
                                </div>
                            </div>

                            <!-- Total Reports Card -->
                            <div class="metric-card">
                                <div class="metric-header">
                                    <div class="metric-info">
                                        <div class="metric-title">📝 Total Reports</div>
                                        <div class="metric-subtitle">As of {{ currentDate }}</div>
                                    </div>
                                    <div class="metric-value orange">105</div>
                                </div>

                                <div class="chart-placeholder">
                                    <img src="/assets/REPORT GRAPH.png" alt="Graph placeholder" style="width: 100%; height: 100%; object-fit: contain;" />
                                </div>

                                <div class="metric-controls">
                                    <select class="control-select" v-model="reportYear">
                                        <option value="2024">For Reports</option>
                                        <option value="2025">2025</option>
                                    </select>
                                    <select class="control-select" v-model="reportRange">
                                        <option value="jan">January</option>
                                        <option value="feb">February</option>
                                        <option value="mar">March</option>
                                        <option value="apr">April</option>
                                        <option value="may">May</option>
                                        <option value="jun">June</option>
                                        <option value="jul">July</option>
                                        <option value="aug">August</option>
                                        <option value="sep">September</option>
                                        <option value="oct">October</option>
                                        <option value="nov">November</option>
                                        <option value="dec">Dec</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section -->
                        <div class="bottom-section">
                            <!-- Small Metrics -->
                            <div class="small-metrics">
                                <div class="small-metric-card">
                                    <div class="small-metric-icon orange">👥</div>
                                    <div class="small-metric-content">
                                        <div class="small-metric-label">Residents</div>
                                        <div class="small-metric-value green">1,201</div>
                                    </div>
                                </div>

                                <div class="small-metric-card">
                                    <div class="small-metric-icon green">🏢</div>
                                    <div class="small-metric-content">
                                        <div class="small-metric-label">Barangay Officials</div>
                                        <div class="small-metric-value green">16</div>
                                    </div>
                                </div>
                       </div>
                            <!-- Large Chart Card -->
                            <div class="large-chart-card">
                                <div class="chart-card-header">
                                    <div class="chart-title">Residents and Officials </div>
                                    <div class="chart-controls">
                                        <select class="control-select" v-model="comparisonYear">
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                        </select>
                                        <select class="control-select" v-model="comparisonRange">
                                            <option value="jan">Jan</option>
                                            <option value="dec">Dec</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="large-chart-placeholder">
                                    <img src="assets/VS GRAPH.png" alt="Comparison graph placeholder" style="width: 100%; height: 100%; object-fit: contain;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

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

// Reactive data
const showSettings = ref(false)
const postRequestYear = ref('2025')
const postRequestRange = ref('jan')
const reportYear = ref('2025')
const reportRange = ref('jan')
const comparisonYear = ref('2025')
const comparisonRange = ref('jan')

// Computed properties
const currentDate = computed(() => {
    const date = new Date()
    return date.toLocaleDateString('en-US', { 
        month: '2-digit', 
        day: '2-digit', 
        year: 'numeric' 
    })
})

// Methods
const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const logout = () => {
  router.get(route('login_admin'))
}

const openHelp = () => {
    console.log('Open help center')
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Full screen layout reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.app-container {
    min-height: 100vh;
    width: 100vw;
    background: url('/assets/BG MAIN.png') no-repeat center center fixed;
    background-size: cover;
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Orange Header Bar */
.header-bar {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    color: white;
    padding: 5px 0;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000;
}

.header-content {
    max-width: none;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 10px;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-logo {
    width: 180px;
    height: 60px;
    padding: 1px;
}

.header-actions {
    position: relative;
}

.settings-btn-img {
    margin-right: 30px;
    width: 30px;
    cursor: pointer;
    transition: transform 0.2s;
}

.settings-btn-img:hover {
    transform: scale(1.1);
}

.settings-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 200px;
    z-index: 1000;
    margin-top: 10px;
    border: 1px solid rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-item {
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
    cursor: pointer;
    font-weight: 500;
}

.settings-item:hover {
    background: #fff7ef;
    color: #ff8c42;
}

.settings-item:last-child {
    border-bottom: none;
}

/* Main Layout - Full Width */
.main-layout {
    flex: 1;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 25px;
    width: 100%;
    max-width: none;
    margin: 0;
    margin-top: 70px;
    padding: 25px 30px;
}

/* Sidebar */
.sidebar {
    background: transparent;
    padding-right: 20px;
}

.profile-card {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    border-radius: 15px;
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(35, 150, 64, 0.3);
    border: 1px solid rgba(255,255,255,0.2);
}



.profile-avatar {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.profile-name {
    font-weight: 700;
    font-size: 17px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.profile-role {
    font-size: 12px;
    background: #ff8c42;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    backdrop-filter: blur(10px);
    margin-top: 5px;
}

.nav-menu {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.05);
}

.nav-item {
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    font-weight: 500;
}

.nav-item:last-child {
    border-bottom: none;
}

.nav-item:hover {
    background: #fff7ef;
    transform: translateX(3px);
}

.nav-item.active {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    color: #239640;
    font-weight: 600;
    border-left: 4px solid #239640;
}

.faq-btn {
    width: 100%;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(43, 178, 74, 0.3);
    transition: all 0.3s ease;
    font-size: 14px;
}

.faq-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(43, 178, 74, 0.4);
}

/* Content Area */
.content-area {
    display: flex;
    flex-direction: column;
    width: 100%;
}

/* Main Content */
.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    flex: 1;
    border: 1px solid rgba(0,0,0,0.05);
}

.dashboard-header {
    background: #ff8c42;
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-title h2 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

/* Dashboard Container */
.dashboard-container {
    padding: 25px;
}

/* Metrics Grid */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.metric-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #ffe6cc;
    box-shadow: 0 6px 18px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.metric-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.metric-title {
    font-weight: 700;
    font-size: 16px;
    color: #333;
}

.metric-subtitle {
    font-size: 12px;
    color: #8b8b8b;
}

.metric-value {
    font-size: 36px;
    font-weight: 900;
    padding: 10px 20px;
    border-radius: 10px;
    min-width: 88px;
    text-align: center;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.metric-value.green {
    background: linear-gradient(135deg, #2bb24a, #239640);
}

.metric-value.orange {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}

.chart-placeholder {
    height: 200px;
    border-radius: 8px;
    border: 1px dashed #ddd;
    background: linear-gradient(180deg, rgba(0,0,0,0.01), rgba(0,0,0,0.02));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8b8b8b;
    font-style: italic;
    font-size: 14px;
}

.metric-controls {
    display: flex;
    gap: 10px;
}

.control-select {
    flex: 1;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    background: #f8f9fa;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.control-select:hover {
    border-color: #ff8c42;
}

.control-select:focus {
    outline: none;
    border-color: #ff8c42;
    background: white;
}

/* Bottom Section */
.bottom-section {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 20px;
}

.small-metrics {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.small-metric-card {
    background: white;
    padding: 18px;
    border-radius: 10px;
    border: 1px solid #ffe6cc;
    box-shadow: 0 6px 12px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    gap: 15px;
}

.small-metric-icon {
    font-size: 24px;
    padding: 12px 16px;
    border-radius: 10px;
    color: white;
    font-weight: 800;
}


.small-metric-content {
    flex: 1;
}

.small-metric-label {
    font-size: 13px;
    color: #000000;
    font-weight: 700;
    margin-bottom: 15px;
}

.small-metric-value {
    font-size: 28px;
    font-weight: 900;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-block;
}

.small-metric-value.green {
    background: linear-gradient(135deg, #e8f8ed, #d4f1dd);
    color: #2bb24a;
}

/* Large Chart Card */
.large-chart-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #ffe6cc;
    box-shadow: 0 6px 18px rgba(0,0,0,0.04);
    min-height: 300px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-title {
    font-weight: 800;
    font-size: 16px;
    color: #333;
}

.chart-controls {
    display: flex;
    gap: 10px;
}

.large-chart-placeholder {
    flex: 1;
    border-radius: 8px;
    border: 1px dashed #ddd;
    background: linear-gradient(180deg, rgba(0,0,0,0.01), rgba(0,0,0,0.02));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8b8b8b;
    font-style: italic;
    font-size: 14px;
    min-height: 240px;
}

/* Custom Scrollbar */
.dashboard-container::-webkit-scrollbar {
    width: 6px;
}

.dashboard-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.dashboard-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.dashboard-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .bottom-section {
        grid-template-columns: 1fr;
    }
    
    .small-metrics {
        flex-direction: row;
    }
}

@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .main-layout {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
    }
    
    .sidebar {
        order: 2;
        padding-right: 0;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .small-metrics {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }
    
    .metric-value {
        font-size: 28px;
        padding: 8px 16px;
    }
    
    .small-metric-value {
        font-size: 24px;
    }
}
</style>