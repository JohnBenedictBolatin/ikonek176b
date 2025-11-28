<template>
    <Head>
        <title>Event Assistance Request</title>
    </Head>

    <div class="app-container">
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/ADMIN LOGO1.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
                        <Link href="#" class="settings-item" @click="closeSettings">Terms & Conditions</Link>
                        <Link href="#" class="settings-item" @click="logout">Sign Out</Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-layout">
            <div class="sidebar">
                <div class="profile-card">
                    <img src="/assets/ADMIN.png" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link href="#" class="nav-item" @click="navigateToDocumentRequest">
                        📄 Document Request
                    </Link>
                    <Link href="#" class="nav-item active">
                        📝 Event Assistance Request
                    </Link>
                    <Link href="#" class="nav-item" @click="navigateToHistory">
                        📜 History
                    </Link>
                </div>
            </div>

            <div class="content-area">
                <div class="main-content">
                    <div class="users-header">
                        <div class="users-title">
                            <h2>Event Assistance Request</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-left">
                            <span class="filter-label">Filter by</span>
                            <div class="filter-dropdown-wrapper">
                                <button class="filter-dropdown-btn" @click="toggleSortDropdown">
                                    {{ sortOption.toUpperCase() }}
                                    <span class="filter-arrow" :class="{ rotated: showSortDropdown }">▼</span>
                                </button>
                                <div v-if="showSortDropdown" class="filter-dropdown-menu">
                                    <button @click="selectSort('newest')" :class="{ active: sortOption === 'newest' }">NEWEST</button>
                                    <button @click="selectSort('oldest')" :class="{ active: sortOption === 'oldest' }">OLDEST</button>
                                    <button @click="selectSort('relevant')" :class="{ active: sortOption === 'relevant' }">RELEVANT</button>
                                </div>
                            </div>
                            <div class="filter-dropdown-wrapper">
                                <button class="filter-dropdown-btn" @click="toggleFilterDropdown">
                                    {{ filterOption.toUpperCase() }}
                                    <span class="filter-arrow" :class="{ rotated: showFilterDropdown }">▼</span>
                                </button>
                                <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                    <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                    <button @click="selectFilter('venue')" :class="{ active: filterOption === 'venue' }">VENUE</button>
                                    <button @click="selectFilter('equipment')" :class="{ active: filterOption === 'equipment' }">EQUIPMENT</button>
                                    <button @click="selectFilter('personnel')" :class="{ active: filterOption === 'personnel' }">PERSONNEL</button>
                                </div>
                            </div>
                        </div>
                        <div class="filter-right">  
                            <div class="search-container">
                                <input type="text" v-model="searchQuery" @input="performSearch" placeholder="SEARCH..." class="search-input" />
                                <button class="search-btn" @click="performSearch">🔍</button>
                            </div>
                        </div>
                    </div>

                    <div class="requests-container">
                        <div v-for="request in filteredRequests" :key="request.id" class="request-card">
                            <div class="request-content">
                                <div class="request-left">
                                    <img :src="request.profileImg" alt="Profile" class="request-avatar" />
                                    <div class="request-info">
                                        <h3 class="request-name">{{ request.name }}</h3>
                                        <p class="request-doc-type">{{ request.assistanceType }}</p>
                                        <p class="request-ref-code">{{ request.referenceCode }}</p>
                                    </div>
                                </div>
                                <div class="request-right">
                                    <p class="request-date">{{ request.date }}</p>
                                    <button @click="openModal(request)" class="view-btn">View Request</button>
                                </div>
                            </div>
                        </div>

                        <div v-if="filteredRequests.length === 0" class="no-requests">
                            <p>No event assistance requests found matching your criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-container" @click.stop>
                <button @click="closeModal" class="modal-close">✕</button>
                <div class="modal-content">
                    <div class="modal-top">
                        <div class="modal-user-section">
                            <img :src="selectedRequest.profileImg" alt="Profile" class="modal-avatar" />
                            <div>
                                <h3 class="modal-name">{{ selectedRequest.name }}</h3>
                                <p class="modal-label">Event Assistance Request</p>
                                <p class="modal-ref">{{ selectedRequest.referenceCode }}</p>
                            </div>
                        </div>
                        <div class="modal-doc-section">
                            <h3 class="modal-doc-type">{{ selectedRequest.assistanceType }}</h3>
                            <p class="modal-purpose">Event: {{ selectedRequest.eventName }}</p>
                        </div>
                        <div class="modal-upload-section">
                            <button class="upload-btn">View Attachments</button>
                        </div>
                    </div>

                    <div class="modal-details">
                        <div class="detail-description">
                            <p class="detail-label">Event Description:</p>
                            <p class="detail-value">{{ selectedRequest.eventDescription }}</p>
                        </div>

                        <div class="details-grid">
                            <div class="detail-item">
                                <p class="detail-label">Event Date:</p>
                                <p class="detail-value">{{ selectedRequest.eventDate }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Event Time:</p>
                                <p class="detail-value">{{ selectedRequest.eventTime }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Expected Attendees:</p>
                                <p class="detail-value">{{ selectedRequest.expectedAttendees }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Contact Number:</p>
                                <p class="detail-value">{{ selectedRequest.contact }}</p>
                            </div>
                            <div class="detail-item">
                                <p class="detail-label">Age:</p>
                                <p class="detail-value">{{ selectedRequest.age }}</p>
                            </div>
                        </div>

                        <div class="detail-item-full">
                            <p class="detail-label">Venue/Location:</p>
                            <p class="detail-value">{{ selectedRequest.venue }}</p>
                        </div>

                        <div class="detail-description">
                            <p class="detail-label">Specific Assistance Needed:</p>
                            <p class="detail-value">{{ selectedRequest.assistanceDetails }}</p>
                        </div>

                        <div class="detail-item-full">
                            <p class="detail-label">Home Address:</p>
                            <p class="detail-value">{{ selectedRequest.address }}</p>
                        </div>

                        <div class="comment-section">
                            <textarea v-model="comment" placeholder="Leave a comment or note..." class="comment-textarea" rows="4"></textarea>
                        </div>

                        <div class="modal-actions">
                            <button @click="openApprovalModal" class="approve-btn">Approve</button>
                            <button @click="openRejectionModal" class="reject-btn">Reject</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isApprovalModalOpen" class="modal-overlay" @click="closeApprovalModal">
            <div class="modal-container approval-modal" @click.stop>
                <button @click="closeApprovalModal" class="modal-close">✕</button>
                <div class="modal-content">
                    <h2 class="approval-title">Set Assistance Details</h2>
                    <p class="approval-subtitle">Provide the assistance details for the approved request</p>

                    <div class="approval-form">
                        <div class="form-group">
                            <label class="form-label">Assistance Type Approved</label>
                            <input type="text" v-model="approvalDetails.assistanceType" class="form-input" :placeholder="selectedRequest.assistanceType" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Amount/Value of Assistance</label>
                            <input type="text" v-model="approvalDetails.assistanceAmount" class="form-input" placeholder="e.g., Equipment list, Venue details" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Release Date</label>
                            <input type="date" v-model="approvalDetails.releaseDate" class="form-input" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Claiming/Coordination Location</label>
                            <input type="text" v-model="approvalDetails.claimLocation" class="form-input" placeholder="e.g., Barangay Hall, Office 2" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Person</label>
                            <input type="text" v-model="approvalDetails.contactPerson" class="form-input" placeholder="Name of barangay staff to coordinate with" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" v-model="approvalDetails.contactNumber" class="form-input" placeholder="+639" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Special Instructions/Requirements</label>
                            <textarea v-model="approvalDetails.instructions" class="form-textarea" rows="4" placeholder="Any documents to bring, preparation needed, or special instructions..."></textarea>
                        </div>

                        <div class="approval-actions">
                            <button @click="confirmApproval" class="confirm-btn">Confirm Approval</button>
                            <button @click="closeApprovalModal" class="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isRejectionModalOpen" class="modal-overlay" @click="closeRejectionModal">
            <div class="modal-container rejection-modal" @click.stop>
                <button @click="closeRejectionModal" class="modal-close">✕</button>
                <div class="modal-content">
                    <h2 class="rejection-title">Reason for Rejection</h2>
                    <p class="rejection-subtitle">Please provide a clear reason for rejecting this assistance request</p>

                    <div class="rejection-form">
                        <div class="form-group">
                            <label class="form-label">Rejection Reason</label>
                            <textarea v-model="rejectionReason" class="form-textarea" rows="6" placeholder="Explain why this request cannot be approved..." required></textarea>
                        </div>

                        <div class="rejection-actions">
                            <button @click="confirmRejection" class="confirm-reject-btn">Confirm Rejection</button>
                            <button @click="closeRejectionModal" class="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3'
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

const showSettings = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const isModalOpen = ref(false)
const isApprovalModalOpen = ref(false)
const isRejectionModalOpen = ref(false)
const selectedRequest = ref(null)
const comment = ref('')
const rejectionReason = ref('')

const approvalDetails = ref({
    assistanceType: '',
    assistanceAmount: '',
    releaseDate: '',
    claimLocation: '',
    contactPerson: '',
    contactNumber: '',
    instructions: ''
})

const requests = ref([
    {
        id: 1,
        name: 'Roberto Cruz',
        profileImg: '/assets/PROFILE PIC 2.jpg',
        assistanceType: 'Venue Assistance',
        referenceCode: 'EA-2025-001',
        date: '09/28/2025',
        dateObj: new Date('2025-09-28'),
        eventName: 'Community Wedding Reception',
        eventDescription: 'I am requesting use of barangay hall for a wedding reception. This is a small, intimate gathering of family and close friends from our community.',
        eventDate: 'November 5, 2025',
        eventTime: '12:00 PM - 6:00 PM',
        expectedAttendees: '80-100 guests',
        contact: '09267891234',
        venue: 'Barangay Multi-Purpose Hall',
        assistanceDetails: 'We need to reserve the barangay hall for 6 hours, including setup time. We will handle our own catering and decorations. Just need the venue, tables, and chairs.',
        age: '50',
        sex: 'Male',
        civilStatus: 'Married',
        address: 'Phase 3 Package 1, Barangay Commonwealth'
    },
    {
        id: 2,
        name: 'Ana Reyes',
        profileImg: '/assets/PROFILE PIC 3.jpg',
        assistanceType: 'Equipment Assistance',
        referenceCode: 'EA-2025-002',
        date: '09/27/2025',
        dateObj: new Date('2025-09-27'),
        eventName: 'Youth Leadership Summit',
        eventDescription: 'Organizing a leadership training seminar for youth in our barangay. This is an urgent program to empower our youth with leadership skills and community awareness.',
        eventDate: 'October 8, 2025',
        eventTime: '1:00 PM - 5:00 PM',
        expectedAttendees: '50 youth participants',
        contact: '09456123789',
        venue: 'Barangay Hall Conference Room',
        assistanceDetails: 'We need to borrow the barangay projector, sound system, and microphones for the presentations. We also need 50 chairs and tables for participants.',
        age: '32',
        sex: 'Female',
        civilStatus: 'Single',
        address: 'Phase 2 Package 7, Barangay Commonwealth'
    },
    {
        id: 3,
        name: 'Carlos Mendoza',
        profileImg: '/assets/PROFILE PIC.jpg',
        assistanceType: 'Personnel Assistance',
        referenceCode: 'EA-2025-003',
        date: '09/26/2025',
        dateObj: new Date('2025-09-26'),
        eventName: 'Senior Citizens Monthly Gathering',
        eventDescription: 'Regular monthly gathering for senior citizens featuring health talks, entertainment, and fellowship. Need assistance with coordination and setup.',
        eventDate: 'October 12, 2025',
        eventTime: '9:00 AM - 12:00 PM',
        expectedAttendees: '60-70 seniors',
        contact: '09123456789',
        venue: 'Barangay Senior Citizens Center',
        assistanceDetails: 'We need 2-3 barangay staff to help with registration, setup of chairs and tables, and distribution of snacks. Also requesting a barangay health worker to conduct blood pressure monitoring.',
        age: '40',
        sex: 'Male',
        civilStatus: 'Married',
        address: 'Phase 1 Package 2, Barangay Commonwealth'
    }
])

const filteredRequests = computed(() => {
    let filtered = [...requests.value]

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) ||
            item.assistanceType.toLowerCase().includes(query) ||
            item.eventName.toLowerCase().includes(query) ||
            item.referenceCode.toLowerCase().includes(query)
        )
    }

    if (filterOption.value !== 'all') {
        filtered = filtered.filter(item => 
            item.assistanceType.toLowerCase().includes(filterOption.value.toLowerCase())
        )
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => b.dateObj - a.dateObj)
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => a.dateObj - b.dateObj)
    } else if (sortOption.value === 'relevant') {
        filtered.sort((a, b) => {
            const urgentKeywords = ['urgent', 'asap', 'soonest', 'deadline', 'immediately']
            const aUrgent = urgentKeywords.some(keyword => 
                a.eventDescription.toLowerCase().includes(keyword)
            )
            const bUrgent = urgentKeywords.some(keyword => 
                b.eventDescription.toLowerCase().includes(keyword)
            )
            if (aUrgent && !bUrgent) return -1
            if (!aUrgent && bUrgent) return 1
            return b.dateObj - a.dateObj
        })
    }

    return filtered
})

const toggleSettings = () => showSettings.value = !showSettings.value
const closeSettings = () => showSettings.value = false
const toggleSortDropdown = () => {
    showSortDropdown.value = !showSortDropdown.value
    showFilterDropdown.value = false
}
const toggleFilterDropdown = () => {
    showFilterDropdown.value = !showFilterDropdown.value
    showSortDropdown.value = false
}
const selectSort = (option) => {
    sortOption.value = option
    showSortDropdown.value = false
}
const selectFilter = (option) => {
    filterOption.value = option
    showFilterDropdown.value = false
}
const logout = () => {
    showSettings.value = false
    router.visit(route('login_approver'))
}
const performSearch = () => console.log('Performing search:', searchQuery.value)

const openModal = (request) => {
    selectedRequest.value = request
    comment.value = ''
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedRequest.value = null
    comment.value = ''
}

const openApprovalModal = () => {
    isModalOpen.value = false
    isApprovalModalOpen.value = true
    const nextWeek = new Date()
    nextWeek.setDate(nextWeek.getDate() + 7)
    approvalDetails.value.releaseDate = nextWeek.toISOString().split('T')[0]
    approvalDetails.value.assistanceType = selectedRequest.value.assistanceType
    approvalDetails.value.claimLocation = 'Barangay Hall'
}

const closeApprovalModal = () => {
    isApprovalModalOpen.value = false
    approvalDetails.value = {
        assistanceType: '',
        assistanceAmount: '',
        releaseDate: '',
        claimLocation: '',
        contactPerson: '',
        contactNumber: '',
        instructions: ''
    }
    isModalOpen.value = true
}

const confirmApproval = () => {
    if (!approvalDetails.value.assistanceType || !approvalDetails.value.assistanceAmount || 
        !approvalDetails.value.releaseDate || !approvalDetails.value.claimLocation ||
        !approvalDetails.value.contactPerson || !approvalDetails.value.contactNumber) {
        alert('Please fill in all required fields')
        return
    }
    
    const index = requests.value.findIndex(r => r.id === selectedRequest.value.id)
    if (index > -1) requests.value.splice(index, 1)
    
    isApprovalModalOpen.value = false
    selectedRequest.value = null
    comment.value = ''
    approvalDetails.value = {
        assistanceType: '',
        assistanceAmount: '',
        releaseDate: '',
        claimLocation: '',
        contactPerson: '',
        contactNumber: '',
        instructions: ''
    }
}

const openRejectionModal = () => {
    isModalOpen.value = false
    isRejectionModalOpen.value = true
}

const closeRejectionModal = () => {
    isRejectionModalOpen.value = false
    rejectionReason.value = ''
    isModalOpen.value = true
}

const confirmRejection = () => {
    if (!rejectionReason.value.trim()) {
        alert('Please provide a reason for rejection')
        return
    }
    
    const index = requests.value.findIndex(r => r.id === selectedRequest.value.id)
    if (index > -1) requests.value.splice(index, 1)
    
    isRejectionModalOpen.value = false
    selectedRequest.value = null
    rejectionReason.value = ''
}

const navigateToDocumentRequest = () => router.visit(route('document_request_approver'))
const navigateToHistory = () => router.visit(route('history_approver'))

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) showSettings.value = false
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showFilterDropdown.value = false
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
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

.header-bar {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    color: white;
    padding: 5px 0;
    box-shadow: 0 4px 15px rgba(35, 150, 64, 0.3);
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
    background: #e8f8ed;
    color: #239640;
}

.settings-item:last-child {
    border-bottom: none;
}

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
    background: #e8f8ed;
    transform: translateX(3px);
}

.nav-item.active {
    background: linear-gradient(135deg, #e8f8ed, #d4f1dd);
    color: #239640;
    font-weight: 600;
    border-left: 4px solid #239640;
}

.content-area {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    flex: 1;
    border: 1px solid rgba(0,0,0,0.05);
}

.users-header {
    background: #ff8c42;
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.users-title h2 {
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

.filter-section {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    }

.filter-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.filter-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.filter-dropdown-wrapper {
    position: relative;
}

.filter-dropdown-btn {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    background: white;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 120px;
    justify-content: space-between;
}

.filter-dropdown-btn:hover {
    border-color: #239640;
}

.filter-arrow {
    font-size: 10px;
    transition: transform 0.3s ease;
}

.filter-arrow.rotated {
    transform: rotate(180deg);
}

.filter-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 180px;
    z-index: 1000;
    margin-top: 5px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.1);
}

.filter-dropdown-menu button {
    display: block;
    width: 100%;
    padding: 10px 15px;
    background: none;
    border: none;
    text-align: left;
    color: #333;
    cursor: pointer;
    transition: background 0.2s;
    font-weight: 500;
    font-size: 12px;
}

.filter-dropdown-menu button:hover {
    background: #e8f8ed;
}

.filter-dropdown-menu button.active {
    background: #e8f8ed;
    color: #239640;
    font-weight: 600;
}

.filter-right {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-container {
    display: flex;
    gap: 5px;
    background: white;
    border-radius: 8px;
    padding: 2px;
    border: 1px solid #ddd;
}

.search-input {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    width: 220px;
    font-size: 12px;
    outline: none;
}

.search-btn {
    background: #f8f9fa;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
}

.search-btn:hover {
    background: #e9ecef;
}

.requests-container {
    padding: 25px;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.request-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.request-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.request-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.request-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.request-avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.request-info {
    flex: 1;
}

.request-name {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.request-doc-type {
    font-size: 14px;
    color: #239640;
    font-weight: 600;
    margin: 3px 0;
}

.request-ref-code {
    font-size: 12px;
    color: #999;
    margin: 3px 0;
    font-family: monospace;
}

.request-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.request-date {
    font-size: 13px;
    color: #999;
    margin: 0;
}

.view-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 122, 40, 0.3);
}

.view-btn:hover {
    background: #239640;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 122, 40, 0.4);
}

.no-requests {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.no-requests p {
    font-size: 16px;
    color: #999;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(4px);
}

.modal-container {
    background: white;
    border-radius: 20px;
    padding: 30px;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.approval-modal, .rejection-modal {
    max-width: 600px;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}


.modal-content {
    margin-top: 10px;
}

.modal-top {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr;
    gap: 20px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 25px;
}

.modal-user-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.modal-avatar {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.modal-name {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.modal-label {
    font-size: 13px;
    color: #666;
    margin: 0 0 3px 0;
}

.modal-ref {
    font-size: 12px;
    color: #999;
    font-family: monospace;
    margin: 0;
}

.modal-doc-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.modal-doc-type {
    font-size: 18px;
    font-weight: 700;
    color: #239640;
    margin: 0 0 8px 0;
}

.modal-purpose {
    font-size: 13px;
    color: #666;
    margin: 0;
}

.modal-upload-section {
    display: flex;
    align-items: left;
}

.upload-btn {
    background: #ff7a28;;
    color: white;
    border: none;
    padding: 5px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.modal-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-description {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #239640;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
}

.detail-item {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 10px;
}

.detail-item-full {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 10px;
}

.detail-label {
    font-size: 12px;
    font-weight: 700;
    color: #666;
    margin: 0 0 5px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 14px;
    color: #333;
    font-weight: 600;
    margin: 0;
}

.comment-section {
    margin-top: 5px;
}

.comment-textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    transition: border-color 0.3s;
}

.comment-textarea:focus {
    outline: none;
    border-color: #239640;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 10px;
}

.approve-btn {
    background: #239640;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
}

.approve-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
}

.reject-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.approval-title {
    font-size: 24px;
    font-weight: 700;
    color: #239640;
    margin: 0 0 10px 0;
    text-align: center;
}

.approval-subtitle {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin: 0 0 30px 0;
}

.approval-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: #239640;
}

.form-textarea {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    transition: border-color 0.3s;
}

.form-textarea:focus {
    outline: none;
    border-color: #239640;
}

.approval-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.confirm-btn {
    flex: 1;
    background: #239640;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(35, 150, 64, 0.3);
}

.confirm-btn:hover {
    background: #1e7d35;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(35, 150, 64, 0.4);
}

.cancel-btn {
    flex: 1;
    background: #6b7280;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.cancel-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
}

.rejection-title {
    font-size: 24px;
    font-weight: 700;
    color: #ef4444;
    margin: 0 0 10px 0;
    text-align: center;
}

.rejection-subtitle {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin: 0 0 30px 0;
}

.rejection-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.rejection-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.confirm-reject-btn {
    flex: 1;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.confirm-reject-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.requests-container::-webkit-scrollbar,
.modal-container::-webkit-scrollbar {
    width: 6px;
}


@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }
    
    .modal-top {
        grid-template-columns: 1fr;
    }
    
    .details-grid {
        grid-template-columns: repeat(2, 1fr);
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
    
    .filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input {
        width: 100%;
    }
    
    .modal-container {
        width: 95%;
        padding: 20px;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-top {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .request-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .request-right {
        width: 100%;
        align-items: flex-start;
    }
}
</style>