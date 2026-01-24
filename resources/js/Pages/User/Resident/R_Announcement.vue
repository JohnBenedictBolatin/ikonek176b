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
                    <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
                </div>
                <div class="header-actions">
                    <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
                    <!-- Settings Dropdown -->
                    <div v-if="showSettings" class="settings-dropdown">
                        <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
                        <button type="button" class="settings-item" @click="openTerms">Terms & Conditions</button>
                        <Link href="#" class="settings-item" @click="logout">Sign Out</Link>
                    </div>
                </div>
            </div>
        </div>

        <TermsModal :open="showTerms" @close="closeTerms" />

        <!-- Main Content Area - Full Width -->
        <div class="main-layout">
            <!-- Profile Card and Navigation Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <img :src="profilePictureUrl" alt="Profile" class="profile-avatar" />
                    <div class="profile-info">
                        <div class="profile-name">{{ user?.name || 'Unknown User' }}</div>
                        <div class="profile-role">{{ displayRole }}</div>
                    </div>
                </div>

                <div class="nav-menu">
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'posts' }"
                        @click="setActiveTab('posts')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        Posts
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'documents' }"
                        @click="navigateToDocuments"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Document Request
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'events' }"
                        @click="navigateToEvents"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Event Assistance
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'notifications' }"
                        @click.prevent="navigateToNotifications"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        Notifications
                    </Link>
                    <Link 
                        href="#" 
                        class="nav-item"
                        :class="{ active: activeTab === 'profile' }"
                        @click="navigateToProfile"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Profile
                    </Link>
                </div>

                <button class="faq-btn" @click="openFAQ">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>
                    FAQs & Help Center
                </button>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Main Content Panel -->
                <div class="main-content-wrapper">
                <div class="main-content">
                    <!-- Discussions Header with Dropdown Toggle -->
                    <div class="discussions-header">
                        <div class="discussions-title">
                            <button class="title-dropdown" @click="toggleModeDropdown">
                                <h2>{{ currentTab === 'announcements' ? 'Announcements' : 'Discussions' }}</h2>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="dropdown-icon" :class="{ rotated: showModeDropdown }">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="filter-arrow" :class="{ rotated: showSortDropdown }">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="filter-arrow" :class="{ rotated: showFilterDropdown }">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div v-if="showFilterDropdown" class="filter-dropdown-menu">
                                    <button @click="selectFilter('all')" :class="{ active: filterOption === 'all' }">ALL</button>
                                    <button @click="selectFilter('business')" :class="{ active: filterOption === 'business' }">BUSINESS</button>
                                    <button @click="selectFilter('education')" :class="{ active: filterOption === 'education' }">EDUCATION</button>
                                    <button @click="selectFilter('emergency')" :class="{ active: filterOption === 'emergency' }">EMERGENCY</button>
                                    <button @click="selectFilter('employment')" :class="{ active: filterOption === 'employment' }">EMPLOYMENT</button>
                                    <button @click="selectFilter('environment')" :class="{ active: filterOption === 'environment' }">ENVIRONMENT</button>
                                    <button @click="selectFilter('governance')" :class="{ active: filterOption === 'governance' }">GOVERNANCE</button>
                                    <button @click="selectFilter('health')" :class="{ active: filterOption === 'health' }">HEALTH</button>
                                    <button @click="selectFilter('incident')" :class="{ active: filterOption === 'incident' }">INCIDENT</button>
                                    <button @click="selectFilter('infrastructure')" :class="{ active: filterOption === 'infrastructure' }">INFRASTRUCTURE</button>
                                    <button @click="selectFilter('inquiries')" :class="{ active: filterOption === 'inquiries' }">INQUIRIES</button>
                                    <button @click="selectFilter('livelihood')" :class="{ active: filterOption === 'livelihood' }">LIVELIHOOD</button>
                                    <button @click="selectFilter('maintenance')" :class="{ active: filterOption === 'maintenance' }">MAINTENANCE</button>
                                    <button @click="selectFilter('sanitation')" :class="{ active: filterOption === 'sanitation' }">SANITATION</button>
                                    <button @click="selectFilter('sports')" :class="{ active: filterOption === 'sports' }">SPORTS</button>
                                    <button @click="selectFilter('traffic')" :class="{ active: filterOption === 'traffic' }">TRAFFIC</button>
                                    <button @click="selectFilter('weather')" :class="{ active: filterOption === 'weather' }">WEATHER</button>
                                    <button @click="selectFilter('welfare')" :class="{ active: filterOption === 'welfare' }">WELFARE</button>
                                    <button @click="selectFilter('youth')" :class="{ active: filterOption === 'youth' }">YOUTH</button>
                                </div>
                            </div>
                        </div>
                        <div class="filter-right">
                            <!-- <button class="add-post-btn" @click="addPost">＋ ADD POST</button> -->
                            <div class="search-container">
                                <input 
                                    type="text" 
                                    v-model="searchQuery" 
                                    @input="performSearch"
                                    placeholder="SEARCH..." 
                                    class="search-input" 
                                />
                                <button class="search-btn" @click="performSearch">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="search-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </button>
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
                            :data-post-id="post.id"
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
                                        :class="getTagClass(tag)"
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
                                <h2 v-if="post.header && post.header.trim()" class="post-header-text">{{ post.header }}</h2>
                                <p class="post-text">{{ post.content }}</p>
                            </div>

                            <div class="post-actions" @click.stop>
                                <div class="reaction-buttons">
                                    <button 
                                        class="reaction-btn"
                                        :class="{ liked: post.userLiked }"
                                        @click="toggleLike(post.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                        {{ post.likes }}
                                    </button>
                                    <button 
                                        class="reaction-btn"
                                        :class="{ disliked: post.userDisliked }"
                                        @click="toggleDislike(post.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                        </svg>
                                        {{ post.dislikes }}
                                    </button>
                                    <button class="comment-btn" @click="viewComments(post.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="reaction-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.578-1.087a9.034 9.034 0 0 0 2.422 0Z" />
                                        </svg>
                                        {{ post.comments }}
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
                        <button class="back-btn" @click="closePost">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="back-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            BACK TO POSTS
                        </button>
                        
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
                                        :class="getTagClass(tag)"
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
                                <h2 v-if="selectedPost.header && selectedPost.header.trim()" class="post-header-text">{{ selectedPost.header }}</h2>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                        {{ selectedPost.likes }}
                                    </button>
                                    <button 
                                        class="reaction-btn"
                                        :class="{ disliked: selectedPost.userDisliked }"
                                        @click="toggleDislike(selectedPost.id)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                        </svg>
                                        {{ selectedPost.dislikes }}
                                    </button>
                                </div>
                                <div class="post-options">
                                    <button class="report-post-btn" @click="reportPost(selectedPost.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="report-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                        Report
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="comments-section">
                            <h3 class="comments-title">Comments ({{ selectedPost.commentsList ? selectedPost.commentsList.length : 0 }})</h3>
                            
                            <!-- Add Comment Form -->
                            <div class="add-comment-form">
                                <img :src="profilePictureUrl" alt="Your Profile" class="comment-avatar" />
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
                                            <button class="report-btn" @click="reportComment(comment.id)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="report-icon-small">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                </svg>
                                                Report
                                            </button>
                                        </div>
                                        <p class="comment-text">{{ comment.text }}</p>
                                        <div class="comment-actions">
                                            <button 
                                                class="comment-reaction-btn"
                                                :class="{ liked: comment.userLiked }"
                                                @click="toggleCommentLike(comment.id)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon-small">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>
                                                {{ comment.likes || 0 }}
                                            </button>
                                            <button 
                                                class="comment-reaction-btn"
                                                :class="{ disliked: comment.userDisliked }"
                                                @click="toggleCommentDislike(comment.id)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon-small">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                                </svg>
                                                {{ comment.dislikes || 0 }}
                                            </button>
                                            <button 
                                                class="reply-btn"
                                                @click="toggleReplyForm(comment.id)"
                                            >
                                                Reply
                                            </button>
                                        </div>

                                        <!-- Replies -->
                                        <div v-if="comment.replies && comment.replies.length > 0" class="replies-list">
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
                                                        <button class="report-btn" @click="reportComment(reply.id)">🚩 Report</button>
                                                    </div>
                                                    <p class="comment-text">{{ reply.text }}</p>
                                                    <div class="comment-actions">
                                                        <button 
                                                            class="comment-reaction-btn"
                                                            :class="{ liked: reply.userLiked }"
                                                            @click="toggleReplyLike(comment.id, reply.id)"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon-small">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                            </svg>
                                                            {{ reply.likes || 0 }}
                                                        </button>
                                                        <button 
                                                            class="comment-reaction-btn"
                                                            :class="{ disliked: reply.userDisliked }"
                                                            @click="toggleReplyDislike(comment.id, reply.id)"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="reaction-icon-small">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                                            </svg>
                                                            {{ reply.dislikes || 0 }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reply Form -->
                                        <div v-if="replyingTo === comment.id" class="reply-form">
                                            <img :src="profilePictureUrl" alt="Your Profile" class="comment-avatar small" />
                                            <div class="reply-input-wrapper">
                                                <textarea 
                                                    v-model="newReply" 
                                                    placeholder="Write a reply..."
                                                    class="reply-input"
                                                    rows="2"
                                                ></textarea>
                                                <div class="reply-actions">
                                                    <button @click="addReply(comment.id)" class="submit-reply-btn">Reply</button>
                                                    <button @click="cancelReply" class="cancel-reply-btn">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Trending Tags Sidebar -->
            <div class="trending-tags-sidebar">
                <div class="trending-tags-card">
                        <h3 class="trending-tags-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="trending-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 0 1 5.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28-2.28 5.94" />
                            </svg>
                            Trending Tags
                        </h3>
                        <div v-if="loadingTrendingTags" class="trending-tags-loading">
                            <p>Loading...</p>
                        </div>
                        <div v-else-if="trendingTags.length === 0" class="trending-tags-empty">
                            <p>No trending tags yet</p>
                        </div>
                        <div v-else class="trending-tags-list">
                            <button
                                v-for="tag in trendingTags"
                                :key="tag.id"
                                class="trending-tag-item"
                                :class="{ active: selectedTrendingTag === tag.name }"
                                @click="selectTrendingTag(tag.name)"
                            >
                                <span class="trending-tag-name">#{{ tag.name }}</span>
                                <span class="trending-tag-count">{{ tag.count }}</span>
                            </button>
                        </div>
                        <button v-if="selectedTrendingTag" class="clear-tag-filter-btn" @click="clearTagFilter">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>

        <!-- Report Modal -->
        <div v-if="showReportModal" class="report-modal-overlay" @click="closeReportModal">
            <div class="report-modal" @click.stop>
                <div class="report-modal-header">
                    <h3>Report {{ reportType }}</h3>
                    <button class="close-modal-btn" @click="closeReportModal">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="close-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="report-modal-body">
                    <p class="report-description">Please select the reason(s) for reporting this {{ reportType.toLowerCase() }}:</p>
                    <div class="report-options">
                        <label class="report-option">
                            <input type="checkbox" value="spam" v-model="reportReasons" />
                            <span>Spam or misleading</span>
                        </label>
                        <label class="report-option">
                            <input type="checkbox" value="harassment" v-model="reportReasons" />
                            <span>Harassment or bullying</span>
                        </label>
                        <label class="report-option">
                            <input type="checkbox" value="hate" v-model="reportReasons" />
                            <span>Hate speech</span>
                        </label>
                        <label class="report-option">
                            <input type="checkbox" value="violence" v-model="reportReasons" />
                            <span>Violence or dangerous content</span>
                        </label>
                        <label class="report-option">
                            <input type="checkbox" value="inappropriate" v-model="reportReasons" />
                            <span>Inappropriate content</span>
                        </label>
                        <label class="report-option">
                            <input type="checkbox" value="false" v-model="reportReasons" />
                            <span>False information</span>
                        </label>
                        <label class="report-option">
                            <input type="checkbox" value="other" v-model="reportReasons" />
                            <span>Other</span>
                        </label>
                    </div>
                    <textarea 
                        v-if="reportReasons.includes('other')"
                        v-model="reportDetails"
                        placeholder="Please provide additional details..."
                        class="report-details-input"
                        rows="4"
                    ></textarea>
                </div>
                <div class="report-modal-footer">
                    <button class="cancel-report-btn" @click="closeReportModal">Cancel</button>
                    <button class="submit-report-btn" @click="submitReport" :disabled="reportReasons.length === 0">Submit Report</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import TermsModal from '@/Components/TermsModal.vue'

// Define props - receive posts from backend
const props = defineProps({
    posts: {
        type: Array,
        default: () => []
    },
    auth: {
        type: Object,
        default: () => ({})
    }
})

// Get page props
const page = usePage()

// SAFE user access - prioritize prop, fallback to page.props
const user = computed(() => {
    // First try from shared Inertia auth (from middleware) - this has profile_pic
    const pageUser = page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null
    if (pageUser) {
        return pageUser
    }
    
    // Then try from props.auth.user (from controller)
    if (props.auth?.user) {
        return props.auth.user
    }
    
    // Then try from page props
    const authUser = page?.props?.auth?.user
    
    // Return default if undefined
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
    const role = roleMap[id] ?? 'Resident'
    return role.toUpperCase()
})

// Profile picture URL
const profilePictureUrl = computed(() => {
    if (user.value?.profile_pic) {
        const pic = user.value.profile_pic
        let url = ''
        // If it's a full URL, return as is
        if (pic.startsWith('http')) {
            url = pic
        }
        // If it already has /storage/, return as is
        else if (pic.startsWith('/storage/')) {
            url = pic
        }
        // Otherwise prepend storage path
        else {
            url = `/storage/${pic}`
        }
        // Add cache-busting parameter based on profile_pic value to force browser reload when it changes
        // Use a hash of the profile_pic path as version to ensure same image gets same URL
        const version = pic ? pic.split('/').pop() + pic.length : Date.now()
        return url + (url.includes('?') ? '&' : '?') + `v=${version}`
    }
    return '/assets/DEFAULT.jpg'
})

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
const reportReasons = ref([])
const reportDetails = ref('')
const reportTargetId = ref(null)
const trendingTags = ref([])
const loadingTrendingTags = ref(false)
const selectedTrendingTag = ref(null)

// Initialize posts from props
// Unified posts ref used by the template
const posts = ref([])

/**
 * Normalize server post into front-end post shape expected by the template.
 * Accepts server post objects with either:
 *  - id (frontend-style) OR post_id (backend)
 *  - images (array of full URLs) OR image_content (storage path) OR image_path
 */
function normalizePost(raw) {
    if (!raw) return null

    // id: prefer 'id', then 'post_id', then 'postId'
    const id = raw.id ?? raw.post_id ?? raw.postId ?? null

    // author info
    const authorName = raw.author?.name ?? raw.author ?? raw.authorName ?? 'Unknown'
    const avatar = raw.avatar ?? raw.author?.avatar ?? raw.author_avatar ?? '/assets/DEFAULT.jpg'

    // tags as array of strings (some server returns objects)
    let tags = []
    if (Array.isArray(raw.tags)) {
        tags = raw.tags.map(t => (typeof t === 'string' ? t : (t.tag_name ?? t.name ?? ''))).filter(Boolean)
    } else if (raw.tags && typeof raw.tags === 'object') {
        tags = Object.values(raw.tags).map(t => (typeof t === 'string' ? t : (t.tag_name ?? t.name ?? ''))).filter(Boolean)
    }

    // images: prefer 'images' (already full URLs) else convert image_content/image_path to storage URL
    let images = []
    if (Array.isArray(raw.images) && raw.images.length) {
        images = raw.images
    } else if (raw.image_content) {
        // backend stored path like "posts/abcd.jpg" -> convert to asset('storage/...')
        images = [raw.image_content.startsWith('http') ? raw.image_content : `/storage/${raw.image_content}`]
    } else if (raw.image_path) {
        images = [raw.image_path.startsWith('http') ? raw.image_path : `/storage/${raw.image_path}`]
    }

    // date/time: server may already give ISO, or 'created_at' string. Keep as ISO string for sorting.
    const dateIso = raw.date ?? raw.created_at ?? raw.createdAt ?? null
    const time = raw.time ?? (dateIso ? new Date(dateIso).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }) : '')

    // title fallback (server sometimes sends title or derive from content)
    const title = raw.title ?? (raw.content ? (raw.content.length > 50 ? raw.content.substring(0, 50) + '...' : raw.content) : 'Untitled Post')

    return {
        id,
        author: authorName,
        avatar: avatar || '/assets/DEFAULT.jpg',
        role: raw.role ?? 'Resident',
        tags,
        date: dateIso,
        time,
        title,
        header: raw.header ?? null,
        content: raw.content ?? '',
        images,
        video_content: raw.video_content ?? raw.video_path ?? null,
        likes: raw.likes ?? 0,
        dislikes: raw.dislikes ?? 0,
        comments: raw.comments ?? 0,
        userLiked: raw.userLiked ?? false,
        userDisliked: raw.userDisliked ?? false,
        commentsList: Array.isArray(raw.commentsList) ? raw.commentsList : []
    }
}

/**
 * Update posts ref from whichever source Inertia provides:
 *  - props.posts (recommended)
 *  - page.props.posts (fallback)
 */
function updatePostsFromProps() {
    const serverPosts = (props.posts && props.posts.length) ? props.posts : (page?.props?.posts ?? [])
    if (!serverPosts || serverPosts.length === 0) {
        posts.value = []
        return
    }

    // normalize each post
    posts.value = serverPosts.map(p => normalizePost(p)).filter(Boolean)
    console.log('✅ posts updated (normalized):', posts.value.length)
}

// watch both component props and Inertia page props so view updates on redirect/rehydration
watch(() => props.posts, updatePostsFromProps, { immediate: true, deep: true })
watch(() => page.props.posts, updatePostsFromProps, { immediate: true, deep: true })

// Scroll to post if post parameter is in URL
onMounted(() => {
    // Wait for posts to load, then check for post parameter
    setTimeout(() => {
        const urlParams = new URLSearchParams(window.location.search)
        const postId = urlParams.get('post')
        if (postId && posts.value.length > 0) {
            scrollToPost(postId)
        }
    }, 1000)
})

// Function to scroll to a specific post
const scrollToPost = (postId) => {
    const postElement = document.querySelector(`[data-post-id="${postId}"]`)
    if (postElement) {
        postElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
        // Highlight the post briefly with light green
        postElement.style.transition = 'background-color 0.3s'
        postElement.style.backgroundColor = '#d4edda'
        setTimeout(() => {
            postElement.style.backgroundColor = ''
        }, 2000)
    }
}



// Import navigation composable
import { useUserNavigation } from '@/composables/useUserNavigation'

// Use navigation composable
const { 
    navigateToDocuments: navToDocuments, 
    navigateToProfile: navToProfile, 
    navigateToEvents: navToEvents, 
    navigateToNotifications: navToNotifications,
    navigateToHelpCenter: navToHelpCenter,
    navigateToAnnouncement: navToAnnouncement,
    navigateToDiscussion: navToDiscussion,
    navigateToDiscussionAddPost: navToDiscussionAddPost
} = useUserNavigation()

// Navigation functions with activeTab management
const navigateToDocuments = () => {
    activeTab.value = 'documents'
    navToDocuments()
}

const navigateToProfile = () => {
    activeTab.value = 'profile'
    navToProfile()
}

const navigateToEvents = () => {
    activeTab.value = 'events'
    navToEvents()
}

const navigateToNotifications = () => {
    activeTab.value = 'notifications'
    navToNotifications()
}

// Fetch trending tags
const fetchTrendingTags = async () => {
    loadingTrendingTags.value = true
    try {
        const response = await axios.get(route('api.tags.trending'), {
            params: {
                days: 30,
                limit: 10
            }
        })
        if (response.data.success) {
            trendingTags.value = response.data.tags || []
        }
    } catch (error) {
        console.error('Error fetching trending tags:', error)
        trendingTags.value = []
    } finally {
        loadingTrendingTags.value = false
    }
}

// Select trending tag
const selectTrendingTag = (tagName) => {
    if (selectedTrendingTag.value === tagName) {
        clearTagFilter()
    } else {
        selectedTrendingTag.value = tagName
        filterOption.value = tagName.toLowerCase()
    }
}

// Clear tag filter
const clearTagFilter = () => {
    selectedTrendingTag.value = null
    filterOption.value = 'all'
}

// Computed filtered posts
const filteredPosts = computed(() => {
    console.log('🔍 Filtering posts:', {
        totalPosts: posts.value.length,
        searchQuery: searchQuery.value,
        filterOption: filterOption.value,
        sortOption: sortOption.value
    })
    
    let filtered = [...posts.value]

    // Search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(post => 
            (post.title && post.title.toLowerCase().includes(query)) ||
            (post.content && post.content.toLowerCase().includes(query)) ||
            (post.author && post.author.toLowerCase().includes(query))
        )
    }

    // Tag filter
    if (filterOption.value !== 'all') {
        filtered = filtered.filter(post => 
            post.tags && post.tags.some(tag => tag.toLowerCase() === filterOption.value.toLowerCase())
        )
    }

    // Sort
    if (sortOption.value === 'newest') {
        filtered.sort((a, b) => new Date(b.date) - new Date(a.date))
    } else if (sortOption.value === 'oldest') {
        filtered.sort((a, b) => new Date(a.date) - new Date(b.date))
    } else if (sortOption.value === 'relevant') {
        filtered.sort((a, b) => (b.likes + b.comments) - (a.likes + a.comments))
    }

    console.log('✅ Filtered posts count:', filtered.length)
    return filtered
})

// Methods
const toggleSettings = () => {
    showSettings.value = !showSettings.value
}

const closeSettings = () => {
    showSettings.value = false
}

const openTerms = () => {
    showSettings.value = false
    showTerms.value = true
}

const closeTerms = () => {
    showTerms.value = false
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
}

const selectFilter = (option) => {
    filterOption.value = option
    showFilterDropdown.value = false
}

const logout = () => {
    showSettings.value = false
    // Properly logout by calling the logout endpoint
    router.post('/logout', {}, {
        onSuccess: () => {
            // Clear any local storage or session storage if needed
            if (typeof window !== 'undefined') {
                localStorage.clear()
                sessionStorage.clear()
            }
            // Redirect to login page after successful logout
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onError: () => {
            // Even if logout fails, redirect to login
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        },
        onFinish: () => {
            // Ensure we redirect even if something goes wrong
            router.visit(route('login'), {
                replace: true,
                preserveState: false,
                preserveScroll: false
            })
        }
    })
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const switchTab = (tab) => {
    currentTab.value = tab
    showModeDropdown.value = false
    if (tab === 'announcements') {
        navToAnnouncement()
    } else if (tab === 'discussions') {
        navToDiscussion()
    }
}

const performSearch = () => {
    console.log('🔎 Performing search:', searchQuery.value)
}

const addPost = () => {
    navToDiscussionAddPost()
}

const viewPost = async (postId) => {
    const post = posts.value.find(p => p.id === postId)
    if (post) {
        selectedPost.value = { ...post }
        console.log('👁️ Viewing post:', postId)
        
        // Load comments from database
        try {
            const response = await axios.get(route('posts.comments.get', postId))
            if (response.data.success) {
                selectedPost.value.commentsList = response.data.comments || []
            }
        } catch (error) {
            console.error('Error loading comments:', error)
            selectedPost.value.commentsList = []
        }
    }
}

const closePost = () => {
    selectedPost.value = null
    newComment.value = ''
    newReply.value = ''
    replyingTo.value = null
}

const addComment = async () => {
    if (!newComment.value.trim() || !selectedPost.value) return
    
    try {
        const response = await axios.post(route('posts.comments.store', selectedPost.value.id), {
            comment_text: newComment.value.trim(),
            parent_comment_id: null
        })
        
        if (response.data.success) {
            selectedPost.value.commentsList.push(response.data.comment)
            
            const originalPost = posts.value.find(p => p.id === selectedPost.value.id)
            if (originalPost) {
                originalPost.comments++
            }
            
            newComment.value = ''
        }
    } catch (error) {
        console.error('Error adding comment:', error)
        alert('Failed to add comment. Please try again.')
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

const addReply = async (commentId) => {
    if (!newReply.value.trim() || !selectedPost.value) return
    
    try {
        const response = await axios.post(route('posts.comments.store', selectedPost.value.id), {
            comment_text: newReply.value.trim(),
            parent_comment_id: commentId
        })
        
        if (response.data.success) {
            const comment = selectedPost.value.commentsList.find(c => c.id === commentId)
            if (comment) {
                comment.replies.push(response.data.comment)
            }
            newReply.value = ''
            replyingTo.value = null
        }
    } catch (error) {
        console.error('Error adding reply:', error)
        alert('Failed to add reply. Please try again.')
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
    reportReasons.value = []
    reportDetails.value = ''
    reportTargetId.value = null
}

const submitReport = async () => {
    if (!reportReasons.value || reportReasons.value.length === 0) {
        alert('Please select at least one reason for reporting.')
        return
    }

    if (reportType.value === 'Post' && reportTargetId.value) {
        try {
            const response = await axios.post(route('reports.store'), {
                post_id: reportTargetId.value,
                reasons: reportReasons.value,
                details: reportReasons.value.includes('other') ? reportDetails.value : null,
            })

            if (response.data.success) {
                alert('Report submitted successfully. Thank you for helping keep our community safe.')
                closeReportModal()
            } else {
                alert(response.data.message || 'Error submitting report. Please try again.')
            }
        } catch (error) {
            console.error('Error submitting report:', error)
            const errorMessage = error.response?.data?.message || 
                               (error.response?.data?.errors ? 
                                Object.values(error.response.data.errors).flat().join(', ') : 
                                'Error submitting report. Please try again.')
            alert(errorMessage)
        }
    } else if (reportType.value === 'Comment') {
        // Comment reporting can be implemented later
        alert('Comment reporting is not yet implemented.')
        closeReportModal()
    }
}

const formatCommentDate = (date) => {
    const now = new Date()
    const commentDate = new Date(date)
    const diff = now - commentDate
    const minutes = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)
    const days = Math.floor(diff / 86400000)
    
    if (minutes < 1) return 'Just now'
    if (minutes < 60) return `${minutes}m ago`
    if (hours < 24) return `${hours}h ago`
    if (days < 7) return `${days}d ago`
    
    return commentDate.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        year: commentDate.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    })
}

const toggleLike = async (postId) => {
    try {
        const response = await axios.post(route('posts.reactions.toggle', postId), {
            reaction_type: 'Like'
        })
        
        if (response.data.success) {
            const post = posts.value.find(p => p.id === postId)
            if (post) {
                post.likes = response.data.likes
                post.dislikes = response.data.dislikes
                post.userLiked = response.data.userLiked
                post.userDisliked = response.data.userDisliked
            }
            
            if (selectedPost.value && selectedPost.value.id === postId) {
                selectedPost.value.likes = response.data.likes
                selectedPost.value.dislikes = response.data.dislikes
                selectedPost.value.userLiked = response.data.userLiked
                selectedPost.value.userDisliked = response.data.userDisliked
            }
        }
    } catch (error) {
        console.error('Error toggling like:', error)
    }
}

const toggleDislike = async (postId) => {
    try {
        const response = await axios.post(route('posts.reactions.toggle', postId), {
            reaction_type: 'Dislike'
        })
        
        if (response.data.success) {
            const post = posts.value.find(p => p.id === postId)
            if (post) {
                post.likes = response.data.likes
                post.dislikes = response.data.dislikes
                post.userLiked = response.data.userLiked
                post.userDisliked = response.data.userDisliked
            }
            
            if (selectedPost.value && selectedPost.value.id === postId) {
                selectedPost.value.likes = response.data.likes
                selectedPost.value.dislikes = response.data.dislikes
                selectedPost.value.userLiked = response.data.userLiked
                selectedPost.value.userDisliked = response.data.userDisliked
            }
        }
    } catch (error) {
        console.error('Error toggling dislike:', error)
    }
}

const viewComments = (postId) => {
    viewPost(postId)
}

const sharePost = (postId) => {
    alert('Post link copied to clipboard!')
}

const showMoreOptions = (postId) => {
    console.log('Show more options for post:', postId)
}

const openFAQ = () => {
    router.visit(route('help_center_resident'))
}

// Helper function to normalize tag names for CSS classes
const getTagClass = (tag) => {
    if (!tag) return ''
    // Convert to lowercase and replace spaces/special chars with nothing
    let normalized = String(tag).toLowerCase().trim().replace(/\s+/g, '').replace(/[^a-z0-9]/g, '')
    
    // Map to specific tag classes
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

const formatDate = (dateString) => {
    const date = new Date(dateString)
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

// Component lifecycle
// Check authentication on mount and redirect if not authenticated
onMounted(() => {
    if (!user.value || !user.value.user_id) {
        router.visit(route('login'), {
            replace: true,
            preserveState: false
        })
        return
    }
    
    document.addEventListener('click', handleClickOutside)
    activeTab.value = 'posts'
    
    // Fetch trending tags
    fetchTrendingTags()
    
    console.log('✅ Employee Discussion Component mounted')
    console.log('📊 Initial posts:', posts.value.length)
    console.log('👤 User:', user.value)
    console.log('📝 Props:', props)
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
    max-width: 100vw;
    overflow-x: hidden;
    background: url('/assets/BG MAIN.png') no-repeat center center fixed;
    background-size: cover;
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Orange Header Bar - Similar to login/register pages */
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
    max-width: 600px;
    width: 90%;
    padding: 24px 28px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
}

.privacy-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #111827;
}

.justified-text {
    font-size: 14px;
    line-height: 1.6;
    color: #374151;
    text-align: justify;
    margin-bottom: 10px;
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
    grid-template-columns: 280px 1fr 280px;
    gap: 25px 25px;
    width: 100vw;
    max-width: 100vw;
    margin: 0;
    margin-top: 70px;
    padding: 25px 30px;
    box-sizing: border-box;
}

/* Sidebar - Enhanced styling */
.sidebar {
    background: transparent;
    padding-right: 0;
    width: 100%;
    display: flex;
    flex-direction: column;
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
    background: #239640;
    padding: 4px 12px;
    border-radius: 15px;
    display: inline-block;
    font-weight: 600;
    backdrop-filter: blur(10px);
    text-transform: uppercase;
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
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    font-weight: 500;
}

.nav-icon {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    flex-shrink: 0;
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
    transition: all 0.3s ease;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}


/* Content Area - Enhanced styling */
.content-area {
    width: 100%;
    min-width: 0;
}

.main-content-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    width: 100%;
    overflow: visible;
}

/* Main Content */
.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    flex: 1;
    border: 1px solid rgba(0,0,0,0.05);
    width: 100%;
    min-width: 0;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
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
    width: 14px;
    height: 14px;
    stroke: currentColor;
    transition: transform 0.3s ease;
    flex-shrink: 0;
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
    width: 10px;
    height: 10px;
    stroke: currentColor;
    transition: transform 0.3s ease;
    flex-shrink: 0;
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
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-btn:hover {
    background: #e9ecef;
}

.search-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    color: #666;
}

/* Posts Container - Enhanced styling */
.posts-container {
    padding: 0;
    max-height: calc(100vh - 280px);
    overflow-y: auto;
    width: 100%;
    flex: 1;
    display: block;
}

.post-card {
    border-bottom: 1px solid #f0f0f0;
    padding: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 15px; /* bump base text size for readability */
}

.post-card:first-child {
    border-radius: 0;
}

.post-card:last-child {
    border-bottom: none;
    border-radius: 0 0 15px 15px;
}

.post-card:hover {
    background: linear-gradient(135deg, #fafbfc, #f8f9fa);
    transform: translateY(-1px);
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
    font-size: 16px;
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
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    /* Default background for unmatched tags */
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

.tag:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Business - Blue/Purple */
.tag.business {
    background: linear-gradient(135deg, #6c5ce7, #5f3dc4) !important;
}

/* Education - Blue */
.tag.education {
    background: linear-gradient(135deg, #3498db, #2980b9) !important;
}

/* Emergency - Red */
.tag.emergency {
    background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
}

/* Employment - Green */
.tag.employment {
    background: linear-gradient(135deg, #27ae60, #229954) !important;
}

/* Environment - Green */
.tag.environment {
    background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
}

/* Governance - Purple */
.tag.governance {
    background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
}

/* Health - Red/Pink */
.tag.health {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Incident - Dark Red */
.tag.incident {
    background: linear-gradient(135deg, #c0392b, #a93226) !important;
}

/* Infrastructure - Orange */
.tag.infrastructure {
    background: linear-gradient(135deg, #f39c12, #e67e22) !important;
}

/* Inquiries - Yellow/Orange */
.tag.inquiries {
    background: linear-gradient(135deg, #f1c40f, #f39c12) !important;
}

/* Livelihood - Teal */
.tag.livelihood {
    background: linear-gradient(135deg, #1abc9c, #16a085) !important;
}

/* Maintenance - Brown/Orange */
.tag.maintenance {
    background: linear-gradient(135deg, #d35400, #ba4a00) !important;
}

/* Sanitation - Cyan */
.tag.sanitation {
    background: linear-gradient(135deg, #00bcd4, #0097a7) !important;
}

/* Sports - Green */
.tag.sports {
    background: linear-gradient(135deg, #4caf50, #388e3c) !important;
}

/* Traffic - Yellow */
.tag.traffic {
    background: linear-gradient(135deg, #ffc107, #ff9800) !important;
}

/* Weather - Light Blue */
.tag.weather {
    background: linear-gradient(135deg, #03a9f4, #0288d1) !important;
}

/* Welfare - Pink */
.tag.welfare {
    background: linear-gradient(135deg, #e91e63, #c2185b) !important;
}

/* Youth - Magenta */
.tag.youth {
    background: linear-gradient(135deg, #e91e63, #ad1457) !important;
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

.post-header-text {
    font-size: 20px;
    font-weight: 600;
    color: #2e2e2e;
    margin: 0 0 12px 0;
    line-height: 1.4;
    letter-spacing: -0.3px;
    text-transform: none;
}

.post-title {
    font-size: 19px;
    font-weight: 600;
    margin: 0 0 10px 0;
    color: #333;
    line-height: 1.4;
}

.post-text {
    font-size: 15px;
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

.reaction-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    flex-shrink: 0;
}

.reaction-icon-small {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    flex-shrink: 0;
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
    background: #888;
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
    color: #000;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-btn:hover {
    transform: translateY(-2px);
}

.back-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    flex-shrink: 0;
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
    border-radius: 12px;
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
    display: flex;
    align-items: center;
    gap: 6px;
}

.report-btn:hover {
    background: rgba(220, 53, 69, 0.1);
}

.report-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    flex-shrink: 0;
}

.report-icon-small {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    flex-shrink: 0;
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
    background: #888;
    border-radius: 3px;
}

.post-detail-container::-webkit-scrollbar-thumb:hover {
    background: #e6763a;
}

/* Post Images Preview in Card View */
.post-images-preview {
    display: flex;
    gap: 8px;
    margin-top: 15px;
    flex-wrap: wrap;
    position: relative;
}

.post-image-preview {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.2s ease;
}

.post-image-preview:hover {
    transform: scale(1.05);
}

.more-images-indicator {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.5);
    color: white;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
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
    display: flex;
    align-items: center;
    gap: 6px;
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
    border-radius: 12px;
    transition: all 0.2s;
}

.close-icon {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    flex-shrink: 0;
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

.report-option input[type="checkbox"] {
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

/* Trending Tags Sidebar */
.trending-tags-sidebar {
    padding-left: 0;
    display: flex;
    flex-direction: column;
    width: 100%;
}

.trending-tags-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    position: sticky;
    top: 90px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    width: 100%;
    box-sizing: border-box;
    align-self: flex-start;
}

.trending-tags-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.trending-icon {
    width: 22px;
    height: 22px;
    stroke: currentColor;
    color: #ff8c42;
    flex-shrink: 0;
}

.trending-tags-loading,
.trending-tags-empty {
    text-align: center;
    padding: 30px 15px;
    color: #999;
    font-size: 14px;
}

.trending-tags-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.trending-tag-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    width: 100%;
}

.trending-tag-item:hover {
    background: #fff7ef;
    border-color: #ff8c42;
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.2);
}

.trending-tag-item.active {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    border-color: #ff8c42;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
}

.trending-tag-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    flex: 1;
}

.trending-tag-item.active .trending-tag-name {
    color: #ff8c42;
}

.trending-tag-count {
    font-size: 12px;
    font-weight: 700;
    color: #666;
    background: white;
    padding: 4px 10px;
    border-radius: 12px;
    min-width: 30px;
    text-align: center;
}

.trending-tag-item.active .trending-tag-count {
    background: #ff8c42;
    color: white;
}

.clear-tag-filter-btn {
    width: 100%;
    margin-top: 15px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.clear-tag-filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
    background: linear-gradient(135deg, #e6763a, #e66b25);
}

/* Custom scrollbar for trending tags card */
.trending-tags-card::-webkit-scrollbar {
    width: 6px;
}

.trending-tags-card::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.trending-tags-card::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.trending-tags-card::-webkit-scrollbar-thumb:hover {
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
    
    .header-content {
        padding: 0 25px;
    }

    .trending-tags-sidebar {
        display: none;
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