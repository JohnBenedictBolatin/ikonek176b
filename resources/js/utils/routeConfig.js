/**
 * Route configuration utility for user types
 * Maps user types (resident/employee) to their respective routes
 */

export const routeConfig = {
  resident: {
    announcement: 'announcement_resident',
    discussion: 'discussion_resident',
    discussionAddPost: 'discussion_addpost_resident',
    documentRequestSelect: 'document_request_select_resident',
    documentRequestDescription: 'document_request_description_resident',
    documentRequestForm: 'document_request_form_resident',
    documentRequestSubmission: 'document_request_submission_resident',
    eventAssistance: 'event_assistance_resident',
    notificationRequest: 'notification_request_resident',
    notificationActivities: 'notification_activities_resident',
    profile: 'profile_resident',
    helpCenter: 'help_center_resident',
    payment: 'payment_resident',
    notificationPickInfo: 'notification_pick_info_resident',
  },
  employee: {
    announcement: 'announcement_employee',
    discussion: 'discussion_resident', // Employees use the same discussion route
    discussionAddPost: 'discussion_addpost_resident', // Same route
    documentRequestSelect: 'document_request_select_employee',
    documentRequestDescription: 'document_request_description_employee',
    documentRequestForm: 'document_request_form_employee',
    documentRequestSubmission: 'document_request_submission_employee',
    eventAssistance: 'event_assistance_employee',
    notificationRequest: 'notification_request_employee',
    notificationActivities: 'notification_activities_employee',
    profile: 'profile_employee',
    helpCenter: 'help_center_employee',
    payment: 'payment_employee',
    notificationPickInfo: 'notification_pick_info_employee',
  }
}

/**
 * Get route configuration based on user type
 * @param {string} userType - 'resident' or 'employee'
 * @returns {object} Route configuration object
 */
export function getRoutes(userType) {
  return routeConfig[userType] || routeConfig.resident
}

/**
 * Determine user type from role ID
 * @param {number} roleId - User's role ID
 * @returns {string} 'resident' or 'employee'
 */
export function getUserType(roleId) {
  // Role 1 = Resident, Roles 2-7, 9 = Employee/Official
  return roleId === 1 ? 'resident' : 'employee'
}

/**
 * Get routes for a specific user based on their role ID
 * @param {number} roleId - User's role ID
 * @returns {object} Route configuration object
 */
export function getRoutesByRoleId(roleId) {
  const userType = getUserType(roleId)
  return getRoutes(userType)
}




