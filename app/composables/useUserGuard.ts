/**
 * Composable to expose the current user's guard (role).
 * Reads from the sanctum user identity first (server-safe),
 * falls back to localStorage for client-side hydration.
 */
export function useUserGuard() {
  const { user } = useSanctumAuth()

  const guard = computed<string | null>(() => {
    // Primary source: the 'guard' field returned by /api/user
    const fromUser = (user.value as any)?.guard
    if (fromUser) return fromUser

    // Fallback: localStorage (client only, for brief hydration gap)
    if (import.meta.client) {
      return localStorage.getItem('user-guard')
    }
    return null
  })

  function setGuard(value: string) {
    if (import.meta.client) {
      localStorage.setItem('user-guard', value)
    }
  }

  function clearGuard() {
    if (import.meta.client) {
      localStorage.removeItem('user-guard')
    }
  }

  return { guard, setGuard, clearGuard }
}
