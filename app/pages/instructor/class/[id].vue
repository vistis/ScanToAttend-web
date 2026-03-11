<script setup lang="ts">
/**
 * Instructor Class Detail — pick a date, view attendance, and update statuses.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['instructor'],
})

const route = useRoute()
const classId = route.params.id as string
const { apiFetch } = useApi()
const toast = useToast()

const loading = ref(true)
const dates = ref<string[]>([])
const selectedDate = ref('')
const selectedSession = ref<string | null>(null)
const students = ref<any[]>([])
const loadingAttendance = ref(false)
const saving = ref<Record<number, boolean>>({})
const sessions = ref<any[]>([])
const error = ref('')

onMounted(async () => {
  try {
    // Get instructor's classes to find sessions for this class
    const classData = await apiFetch<any>('/class/instructor/list')
    const thisClass = (classData.classes ?? []).find((c: any) => String(c.id) === classId)
    sessions.value = thisClass?.sessions ?? []

    // Get attendance dates
    const dateData = await apiFetch<any>('/attendance/dates/class', {
      params: { id: classId },
    })
    dates.value = dateData.dates ?? []

    if (dates.value.length > 0) {
      selectedDate.value = dates.value[dates.value.length - 1] // pick latest
    }

    // Auto-select first session
    if (sessions.value.length > 0) {
      selectedSession.value = String(sessions.value[0].id)
    }
  }
  catch (err: any) {
    error.value = err?.data?.message || 'Failed to load data.'
  }
  finally {
    loading.value = false
  }
})

/** Load attendance when date + session changes */
watch([selectedDate, selectedSession], async () => {
  if (!selectedDate.value || !selectedSession.value) return

  loadingAttendance.value = true
  try {
    const data = await apiFetch<any>('/attendance/session', {
      params: {
        id: selectedSession.value,
        date: selectedDate.value,
      },
    })
    students.value = data.students ?? []
  }
  catch (err: any) {
    error.value = err?.data?.message || 'Failed to load attendance.'
    students.value = []
  }
  finally {
    loadingAttendance.value = false
  }
})

/** Update attendance status for a student */
async function updateStatus(attendanceId: number, status: string) {
  saving.value[attendanceId] = true
  try {
    await apiFetch('/attendance/update', {
      method: 'PATCH',
      body: { id: attendanceId, status },
    })
    toast.success(`Marked as ${status}.`)
  }
  catch {
    toast.error('Failed to update status.')
  }
  finally {
    saving.value[attendanceId] = false
  }
}
</script>

<template>
  <div>
    <div class="flex items-center space-x-2 mb-6">
      <NuxtLink
        to="/instructor"
        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
      >
        <Icon
          name="heroicons:arrow-left"
          class="w-5 h-5"
        />
      </NuxtLink>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
        Class Attendance
      </h1>
    </div>

    <div
      v-if="error"
      class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-sm border border-rose-200/60 dark:border-rose-500/20"
    >
      {{ error }}
    </div>

    <LoadingState :loading="loading">
      <EmptyState
        v-if="dates.length === 0"
        message="No attendance records for this class yet."
      />

      <div v-else>
        <!-- Date & Session picker -->
        <div class="card mb-6">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="label">Date</label>
              <select
                v-model="selectedDate"
                class="input"
              >
                <option
                  v-for="d in dates"
                  :key="d"
                  :value="d"
                >
                  {{ d }}
                </option>
              </select>
            </div>
            <div>
              <label class="label">Session</label>
              <select
                v-model="selectedSession"
                class="input"
              >
                <option
                  disabled
                  value=""
                >
                  Select session
                </option>
                <option
                  v-for="s in sessions"
                  :key="s.id"
                  :value="s.id"
                >
                  {{ s.day }} {{ s.start_at?.slice(0, 5) }}–{{ s.end_at?.slice(0, 5) }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Attendance table -->
        <div class="card overflow-hidden p-0">
          <LoadingState :loading="loadingAttendance">
            <EmptyState
              v-if="students.length === 0"
              message="Select a date and session to view attendance."
            />

            <table
              v-else
              class="min-w-full divide-y divide-slate-200/60 dark:divide-slate-700"
            >
              <thead>
                <tr>
                  <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Student
                  </th>
                  <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Email
                  </th>
                  <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Status
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <tr
                  v-for="student in students"
                  :key="student.id"
                  class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <img
                        v-if="student.profile_picture"
                        :src="student.profile_picture"
                        class="h-8 w-8 flex-shrink-0 rounded-xl mr-3 object-cover"
                        :alt="student.first_name"
                      >
                      <div
                        v-else
                        class="h-8 w-8 flex-shrink-0 rounded-xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-500/20 dark:to-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center mr-3 text-sm font-medium overflow-hidden"
                      >
                        {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
                      </div>
                      <span class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ student.first_name }} {{ student.last_name }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                    {{ student.email }}
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                      <label
                        v-for="s in ['Present', 'Tardy', 'Absent']"
                        :key="s"
                        class="inline-flex items-center cursor-pointer"
                      >
                        <input
                          type="radio"
                          :name="`status-${student.id}`"
                          :value="s"
                          :checked="student.status === s"
                          class="h-4 w-4 text-primary-600 focus:ring-primary-500"
                          :disabled="saving[student.attendance_id]"
                          @change="() => {
                            student.status = s
                            if (student.attendance_id) {
                              updateStatus(student.attendance_id, s)
                            }
                          }"
                        >
                        <span
                          class="ml-1 text-xs font-medium"
                          :class="{
                            'text-emerald-600 dark:text-emerald-400': s === 'Present',
                            'text-amber-600 dark:text-amber-400': s === 'Tardy',
                            'text-rose-600 dark:text-rose-400': s === 'Absent',
                          }"
                        >
                          {{ s }}
                        </span>
                      </label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </LoadingState>
        </div>
      </div>
    </LoadingState>
  </div>
</template>
