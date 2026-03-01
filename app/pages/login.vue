<script setup lang="ts">
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

const { login, refreshIdentity, user } = useSanctumAuth()
const { setGuard } = useUserGuard()

const form = reactive({
  username: '',
  password: '',
})

const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true

  try {
    // nuxt-auth-sanctum cookie mode:
    // 1. Fetches CSRF cookie from /sanctum/csrf-cookie
    // 2. POSTs credentials to /api/login
    await login({
      username: form.username,
      password: form.password,
    })

    // Fetch user identity (returns flat user object with 'guard' field)
    await refreshIdentity()

    // Read guard from the user identity
    const guard = (user.value as any)?.guard
    if (guard) {
      setGuard(guard)
      await navigateTo(`/${guard}`)
    }
    else {
      error.value = 'Login succeeded but could not determine user role.'
    }
  }
  catch (err: any) {
    const msg = err?.data?.message || err?.response?._data?.message || err?.message || ''
    error.value = msg || 'Login failed. Please check your credentials.'
  }
  finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <div class="card">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary-600">
          ScanToAttend
        </h1>
        <p class="mt-2 text-sm text-gray-500">
          IoT-Based Classroom Attendance System
        </p>
      </div>

      <!-- Error -->
      <div
        v-if="error"
        class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
      >
        {{ error }}
      </div>

      <!-- Form -->
      <form
        class="space-y-5"
        @submit.prevent="handleLogin"
      >
        <div>
          <label
            for="username"
            class="label"
          >Username</label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            class="input"
            placeholder="Enter your username"
            required
            autofocus
          >
        </div>

        <div>
          <label
            for="password"
            class="label"
          >Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="input"
            placeholder="Enter your password"
            required
          >
        </div>

        <button
          type="submit"
          class="btn-primary w-full"
          :disabled="loading"
        >
          <span
            v-if="loading"
            class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"
          />
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>
    </div>
  </div>
</template>
