/**
 * Composable for making authenticated API calls to the Laravel backend.
 * Uses cookie-based auth (credentials: 'include') with CSRF token.
 */
export function useApi() {
  const config = useRuntimeConfig()

  function getCsrfToken(): string | null {
    if (!import.meta.client) return null
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match ? decodeURIComponent(match[1]) : null
  }

  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase as string,
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    onRequest({ options }) {
      const token = getCsrfToken()
      if (token) {
        options.headers = {
          ...options.headers,
          'X-XSRF-TOKEN': token,
        }
      }
    },
  })

  return { apiFetch }
}
