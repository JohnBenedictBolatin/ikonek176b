/**
 * Composable for user navigation based on user type
 * Handles route navigation for both residents and employees
 */

import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { getRoutesByRoleId } from '@/utils/routeConfig'

/**
 * Composable that provides navigation functions based on user role
 * @returns {object} Navigation functions and user type info
 */
export function useUserNavigation() {
  const page = usePage()
  
  // Get authenticated user
  const user = computed(() => {
    return page?.props?.value?.auth?.user ?? page?.props?.auth?.user ?? null
  })
  
  // Get user type based on role ID
  const userType = computed(() => {
    const roleId = user.value?.fk_role_id ?? 1
    return roleId === 1 ? 'resident' : 'employee'
  })
  
  // Get routes for current user type
  const routes = computed(() => {
    const roleId = user.value?.fk_role_id ?? 1
    return getRoutesByRoleId(roleId)
  })
  
  // Navigation functions - route() should be available globally via ZiggyVue
  // Access route helper (available globally in Vue apps with ZiggyVue)
  const routeHelper = (typeof route !== 'undefined') ? route : 
                     (typeof window !== 'undefined' && typeof window.route === 'function') ? window.route :
                     null

  if (!routeHelper) {
    console.error('Route helper not available. Make sure ZiggyVue is properly configured.')
  }

  const navigateToDocuments = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.documentRequestSelect))
    } else {
      console.error('Cannot navigate: route helper not available')
    }
  }
  
  const navigateToProfile = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.profile))
    }
  }
  
  const navigateToEvents = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.eventAssistance))
    }
  }
  
  const navigateToNotifications = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.notificationActivities))
    }
  }
  
  const navigateToHelpCenter = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.helpCenter))
    }
  }
  
  const navigateToAnnouncement = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.announcement))
    }
  }
  
  const navigateToDiscussion = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.discussion))
    } else {
      console.error('Cannot navigate to discussion: route helper not available')
    }
  }
  
  const navigateToDiscussionAddPost = () => {
    if (routeHelper) {
      router.visit(routeHelper(routes.value.discussionAddPost))
    }
  }
  
  return {
    user,
    userType,
    routes,
    navigateToDocuments,
    navigateToProfile,
    navigateToEvents,
    navigateToNotifications,
    navigateToHelpCenter,
    navigateToAnnouncement,
    navigateToDiscussion,
    navigateToDiscussionAddPost,
  }
}

