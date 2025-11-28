<template>
    <Head>
        <title>Profile</title>
    </Head>

    <!-- Full Screen Layout -->
    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <!-- Settings Dropdown -->
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
                        <Link href="#" class="settings-item" @click="closeSettings">Terms & Conditions</Link>
                        <Link href="#" class="settings-item" @click="logout">Sign Out</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card-sidebar">
                    <img src="/assets/DEFAULT.jpg" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'posts' }"
                        @click="navigateToPosts"
                    >
                        📋 Posts
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'documents' }"
                        @click="navigateToDocuments"
                    >
                        📄 Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'events' }"
                        @click="navigateToEvents"
                    >
                        🤝 Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'notifications' }"
                        @click="navigateToNotifications"
                    >
                        🔔 Notifications
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item active"
                        :class="{ active: activeTab === 'profile' }"
                        @click="navigateToProfile"
                    >
                        👤 Profile
                    </Link>
                </div>

                <button class="faq-btn" @click="openFAQ">
                    ❓ FAQs & Help Center
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-title">
                            <h2>My Profile</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Profile Content Container -->
                    <div class="profile-content-container">
                        <!-- Profile Details Card -->
                        <div class="profile-details-card">
                            <div class="profile-photo-section">
                                <img src="/assets/DEFAULT.jpg" alt="User Photo" class="profile-photo" />
                                <button class="edit-profile-btn">EDIT PROFILE</button>
                            </div>
                            <div class="profile-info-section">
                                <h3 class="profile-display-name">Jb Bolatin</h3>
                                <div class="profile-badge">RESIDENT</div>
                                <p class="profile-description">
                                    Ako po ay nakatira sa Bagong Silang, Caloocan City, at isang Barangay 176B resident. Mahilig ako makisama sa mga aktibites ng komunidad at tumutulong sa mga makabuluhang programa.
                                </p>
                                <div class="profile-stats">
                                    <div class="stat-item">
                                        <div class="stat-value">{{ userPosts.length }}</div>
                                        <div class="stat-label">Posts</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value">156</div>
                                        <div class="stat-label">Reactions</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value">42</div>
                                        <div class="stat-label">Comments</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities Section -->
                        <!-- Recent Activities Section -->
                            <div class="activities-section">
                                <div class="activities-header">
                                    <div class="activities-title">
                                        <h3>Recent Activities</h3>
                                    </div>
                                </div>



                            <!-- Filter Bar -->
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
                                            <button @click="selectSort('relevant')" :class="{ active: sortOption === 'relevant' }">RELEVANT</button>
                                            <button @click="selectSort('oldest')" :class="{ active: sortOption === 'oldest' }">OLDEST</button>
                                        </div>
                                    </div>
                                    <div class="filter-dropdown-wrapper">
                                        <button class="filter-dropdown-btn" @click="toggleFilterDropdown">
                                            {{ filterOption.toUpperCase() }}
                                            <span class="filter-arrow" :class="{ rotated: showFilterDropdown }">▼</span>
                                        </button>
                                        <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                            <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                            <button @click="selectFilter('question')" :class="{ active: filterOption === 'question' }">QUESTION</button>
                                            <button @click="selectFilter('suggestion')" :class="{ active: filterOption === 'suggestion' }">SUGGESTION</button>
                                            <button @click="selectFilter('concern')" :class="{ active: filterOption === 'concern' }">CONCERN</button>
                                            <button @click="selectFilter('environment')" :class="{ active: filterOption === 'environment' }">ENVIRONMENT</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Posts -->
                            <div class="activity-posts">
                                <div 
                                    v-for="post in filteredUserPosts" 
                                    :key="post.id"
                                    class="activity-card"
                                    @click="viewPost(post.id)"
                                >
                                    <div class="activity-header-row">
                                        <div class="activity-author">
                                            <img :src="post.avatar" alt="Author Photo" class="activity-avatar" />
                                            <div class="activity-author-info">
                                                <div class="activity-author-name">{{ post.author }}</div>
                                                <div class="activity-tags">
                                                    <span 
                                                        v-for="tag in post.tags" 
                                                        :key="tag"
                                                        class="tag"
                                                        :class="tag.toLowerCase()"
                                                    >
                                                        #{{ tag }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="activity-time">
                                            <div class="date">{{ formatDate(post.date) }}</div>
                                            <div class="time">{{ post.time }}</div>
                                        </div>
                                    </div>

                                    <div class="activity-content">
                                        <h4 class="activity-title">{{ post.title }}</h4>
                                        <p class="activity-text">{{ post.content }}</p>
                                    </div>

                                    <div class="activity-reactions">
                                        <button class="reaction-btn" @click.stop="toggleLike(post.id)" :class="{ liked: post.userLiked }">
                                            👍 {{ post.likes }}
                                        </button>
                                        <button class="reaction-btn" @click.stop="toggleDislike(post.id)" :class="{ disliked: post.userDisliked }">
                                            👎 {{ post.dislikes }}
                                        </button>
                                        <button class="comment-btn" @click.stop="viewComments(post.id)">
                                            💬 {{ post.comments }}
                                        </button>
                                    </div>
                                </div>

                                <!-- No posts message -->
                                <div v-if="filteredUserPosts.length === 0" class="no-posts">
                                    <p>No posts found matching your criteria.</p>
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
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
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
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const activeTab = ref('profile')
const sortOption = ref('newest')
const filterOption = ref('all')

// Sample user posts data
const userPosts = ref([
    {
        id: 1,
        author: 'Jb Bolatin',
        avatar: '/assets/DEFAULT.jpg',
        title: 'Mungkahi para sa Kalinisan ng Barangay',
        content: 'Magandang araw po sa ating butihing Kapitan at mga Kagawad! Ako\'y taos-pusong nagpapasalamat sa inyong suporta sa aming komunidad at nais ko po sanang iparating ang mungkahi na magkaroon ng basurahan sa bawat kanto upang mapanatili natin ang kalinisan. Maraming salamat po!',
        tags: ['ENVIRONMENT', 'SUGGESTION'],
        date: new Date('2025-06-01'),
        time: '4:00 PM',
        likes: 89,
        dislikes: 2,
        comments: 8,
        userLiked: false,
        userDisliked: false
    },
    {
        id: 2,
        author: 'Jb Bolatin',
        avatar: '/assets/DEFAULT.jpg',
        title: 'Tanong tungkol sa Medical Mission',
        content: 'Magandang araw po! Gusto ko pong magtanong kung kailan po ang susunod na Medical Mission? Marami po kasi sa amin dito sa Phase 2 ang nangangailangan ng check-up. Salamat po!',
        tags: ['QUESTION'],
        date: new Date('2025-05-28'),
        time: '2:30 PM',
        likes: 45,
        dislikes: 0,
        comments: 12,
        userLiked: false,
        userDisliked: false
    },
    {
        id: 3,
        author: 'Jb Bolatin',
        avatar: '/assets/DEFAULT.jpg',
        title: 'Concern sa Street Lighting',
        content: 'Magandang gabi po. Nais ko pong ipaalam na ang ilaw sa may kanto ng Phase 3 ay sira na po. Madilim po ang daan at medyo delikado na para sa mga residente, lalo na sa gabi. Sana po ay maaksyunan. Salamat po!',
        tags: ['CONCERN'],
        date: new Date('2025-05-25'),
        time: '7:15 PM',
        likes: 67,
        dislikes: 1,
        comments: 15,
        userLiked: false,
        userDisliked: false
    },
    {
        id: 4,
        author: 'Jb Bolatin',
        avatar: '/assets/DEFAULT.jpg',
        title: 'Pasasalamat sa Cleanup Drive',
        content: 'Salamat po sa lahat ng nag-organisa ng cleanup drive kahapon! Napakaganda po ng resulta at mas malinis na ang ating barangay. Sana po ay ipagpatuloy natin ito. Mabuhay ang Barangay 176B!',
        tags: ['ENVIRONMENT'],
        date: new Date('2025-05-20'),
        time: '10:00 AM',
        likes: 124,
        dislikes: 0,
        comments: 20,
        userLiked: false,
        userDisliked: false
    }
])

// Computed filtered posts
const filteredUserPosts = computed(() => {
    let filtered = [...userPosts.value]

    if (filterOption.value !== 'all') {
        filtered = filtered.filter(post => 
            post.tags.some(tag => tag.toLowerCase() === filterOption.value.toLowerCase())
        )
    }

    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => new Date(b.date) - new Date(a.date))
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => new Date(a.date) - new Date(b.date))
    } else if (sortOption.value === 'relevant') {
        filtered.sort((a, b) => (b.likes + b.comments) - (a.likes + a.comments))
    }

    return filtered
})

// Methods
const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

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
    router.visit(route('login'))
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToPosts = () => {
    activeTab.value = 'posts'
    router.visit(route('announcement_resident'))
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_resident'))
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_resident'))
}

const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_request_resident'))
}

const viewPost = (postId) => {
    router.visit(route('discussion_clickpost_resident', { id: postId }))
}

const viewComments = (postId) => {
    router.visit(route('discussion_clickpost_resident', { id: postId }))
}

const toggleLike = (postId) => {
    const post = userPosts.value.find(p => p.id === postId)
    if (post) {
        if (post.userLiked) {
            post.likes--
            post.userLiked = false
        } else {
            if (post.userDisliked) {
                post.dislikes--
                post.userDisliked = false
            }
            post.likes++
            post.userLiked = true
        }
    }
}

const toggleDislike = (postId) => {
    const post = userPosts.value.find(p => p.id === postId)
    if (post) {
        if (post.userDisliked) {
            post.dislikes--
            post.userDisliked = false
        } else {
            if (post.userLiked) {
                post.likes--
                post.userLiked = false
            }
            post.dislikes++
            post.userDisliked = true
        }
    }
}

const openFAQ = () => {
    router.visit(route('help_center_resident'))
}

const formatDate = (date) => {
    return date.toLocaleDateString('en-US', { 
        month: 'long', 
        day: '2-digit', 
        year: 'numeric' 
    })
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showFilterDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'profile'
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
    transition: transform 0.3s ease;
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

/* Main Layout */
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

.profile-card-sidebar {
    background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
    border-radius: 15px;
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.3);
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
    background: #239640;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
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
    color: #ff8c42;
    font-weight: 600;
    border-left: 4px solid #ff8c42;
}

.faq-btn {
    width: 100%;
    background: #ff8c42;
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
}


/* Content Area */
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

.profile-header {
background: linear-gradient(135deg, #2bb24a, #239640);
color: white;
padding: 16px 25px;
display: flex;
justify-content: space-between;
align-items: center;
position: relative;
}

.profile-title h2 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    position: relative;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

/* Profile Content Container */
.profile-content-container {
    padding: 30px 25px;
    overflow-y: auto;
}

/* Profile Details Card */
.profile-details-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 15px;
    padding: 30px;
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.profile-photo-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.profile-photo {
    width: 100%;        /* scales with parent container */
    max-width: 1000px;  /* doesn’t get too big */
    height: auto;      /* keeps aspect ratio */
    border-radius: 15px;
    object-fit: cover;
}

.edit-profile-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 10px 5px;
    border-radius: 10px;
    margin-top: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 13px;
    width: 100%;
}

.edit-profile-btn:hover {
    transform: translateY(-2px);
}

.profile-info-section {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.profile-display-name {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.profile-badge {
    font-size: 12px;
    background: #239640;
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    display: inline-block;
    width: fit-content;
    font-weight: 600;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.profile-description {
    font-size: 15px;
    line-height: 1.7;
    color: #555;
    margin: 10px 0;
}

.profile-stats {
    display: flex;
    gap: 40px;
    margin-top: 20px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #ff8c42;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
}

/* Activities Section */
.activities-section {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.activities-header {
    background: white; /* Changed from green gradient */
    color: #333; /* Changed from white to black */
    padding: 20px 25px; /* Increased padding for better spacing */
    border-bottom: 2px solid #e0e0e0; /* Add separator line */
}

.activities-title h3 {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    color: #333; /* Changed from white to black */
    text-shadow: none; /* Remove text shadow */
}

/* Remove or comment out this style since we're removing the subtitle */
.activities-subtitle {
    display: none; /* Hide the subtitle */
}

/* Filter Section */
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
    border-color: #ff8c42;
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
    min-width: 150px;
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
    background: #fff7ef;
}

.filter-dropdown-menu button.active {
    background: #fff7ef;
    color: #ff8c42;
    font-weight: 600;
}

/* Activity Posts */
.activity-posts {
    padding: 0;
}

.activity-card {
    padding: 25px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.activity-card:hover {
    background: linear-gradient(135deg, #fafbfc, #f8f9fa);
    transform: translateY(-1px);
}

.activity-card:last-child {
    border-bottom: none;
}

.activity-header-row {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.activity-author {
    display: flex;
    align-items: start;
    gap: 15px;
}

.activity-avatar {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.activity-author-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.activity-author-name {
    font-weight: 700;
    font-size: 15px;
    color: #333;
}

.activity-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.tag {
    font-size: 11px;
    padding: 5px 10px;
    border-radius: 15px;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.tag.question {
    background: linear-gradient(135deg, #ff9800, #f57c00);
}

.tag.suggestion {
    background: linear-gradient(135deg, #273cb0, #273cb0);
}

.tag.concern {
    background: linear-gradient(135deg, #ff0303, #ff0303);
}

.tag.environment {
    background: linear-gradient(135deg, #4caf50, #388e3c);
}

.activity-time {
    text-align: right;
    font-size: 12px;
    color: #666;
    font-weight: 500;
}

.date {
    font-weight: 600;
}

.time {
    color: #999;
    margin-top: 2px;
}

.activity-content {
    margin: 15px 0;
}

.activity-title {
    font-size: 17px;
    font-weight: 600;
    margin: 0 0 10px 0;
    color: #333;
    line-height: 1.4;
}

.activity-text {
    font-size: 14px;
    line-height: 1.6;
    color: #555;
    margin: 0;
}

.activity-reactions {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.reaction-btn,
.comment-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    color: #666;
    padding: 10px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.reaction-btn:hover,
.comment-btn:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    color: #333;
}

.reaction-btn.liked {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    color: white;
    box-shadow: 0 4px 15px rgba(74, 222, 128, 0.4);
}

.reaction-btn.liked:hover {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 222, 128, 0.5);
}

.reaction-btn.disliked {
    background: linear-gradient(135deg, #f87171, #ef4444);
    color: white;
    box-shadow: 0 4px 15px rgba(248, 113, 113, 0.4);
}

.reaction-btn.disliked:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(248, 113, 113, 0.5);
}


.no-posts {
    padding: 60px 40px;
    text-align: center;
    color: #666;
}

.no-posts p {
    font-size: 16px;
    color: #999;
}

/* Custom Scrollbar */
.profile-content-container::-webkit-scrollbar {
    width: 6px;
}

.profile-content-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.profile-content-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.profile-content-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Responsive */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
        margin-top: 70px;
    }
    
    .profile-details-card {
        grid-template-columns: 150px 1fr;
        gap: 20px;
        padding: 25px;
    }
    
    .profile-photo {
        width: 200px;
        height: 150px;
    }
}

@media (max-width: 768px) {
    .main-layout {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
        margin-top: 70px;
    }
    
    .sidebar {
        order: 2;
        padding-right: 0;
    }
    
    .profile-details-card {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 20px;
    }
    
    .profile-photo-section {
        justify-content: center;
    }
    
    .profile-badge {
        margin: 0 auto;
    }
    
    .profile-stats {
        justify-content: center;
    }
    
    .filter-section {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .activity-header-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .activity-time {
        text-align: left;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
        margin-top: 70px;
    }
    
    .profile-details-card {
        padding: 15px;
    }
    
    .profile-display-name {
        font-size: 22px;
    }
    
    .profile-stats {
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .activity-card {
        padding: 20px 15px;
    }
    
    .activity-reactions {
        flex-wrap: wrap;
    }
}
</style>