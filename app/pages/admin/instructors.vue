<script setup lang="ts">
/**
 * Admin Instructors — list, add, update, delete instructors.
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
const instructors = ref<any[]>([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingInstructor = ref<any>(null)
const saving = ref(false)
const error = ref('')
<<<<<<< HEAD
const searchQuery = ref('')
const confirmDelete = ref(false)
const deleteId = ref(0)

const filteredInstructors = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return instructors.value
  return instructors.value.filter((i) => {
    const name = `${i.first_name} ${i.last_name}`.toLowerCase()
    return name.includes(q) || (i.email || '').toLowerCase().includes(q)
  })
})
=======
>>>>>>> origin/frontend

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

async function loadInstructors() {
  loading.value = true
  try {
    const data = await apiFetch<any>('/instructor/list')
    instructors.value = data.instructor ?? []
  }
  catch {
    //
  }
  finally {
    loading.value = false
  }
}

onMounted(loadInstructors)

async function addInstructor() {
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
    await apiFetch('/instructor/add', { method: 'POST', body: formData })
    showAddModal.value = false
    Object.assign(addForm, { first_name: '', last_name: '', password: '', profile_picture: null })
<<<<<<< HEAD
    toast.success('Instructor added.')
=======
>>>>>>> origin/frontend
    await loadInstructors()
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to add instructor.'
  }
  finally {
    saving.value = false
  }
}

function openEdit(instructor: any) {
  editingInstructor.value = instructor
  editForm.id = instructor.id
  editForm.password = ''
  editForm.profile_picture = null
  showEditModal.value = true
}

async function updateInstructor() {
  saving.value = true
  error.value = ''

  const formData = new FormData()
  formData.append('id', String(editForm.id))
  if (editForm.password) formData.append('password', editForm.password)
  if (editForm.profile_picture) formData.append('profile_picture', editForm.profile_picture)

  try {
    await apiFetch('/instructor/update', { method: 'PATCH', body: formData })
    showEditModal.value = false
<<<<<<< HEAD
    toast.success('Instructor updated.')
=======
>>>>>>> origin/frontend
    await loadInstructors()
  }
  catch (err: any) {
    error.value = err?.data?.message ? JSON.stringify(err.data.message) : 'Failed to update instructor.'
  }
  finally {
    saving.value = false
  }
}

async function deleteInstructor(id: number) {
<<<<<<< HEAD
  deleteId.value = id
  confirmDelete.value = true
}

async function executeDelete() {
  try {
    await apiFetch('/instructor/remove', { method: 'DELETE', body: { id: deleteId.value } })
    toast.success('Instructor deleted.')
    await loadInstructors()
  }
  catch {
    toast.error('Failed to delete instructor.')
  }
  finally {
    confirmDelete.value = false
=======
  if (!confirm('Are you sure you want to delete this instructor?')) return

  try {
    await apiFetch('/instructor/remove', { method: 'DELETE', body: { id } })
    await loadInstructors()
  }
  catch {
    //
>>>>>>> origin/frontend
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
<<<<<<< HEAD
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
          Instructors
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Manage instructor accounts</p>
      </div>
=======
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Instructors
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
        Add Instructor
      </button>
    </div>

<<<<<<< HEAD
    <!-- Search -->
    <div class="relative mb-6">
      <Icon name="heroicons:magnifying-glass" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search instructors..."
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
        v-if="instructors.length === 0"
        message="No instructors registered yet."
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
                Instructor
              </th>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
=======
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Instructor
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Email
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
              v-for="instructor in filteredInstructors"
              :key="instructor.id"
              class="border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors"
=======
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="instructor in instructors"
              :key="instructor.id"
>>>>>>> origin/frontend
            >
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <img
                    v-if="instructor.profile_picture"
                    :src="instructor.profile_picture"
<<<<<<< HEAD
                    class="h-9 w-9 rounded-xl mr-3 object-cover"
                  >
                  <div
                    v-else
                    class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-500/20 dark:to-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mr-3 text-sm font-bold"
                  >
                    {{ instructor.first_name?.[0] }}{{ instructor.last_name?.[0] }}
                  </div>
                  <p class="text-sm font-semibold text-slate-900 dark:text-white">
=======
                    class="h-8 w-8 rounded-full mr-3"
                  >
                  <div
                    v-else
                    class="h-8 w-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 text-sm font-medium"
                  >
                    {{ instructor.first_name?.[0] }}{{ instructor.last_name?.[0] }}
                  </div>
                  <p class="text-sm font-medium text-gray-900">
>>>>>>> origin/frontend
                    {{ instructor.first_name }} {{ instructor.last_name }}
                  </p>
                </div>
              </td>
<<<<<<< HEAD
              <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
=======
              <td class="px-6 py-4 text-sm text-gray-500">
>>>>>>> origin/frontend
                {{ instructor.email }}
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button
<<<<<<< HEAD
                  class="btn-secondary text-sm !py-2 !px-3"
=======
                  class="btn-secondary text-sm"
>>>>>>> origin/frontend
                  @click="openEdit(instructor)"
                >
                  Edit
                </button>
                <button
<<<<<<< HEAD
                  class="btn-danger text-sm !py-2 !px-3"
=======
                  class="btn-danger text-sm"
>>>>>>> origin/frontend
                  @click="deleteInstructor(instructor.id)"
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
              Add Instructor
            </h2>
            <form
              class="space-y-4"
              @submit.prevent="addInstructor"
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
=======
      <div
        v-if="showAddModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="showAddModal = false"
      >
        <div class="card w-full max-w-md mx-4">
          <h2 class="text-lg font-bold mb-4">
            Add Instructor
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="addInstructor"
          >
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="label">First Name</label>
                <input
                  v-model="addForm.first_name"
                  type="text"
>>>>>>> origin/frontend
                  class="input"
                  required
                >
              </div>
              <div>
<<<<<<< HEAD
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
=======
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
              Edit: {{ editingInstructor?.first_name }} {{ editingInstructor?.last_name }}
            </h2>
            <form
              class="space-y-4"
              @submit.prevent="updateInstructor"
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
      title="Delete Instructor?"
      message="This will permanently delete this instructor and remove them from all assigned classes."
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
            Edit: {{ editingInstructor?.first_name }} {{ editingInstructor?.last_name }}
          </h2>
          <form
            class="space-y-4"
            @submit.prevent="updateInstructor"
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
>>>>>>> origin/frontend
  </div>
</template>
