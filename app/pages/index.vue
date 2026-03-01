<script setup lang="ts">
/**
 * Root index page — redirects based on auth state.
 */
const { isAuthenticated } = useSanctumAuth()
const { guard } = useUserGuard()

definePageMeta({})

// Use a watcher so we redirect once guard resolves (may be async)
watch(
  [() => isAuthenticated.value, guard],
  ([authed, g]) => {
    if (authed && g) {
      navigateTo(`/${g}`, { replace: true })
    }
    else if (!authed) {
      navigateTo('/login', { replace: true })
    }
  },
  { immediate: true },
)
</script>

<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600" />
  </div>
</template>
