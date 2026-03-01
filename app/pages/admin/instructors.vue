<script setup lang="ts">
/**
 * Admin Instructors — list, add, update, delete instructors.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
const loading = ref(true)
const instructors = ref<any[]>([])
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingInstructor = ref<any>(null)
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
  if (!confirm('Are you sure you want to delete this instructor?')) return

  try {
    await apiFetch('/instructor/remove', { method: 'DELETE', body: { id } })
    await loadInstructors()
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
        Instructors
      </h1>
      <button
        class="btn-primary"
        @click="showAddModal = true"
      >
        <Icon
          name="heroicons:plus"
          class="w-4 h-4 mr-1"
        />
        Add Instructor
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
        v-if="instructors.length === 0"
        message="No instructors registered yet."
      />

      <div
        v-else
        class="card overflow-hidden p-0"
      >
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
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="instructor in instructors"
              :key="instructor.id"
            >
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <img
                    v-if="instructor.profile_picture"
                    :src="instructor.profile_picture"
                    class="h-8 w-8 rounded-full mr-3"
                  >
                  <div
                    v-else
                    class="h-8 w-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 text-sm font-medium"
                  >
                    {{ instructor.first_name?.[0] }}{{ instructor.last_name?.[0] }}
                  </div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ instructor.first_name }} {{ instructor.last_name }}
                  </p>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ instructor.email }}
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <button
                  class="btn-secondary text-sm"
                  @click="openEdit(instructor)"
                >
                  Edit
                </button>
                <button
                  class="btn-danger text-sm"
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
  </div>
</template>
