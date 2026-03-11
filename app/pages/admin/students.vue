<script setup lang="ts">
/**
 * Admin Students — list, add, update, delete students.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
const toast = useToast()
const loading = ref(true)
const students = ref<any[]>([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingStudent = ref<any>(null)
const saving = ref(false)
const error = ref('')
const searchQuery = ref('')
const confirmDelete = ref(false)
const deleteId = ref(0)

const filteredStudents = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return students.value
  return students.value.filter((s) => {
    const name = `${s.first_name} ${s.last_name}`.toLowerCase()
    return name.includes(q) || (s.email || '').toLowerCase().includes(q)
  })
})

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
    toast.success('Student added.')
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
    toast.success('Student updated.')
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
  deleteId.value = id
  confirmDelete.value = true
}

async function executeDelete() {
  try {
    await apiFetch('/student/remove', { method: 'DELETE', body: { id: deleteId.value } })
    toast.success('Student deleted.')
    await loadStudents()
  }
  catch {
    toast.error('Failed to delete student.')
  }
  finally {
    confirmDelete.value = false
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
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
          Students
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Manage student accounts</p>
      </div>
      <button
        class="btn-primary"
        @click="showAddModal = true"
      >
        <Icon
          name="heroicons:plus"
          class="w-4 h-4 mr-1.5"
        />
        Add Student
      </button>
    </div>

    <!-- Search -->
    <div class="relative mb-6">
      <Icon name="heroicons:magnifying-glass" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search students..."
        class="input !pl-10"
      >
    </div>

    <div
      v-if="error"
      class="mb-4 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/60 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm"
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
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-slate-100 dark:border-slate-700">
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Student
              </th>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Fingerprint
              </th>
              <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="student in filteredStudents"
              :key="student.id"
              class="border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors"
            >
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <img
                    v-if="student.profile_picture"
                    :src="student.profile_picture"
                    class="h-9 w-9 flex-shrink-0 rounded-xl mr-3 object-cover"
                  >
                  <div
                    v-else
                    class="h-9 w-9 flex-shrink-0 rounded-xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-500/20 dark:to-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center mr-3 text-sm font-bold"
                  >
                    {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">
                      {{ student.first_name }} {{ student.last_name }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                {{ student.email }}
              </td>
              <td class="px-6 py-4">
                <span
                  v-if="student.fingerprint_id"
                  class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-500/20"
                >
                  <Icon
                    name="heroicons:finger-print"
                    class="w-3 h-3 mr-1"
                  />
                  ID #{{ student.fingerprint_id }}
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-500/20"
                >
                  Not enrolled
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button
                  class="btn-secondary text-sm !py-2 !px-3"
                  @click="openEdit(student)"
                >
                  Edit
                </button>
                <button
                  class="btn-danger text-sm !py-2 !px-3"
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
                  class="input !py-2"
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
      </Transition>
    </Teleport>

    <!-- Edit Modal -->
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
          v-if="showEditModal"
          class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50"
          @click.self="showEditModal = false"
        >
          <div class="card w-full max-w-md mx-4 shadow-2xl">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
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
                  class="input !py-2"
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
      </Transition>
    </Teleport>

    <ConfirmModal
      v-model="confirmDelete"
      title="Delete Student?"
      message="This will permanently delete this student and all their attendance records."
      confirm-text="Delete"
      danger
      @confirm="executeDelete"
    />
  </div>
</template>
