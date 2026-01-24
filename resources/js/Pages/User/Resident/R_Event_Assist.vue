<template>
  <Head>
    <title>Event Assistance</title>
  </Head>

  <div class="app-container">
    <!-- Orange Header -->
    <div class="header-bar">
      <div class="header-content">
        <div class="logo-section">
          <img src="/assets/LOGO.png" alt="Logo" class="header-logo" />
        </div>
        <div class="header-actions">
          <img src="/assets/SETTINGS.png" alt="Settings" class="settings-btn-img" @click="toggleSettings" />
          <div v-if="showSettings" class="settings-dropdown">
            <Link href="#" class="settings-item" @click="closeSettings">Help Center</Link>
            <button type="button" class="settings-item" @click="openTerms">Terms & Conditions</button>
            <Link href="#" class="settings-item" @click="logout">Sign Out</Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="profile-card">
          <img :src="profilePictureUrl" alt="Profile" class="profile-avatar" />
          <div class="profile-info">
            <div class="profile-name">{{ user?.name || 'Unknown User' }}</div>
            <div class="profile-role">{{ displayRole }}</div>
          </div>
        </div>

        <div class="nav-menu">
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'posts' }" @click="navigateToPosts">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            Posts
          </Link>
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'documents' }" @click="navigateToDocuments">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            Document Request
          </Link>
          <Link href="#" class="nav-item active" :class="{ active: activeTab === 'events' }" @click="setActiveTab('events')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            Event Assistance
          </Link>
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'notifications' }" @click="navigateToNotifications">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="nav-icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            Notifications
          </Link>
          <Link href="#" class="nav-item" :class="{ active: activeTab === 'profile' }" @click="navigateToProfile">
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
        <div class="main-content">
          <!-- Header -->
          <div class="event-header">
            <h2>Event Assistance</h2>
            <div class="header-icon">
              <img src="/assets/ICON.png" alt="iKONEK" class="small-logo" />
            </div>
          </div>

          <!-- View 0: Requests List (Default) -->
          <div v-if="currentView === 'list'" class="requests-list-view">
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
                    <button @click="selectSort('oldest')" :class="{ active: sortOption === 'oldest' }">OLDEST</button>
                  </div>
                </div>
                <div class="filter-dropdown-wrapper" style="margin-left: 15px;">
                  <button class="filter-dropdown-btn" @click="toggleStatusDropdown">
                    {{ statusFilter === 'all' ? 'ALL STATUS' : statusFilter.toUpperCase() }}
                    <span class="filter-arrow" :class="{ rotated: showStatusDropdown }">▼</span>
                  </button>
                  <div v-if="showStatusDropdown" class="filter-dropdown-menu">
                    <button @click="selectStatus('all')" :class="{ active: statusFilter === 'all' }">ALL STATUS</button>
                    <button @click="selectStatus('pending')" :class="{ active: statusFilter === 'pending' }">PENDING</button>
                    <button @click="selectStatus('approved')" :class="{ active: statusFilter === 'approved' }">APPROVED</button>
                    <button @click="selectStatus('rejected')" :class="{ active: statusFilter === 'rejected' }">REJECTED</button>
                  </div>
                </div>
              </div>
              <div class="filter-right">
                <div class="search-container">
                  <input 
                    type="text" 
                    v-model="searchQuery" 
                    @input="performSearch"
                    placeholder="SEARCH..." 
                    class="search-input" 
                  />
                  <button class="search-btn" @click="performSearch">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="search-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                  </button>
                </div>
                <button class="request-new-btn" @click="showRequestForm">
                  ＋ REQUEST EVENT ASSISTANCE
                </button>
              </div>
            </div>

            <div class="requests-table-container">
              <table class="requests-table">
                <thead>
                  <tr>
                    <th>Event Assistance</th>
                    <th>Request Number</th>
                    <th>Date and Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="request in filteredEventRequests" 
                    :key="request.id"
                    @click="viewRequestDetails(request)"
                    class="request-row"
                  >
                    <td>{{ request.title }}</td>
                    <td>#{{ request.requestNumber }}</td>
                    <td>{{ request.date }} | {{ request.time }}</td>
                    <td>
                      <span class="status-badge" :class="request.status.toLowerCase()">
                        <span class="badge-icon">
                          <template v-if="request.status.toLowerCase() === 'pending'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="badge-icon-svg">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                          </template>
                          <template v-else-if="request.status.toLowerCase() === 'approved'">✓</template>
                          <template v-else-if="request.status.toLowerCase() === 'rejected'">✕</template>
                        </span>
                        <span class="badge-text">{{ request.status }}</span>
                      </span>
                    </td>
                  </tr>
                  <tr v-if="filteredEventRequests.length === 0">
                    <td colspan="4" class="no-requests">No event assistance requests found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- View 1: Event Selection -->
          <div v-if="currentView === 'selection'" class="event-selection">
            <div class="event-types-wrapper">
              <button class="back-btn-selection" @click="backToList">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                BACK TO REQUESTS
              </button>
              <div class="event-types">
                <!-- Render event checkboxes -->
                <label
                  v-for="eventName in eventNames"
                  :key="eventName"
                  class="event-type-checkbox-label"
                  :class="{ active: selectedEventTypes.includes(eventName) }"
                >
                  <input
                    type="checkbox"
                    :value="eventName"
                    v-model="selectedEventTypes"
                    class="event-type-checkbox"
                    @change="handleEventTypeToggle(eventName)"
                  />
                  <span class="event-type-checkbox-text">{{ eventName }}</span>
                </label>
              </div>
            </div>

            <div class="event-info">
              <h3 class="event-title" v-if="selectedEventTypes.length === 0">Select Event Types</h3>
              <h3 class="event-title" v-else-if="selectedEventTypes.length === 1">{{ selectedEventTypes[0] }}</h3>
              <h3 class="event-title" v-else>{{ selectedEventTypes.length }} Event Types Selected</h3>
              
              <div v-if="selectedEventTypes.length > 0" class="selected-events-summary">
                <p class="selected-count">Selected: <strong>{{ selectedEventTypes.join(', ') }}</strong></p>
              </div>

              <!-- Show descriptions for all selected event types -->
              <div v-if="selectedEventTypes.length > 0" class="event-descriptions-list">
                <div v-for="eventName in selectedEventTypes" :key="eventName" class="event-description-item">
                  <h4 class="event-description-title">{{ eventName }}</h4>
                  <div class="event-description">
                    <p>{{ eventDescriptions[eventName] }}</p>
                  </div>
                  
                  <!-- Court Type Selection for Court Reservation -->
                  <div v-if="eventName === 'Court Reservation'" class="court-type-selection">
                    <label class="court-type-label">Court Type <span class="required-star">*</span></label>
                    <select 
                      :value="(form.court_types && form.court_types[eventName]) || ''"
                      @change="updateCourtType(eventName, $event.target.value)"
                      class="form-input court-type-select" 
                      required
                    >
                      <option value="">Select Court Type</option>
                      <option value="Open">Open Court</option>
                      <option value="Covered">Covered Court</option>
                    </select>
                  </div>

                  <div class="requirements-section">
                    <h4>REQUIREMENTS</h4>
                    <ol class="requirements-list">
                      <li v-for="(req, index) in eventRequirements[eventName]" :key="index">
                        {{ req }}
                      </li>
                    </ol>
                  </div>
                </div>
              </div>

              <div v-else class="no-selection-message">
                <p>Please select one or more event types from the list on the left.</p>
              </div>

              <!-- Terms and Conditions -->
              <div class="terms-section">
                <h4 class="terms-title">TERMS AND CONDITIONS</h4>
                <div class="terms-content" ref="termsContainer" @scroll="checkTermsScroll">
                  <p><strong>1. Reservation Policy:</strong> Court reservations are subject to availability and must be made at least 3 days in advance. Reservations are on a first-come, first-served basis.</p>
                  <p><strong>2. Cancellation Policy:</strong> Cancellations must be made at least 24 hours before the scheduled event. Failure to cancel may result in restrictions on future reservations.</p>
                  <p><strong>3. Usage Guidelines:</strong> Users are responsible for maintaining cleanliness and order during their event. Any damage to facilities will be charged to the reserving party.</p>
                  <p><strong>4. Time Restrictions:</strong> Events must end by 10:00 PM. Extended use requires special permission from barangay officials.</p>
                  <p><strong>5. Prohibited Activities:</strong> Smoking, alcohol consumption, gambling, and any illegal activities are strictly prohibited within the court premises.</p>
                  <p><strong>6. Liability:</strong> The barangay is not responsible for any personal injury or property damage that may occur during the event. Users participate at their own risk.</p>
                  <p><strong>7. Compliance:</strong> All users must comply with barangay ordinances and regulations. Violations may result in immediate termination of reservation and future restrictions.</p>
                  <p><strong>8. Documentation:</strong> Valid identification and supporting documents must be provided. False information will result in request rejection.</p>
                  <p><strong>9. Approval Process:</strong> All requests are subject to review and approval by barangay officials. Approval is not guaranteed and may take 2-3 business days.</p>
                </div>
                <div class="terms-checkbox-wrapper">
                  <label class="terms-checkbox-label">
                    <input 
                      type="checkbox" 
                      v-model="termsAccepted" 
                      :disabled="!hasScrolledToBottom"
                      class="terms-checkbox"
                    />
                    <span>I have read and agree to the Terms and Conditions</span>
                  </label>
                </div>
              </div>

              <button 
                class="request-btn" 
                :disabled="!termsAccepted || !canProceed" 
                @click="proceedToForm"
                :class="{ disabled: !termsAccepted || !canProceed }"
              >
                REQUEST ASSISTANCE
              </button>
            </div>
          </div>

          <!-- View 2: Request Form -->
          <div v-if="currentView === 'form'" class="request-form-container">
            <div class="form-header-row">
              <button class="back-btn" @click="backToSelection">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 6px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                BACK TO EVENT SELECTION
              </button>
              <p class="form-document-type">Event Type(s): <strong>{{ selectedEventTypes.join(', ') }}</strong></p>
            </div>

            <h3 class="form-title">REQUEST FORM</h3>

            <div class="form-sections">
              <!-- Event Details -->
              <div class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                  </svg>
                  Event Details
                </h4>
                
                <div class="event-details-grid">
                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Event Date <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="date" v-model="form.event_date" :min="today" class="form-input" required />
                    </div>
                  </div>

                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Event Time <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <div class="time-input-container">
                        <div class="time-input-group">
                          <label class="time-label">Event Start</label>
                          <div class="time-input-wrapper">
                            <input
                              ref="startTimeInput"
                              type="time"
                              v-model="form.event_time"
                              class="form-input time-input"
                              aria-label="Event Start"
                              required
                            />
                          </div>
                        </div>
                        <div class="time-separator">to</div>
                        <div class="time-input-group">
                          <label class="time-label">Event End</label>
                          <div class="time-input-wrapper">
                            <input
                              ref="endTimeInput"
                              type="time"
                              v-model="form.end_time"
                              class="form-input time-input"
                              aria-label="Event End"
                              required
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Expected Attendees <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="number" v-model.number="form.attendees" placeholder="Enter number of attendees" class="form-input" required />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Valid ID Upload Section (like document requests) - Show for ALL event types -->
              <div class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                  </svg>
                  Valid Identification <span class="required-star">*</span>
                </h4>
                
                <div class="upload-section">
                  <!-- ID Type select -->
                  <select v-model="form.id_type" class="form-input upload-select">
                    <option value="">Type of Identification</option>
                    <option value="National ID">National ID</option>
                    <option value="Driver's License">Driver's License</option>
                    <option value="Passport">Passport</option>
                    <option value="Voter's ID">Voter's ID</option>
                    <option value="SSS ID">SSS ID</option>
                    <option value="UMID">UMID</option>
                  </select>

                  <!-- Upload front -->
                  <div class="upload-row">
                    <button class="upload-btn" @click.prevent="triggerFileUpload('front')">
                      UPLOAD FRONT
                    </button>
                    <input 
                      type="file" 
                      ref="fileFrontInput" 
                      @change="handleFileUpload($event, 'front')" 
                      class="file-input-hidden"
                      accept="image/*,.pdf"
                      style="display: none"
                    />

                    <button class="upload-btn" @click.prevent="triggerFileUpload('back')">
                      UPLOAD BACK
                    </button>
                    <input 
                      type="file" 
                      ref="fileBackInput" 
                      @change="handleFileUpload($event, 'back')" 
                      class="file-input-hidden"
                      accept="image/*,.pdf"
                      style="display: none"
                    />
                  </div>
                </div>

                <!-- ID Front Preview -->
                <div v-if="idFrontName" class="uploaded-file-info-compact" style="margin-top: 10px;">
                    <svg class="file-checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 18px; height: 18px; color: #4caf50;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="file-name-compact">{{ idFrontName }}</span>
                    <button 
                        type="button"
                        class="remove-file-btn-small" 
                        @click="removeIdFile('front')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- ID Back Preview -->
                <div v-if="idBackName" class="uploaded-file-info-compact" style="margin-top: 10px;">
                    <svg class="file-checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 18px; height: 18px; color: #4caf50;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="file-name-compact">{{ idBackName }}</span>
                    <button 
                        type="button"
                        class="remove-file-btn-small" 
                        @click="removeIdFile('back')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- ID number field appears only when an ID type is chosen AND at least one file uploaded -->
                <div v-if="showIdNumber" class="id-number-field" style="margin-top: 12px;">
                  <label class="field-label">{{ idNumberLabel }} <span>*</span></label>
                  <input
                    type="text"
                    v-model="form.valid_id_number"
                    placeholder="Enter ID Number"
                    class="form-input"
                    required
                  />
                </div>
              </div>

              <!-- Manpower Assistance - Specific Fields -->
              <div v-if="selectedEventTypes.includes('Manpower Assistance')" class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                  </svg>
                  Manpower Details
                </h4>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Number of Manpower Needed <span class="required-star">*</span>
                    </label>
                    <p class="field-description">Enter the total number of personnel needed for your event</p>
                  </div>
                  <div class="field-input-wrapper">
                    <input 
                      type="number" 
                      v-model.number="form.extra_fields.manpower_count" 
                      placeholder="Enter number of manpower needed" 
                      class="form-input" 
                      min="1" 
                      required 
                    />
                  </div>
                </div>
              </div>

              <!-- Purpose and Documents - Generic for most events -->
              <div v-if="selectedEventTypes.some(type => !['Tent and Tables Borrowing', 'Sports Equipment Borrowing', 'Sound System Borrowing', 'Manpower Assistance'].includes(type))" class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Purpose and Supporting Documents
                </h4>

                <div class="purpose-documents-grid">
                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Purpose of Request <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <select v-model="form.purpose" class="form-input" required>
                        <option value="">Select purpose of request</option>
                        <option v-for="(opt, idx) in purposeOptions" :key="idx" :value="opt">{{ opt }}</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                  </div>

                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Supporting Documents <span class="required-star">*</span>
                      </label>
                      <p class="field-description">Upload supporting documents for your event request</p>
                    </div>
                    <div class="field-input-wrapper">
                      <div class="upload-section">
                        <select v-model="form.document_type" class="form-input upload-select" required>
                          <option value="">Type of Supporting Document</option>
                          <option v-for="doc in relevantSupportingDocuments" :key="doc" :value="doc">{{ doc }}</option>
                        </select>

                        <button class="upload-btn" @click="triggerMainFileUpload">UPLOAD</button>
                        <input
                          type="file"
                          ref="mainFileInput"
                          @change="handleMainFileUpload"
                          class="file-input-hidden"
                          accept="image/*,.pdf"
                          multiple
                          style="display:none"
                        />
                      </div>

                      <div v-if="mainUploadedNames.length > 0" class="uploaded-files-list" style="margin-top: 10px;">
                        <div v-for="(file, index) in mainUploadedNames" :key="index" class="uploaded-file-info-compact" style="margin-bottom: 8px;">
                          <svg class="file-checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="width: 18px; height: 18px; color: #4caf50;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                          <span class="file-name-compact">{{ file }}</span>
                          <button 
                            type="button"
                            class="remove-file-btn-small" 
                            @click="removeMainFile(index)"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="form.purpose === 'Other'" class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Specify Purpose <span class="required-star">*</span>
                    </label>
                    <p class="field-description">Maximum 255 characters</p>
                  </div>
                  <div class="field-input-wrapper">
                    <input 
                      type="text" 
                      v-model="form.others" 
                      placeholder="Please specify the purpose" 
                      class="form-input" 
                      maxlength="255"
                      required 
                    />
                    <p v-if="form.others" class="char-count" :class="{ 'char-warning': form.others.length > 200 }">
                      {{ form.others.length }}/255 characters
                    </p>
                  </div>
                </div>
              </div>

              <!-- Tent and Tables Borrowing - Specific Fields -->
              <div v-if="selectedEventTypes.includes('Tent and Tables Borrowing')" class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                  </svg>
                  Equipment Details
                </h4>

                <div class="purpose-documents-grid">
                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Number of Tents Needed <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="number" v-model.number="form.extra_fields.tent_count" placeholder="Enter number of tents" class="form-input" min="0" required />
                    </div>
                  </div>

                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Number of Tables Needed <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="number" v-model.number="form.extra_fields.table_count" placeholder="Enter number of tables" class="form-input" min="0" required />
                    </div>
                  </div>
                </div>

                <div class="purpose-documents-grid">
                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Number of Chairs Needed <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="number" v-model.number="form.extra_fields.chair_count" placeholder="Enter number of chairs" class="form-input" min="0" required />
                    </div>
                  </div>

                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Purpose of Use <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <select v-model="form.extra_fields.purpose_of_use" class="form-input" required>
                        <option value="">Select purpose of use</option>
                        <option value="Personal Event">Personal Event</option>
                        <option value="Family Gathering">Family Gathering</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Community Event">Community Event</option>
                        <option value="Religious Activity">Religious Activity</option>
                        <option value="Cultural Activity">Cultural Activity</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div v-if="form.extra_fields.purpose_of_use === 'Other'" class="dynamic-field-wrapper" style="margin-top: 15px;">
                  <div class="field-header">
                    <label class="field-label">
                      Specify Purpose <span class="required-star">*</span>
                    </label>
                  </div>
                  <div class="field-input-wrapper">
                    <input type="text" v-model="form.extra_fields.other_purpose_of_use" placeholder="Please specify the purpose" class="form-input" :required="form.extra_fields.purpose_of_use === 'Other'" />
                  </div>
                </div>
              </div>

              <!-- Sports Equipment Borrowing - Specific Fields -->
              <div v-if="selectedEventTypes.includes('Sports Equipment Borrowing')" class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                  </svg>
                  Equipment Details
                </h4>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Type of Sport <span class="required-star">*</span>
                    </label>
                  </div>
                  <div class="field-input-wrapper">
                    <select v-model="form.extra_fields.sport_type" class="form-input" required>
                      <option value="">Select Sport Type</option>
                      <option value="Basketball">Basketball</option>
                      <option value="Volleyball">Volleyball</option>
                      <option value="Badminton">Badminton</option>
                      <option value="Table Tennis">Table Tennis</option>
                      <option value="Chess">Chess</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Specific Equipment Needed <span class="required-star">*</span>
                    </label>
                    <p class="field-description">List the specific equipment items you need (e.g., 2 basketballs, 1 volleyball net, etc.)</p>
                  </div>
                  <div class="field-input-wrapper">
                    <textarea v-model="form.extra_fields.equipment_list" placeholder="Example: 2 basketballs, 1 volleyball, 1 badminton racket" rows="4" class="form-textarea" required></textarea>
                  </div>
                </div>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Purpose of Use <span class="required-star">*</span>
                    </label>
                    <p class="field-description">Select the purpose for borrowing these equipment</p>
                  </div>
                  <div class="field-input-wrapper">
                    <select v-model="form.extra_fields.purpose_of_use" class="form-input" required>
                      <option value="">Select purpose of use</option>
                      <option value="Sports Tournament">Sports Tournament</option>
                      <option value="Practice Session">Practice Session</option>
                      <option value="Training Activity">Training Activity</option>
                      <option value="Recreational Activity">Recreational Activity</option>
                      <option value="School Event">School Event</option>
                      <option value="Community Sports Event">Community Sports Event</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
                
                <div v-if="form.extra_fields.purpose_of_use === 'Other'" class="dynamic-field-wrapper" style="margin-top: 15px;">
                  <div class="field-header">
                    <label class="field-label">
                      Specify Purpose <span class="required-star">*</span>
                    </label>
                  </div>
                  <div class="field-input-wrapper">
                    <input type="text" v-model="form.extra_fields.other_purpose_of_use" placeholder="Please specify the purpose" class="form-input" :required="form.extra_fields.purpose_of_use === 'Other'" />
                  </div>
                </div>
              </div>

              <!-- Sound System Borrowing - Specific Fields -->
              <div v-if="selectedEventTypes.includes('Sound System Borrowing')" class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                  </svg>
                  Equipment Details
                </h4>

                <div class="purpose-documents-grid">
                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Number of Speakers Needed <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="number" v-model.number="form.extra_fields.speaker_count" placeholder="Enter number of speakers" class="form-input" min="0" required />
                    </div>
                  </div>

                  <div class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Number of Microphones Needed <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input type="number" v-model.number="form.extra_fields.microphone_count" placeholder="Enter number of microphones" class="form-input" min="0" required />
                    </div>
                  </div>
                </div>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Additional Equipment Needed <span class="required-star">*</span>
                    </label>
                    <p class="field-description">Specify any additional audio equipment needed (e.g., mixer, cables, stands, etc.)</p>
                  </div>
                  <div class="field-input-wrapper">
                    <textarea v-model="form.extra_fields.additional_equipment" placeholder="Example: Audio mixer, extension cables, microphone stands" rows="3" class="form-textarea" required></textarea>
                  </div>
                </div>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Purpose of Use <span class="required-star">*</span>
                    </label>
                  </div>
                  <div class="field-input-wrapper">
                    <select v-model="form.extra_fields.purpose_of_use" class="form-input" required>
                      <option value="">Select purpose of use</option>
                      <option value="Community Event">Community Event</option>
                      <option value="Religious Activity">Religious Activity</option>
                      <option value="Cultural Activity">Cultural Activity</option>
                      <option value="Announcement">Announcement</option>
                      <option value="Meeting">Meeting</option>
                      <option value="Celebration">Celebration</option>
                      <option value="Other">Other</option>
                    </select>
                    
                    <div v-if="form.extra_fields.purpose_of_use === 'Other'" style="margin-top: 10px;">
                      <input type="text" v-model="form.extra_fields.other_purpose_of_use" placeholder="Please specify the purpose" class="form-input" :required="form.extra_fields.purpose_of_use === 'Other'" />
                    </div>
                  </div>
                </div>

                <div class="dynamic-field-wrapper">
                  <div class="field-header">
                    <label class="field-label">
                      Need Technical Assistance? <span class="required-star">*</span>
                    </label>
                  </div>
                  <div class="field-input-wrapper">
                    <select v-model="form.extra_fields.need_technician" class="form-input" required>
                      <option value="">Select</option>
                      <option value="Yes">Yes, I need technical assistance</option>
                      <option value="No">No, I can handle it myself</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Event Location - Conditional based on event type -->
              <div class="form-section">
                <h4 class="section-title">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                  </svg>
                  Location Details
                </h4>

                <!-- Location fields for each selected event type -->
                <template v-for="eventType in selectedEventTypes" :key="eventType">
                  <div v-if="eventType === 'Court Reservation'" class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Court Location ({{ eventType }}) <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <select 
                        v-model="form.locations[eventType]" 
                        class="form-input" 
                        required
                      >
                        <option value="">Select Court Location</option>
                        <!-- Open Court Options -->
                        <option v-if="form.court_types && form.court_types[eventType] === 'Open'" value="Bagsak Open Court">Bagsak Open Court</option>
                        <option v-if="form.court_types && form.court_types[eventType] === 'Open'" value="Phase 5 Open Court">Phase 5 Open Court</option>
                        <option v-if="form.court_types && form.court_types[eventType] === 'Open'" value="Phase 2 Open Court">Phase 2 Open Court</option>
                        <!-- Covered Court Options -->
                        <option v-if="form.court_types && form.court_types[eventType] === 'Covered'" value="Phase 2 Covered Court">Phase 2 Covered Court</option>
                        <option v-if="form.court_types && form.court_types[eventType] === 'Covered'" value="Phase 5Y Covered Court">Phase 5Y Covered Court</option>
                      </select>
                    </div>
                  </div>

                  <div v-else-if="eventType === 'Community Hall Reservation'" class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Community Hall Location ({{ eventType }}) <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <select 
                        v-model="form.locations[eventType]" 
                        class="form-input" 
                        required
                      >
                        <option value="">Select Community Hall Location</option>
                        <option value="Phase 2 Community Hall">Phase 2 Community Hall</option>
                        <option value="Phase 5 Community Hall">Phase 5 Community Hall</option>
                        <option value="Phase 5Y Community Hall">Phase 5Y Community Hall</option>
                      </select>
                    </div>
                  </div>

                  <div v-else class="dynamic-field-wrapper">
                    <div class="field-header">
                      <label class="field-label">
                        Event Location ({{ eventType }}) <span class="required-star">*</span>
                      </label>
                    </div>
                    <div class="field-input-wrapper">
                      <input 
                        type="text" 
                        v-model="form.locations[eventType]" 
                        :placeholder="`Enter location for ${eventType}`" 
                        class="form-input" 
                        required
                      />
                    </div>
                  </div>
                </template>
              </div>

            </div>

            <button class="submit-btn" :disabled="isSubmitting" @click="submitRequest">
              <span v-if="!isSubmitting">SUBMIT REQUEST</span>
              <span v-else>Submitting...</span>
            </button>
          </div>

          <!-- View 3: Success Confirmation -->
          <div v-if="currentView === 'success'" class="success-container">
            <div class="success-content">
              <div class="checkmark">
                ✓
              </div>
              <h3 class="success-title">REQUEST SUBMITTED</h3>
              <p class="request-number">REQUEST NO. #{{ requestNumber }}</p>

              <div class="success-message">
                <p>You have successfully submitted your request for <span class="highlight">{{ selectedEventTypes.join(', ') }}</span>.
                </p>
                <p>Your request is now being reviewed by the barangay officials. We will contact you within 2-3 business days regarding the status of your application.</p>
                <p>Your reference number is <span class="highlight">#{{ requestNumber }}</span>. Please keep this for tracking purposes.</p>
              </div>

              <button class="view-request-btn" @click="viewRequest">VIEW REQUEST</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Request Details Modal -->
  <div v-if="showDetailsModal" class="modal-overlay" @click="closeDetailsModal">
    <div class="modal-content details-modal" @click.stop>
      <button class="close-modal-btn" @click="closeDetailsModal">✕</button>
      
      <!-- Success/Status Header -->
      <div class="modal-icon" :class="selectedRequest?.status.toLowerCase() + '-icon'">
        <div class="status-badge">
          <span v-if="selectedRequest?.status === 'APPROVED'">✓</span>
          <span v-if="selectedRequest?.status === 'PENDING'">⏱</span>
          <span v-if="selectedRequest?.status === 'REJECTED'">✕</span>
        </div>
      </div>
      
      <h3 class="modal-title">REQUEST {{ selectedRequest?.status }}</h3>
      <p class="request-number-display">REQUEST NO. #{{ selectedRequest?.requestNumber }}</p>
      
      <div class="details-content">
        <!-- Request Message -->
        <p class="details-message">
          Your request for <strong>{{ selectedRequest?.title }}</strong> has been 
          <strong>{{ selectedRequest?.status.toLowerCase() }}</strong>.
        </p>
        
        <!-- APPROVED Status -->
        <div v-if="selectedRequest?.status === 'APPROVED'" class="accepted-section">
          <p class="details-message">
            Your event assistance request has been approved.
          </p>
          
          <div class="request-info-box">
            <div class="info-item">
              <span class="info-label">EVENT TYPE:</span>
              <span class="info-value">{{ selectedRequest?.title }}</span>
            </div>
            <div v-if="selectedRequest?.event_location" class="info-item">
              <span class="info-label">LOCATION:</span>
              <span class="info-value">{{ selectedRequest.event_location }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">SUBMITTED ON:</span>
              <span class="info-value">{{ selectedRequest?.date }}, {{ selectedRequest?.time }}</span>
            </div>
          </div>
          
          <p class="present-message">
            <strong>Present this request number:</strong><br/>
            <span class="highlight-number">#{{ selectedRequest?.requestNumber }}</span>
          </p>
        </div>
        
        <!-- PENDING Status -->
        <div v-if="selectedRequest?.status === 'PENDING'" class="pending-section">
          <p class="details-message">
            Your request is currently being reviewed by the barangay officials. You will be notified once a decision has been made.
          </p>
          
          <div class="request-info-box">
            <div class="info-item">
              <span class="info-label">SUBMITTED ON:</span>
              <span class="info-value">{{ selectedRequest?.date }}, {{ selectedRequest?.time }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">REQUEST TYPE:</span>
              <span class="info-value">{{ selectedRequest?.title }}</span>
            </div>
            <div v-if="selectedRequest?.event_location" class="info-item">
              <span class="info-label">EVENT LOCATION:</span>
              <span class="info-value">{{ selectedRequest.event_location }}</span>
            </div>
          </div>
          
          <p class="note-message">
            <strong>Note:</strong> Processing time typically takes 2-3 business days. Thank you for your patience.
          </p>
        </div>
        
        <!-- REJECTED Status -->
        <div v-if="selectedRequest?.status === 'REJECTED'" class="rejected-section">
          <p class="details-message">
            Unfortunately, your request has been rejected. Please review the reason below and contact the barangay office if you have questions.
          </p>
          
          <div class="rejection-box">
            <h4>Reason for Rejection:</h4>
            <p>{{ getRejectionReason(selectedRequest) }}</p>
          </div>
          
          <div class="request-info-box">
            <div class="info-item">
              <span class="info-label">SUBMITTED ON:</span>
              <span class="info-value">{{ selectedRequest?.date }}, {{ selectedRequest?.time }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">REQUEST TYPE:</span>
              <span class="info-value">{{ selectedRequest?.title }}</span>
            </div>
          </div>
          
          <p class="note-message">
            You may submit a new request with the correct information or contact Ms. Mercy Alpaño at the barangay hall for assistance.
          </p>
        </div>
        
        <!-- Admin Feedback Section -->
        <div v-if="selectedRequest?.admin_feedback || selectedRequest?.raw?.admin_feedback" class="feedback-section">
          <h4 class="section-title-feedback">
            <span class="icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
              </svg>
            </span> Approver Comment
          </h4>
          <div class="feedback-box">
            <p>{{ selectedRequest?.admin_feedback ?? selectedRequest?.raw?.admin_feedback }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Terms & Conditions Modal -->
  <TermsModal :open="showTerms" @close="closeTerms" />
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import TermsModal from '@/Components/TermsModal.vue'

// ensure axios uses X-Requested-With and CSRF (Laravel default)
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
if (csrf) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf

// --- Inertia-shared auth user ---
const page = usePage()
const user = computed(() => page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null)

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
    const version = pic ? pic.split('/').pop() + pic.length : Date.now()
    return url + (url.includes('?') ? '&' : '?') + `v=${version}`
  }
  return '/assets/DEFAULT.jpg'
})

// role map
const roleMap = {
  1: 'Resident', 2: 'Barangay Captain', 3: 'Barangay Secretary',
  4: 'Barangay Treasurer', 5: 'Barangay Kagawad',
  6: 'Sangguniang Kabataan Chairman', 7: 'Sangguniang Kabataan Kagawad',
  9: 'System Admin'
}
const displayRole = computed(() => {
  const id = user.value?.fk_role_id ?? user.value?.role_id ?? null
  const role = id ? (roleMap[id] ?? `Role ${id}`) : 'Resident'
  return role.toUpperCase()
})

// UI state
const showSettings = ref(false)
const activeTab = ref('events')
const currentView = ref('list') // Default to 'list' view to show submitted requests
const selectedEventTypes = ref([]) // Changed to array for multiple selections
const selectedEventType = computed(() => selectedEventTypes.value.length > 0 ? selectedEventTypes.value[0] : 'Court Reservation') // Keep for backward compatibility
const requestNumber = ref('')
const isSubmitting = ref(false)

// Filter state
const showSortDropdown = ref(false)
const showStatusDropdown = ref(false)
const sortOption = ref('newest')
const statusFilter = ref('all')
const searchQuery = ref('')

// Request details modal state
const showDetailsModal = ref(false)
const selectedRequest = ref(null)

// Get props from Inertia page
const eventRequestsRaw = computed(() => {
  return page?.props?.value?.eventAssistanceRequests ?? page?.props?.eventAssistanceRequests ?? page?.props?.value?.event_assistance_requests ?? page?.props?.event_assistance_requests ?? []
})

// Map event assistance requests into normalized shape
const mappedEventRequests = computed(() => {
  const server = eventRequestsRaw.value || []
  if (server.length === 0) return []

  return (Array.isArray(server) ? server : []).map((r) => {
    const createdAt = r.created_at
    let timestamp = null
    
    if (createdAt) {
      try { 
        timestamp = new Date(createdAt)
        if (isNaN(timestamp.getTime())) timestamp = null
      } catch (e) { 
        timestamp = null 
      }
    }

    let dateStr = ''
    let timeStr = ''
    if (timestamp && !isNaN(timestamp.getTime())) {
      try {
        dateStr = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(timestamp)
        timeStr = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }).format(timestamp)
      } catch (e) {
        dateStr = timestamp.toLocaleDateString()
        timeStr = timestamp.toLocaleTimeString()
      }
    } else {
      dateStr = 'N/A'
      timeStr = 'N/A'
    }

    const status = (r.status ?? 'PENDING').toString().toUpperCase()
    const title = r.title ?? r.event_type ?? 'Event Assistance Request'
    const requestNumber = r.eventassist_request_ticket ?? `EA-${r.event_assist_request_id}`

    return {
      id: r.event_assist_request_id,
      requestNumber,
      title,
      status,
      date: dateStr,
      time: timeStr,
      type: 'event',
      event_location: r.event_location ?? null,
      admin_feedback: r.admin_feedback ?? null,
      raw: r,
    }
  })
})

// Filtered event requests
const filteredEventRequests = computed(() => {
  let filtered = [...mappedEventRequests.value]

  // Filter by status
  if (statusFilter.value !== 'all') {
    filtered = filtered.filter(request => 
      request.status.toLowerCase() === statusFilter.value.toLowerCase()
    )
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(request => {
      const title = (request.title || '').toLowerCase()
      const requestNumber = (request.requestNumber || '').toLowerCase()
      const date = (request.date || '').toLowerCase()
      const status = (request.status || '').toLowerCase()
      return title.includes(query) || 
             requestNumber.includes(query) || 
             date.includes(query) || 
             status.includes(query)
    })
  }

  // Sort
  if (sortOption.value === 'newest') {
    filtered.sort((a, b) => {
      const dateA = a.raw?.created_at ? new Date(a.raw.created_at) : new Date(0)
      const dateB = b.raw?.created_at ? new Date(b.raw.created_at) : new Date(0)
      return dateB - dateA
    })
  } else if (sortOption.value === 'oldest') {
    filtered.sort((a, b) => {
      const dateA = a.raw?.created_at ? new Date(a.raw.created_at) : new Date(0)
      const dateB = b.raw?.created_at ? new Date(b.raw.created_at) : new Date(0)
      return dateA - dateB
    })
  }

  return filtered
})

// Filter functions
const toggleSortDropdown = () => {
  showSortDropdown.value = !showSortDropdown.value
  showStatusDropdown.value = false
}

const toggleStatusDropdown = () => {
  showStatusDropdown.value = !showStatusDropdown.value
  showSortDropdown.value = false
}

const selectSort = (option) => {
  sortOption.value = option
  showSortDropdown.value = false
}

const selectStatus = (status) => {
  statusFilter.value = status
  showStatusDropdown.value = false
}

const performSearch = () => {
  // Search is handled by computed property automatically
}

// Court options removed - using dropdown instead

// Terms and conditions
const termsAccepted = ref(false)
const hasScrolledToBottom = ref(false)
const termsContainer = ref(null)

const canProceed = computed(() => {
  // Check if terms are accepted and scrolled to bottom
  if (!termsAccepted.value || !hasScrolledToBottom.value) {
    return false
  }
  
  // If Court Reservation is selected, check if court type is selected
  if (selectedEventTypes.value.includes('Court Reservation')) {
    const courtType = form.court_types && form.court_types['Court Reservation']
    if (!courtType) {
      return false
    }
  }
  
  // At least one event type must be selected
  if (selectedEventTypes.value.length === 0) {
    return false
  }
  
  return true
})

const checkTermsScroll = () => {
  if (!termsContainer.value) return
  const container = termsContainer.value
  const scrollTop = container.scrollTop
  const scrollHeight = container.scrollHeight
  const clientHeight = container.clientHeight
  
  // Check if scrolled to bottom (with 10px tolerance)
  if (scrollTop + clientHeight >= scrollHeight - 10) {
    hasScrolledToBottom.value = true
  }
}

// main file input ref + names
const mainFileInput = ref(null)
const mainUploadedNames = ref([])

// ID upload refs and names (like document requests)
const fileFrontInput = ref(null)
const fileBackInput = ref(null)
const idFrontName = ref('')
const idBackName = ref('')

// dynamic file names container
const dynamicFileNames = ref({})

// basic helper date
const today = new Date().toISOString().split('T')[0]

// treat fields whose name contains 'birth' as future-date fields (no past selection)
const isFutureDateField = (field) => {
  if (!field || !field.name) return false
  return String(field.name).toLowerCase().includes('birth')
}

// PURPOSE options
const purposeOptions = [
  'Personal Celebration', 'Sports Activity', 'Barangay Escort', 'Community Event', 'Religious or Cultural Activity', 'Logistical Support'
]

// Use Inertia form so nested file uploads are handled (forceFormData true on post)
// NOTE: duration removed from initial state; end_time added
// Updated to support multiple event types
const form = useForm({
  event_types: [], // Array of selected event types
  event_type: '', // Keep for backward compatibility, will be comma-separated string
  purpose: '',
  others: '',
  location: '',
  event_date: '',
  event_time: '',
  start_datetime: null,
  // duration: removed (will be set to 1 on submit)
  end_datetime: null,
  end_time: '', // new pick up end time (time-only like event_time)
  attendees: null,
  document_type: '',
  documents: [],         // main uploaded files array (files will be attached)
  extra_fields: {},      // per-event dynamic fields; can contain file objects too
  // NEW: keep the selected court type per event type (Open / Covered)
  court_types: {}, // Object mapping event type to court type: { 'Court Reservation': 'Open' }
  court_type: '', // Keep for backward compatibility
  locations: {}, // Object mapping event type to location: { 'Court Reservation': 'Phase 2 Covered Court' }
  // ID upload fields (like document requests)
  id_type: '',
  id_front: null,
  id_back: null,
  valid_id_content: null,
  valid_id_number: ''
})

// Update court type with proper reactivity
const updateCourtType = (eventName, value) => {
  if (!form.court_types) {
    form.court_types = {}
  }
  // Use Vue's reactivity by creating a new object reference
  form.court_types = {
    ...form.court_types,
    [eventName]: value
  }
}

// Handle event type toggle
const handleEventTypeToggle = (eventName) => {
  // Initialize extra fields for newly selected event types
  if (selectedEventTypes.value.includes(eventName)) {
    initExtraFieldsForEvent(eventName)
    // Initialize court_types object if needed - ensure it's reactive
    if (!form.court_types) {
      form.court_types = {}
    }
    // Ensure the property exists for reactivity
    if (eventName === 'Court Reservation' && !form.court_types[eventName]) {
      form.court_types = {
        ...form.court_types,
        [eventName]: ''
      }
    }
    // Initialize locations object if needed
    if (!form.locations) {
      form.locations = {}
    }
  } else {
    // Clean up when deselected
    if (form.court_types && form.court_types[eventName]) {
      delete form.court_types[eventName]
      // Force reactivity update
      form.court_types = { ...form.court_types }
    }
    if (form.locations && form.locations[eventName]) {
      delete form.locations[eventName]
      // Force reactivity update
      form.locations = { ...form.locations }
    }
  }
}

// Watch selectedEventTypes to update form
watch(selectedEventTypes, (newTypes) => {
  form.event_types = [...newTypes]
  form.event_type = newTypes.join(',') // Keep for backward compatibility
  // Initialize fields for all selected types
  newTypes.forEach(eventType => {
    initExtraFieldsForEvent(eventType)
  })
}, { deep: true })

// sample event list, descriptions and requirements (kept from original)
const eventNames = [
  'Court Reservation', 'Community Hall Reservation', 'Manpower Assistance', 'Tent and Tables Borrowing',
  'Sports Equipment Borrowing', 'Sound System Borrowing'
]

const eventDescriptions = {
    'Court Reservation': 'Ang Court Reservation ay para sa mga residente na nais gumamit ng barangay covered court para sa basketball, volleyball, o iba pang sports activities. Libre ito at maaaring i-schedule ayon sa availability at guidelines.',
    'Community Hall Reservation': 'Ang Community Hall ay available para sa meetings, seminars, trainings, at iba pang indoor activities. May aircon, sound system, at seating capacity na 100 persons. Libre itong gamitin basta may approved reservation.',
    'Sports Equipment Borrowing': 'Ang barangay ay may sports equipment tulad ng basketball, volleyball, chess set, at iba pa na maaaring hiramin ng mga residente. Libre itong ipagamit at inaasahang ibalik sa maayos na kondisyon.',
    'Sound System Borrowing': 'Maaaring hiramin nang libre ang sound system ng barangay para sa events, programs, o gatherings. Kasama dito ang speakers, microphone, at basic audio equipment. Maaaring maglaan ng technician para sa mas malalaking events depende sa availability.',
    'Manpower Assistance': 'Nagbibigay ang barangay ng manpower assistance para sa events o activities na nangangailangan ng marshals, utility personnel, o event helpers. Libre ang serbisyong ito ngunit nakadepende sa availability kaya kinakailangan ang early request.',
    'Tent and Tables Borrowing': 'May available na tents, tables, at chairs ang barangay na maaaring hiramin nang libre para sa outdoor events, birthday parties, o family gatherings. Limitado ang quantity kaya inirerekomenda ang advance reservation.'
}


const eventRequirements = {
    'Court Reservation': [
        '• Valid ID of the event organizer',
        '• List of game/activity schedule and participants',
    ],
    'Community Hall Reservation': [
        '• Valid ID of the event organizer',
        '• List of event schedule and attendees',
    ],
    'Manpower Assistance': [
        '• Valid ID of the event organizer',
        '• Detailed event plan and manpower requirements',
    ],
    'Sports Equipment Borrowing': [
        '• Valid ID of the equipment borrower',
        '• List of game/activity schedule and participants',
    ],
    'Sound System Borrowing': [
        '• Valid ID of the equipment borrower',
        '• Detailed event plan and sound system requirements',
    ],
    'Tent and Tables Borrowing': [
        '• Valid ID of the equipment borrower',
        '• Detailed event plan and sound system requirements',
    ],
}

// Define dynamic per-event fields (similar structure to previous docs mapping)
const eventFields = {
  'Funeral Assistance': [
    { name: 'death_certificate', label: 'Death Certificate', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'relation_to_deceased', label: 'Relation to Deceased', type: 'text', required: true },
    { name: 'funeral_service_contract', label: 'Funeral Service Contract (if any)', type: 'file', required: false, accept: '.pdf' }
  ],

  'Court Reservation': [
    { name: 'waiver_form', label: 'Waiver Form', type: 'file', required: true, accept: '.pdf' },
    { name: 'tournament_type', label: 'Tournament Type', type: 'select', required: false, options: ['Basketball', 'Volleyball', 'Badminton', 'Other'], placeholder: 'Select tournament type' },
  ],

  'Covered Court Event': [
    { name: 'event_proposal', label: 'Event Proposal / Details', type: 'file', required: true, accept: '.pdf' },
    { name: 'organizer_contact', label: 'Organizer Contact Number', type: 'text', required: true }
  ],

  'Community Hall Reservation': [
    { name: 'purpose_letter', label: 'Purpose of Use Letter', type: 'file', required: true, accept: '.pdf' },
    { name: 'expected_guests', label: 'Expected Guests', type: 'number', required: false }
  ],

  'Medical Mission Assistance': [
    { name: 'medical_records', label: 'Medical Records (if available)', type: 'file', required: false, accept: '.pdf,image/*' },
    { name: 'target_group', label: 'Target Group', type: 'select', required: true, options: ['Children', 'Adults', 'Senior Citizens', 'All'], placeholder: 'Select target group' }
  ],

  'Feeding Program Request': [
    { name: 'organization_certificate', label: 'Organization Certificate', type: 'file', required: false, accept: '.pdf' },
    { name: 'list_of_beneficiaries', label: 'List of Beneficiaries (file)', type: 'file', required: true, accept: '.pdf' }
  ],

  'Sports Equipment Lending': [
    { name: 'equipment_list', label: 'Equipment List (what you want to borrow)', type: 'textarea', required: true },
    { name: 'deposit_receipt', label: 'Deposit Receipt', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Sound System Rental': [
    { name: 'rental_hours', label: 'Number of Hours', type: 'number', required: true },
    { name: 'technician_request', label: 'Need Technician?', type: 'select', required: false, options: ['Yes','No'] }
  ],

  'Tent and Tables Rental': [
    { name: 'quantity_needed', label: 'Quantity Needed (tents/tables/chairs)', type: 'text', required: true },
    { name: 'rental_deposit', label: 'Rental Deposit Proof', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Ambulance Service Request': [
    { name: 'patient_name', label: 'Patient Name', type: 'text', required: true },
    { name: 'medical_certificate', label: 'Medical Certificate / Referral', type: 'file', required: false, accept: '.pdf,image/*' }
  ],

  'Disaster Relief Assistance': [
    { name: 'incident_report', label: 'Incident Report / BLOTTER', type: 'file', required: true, accept: '.pdf,image/*' },
    { name: 'photos_of_damage', label: 'Photos of Damage', type: 'file', required: false, accept: 'image/*' }
  ],

  'Livelihood Program Application': [
    { name: 'livelihood_proposal', label: 'Livelihood Proposal', type: 'file', required: true, accept: '.pdf' },
    { name: 'proof_low_income', label: 'Proof of Low Income (if required)', type: 'file', required: false, accept: '.pdf,image/*' }
  ]
}

// computed fields for current event (for backward compatibility)
// Returns fields for all selected event types combined
const currentEventFields = computed(() => {
  const allFields = []
  selectedEventTypes.value.forEach(eventType => {
    const fields = eventFields[eventType] ?? []
    allFields.push(...fields)
  })
  return allFields
})

// Supporting documents relevant to each event type
const supportingDocumentsByEvent = {
  'Court Reservation': [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Event Schedule/Plan',
    'Participant List',
    'Other Documents'
  ],
  'Community Hall Reservation': [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Event Schedule/Plan',
    'Attendee List',
    'Other Documents'
  ],
  'Manpower Assistance': [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Event Plan',
    'Event Details',
    'Other Documents'
  ],
  'Tent and Tables Borrowing': [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Event Plan',
    'Other Documents'
  ],
  'Sports Equipment Borrowing': [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Equipment List',
    'Activity Schedule',
    'Other Documents'
  ],
  'Sound System Borrowing': [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Event Plan',
    'Sound System Requirements',
    'Other Documents'
  ]
}

// Computed property to get relevant supporting documents based on selected event type
const relevantSupportingDocuments = computed(() => {
  return supportingDocumentsByEvent[selectedEventType.value] || [
    'Valid ID',
    'Authorization Letter',
    'Barangay Clearance',
    'Other Documents'
  ]
})

// initialize extra_fields keys so UI bindings are ready
const initExtraFieldsForEvent = (eventName) => {
  form.extra_fields = form.extra_fields || {}
  const defs = eventFields[eventName] ?? []
  defs.forEach(f => {
    if (form.extra_fields[f.name] === undefined) {
      form.extra_fields[f.name] = f.type === 'checkbox' ? [] : null
    }
    if (!dynamicFileNames.value[f.name]) dynamicFileNames.value[f.name] = ''
  })
}

// initialize default selected
initExtraFieldsForEvent(selectedEventType.value)

// checkbox toggle helper
const toggleCheckbox = (fieldName, value, checked) => {
  if (!Array.isArray(form.extra_fields[fieldName])) form.extra_fields[fieldName] = []
  const idx = form.extra_fields[fieldName].indexOf(value)
  if (checked && idx === -1) form.extra_fields[fieldName].push(value)
  if (!checked && idx !== -1) form.extra_fields[fieldName].splice(idx, 1)
}

// ID upload handlers (like document requests)
const triggerFileUpload = (side) => {
  if (side === 'front' && fileFrontInput.value && typeof fileFrontInput.value.click === 'function') {
    fileFrontInput.value.click()
  } else if (side === 'back' && fileBackInput.value && typeof fileBackInput.value.click === 'function') {
    fileBackInput.value.click()
  }
}

const handleFileUpload = (event, side) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (side === 'front') {
    idFrontName.value = file.name
    form.id_front = file
    form.valid_id_content = file
  } else if (side === 'back') {
    idBackName.value = file.name
    form.id_back = file
    if (!form.valid_id_content) {
      form.valid_id_content = file
    }
  }
}

const removeIdFile = (side) => {
  if (side === 'front') {
    idFrontName.value = ''
    form.id_front = null
    // If front was the valid_id_content, clear it
    if (form.valid_id_content === form.id_front || !form.id_back) {
      form.valid_id_content = form.id_back || null
    }
    // Reset file input
    if (fileFrontInput.value) fileFrontInput.value.value = ''
  } else if (side === 'back') {
    idBackName.value = ''
    form.id_back = null
    // Reset file input
    if (fileBackInput.value) fileBackInput.value.value = ''
  }
}

// computed for showing id number field only after an id_type is selected AND at least one side uploaded
const showIdNumber = computed(() => {
  return !!form.id_type && (!!form.id_front || !!form.id_back)
})

// mapping labels per id type
const idNumberLabels = {
  'National ID': 'National ID No.',
  "Driver's License": "Driver's License No.",
  'Passport': 'Passport No.',
  "Voter's ID": "Voter's ID No.",
  'SSS ID': 'SSS No.',
  'UMID': 'UMID No.'
}
const idNumberLabel = computed(() => {
  return idNumberLabels[form.id_type] || 'ID Number'
})

// main file uploads handlers (multiple)
const triggerMainFileUpload = () => {
  if (mainFileInput.value && typeof mainFileInput.value.click === 'function') mainFileInput.value.click()
}
const handleMainFileUpload = (e) => {
  const files = Array.from(e.target.files || [])
  // store file objects in form.documents array (Inertia will package them when forceFormData is used)
  form.documents = form.documents || []
  files.forEach(f => {
    form.documents.push(f)
    mainUploadedNames.value.push(f.name)
  })
}

// remove main file
const removeMainFile = (index) => {
  form.documents.splice(index, 1)
  mainUploadedNames.value.splice(index, 1)
}

// dynamic file handlers for per-event fields
const triggerDynamicFileUpload = (fieldName) => {
  // Find the file input by its data attribute
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input && typeof input.click === 'function') {
    input.click()
  } else {
    console.error('Could not find file input for field:', fieldName)
    // Fallback: try to find by searching all file inputs
    const allInputs = Array.from(document.querySelectorAll('input[type="file"]'))
    const targetInput = allInputs.find(inp => {
      // Check if this input is in a container that matches the field name
      const container = inp.closest('.dynamic-field-wrapper')
      if (container) {
        const label = container.querySelector('.field-label')
        // This is a last resort - not ideal but might work
        return label && label.textContent.includes(fieldName)
      }
      return false
    })
    if (targetInput) {
      targetInput.click()
    }
  }
}

const handleDynamicFileUpload = (e, fieldName) => {
  const file = (e.target.files && e.target.files[0]) || null
  if (!file) return
  // store file object in form.extra_fields[fieldName] — will be handled by FormData
  form.extra_fields = { ...form.extra_fields, [fieldName]: file }
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: file.name }
}

const removeDynamicFile = (fieldName) => {
  // Remove file from form
  form.extra_fields = { ...form.extra_fields, [fieldName]: null }
  // Clear filename display
  dynamicFileNames.value = { ...dynamicFileNames.value, [fieldName]: '' }
  // Reset file input
  const input = document.querySelector(`input[data-dyn-field="${fieldName}"]`)
  if (input) input.value = ''
}

// navigation & UI helpers (kept from your original)
const toggleSettings = () => { showSettings.value = !showSettings.value }
const closeSettings = () => { showSettings.value = false }

// Terms & Conditions modal
const showTerms = ref(false)
const openTerms = () => {
    showSettings.value = false
    showTerms.value = true
}
const closeTerms = () => {
    showTerms.value = false
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
const setActiveTab = (tab) => { activeTab.value = tab }
const navigateToPosts = () => { activeTab.value = 'posts'; router.visit(route('announcement_resident')) }
const navigateToDocuments = () => { activeTab.value = 'documents'; router.visit(route('document_request_select_resident')) }
const navigateToProfile = () => { activeTab.value = 'profile'; router.visit(route('profile_resident')) }
const navigateToNotifications = () => { activeTab.value = 'notifications'; router.visit(route('notification_activities_resident')) }
const openFAQ = () => { router.visit(route('help_center_resident')) }

// Show request form (switch to selection view)
const showRequestForm = () => {
  currentView.value = 'selection'
}

// View request details (open modal)
const viewRequestDetails = (request) => {
  selectedRequest.value = request
  showDetailsModal.value = true
}

// Close details modal
const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedRequest.value = null
}

// Helper functions for modal
const getRejectionReason = (request) => {
  const adminFeedback = request?.admin_feedback ?? request?.raw?.admin_feedback ?? null
  if (adminFeedback && adminFeedback.trim() !== '') {
    return adminFeedback.trim()
  }
  return 'The submitted information does not meet the requirements. Please contact the barangay office for more details.'
}

// Go back to list view from form
const backToList = () => {
  currentView.value = 'list'
}

// add these refs near other refs (e.g., mainFileInput)
const startTimeInput = ref(null)
const endTimeInput = ref(null)

// focus helpers used by the placeholder so clicking it focuses the native time input
const focusStartTime = () => {
  if (startTimeInput.value && typeof startTimeInput.value.focus === 'function') {
    startTimeInput.value.focus()
  }
}
const focusEndTime = () => {
  if (endTimeInput.value && typeof endTimeInput.value.focus === 'function') {
    endTimeInput.value.focus()
  }
}

// Event selection helpers (kept for backward compatibility but not used with checkboxes)
const selectEvent = (eventType) => {
  if (!selectedEventTypes.value.includes(eventType)) {
    selectedEventTypes.value.push(eventType)
  }
  handleEventTypeToggle(eventType)
}

const proceedToForm = () => {
  // Check if at least one event type is selected
  if (selectedEventTypes.value.length === 0) {
    alert('Please select at least one event type before proceeding.')
    return
  }

  // Check if Court Reservation is selected and court type is required
  if (selectedEventTypes.value.includes('Court Reservation')) {
    const courtType = form.court_types && form.court_types['Court Reservation']
    if (!courtType) {
      alert('Please select a Court Type (Open or Covered) for Court Reservation before proceeding.')
      return
    }
  }

  // Initialize fields for all selected event types
  selectedEventTypes.value.forEach(eventType => {
    initExtraFieldsForEvent(eventType)
  })

  // Check terms acceptance
  if (!termsAccepted.value || !hasScrolledToBottom.value) {
    alert('Please read and accept the Terms and Conditions before proceeding.')
    return
  }

  currentView.value = 'form'
}

const backToSelection = () => {
  currentView.value = 'selection'
}

// basic submission with dynamic validation and Inertia POST (forceFormData)
const testing = true // flip to false to enable stricter client validation

const submitRequest = async () => {
  if (!testing) {
    if (!form.purpose) { alert('Please select a purpose'); return }
    if (!form.event_date || !form.event_time) { alert('Please specify event date/time'); return }
    
    // Validate ID upload for all event types (like document requests)
    if (!form.id_type) {
      alert('Please select a type of identification.')
      return
    }
    if (!form.id_front || !form.id_back) {
      alert('Please upload both the front and back of your selected ID.')
      return
    }
    if (!form.valid_id_number) {
      alert(`Please enter ${idNumberLabel.value}`)
      return
    }
  }

  // set duration to 1 hour as requested
  form.duration = 1

  // compute start & end datetimes (optional; backend should validate)
  if (form.event_date && form.event_time) {
    form.start_datetime = `${form.event_date} ${form.event_time}`

    // compute end_datetime from start + duration (duration now set to 1)
    if (form.duration !== undefined && form.duration !== null) {
      const start = new Date(`${form.event_date}T${form.event_time}`)
      const end = new Date(start.getTime() + Number(form.duration) * 60 * 60 * 1000)
      const pad = n => String(n).padStart(2, '0')
      form.end_datetime = `${end.getFullYear()}-${pad(end.getMonth()+1)}-${pad(end.getDate())} ${pad(end.getHours())}:${pad(end.getMinutes())}:${pad(end.getSeconds())}`

      // also update the end_time field (time-only) so UI reflects the computed end time
      form.end_time = `${pad(end.getHours())}:${pad(end.getMinutes())}`
    }
  }

  // client-side dynamic field validation (if testing=false)
  if (!testing) {
    const missing = currentEventFields.value.find(f => {
      const v = form.extra_fields?.[f.name]
      if (f.required) {
        if (f.type === 'file') return !v
        if (f.type === 'checkbox') return !Array.isArray(v) || v.length === 0
        return v === null || v === '' || v === undefined
      }
      return false
    })
    if (missing) { alert(`Please provide: ${missing.label}`); return }
  }

  if (isSubmitting.value) return
  isSubmitting.value = true

  // Map valid_id_number if provided
  if (form.valid_id_number) {
    form.valid_id_number = form.valid_id_number
  }

  // Ensure valid_id_content is the front file if available
  if (form.id_front) {
    form.valid_id_content = form.id_front
  }

  try {
    const fd = new FormData()

    // Ensure event_types array is sent
    if (form.event_types && form.event_types.length > 0) {
      form.event_types.forEach(eventType => {
        fd.append('event_types[]', eventType)
      })
      // Also send as comma-separated string for backward compatibility
      form.event_type = form.event_types.join(',')
    } else if (!form.event_type) {
      form.event_type = selectedEventTypes.value.join(',')
    }

    // Send event_types as JSON for easier backend processing
    fd.append('event_types_json', JSON.stringify(form.event_types || selectedEventTypes.value))
    
    // Send locations object as JSON
    if (form.locations && Object.keys(form.locations).length > 0) {
      fd.append('locations_json', JSON.stringify(form.locations))
    }
    
    // Send court_types object as JSON
    if (form.court_types && Object.keys(form.court_types).length > 0) {
      fd.append('court_types_json', JSON.stringify(form.court_types))
    }

    // append scalar fields (include duration and end_time)
    const scalarKeys = ['event_type','purpose','others','event_date','event_time','start_datetime','duration','end_datetime','end_time','attendees','document_type','id_type','valid_id_number']
    scalarKeys.forEach(k => {
      const value = form[k]
      if (value !== undefined && value !== null && value !== '') {
        fd.append(k, value)
      }
    })
    
    // Handle location - use first location or main location field
    if (form.locations && Object.keys(form.locations).length > 0) {
      const firstLocation = Object.values(form.locations)[0]
      if (firstLocation) {
        fd.append('location', firstLocation)
      }
    } else if (form.location) {
      fd.append('location', form.location)
    }
    
    // Handle court_type - use first court type or main court_type field
    if (form.court_types && Object.keys(form.court_types).length > 0) {
      const firstCourtType = Object.values(form.court_types)[0]
      if (firstCourtType) {
        fd.append('court_type', firstCourtType)
      }
    } else if (form.court_type) {
      fd.append('court_type', form.court_type)
    }
    
    // Log what we're sending for debugging
    console.log('Submitting event assistance request:', {
      event_type: form.event_type,
      purpose: form.purpose,
      event_date: form.event_date,
      event_time: form.event_time,
      has_valid_id: !!form.valid_id_content,
      has_documents: form.documents?.length > 0,
    })

    // Append ID files (valid_id_content, id_front, id_back)
    if (form.valid_id_content instanceof File) {
      fd.append('valid_id_content', form.valid_id_content)
    }
    if (form.id_front instanceof File) {
      fd.append('id_front', form.id_front)
    }
    if (form.id_back instanceof File) {
      fd.append('id_back', form.id_back)
    }

    // append multiple main documents as documents[]
    if (Array.isArray(form.documents)) {
      form.documents.forEach(file => {
        fd.append('documents[]', file)
      })
    }

    // append extra_fields: files, arrays, or scalars
    if (form.extra_fields && typeof form.extra_fields === 'object') {
      Object.keys(form.extra_fields).forEach(key => {
        const val = form.extra_fields[key]
        if (val === null || val === undefined) return

        if (val instanceof File) {
          // single file
          fd.append(`extra_fields[${key}]`, val)
        } else if (Array.isArray(val)) {
          // arrays (e.g., checkbox values)
          val.forEach(v => fd.append(`extra_fields[${key}][]`, v))
        } else {
          fd.append(`extra_fields[${key}]`, val)
        }
      })
    }

    // POST to Laravel JSON endpoint (your controller returns JSON with ticket)
    const url = route('event_assist.store')
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    const res = await window.axios.post(url, fd, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      onUploadProgress: (progressEvent) => {
        if (progressEvent.total) {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          console.log('Upload progress:', percentCompleted + '%')
        }
      }
    })

    // read ticket from response
    const ticket = res?.data?.ticket || res?.data?.request_number || res?.data?.id ? res?.data?.ticket : null
    requestNumber.value = ticket ?? (res?.data?.id ? `EA-${String(res.data.id).padStart(3,'0')}` : 'EA-TICKET-PENDING')
    currentView.value = 'success'
    isSubmitting.value = false
    console.log('Request submitted successfully, ticket:', ticket)
  } catch (err) {
    console.error('Error submitting request:', err)
    console.error('Error response:', err.response?.data)
    isSubmitting.value = false
    
    if (err.response) {
      if (err.response.status === 422) {
        // Validation errors
        const errors = err.response.data.errors || {}
        const errorMessages = err.response.data.message || 'Validation error'
        const firstKey = Object.keys(errors)[0]
        const firstMsg = firstKey ? (errors[firstKey][0] || errorMessages) : errorMessages
        alert(`Validation Error: ${firstMsg}`)
      } else if (err.response.data && err.response.data.message) {
        // Server error with message
        alert(`Error: ${err.response.data.message}`)
      } else {
        // Generic server error
        alert('An error occurred while submitting. Please try again.')
      }
    } else if (err.message) {
      // Network or other error
      alert(`Error: ${err.message}`)
    } else {
      // Unknown error
      console.error('Submit error', err)
      alert('An error occurred while submitting. Please try again.')
    }
  } finally {
    isSubmitting.value = false
  }
}


const viewRequest = () => { 
  currentView.value = 'list'
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.filter-dropdown-wrapper')) {
    showSortDropdown.value = false
    showStatusDropdown.value = false
  }
  if (!event.target.closest('.header-actions')) {
    showSettings.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  activeTab.value = 'events'
  // Default to list view to show submitted requests
  currentView.value = 'list'
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

.event-type-wrapper { display: inline-block; margin: 6px; position: relative; }
/* Court options removed - using dropdown instead */
.court-selected-note { margin:6px 0; color:#333; }

/* Dynamic Fields Styling - matching document request */
.dynamic-field-wrapper {
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dynamic-field-wrapper:hover {
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.15);
    transform: translateY(-1px);
}

.field-header {
    margin-bottom: 12px;
}

.field-label {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #333;
    margin-bottom: 6px;
}

.required-star {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
    font-weight: 700;
}

.optional-text {
    color: #999;
    font-size: 13px;
    font-weight: 500;
    margin-left: 8px;
}

.field-description {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
    line-height: 1.5;
    font-style: italic;
}

.field-input-wrapper {
    width: 100%;
}

/* File Uploads Grid Layout */
.file-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.file-upload-item {
    background: white;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.file-upload-item:hover {
    border-color: #ff8c42;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.15);
    transform: translateY(-1px);
}

.file-upload-header {
    margin-bottom: 10px;
}

.file-upload-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
}

.file-upload-description {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
    line-height: 1.4;
    font-style: italic;
}

.file-upload-controls {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.upload-btn-dynamic {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    justify-content: center;
    width: 100%;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(43, 178, 74, 0.3);
}

.upload-btn-dynamic:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 178, 74, 0.4);
}

.upload-icon {
    font-size: 18px;
}

.uploaded-file-info-compact {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-radius: 8px;
    border: 1px solid #4caf50;
    box-shadow: 0 1px 4px rgba(76, 175, 80, 0.2);
    font-size: 14px;
    width: 100%;
}

.file-checkmark {
    color: #4caf50;
    font-size: 18px;
    font-weight: 700;
}

.file-name-compact {
    flex: 1;
    color: #2e7d32;
    font-weight: 600;
    font-size: 14px;
    word-break: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
}

.remove-file-btn-small {
    background: #e74c3c;
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
    flex-shrink: 0;
}

.remove-file-btn-small:hover {
    background: #c0392b;
    transform: scale(1.1);
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    background: linear-gradient(135deg, #fff7ef, #ffede0);
    transform: translateX(3px);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff8c42;
}

.checkbox-label span {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

/* Legacy styles for backward compatibility */
.dynamic-field { 
    margin-bottom: 1rem; 
}
.checkbox-label { display:block; margin-bottom:.25rem; }
.uploaded-files-list { margin-top:.5rem; }
.uploaded-file-item { display:flex; align-items:center; gap:.5rem; margin-bottom:.25rem; }
.remove-file { border:none; background:transparent; cursor:pointer; color:#900; }

.app-container {
    min-height: 100vh;
    width: 100vw;
    background: url('/assets/BG MAIN.png') no-repeat center center fixed;
    background-size: cover;
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
    z-index: 1000;
}

.header-content {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 10px;
}

.header-logo {
    width: 180px;
    height: 60px;
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
    overflow: hidden;
}

.settings-item {
    display: block;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    font-weight: 500;
}

.settings-item:hover {
    background: #fff7ef;
    color: #ff8c42;
}

.main-layout {
    flex: 1;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 25px;
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
    flex-shrink: 0;
    stroke: currentColor;
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

.faq-btn .nav-icon {
    stroke: white;
}

.content-area {
    display: flex;
    flex-direction: column;
}

.main-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.event-header {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.event-header h2 {
    font-size: 22px;
    font-weight: 700;
}

.small-logo {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    padding: 3px;
}

.event-selection {
    display: grid;
    grid-template-columns: 320px 1fr;
    min-height: 600px;
    gap: 0;
}

.event-types-wrapper {
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    padding: 20px 0;
}

.event-types-wrapper .event-types {
    padding: 0;
    margin-top: 0;
    background: transparent;
    border-right: none;
}

.event-types {
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

.event-type-btn {
    background: transparent;
    border: none;
    padding: 15px 25px;
    text-align: left;
    font-size: 14px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    border-left: 4px solid transparent;
    transition: all 0.2s;
}

.event-type-btn:hover {
    background: #fff;
    color: #ff8c42;
}

.event-type-btn.active {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border-left-color: #ff8c42;
}

/* Checkbox styling for event types */
.event-type-checkbox-label {
    display: flex;
    align-items: center;
    padding: 15px 25px;
    cursor: pointer;
    border-left: 4px solid transparent;
    transition: all 0.2s;
    background: transparent;
    color: #666;
    font-size: 14px;
    font-weight: 600;
}

.event-type-checkbox-label:hover {
    background: #fff;
    color: #ff8c42;
}

.event-type-checkbox-label.active {
    background: #e9ecef;
    color: #333;
    border-left-color: #ff8c42;
}

.event-type-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    cursor: pointer;
    accent-color: #ff8c42;
    flex-shrink: 0;
}

.event-type-checkbox:checked {
    accent-color: #ff8c42;
}

.event-type-checkbox-text {
    flex: 1;
}

.selected-events-summary {
    margin-bottom: 20px;
    padding: 15px;
    background: #f0f7ff;
    border-radius: 8px;
    border-left: 4px solid #ff8c42;
}

.selected-count {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.selected-count strong {
    color: #ff8c42;
    font-weight: 700;
}

.event-descriptions-list {
    margin-bottom: 30px;
}

.event-description-item {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 2px solid #e0e0e0;
}

.event-description-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.event-description-title {
    font-size: 24px;
    font-weight: 700;
    color: #ff8c42;
    margin-bottom: 15px;
}

.no-selection-message {
    text-align: center;
    padding: 40px;
    color: #999;
    font-size: 16px;
    font-style: italic;
}

.event-info {
    padding: 40px;
    overflow-y: auto;
    max-height: calc(100vh - 250px);
}

.event-title {
    font-size: 40px;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
}

.event-description {
    font-size: 20px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 30px;
    text-align: justify;
}

.requirements-section h4 {
    color: #2bb24a;
    font-size: 25px;
    font-weight: 700;
    margin-bottom: 15px;
}

.requirements-list {
    list-style-position: inside;
    color: #333;
    font-size: 18px;
    line-height: 2;
    font-weight: 600;
    margin-bottom: 40px;
}

/* Court Type Selection */
.court-type-selection {
    margin: 20px 0;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.05);
}

.court-type-label {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.court-type-select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.3s ease;
}

.court-type-select:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.2);
}

/* Terms and Conditions */
.terms-section {
    margin: 25px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
}

.terms-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

.terms-content {
    max-height: 300px;
    overflow-y: auto;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    margin-bottom: 15px;
    line-height: 1.8;
    font-size: 14px;
    color: #333;
}

.terms-content p {
    margin-bottom: 12px;
}

.terms-content p:last-child {
    margin-bottom: 0;
}

.terms-content strong {
    color: #ff8c42;
}

.terms-checkbox-wrapper {
    display: flex;
    justify-content: center;
    padding: 10px 0;
}

.terms-checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.terms-checkbox-label:has(input:disabled) {
    opacity: 0.6;
    cursor: not-allowed;
}

.terms-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff8c42;
}

.required-star {
    color: #e74c3c;
    font-size: 16px;
    margin-left: 4px;
    font-weight: 700;
}

.request-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    display: block;
    margin-left: auto;
    margin-top: 20px;
}

.request-btn:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.request-btn.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

.request-form-container {
    padding: 30px 40px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.back-btn {
    background: transparent;
    border: none;
    color: #ff8c42;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 20px;
    transition: all 0.2s;
}

.back-btn:hover {
    color: #e6763a;
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    text-align: center;
}

.form-sections {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-bottom: 30px;
}

.form-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title .icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.form-grid:last-child {
    margin-bottom: 0;
}

/* Event Details Grid - side by side layout */
.event-details-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

/* Purpose and Documents Grid - side by side layout */
.purpose-documents-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 1024px) {
    .event-details-grid {
        grid-template-columns: 1fr;
    }
    
    .purpose-documents-grid {
        grid-template-columns: 1fr;
    }
}

.form-input {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #ff8c42;
}

/* Time Input Styles - Compact Side by Side */
.time-input-container {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: nowrap;
}

.time-input-group {
    flex: 0 1 auto;
    min-width: 130px;
    max-width: 160px;
}

.time-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
}

.time-input-wrapper {
    position: relative;
}

.time-input {
    width: 100%;
    padding: 8px 10px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
}

.time-input:hover {
    border-color: #ff8c42;
    background: #fff7ef;
}

.time-input:focus {
    outline: none;
    border-color: #ff8c42;
    box-shadow: 0 0 0 3px rgba(255, 140, 66, 0.15);
    background: white;
}

.time-separator {
    font-size: 12px;
    font-weight: 600;
    color: #888;
    padding: 0 2px;
    flex-shrink: 0;
    margin-top: 18px;
}

/* Time input icon styling */
.time-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 0.6;
    filter: invert(0.5);
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.time-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
    background: rgba(255, 140, 66, 0.1);
}

@media (max-width: 640px) {
    .time-input-container {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .time-separator {
        align-self: center;
        padding: 0;
        margin: 0;
    }
    
    .time-input-group {
        width: 100%;
        max-width: 100%;
    }
}

.full-width {
    grid-column: 1 / -1;
}

.contact-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.otp-section {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
}

.verify-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.verify-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    background: white;
    margin-bottom: 15px;
    transition: border-color 0.2s;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-textarea:focus {
    outline: none;
    border-color: #ff8c42;
}

.upload-section {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
}

.upload-btn {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    border: none;
    padding: 12px 35px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-transform: uppercase;
    flex: 1;
    min-width: 140px;
    white-space: nowrap;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(43, 178, 74, 0.4);
}

.upload-row {
    display: flex;
    gap: 12px;
    margin-left: 10px;
}

.uploaded-file {
    margin-top: 10px;
    font-size: 13px;
    color: #2bb24a;
    font-weight: 600;
}

.id-number-field {
    margin-top: 12px;
}

.file-input-hidden {
    display: none;
}

.uploaded-files-list {
    margin-top: 15px;
}

.uploaded-label {
    font-size: 13px;
    font-weight: 600;
    color: #2bb24a;
    margin-bottom: 10px;
}

.uploaded-file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    border: 1px solid #e0e0e0;
}

.uploaded-file-item span {
    font-size: 13px;
    color: #555;
}

.remove-file {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #dc3545;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.remove-file:hover {
    background: rgba(220, 53, 69, 0.2);
}

.submit-btn {
    width: 30%;
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 10px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: block;       /* Make it block-level */
    margin-left: auto;    /* Pushes it to the right */
}


.success-container {
    padding: 60px 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 600px;
}

.success-content {
    text-align: center;
    max-width: 600px;
}

.checkmark {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    font-weight: 700;
    box-shadow: 0 8px 25px rgba(43, 178, 74, 0.4);
    margin: 0 auto 30px;
}

.success-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.request-number {
    font-size: 14px;
    color: #ff8c42;
    font-weight: 700;
    margin-bottom: 30px;
}

.success-message {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 40px;
}

.success-message p {
    margin-bottom: 15px;
}

.success-message .highlight {
    color: #ff8c42;
    font-weight: 700;
}

.view-request-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    transition: all 0.3s;
}

.view-request-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
}

/* Scrollbar Styles */
.event-types::-webkit-scrollbar,
.event-info::-webkit-scrollbar,
.request-form-container::-webkit-scrollbar {
    width: 6px;
}

.event-types::-webkit-scrollbar-track,
.event-info::-webkit-scrollbar-track,
.request-form-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.event-types::-webkit-scrollbar-thumb,
.event-info::-webkit-scrollbar-thumb,
.request-form-container::-webkit-scrollbar-thumb {
    background: #e2e2e2;
    border-radius: 3px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .main-layout {
        grid-template-columns: 280px 1fr;
        padding: 20px;
        gap: 20px;
    }

    .event-selection {
        grid-template-columns: 280px 1fr;
    }

    .form-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .request-btn {
        margin-left: auto;
        margin-right: 0;
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
    }

    .event-selection {
        grid-template-columns: 1fr;
    }

    .event-types-wrapper {
        border-right: none;
        border-bottom: 1px solid #e0e0e0;
    }

    .event-types {
        border-right: none;
        border-bottom: none;
        max-height: 300px;
    }

    .event-info {
        padding: 25px;
    }

    .event-title {
        font-size: 28px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .upload-section {
        grid-template-columns: 1fr;
    }

    .otp-section {
        grid-template-columns: 1fr;
    }

    .request-form-container {
        padding: 20px;
    }

    .request-btn {
        width: 100%;
        margin-left: 0;
    }
}

@media (max-width: 480px) {
    .main-layout {
        padding: 10px;
    }

    .profile-card {
        padding: 15px;
    }

    .event-info {
        padding: 20px;
    }

    .event-title {
        font-size: 22px;
    }

    .event-description {
        font-size: 16px;
    }

    .requirements-section h4 {
        font-size: 20px;
    }

    .requirements-list {
        font-size: 16px;
    }

    .request-form-container {
        padding: 15px;
    }

    .form-section {
        padding: 20px;
    }

    .success-container {
        padding: 40px 20px;
    }

    .checkmark {
        width: 100px;
        height: 100px;
        font-size: 50px;
    }
}

/* Requests List View Styles */
.requests-list-view {
    padding: 30px 40px;
    min-height: 600px;
}

.requests-header {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 25px;
}

.request-new-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.request-new-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255, 140, 66, 0.4);
}

.requests-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.requests-table {
    width: 100%;
    border-collapse: collapse;
}

.requests-table thead {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
}

.requests-table th {
    padding: 15px 20px;
    text-align: left;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
}

.requests-table tbody tr {
    border-bottom: 1px solid #e0e0e0;
    transition: all 0.2s ease;
    cursor: pointer;
}

.requests-table tbody tr:hover {
    background: #f8f9fa;
}

.requests-table tbody tr:last-child {
    border-bottom: none;
}

.requests-table td {
    padding: 15px 20px;
    font-size: 14px;
    color: #333;
}

.no-requests {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 40px !important;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.approved {
    background: #d4edda;
    color: #155724;
}

.status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 15px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.details-modal {
    padding: 30px 40px;
}

.close-modal-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.close-modal-btn:hover {
    background: #f0f0f0;
    color: #333;
}

.modal-icon {
    text-align: center;
    margin-bottom: 20px;
}

.modal-icon .status-badge {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    font-weight: 700;
}

.approved-icon .status-badge {
    background: linear-gradient(135deg, #2bb24a, #239640);
    color: white;
}

.pending-icon .status-badge {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    color: white;
}

.rejected-icon .status-badge {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 10px;
    color: #333;
}

.request-number-display {
    text-align: center;
    font-size: 14px;
    color: #ff8c42;
    font-weight: 700;
    margin-bottom: 30px;
}

.details-content {
    margin-top: 20px;
}

.details-message {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 20px;
    text-align: center;
}

.accepted-section,
.pending-section,
.rejected-section {
    margin-top: 20px;
}

.rejection-box {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.rejection-box h4 {
    color: #721c24;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 10px;
}

.rejection-box p {
    color: #721c24;
    font-size: 14px;
    margin: 0;
}

/* Request Info Box */
.request-info-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin: 20px 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 700;
    color: #666;
    font-size: 13px;
    min-width: 120px;
}

.info-value {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    text-align: right;
    flex: 1;
}

.present-message {
    text-align: center;
    margin-top: 25px;
    padding: 15px;
    background: #fff3cd;
    border-radius: 8px;
    font-size: 14px;
    color: #856404;
}

.highlight-number {
    color: #ff8c42;
    font-weight: 700;
    font-size: 18px;
}

.note-message {
    font-size: 13px;
    color: #666;
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    line-height: 1.6;
}

/* Feedback Section */
.feedback-section {
    margin-top: 25px;
    padding: 20px;
    background: #e7f3ff;
    border-radius: 12px;
    border-left: 4px solid #2196F3;
}

.section-title-feedback {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.feedback-box {
    background: white;
    padding: 15px;
    border-radius: 8px;
}

.feedback-box p {
    margin: 0;
    font-size: 14px;
    color: #333;
    line-height: 1.6;
}

/* Filter Section Styles */
.filter-section {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-radius: 12px 12px 0 0;
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
    color: #333;
}

.filter-dropdown-btn:hover {
    border-color: #ff8c42;
}

.filter-arrow {
    font-size: 10px;
    transition: transform 0.3s ease;
    color: #666;
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

.search-container {
    display: flex;
    gap: 5px;
    background: white;
    border-radius: 8px;
    padding: 2px;
    border: 1px solid #ddd;
    align-items: center;
}

.search-input {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    width: 250px;
    font-size: 12px;
    outline: none;
}

.search-btn {
    background: transparent;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: #666;
}

.search-btn:hover {
    background: #f0f0f0;
    color: #ff8c42;
}

.search-icon {
    width: 18px;
    height: 18px;
    stroke: currentColor;
}

.request-new-btn {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.request-new-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
}

.request-new-btn:active {
    transform: translateY(0);
}

.requests-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.requests-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.requests-table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
}

.requests-table th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #666;
}

.requests-table th:first-child {
    padding-left: 25px;
}

.requests-table th:nth-child(4) {
    text-align: center;
}

.requests-table th:last-child {
    padding-right: 25px;
    text-align: center;
}

.requests-table td:nth-child(4) {
    text-align: center;
    vertical-align: middle;
}

.requests-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
    cursor: pointer;
    background: white;
}

.requests-table tbody tr:hover {
    background: #f5f5f5;
}

.requests-table tbody tr:last-child {
    border-bottom: none;
}

.requests-table td {
    padding: 18px 20px;
    font-size: 16px;
    color: #333;
    vertical-align: middle;
}

.requests-table td:first-child {
    padding-left: 25px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
}

.requests-table td:last-child {
    padding-right: 25px;
    vertical-align: middle;
    text-align: center;
}

.requests-table td:nth-child(2) {
    color: #ff8c42;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    font-size: 17px;
}

.requests-table td:nth-child(3) {
    color: #666;
    font-size: 15px;
}

.requests-table .status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid transparent;
}

.requests-table .status-badge .badge-icon {
    font-size: 12px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.requests-table .status-badge .badge-icon-svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
}

.requests-table .status-badge .badge-text {
    font-weight: bold;
    font-size: 12px;
    text-transform: uppercase;
}

.requests-table .status-badge.pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.requests-table .status-badge.approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.requests-table .status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Back button for selection view */
.back-btn-selection {
    background: transparent;
    border: none;
    color: #000;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 15px;
    padding: 10px 25px;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}

.back-btn-selection:hover {
    color: #ff8c42;
    transform: translateX(-3px);
}

/* Form header row */
.form-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.form-document-type {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.form-document-type strong {
    color: #ff8c42;
    font-weight: 700;
}
</style>