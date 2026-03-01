<script setup lang="ts">
/**
 * Admin Courses — list, add, update, delete courses.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const courses = ref<any[]>([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingCourse = ref<any>(null)
const saving = ref(false)
const error = ref('')

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
  if (!confirm('Delete this course and all its classes?')) return
  try {
    await apiFetch('/course/remove', { method: 'DELETE', body: { id } })
    await loadCourses()
  }
  catch {
    //
  }
}
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Courses
      </h1>
      <button
        class="btn-primary"
        @click="showAddModal = true"
      >
        <Icon
          name="heroicons:plus"
          class="w-4 h-4 mr-1"
        />
        Add Course
      </button>
    </div>

    <div
      v-if="error"
      class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
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
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="course in courses"
              :key="course.id"
            >
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ course.code }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ course.name }}
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button
                  class="btn-secondary text-sm"
                  @click="openEdit(course)"
                >
                  Edit
                </button>
                <button
                  class="btn-danger text-sm"
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
    </Teleport>

    <!-- Edit Modal -->
    <Teleport to="body">
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
  </div>
</template>
