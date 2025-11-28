<x-post-layout>
    <div class="tabs">
      <a href="resident_announcement.php" class="active">ANNOUNCEMENTS</a>
      <a href="">DISCUSSIONS</a>
    </div>

    <a href="resident_discussion.php">BACK TO POSTS</a>
    <button type="button" onclick="window.location.href=''">ADD TAG</button>

    <div class="post-box">
      <textarea id="postContent" placeholder="Write your post..."></textarea>

      <div class="controls">
        <label class="attach-label">
          📎 Attach File
          <input type="file" id="fileUpload">
        </label>
        <div class="word-count" id="wordCount">0 / 250 words</div>
      </div>

      <div class="tag-input">
        <input type="text" id="tagInput" placeholder="Enter a tag and press Enter">
      </div>

      <div class="tags-display" id="tagsContainer"></div>

      <div class="action-buttons">
        <button class="submit-btn" onclick="submitPost()">Submit</button>
        <button class="draft-btn" onclick="saveDraft()">Save as Draft</button>
      </div>
    </div>

    <!-- DRAFT AREA -->
    <div class="draft-box" id="draftBox" style="display:none;">
      <h3>Saved Drafts</h3>
      <div id="draftsContainer"></div>
    </div>

    <!-- functions for draft and the text area -->
    <script>
      const postContent = document.getElementById("postContent");
      const wordCount = document.getElementById("wordCount");
      const tagInput = document.getElementById("tagInput");
      const tagsContainer = document.getElementById("tagsContainer");
      const draftBox = document.getElementById("draftBox");
      const draftsContainer = document.getElementById("draftsContainer");

      let tags = [];

      // Word Count
      postContent.addEventListener("input", () => {
        const words = postContent.value.trim().split(/\s+/).filter(Boolean);
        if (words.length > 250) {
          postContent.value = words.slice(0, 250).join(" ");
        }
        wordCount.textContent = `${words.length} / 250 words`;
      });

      // Add tag
      tagInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && this.value.trim() !== "") {
          e.preventDefault();
          const tag = this.value.trim();
          addTag(tag);
          this.value = "";
        }
      });

      function addTag(text) {
        tags.push(text);
        const span = document.createElement("span");
        span.className = "tag";
        span.textContent = `#${text}`;
        tagsContainer.appendChild(span);
      }

      function submitPost() {
        if (!postContent.value.trim()) {
          alert("Please enter some text before submitting.");
          return;
        }
        alert("Post submitted successfully!");
        postContent.value = "";
        tagsContainer.innerHTML = "";
        tags = [];
        wordCount.textContent = "0 / 250 words";
      }

      function saveDraft() {
        const text = postContent.value.trim();
        if (!text) {
          alert("Nothing to save. Enter content first.");
          return;
        }

        const draftItem = document.createElement("div");
        draftItem.className = "draft-item";
        draftItem.innerHTML = `<p>${text}</p>`;

        if (tags.length > 0) {
          const tagDiv = document.createElement("div");
          tagDiv.className = "draft-tags";
          tags.forEach(tag => {
            const t = document.createElement("span");
            t.className = "draft-tag";
            t.textContent = `#${tag}`;
            tagDiv.appendChild(t);
          });
          draftItem.appendChild(tagDiv);
        }

        draftsContainer.appendChild(draftItem);
        draftBox.style.display = "block";

        // Reset input
        postContent.value = "";
        tagsContainer.innerHTML = "";
        tags = [];
        wordCount.textContent = "0 / 250 words";
      }
    </script>
    
</x-post-layout>