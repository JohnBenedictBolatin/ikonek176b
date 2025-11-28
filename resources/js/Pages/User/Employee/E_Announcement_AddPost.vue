<script setup>
  import { Link } from '@inertiajs/vue3'
  import { Head } from '@inertiajs/vue3'
  import { ref, computed, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3'
  import LayoutEmployee  from '@/Layouts/LayoutEmployee.vue';

const postContent = ref('')
const tags = ref([])
const tagInput = ref('')
const drafts = ref([])
const showDrafts = ref(false)

const wordCount = computed(() => {
  const words = postContent.value.trim().split(/\s+/).filter(Boolean)
  return `${words.length} / 250 words`
})

function handlePostInput() {
  const words = postContent.value.trim().split(/\s+/).filter(Boolean)
  if (words.length > 250) {
    postContent.value = words.slice(0, 250).join(' ')
  }
}

function handleTagKeydown(e) {
  if (e.key === 'Enter' && tagInput.value.trim() !== '') {
    e.preventDefault()
    addTag(tagInput.value.trim())
    tagInput.value = ''
  }
}

function addTag(tagText) {
  if (!tags.value.includes(tagText)) {
    tags.value.push(tagText)
  }
}

function removeTag(index) {
  tags.value.splice(index, 1)
}

function submitPost() {
  if (!postContent.value.trim()) {
    alert('Please enter some text before submitting.')
    return
  }

  alert('Post submitted successfully!')
  resetPostForm()
}

function saveDraft() {
  if (!postContent.value.trim()) {
    alert('Nothing to save. Enter content first.')
    return
  }

  drafts.value.push({
    content: postContent.value,
    tags: [...tags.value],
  })

  showDrafts.value = true
  resetPostForm()
}

function resetPostForm() {
  postContent.value = ''
  tags.value = []
  tagInput.value = ''
}

/* Added utility methods referenced by template */
function goToAddTag() {
  // focus the tag input if present in DOM
  const el = document.querySelector('.tag-input')
  if (el) el.focus()
}

function loadDraft(i) {
  const d = drafts.value[i]
  if (!d) return
  postContent.value = d.content
  tags.value = [...d.tags]
  // Optionally remove draft after loading - keep it for now
}

function deleteDraft(i) {
  drafts.value.splice(i, 1)
  if (drafts.value.length === 0) showDrafts.value = false
}
</script>

<template>
  <LayoutEmployee>
    <div class="main-content">
      <div class="discussions-header">
        <div class="discussions-title">
          <h2>Create Announcement</h2>
        </div>
        <button class="add-tags-btn" type="button" @click="goToAddTag">ADD TAG</button>
      </div>

      <div class="add-post-section">
        <div class="back-btn">
          <Link :href="route('announcement_employee')" class="back-link">← BACK TO POST</Link>
        </div>

        <!-- Top tabs -->
        <div class="nav-menu" style="display:flex; gap:8px; padding:10px 15px; margin-bottom:20px;">
          <Link :href="route('announcement_employee')" class="nav-item" style="margin-right:8px;">ANNOUNCEMENTS</Link>
          <Link :href="route('discussion_employee')" class="nav-item">DISCUSSIONS</Link>
        </div>

        <div class="post-input-container">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <div style="font-weight:700">Create an Announcement Post</div>
            <div class="char-count">{{ wordCount }}</div>
          </div>

          <textarea
            v-model="postContent"
            placeholder="Write your post..."
            @input="handlePostInput"
            class="post-textarea"
          ></textarea>

          <div class="post-actions" style="margin-top:12px;">
            <label class="attach-btn" style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
              📎 Attach File
              <input type="file" class="file-input-hidden" />
            </label>

            <div class="tags-section" style="display:flex; align-items:center; gap:12px;">
              <div class="tags-label">Tags</div>
              <div class="tag-input-wrap" style="flex:1;">
                <input
                  v-model="tagInput"
                  placeholder="Type a tag and press Enter"
                  @keydown="handleTagKeydown"
                  class="tag-input"
                  style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #e0e0e0;"
                />
              </div>
            </div>
          </div>

          <div class="selected-tags" style="margin-top:12px;">
            <span
              v-for="(t, i) in tags"
              :key="i"
              class="tag-chip general"
              style="display:inline-flex; align-items:center; gap:8px; margin-right:8px;"
            >
              #{{ t }}
              <button class="remove-tag-btn" @click="removeTag(i)">×</button>
            </span>
          </div>

          <div style="display:flex; gap:12px; margin-top:18px;">
            <button class="publish-btn" @click="submitPost" style="width:auto; padding:10px 18px;">Submit</button>
            <button class="attach-btn" @click="saveDraft" style="background:transparent; color:#239640; border:1px solid #239640; padding:10px 18px;">Save as Draft</button>
          </div>
        </div>

        <!-- Drafts area -->
        <div class="drafts-section" v-show="showDrafts" style="margin-top:26px;">
          <div class="drafts-title">Saved Drafts</div>

          <div v-if="drafts.length === 0" class="no-drafts">No drafts saved.</div>

          <div v-for="(d, i) in drafts" :key="i" class="draft-card" style="margin-top:12px;">
            <div class="draft-text">{{ d.content }}</div>
            <div class="draft-preview" style="margin-top:8px;">
              <div class="draft-tags" style="margin-bottom:8px;">
                <span class="draft-tag" v-for="(tg, j) in d.tags" :key="j" style="margin-right:8px; font-weight:600;">#{{ tg }}</span>
              </div>
            </div>
            <div class="draft-footer" style="margin-top:12px;">
              <div class="draft-date">Draft {{ i + 1 }}</div>
              <div>
                <button class="btn-link" @click="loadDraft(i)" style="margin-right:10px; background:none; border:none; color:#239640; cursor:pointer;">Load</button>
                <button class="btn-link danger" @click="deleteDraft(i)" style="background:none; border:none; color:#dc3545; cursor:pointer;">Delete</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </LayoutEmployee>
</template>

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

