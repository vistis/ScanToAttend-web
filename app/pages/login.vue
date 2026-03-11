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
<<<<<<< HEAD
  <div class="w-full max-w-sm mx-auto px-4">
    <!-- Logo -->
    <div class="text-center mb-8">
      <div class="w-12 h-12 rounded-xl bg-primary-600 flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:finger-print" class="w-7 h-7 text-white" />
      </div>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
        ScanToAttend
      </h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Sign in to your account
      </p>
    </div>

    <div class="card">
      <!-- Error -->
      <div
        v-if="error"
        class="mb-4 p-3 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-sm ring-1 ring-inset ring-rose-600/10 dark:ring-rose-500/20"
=======
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
>>>>>>> origin/frontend
      >
        {{ error }}
      </div>

      <!-- Form -->
      <form
<<<<<<< HEAD
        class="space-y-4"
        @submit.prevent="handleLogin"
      >
        <div>
          <label for="username" class="label">Username</label>
=======
        class="space-y-5"
        @submit.prevent="handleLogin"
      >
        <div>
          <label
            for="username"
            class="label"
          >Username</label>
>>>>>>> origin/frontend
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
<<<<<<< HEAD
          <label for="password" class="label">Password</label>
=======
          <label
            for="password"
            class="label"
          >Password</label>
>>>>>>> origin/frontend
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
<<<<<<< HEAD
          class="btn-primary w-full justify-center"
=======
          class="btn-primary w-full"
>>>>>>> origin/frontend
          :disabled="loading"
        >
          <span
            v-if="loading"
<<<<<<< HEAD
            class="animate-spin rounded-full h-4 w-4 border-2 border-primary-300 border-t-white mr-2"
=======
            class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"
>>>>>>> origin/frontend
          />
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>
    </div>
  </div>
</template>
