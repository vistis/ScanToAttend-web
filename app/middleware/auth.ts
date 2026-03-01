/**
 * Middleware: Redirect unauthenticated users to login.
 */
export default defineNuxtRouteMiddleware((to) => {
  const { isAuthenticated } = useSanctumAuth()

  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }
})
