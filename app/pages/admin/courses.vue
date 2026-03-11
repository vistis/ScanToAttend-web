<script setup lang="ts">
/**
 * Admin Courses — list, add, update, delete courses.
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
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingCourse = ref<any>(null)
const saving = ref(false)
const error = ref('')
<<<<<<< HEAD
const searchQuery = ref('')
const confirmDelete = ref(false)
const deleteId = ref(0)

const filteredCourses = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return courses.value
  return courses.value.filter(c =>
    c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q),
  )
})
=======
>>>>>>> origin/frontend

const addForm = reactive({ code: '', name: '' })
const editForm = reactive({ id: 0, code: '', name: '' })

async function loadCourses() {
  loading.value = true
  try {
    const data = await apiFetch<any>('/course/list')
    courses.value = data.courses ?? []
  }
  catch {
    //
  }
  finally {
    loading.value = false
  }
}

onMounted(loadCourses)

async function addCourse() {
  saving.value = true
  error.value = ''
  try {
    await apiFetch('/course/add', { method: 'POST', body: addForm })
    showAddModal.value = false
    Object.assign(addForm, { code: '', name: '' })
<<<<<<< HEAD
    toast.success('Course added.')
=======
>>>>>>> origin/frontend
    await loadCourses()
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to add course.'
  }
  finally {
    saving.value = false
  }
}

function openEdit(course: any) {
  editingCourse.value = course
  editForm.id = course.id
  editForm.code = course.code
  editForm.name = course.name
  showEditModal.value = true
}

async function updateCourse() {
  saving.value = true
  error.value = ''
  try {
    await apiFetch('/course/update', { method: 'PATCH', body: editForm })
    showEditModal.value = false
<<<<<<< HEAD
    toast.success('Course updated.')
=======
>>>>>>> origin/frontend
    await loadCourses()
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to update course.'
  }
  finally {
    saving.value = false
  }
}

async function deleteCourse(id: number) {
<<<<<<< HEAD
  deleteId.value = id
  confirmDelete.value = true
}

async function executeDelete() {
  try {
    await apiFetch('/course/remove', { method: 'DELETE', body: { id: deleteId.value } })
    toast.success('Course deleted.')
    await loadCourses()
  }
  catch {
    toast.error('Failed to delete course.')
  }
  finally {
    confirmDelete.value = false
=======
  if (!confirm('Delete this course and all its classes?')) return
  try {
    await apiFetch('/course/remove', { method: 'DELETE', body: { id } })
    await loadCourses()
  }
  catch {
    //
>>>>>>> origin/frontend
  }
}
</script>

<template>
  <div>
<<<<<<< HEAD
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
          Courses
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Manage course catalog</p>
      </div>
=======
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Courses
      </h1>
>>>>>>> origin/frontend
      <button
        class="btn-primary"
        @click="showAddModal = true"
      >
        <Icon
          name="heroicons:plus"
<<<<<<< HEAD
          class="w-4 h-4 mr-1.5"
=======
          class="w-4 h-4 mr-1"
>>>>>>> origin/frontend
        />
        Add Course
      </button>
    </div>

<<<<<<< HEAD
    <!-- Search -->
    <div class="relative mb-6">
      <Icon name="heroicons:magnifying-glass" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search courses..."
        class="input !pl-10"
      >
    </div>

    <div
      v-if="error"
      class="mb-4 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/60 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm"
=======
    <div
      v-if="error"
      class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
>>>>>>> origin/frontend
    >
      {{ error }}
    </div>

    <LoadingState :loading="loading">
      <EmptyState
        v-if="courses.length === 0"
        message="No courses yet."
      />

      <div
        v-else
        class="card overflow-hidden p-0"
      >
<<<<<<< HEAD
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-slate-100 dark:border-slate-700">
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Code
              </th>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
=======
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Code
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Name
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
>>>>>>> origin/frontend
                Actions
              </th>
            </tr>
          </thead>
<<<<<<< HEAD
          <tbody>
            <tr
              v-for="course in filteredCourses"
              :key="course.id"
              class="border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors"
            >
              <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">
                {{ course.code }}
              </td>
              <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
=======
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="course in courses"
              :key="course.id"
            >
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ course.code }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
>>>>>>> origin/frontend
                {{ course.name }}
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button
<<<<<<< HEAD
                  class="btn-secondary text-sm !py-2 !px-3"
=======
                  class="btn-secondary text-sm"
>>>>>>> origin/frontend
                  @click="openEdit(course)"
                >
                  Edit
                </button>
                <button
<<<<<<< HEAD
                  class="btn-danger text-sm !py-2 !px-3"
=======
                  class="btn-danger text-sm"
>>>>>>> origin/frontend
                  @click="deleteCourse(course.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </LoadingState>

    <!-- Add Modal -->
    <Teleport to="body">
<<<<<<< HEAD
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showAddModal"
          class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50"
          @click.self="showAddModal = false"
        >
          <div class="card w-full max-w-md mx-4 shadow-2xl">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
              Add Course
            </h2>
            <form
              class="space-y-4"
              @submit.prevent="addCourse"
            >
              <div>
                <label class="label">Course Code</label>
                <input
                  v-model="addForm.code"
                  type="text"
                  class="input"
                  placeholder="e.g. CS101"
                  required
                >
              </div>
              <div>
                <label class="label">Course Name</label>
                <input
                  v-model="addForm.name"
                  type="text"
                  class="input"
                  placeholder="e.g. Introduction to Computer Science"
                  required
                >
              </div>
              <div class="flex justify-end space-x-2 pt-2">
                <button
                  type="button"
                  class="btn-secondary"
                  @click="showAddModal = false"
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
      </Transition>
=======
      <div
        v-if="showAddModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="showAddModal = false"
      >
        <div class="card w-full max-w-md mx-4">
          <h2 class="text-lg font-bold mb-4">
            Add Course
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="addCourse"
          >
            <div>
              <label class="label">Course Code</label>
              <input
                v-model="addForm.code"
                type="text"
                class="input"
                placeholder="e.g. CS101"
                required
              >
            </div>
            <div>
              <label class="label">Course Name</label>
              <input
                v-model="addForm.name"
                type="text"
                class="input"
                placeholder="e.g. Introduction to Computer Science"
                required
              >
            </div>
            <div class="flex justify-end space-x-2 pt-2">
              <button
                type="button"
                class="btn-secondary"
                @click="showAddModal = false"
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
>>>>>>> origin/frontend
    </Teleport>

    <!-- Edit Modal -->
    <Teleport to="body">
<<<<<<< HEAD
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showEditModal"
          class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50"
          @click.self="showEditModal = false"
        >
          <div class="card w-full max-w-md mx-4 shadow-2xl">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
              Edit Course
            </h2>
            <form
              class="space-y-4"
              @submit.prevent="updateCourse"
            >
              <div>
                <label class="label">Course Code</label>
                <input
                  v-model="editForm.code"
                  type="text"
                  class="input"
                  required
                >
              </div>
              <div>
                <label class="label">Course Name</label>
                <input
                  v-model="editForm.name"
                  type="text"
                  class="input"
                  required
                >
              </div>
              <div class="flex justify-end space-x-2 pt-2">
                <button
                  type="button"
                  class="btn-secondary"
                  @click="showEditModal = false"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn-primary"
                  :disabled="saving"
                >
                  {{ saving ? 'Saving...' : 'Save' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <ConfirmModal
      v-model="confirmDelete"
      title="Delete Course?"
      message="This will permanently delete this course and all its classes, sessions, and registrations."
      confirm-text="Delete"
      danger
      @confirm="executeDelete"
    />
=======
      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="showEditModal = false"
      >
        <div class="card w-full max-w-md mx-4">
          <h2 class="text-lg font-bold mb-4">
            Edit Course
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="updateCourse"
          >
            <div>
              <label class="label">Course Code</label>
              <input
                v-model="editForm.code"
                type="text"
                class="input"
                required
              >
            </div>
            <div>
              <label class="label">Course Name</label>
              <input
                v-model="editForm.name"
                type="text"
                class="input"
                required
              >
            </div>
            <div class="flex justify-end space-x-2 pt-2">
              <button
                type="button"
                class="btn-secondary"
                @click="showEditModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
                :disabled="saving"
              >
                {{ saving ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
>>>>>>> origin/frontend
  </div>
</template>
