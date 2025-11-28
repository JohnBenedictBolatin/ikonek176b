<template>
    <Head>
        <title>Add Post</title>
    </Head>

    <div class="app-container">
        <!-- Orange Header with Logo and Settings -->
        <div class="header-bar">
            <div class="header-content">
                <div class="logo-section">
                    <img src="/assets/WHITE LOGO.png" alt="Logo" class="header-logo" />
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
                    <img src="/assets/KAPITAN.jpg" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">Kap. Sammy Reyes</div>
                        <div class="profile-role">BARANGAY CAPTAIN</div>
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
                        @click="setActiveTab('events')"
                    >
                        🤝 Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'notifications' }"
                        @click="setActiveTab('notifications')"
                    >
                        🔔 Notifications
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'profile' }"
                        @click="setActiveTab('profile')"
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
                            <h2>Discussions</h2>
                        </div>
                        <div class="header-icon">
                            <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
                        </div>
                    </div>

                    <!-- Add Post Section -->
                    <div class="add-post-section">
                        <button class="back-btn" @click="backToPosts">
                            ◀ BACK TO POSTS
                        </button>

                        <button class="add-tags-btn" @click="toggleTagsDropdown">
                            # ADD TAGS
                        </button>

                        <!-- Post Input Area -->
                        <div class="post-input-container">
                            <textarea 
                                v-model="postContent"
                                placeholder="Write a post..."
                                class="post-textarea"
                                @input="updateCharCount"
                            ></textarea>

                            <div class="post-actions">
                                <button class="attach-btn" @click="triggerFileUpload">
                                    ATTACH
                                </button>
                                <span class="char-count">{{ charCount }}/250</span>
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
                                    v-for="tag in selectedTags" 
                                    :key="tag"
                                    class="tag-chip"
                                    :class="tag.toLowerCase()"
                                >
                                    #{{ tag }}
                                    <button class="remove-tag-btn" @click="removeTag(tag)">⊖</button>
                                </span>
                            </div>

                            <!-- Tags Dropdown -->
                            <div v-if="showTagsDropdown" class="tags-dropdown">
                                <button 
                                    v-for="tag in availableTags" 
                                    :key="tag"
                                    class="tag-option"
                                    :class="{ selected: selectedTags.includes(tag) }"
                                    @click="toggleTag(tag)"
                                >
                                    {{ tag }}
                                </button>
                            </div>
                        </div>

                        <!-- Publish Button -->
                        <button class="publish-btn" @click="publishPost">
                            PUBLISH
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
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const showSettings = ref(false)
const activeTab = ref('posts')
const postContent = ref('')
const charCount = ref(0)
const selectedTags = ref(['QUESTION'])
const showTagsDropdown = ref(false)
const uploadedFiles = ref([])
const fileInput = ref(null)

const availableTags = ['QUESTION', 'HELP', 'GENERAL']

const drafts = ref([
    {
        id: 1,
        title: 'Magandang araw po sa ating butihing Kapitan at mga Kagawad!',
        content: 'May tanong lang po sana ako tungkol sa balita na magkakaroon daw po tayo ng',
        date: 'June 01, 2025 (2:22 pm)'
    }
])

const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const logout = () => {
    showSettings.value = false
    router.visit(route('login'))
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const navigateToDocuments = () => {
    activeTab.value = 'documents'
    router.visit(route('document_request_select_employee'))
}

const backToPosts = () => {
    router.visit(route('discussion_employee'))
}

const toggleTagsDropdown = () => {
    showTagsDropdown.value = !showTagsDropdown.value
}

const toggleTag = (tag) => {
    const index = selectedTags.value.indexOf(tag)
    if (index > -1) {
        selectedTags.value.splice(index, 1)
    } else {
        selectedTags.value.push(tag)
    }
}

const removeTag = (tag) => {
    const index = selectedTags.value.indexOf(tag)
    if (index > -1) {
        selectedTags.value.splice(index, 1)
    }
}

const updateCharCount = () => {
    charCount.value = postContent.value.length
}

const triggerFileUpload = () => {
    fileInput.value.click()
}

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files)
    files.forEach(file => {
        const reader = new FileReader()
        reader.onload = (e) => {
            uploadedFiles.value.push({
                file: file,
                preview: e.target.result
            })
        }
        reader.readAsDataURL(file)
    })
}

const removeFile = (index) => {
    uploadedFiles.value.splice(index, 1)
}

const publishPost = () => {
    if (!postContent.value.trim()) {
        alert('Please write something before publishing')
        return
    }

    if (selectedTags.value.length === 0) {
        alert('Please select at least one tag')
        return
    }

    alert('Post published successfully!')
    router.visit(route('discussion_employee'))
}

const deleteDraft = (draftId) => {
    const index = drafts.value.findIndex(d => d.id === draftId)
    if (index > -1) {
        drafts.value.splice(index, 1)
        alert('Draft deleted successfully')
    }
}

const openFAQ = () => {
    console.log('Opening FAQ')
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.header-actions')) {
        showSettings.value = false
    }
    if (!event.target.closest('.tags-section')) {
        showTagsDropdown.value = false
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

.header-actions {
    position: relative;
}

.settings-btn-img {
    margin-right: 30px;
    width: 30px;
    cursor: pointer;
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
    background:#239640;
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
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.tag-chip.question {
    background: linear-gradient(135deg, #ff9800, #f57c00);
}

.tag-chip.help {
    background: linear-gradient(135deg, #e91e63, #c2185b);
}

.tag-chip.general {
    background: linear-gradient(135deg, #9c27b0, #7b1fa2);
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


.tags-dropdown {
    position: absolute;
    top: 100%;
    left: 50px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 10px;
    z-index: 100;
    margin-top: 10px;
    border: 1px solid rgba(0,0,0,0.1);
}

.tag-option {
    display: block;
    width: 100%;
    padding: 10px 15px;
    background: none;
    border: none;
    text-align: left;
    color: #333;
    cursor: pointer;
    transition: background 0.2s;
    font-weight: 600;
    border-radius: 6px;
    margin-bottom: 5px;
}

.tag-option:hover {
    background: #f8f9fa;
}

.tag-option.selected {
    background: #fff7ef;
    color: #ff8c42;
}

.publish-btn {
    width: 30%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    transition: all 0.3s;
    margin-bottom: 40px;
    margin-left: 970px;
}

.publish-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
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

.add-post-section::-webkit-scrollbar {
    width: 6px;
}

.add-post-section::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.add-post-section::-webkit-scrollbar-thumb {
    background: #ff8c42;
    border-radius: 3px;
}

.add-post-section::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
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
</style>