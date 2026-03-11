<script setup lang="ts">
/**
 * Instructor Dashboard — shows assigned classes and today's schedule.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['instructor'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const classes = ref<any[]>([])

const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']

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

/** Get today's day name */
const todayName = computed(() => dayNames[new Date().getDay()])

/** Filter classes that have sessions today */
const todaySchedule = computed(() => {
  const result: any[] = []
  for (const cls of classes.value) {
    if (!cls.sessions) continue
    const todaySessions = cls.sessions.filter((s: any) => s.day === todayName.value)
    if (todaySessions.length > 0) {
      result.push({
        ...cls,
        todaySessions: todaySessions.sort((a: any, b: any) => a.start_at.localeCompare(b.start_at)),
      })
    }
  }
  return result
})
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
        Dashboard
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-1">
        Your assigned courses and today's schedule.
      </p>
    </div>

    <LoadingState :loading="loading">
      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left: Course List -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200">
              Courses
            </h2>
          </div>

          <EmptyState
            v-if="classes.length === 0"
            message="You are not assigned to any classes."
          />

          <div
            v-else
            class="space-y-3"
          >
            <NuxtLink
              v-for="cls in classes"
              :key="cls.id"
              :to="`/instructor/class/${cls.id}`"
              class="group block card p-4 hover:shadow-soft-lg transition-all duration-200"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                    {{ cls.course_code ?? cls.code }} Section {{ cls.section }} - {{ cls.course_name ?? cls.name }}
                  </h3>
                  <div
                    v-if="cls.sessions && cls.sessions.length"
                    class="mt-2 space-y-0.5"
                  >
                    <p
                      v-for="session in cls.sessions"
                      :key="session.id"
                      class="text-xs text-slate-500 dark:text-slate-400"
                    >
                      ({{ session.day.slice(0, 3) }}) {{ session.start_at?.slice(0, 5) }} - {{ session.end_at?.slice(0, 5) }}
                    </p>
                  </div>
                </div>
                <Icon
                  name="heroicons:chevron-right"
                  class="w-5 h-5 text-slate-400 group-hover:text-primary-500 flex-shrink-0 mt-1 transition-colors"
                />
              </div>
            </NuxtLink>
          </div>
        </div>

        <!-- Right: Today's Schedule -->
        <div class="lg:w-80 flex-shrink-0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200">
              Today's Schedule
            </h2>
            <NuxtLink
              to="/instructor/schedule"
              class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 bg-primary-50 dark:bg-primary-500/10 hover:bg-primary-100 dark:hover:bg-primary-500/20 px-3 py-1.5 rounded-xl transition-colors"
            >
              <Icon
                name="heroicons:calendar-days"
                class="w-4 h-4"
              />
              View Schedule
            </NuxtLink>
          </div>

          <div class="card p-4">
            <div
              v-if="todaySchedule.length === 0"
              class="text-center py-8 text-slate-400 dark:text-slate-500 text-sm"
            >
              Your schedule is empty today
            </div>

            <div
              v-else
              class="space-y-4"
            >
              <div
                v-for="cls in todaySchedule"
                :key="cls.id"
                class="p-3 rounded-xl bg-slate-50 dark:bg-surface-850"
              >
                <p class="font-medium text-slate-900 dark:text-white text-sm">
                  {{ cls.course_code ?? cls.code }} Section {{ cls.section }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                  {{ cls.course_name ?? cls.name }}
                </p>
                <div class="mt-1.5 space-y-0.5">
                  <p
                    v-for="session in cls.todaySessions"
                    :key="session.id"
                    class="text-xs text-slate-600 dark:text-slate-300"
                  >
                    <Icon
                      name="heroicons:clock"
                      class="w-3.5 h-3.5 inline mr-0.5"
                    />
                    {{ session.start_at?.slice(0, 5) }} - {{ session.end_at?.slice(0, 5) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </LoadingState>
  </div>
</template>
