<template>
    <Head>
        <title>Create Discussion Post</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
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

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img :src="profilePictureUrl" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item active"
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
                <div class="main-content">
                    <!-- Discussions Header -->
                    <div class="discussions-header">
                        <div class="discussions-title">
                            <h2>Create Discussion Post</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Add Post Section -->
                    <div class="add-post-section">
                        <button class="back-btn" @click="backToPosts">
                            ◀ BACK TO DISCUSSIONS
                        </button>

                        <button class="add-tags-btn" @click="openTagsModal">
                            # ADD TAGS
                        </button>

                        <!-- Post Input Area -->
                        <div class="post-input-container">
                            <input 
                                v-model="postHeader"
                                type="text"
                                placeholder="Add a post header (optional)..."
                                class="post-header-input"
                                maxlength="255"
                            />
                            <textarea 
                                v-model="postContent"
                                placeholder="Write your discussion post..."
                                class="post-textarea"
                                @input="updateCharCount"
                            ></textarea>

                            <div class="post-actions">
                                <button class="attach-btn" @click="triggerFileUpload">
                                    ATTACH
                                </button>
                                <span class="char-count">{{ charCount }}/1000</span>
                                <input 
                                    type="file" 
                                    ref="fileInput" 
                                    @change="handleFileUpload" 
                                    class="file-input-hidden"
                                    accept="image/*"
                                    multiple
                                />
                            </div>

                            <!-- Uploaded Files Preview -->
                            <div v-if="uploadedFiles.length > 0" class="uploaded-files">
                                <div 
                                    v-for="(file, index) in uploadedFiles" 
                                    :key="index"
                                    class="file-preview"
                                >
                                    <img :src="file.preview" alt="Preview" class="file-preview-img" />
                                    <button class="remove-file-btn" @click="removeFile(index)">✕</button>
                                </div>
                            </div>
                        </div>

                        <!-- Tags Selection -->
                        <div class="tags-section">
                            <span class="tags-label">TAGS:</span>
                            <div class="selected-tags">
                                <span 
                                    v-for="tag in selectedTagsData" 
                                    :key="tag.tag_id"
                                    class="tag-chip"
                                    :class="getTagClass(tag.tag_name)"
                                >
                                    #{{ tag.tag_name }}
                                    <button class="remove-tag-btn" @click="removeTag(tag.tag_id)">⊖</button>
                                </span>
                                <span v-if="selectedTagsData.length === 0" class="no-tags-text">
                                    No tags selected
                                </span>
                            </div>
                        </div>

                        <!-- Publish Button -->
                        <button class="publish-btn" @click="publishPost" :disabled="form.processing">
                            {{ form.processing ? 'PUBLISHING...' : 'PUBLISH POST' }}
                        </button>

                        <!-- Drafts Section -->
                        <div class="drafts-section">
                            <h3 class="drafts-title">DRAFTS</h3>
                            
                            <div 
                                v-for="draft in drafts" 
                                :key="draft.id"
                                class="draft-card"
                            >
                                <div class="draft-content">
                                    <h4 class="draft-text">{{ draft.title }}</h4>
                                    <p class="draft-preview">{{ draft.content }}</p>
                                </div>
                                <div class="draft-footer">
                                    <span class="draft-date">{{ draft.date }}</span>
                                    <button class="delete-draft-btn" @click="deleteDraft(draft.id)">🗑</button>
                                </div>
                            </div>

                            <div v-if="drafts.length === 0" class="no-drafts">
                                <p>No drafts available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tags Modal Popup -->
        <div v-if="showTagsModal" class="modal-overlay" @click="closeTagsModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Select Tags</h3>
                    <button class="modal-close-btn" @click="closeTagsModal">✕</button>
                </div>
                <div class="modal-body">
                    <div v-if="availableTags && availableTags.length > 0" class="tags-grid">
                        <button 
                            v-for="tag in availableTags" 
                            :key="tag.tag_id"
                            class="tag-option"
                            :class="{ selected: isTagSelected(tag.tag_id) }"
                            @click="toggleTag(tag)"
                        >
                            <span class="tag-checkbox">
                                {{ isTagSelected(tag.tag_id) ? '✓' : '' }}
                            </span>
                            {{ tag.tag_name }}
                        </button>
                    </div>
                    <div v-else class="no-tags-available">
                        <p>No tags available. Please contact administrator.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="modal-cancel-btn" @click="closeTagsModal">Cancel</button>
                    <button class="modal-confirm-btn" @click="confirmTags">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    availableTags: {
        type: Array,
        default: () => []
    }
})

// Get page props
const page = usePage()

// SAFE user access - prioritize prop, fallback to page.props
const user = computed(() => {
    const authUser = page?.props?.auth?.user
    
    if (!authUser) {
        return {
            user_id: null,
            name: 'Guest',
            avatar: '/assets/DEFAULT.jpg',
            role: 'Employee',
            fk_role_id: 2
        }
    }
    
    return authUser
})

// Map of role_id -> role_name
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

// Computed display role
const displayRole = computed(() => {
    const id = user.value?.fk_role_id ?? 2
    return roleMap[id] ?? 'Employee'
})

// Profile picture URL
const profilePictureUrl = computed(() => {
    if (user.value?.profile_pic) {
        const pic = user.value.profile_pic
        // If it's a full URL, return as is
        if (pic.startsWith('http')) {
            return pic
        }
        // If it already has /storage/, return as is
        if (pic.startsWith('/storage/')) {
            return pic
        }
        // Otherwise prepend storage path
        return `/storage/${pic}`
    }
    return '/assets/DEFAULT.jpg'
})

const showSettings = ref(false)
const activeTab = ref('posts')
const postHeader = ref('')
const postContent = ref('')
const charCount = ref(0)
const selectedTagIds = ref([])
const showTagsModal = ref(false)
const uploadedFiles = ref([])
const fileInput = ref(null)

const drafts = ref([])

// computed to map selected IDs to tag objects
const selectedTagsData = computed(() => {
    if (!props.availableTags || !Array.isArray(props.availableTags)) return []
    return props.availableTags.filter(tag => selectedTagIds.value.includes(tag.tag_id))
})

const form = useForm({
    header: '',
    content: '',
    tag_ids: [],      // will be an array of tag ids
    image: null,
    video_content: null,
    is_poll: 0,
})

// Helper function to normalize tag names for CSS classes
const getTagClass = (tagName) => {
    if (!tagName) return ''
    let normalized = String(tagName).toLowerCase().trim().replace(/\s+/g, '').replace(/[^a-z0-9]/g, '')
    
    if (normalized === 'business') return 'business'
    if (normalized === 'education') return 'education'
    if (normalized === 'emergency') return 'emergency'
    if (normalized === 'employment') return 'employment'
    if (normalized === 'environment' || normalized === 'env') return 'environment'
    if (normalized === 'governance') return 'governance'
    if (normalized === 'health' || normalized === 'medical') return 'health'
    if (normalized === 'incident') return 'incident'
    if (normalized === 'infrastructure') return 'infrastructure'
    if (normalized === 'inquiries' || normalized === 'inquiry') return 'inquiries'
    if (normalized === 'livelihood') return 'livelihood'
    if (normalized === 'maintenance') return 'maintenance'
    if (normalized === 'sanitation') return 'sanitation'
    if (normalized === 'sports') return 'sports'
    if (normalized === 'traffic') return 'traffic'
    if (normalized === 'weather') return 'weather'
    if (normalized === 'welfare') return 'welfare'
    if (normalized === 'youth') return 'youth'
    
    return normalized
}

const toggleSettings = () => showSettings.value = !showSettings.value
const closeSettings = () => showSettings.value = false
const logout = () => { showSettings.value = false; router.visit(route('login')) }
const setActiveTab = (tab) => activeTab.value = tab
const navigateToDocuments = () => { activeTab.value = 'documents'; router.visit(route('document_request_select_employee')) }
const navigateToProfile = () => { activeTab.value = 'profile'; router.visit(route('profile_employee')) }
const navigateToEvents = () => { activeTab.value = 'events'; router.visit(route('event_assistance_employee')) }
const navigateToNotifications = () => { activeTab.value = 'notifications'; router.visit(route('notification_request_employee')) }
const backToPosts = () => router.visit(route('discussion_resident'))

const openTagsModal = () => { showTagsModal.value = true }
const closeTagsModal = () => { showTagsModal.value = false }

const isTagSelected = (tagId) => selectedTagIds.value.includes(tagId)

const toggleTag = (tag) => {
    const idx = selectedTagIds.value.indexOf(tag.tag_id)
    if (idx > -1) selectedTagIds.value.splice(idx, 1)
    else selectedTagIds.value.push(tag.tag_id)
}

const removeTag = (tagId) => {
    const idx = selectedTagIds.value.indexOf(tagId)
    if (idx > -1) selectedTagIds.value.splice(idx, 1)
}

const confirmTags = () => {
    closeTagsModal()
}

const updateCharCount = () => charCount.value = postContent.value.length
const triggerFileUpload = () => fileInput.value && fileInput.value.click()

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files || [])
    files.forEach(file => {
        const reader = new FileReader()
        reader.onload = (e) => {
            uploadedFiles.value.push({ file, preview: e.target.result })
        }
        reader.readAsDataURL(file)
    })
}

const removeFile = (index) => uploadedFiles.value.splice(index, 1)

const publishPost = () => {
    if (!postContent.value.trim()) {
        alert('Please write something before publishing')
        return
    }
    if (selectedTagIds.value.length === 0) {
        alert('Please select at least one tag')
        return
    }

    // prepare form
    form.clearErrors()
    form.header = postHeader.value.trim()
    form.content = postContent.value
    form.tag_ids = selectedTagIds.value.slice()

    // include first file if available
    if (uploadedFiles.value.length > 0) {
        form.image = uploadedFiles.value[0].file
    } else {
        form.image = null
    }

    form.post(route('posts.store'), {
        preserveState: false,
        onStart: () => console.log('Publishing discussion post...'),
        onSuccess: (page) => {
            console.log('Discussion post published, clearing form')
            // reset local UI state
            postHeader.value = ''
            postContent.value = ''
            charCount.value = 0
            selectedTagIds.value = []
            uploadedFiles.value = []
            form.reset()
            // redirect to discussion index
            router.visit(route('discussion_resident'))
        },
        onError: (errors) => {
            console.error('Server validation errors', errors)
            if (errors.content) alert(errors.content[0])
            if (errors.tag_ids) alert(errors.tag_ids[0])
        }
    })
}

const deleteDraft = (draftId) => {
    const idx = drafts.value.findIndex(d => d.id === draftId)
    if (idx > -1) drafts.value.splice(idx, 1)
}

const openFAQ = () => {
    router.visit(route('help_center_employee'))
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) showSettings.value = false
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'posts'
})

onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 350px;
    max-height: 80vh;
    overflow-y: auto;
}

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
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
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
    transition: all 0.3s ease;
    font-size: 14px;
}

.faq-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
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

.discussions-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.discussions-title h2 {
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

.add-post-section {
    padding: 30px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    position: relative;
}

.back-btn {
    background: transparent;
    border: none;
    color: #ff8c42;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-btn:hover {
    color: #e6763a;
    transform: translateX(-3px);
}

.add-tags-btn {
    position: absolute;
    top: 30px;
    right: 50px;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}

.add-tags-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.post-input-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.post-header-input {
    width: 100%;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: white;
    margin-bottom: 15px;
    transition: border-color 0.2s;
}

.post-header-input:focus {
    outline: none;
    border-color: #ff8c42;
}

.post-textarea {
    width: 100%;
    min-height: 150px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    resize: vertical;
    background: white;
    margin-bottom: 15px;
    transition: border-color 0.2s;
}

.post-textarea:focus {
    outline: none;
    border-color: #ff8c42;
}

.post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.attach-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}

.attach-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
}

.char-count {
    font-size: 13px;
    color: #999;
    font-weight: 600;
}

.file-input-hidden {
    display: none;
}

.uploaded-files {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.file-preview {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.file-preview-img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.remove-file-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.remove-file-btn:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.tags-section {
    position: relative;
    margin-bottom: 25px;
}

.tags-label {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-right: 15px;
}

.selected-tags {
    display: inline-flex;
    gap: 10px;
    flex-wrap: wrap;
}

.tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    /* Default background for unmatched tags */
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

.tag-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Business - Blue/Purple */
.tag-chip.business {
    background: linear-gradient(135deg, #6c5ce7, #5f3dc4) !important;
}

/* Education - Blue */
.tag-chip.education {
    background: linear-gradient(135deg, #3498db, #2980b9) !important;
}

/* Emergency - Red */
.tag-chip.emergency {
    background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
}

/* Employment - Green */
.tag-chip.employment {
    background: linear-gradient(135deg, #27ae60, #229954) !important;
}

/* Environment - Green */
.tag-chip.environment {
    background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
}

/* Governance - Purple */
.tag-chip.governance {
    background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
}

/* Health - Red/Pink */
.tag-chip.health {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Incident - Dark Red */
.tag-chip.incident {
    background: linear-gradient(135deg, #c0392b, #a93226) !important;
}

/* Infrastructure - Orange */
.tag-chip.infrastructure {
    background: linear-gradient(135deg, #f39c12, #e67e22) !important;
}

/* Inquiries - Yellow/Orange */
.tag-chip.inquiries {
    background: linear-gradient(135deg, #f1c40f, #f39c12) !important;
}

/* Livelihood - Teal */
.tag-chip.livelihood {
    background: linear-gradient(135deg, #1abc9c, #16a085) !important;
}

/* Maintenance - Brown/Orange */
.tag-chip.maintenance {
    background: linear-gradient(135deg, #d35400, #ba4a00) !important;
}

/* Sanitation - Cyan */
.tag-chip.sanitation {
    background: linear-gradient(135deg, #00bcd4, #0097a7) !important;
}

/* Sports - Green */
.tag-chip.sports {
    background: linear-gradient(135deg, #4caf50, #388e3c) !important;
}

/* Traffic - Yellow */
.tag-chip.traffic {
    background: linear-gradient(135deg, #ffc107, #ff9800) !important;
}

/* Weather - Light Blue */
.tag-chip.weather {
    background: linear-gradient(135deg, #03a9f4, #0288d1) !important;
}

/* Welfare - Pink */
.tag-chip.welfare {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Youth - Magenta */
.tag-chip.youth {
    background: linear-gradient(135deg, #e91e63, #ad1457) !important;
}

.remove-tag-btn {
    border: none;
    color: white;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.publish-btn {
    width: auto;
    min-width: 200px;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 40px;
    margin-left: auto;
    display: block;
}

.publish-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
}

.publish-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.drafts-section {
    margin-top: 40px;
}

.drafts-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
}

.draft-card {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.2);
    border: 2px solid #4caf50;
}

.draft-content {
    margin-bottom: 15px;
}

.draft-text {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.draft-preview {
    font-size: 14px;
    color: #555;
    line-height: 1.6;
}

.draft-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.draft-date {
    font-size: 13px;
    color: #4caf50;
    font-weight: 600;
}

.delete-draft-btn {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #dc3545;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
}

.delete-draft-btn:hover {
    background: rgba(220, 53, 69, 0.2);
    transform: scale(1.05);
}

.no-drafts {
    text-align: center;
    padding: 40px;
    color: #999;
    font-size: 14px;
}

.no-tags-text {
    color: #999;
    font-style: italic;
}

.add-post-section::-webkit-scrollbar {
    width: 6px;
}

.add-post-section::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.add-post-section::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.add-post-section::-webkit-scrollbar-thumb:hover {
    background: #666;
}

.modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
}

.modal-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
}

.modal-close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s;
}

.modal-close-btn:hover {
    background: rgba(255,255,255,0.2);
}

.modal-body {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
}

.tags-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}

.tag-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 600;
    text-align: left;
}

.tag-option:hover {
    background: #fff7ef;
    border-color: #ff8c42;
}

.tag-option.selected {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    border-color: #ff8c42;
    color: #ff8c42;
}

.tag-checkbox {
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ff8c42;
}

.tag-option.selected .tag-checkbox {
    border-color: #ff8c42;
    background: #ff8c42;
    color: white;
}

.no-tags-available {
    text-align: center;
    padding: 40px;
    color: #999;
}

.modal-footer {
    padding: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #e0e0e0;
}

.modal-cancel-btn,
.modal-confirm-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.modal-cancel-btn {
    background: #f8f9fa;
    color: #333;
}

.modal-cancel-btn:hover {
    background: #e9ecef;
}

.modal-confirm-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
}

.modal-confirm-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.3);
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

    .add-tags-btn {
        position: static;
        width: 100%;
        margin-bottom: 20px;
    }

    .uploaded-files {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .add-post-section {
        padding: 20px 15px;
    }

    .profile-card {
        padding: 15px;
    }
}

/* Global scrollbar styling */
body::-webkit-scrollbar,
html::-webkit-scrollbar {
    width: 8px;
}

body::-webkit-scrollbar-track,
html::-webkit-scrollbar-track {
    background: #f1f1f1;
}

body::-webkit-scrollbar-thumb,
html::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

body::-webkit-scrollbar-thumb:hover,
html::-webkit-scrollbar-thumb:hover {
    background: #666;
}
</style>
