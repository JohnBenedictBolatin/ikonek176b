# Quick Testing Checklist
## iKonek176B - Quick Reference

Use this checklist for rapid testing. For detailed test cases, see `TESTING_OUTLINE.md`.

---

## 🔴 Critical Paths (Must Test First)

### 1. Authentication
- [ ] Resident login works
- [ ] Employee login works
- [ ] Admin login works (all roles)
- [ ] Logout works
- [ ] Cannot access protected pages without login

### 2. Registration
- [ ] Resident registration → OTP → System Admin approval → Login works
- [ ] Employee registration → OTP → System Admin approval → Login works
- [ ] OTP sending and verification works

### 3. Document Request Flow
- [ ] Resident requests document → Approver approves → Document generated → Resident downloads

### 4. Event Assistance Flow
- [ ] Resident requests assistance → Approver approves → Resident notified

### 5. Payment Flow
- [ ] User makes payment → Treasurer approves → Status updates

---

## 🟡 User Roles Quick Test

### Resident (Role ID: 1)
- [ ] Can view announcements
- [ ] Can create discussion posts
- [ ] Can request documents
- [ ] Can request event assistance
- [ ] Can make payments
- [ ] Can view notifications
- [ ] Can edit profile
- [ ] Cannot access admin pages

### Employee (Role ID: 2-7, 9)
- [ ] Can view announcements
- [ ] Can create announcements
- [ ] Can create discussion posts
- [ ] Can request documents
- [ ] Can request event assistance
- [ ] Can make payments
- [ ] Can view notifications
- [ ] Can edit profile
- [ ] Cannot access admin pages

### Approver
- [ ] Can view pending document requests
- [ ] Can approve/reject document requests
- [ ] Can view pending event requests
- [ ] Can approve/reject event requests
- [ ] Can view history
- [ ] Can download generated documents
- [ ] Cannot access other admin pages

### Treasurer
- [ ] Can view pending payments
- [ ] Can approve/reject payments
- [ ] Can view payment history
- [ ] Cannot access other admin pages

### System Admin
- [ ] Can view dashboard
- [ ] Can manage users (restrict, unrestrict, delete, change password)
- [ ] Can approve/reject registration requests
- [ ] Can view reports
- [ ] Can handle reported content
- [ ] Can view all records and history

---

## 🟢 Feature Quick Test

### Announcements
- [ ] Residents see only approved announcements
- [ ] Employees can create announcements
- [ ] Announcements display correctly
- [ ] Images/media display correctly

### Discussion Forum
- [ ] Can create posts
- [ ] Can react to posts
- [ ] Can comment on posts
- [ ] Can react to comments
- [ ] Can vote on polls
- [ ] Can report posts
- [ ] Can delete own posts/comments

### Document Requests
- [ ] Can select document type
- [ ] Can fill form
- [ ] Can upload valid ID
- [ ] Can submit request
- [ ] Can view status
- [ ] Can appeal rejection (resets to pending)
- [ ] Can update/resubmit after appeal
- [ ] Resubmitted request appears in approver panel
- [ ] Can download generated document

### Event Assistance
- [ ] Can select items
- [ ] Can submit request
- [ ] Can view status
- [ ] Can appeal rejection (resets to pending)
- [ ] Can update/resubmit after appeal
- [ ] Resubmitted request appears in approver panel

### Payments
- [ ] Can submit payment
- [ ] Can upload receipt
- [ ] Can view status
- [ ] Treasurer can approve/reject

### Notifications
- [ ] Notifications appear
- [ ] Can mark as read
- [ ] Unread count is accurate
- [ ] Clicking notification navigates correctly

### Profile
- [ ] Can view profile
- [ ] Can edit information
- [ ] Can update profile picture
- [ ] Can change password

### Reports & Moderation
- [ ] Can report posts
- [ ] Moderator can view reports
- [ ] Moderator can dismiss/delete
- [ ] Actions are logged

---

## 🔵 Error Handling Quick Test

- [ ] Invalid login shows error
- [ ] Invalid OTP shows error
- [ ] Form validation shows errors
- [ ] File upload errors are handled
- [ ] 404 pages work
- [ ] 500 errors are caught
- [ ] Network errors are handled

---

## 🟣 Security Quick Test

- [ ] Cannot access unauthorized pages
- [ ] Cannot modify others' data
- [ ] CSRF protection works
- [ ] File uploads are validated
- [ ] SQL injection attempts blocked
- [ ] XSS attempts blocked

---

## ⚪ Performance Quick Test

- [ ] Pages load quickly (< 3 seconds)
- [ ] Large lists paginate correctly
- [ ] Search is fast
- [ ] File uploads work
- [ ] No console errors

---

## 📱 Responsive Design Quick Test

- [ ] Mobile view works
- [ ] Tablet view works
- [ ] Desktop view works
- [ ] Navigation works on all devices
- [ ] Forms are usable on mobile

---

## 🌐 Browser Compatibility Quick Test

- [ ] Chrome works
- [ ] Firefox works
- [ ] Edge works
- [ ] Mobile browsers work
- [ ] No console errors

---

## ✅ Final Checks

- [ ] All critical paths work
- [ ] All user roles work
- [ ] All features work
- [ ] Error handling works
- [ ] Security is verified
- [ ] Performance is acceptable
- [ ] Responsive design works
- [ ] Browser compatibility confirmed

---

## 🐛 Common Issues Checklist

- [ ] Session persists after refresh
- [ ] File uploads work
- [ ] Notifications send and display
- [ ] Permissions are enforced
- [ ] Data displays correctly
- [ ] Forms submit correctly
- [ ] Pagination works
- [ ] Search works
- [ ] Filters work

---

**Quick Test Time**: ~2-3 hours for all critical paths
**Full Test Time**: ~1-2 days for comprehensive testing

