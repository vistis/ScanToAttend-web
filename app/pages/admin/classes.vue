<script setup lang="ts">
/**
 * Admin Classes — manage classes, assign instructors, manage sessions and registrations.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const courses = ref<any[]>([])
const selectedCourse = ref<number | null>(null)
const classes = ref<any[]>([])
const instructors = ref<any[]>([])
const students = ref<any[]>([])
const loadingClasses = ref(false)
const saving = ref(false)
const error = ref('')

const showAddSession = ref(false)
const showAddRegistration = ref(false)
const activeClassId = ref<number | null>(null)
const classStudents = ref<any[]>([])
const loadingStudents = ref(false)

const sessionForm = reactive({
  class_id: 0,
  day: 'Monday',
  start_at: '08:00',
  end_at: '09:30',
})

const registrationForm = reactive({
  class_id: 0,
  student_id: 0,
})

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']

onMounted(async () => {
  try {
    const [coursesRes, instructorsRes, studentsRes] = await Promise.all([
      apiFetch<any>('/course/list'),
      apiFetch<any>('/instructor/list'),
      apiFetch<any>('/student/list'),
    ])
    courses.value = coursesRes.courses ?? []
    instructors.value = instructorsRes.instructor ?? []
    students.value = studentsRes.students ?? []
  }
  catch {
    //
  }
  finally {
    loading.value = false
  }
})

watch(selectedCourse, async (courseId) => {
  if (!courseId) {
    classes.value = []
    return
  }
  loadingClasses.value = true
  try {
    const data = await apiFetch<any>('/class/list/course', {
      params: { id: courseId },
    })
    classes.value = data.classes ?? []
  }
  catch {
    classes.value = []
  }
  finally {
    loadingClasses.value = false
  }
})

async function addClass() {
  if (!selectedCourse.value) return
  saving.value = true
  try {
    await apiFetch('/class/add', {
      method: 'POST',
      body: { course_id: selectedCourse.value },
    })
    // Reload classes
    const data = await apiFetch<any>('/class/list/course', {
      params: { id: selectedCourse.value },
    })
    classes.value = data.classes ?? []
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to add class.'
  }
  finally {
    saving.value = false
  }
}

async function deleteClass(id: number) {
  if (!confirm('Delete this class and all its sessions/registrations?')) return
  try {
    await apiFetch('/class/remove', { method: 'DELETE', body: { id } })
    classes.value = classes.value.filter(c => c.id !== id)
  }
  catch {
    //
  }
}

async function assignInstructor(classId: number, instructorId: number) {
  try {
    await apiFetch('/class/assign', {
      method: 'PATCH',
      body: { id: classId, instructor_id: instructorId },
    })
  }
  catch {
    //
  }
}

async function unassignInstructor(classId: number) {
  try {
    await apiFetch('/class/unassign', {
      method: 'PATCH',
      body: { id: classId },
    })
    const cls = classes.value.find(c => c.id === classId)
    if (cls) cls.instructor_id = null
  }
  catch {
    //
  }
}

function openAddSession(classId: number) {
  sessionForm.class_id = classId
  showAddSession.value = true
}

async function addSession() {
  saving.value = true
  try {
    await apiFetch('/session/add', {
      method: 'POST',
      body: {
        class_id: sessionForm.class_id,
        day: sessionForm.day,
        start_at: sessionForm.start_at + ':00',
        end_at: sessionForm.end_at + ':00',
      },
    })
    showAddSession.value = false
    // Reload classes for the current course
    if (selectedCourse.value) {
      const data = await apiFetch<any>('/class/list/course', {
        params: { id: selectedCourse.value },
      })
      classes.value = data.classes ?? []
    }
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to add session.'
  }
  finally {
    saving.value = false
  }
}

async function deleteSession(sessionId: number) {
  if (!confirm('Delete this session?')) return
  try {
    await apiFetch('/session/remove', { method: 'DELETE', body: { id: sessionId } })
    if (selectedCourse.value) {
      const data = await apiFetch<any>('/class/list/course', {
        params: { id: selectedCourse.value },
      })
      classes.value = data.classes ?? []
    }
  }
  catch {
    //
  }
}

function openAddRegistration(classId: number) {
  registrationForm.class_id = classId
  registrationForm.student_id = 0
  showAddRegistration.value = true
}

async function addRegistration() {
  saving.value = true
  try {
    await apiFetch('/registration/add', {
      method: 'POST',
      body: {
        class_id: registrationForm.class_id,
        student_id: registrationForm.student_id,
      },
    })
    showAddRegistration.value = false
    if (activeClassId.value) await loadClassStudents(activeClassId.value)
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to register student.'
  }
  finally {
    saving.value = false
  }
}

async function removeRegistration(classId: number, studentId: number) {
  if (!confirm('Remove this student from the class?')) return
  try {
    await apiFetch('/registration/remove', {
      method: 'DELETE',
      body: { class_id: classId, student_id: studentId },
    })
    classStudents.value = classStudents.value.filter(s => s.id !== studentId)
  }
  catch {
    //
  }
}

async function loadClassStudents(classId: number) {
  activeClassId.value = classId
  loadingStudents.value = true
  try {
    const data = await apiFetch<any>('/student/list-for-admin/class', {
      params: { id: classId },
    })
    classStudents.value = data.students ?? []
  }
  catch {
    classStudents.value = []
  }
  finally {
    loadingStudents.value = false
  }
}
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Classes
      </h1>
    </div>

    <div
      v-if="error"
      class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
    >
      {{ error }}
      <button
        class="ml-2 underline"
        @click="error = ''"
      >
        dismiss
      </button>
    </div>

    <LoadingState :loading="loading">
      <!-- Course selector -->
      <div class="card mb-6">
        <div class="flex items-end gap-4">
          <div class="flex-1">
            <label class="label">Select Course</label>
            <select
              v-model="selectedCourse"
              class="input"
            >
              <option
                disabled
                :value="null"
              >
                Choose a course...
              </option>
              <option
                v-for="course in courses"
                :key="course.id"
                :value="course.id"
              >
                {{ course.code }} — {{ course.name }}
              </option>
            </select>
          </div>
          <button
            class="btn-primary"
            :disabled="!selectedCourse"
            @click="addClass"
          >
            <Icon
              name="heroicons:plus"
              class="w-4 h-4 mr-1"
            />
            Add Section
          </button>
        </div>
      </div>

      <!-- Classes for selected course -->
      <LoadingState :loading="loadingClasses">
        <EmptyState
          v-if="!selectedCourse"
          message="Select a course to manage its classes."
        />
        <EmptyState
          v-else-if="classes.length === 0"
          message="No classes for this course yet."
        />

        <div
          v-else
          class="space-y-4"
        >
          <div
            v-for="cls in classes"
            :key="cls.id"
            class="card"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold">
                  Section {{ cls.section }}
                </h3>
                <p class="text-sm text-gray-500">
                  Class ID: {{ cls.id }}
                </p>
              </div>
              <button
                class="btn-danger text-sm"
                @click="deleteClass(cls.id)"
              >
                Delete
              </button>
            </div>

            <!-- Instructor assignment -->
            <div class="mb-4">
              <label class="label">Assigned Instructor</label>
              <div class="flex items-center gap-2">
                <select
                  :value="cls.instructor_id"
                  class="input flex-1"
                  @change="(e) => assignInstructor(cls.id, Number((e.target as HTMLSelectElement).value))"
                >
                  <option :value="null">
                    Unassigned
                  </option>
                  <option
                    v-for="inst in instructors"
                    :key="inst.id"
                    :value="inst.id"
                  >
                    {{ inst.first_name }} {{ inst.last_name }}
                  </option>
                </select>
                <button
                  v-if="cls.instructor_id"
                  class="btn-secondary text-sm"
                  @click="unassignInstructor(cls.id)"
                >
                  Unassign
                </button>
              </div>
            </div>

            <!-- Sessions -->
            <div class="mb-4">
              <div class="flex justify-between items-center mb-2">
                <span class="label mb-0">Sessions</span>
                <button
                  class="text-sm text-primary-600 hover:underline"
                  @click="openAddSession(cls.id)"
                >
                  + Add Session
                </button>
              </div>
              <div
                v-if="cls.sessions && cls.sessions.length"
                class="space-y-1"
              >
                <div
                  v-for="session in cls.sessions"
                  :key="session.id"
                  class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2"
                >
                  <span>
                    {{ session.day }} {{ session.start_at?.slice(0, 5) }}–{{ session.end_at?.slice(0, 5) }}
                  </span>
                  <button
                    class="text-red-500 hover:text-red-700 text-xs"
                    @click="deleteSession(session.id)"
                  >
                    Remove
                  </button>
                </div>
              </div>
              <p
                v-else
                class="text-sm text-gray-400"
              >
                No sessions.
              </p>
            </div>

            <!-- Students -->
            <div>
              <div class="flex justify-between items-center mb-2">
                <span class="label mb-0">Enrolled Students</span>
                <div class="space-x-2">
                  <button
                    class="text-sm text-primary-600 hover:underline"
                    @click="loadClassStudents(cls.id)"
                  >
                    View
                  </button>
                  <button
                    class="text-sm text-primary-600 hover:underline"
                    @click="openAddRegistration(cls.id)"
                  >
                    + Enroll
                  </button>
                </div>
              </div>
              <div
                v-if="activeClassId === cls.id"
              >
                <LoadingState :loading="loadingStudents">
                  <div
                    v-if="classStudents.length === 0"
                    class="text-sm text-gray-400"
                  >
                    No students enrolled.
                  </div>
                  <div
                    v-else
                    class="space-y-1"
                  >
                    <div
                      v-for="student in classStudents"
                      :key="student.id"
                      class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2"
                    >
                      <span>{{ student.first_name }} {{ student.last_name }} ({{ student.email }})</span>
                      <button
                        class="text-red-500 hover:text-red-700 text-xs"
                        @click="removeRegistration(cls.id, student.id)"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                </LoadingState>
              </div>
            </div>
          </div>
        </div>
      </LoadingState>
    </LoadingState>

    <!-- Add Session Modal -->
    <Teleport to="body">
      <div
        v-if="showAddSession"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="showAddSession = false"
      >
        <div class="card w-full max-w-md mx-4">
          <h2 class="text-lg font-bold mb-4">
            Add Session
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="addSession"
          >
            <div>
              <label class="label">Day</label>
              <select
                v-model="sessionForm.day"
                class="input"
              >
                <option
                  v-for="d in days"
                  :key="d"
                  :value="d"
                >
                  {{ d }}
                </option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="label">Start Time</label>
                <input
                  v-model="sessionForm.start_at"
                  type="time"
                  class="input"
                  required
                >
              </div>
              <div>
                <label class="label">End Time</label>
                <input
                  v-model="sessionForm.end_at"
                  type="time"
                  class="input"
                  required
                >
              </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
              <button
                type="button"
                class="btn-secondary"
                @click="showAddSession = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
                :disabled="saving"
              >
                {{ saving ? 'Adding...' : 'Add' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Enroll Student Modal -->
    <Teleport to="body">
      <div
        v-if="showAddRegistration"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="showAddRegistration = false"
      >
        <div class="card w-full max-w-md mx-4">
          <h2 class="text-lg font-bold mb-4">
            Enroll Student
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="addRegistration"
          >
            <div>
              <label class="label">Select Student</label>
              <select
                v-model="registrationForm.student_id"
                class="input"
                required
              >
                <option
                  disabled
                  :value="0"
                >
                  Choose a student...
                </option>
                <option
                  v-for="s in students"
                  :key="s.id"
                  :value="s.id"
                >
                  {{ s.first_name }} {{ s.last_name }} ({{ s.email }})
                </option>
              </select>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
              <button
                type="button"
                class="btn-secondary"
                @click="showAddRegistration = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
                :disabled="saving || !registrationForm.student_id"
              >
                {{ saving ? 'Enrolling...' : 'Enroll' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
