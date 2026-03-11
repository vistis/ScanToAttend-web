<script setup lang="ts">
/**
 * Admin Dashboard — overview cards linking to management pages.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
const loading = ref(true)

const counts = reactive({
  students: 0,
  instructors: 0,
  courses: 0,
  classes: 0,
})

onMounted(async () => {
  try {
    const [studentsRes, instructorsRes, coursesRes] = await Promise.all([
      apiFetch<any>('/student/list'),
      apiFetch<any>('/instructor/list'),
      apiFetch<any>('/course/list'),
    ])
    counts.students = studentsRes.students?.length ?? 0
    counts.instructors = instructorsRes.instructor?.length ?? 0
    counts.courses = coursesRes.courses?.length ?? 0
  }
  catch {
    // handle
  }
  finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
        Admin Dashboard
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1">Manage your attendance system</p>
    </div>

    <LoadingState :loading="loading">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <NuxtLink to="/admin/students" class="group">
          <StatCard
            title="Students"
            :value="counts.students"
            icon="heroicons:user-group"
            color="blue"
          />
        </NuxtLink>
        <NuxtLink to="/admin/instructors" class="group">
          <StatCard
            title="Instructors"
            :value="counts.instructors"
            icon="heroicons:academic-cap"
            color="green"
          />
        </NuxtLink>
        <NuxtLink to="/admin/courses" class="group">
          <StatCard
            title="Courses"
            :value="counts.courses"
            icon="heroicons:book-open"
            color="yellow"
          />
        </NuxtLink>
        <NuxtLink to="/admin/classes" class="group">
          <StatCard
            title="Classes"
            :value="counts.classes"
            icon="heroicons:building-library"
            color="gray"
          />
        </NuxtLink>
        <NuxtLink to="/admin/enrollment" class="group">
          <StatCard
            title="Enrollment"
            :value="'FP'"
            icon="heroicons:finger-print"
            color="purple"
          />
        </NuxtLink>
      </div>
    </LoadingState>
  </div>
</template>
