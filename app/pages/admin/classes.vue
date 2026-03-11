<script setup lang="ts">
/**
 * Admin Classes — manage classes, assign instructors, manage sessions and registrations.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
<<<<<<< HEAD
const toast = useToast()
=======
>>>>>>> origin/frontend
const loading = ref(true)
const courses = ref<any[]>([])
const selectedCourse = ref<number | null>(null)
const classes = ref<any[]>([])
const instructors = ref<any[]>([])
const students = ref<any[]>([])
const loadingClasses = ref(false)
const saving = ref(false)
<<<<<<< HEAD

// Expanded class accordion
const expandedClassId = ref<number | null>(null)
const classStudentsMap = ref<Record<number, any[]>>({})
const loadingStudentsFor = ref<number | null>(null)

// Create class modal
const showCreateModal = ref(false)
const createForm = reactive({
  course_id: null as number | null,
  instructor_id: null as number | null,
  sessions: [] as { day: string, start_at: string, end_at: string }[],
})
const createStep = ref(1)
const creating = ref(false)

// Session form (for adding to existing class inline)
const addingSessionTo = ref<number | null>(null)
const sessionForm = reactive({
=======
const error = ref('')

const showAddSession = ref(false)
const showAddRegistration = ref(false)
const activeClassId = ref<number | null>(null)
const classStudents = ref<any[]>([])
const loadingStudents = ref(false)

const sessionForm = reactive({
  class_id: 0,
>>>>>>> origin/frontend
  day: 'Monday',
  start_at: '08:00',
  end_at: '09:30',
})

<<<<<<< HEAD
// Enrollment
const enrollingClassId = ref<number | null>(null)
const enrollSearch = ref('')
const enrolling = ref(false)

// Delete confirmation
const confirmDelete = ref(false)
const deleteTarget = ref<{ type: 'class' | 'session' | 'student', id: number, classId?: number, label: string }>({ type: 'class', id: 0, label: '' })
=======
const registrationForm = reactive({
  class_id: 0,
  student_id: 0,
})
>>>>>>> origin/frontend

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
<<<<<<< HEAD

    // Auto-select first course
    if (courses.value.length > 0) {
      selectedCourse.value = courses.value[0].id
    }
  }
  catch {
    toast.error('Failed to load data.')
=======
  }
  catch {
    //
>>>>>>> origin/frontend
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
<<<<<<< HEAD
  expandedClassId.value = null
=======
>>>>>>> origin/frontend
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

<<<<<<< HEAD
// --- Create Class Flow ---
function openCreateModal() {
  createForm.course_id = selectedCourse.value
  createForm.instructor_id = null
  createForm.sessions = []
  createStep.value = 1
  showCreateModal.value = true
}

function addSessionToCreate() {
  createForm.sessions.push({ day: 'Monday', start_at: '08:00', end_at: '09:30' })
}

function removeSessionFromCreate(index: number) {
  createForm.sessions.splice(index, 1)
}

async function createClass() {
  if (!createForm.course_id) return
  creating.value = true
  try {
    // Step 1: Create the class
    const classRes = await apiFetch<any>('/class/add', {
      method: 'POST',
      body: { course_id: createForm.course_id },
    })
    const newClassId = classRes.class?.id

    if (newClassId) {
      // Step 2: Assign instructor if selected
      if (createForm.instructor_id) {
        await apiFetch('/class/assign', {
          method: 'PATCH',
          body: { id: newClassId, instructor_id: createForm.instructor_id },
        })
      }

      // Step 3: Add sessions
      for (const session of createForm.sessions) {
        await apiFetch('/session/add', {
          method: 'POST',
          body: {
            class_id: newClassId,
            day: session.day,
            start_at: session.start_at + ':00',
            end_at: session.end_at + ':00',
          },
        })
      }
    }

    showCreateModal.value = false
    toast.success('Class created successfully!')

    // Switch to the course and reload
    selectedCourse.value = createForm.course_id
    const data = await apiFetch<any>('/class/list/course', {
      params: { id: createForm.course_id },
=======
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
>>>>>>> origin/frontend
    })
    classes.value = data.classes ?? []
  }
  catch (err: any) {
<<<<<<< HEAD
    toast.error(err?.data?.message ? String(err.data.message) : 'Failed to create class.')
  }
  finally {
    creating.value = false
  }
}

// --- Class Accordion ---
async function toggleClass(classId: number) {
  if (expandedClassId.value === classId) {
    expandedClassId.value = null
    return
  }
  expandedClassId.value = classId
  enrollingClassId.value = null
  addingSessionTo.value = null

  // Load students if not already cached
  if (!classStudentsMap.value[classId]) {
    await loadClassStudents(classId)
  }
}

async function loadClassStudents(classId: number) {
  loadingStudentsFor.value = classId
  try {
    const data = await apiFetch<any>('/student/list-for-admin/class', {
      params: { id: classId },
    })
    classStudentsMap.value[classId] = data.students ?? []
  }
  catch {
    classStudentsMap.value[classId] = []
  }
  finally {
    loadingStudentsFor.value = null
  }
}

// --- Instructor ---
async function assignInstructor(classId: number, instructorId: number | null) {
  try {
    if (instructorId) {
      await apiFetch('/class/assign', {
        method: 'PATCH',
        body: { id: classId, instructor_id: instructorId },
      })
      toast.success('Instructor assigned.')
    }
    else {
      await apiFetch('/class/unassign', {
        method: 'PATCH',
        body: { id: classId },
      })
      const cls = classes.value.find(c => c.id === classId)
      if (cls) cls.instructor_id = null
      toast.success('Instructor unassigned.')
    }
  }
  catch {
    toast.error('Failed to update instructor.')
  }
}

// --- Sessions ---
function startAddSession(classId: number) {
  addingSessionTo.value = classId
  sessionForm.day = 'Monday'
  sessionForm.start_at = '08:00'
  sessionForm.end_at = '09:30'
}

async function addSession(classId: number) {
  saving.value = true
  try {
    await apiFetch('/session/add', {
      method: 'POST',
      body: {
        class_id: classId,
        day: sessionForm.day,
        start_at: sessionForm.start_at + ':00',
        end_at: sessionForm.end_at + ':00',
      },
    })
    addingSessionTo.value = null
    toast.success('Session added.')
    await reloadClasses()
  }
  catch (err: any) {
    toast.error(err?.data?.message ? String(err.data.message) : 'Failed to add session.')
=======
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to add class.'
>>>>>>> origin/frontend
  }
  finally {
    saving.value = false
  }
}

<<<<<<< HEAD
async function confirmDeleteSession(sessionId: number) {
  deleteTarget.value = { type: 'session', id: sessionId, label: 'this session' }
  confirmDelete.value = true
}

// --- Enrollment ---
const filteredStudents = computed(() => {
  const classId = enrollingClassId.value
  const enrolled = classId ? (classStudentsMap.value[classId] ?? []) : []
  const enrolledIds = new Set(enrolled.map((s: any) => s.id))
  const search = enrollSearch.value.toLowerCase().trim()

  return students.value.filter((s) => {
    if (enrolledIds.has(s.id)) return false
    if (!search) return true
    const name = `${s.first_name} ${s.last_name}`.toLowerCase()
    return name.includes(search) || (s.email || '').toLowerCase().includes(search)
  })
})

function startEnrolling(classId: number) {
  enrollingClassId.value = classId
  enrollSearch.value = ''
}

async function enrollStudent(classId: number, studentId: number) {
  enrolling.value = true
  try {
    await apiFetch('/registration/add', {
      method: 'POST',
      body: { class_id: classId, student_id: studentId },
    })
    toast.success('Student enrolled.')
    await loadClassStudents(classId)
  }
  catch (err: any) {
    toast.error(err?.data?.message ? String(err.data.message) : 'Failed to enroll student.')
  }
  finally {
    enrolling.value = false
  }
}

function confirmRemoveStudent(classId: number, studentId: number, name: string) {
  deleteTarget.value = { type: 'student', id: studentId, classId, label: name }
  confirmDelete.value = true
}

function confirmDeleteClass(classId: number, section: string) {
  deleteTarget.value = { type: 'class', id: classId, label: `Section ${section}` }
  confirmDelete.value = true
}

async function executeDelete() {
  const { type, id, classId } = deleteTarget.value
  try {
    if (type === 'class') {
      await apiFetch('/class/remove', { method: 'DELETE', body: { id } })
      classes.value = classes.value.filter(c => c.id !== id)
      if (expandedClassId.value === id) expandedClassId.value = null
      toast.success('Class deleted.')
    }
    else if (type === 'session') {
      await apiFetch('/session/remove', { method: 'DELETE', body: { id } })
      toast.success('Session deleted.')
      await reloadClasses()
    }
    else if (type === 'student' && classId) {
      await apiFetch('/registration/remove', {
        method: 'DELETE',
        body: { class_id: classId, student_id: id },
      })
      classStudentsMap.value[classId] = (classStudentsMap.value[classId] ?? []).filter((s: any) => s.id !== id)
      toast.success('Student removed from class.')
    }
  }
  catch {
    toast.error('Operation failed.')
  }
  finally {
    confirmDelete.value = false
  }
}

async function reloadClasses() {
  if (!selectedCourse.value) return
  const data = await apiFetch<any>('/class/list/course', {
    params: { id: selectedCourse.value },
  })
  classes.value = data.classes ?? []
}

// --- Helpers ---
function getInstructorName(instructorId: number | null) {
  if (!instructorId) return null
  const inst = instructors.value.find(i => i.id === instructorId)
  return inst ? `${inst.first_name} ${inst.last_name}` : null
}

function getCourseName(courseId: number | null) {
  const course = courses.value.find(c => c.id === courseId)
  return course ? `${course.code} — ${course.name}` : ''
=======
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
>>>>>>> origin/frontend
}
</script>

<template>
  <div>
<<<<<<< HEAD
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
          Classes
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">
          Manage sections, instructors, sessions & registrations
        </p>
      </div>
      <button
        class="btn-primary"
        @click="openCreateModal"
      >
        <Icon name="heroicons:plus" class="w-4 h-4 mr-1.5" />
        New Class
=======
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
>>>>>>> origin/frontend
      </button>
    </div>

    <LoadingState :loading="loading">
<<<<<<< HEAD
      <!-- Course Filter Tabs -->
      <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
        <button
          v-for="course in courses"
          :key="course.id"
          class="flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
          :class="selectedCourse === course.id
            ? 'bg-primary-600 text-white shadow-sm'
            : 'bg-white dark:bg-surface-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-500/30 hover:text-primary-600 dark:hover:text-primary-400'"
          @click="selectedCourse = course.id"
        >
          {{ course.code }}
        </button>
      </div>

      <!-- Classes List -->
      <LoadingState :loading="loadingClasses">
        <EmptyState
          v-if="!selectedCourse"
          message="Select a course above to manage its classes."
        />
        <EmptyState
          v-else-if="classes.length === 0"
          message="No classes for this course yet. Click 'New Class' to create one."
        />

        <div v-else class="space-y-4">
          <div
            v-for="cls in classes"
            :key="cls.id"
            class="card overflow-hidden"
            :class="{ '!p-0': true }"
          >
            <!-- Class Header (always visible, clickable to expand) -->
            <button
              class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-slate-50/50 dark:hover:bg-surface-850/50 transition-colors"
              @click="toggleClass(cls.id)"
            >
              <div class="flex items-center gap-4 min-w-0">
                <div
                  class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0"
                  :class="cls.instructor_id
                    ? 'bg-primary-100 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400'
                    : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
                >
                  S{{ cls.section }}
                </div>
                <div class="min-w-0">
                  <h3 class="font-semibold text-slate-900 dark:text-white truncate">
                    Section {{ cls.section }}
                  </h3>
                  <p class="text-sm text-slate-500 dark:text-slate-400 truncate">
                    {{ getInstructorName(cls.instructor_id) || 'No instructor assigned' }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-3 flex-shrink-0">
                <!-- Badges -->
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                  <Icon name="heroicons:calendar-days" class="w-3.5 h-3.5" />
                  {{ cls.sessions?.length || 0 }}
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                  <Icon name="heroicons:users" class="w-3.5 h-3.5" />
                  {{ classStudentsMap[cls.id]?.length ?? '—' }}
                </span>
                <Icon
                  name="heroicons:chevron-down"
                  class="w-5 h-5 text-slate-400 transition-transform duration-200"
                  :class="{ 'rotate-180': expandedClassId === cls.id }"
                />
              </div>
            </button>

            <!-- Expanded Content -->
            <Transition
              enter-active-class="transition-all duration-200 ease-out"
              enter-from-class="max-h-0 opacity-0"
              enter-to-class="max-h-[2000px] opacity-100"
              leave-active-class="transition-all duration-150 ease-in"
              leave-from-class="max-h-[2000px] opacity-100"
              leave-to-class="max-h-0 opacity-0"
            >
              <div
                v-if="expandedClassId === cls.id"
                class="border-t border-slate-100 dark:border-slate-700"
              >
                <div class="p-5 space-y-6">
                  <!-- Instructor Assignment -->
                  <div>
                    <label class="label">Instructor</label>
                    <select
                      :value="cls.instructor_id"
                      class="input"
                      @change="(e) => {
                        const val = (e.target as HTMLSelectElement).value
                        assignInstructor(cls.id, val === '' ? null : Number(val))
                      }"
                    >
                      <option value="">
                        No instructor
                      </option>
                      <option
                        v-for="inst in instructors"
                        :key="inst.id"
                        :value="inst.id"
                      >
                        {{ inst.first_name }} {{ inst.last_name }}
                      </option>
                    </select>
                  </div>

                  <!-- Sessions -->
                  <div>
                    <div class="flex justify-between items-center mb-3">
                      <span class="label mb-0">Schedule</span>
                      <button
                        v-if="addingSessionTo !== cls.id"
                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors"
                        @click="startAddSession(cls.id)"
                      >
                        <Icon name="heroicons:plus" class="w-4 h-4" />
                        Add Session
                      </button>
                    </div>

                    <!-- Inline Add Session Form -->
                    <div
                      v-if="addingSessionTo === cls.id"
                      class="mb-3 p-4 rounded-xl bg-primary-50/50 dark:bg-primary-500/5 border border-primary-100 dark:border-primary-500/20"
                    >
                      <div class="grid grid-cols-[1fr_auto_auto_auto] gap-2 items-end">
                        <div>
                          <label class="label text-xs">Day</label>
                          <select v-model="sessionForm.day" class="input !py-2 text-sm">
                            <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
                          </select>
                        </div>
                        <div>
                          <label class="label text-xs">Start</label>
                          <input v-model="sessionForm.start_at" type="time" class="input !py-2 text-sm">
                        </div>
                        <div>
                          <label class="label text-xs">End</label>
                          <input v-model="sessionForm.end_at" type="time" class="input !py-2 text-sm">
                        </div>
                        <div class="flex gap-1.5">
                          <button
                            class="btn-primary !py-2 !px-3 text-sm"
                            :disabled="saving"
                            @click="addSession(cls.id)"
                          >
                            {{ saving ? '...' : 'Add' }}
                          </button>
                          <button
                            class="btn-secondary !py-2 !px-3 text-sm"
                            @click="addingSessionTo = null"
                          >
                            <Icon name="heroicons:x-mark" class="w-4 h-4" />
                          </button>
                        </div>
                      </div>
                    </div>

                    <div v-if="cls.sessions && cls.sessions.length" class="space-y-1.5">
                      <div
                        v-for="session in cls.sessions"
                        :key="session.id"
                        class="flex items-center justify-between text-sm bg-slate-50 dark:bg-surface-850 rounded-xl px-4 py-2.5 border border-slate-100 dark:border-slate-700/50"
                      >
                        <div class="flex items-center gap-2">
                          <Icon name="heroicons:clock" class="w-4 h-4 text-slate-400 dark:text-slate-500" />
                          <span class="font-medium text-slate-700 dark:text-slate-300">
                            {{ session.day }}
                          </span>
                          <span class="text-slate-500 dark:text-slate-400">
                            {{ session.start_at?.slice(0, 5) }} – {{ session.end_at?.slice(0, 5) }}
                          </span>
                        </div>
                        <button
                          class="text-slate-400 hover:text-rose-500 transition-colors"
                          title="Remove session"
                          @click="confirmDeleteSession(session.id)"
                        >
                          <Icon name="heroicons:trash" class="w-4 h-4" />
                        </button>
                      </div>
                    </div>
                    <p v-else class="text-sm text-slate-400 dark:text-slate-500 italic">
                      No sessions scheduled. Add one above.
                    </p>
                  </div>

                  <!-- Students -->
                  <div>
                    <div class="flex justify-between items-center mb-3">
                      <span class="label mb-0">
                        Enrolled Students
                        <span
                          v-if="classStudentsMap[cls.id]"
                          class="text-slate-400 dark:text-slate-500 font-normal"
                        >
                          ({{ classStudentsMap[cls.id].length }})
                        </span>
                      </span>
                      <button
                        v-if="enrollingClassId !== cls.id"
                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors"
                        @click="startEnrolling(cls.id)"
                      >
                        <Icon name="heroicons:user-plus" class="w-4 h-4" />
                        Enroll
                      </button>
                      <button
                        v-else
                        class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
                        @click="enrollingClassId = null"
                      >
                        Done
                      </button>
                    </div>

                    <!-- Enroll Search Panel (inline) -->
                    <div
                      v-if="enrollingClassId === cls.id"
                      class="mb-3 p-4 rounded-xl bg-primary-50/50 dark:bg-primary-500/5 border border-primary-100 dark:border-primary-500/20"
                    >
                      <div class="relative mb-3">
                        <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" />
                        <input
                          v-model="enrollSearch"
                          type="text"
                          placeholder="Search students by name or email..."
                          class="input !pl-9 !py-2 text-sm"
                        >
                      </div>
                      <div class="max-h-48 overflow-y-auto space-y-1">
                        <button
                          v-for="s in filteredStudents"
                          :key="s.id"
                          class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-white dark:hover:bg-surface-800 transition-colors"
                          :disabled="enrolling"
                          @click="enrollStudent(cls.id, s.id)"
                        >
                          <div class="h-7 w-7 rounded-lg bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-medium flex-shrink-0">
                            {{ s.first_name?.[0] }}{{ s.last_name?.[0] }}
                          </div>
                          <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">
                              {{ s.first_name }} {{ s.last_name }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ s.email }}</p>
                          </div>
                          <Icon name="heroicons:plus-circle" class="w-5 h-5 text-primary-500 dark:text-primary-400 flex-shrink-0" />
                        </button>
                        <p
                          v-if="filteredStudents.length === 0"
                          class="text-sm text-slate-400 dark:text-slate-500 text-center py-3"
                        >
                          {{ enrollSearch ? 'No matching students found.' : 'All students are already enrolled.' }}
                        </p>
                      </div>
                    </div>

                    <!-- Student List -->
                    <div v-if="loadingStudentsFor === cls.id" class="py-4 text-center">
                      <Icon name="heroicons:arrow-path" class="w-5 h-5 text-slate-400 animate-spin mx-auto" />
                    </div>
                    <div v-else-if="!classStudentsMap[cls.id] || classStudentsMap[cls.id].length === 0" class="text-sm text-slate-400 dark:text-slate-500 italic">
                      No students enrolled yet.
                    </div>
                    <div v-else class="space-y-1.5">
                      <div
                        v-for="student in classStudentsMap[cls.id]"
                        :key="student.id"
                        class="flex items-center justify-between text-sm bg-slate-50 dark:bg-surface-850 rounded-xl px-4 py-2.5 border border-slate-100 dark:border-slate-700/50"
                      >
                        <div class="flex items-center gap-2.5">
                          <div class="h-7 w-7 rounded-lg bg-primary-100 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xs font-medium flex-shrink-0">
                            {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
                          </div>
                          <div>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ student.first_name }} {{ student.last_name }}</span>
                            <span class="text-slate-400 dark:text-slate-500 ml-1.5 text-xs">{{ student.email }}</span>
                          </div>
                        </div>
                        <button
                          class="text-slate-400 hover:text-rose-500 transition-colors"
                          title="Remove student"
                          @click="confirmRemoveStudent(cls.id, student.id, `${student.first_name} ${student.last_name}`)"
                        >
                          <Icon name="heroicons:x-mark" class="w-4 h-4" />
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Delete Class -->
                  <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button
                      class="text-sm font-medium text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 transition-colors"
                      @click="confirmDeleteClass(cls.id, cls.section)"
                    >
                      <Icon name="heroicons:trash" class="w-4 h-4 inline mr-1" />
                      Delete this class
                    </button>
                  </div>
                </div>
              </div>
            </Transition>
=======
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
>>>>>>> origin/frontend
          </div>
        </div>
      </LoadingState>
    </LoadingState>

<<<<<<< HEAD
    <!-- Create Class Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showCreateModal"
          class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50"
          @click.self="showCreateModal = false"
        >
          <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
              <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                Create New Class
              </h2>
              <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Set up a class with instructor and schedule in one step.
              </p>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-5 space-y-5 max-h-[60vh] overflow-y-auto">
              <!-- Course -->
              <div>
                <label class="label">
                  Course
                  <span class="text-rose-500">*</span>
                </label>
                <select v-model="createForm.course_id" class="input" required>
                  <option disabled :value="null">Choose a course...</option>
                  <option v-for="course in courses" :key="course.id" :value="course.id">
                    {{ course.code }} — {{ course.name }}
                  </option>
                </select>
              </div>

              <!-- Instructor -->
              <div>
                <label class="label">Instructor <span class="text-slate-400 dark:text-slate-500 font-normal">(optional)</span></label>
                <select v-model="createForm.instructor_id" class="input">
                  <option :value="null">Assign later...</option>
                  <option v-for="inst in instructors" :key="inst.id" :value="inst.id">
                    {{ inst.first_name }} {{ inst.last_name }}
                  </option>
                </select>
              </div>

              <!-- Sessions -->
              <div>
                <div class="flex justify-between items-center mb-2">
                  <label class="label mb-0">Schedule <span class="text-slate-400 dark:text-slate-500 font-normal">(optional)</span></label>
                  <button
                    type="button"
                    class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300"
                    @click="addSessionToCreate"
                  >
                    <Icon name="heroicons:plus" class="w-4 h-4" />
                    Add Session
                  </button>
                </div>

                <div v-if="createForm.sessions.length === 0" class="text-sm text-slate-400 dark:text-slate-500 italic py-2">
                  No sessions added. You can add them later.
                </div>

                <div v-else class="space-y-2">
                  <div
                    v-for="(session, idx) in createForm.sessions"
                    :key="idx"
                    class="grid grid-cols-[1fr_auto_auto_auto] gap-2 items-end bg-slate-50 dark:bg-surface-850 rounded-xl p-3 border border-slate-100 dark:border-slate-700/50"
                  >
                    <select v-model="session.day" class="input !py-2 text-sm">
                      <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
                    </select>
                    <input v-model="session.start_at" type="time" class="input !py-2 text-sm">
                    <input v-model="session.end_at" type="time" class="input !py-2 text-sm">
                    <button
                      class="text-slate-400 hover:text-rose-500 transition-colors p-2"
                      @click="removeSessionFromCreate(idx)"
                    >
                      <Icon name="heroicons:trash" class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
              <button class="btn-secondary" @click="showCreateModal = false">
                Cancel
              </button>
              <button
                class="btn-primary"
                :disabled="!createForm.course_id || creating"
                @click="createClass"
              >
                <Icon v-if="creating" name="heroicons:arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                {{ creating ? 'Creating...' : 'Create Class' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      v-model="confirmDelete"
      :title="deleteTarget.type === 'class' ? 'Delete Class?' : deleteTarget.type === 'session' ? 'Delete Session?' : 'Remove Student?'"
      :message="deleteTarget.type === 'class'
        ? `This will permanently delete ${deleteTarget.label} and all its sessions and student registrations.`
        : deleteTarget.type === 'session'
          ? 'This session will be permanently deleted.'
          : `${deleteTarget.label} will be removed from this class.`"
      :confirm-text="deleteTarget.type === 'student' ? 'Remove' : 'Delete'"
      danger
      @confirm="executeDelete"
    />
=======
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
>>>>>>> origin/frontend
  </div>
</template>
