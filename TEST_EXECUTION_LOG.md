# Test Execution Log
## iKonek176B Barangay Management System

**Testing Session**: [Date]  
**Tester**: [Name]  
**Environment**: [Development/Staging/Production]  
**Browser**: [Browser Name & Version]  
**OS**: [Operating System]

---

## Test Summary

| Category | Total | Passed | Failed | Skipped | Notes |
|----------|-------|--------|--------|---------|-------|
| Authentication | | | | | |
| Registration | | | | | |
| Resident Features | | | | | |
| Employee Features | | | | | |
| Approver Features | | | | | |
| Treasurer Features | | | | | |
| System Admin Features | | | | | |
| Shared Features | | | | | |
| Security | | | | | |
| Performance | | | | | |
| **TOTAL** | | | | | |

---

## Test Cases

### Authentication & Authorization

#### Test Case: AUTH-001 - Resident Login
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Navigate to `/login_user`
  2. Enter valid contact number and password
  3. Click login
- **Expected**: Redirects to resident dashboard
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: AUTH-002 - Employee Login
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Navigate to `/login_employee`
  2. Enter valid credentials
  3. Click login
- **Expected**: Redirects to employee dashboard
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: AUTH-003 - Admin Login (Approver)
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Navigate to `/login_approver`
  2. Enter valid admin credentials
  3. Click login
- **Expected**: Redirects to approver dashboard
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: AUTH-004 - Unauthorized Access
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Logout or use incognito mode
  2. Try to access `/a_document_request`
- **Expected**: Redirects to login page
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Registration

#### Test Case: REG-001 - Resident Registration
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Navigate to `/register_resident`
  2. Fill all required fields
  3. Request OTP
  4. Verify OTP
  5. Submit registration
- **Expected**: Registration request created, appears in admin panel
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: REG-002 - OTP Verification
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Request OTP
  2. Enter correct OTP
  3. Enter incorrect OTP
  4. Enter expired OTP
- **Expected**: Correct OTP works, incorrect/expired rejected
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Document Requests

#### Test Case: DOC-001 - Create Document Request
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Navigate to document request
  3. Select document type
  4. Fill form
  5. Upload valid ID
  6. Submit
- **Expected**: Request created, appears in approver panel
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DOC-002 - Approve Document Request
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as approver
  2. View pending document request
  3. Review details and valid ID
  4. Click approve
- **Expected**: Document generated, notification sent, status updated
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DOC-003 - Download Generated Document
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. View approved document request
  3. Click download PDF
  4. Click download DOCX
- **Expected**: Both formats download correctly with correct content
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DOC-004 - Document Request Appeal & Resubmission
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as approver
  2. Reject a document request (with feedback)
  3. Logout, login as resident
  4. View rejected request in history
  5. Click appeal button
  6. Verify appeal form shows rejection reason
  7. Upload new valid ID
  8. Correct any incorrect fields
  9. Submit appeal (POST)
  10. Verify status changed to "Pending"
  11. Update request with corrected information (PUT)
  12. Verify updated request appears in approver panel
  13. Login as approver, verify resubmitted request
  14. Approve resubmitted request
- **Expected**: Full appeal → update → resubmit → approve flow works
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DOC-005 - Cannot Appeal Non-Rejected Requests
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Try to appeal a pending request
  3. Try to appeal an approved request
- **Expected**: Both attempts fail with appropriate error
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DOC-006 - Cannot Update Non-Pending Requests
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Try to update a rejected request (without appealing first)
  3. Try to update an approved request
- **Expected**: Both attempts fail, must appeal first
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Event Assistance

#### Test Case: EVENT-001 - Create Event Assistance Request
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Navigate to event assistance
  3. Select items and quantities
  4. Upload valid ID
  5. Submit
- **Expected**: Request created, appears in approver panel
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: EVENT-002 - Approve Event Request
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as approver
  2. View pending event request
  3. Review details
  4. Approve
- **Expected**: Status updated, notification sent
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: EVENT-003 - Event Assistance Appeal & Resubmission
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as approver
  2. Reject an event assistance request (with feedback)
  3. Logout, login as resident
  4. View rejected request in history
  5. Click appeal button
  6. Verify appeal form shows rejection reason
  7. Upload new valid ID
  8. Modify event details (date, items, quantities)
  9. Submit appeal (POST)
  10. Verify status changed to "Pending"
  11. Update request with corrected information (PUT)
  12. Verify updated request appears in approver panel
  13. Login as approver, verify resubmitted request
  14. Approve resubmitted request
- **Expected**: Full appeal → update → resubmit → approve flow works
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: EVENT-004 - Cannot Appeal Non-Rejected Event Requests
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Try to appeal a pending event request
  3. Try to appeal an approved event request
- **Expected**: Both attempts fail with appropriate error
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: EVENT-005 - Cannot Update Non-Pending Event Requests
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Try to update a rejected event request (without appealing first)
  3. Try to update an approved event request
- **Expected**: Both attempts fail, must appeal first
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Payments

#### Test Case: PAY-001 - Submit Payment
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident/employee
  2. Navigate to payment page
  3. Select payment method
  4. Upload receipt
  5. Submit
- **Expected**: Payment submitted, appears in treasurer panel
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: PAY-002 - Approve Payment
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as treasurer
  2. View pending payment
  3. Review receipt
  4. Approve
- **Expected**: Status updated, notification sent
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Discussion Forum

#### Test Case: DISC-001 - Create Post
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident/employee
  2. Navigate to discussion
  3. Click create post
  4. Enter content, add tags
  5. Submit
- **Expected**: Post appears in discussion feed
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DISC-002 - React to Post
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. View a post
  2. Click like/react button
  3. Check reaction count
- **Expected**: Reaction added, count updates
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: DISC-003 - Comment on Post
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. View a post
  2. Add comment
  3. Submit
- **Expected**: Comment appears, count updates
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Announcements

#### Test Case: ANN-001 - View Announcements (Resident)
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Navigate to announcements
- **Expected**: Only approved announcements visible
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: ANN-002 - Create Announcement (Employee)
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as employee
  2. Navigate to announcements
  3. Create new announcement
  4. Submit
- **Expected**: Announcement created (pending approval if applicable)
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### User Management (System Admin)

#### Test Case: USER-001 - View Users
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as system admin
  2. Navigate to users page
- **Expected**: List of all users displays
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: USER-002 - Restrict User
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Select a user
  2. Click restrict
  3. Confirm
  4. Try to login as that user
- **Expected**: User cannot login
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: USER-003 - Approve Registration Request
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. View pending registration request
  2. Review details
  3. Approve
- **Expected**: User account created, can login
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Notifications

#### Test Case: NOTIF-001 - Receive Notification
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Trigger a notification (e.g., document approved)
  2. Check notification badge
  3. View notifications
- **Expected**: Notification appears, badge updates
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: NOTIF-002 - Mark as Read
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. View notifications
  2. Mark one as read
  3. Mark all as read
- **Expected**: Status updates, badge count decreases
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Profile Management

#### Test Case: PROF-001 - Edit Profile
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Navigate to profile
  2. Edit information
  3. Save
- **Expected**: Changes saved, displayed correctly
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: PROF-002 - Update Profile Picture
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Navigate to profile
  2. Upload new picture
  3. Save
- **Expected**: Picture updates, displays correctly
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Security Tests

#### Test Case: SEC-001 - CSRF Protection
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Try to submit form without CSRF token
- **Expected**: Request rejected
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: SEC-002 - Unauthorized Access
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Login as resident
  2. Try to access `/a_document_request`
- **Expected**: Access denied or redirect
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: SEC-003 - File Upload Validation
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Try to upload .exe file
  2. Try to upload very large file
- **Expected**: Both rejected
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

### Performance Tests

#### Test Case: PERF-001 - Page Load Time
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. Measure load time of main pages
- **Expected**: All pages load < 3 seconds
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

#### Test Case: PERF-002 - Large Dataset
- **Date**: 
- **Status**: ⬜ Pass / ⬜ Fail / ⬜ Blocked
- **Steps**:
  1. View page with many records (100+)
- **Expected**: Pagination works, page loads quickly
- **Actual**: 
- **Screenshots**: 
- **Notes**: 

---

## Bugs Found

### Bug #1
- **ID**: BUG-001
- **Date Found**: 
- **Severity**: ⬜ Critical / ⬜ High / ⬜ Medium / ⬜ Low
- **Feature**: 
- **Description**: 
- **Steps to Reproduce**:
  1. 
  2. 
  3. 
- **Expected**: 
- **Actual**: 
- **Screenshots**: 
- **Browser**: 
- **Status**: ⬜ Open / ⬜ Fixed / ⬜ Won't Fix
- **Fixed Date**: 
- **Notes**: 

### Bug #2
- **ID**: BUG-002
- **Date Found**: 
- **Severity**: ⬜ Critical / ⬜ High / ⬜ Medium / ⬜ Low
- **Feature**: 
- **Description**: 
- **Steps to Reproduce**:
  1. 
  2. 
  3. 
- **Expected**: 
- **Actual**: 
- **Screenshots**: 
- **Browser**: 
- **Status**: ⬜ Open / ⬜ Fixed / ⬜ Won't Fix
- **Fixed Date**: 
- **Notes**: 

---

## Issues & Observations

### Issue #1
- **Date**: 
- **Description**: 
- **Impact**: 
- **Recommendation**: 

---

## Test Environment Notes

- **Database**: 
- **SMS Provider**: 
- **File Storage**: 
- **Queue Status**: 
- **Cache Status**: 

---

## Sign-off

**Testing Completed By**: _________________  
**Date**: _________________  
**Status**: ⬜ Ready for Presentation / ⬜ Needs Fixes  
**Approved By**: _________________  
**Date**: _________________

---

## Notes

_Use this section for any additional notes, observations, or recommendations._

