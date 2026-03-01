<script setup lang="ts">
const { user, logout } = useSanctumAuth()
const { guard, clearGuard } = useUserGuard()

const navItems = computed(() => {
  switch (guard.value) {
    case 'student':
      return [
        { label: 'Dashboard', to: '/student' },
        { label: 'Schedule', to: '/student/schedule' },
      ]
    case 'instructor':
      return [
        { label: 'Dashboard', to: '/instructor' },
        { label: 'Schedule', to: '/instructor/schedule' },
      ]
    case 'admin':
      return [
        { label: 'Dashboard', to: '/admin' },
        { label: 'Students', to: '/admin/students' },
        { label: 'Instructors', to: '/admin/instructors' },
        { label: 'Courses', to: '/admin/courses' },
        { label: 'Classes', to: '/admin/classes' },
        { label: 'Enrollment', to: '/admin/enrollment' },
      ]
    default:
      return []
  }
})

const mobileMenuOpen = ref(false)

async function handleLogout() {
  try {
    await logout()
  }
  catch {
    // Token may already be invalid
  }
  clearGuard()
  navigateTo('/login')
}
</script>

<template>
  <nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Left: Logo + Nav links -->
        <div class="flex items-center space-x-8">
          <NuxtLink
            :to="`/${guard}`"
            class="text-xl font-bold text-primary-600"
          >
            ScanToAttend
          </NuxtLink>

          <div class="hidden sm:flex space-x-4">
            <NuxtLink
              v-for="item in navItems"
              :key="item.to"
              :to="item.to"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50 transition-colors"
              active-class="text-primary-600 bg-primary-50"
            >
              {{ item.label }}
            </NuxtLink>
          </div>
        </div>

        <!-- Right: User info + Logout -->
        <div class="flex items-center space-x-4">
          <span
            v-if="user"
            class="hidden sm:block text-sm text-gray-600"
          >
            {{ (user as any).first_name }} {{ (user as any).last_name }}
          </span>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 capitalize">
            {{ guard }}
          </span>
          <button
            class="btn-secondary text-sm"
            @click="handleLogout"
          >
            Logout
          </button>

          <!-- Mobile hamburger -->
          <button
            class="sm:hidden p-2 rounded-md text-gray-400 hover:text-gray-500"
            @click="mobileMenuOpen = !mobileMenuOpen"
          >
            <Icon
              :name="mobileMenuOpen ? 'heroicons:x-mark' : 'heroicons:bars-3'"
              class="w-6 h-6"
            />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div
      v-show="mobileMenuOpen"
      class="sm:hidden border-t border-gray-200 bg-white"
    >
      <div class="px-4 py-3 space-y-1">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-primary-600 hover:bg-gray-50"
          active-class="text-primary-600 bg-primary-50"
          @click="mobileMenuOpen = false"
        >
          {{ item.label }}
        </NuxtLink>
      </div>
    </div>
  </nav>
</template>
