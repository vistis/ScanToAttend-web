/**
 * Middleware: Only allow students to access /student/* routes.
 */
export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated } = useSanctumAuth()
  const { guard } = useUserGuard()

  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }

  if (guard.value && guard.value !== 'student') {
    return navigateTo(`/${guard.value}`)
  }
})
