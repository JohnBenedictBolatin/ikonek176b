<template>
    <Head>
        <title>Announcements & Discussions</title>
    </Head>

    <!-- Full Screen Layout -->
    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/WHITE LOGO.png" alt="Logo" class="header-logo" />
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

        <!-- Main Content Area - Full Width -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img src="/assets/KAPITAN.jpg" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">Kap. Sammy Reyes</div>
                        <div class="profile-role">BARANGAY CAPTAIN</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'posts' }"
                        @click="setActiveTab('posts')"
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
                        class="nav-item"
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
                    <!-- Discussions Header with Dropdown Toggle -->
                    <div class="discussions-header">
                        <div class="discussions-title">
                            <button class="title-dropdown" @click="toggleModeDropdown">
                                <h2>{{ currentTab === 'announcements' ? 'Announcements' : 'Discussions' }}</h2>
                                <span class="dropdown-icon" :class="{ rotated: showModeDropdown }">▼</span>
                            </button>
                            <!-- Dropdown Menu -->
                            <div v-if="showModeDropdown" class="mode-dropdown">
                                <button 
                                    class="mode-option"
                                    :class="{ active: currentTab === 'announcements' }"
                                    @click="switchTab('announcements')"
                                >
                                    Announcements
                                </button>
                                <button 
                                    class="mode-option"
                                    :class="{ active: currentTab === 'discussions' }"
                                    @click="switchTab('discussions')"
                                >
                                    Discussions
                                </button>
                            </div>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Filter Bar -->
                    <div class="filter-section" v-if="!selectedPost">
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
                                    <button @click="selectFilter('medical')" :class="{ active: filterOption === 'medical' }">MEDICAL</button>
                                    <button @click="selectFilter('programs')" :class="{ active: filterOption === 'programs' }">PROGRAMS</button>
                                    <button @click="selectFilter('youth')" :class="{ active: filterOption === 'youth' }">YOUTH</button>
                                    <button @click="selectFilter('leadership')" :class="{ active: filterOption === 'leadership' }">LEADERSHIP</button>
                                    <button @click="selectFilter('environment')" :class="{ active: filterOption === 'environment' }">ENVIRONMENT</button>
                                </div>
                            </div>
                        </div>
<div class="filter-right">
    <button class="add-post-btn" @click="addPost">＋ ADD POST</button>
    <div class="search-container">
        <input 
            type="text" 
            v-model="searchQuery" 
            @input="performSearch"
            placeholder="SEARCH..." 
            class="search-input" 
        />
        <button class="search-btn" @click="performSearch">🔍</button>
    </div>
</div>
                    </div>

                    <!-- Posts Container -->
                    <div class="posts-container" v-if="!selectedPost">
                        <!-- Filtered Posts -->
                        <div 
                            v-for="post in filteredPosts" 
                            :key="post.id"
                            class="post-card"
                            @click="viewPost(post.id)"
                        >
                            <div class="post-header">
                                <img :src="post.avatar" :alt="post.author" class="post-avatar" />
                                <div class="post-meta">
                                    <div class="post-author">{{ post.author }}</div>
                                    <span class="author-badge official">{{ post.role }}</span>
                                </div>
                                <div class="post-tags">
                                    <span 
                                        v-for="tag in post.tags" 
                                        :key="tag"
                                        class="tag"
                                        :class="tag.toLowerCase()"
                                    >
                                        #{{ tag }}
                                    </span>
                                </div>
                                <div class="post-time">
                                    <div class="date">{{ formatDate(post.date) }}</div>
                                    <div class="time">{{ post.time }}</div>
                                </div>
                            </div>

                            <div class="post-content">
                                <h3 class="post-title">{{ post.title }}</h3>
                                <p class="post-text">{{ post.content }}</p>
                            </div>

                            <div class="post-actions" @click.stop>
                                <div class="reaction-buttons">
                                    <button 
                                        class="reaction-btn"
                                        :class="{ liked: post.userLiked }"
                                        @click="toggleLike(post.id)"
                                    >
                                        👍 {{ post.likes }}
                                    </button>
                                    <button 
                                        class="reaction-btn"
                                        :class="{ disliked: post.userDisliked }"
                                        @click="toggleDislike(post.id)"
                                    >
                                        👎 {{ post.dislikes }}
                                    </button>
                                    <button class="comment-btn" @click="viewComments(post.id)">
                                        💬 {{ post.comments }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- No posts message -->
                        <div v-if="filteredPosts.length === 0" class="no-posts">
                            <p>No posts found matching your criteria.</p>
                        </div>
                    </div>

                    <!-- Detailed Post View -->
                    <div class="post-detail-container" v-if="selectedPost">
                        <button class="back-btn" @click="closePost">◀ Back to Posts</button>
                        
                        <!-- Post Detail -->
                        <div class="post-detail">
                            <div class="post-header">
                                <img :src="selectedPost.avatar" :alt="selectedPost.author" class="post-avatar" />
                                <div class="post-meta">
                                    <div class="post-author">{{ selectedPost.author }}</div>
                                    <span class="author-badge official">{{ selectedPost.role }}</span>
                                </div>
                                <div class="post-tags">
                                    <span 
                                        v-for="tag in selectedPost.tags" 
                                        :key="tag"
                                        class="tag"
                                        :class="tag.toLowerCase()"
                                    >
                                        #{{ tag }}
                                    </span>
                                </div>
                                <div class="post-time">
                                    <div class="date">{{ formatDate(selectedPost.date) }}</div>
                                    <div class="time">{{ selectedPost.time }}</div>
                                </div>
                            </div>

                            <div class="post-content">
                                <h3 class="post-title">{{ selectedPost.title }}</h3>
                                <p class="post-text">{{ selectedPost.content }}</p>
                                
                                <!-- Post Images -->
                                <div v-if="selectedPost.images && selectedPost.images.length > 0" class="post-images">
                                    <img 
                                        v-for="(image, index) in selectedPost.images" 
                                        :key="index"
                                        :src="image" 
                                        :alt="`Post image ${index + 1}`"
                                        class="post-image"
                                    />
                                </div>
                            </div>

                            <div class="post-actions">
                                <div class="reaction-buttons">
                                    <button 
                                        class="reaction-btn"
                                        :class="{ liked: selectedPost.userLiked }"
                                        @click="toggleLike(selectedPost.id)"
                                    >
                                        👍 {{ selectedPost.likes }}
                                    </button>
                                    <button 
                                        class="reaction-btn"
                                        :class="{ disliked: selectedPost.userDisliked }"
                                        @click="toggleDislike(selectedPost.id)"
                                    >
                                        👎 {{ selectedPost.dislikes }}
                                    </button>
                                </div>
                                <div class="post-options">
                                    <button class="share-btn" @click="sharePost(selectedPost.id)">📤</button>
                                    <button class="report-post-btn" @click="reportPost(selectedPost.id)">🚩 Report</button>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="comments-section">
                            <h3 class="comments-title">Comments ({{ selectedPost.commentsList.length }})</h3>
                            
                            <!-- Add Comment Form -->
                            <div class="add-comment-form">
                                <img src="/assets/KAPITAN.jpg" alt="Your Profile" class="comment-avatar" />
                                <div class="comment-input-wrapper">
                                    <textarea 
                                        v-model="newComment" 
                                        placeholder="Write a comment..."
                                        class="comment-input"
                                        rows="3"
                                    ></textarea>
                                    <button @click="addComment" class="submit-comment-btn">Post Comment</button>
                                </div>
                            </div>

                            <!-- Comments List -->
                            <div class="comments-list">
                                <div 
                                    v-for="comment in selectedPost.commentsList" 
                                    :key="comment.id"
                                    class="comment-item"
                                >
                                    <img :src="comment.avatar" :alt="comment.author" class="comment-avatar" />
                                    <div class="comment-content-wrapper">
                                        <div class="comment-header">
                                            <div class="comment-author-info">
                                                <span class="comment-author">{{ comment.author }}</span>
                                                <span class="comment-date">{{ formatCommentDate(comment.date) }}</span>
                                            </div>
                                            <button class="report-btn" @click="reportComment(comment.id)">🚩 Report</button>
                                        </div>
                                        <p class="comment-text">{{ comment.text }}</p>
                                        <div class="comment-actions">
                                            <button 
                                                class="comment-reaction-btn"
                                                :class="{ liked: comment.userLiked }"
                                                @click="toggleCommentLike(comment.id)"
                                            >
                                                👍 {{ comment.likes }}
                                            </button>
                                            <button 
                                                class="comment-reaction-btn"
                                                :class="{ disliked: comment.userDisliked }"
                                                @click="toggleCommentDislike(comment.id)"
                                            >
                                                👎 {{ comment.dislikes }}
                                            </button>
                                            <button 
                                                class="reply-btn"
                                                @click="toggleReplyForm(comment.id)"
                                            >
                                                💬 Reply ({{ comment.replies.length }})
                                            </button>
                                        </div>

                                        <!-- Reply Form -->
                                        <div v-if="replyingTo === comment.id" class="reply-form">
                                            <img src="/assets/KAPITAN.jpg" alt="Your Profile" class="comment-avatar small" />
                                            <div class="reply-input-wrapper">
                                                <textarea 
                                                    v-model="newReply" 
                                                    placeholder="Write a reply..."
                                                    class="reply-input"
                                                    rows="2"
                                                ></textarea>
                                                <div class="reply-actions">
                                                    <button @click="cancelReply" class="cancel-reply-btn">Cancel</button>
                                                    <button @click="addReply(comment.id)" class="submit-reply-btn">Reply</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Replies List -->
                                        <div v-if="comment.replies.length > 0" class="replies-list">
                                            <div 
                                                v-for="reply in comment.replies" 
                                                :key="reply.id"
                                                class="reply-item"
                                            >
                                                <img :src="reply.avatar" :alt="reply.author" class="comment-avatar small" />
                                                <div class="reply-content-wrapper">
                                                    <div class="comment-header">
                                                        <div class="comment-author-info">
                                                            <span class="comment-author">{{ reply.author }}</span>
                                                            <span class="comment-date">{{ formatCommentDate(reply.date) }}</span>
                                                        </div>
                                                        <button class="report-btn" @click="reportComment(reply.id)">🚩</button>
                                                    </div>
                                                    <p class="comment-text">{{ reply.text }}</p>
                                                    <div class="comment-actions">
                                                        <button 
                                                            class="comment-reaction-btn"
                                                            :class="{ liked: reply.userLiked }"
                                                            @click="toggleReplyLike(comment.id, reply.id)"
                                                        >
                                                            👍 {{ reply.likes }}
                                                        </button>
                                                        <button 
                                                            class="comment-reaction-btn"
                                                            :class="{ disliked: reply.userDisliked }"
                                                            @click="toggleReplyDislike(comment.id, reply.id)"
                                                        >
                                                            👎 {{ reply.dislikes }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Modal -->
                    <div v-if="showReportModal" class="report-modal-overlay" @click="closeReportModal">
                        <div class="report-modal" @click.stop>
                            <div class="report-modal-header">
                                <h3>Report {{ reportType }}</h3>
                                <button class="close-modal-btn" @click="closeReportModal">✕</button>
                            </div>
                            <div class="report-modal-body">
                                <p class="report-description">Please select the reason for reporting this {{ reportType.toLowerCase() }}:</p>
                                <div class="report-options">
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="spam" v-model="reportReason" />
                                        <span>Spam or misleading</span>
                                    </label>
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="harassment" v-model="reportReason" />
                                        <span>Harassment or bullying</span>
                                    </label>
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="hate" v-model="reportReason" />
                                        <span>Hate speech</span>
                                    </label>
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="violence" v-model="reportReason" />
                                        <span>Violence or dangerous content</span>
                                    </label>
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="inappropriate" v-model="reportReason" />
                                        <span>Inappropriate content</span>
                                    </label>
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="false" v-model="reportReason" />
                                        <span>False information</span>
                                    </label>
                                    <label class="report-option">
                                        <input type="radio" name="reportReason" value="other" v-model="reportReason" />
                                        <span>Other</span>
                                    </label>
                                </div>
                                <textarea 
                                    v-if="reportReason === 'other'"
                                    v-model="reportDetails"
                                    placeholder="Please provide additional details..."
                                    class="report-details-input"
                                    rows="4"
                                ></textarea>
                            </div>
                            <div class="report-modal-footer">
                                <button class="cancel-report-btn" @click="closeReportModal">Cancel</button>
                                <button class="submit-report-btn" @click="submitReport" :disabled="!reportReason">Submit Report</button>
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
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Reactive data
const showSettings = ref(false)
const showModeDropdown = ref(false)
const showSortDropdown = ref(false)
const showFilterDropdown = ref(false)
const activeTab = ref('posts')
const currentTab = ref('announcements')
const sortOption = ref('newest')
const filterOption = ref('all')
const searchQuery = ref('')
const selectedPost = ref(null)
const newComment = ref('')
const newReply = ref('')
const replyingTo = ref(null)
const showReportModal = ref(false)
const reportType = ref('')
const reportReason = ref('')
const reportDetails = ref('')
const reportTargetId = ref(null)

const navigateToProfile = () => {
    activeTab.value = 'profile'
    router.visit(route('profile_employee'))
}
const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_employee'))  // ✅ Correct
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    router.visit(route('event_assistance_employee'))
}
const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    router.visit(route('notification_request_employee'))
}
// Sample posts data
const posts = ref([
    {
        id: 1,
        author: 'Kap. Sammy DG Reyes',
        role: 'BARANGAY CAPTAIN',
        avatar: '/assets/KAPITAN.jpg',
        title: '🌿✨ Barangay 176B Cleanup Drive ✨🌿',
        content: 'Sama-sama nating inalagaan ang ating komunidad! Barangay 176B successfully conducted a cleanup drive sa ilang parte ng ating barangay upang mapanatili ang kalinisan at kaayusan. 💪🧹 Maraming salamat sa lahat ng nakibahagi at nagbigay ng oras at effort—tunay na ipinakita ninyo ang diwa ng bayanihan. 💚 Tuloy-tuloy lang tayo sa pagkilos para sa mas malinis, ligtas, at maayos na Barangay 176B! 🌎✨ #Barangay176B #CleanupDrive #BayanihanSpirit',
        tags: ['ENVIRONMENT', 'PROGRAMS'],
        date: new Date('2025-06-01'),
        time: '11:22 AM',
        likes: 521,
        dislikes: 0,
        comments: 25,
        userLiked: false,
        userDisliked: false,
        images: ['/assets/KAP POST 1.jpg', '/assets/KAP POST 2.jpg', '/assets/KAP POST 3.jpg'],
        commentsList: [
            {
                id: 1,
                author: 'Maria Santos',
                avatar: '/assets/PROFILE PIC 6.jpg',
                text: 'Salamat po! Mas malinis na ang mga kalye sa barangay natin. More power to Barangay 176B! 💚',
                date: new Date('2025-06-01T14:30:00'),
                likes: 45,
                dislikes: 0,
                userLiked: false,
                userDisliked: false,
                replies: [
                    {
                        id: 1,
                        author: 'Juan Dela Cruz',
                        avatar: '/assets/PROFILE PIC 7.jpg',
                        text: 'Agreed! Dapat mas dumalas ang mga ganito!',
                        date: new Date('2025-06-01T15:00:00'),
                        likes: 12,
                        dislikes: 0,
                        userLiked: false,
                        userDisliked: false
                    }
                ]
            },
            {
                id: 2,
                author: 'Manuelo Reyes III',
                avatar: '/assets/PROFILE PIC 2.jpg',
                text: 'Great initiative! Looking forward to the next cleanup drive.',
                date: new Date('2025-06-02T09:15:00'),
                likes: 28,
                dislikes: 0,
                userLiked: false,
                userDisliked: false,
                replies: []
            }
        ]
    },
    {
        id: 2,
        author: 'Hon. Hero Delos Santos',
        role: 'Barangay SK Chairperson',
        avatar: '/assets/SK.jpg',
        title: 'Hello, Kabataan ng Barangay 176B! 🙌 Magkakaroon tayo ng Leadership Seminar sa June 16 sa Phase 2 Covered Court. 🌟',
        content: 'Layunin nito na mabigyan tayo ng pagkakataon to learn, grow, and build stronger connections as one youth community. 💪🌟 Iniimbitahan ko ang lahat ng kabataan na maging parte ng program na ito. Sama-sama tayong kikilos para sa mas makulay, mas aktibo, at mas progresibong Barangay 176B! Kita-kits, mga ka-Barangay! 💚 #SK176B #KabataanNgayon #ParaSaKabataan',
        tags: ['LEADERSHIP', 'PROGRAMS', 'YOUTH'],
        date: new Date('2025-06-8'),
        time: '3:11 PM',
        likes: 15,
        dislikes: 2,
        comments: 8,
        userLiked: false,
        userDisliked: false,
        images: ['/assets/SK POST 1.jpg', '/assets/SK POST 2.jpg', '/assets/SK POST 3.jpg'],
        commentsList: [
            {
                id: 1,
                author: 'Anna Mae Buenavente',
                avatar: '/assets/PROFILE PIC 5.jpg',
                text: 'Excited for this! Aattend ako for sure! 🎉',
                date: new Date('2025-06-08T16:00:00'),
                likes: 8,
                dislikes: 0,
                userLiked: false,
                userDisliked: false,
                replies: []
            }
        ]
    },
    {
        id: 3,
        author: 'Hon. Jun Paunil Jr.',
        role: 'Barangay Kagawad',
        avatar: '/assets/KAGAWAD.jpg',
        title: 'Magandang araw, Barangay 176B! 🌟 Kami po ay magsasagawa ng Medical and Dental Mission sa darating na May 8 sa Phase 3 Court.',
        content: 'Layon ng programang ito na magbigay ng libreng check-up, dental services, at basic medicines para sa ating mga residente. 💊✨ Inaanyayahan po namin ang lahat na makiisa at samantalahin ang serbisyong handog ng ating barangay. Sama-sama po nating isulong ang kalusugan ng bawat pamilyang 176B! 💚 #Barangay176B #KagawadInAction #MedicalMission',
        tags: ['MEDICAL', 'PROGRAMS'],
        date: new Date('2025-04-30'),
        time: '10:00 AM',
        likes: 45,
        dislikes: 0,
        comments: 12,
        userLiked: false,
        userDisliked: false,
        images: ['/assets/KAG POST 1.jpg', '/assets/KAG POST 2.jpg', '/assets/KAG POST 3.jpg'],
        commentsList: [
            {
                id: 1,
                author: 'Liza Garcia',
                avatar: '/assets/PROFILE PIC 8.jpg',
                text: 'Thank you for this initiative! This will really help our community.',
                date: new Date('2025-04-30T11:30:00'),
                likes: 15,
                dislikes: 0,
                userLiked: false,
                userDisliked: false,
                replies: []
            }
        ]
    }
])

// Computed filtered posts
const filteredPosts = computed(() => {
    let filtered = [...posts.value]

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(post => 
            post.title.toLowerCase().includes(query) ||
            post.content.toLowerCase().includes(query) ||
            post.author.toLowerCase().includes(query)
        )
    }

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

const toggleModeDropdown = () => {
    showModeDropdown.value = !showModeDropdown.value
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
    applySorting()
}

const selectFilter = (option) => {
    filterOption.value = option
    showFilterDropdown.value = false
    applyFilter()
}

const logout = () => {
    showSettings.value = false
    router.visit(route('login'))
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const switchTab = (tab) => {
    currentTab.value = tab
    showModeDropdown.value = false
    if (tab === 'discussions') {
        router.visit(route('discussion_employee'))
    } else {
        router.visit(route('announcement_employee'))
    }
}

const applySorting = () => {
    console.log('Applied sorting:', sortOption.value)
}

const applyFilter = () => {
    console.log('Applied filter:', filterOption.value)
}

const performSearch = () => {
    console.log('Performing search:', searchQuery.value)
}

const addPost = () => {
    router.visit(route('discussion_addpost_employee'))
}

const viewPost = (postId) => {
    const post = posts.value.find(p => p.id === postId)
    if (post) {
        selectedPost.value = { ...post }
    }
}

const closePost = () => {
    selectedPost.value = null
    newComment.value = ''
    newReply.value = ''
    replyingTo.value = null
}

const addComment = () => {
    if (newComment.value.trim() && selectedPost.value) {
        const comment = {
            id: selectedPost.value.commentsList.length + 1,
            author: 'Kap. Sammy Reyes',
            avatar: '/assets/KAPITAN.jpg',
            text: newComment.value,
            date: new Date(),
            likes: 0,
            dislikes: 0,
            userLiked: false,
            userDisliked: false,
            replies: []
        }
        selectedPost.value.commentsList.push(comment)
        
        // Update the original post in the posts array
        const originalPost = posts.value.find(p => p.id === selectedPost.value.id)
        if (originalPost) {
            originalPost.comments++
        }
        
        newComment.value = ''
    }
}

const toggleReplyForm = (commentId) => {
    if (replyingTo.value === commentId) {
        replyingTo.value = null
        newReply.value = ''
    } else {
        replyingTo.value = commentId
        newReply.value = ''
    }
}

const cancelReply = () => {
    replyingTo.value = null
    newReply.value = ''
}

const addReply = (commentId) => {
    if (newReply.value.trim() && selectedPost.value) {
        const comment = selectedPost.value.commentsList.find(c => c.id === commentId)
        if (comment) {
            const reply = {
                id: comment.replies.length + 1,
                author: 'Kap. Sammy Reyes',
                avatar: '/assets/KAPITAN.jpg',
                text: newReply.value,
                date: new Date(),
                likes: 0,
                dislikes: 0,
                userLiked: false,
                userDisliked: false
            }
            comment.replies.push(reply)
        }
        newReply.value = ''
        replyingTo.value = null
    }
}

const toggleCommentLike = (commentId) => {
    if (selectedPost.value) {
        const comment = selectedPost.value.commentsList.find(c => c.id === commentId)
        if (comment) {
            if (comment.userLiked) {
                comment.likes--
                comment.userLiked = false
            } else {
                if (comment.userDisliked) {
                    comment.dislikes--
                    comment.userDisliked = false
                }
                comment.likes++
                comment.userLiked = true
            }
        }
    }
}

const toggleCommentDislike = (commentId) => {
    if (selectedPost.value) {
        const comment = selectedPost.value.commentsList.find(c => c.id === commentId)
        if (comment) {
            if (comment.userDisliked) {
                comment.dislikes--
                comment.userDisliked = false
            } else {
                if (comment.userLiked) {
                    comment.likes--
                    comment.userLiked = false
                }
                comment.dislikes++
                comment.userDisliked = true
            }
        }
    }
}

const toggleReplyLike = (commentId, replyId) => {
    if (selectedPost.value) {
        const comment = selectedPost.value.commentsList.find(c => c.id === commentId)
        if (comment) {
            const reply = comment.replies.find(r => r.id === replyId)
            if (reply) {
                if (reply.userLiked) {
                    reply.likes--
                    reply.userLiked = false
                } else {
                    if (reply.userDisliked) {
                        reply.dislikes--
                        reply.userDisliked = false
                    }
                    reply.likes++
                    reply.userLiked = true
                }
            }
        }
    }
}

const toggleReplyDislike = (commentId, replyId) => {
    if (selectedPost.value) {
        const comment = selectedPost.value.commentsList.find(c => c.id === commentId)
        if (comment) {
            const reply = comment.replies.find(r => r.id === replyId)
            if (reply) {
                if (reply.userDisliked) {
                    reply.dislikes--
                    reply.userDisliked = false
                } else {
                    if (reply.userLiked) {
                        reply.likes--
                        reply.userLiked = false
                    }
                    reply.dislikes++
                    reply.userDisliked = true
                }
            }
        }
    }
}

const reportPost = (postId) => {
    reportType.value = 'Post'
    reportTargetId.value = postId
    showReportModal.value = true
}

const reportComment = (commentId) => {
    reportType.value = 'Comment'
    reportTargetId.value = commentId
    showReportModal.value = true
}

const closeReportModal = () => {
    showReportModal.value = false
    reportType.value = ''
    reportReason.value = ''
    reportDetails.value = ''
    reportTargetId.value = null
}

const submitReport = () => {
    if (reportReason.value) {
        alert(`Report submitted successfully. Thank you for helping keep our community safe.\n\nType: ${reportType.value}\nReason: ${reportReason.value}${reportReason.value === 'other' ? '\nDetails: ' + reportDetails.value : ''}`)
        closeReportModal()
    }
}

const formatCommentDate = (date) => {
    const now = new Date()
    const diff = now - date
    const minutes = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)
    const days = Math.floor(diff / 86400000)
    
    if (minutes < 1) return 'Just now'
    if (minutes < 60) return `${minutes}m ago`
    if (hours < 24) return `${hours}h ago`
    if (days < 7) return `${days}d ago`
    
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    })
}

const toggleLike = (postId) => {
    const post = posts.value.find(p => p.id === postId)
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
    const post = posts.value.find(p => p.id === postId)
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

const viewComments = (postId) => {
    router.visit(route('announcement_clickpost_employee', { id: postId }))
}

const sharePost = (postId) => {
    const url = window.location.origin + route('announcement_clickpost_employee', { id: postId })
    navigator.clipboard.writeText(url)
    alert('Post link copied to clipboard!')
}

const showMoreOptions = (postId) => {
    console.log('Show more options for post:', postId)
}

const openFAQ = () => {
    router.visit(route('help_center_employee'))
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
    if (!event.target.closest('.discussions-title')) {
        showModeDropdown.value = false
    }
    if (!event.target.closest('.filter-dropdown-wrapper')) {
        showSortDropdown.value = false
        showFilterDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'posts'
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

/* Orange Header Bar - Similar to login/register pages */
.header-bar {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
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

.logo-text {
    font-size: 20px;
    font-weight: 700;
    color: white;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.header-actions {
    position: relative;
}

.settings-btn {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}
.settings-btn-img {
    margin-right: 30px; /* adjust value as needed */
    width: 30px;
}

.settings-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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

/* Sidebar - Enhanced styling */
.sidebar {
    background: transparent;
    padding-right: 20px;
}

.profile-card {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
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
    backdrop-filter: blur(10px);
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

/* Content Area - Enhanced styling */
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

.discussions-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 8px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.discussions-title {
    position: relative;
}

.title-dropdown {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background 0.2s;
}

.title-dropdown:hover {
    background: rgba(255,255,255,0.1);
}

.title-dropdown h2 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.dropdown-icon {
    font-size: 14px;
    transition: transform 0.3s ease;
}

.dropdown-icon.rotated {
    transform: rotate(180deg);
}

.mode-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 180px;
    z-index: 1000;
    margin-top: 8px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.1);
}

.mode-option {
    display: block;
    width: 100%;
    padding: 12px 16px;
    background: none;
    border: none;
    text-align: left;
    color: #333;
    cursor: pointer;
    transition: background 0.2s;
    font-weight: 500;
}

.mode-option:hover {
    background: #fff7ef;
}

.mode-option.active {
    background: #fff7ef;
    color: #ff8c42;
    font-weight: 600;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

/* Filter Section - Enhanced styling */
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

.filter-select {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    background: white;
    transition: border-color 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: #ff8c42;
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

.filter-right {
    display: flex;
    gap: 15px;
    align-items: center;
}

.add-post-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.add-post-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
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

/* Posts Container - Enhanced styling */
.posts-container {
    padding: 0;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.post-card {
    border-bottom: 1px solid #f0f0f0;
    padding: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.post-card:hover {
    background: linear-gradient(135deg, #fafbfc, #f8f9fa);
    transform: translateY(-1px);
}

.post-card:last-child {
    border-bottom: none;
}

.post-header {
    display: grid;
    grid-template-columns: 55px 1fr auto auto;
    gap: 15px;
    align-items: start;
    margin-bottom: 15px;
}

.post-avatar {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.post-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.post-author {
    font-weight: 700;
    font-size: 15px;
    color: #333;
}

.author-badge {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-block;
    width: fit-content;
    text-transform: uppercase;
}

.author-badge.official {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

.post-tags {
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
.tag.medical{
    background: linear-gradient(135deg, #ff0303, #ff0303);
}
.tag.programs {
    background: linear-gradient(135deg, #273cb0, #273cb0);
}
.tag.youth {
    background: linear-gradient(135deg, #c6077d, #c6077d);
}

.tag.leadership {
    background: linear-gradient(135deg, #9f01ed, #9f01ed);
}
.tag.question {
    background: linear-gradient(135deg, #ff9800, #f57c00);
}

.tag.environment {
    background: linear-gradient(135deg, #4caf50, #388e3c);
}

.post-time {
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

.post-content {
    margin: 15px 0;
}

.post-title {
    font-size: 17px;
    font-weight: 600;
    margin: 0 0 10px 0;
    color: #333;
    line-height: 1.4;
}

.post-text {
    font-size: 14px;
    line-height: 1.6;
    color: #555;
    margin: 0;
}

.post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.reaction-buttons {
    display: flex;
    gap: 10px;
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

.comment-btn {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.comment-btn:hover {
    background: rgba(59, 130, 246, 0.15);
    color: #2563eb;
    border-color: rgba(59, 130, 246, 0.3);
    transform: translateY(-2px);
}

.post-options {
    display: flex;
    gap: 10px;
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
.posts-container::-webkit-scrollbar {
    width: 6px;
}

.posts-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.posts-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.posts-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Post Detail View Styles */
.post-detail-container {
    padding: 20px 25px;
    max-height: calc(100vh - 350px);
    overflow-y: auto;
}

.back-btn {
    color: #ff7a28;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.back-btn:hover {
    transform: translateY(-2px);
}

.post-detail {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/* Comments Section */
.comments-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.comments-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #333;
}

/* Add Comment Form */
.add-comment-form {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.comment-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    flex-shrink: 0;
}

.comment-avatar.small {
    width: 35px;
    height: 35px;
}

.comment-input-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.comment-input,
.reply-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    resize: vertical;
    transition: border-color 0.2s;
}

.comment-input:focus,
.reply-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.submit-comment-btn {
    align-self: flex-end;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-comment-btn:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #e6763a, #e66b25);
}

/* Comments List */
.comments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.comment-item {
    display: flex;
    gap: 15px;
    padding: 20px;
    background: #fafbfc;
    border-radius: 12px;
    transition: background 0.2s;
}

.comment-item:hover {
    background: #f5f6f7;
}

.comment-content-wrapper {
    flex: 1;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.comment-author-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.comment-author {
    font-weight: 700;
    font-size: 14px;
    color: #333;
}

.comment-date {
    font-size: 12px;
    color: #999;
}

.report-btn {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 12px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.2s;
    font-weight: 600;
}

.report-btn:hover {
    background: rgba(220, 53, 69, 0.1);
}

.comment-text {
    font-size: 14px;
    line-height: 1.6;
    color: #555;
    margin-bottom: 12px;
}

/* Comment Actions */
.comment-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.comment-reaction-btn,
.reply-btn {
    background: white;
    border: 1px solid #e0e0e0;
    color: #666;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.comment-reaction-btn:hover,
.reply-btn:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
}

.comment-reaction-btn.liked {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    color: white;
    border-color: transparent;
}

.comment-reaction-btn.disliked {
    background: linear-gradient(135deg, #f87171, #ef4444);
    color: white;
    border-color: transparent;
}

/* Reply Form */
.reply-form {
    display: flex;
    gap: 12px;
    margin-top: 15px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e0e0e0;
}

.reply-input-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.reply-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.cancel-reply-btn {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    color: #666;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.cancel-reply-btn:hover {
    background: #e9ecef;
}

.submit-reply-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
}

.submit-reply-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}
.add-post-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.add-post-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

/* Replies List */
.replies-list {
    margin-top: 15px;
    margin-left: 50px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.reply-item {
    display: flex;
    gap: 12px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.reply-content-wrapper {
    flex: 1;
}

.post-detail-container::-webkit-scrollbar {
    width: 6px;
}

.post-detail-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.post-detail-container::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.post-detail-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Post Images */
.post-images {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 20px;
}

.post-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.3s ease;
}

.post-image:hover {
    transform: scale(1.05);
}

/* Report Button for Posts */
.report-post-btn {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #dc3545;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    padding: 8px 14px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.report-post-btn:hover {
    background: rgba(220, 53, 69, 0.1);
    transform: translateY(-1px);
}

/* Report Modal */
.report-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(4px);
}

.report-modal {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.report-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
}

.report-modal-header h3 {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.close-modal-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.close-modal-btn:hover {
    background: #f8f9fa;
    color: #333;
}

.report-modal-body {
    padding: 25px;
}

.report-description {
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
}

.report-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.report-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.report-option:hover {
    background: #f8f9fa;
    border-color: #ff8c42;
}

.report-option input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff8c42;
}

.report-option span {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.report-details-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    resize: vertical;
    margin-top: 15px;
    transition: border-color 0.2s;
}

.report-details-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.report-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 25px;
    border-top: 1px solid #e9ecef;
}

.cancel-report-btn {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    color: #666;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.cancel-report-btn:hover {
    background: #e9ecef;
}

.submit-report-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-report-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #c82333, #bd2130);
}

.submit-report-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
        margin-top: 70px;
    }
    
    .header-content {
        padding: 0 25px;
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
    
    .header-content {
        padding: 0 20px;
    }
    
    .filter-section {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .filter-right {
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .search-input {
        width: 100%;
        min-width: 200px;
    }
    
    .post-header {
        grid-template-columns: 50px 1fr;
        gap: 12px;
    }
    
    .post-tags,
    .post-time {
        grid-column: 1 / -1;
        margin-top: 10px;
    }
    
    .post-time {
        text-align: left;
    }
    
    .post-actions {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .reaction-buttons {
        justify-content: center;
    }
    
    .post-options {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .header-content {
        padding: 0 15px;
    }
    
    .main-layout {
        padding: 10px;
        margin-top: 70px;
    }
    
    .logo-text {
        display: none;
    }
    
    .profile-card {
        padding: 15px;
    }
    
    .nav-item {
        padding: 12px 15px;
        font-size: 14px;
    }
    
    .post-card {
        padding: 20px 15px;
    }
}
</style>