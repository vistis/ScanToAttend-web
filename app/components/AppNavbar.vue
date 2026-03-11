<script setup lang="ts">
const { user, logout } = useSanctumAuth()
const { guard, clearGuard } = useUserGuard()
const { isDark, toggle: toggleDark } = useDarkMode()

const navItems = computed(() => {
  switch (guard.value) {
    case 'student':
      return [
        { label: 'Dashboard', to: '/student', icon: 'heroicons:squares-2x2' },
        { label: 'Schedule', to: '/student/schedule', icon: 'heroicons:calendar-days' },
      ]
    case 'instructor':
      return [
        { label: 'Dashboard', to: '/instructor', icon: 'heroicons:squares-2x2' },
        { label: 'Schedule', to: '/instructor/schedule', icon: 'heroicons:calendar-days' },
      ]
    case 'admin':
      return [
        { label: 'Dashboard', to: '/admin', icon: 'heroicons:squares-2x2' },
        { label: 'Students', to: '/admin/students', icon: 'heroicons:users' },
        { label: 'Instructors', to: '/admin/instructors', icon: 'heroicons:academic-cap' },
        { label: 'Courses', to: '/admin/courses', icon: 'heroicons:book-open' },
        { label: 'Classes', to: '/admin/classes', icon: 'heroicons:rectangle-stack' },
        { label: 'Enrollment', to: '/admin/enrollment', icon: 'heroicons:finger-print' },
      ]
    default:
      return []
  }
})

const mobileOpen = ref(false)

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
  <!-- Mobile top bar -->
  <div class="lg:hidden fixed top-0 left-0 right-0 z-40 h-14 bg-white dark:bg-surface-900 border-b border-slate-200 dark:border-slate-800 flex items-center px-4 gap-3">
    <button
      class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-surface-800 transition-colors"
      @click="mobileOpen = true"
    >
      <Icon name="heroicons:bars-3" class="w-5 h-5" />
    </button>
    <div class="flex items-center gap-2">
      <div class="w-7 h-7 rounded-lg bg-primary-600 flex items-center justify-center">
        <Icon name="heroicons:finger-print" class="w-4 h-4 text-white" />
      </div>
      <span class="text-sm font-semibold text-slate-900 dark:text-white">ScanToAttend</span>
    </div>
  </div>

  <!-- Mobile overlay -->
  <Transition
    enter-active-class="transition-opacity duration-200"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="mobileOpen"
      class="lg:hidden fixed inset-0 z-40 bg-black/40"
      @click="mobileOpen = false"
    />
  </Transition>

  <!-- Sidebar -->
  <aside
    class="fixed top-0 left-0 z-50 h-screen w-64 flex flex-col bg-white dark:bg-surface-900 border-r border-slate-200 dark:border-slate-800 transition-transform duration-200 lg:translate-x-0"
    :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <!-- Logo -->
    <div class="h-14 flex items-center gap-2.5 px-5 border-b border-slate-200 dark:border-slate-800 flex-shrink-0">
      <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
        <Icon name="heroicons:finger-print" class="w-5 h-5 text-white" />
      </div>
      <span class="text-base font-bold text-slate-900 dark:text-white">ScanToAttend</span>
      <button
        class="lg:hidden ml-auto p-1 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-surface-800"
        @click="mobileOpen = false"
      >
        <Icon name="heroicons:x-mark" class="w-5 h-5" />
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
      <NuxtLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-surface-800 hover:text-slate-900 dark:hover:text-white transition-colors"
        active-class="!bg-primary-50 !text-primary-700 dark:!bg-primary-500/10 dark:!text-primary-400"
        @click="mobileOpen = false"
      >
        <Icon :name="item.icon" class="w-5 h-5 flex-shrink-0" />
        {{ item.label }}
      </NuxtLink>
    </nav>

    <!-- Bottom section -->
    <div class="border-t border-slate-200 dark:border-slate-800 p-3 space-y-2 flex-shrink-0">
      <!-- Dark mode toggle -->
      <button
        class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-surface-800 transition-colors"
        @click="toggleDark"
      >
        <Icon
          :name="isDark ? 'heroicons:sun' : 'heroicons:moon'"
          class="w-5 h-5 flex-shrink-0"
        />
        {{ isDark ? 'Light Mode' : 'Dark Mode' }}
      </button>

      <!-- User info + logout -->
      <div v-if="user" class="flex items-center gap-3 px-3 py-2">
        <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-500/10 flex items-center justify-center text-xs font-bold text-primary-700 dark:text-primary-400 flex-shrink-0">
          {{ (user as any).first_name?.[0] }}{{ (user as any).last_name?.[0] }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
            {{ (user as any).first_name }} {{ (user as any).last_name }}
          </p>
          <p class="text-xs text-slate-400 capitalize">{{ guard }}</p>
        </div>
        <button
          class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-surface-800 transition-colors"
          title="Logout"
          @click="handleLogout"
        >
          <Icon name="heroicons:arrow-right-start-on-rectangle" class="w-4 h-4" />
        </button>
      </div>
    </div>
  </aside>
</template>
