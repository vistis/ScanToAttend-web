<script setup lang="ts">
/**
 * Admin Students — list, add, update, delete students.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const students = ref<any[]>([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingStudent = ref<any>(null)
const saving = ref(false)
const error = ref('')

const addForm = reactive({
  first_name: '',
  last_name: '',
  password: '',
  profile_picture: null as File | null,
})

const editForm = reactive({
  id: 0,
  password: '',
  profile_picture: null as File | null,
})

async function loadStudents() {
  loading.value = true
  try {
    const data = await apiFetch<any>('/student/list')
    students.value = data.students ?? []
  }
  catch {
    //
  }
  finally {
    loading.value = false
  }
}

onMounted(loadStudents)

async function addStudent() {
  saving.value = true
  error.value = ''

  const formData = new FormData()
  formData.append('first_name', addForm.first_name)
  formData.append('last_name', addForm.last_name)
  formData.append('password', addForm.password)
  if (addForm.profile_picture) {
    formData.append('profile_picture', addForm.profile_picture)
  }

  try {
    await apiFetch('/student/add', { method: 'POST', body: formData })
    showAddModal.value = false
    Object.assign(addForm, { first_name: '', last_name: '', password: '', profile_picture: null })
    await loadStudents()
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to add student.'
  }
  finally {
    saving.value = false
  }
}

function openEdit(student: any) {
  editingStudent.value = student
  editForm.id = student.id
  editForm.password = ''
  editForm.profile_picture = null
  showEditModal.value = true
}

async function updateStudent() {
  saving.value = true
  error.value = ''

  const formData = new FormData()
  formData.append('id', String(editForm.id))
  if (editForm.password) formData.append('password', editForm.password)
  if (editForm.profile_picture) formData.append('profile_picture', editForm.profile_picture)

  try {
    await apiFetch('/student/update', { method: 'PATCH', body: formData })
    showEditModal.value = false
    await loadStudents()
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to update student.'
  }
  finally {
    saving.value = false
  }
}

async function deleteStudent(id: number) {
  if (!confirm('Are you sure you want to delete this student?')) return

  try {
    await apiFetch('/student/remove', { method: 'DELETE', body: { id } })
    await loadStudents()
  }
  catch {
    //
  }
}

function handleFileInput(event: Event, target: 'add' | 'edit') {
  const file = (event.target as HTMLInputElement).files?.[0] ?? null
  if (target === 'add') addForm.profile_picture = file
  else editForm.profile_picture = file
}
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Students
      </h1>
      <button
        class="btn-primary"
        @click="showAddModal = true"
      >
        <Icon
          name="heroicons:plus"
          class="w-4 h-4 mr-1"
        />
        Add Student
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
        v-if="students.length === 0"
        message="No students registered yet."
      />

      <div
        v-else
        class="card overflow-hidden p-0"
      >
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Student
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Fingerprint
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Actions
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
                  >
                  <div
                    v-else
                    class="h-8 w-8 flex-shrink-0 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3 text-sm font-medium"
                  >
                    {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ student.first_name }} {{ student.last_name }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ student.email }}
              </td>
              <td class="px-6 py-4">
                <span
                  v-if="student.fingerprint_id"
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                >
                  <Icon
                    name="heroicons:finger-print"
                    class="w-3 h-3 mr-1"
                  />
                  ID #{{ student.fingerprint_id }}
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700"
                >
                  Not enrolled
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button
                  class="btn-secondary text-sm"
                  @click="openEdit(student)"
                >
                  Edit
                </button>
                <button
                  class="btn-danger text-sm"
                  @click="deleteStudent(student.id)"
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
            Add Student
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="addStudent"
          >
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="label">First Name</label>
                <input
                  v-model="addForm.first_name"
                  type="text"
                  class="input"
                  required
                >
              </div>
              <div>
                <label class="label">Last Name</label>
                <input
                  v-model="addForm.last_name"
                  type="text"
                  class="input"
                  required
                >
              </div>
            </div>
            <div>
              <label class="label">Password</label>
              <input
                v-model="addForm.password"
                type="password"
                class="input"
                required
              >
            </div>
            <div>
              <label class="label">Profile Picture</label>
              <input
                type="file"
                accept="image/*"
                class="input"
                @change="(e) => handleFileInput(e, 'add')"
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
            Edit Student: {{ editingStudent?.first_name }} {{ editingStudent?.last_name }}
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="updateStudent"
          >
            <div>
              <label class="label">New Password (optional)</label>
              <input
                v-model="editForm.password"
                type="password"
                class="input"
              >
            </div>
            <div>
              <label class="label">New Profile Picture (optional)</label>
              <input
                type="file"
                accept="image/*"
                class="input"
                @change="(e) => handleFileInput(e, 'edit')"
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
