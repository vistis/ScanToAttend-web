/**
 * Middleware: Only allow admins to access /admin/* routes.
 */
export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated } = useSanctumAuth()
  const { guard } = useUserGuard()

  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }

  if (guard.value && guard.value !== 'admin') {
    return navigateTo(`/${guard.value}`)
  }
})
