<script setup lang="ts">
/**
 * Student Schedule — weekly calendar view of class sessions.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['student'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const classes = ref<any[]>([])

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']

onMounted(async () => {
  try {
    const data = await apiFetch<any>('/class/student/list')
    classes.value = data.classes ?? []
  }
  catch {
    // handle
  }
  finally {
    loading.value = false
  }
})

/** Build schedule grid: group sessions by day */
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

  // Sort each day by start_at
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
        title="Student Schedule"
        :schedule="schedule"
        :days="days"
      />

      <!-- Course list table -->
      <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
          Enrolled Courses
        </h2>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
          <CourseTable
            :classes="classes"
            show-instructor
          />
        </div>
      </div>
    </LoadingState>
  </div>
</template>
