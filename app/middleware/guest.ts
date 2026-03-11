/**
 * Middleware: Redirect authenticated users away from login page.
 */
export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated } = useSanctumAuth()
  const { guard } = useUserGuard()

  if (isAuthenticated.value && guard.value) {
    return navigateTo(`/${guard.value}`)
  }
})
