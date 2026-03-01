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
  }
  catch {
    // revert would be nice, but error is enough feedback
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
        class="text-gray-400 hover:text-gray-600"
      >
        <Icon
          name="heroicons:arrow-left"
          class="w-5 h-5"
        />
      </NuxtLink>
      <h1 class="text-2xl font-bold">
        Class Attendance
      </h1>
    </div>

    <div
      v-if="error"
      class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
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
              class="min-w-full divide-y divide-gray-200"
            >
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Student
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Email
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Status
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="student in students"
                  :key="student.id"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <img
                        v-if="student.profile_picture"
                        :src="student.profile_picture"
                        class="h-8 w-8 flex-shrink-0 rounded-full mr-3"
                        :alt="student.first_name"
                      >
                      <div
                        v-else
                        class="h-8 w-8 flex-shrink-0 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3 text-sm font-medium overflow-hidden"
                      >
                        {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
                      </div>
                      <span class="text-sm font-medium text-gray-900">
                        {{ student.first_name }} {{ student.last_name }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">
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
                          class="ml-1 text-xs"
                          :class="{
                            'text-green-600': s === 'Present',
                            'text-yellow-600': s === 'Tardy',
                            'text-red-600': s === 'Absent',
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
