<script setup lang="ts">
/**
 * Instructor Schedule — weekly calendar view of assigned class sessions.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['instructor'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const classes = ref<any[]>([])

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']

onMounted(async () => {
  try {
    const data = await apiFetch<any>('/class/instructor/list')
    classes.value = data.classes ?? []
  }
  catch {
    // handle
  }
  finally {
    loading.value = false
  }
})

const schedule = computed(() => {
  const grid: Record<string, any[]> = {}
  for (const day of days) {
    grid[day] = []
  }

  for (const cls of classes.value) {
    if (cls.sessions) {
      for (const session of cls.sessions) {
        if (grid[session.day]) {
          grid[session.day].push({
            ...session,
            course_code: cls.course_code ?? cls.code,
            course_name: cls.course_name ?? cls.name,
            section: cls.section,
            class_id: cls.id,
          })
        }
      }
    }
  }

  for (const day of days) {
    grid[day].sort((a: any, b: any) => a.start_at.localeCompare(b.start_at))
  }

  return grid
})
</script>

<template>
  <div>
    <LoadingState :loading="loading">
      <WeeklySchedule
        title="Instructor Schedule"
        :schedule="schedule"
        :days="days"
        linkable
        link-base-path="/instructor/class"
      />

      <!-- Course list table -->
      <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
          Assigned Courses
        </h2>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
          <CourseTable
            :classes="classes"
            link-base-path="/instructor/class"
          />
        </div>
      </div>
    </LoadingState>
  </div>
</template>
